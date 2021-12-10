<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use App\Http\Helpers\ApiHelper;


class AclController extends Controller
{

    public function accesscontrollist(Request $request)
    {

        $nama_api = 'accesscontrol list';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

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
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'accesscontrol/list';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result->status);

                                    if($result->status == 1)
                                    {
                                       // Log::Info($nama_api.' sukses '.Input::get('email').' '.$_SERVER['REMOTE_ADDR']);
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
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', 0);
                                
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

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result);

    }

    public function accesscontrolinsert(Request $request)
    {

        $nama_api = 'accesscontrol insert';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'routelist' => 'required',
                'role_code' => 'required',
                'role_name' => 'required',
                'signature' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'routelist.required' =>'Route list belum di isi',
                'role_code.required' =>'Role code belum di isi',
                'role_name.required' =>'Role name belum di isi',
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
                                    'routelist' => $request->routelist,
                                    'role_code' => $request->role_code,
                                    'role_name' => $request->role_name,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                ];



                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'accesscontrol/insert';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result->status);

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
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', 0);
                                
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

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result);

    }

    public function accesscontrolcreate(Request $request)
    {

        $nama_api = 'accesscontrol create';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

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
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'accesscontrol/create';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result->status);

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
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', 0);
                                
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

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result);

    }

    public function accesscontroledit(Request $request)
    {

        $nama_api = 'accesscontrol edit';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

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
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'accesscontrol/edit';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result->status);

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
                                    // $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', 0);
                                
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

        // $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result);

    }

    public function accesscontrolupdate(Request $request)
    {

        $nama_api = 'accesscontrol update';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'id' => 'required',
                'role_code' => 'required',
                'role_name' => 'required',
                'signature' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'id.required' =>'ID belum di isi',
                'role_code' => 'Role code belum diisi',
                'role_name' => 'Role name belum diisi',
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
                                    'id' => $request->id,    
                                    'routelist' => $request->routelist,
                                    'role_code' => $request->role_code,
                                    'role_name' => $request->role_name,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                ];


                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'accesscontrol/update';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result->status);

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
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', 0);
                                
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

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result);

    }

    public function accesscontroldelete(Request $request)
    {

        $nama_api = 'accesscontrol delete';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

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
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'accesscontrol/delete';

                                $result= ApiHelper::hitBackend($param, $url);
                                $result = json_decode($result);

                                if (isset($result->status))
                                {
                                    // Update log success
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result->status);

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
                                    $update_log = BusHelper::update_log($simpanlog, json_encode($result), \Carbon\Carbon::now(), 'LogUser', 0);
                                
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

        $update_log_awal = BusHelper::update_log($simpanlog_awal, json_encode($result), \Carbon\Carbon::now(), 'LogUser', $result['status']);

        return response()->json($result);

    }

    

}
