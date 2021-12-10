<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use App\Http\Helpers\ApiHelper;


class MasterbusController extends Controller
{

    public function buslist(Request $request)
    {
Log::Info('wooo');
        $nama_api = 'bus list';
    
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

                                $url = 'bus/list';

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

    public function buscreate(Request $request)
    {

        $nama_api = 'bus create';
    
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

                                $url = 'bus/create';

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
                                            'data_tipekursi' => $result->data_tipekursi,
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

    public function busedit(Request $request)
    {

        $nama_api = 'bus edit';
    
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

                                $url = 'bus/edit';

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
                                            'data_po' => $result->data_po,
                                            'data_tipekursi' => $result->data_tipekursi,
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

    public function businsert(Request $request)
    {

        $nama_api = 'bus insert';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
                'po_id' => 'required',
                'nama_bus' => 'required',
                'tipekursi_id' => 'required',
                'terminal_berangkat' => 'required',
                'terminal_tujuan' => 'required',
                'jumlah_kursi' => 'required|numeric',
                'harga_tiket' => 'required|numeric',
                'waktu_berangkat' => 'required',
                'estimasi_tiba' => 'required',
                'status' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'signature.required' =>'Signature belum di isi',
                'po_id.required' =>'PO belum di isi',
                'nama_bus.required' =>'Nama bus belum di isi',
                'tipekursi_id.required' =>'Tipe kursi belum di isi',
                'terminal_berangkat.required' =>'Terminal berangkat belum di isi',
                'terminal_tujuan.required' =>'Terminal tujuan belum di isi',
                'jumlah_kursi.required' =>'Jumlah kursi belum di isi',
                'jumlah_kursi.numeric' =>'Jumlah kursi harus angka',
                'harga_tiket.required' =>'Harga tiket belum di isi',
                'harga_tiket.numeric' =>'Harga tiket harus angka',
                'waktu_berangkat.required' =>'Waktu berangkat belum di isi',
                'estimasi_tiba.required' =>'Estimasi tiba belum di isi',
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
                                    'po_id' => $request->po_id,
                                    'nama_bus' => $request->nama_bus,
                                    'tipekursi_id' => $request->tipekursi_id,
                                    'terminal_berangkat' => $request->terminal_berangkat,
                                    'terminal_tujuan' => $request->terminal_tujuan,
                                    'jumlah_kursi' => $request->jumlah_kursi,
                                    'harga_tiket' => $request->harga_tiket,
                                    'waktu_berangkat' => $request->waktu_berangkat,
                                    'estimasi_tiba' => $request->estimasi_tiba,
                                    'status' => $request->status,
                                ];



                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'bus/insert';

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


    public function busupdate(Request $request)
    {

        $nama_api = 'bus update';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'id' => 'required',
                'signature' => 'required',
                'po_id' => 'required',
                'nama_bus' => 'required',
                'tipekursi_id' => 'required',
                'terminal_berangkat' => 'required',
                'terminal_tujuan' => 'required',
                'jumlah_kursi' => 'required|numeric',
                'harga_tiket' => 'required|numeric',
                'waktu_berangkat' => 'required',
                'estimasi_tiba' => 'required',
                'status' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'id.required' =>'ID belum di isi',
                'signature.required' =>'Signature belum di isi',
                'po_id.required' =>'PO belum di isi',
                'nama_bus.required' =>'Nama bus belum di isi',
                'tipekursi_id.required' =>'Tipe kursi belum di isi',
                'terminal_berangkat.required' =>'Terminal berangkat belum di isi',
                'terminal_tujuan.required' =>'Terminal tujuan belum di isi',
                'jumlah_kursi.required' =>'Jumlah kursi belum di isi',
                'jumlah_kursi.numeric' =>'Jumlah kursi harus angka',
                'harga_tiket.required' =>'Harga tiket belum di isi',
                'harga_tiket.numeric' =>'Harga tiket harus angka',
                'waktu_berangkat.required' =>'Waktu berangkat belum di isi',
                'estimasi_tiba.required' =>'Estimasi tiba belum di isi',
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
                                    'po_id' => $request->po_id,    
                                    'nama_bus' => $request->nama_bus,    
                                    'tipekursi_id' => $request->tipekursi_id,    
                                    'terminal_berangkat' => $request->terminal_berangkat,    
                                    'terminal_tujuan' => $request->terminal_tujuan,    
                                    'jumlah_kursi' => $request->jumlah_kursi,    
                                    'harga_tiket' => $request->harga_tiket,    
                                    'waktu_berangkat' => $request->waktu_berangkat,    
                                    'estimasi_tiba' => $request->estimasi_tiba,      
                                    'status' => $request->status,    
                                    
                                ];


                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'bus/update';

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


    public function busdelete(Request $request)
    {

        $nama_api = 'bus delete';
    
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

                                $url = 'bus/delete';

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
