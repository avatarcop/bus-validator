<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use App\Http\Helpers\ApiHelper;


class MasterpoController extends Controller
{

    public function polist(Request $request)
    {

        $nama_api = 'po list';
    
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

                                $url = 'po/list';

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

    public function pocreate(Request $request)
    {

        $nama_api = 'po create';
    
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

                                $url = 'po/create';

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

    public function poedit(Request $request)
    {

        $nama_api = 'po edit';
    
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

                                $url = 'po/edit';

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

    public function poinsert(Request $request)
    {

        $nama_api = 'po insert';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
                'logo_po' => 'required',
                'nama_po' => 'required',
                'status' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'signature.required' =>'Signature belum di isi',
                'logo_po.required' =>'Logo PO belum di isi',
                'nama_po.required' =>'Nama PO belum di isi',
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
                                    'logo_po' => $request->logo_po,
                                    'nama_po' => $request->nama_po,
                                    'status' => $request->status,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                ];



                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'po/insert';

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


    public function poupdate(Request $request)
    {

        $nama_api = 'po update';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogBus');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'id' => 'required',
                'nama_po' => 'required',
                'status' => 'required',
                'signature' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'id.required' =>'ID belum di isi',
                'nama_po' => 'Nama PO belum diisi',
                'status' => 'Status belum diisi',
                'signature.required' =>'Signature belum di isi',
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
                                    'logo_po' => $request->logo_po,
                                    'nama_po' => $request->nama_po,
                                    'status' => $request->status,
                                ];


                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogBus');

                                $url = 'po/update';

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


    public function podelete(Request $request)
    {

        $nama_api = 'po delete';
    
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

                                $url = 'po/delete';

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
