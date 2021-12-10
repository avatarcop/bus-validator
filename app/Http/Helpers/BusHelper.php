<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Log;
use App\Models\MasterSetting;
class BusHelper extends Controller
{
	public static function simpan_log($api='', $user='',$request=[],$request_datetime='',$ket='',$ip='',$table_log='')
    {

        $log='';
        if( $table_log == 'LogUser')
        {
            $log = new \App\Models\LogUser;
        }elseif( $table_log == 'LogBus')
        {
            $log = new \App\Models\LogBus;
        }

        $log->api = $api;
        $log->user = $user;
        $log->request = $request;
        $log->request_datetime = $request_datetime;
        $log->keterangan = $ket;
        $log->ipaddress = $ip;
        $log->save();

        return $log->id;
    }

    public static function update_log($log_id='', $result=[], $response_time='',$table_log='', $status='')
    {

        $log='';
        if( $table_log == 'LogUser')
        {
            $log = \App\Models\LogUser::find($log_id);
        }elseif( $table_log == 'LogBus')
        {
            $log = \App\Models\LogBus::find($log_id);
        }

        $log->response = $result;
        $log->response_datetime = $response_time;
        $log->status = $status;
        $log->update();

        return 1;
    }

    public static function enable_all_service()
    {
        $cekservis       = MasterSetting::where('setting_name', 'enable_all_service')->first();
        $cekservis_msg   = MasterSetting::where('setting_name', 'enable_all_service_message')->first();
        if($cekservis && $cekservis_msg)
        {
            if($cekservis->setting_value == 1)
            {
                $result = [
                    'status' => 1,
                    'message' => 'Sukses',
                ];

            }else{
                $result = [
                    'status' => 0,
                    'message' => $cekservis_msg->setting_value,
                ];

                Log::Info('Disable all service');
            }
        }else{
            $result = [
                    'status' => 0,
                    'message' => 'Maintenance',
                ];

            Log::Info('Disable all service, master setting is not correct');
        }

        return $result;
    }

    public static function check_blacklist()
    {
        $cek_blacklist = \App\Models\Blacklist::select('id', 'user', 'ket', 'created_at', 'updated_at')
            ->where('user', Input::get('email'))
            ->first();

        if(isset($cek_blacklist))
        {
            Log::Info('Error blacklist '.Input::get('email').' - '.json_encode($cek_blacklist));

            return [
                    'status' => 0,
                    'message' => 'User '.Input::get('email').' tidak diizinkan untuk menggunakan aplikasi ini',
            ];
        }else{
                return [
                        'status' => 1,
                        'message' => 'Sukses',

                    ];

        }
    }

    public static function cek_signature($signature) 
    {
        $tes = self::setting('signature_testing');

        if($tes == 1)
        {
            Log::Info('Signature testing validator');
            $result = 1;    
        }else{
            $signat = \App\Models\Signature::where('backend_signature', $signature)->first();

            $result = '';
            if(!$signat){
                // tidak ada signature ganda
                $result = 1;
            }else{
                $result = 0;
            }
        }
        
    
        return $result;
    }

    public static function dekrip_signature($signature) 
    {
        $tes = self::setting('signature_testing');
        if($tes==1)
        {
            $result = [
                    'status' => 1,
                    'message' => 'Signature sukses',
                    'backend_signature' => '-',
                ];
        }else{
            $iv = MasterSetting::where('setting_name','iv')->first()->setting_value;
            $key = MasterSetting::where('setting_name','key')->first()->setting_value;

            // Pecah Signature
            $decryptedSignature = self::dekripsi_with_iv($key, $signature, $iv);

            if (strpos( $decryptedSignature, '|' ) > 0)
            {
                $parts = explode('|', $decryptedSignature);

                $device_id = $parts[0];
                $waktu = $parts[1];

                if($device_id == '' or $waktu == '')
                {
                    $result = array(
                            'status' => 0,
                            'message' => 'Invalid signature'
                        );

                    Log::Info('Error signature '.Input::get('email').' - '.json_encode($result));

                }else{
                    self::simpan_signature($device_id,$waktu,$signature);
                    
                    $data = $device_id.'&&'.$waktu;
                    $backend_signature = self::enkripsi($key, $data, $iv);

                    $result = array(
                            'status' => 1,
                            'message' => 'Sukses',
                            'backend_signature' => $backend_signature,
                        );

                }

            }else{
                $result = array(
                            'status' => 0,
                            'message' => 'Terjadi kesalahan'
                        );

                Log::Info('Error Invalid signature '.Input::get('email').' - '.json_encode($result));

            }
        }

        return $result;
    }

    public static function enkripsi($key, $data, $iv)
    {
        $OPENSSL_CIPHER_NAME = "aes-128-cbc";
        $CIPHER_KEY_LEN = 16;

        if (strlen($key) < $CIPHER_KEY_LEN)
        {
            $key = str_pad($key, $CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > $CIPHER_KEY_LEN) {
            $key = substr($str, 0, $CIPHER_KEY_LEN); //truncate to 16 bytes
        }

        $encodedEncryptedData = base64_encode(openssl_encrypt($data, $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv));
        $encodedIV = base64_encode($iv);
        $encryptedPayload = $encodedEncryptedData;

        return $encryptedPayload;
    }

    public static function dekripsi_with_iv($key, $data, $iv)
    {
        $OPENSSL_CIPHER_NAME = "aes-128-cbc";
        $CIPHER_KEY_LEN = 16;

        if (strlen($key) < $CIPHER_KEY_LEN)
        {
            $key = str_pad($key, $CIPHER_KEY_LEN, "0"); //0 pad to len 16
        } else if (strlen($key) > $CIPHER_KEY_LEN) {
            $key = substr($str, 0, $CIPHER_KEY_LEN); //truncate to 16 bytes
        }

        $decryptedData = openssl_decrypt(base64_decode($data), $OPENSSL_CIPHER_NAME, $key, OPENSSL_RAW_DATA, $iv);
        // Log::Info('key '.$key);
        // Log::Info('signature '.Input::get('signature'));
        // Log::Info('iv '.$iv);
        return $decryptedData;
    }

    public static function setting($setting_name='')
    {
        $setting = MasterSetting::where('setting_name', $setting_name)->first();
        if($setting)
        {
            return $setting->setting_value;
        }else{
            return '';
        }

    }

    public static function simpan_signature($device_id,$waktu,$signature) 
    {
        $result = new \App\Models\Signature;
        $result->device_id  = $device_id;   
        $result->waktu      = $waktu;
        $result->backend_signature     = $signature;
        $result->user     = Input::get('email');
        $result->save();
        return $result;
    }

}