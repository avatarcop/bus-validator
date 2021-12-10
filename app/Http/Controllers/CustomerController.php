<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use App\Http\Helpers\ApiHelper;


class CustomerController extends Controller
{

    public function customerlist(Request $request)
    {
        $nama_api = 'customer list';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
                'route' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'signature.required' =>'Signature belum di isi',
                'route.required' =>'Route belum di isi',
            ]);

            if($validator->passes())
            {
                //  validasi all servis
                $result = BusHelper::enable_all_service();
                if($result['status'] == '0'){
                    // validasi all servis
                }else{

                    $request_time = \carbon\carbon::now();

                    // validasi blacklist
                    $result = BusHelper::check_blacklist();

                    if($result['status'] == '0')
                    {
                        // gagal
                    }else
                    {
                        // cek signature  
                        $result= BusHelper::cek_signature($request->signature);   
                        if($result != 1){
            
                            $result = [
                                'status' => 0,
                                'message' => 'Duplikat signature'
                            ];
                        }else
                        {
                            $result= BusHelper::dekrip_signature($request->signature);   
                        
                            if ($result['status'] != 1) 
                            {
                                $result = [
                                    'status' => 0,
                                    'message' => 'Signature salah'
                                ];
                            }
                            else
                            {

                                $param = [
                                    'email' => $request->email,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'route' => $request->route,
                                ];



                                // Simpan log
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'customer/list';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result->status);

                                    if($result->status == 1)
                                    {
                                        //Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 1,
                                            'message' => $result->message,
                                            'data' => $result->data,
                                        ];
                                    }else{
                                        Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 0,
                                            'message' => $result->message,
                                        ];
                                    }

                                }else{

                                    Log::Info($nama_api.' null response '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                    // PILIHAN ERROR
                                    
                                    $result = [
                                        'status' => 0,
                                        'message' => 'Tidak terhubung dengan server',
                                    ];

                                    // Update log gagal
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', 0);
                                
                                }
                            }

                         }       
                    
                    }
                }

            }else
            {
                $result = [
                    'status' => 0,
                    'message' => 'Error validation '.$validator->errors()
                ];
                Log::Info($nama_api.' error validation '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));

            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result);

    }

    public function customercreate(Request $request)
    {

        $nama_api = 'customer create';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
                'route' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'signature.required' =>'Signature belum di isi',
                'route.required' =>'Route belum di isi',
            ]);

            if($validator->passes())
            {
                //  validasi all servis
                $result = BusHelper::enable_all_service();
                if($result['status'] == '0'){
                    // validasi all servis
                }else{

                    $request_time = \carbon\carbon::now();

                    // validasi blacklist
                    $result = BusHelper::check_blacklist();

                    if($result['status'] == '0')
                    {
                        // gagal
                    }else
                    {
                        // cek signature  
                        $result= BusHelper::cek_signature($request->signature);   
                        if($result != 1){
            
                            $result = [
                                'status' => 0,
                                'message' => 'Duplikat signature'
                            ];
                        }else
                        {
                            $result= BusHelper::dekrip_signature($request->signature);   
                        
                            if ($result['status'] != 1) 
                            {
                                $result = [
                                    'status' => 0,
                                    'message' => 'Signature salah'
                                ];
                            }
                            else
                            {

                                $param = [
                                    'email' => $request->email,   
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'route' => $request->route,
                                ];


                                // Simpan log
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'customer/create';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result->status);

                                    if($result->status == 1)
                                    {
                                        Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        
                                        $result = [
                                            'status' => 1,
                                            'message' => $result->message,
                                            'data' => $result->data,
                                        ];

                                    }else{
                                        Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 0,
                                            'message' => $result->message,
                                        ];
                                    }

                                }else{

                                    Log::Info($nama_api.' null response '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                    // PILIHAN ERROR
                                    
                                    $result = [
                                        'status' => 0,
                                        'message' => 'Tidak terhubung dengan server',
                                    ];

                                    // Update log gagal
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', 0);
                                
                                }
                            }

                         }       
                    
                    }
                }

            }else
            {
                $result = [
                    'status' => 0,
                    'message' => 'Error validation '.$validator->errors()
                ];
                Log::Info($nama_api.' error validation '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));

            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result);

    }

    public function customeredit(Request $request)
    {

        $nama_api = 'customer edit';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'id' => 'required',
                'signature' => 'required',
                'route' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'id.required' =>'ID belum di isi',
                'signature.required' =>'Signature belum di isi',
                'route.required' =>'Route belum di isi',
            ]);

            if($validator->passes())
            {
                //  validasi all servis
                $result = BusHelper::enable_all_service();
                if($result['status'] == '0'){
                    // validasi all servis
                }else{

                    $request_time = \carbon\carbon::now();

                    // validasi blacklist
                    $result = BusHelper::check_blacklist();

                    if($result['status'] == '0')
                    {
                        // gagal
                    }else
                    {
                        // cek signature  
                        $result= BusHelper::cek_signature($request->signature);   
                        if($result != 1){
            
                            $result = [
                                'status' => 0,
                                'message' => 'Duplikat signature'
                            ];
                        }else
                        {
                            $result= BusHelper::dekrip_signature($request->signature);   
                        
                            if ($result['status'] != 1) 
                            {
                                $result = [
                                    'status' => 0,
                                    'message' => 'Signature salah'
                                ];
                            }
                            else
                            {

                                $param = [
                                    'email' => $request->email,
                                    'id' => $request->id,    
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'route' => $request->route,
                                ];


                                // Simpan log
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'customer/edit';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result->status);

                                    if($result->status == 1)
                                    {
                                        Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        
                                        $result = [
                                            'status' => 1,
                                            'message' => $result->message,
                                            'data' => $result->data
                                        ];

                                    }else{
                                        Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 0,
                                            'message' => $result->message,
                                        ];
                                    }

                                }else{

                                    Log::Info($nama_api.' null response '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                    // PILIHAN ERROR
                                    
                                    $result = [
                                        'status' => 0,
                                        'message' => 'Tidak terhubung dengan server',
                                    ];

                                    // Update log gagal
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', 0);
                                
                                }
                            }

                         }       
                    
                    }
                }

            }else
            {
                $result = [
                    'status' => 0,
                    'message' => 'Error validation '.$validator->errors()
                ];
                Log::Info($nama_api.' error validation '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));

            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result);

    }

    public function customerinsert(Request $request)
    {

        $nama_api = 'customer insert';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
                'nama' => 'required',
                'email' => 'required',
                'password' => 'required',
                'password_c' => 'required|same:password',
                'no_hp' => 'required|numeric|digits_between:8,16',
                'status' => 'required',

            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'signature.required' =>'Signature belum di isi',
                'nama.required' =>'Nama customer belum di isi',
                'email.required' =>'Email belum di isi',
                'password.required' =>'Password belum di isi',
                'password_c.required' =>'Password Konfirmasi belum di isi',
                'no_hp.required' =>'No Handphone belum di isi',
                'no_hp.numeric' =>'No Handphone harus angka',
                'no_hp.digits_between' =>'No Handphone harus antara 8 - 16 digit',
                'status.required' =>'Status belum di isi',

            ]);

            if($validator->passes())
            {
                //  validasi all servis
                $result = BusHelper::enable_all_service();
                if($result['status'] == '0'){
                    // validasi all servis
                }else{

                    $request_time = \carbon\carbon::now();

                    // validasi blacklist
                    $result = BusHelper::check_blacklist();

                    if($result['status'] == '0')
                    {
                        // gagal
                    }else
                    {
                        // cek signature  
                        $result= BusHelper::cek_signature($request->signature);   
                        if($result != 1){
            
                            $result = [
                                'status' => 0,
                                'message' => 'Duplikat signature'
                            ];
                        }else
                        {
                            $result= BusHelper::dekrip_signature($request->signature);   
                        
                            if ($result['status'] != 1) 
                            {
                                $result = [
                                    'status' => 0,
                                    'message' => 'Signature salah'
                                ];
                            }
                            else
                            {

                                $param = [
                                    'email' => $request->email,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'nama' => $request->nama,
                                    'email' => $request->email,
                                    'password' => $request->password,
                                    'no_hp' => $request->no_hp,
                                    'status' => $request->status,
                                    
                                ];



                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'customer/insert';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result->status);

                                    if($result->status == 1)
                                    {
                                        Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 1,
                                            'message' => $result->message,
                                            'data' => $result->data,
                                        ];
                                    }else{
                                        Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 0,
                                            'message' => $result->message,
                                        ];
                                    }

                                }else{

                                    Log::Info($nama_api.' null response '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                    // PILIHAN ERROR
                                    
                                    $result = [
                                        'status' => 0,
                                        'message' => 'Tidak terhubung dengan server',
                                    ];

                                    // Update log gagal
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', 0);
                                
                                }
                            }

                         }       
                    
                    }
                }

            }else
            {
                $result = [
                    'status' => 0,
                    'message' => 'Error validation '.$validator->errors()
                ];
                Log::Info($nama_api.' error validation '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));

            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result);

    }


    public function customerupdate(Request $request)
    {

        $nama_api = 'customer update';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
                'id' => 'required',
                'nama' => 'required',
                'email' => 'required',
                'password' => 'required',
                'no_hp' => 'required|numeric|digits_between:8,16',
                'status' => 'required',

            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'signature.required' =>'Signature belum di isi',
                'id.required' =>'ID belum di isi',
                'nama.required' =>'Nama customer belum di isi',
                'email.required' =>'Total kursi belum di isi',
                'password.required' =>'Denah kursi belum di isi',
                'no_hp.required' =>'No Handphone belum di isi',
                'no_hp.numeric' =>'No Handphone harus angka',
                'no_hp.digits_between' =>'No Handphone harus antara 8 - 16 digit',
                'status.required' =>'Status belum di isi',

            ]);

            if($validator->passes())
            {
                //  validasi all servis
                $result = BusHelper::enable_all_service();
                if($result['status'] == '0'){
                    // validasi all servis
                }else{

                    $request_time = \carbon\carbon::now();

                    // validasi blacklist
                    $result = BusHelper::check_blacklist();

                    if($result['status'] == '0')
                    {
                        // gagal
                    }else
                    {
                        // cek signature  
                        $result= BusHelper::cek_signature($request->signature);   
                        if($result != 1){
            
                            $result = [
                                'status' => 0,
                                'message' => 'Duplikat signature'
                            ];
                        }else
                        {
                            $result= BusHelper::dekrip_signature($request->signature);   
                        
                            if ($result['status'] != 1) 
                            {
                                $result = [
                                    'status' => 0,
                                    'message' => 'Signature salah'
                                ];
                            }
                            else
                            {

                                $param = [
                                    'email' => $request->email,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'id' => $request->id,    
                                    'nama' => $request->nama,    
                                    'email' => $request->email,    
                                    'password' => $request->password,                                    
                                    'no_hp' => $request->no_hp,                                    
                                    'status' => $request->status,                                    
                                ];


                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'customer/update';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result->status);

                                    if($result->status == 1)
                                    {
                                        Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 1,
                                            'message' => $result->message,
                                            'data' => $result->data,
                                        ];
                                    }else{
                                        Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 0,
                                            'message' => $result->message,
                                        ];
                                    }

                                }else{

                                    Log::Info($nama_api.' null response '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                    // PILIHAN ERROR
                                    
                                    $result = [
                                        'status' => 0,
                                        'message' => 'Tidak terhubung dengan server',
                                    ];

                                    // Update log gagal
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', 0);
                                
                                }
                            }

                         }       
                    
                    }
                }

            }else
            {
                $result = [
                    'status' => 0,
                    'message' => 'Error validation '.$validator->errors()
                ];
                Log::Info($nama_api.' error validation '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));

            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result);

    }


    public function customerdelete(Request $request)
    {

        $nama_api = 'customer delete';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'id' => 'required',
                'signature' => 'required',
                'route' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'id.required' =>'ID belum di isi',
                'signature.required' =>'Signature belum di isi',
                'route.required' =>'Route belum di isi',
            ]);

            if($validator->passes())
            {
                //  validasi all servis
                $result = BusHelper::enable_all_service();
                if($result['status'] == '0'){
                    // validasi all servis
                }else{

                    $request_time = \carbon\carbon::now();

                    // validasi blacklist
                    $result = BusHelper::check_blacklist();

                    if($result['status'] == '0')
                    {
                        // gagal
                    }else
                    {
                        // cek signature  
                        $result= BusHelper::cek_signature($request->signature);   
                        if($result != 1){
            
                            $result = [
                                'status' => 0,
                                'message' => 'Duplikat signature'
                            ];
                        }else
                        {
                            $result= BusHelper::dekrip_signature($request->signature);   
                        
                            if ($result['status'] != 1) 
                            {
                                $result = [
                                    'status' => 0,
                                    'message' => 'Signature salah'
                                ];
                            }
                            else
                            {

                                $param = [
                                    'email' => $request->email,
                                    'id' => $request->id,    
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'route' => $request->route,
                                ];


                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'customer/delete';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result->status);

                                    if($result->status == 1)
                                    {
                                        Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 1,
                                            'message' => $result->message,
                                            'data' => $result->data,
                                        ];
                                    }else{
                                        Log::Info($nama_api.' gagal '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                        $result = [
                                            'status' => 0,
                                            'message' => $result->message,
                                        ];
                                    }

                                }else{

                                    Log::Info($nama_api.' null response '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
                                    // PILIHAN ERROR
                                    
                                    $result = [
                                        'status' => 0,
                                        'message' => 'Tidak terhubung dengan server',
                                    ];

                                    // Update log gagal
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogBus', 0);
                                
                                }
                            }

                         }       
                    
                    }
                }

            }else
            {
                $result = [
                    'status' => 0,
                    'message' => 'Error validation '.$validator->errors()
                ];
                Log::Info($nama_api.' error validation '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));

            }

        }catch (\Exception $e) {
            $result = [
                'status' => 0,
                'message' => 'Error exception',
                'error' => report($e),
            ];
            Log::Info($nama_api.' error exception '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));


        }catch (\Throwable $e) {
            $result = [
                'status' => 0,
                'message' => 'Error throwable',
                'error' => report($e),
            ];

            Log::Info($nama_api.' error Throwable '.Input::get('email').' '.$_SERVER['REMOTE_ADDR'].' '.json_encode($result));
        }

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogBus', $result['status']);

        return response()->json($result);

    }
    

}
