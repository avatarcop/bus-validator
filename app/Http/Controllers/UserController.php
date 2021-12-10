<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Log;
use Illuminate\Support\Facades\Input;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
use App\Http\Helpers\ApiHelper;


class UserController extends Controller
{

    public function login(Request $request)
    {

        $nama_api = 'login';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'password.required' =>'Password belum di isi',
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
	                    $param = [
	                        'email' => $request->email,
	                        'password' => $request->password,
	                    ];

	                    // Simpan log
	                    $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

	                    $url = 'login';

                        $result= ApiHelper::hitLogin($param, $url);
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


    public function register(Request $request)
    {

        $nama_api = 'register';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'name_reg' => 'required',
                'email_reg' => 'required|email',
                'password_reg' => 'required',
                'c_password_reg' => 'required|same:password_reg',
                'signature' => 'required',
                'accesscontrol_id' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'name_reg.required' =>'Name belum di isi',
                'email_reg.required' =>'Email register belum di isi',
                'password_reg.required' =>'Password belum di isi',
                'c_password_reg.required' =>'Password belum di isi',
                'signature.required' =>'Signature belum di isi',
                'accesscontrol_id.required' =>'Access control belum di isi',
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
                                    'name_reg' => $request->name_reg,
                                    'email_reg' => $request->email_reg,
                                    'password_reg' => $request->password_reg,
                                    'c_password_reg' => $request->c_password_reg,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'accesscontrol_id' => $request->accesscontrol_id,
                                ];



                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'user/register';

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
                                            'acl' => $result->acl,
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

    public function usercreate(Request $request)
    {

        $nama_api = 'user create';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
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
                                    'route' => $request->route,
                                ];


                                // Simpan log
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'user/create';

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

    public function useredit(Request $request)
    {

        $nama_api = 'user edit';
    
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

                                $url = 'user/edit';

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
                                            'data' => $result->data,
                                            'data_acl' => $result->data_acl,
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

    public function userupdate(Request $request)
    {

        $nama_api = 'user update';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'id' => 'required',
                'name_reg' => 'required',
                'email_reg' => 'required|email',
                'password_reg' => 'required',
                'c_password_reg' => 'required|same:password_reg',
                'signature' => 'required',
                'accesscontrol_id' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
                'id.required' =>'ID belum di isi',
                'name_reg.required' => 'Nama belum diisi',
                'email_reg.required' => 'Email belum diisi',
                'password_reg.required' => 'Password belum diisi',
                'c_password_reg.required' => 'Password konfirmasi belum diisi',
                'signature.required.required' =>'Signature belum di isi',
                'accesscontrol_id.required' =>'Access control belum di isi',
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
                                    'name_reg' => $request->name_reg,
                                    'email_reg' => $request->email_reg,
                                    'password_reg' => $request->password_reg,
                                    'c_password_reg' => $request->c_password_reg,
                                    'backend_signature' => $result['backend_signature'],
                                    'token' => $request->token,
                                    'accesscontrol_id' => $request->accesscontrol_id,
                                ];


                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'user/update';

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
                                            'acl' => $result->acl,
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

    public function userdelete(Request $request)
    {

        $nama_api = 'user delete';
    
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

                                $url = 'user/delete';

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

    public function userlist(Request $request)
    {

        $nama_api = 'user list';
    
        // Simpan log
        // $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',
                'signature' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
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
                                    'route' => $request->route,
                                ];



                                // Simpan log
                                // $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'user/list';

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


    public function logout(Request $request)
    {

        $nama_api = 'logout';
    
        // Simpan log
        $simpanlog_awal = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'frontend', $_SERVER['REMOTE_ADDR'], 'LogUser');

        try
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email',
                'token' => 'required',  
                'signature' => 'required',
            ],[
                'email.required' => 'Email belum diisi',
                'token.required' => 'Token belum diisi',
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
                                ];



                                // Simpan log
                                $simpanlog = BusHelper::simpan_log($nama_api, $request->email, json_encode($request->all()), \Carbon\Carbon::now(), 'backend', $_SERVER['REMOTE_ADDR'], 'LogUser');

                                $url = 'logout';

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


}
