<?php

namespace App\Http\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Log;
use App\Models\MasterSetting;
use App\Http\Helpers\BusHelper;
class ApiHelper extends Controller
{
    public static function url_serverapi()
    {
        $lok = BusHelper::setting('set_url_lokal');    
        $dev = BusHelper::setting('set_url_development'); 
        $prod = BusHelper::setting('set_url_production');  
        $urllok = BusHelper::setting('url_lokal');    
        $urldev = BusHelper::setting('url_development'); 
        $urlprod = BusHelper::setting('url_production'); 

        if(isset($lok) && isset($dev) && isset($prod) && isset($urllok) && isset($urldev) && isset($urlprod) )
        {
            
            if($lok==1 && $prod==0 && $prod==0)
            {
                $url = $urllok;
            }elseif($dev==1 && $lok==0 && $prod==0)
            {
                $url = $urldev;
            }elseif($prod==1 && $lok==0 && $dev==0)
            {
                $url = $urldev;
            }else{
                $url = '';
                
            }

        }else{
            $url = '';
           
        }

        return $url;

    } 

    public static function hitLogin($param=[], $url_backend='') 
    {
        $url = self::url_serverapi();
        $url_msg   = BusHelper::setting('urlerror_message');
        if(!$url_msg)
        {
            $msg = 'url error';
        }else{
            $msg = $url_msg;
        }

        if(empty($url))
        {
            return json_encode(array('status' => 0, 'message' => $msg));
        }

        $url = $url."/".$url_backend;

        $header = array(
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($param));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return $result;
    }

    public static function hitBackend($param=[], $url_backend='') 
    {
        $url = self::url_serverapi();
        $url_msg   = BusHelper::setting('urlerror_message');
        if(!$url_msg)
        {
            $msg = 'url error';
        }else{
            $msg = $url_msg;
        }

        if(empty($url))
        {
            return json_encode(array('status' => 0, 'message' => $msg));
        }

        $url = $url."/".$url_backend;

        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$param['token']
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($param));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return $result;

    } 

}