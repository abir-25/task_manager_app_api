<?php
/**
 * Created by PhpStorm.
 * User: shahyasin
 * Date: 4/8/16
 * Time: 3:07 PM
 */

namespace App\DataModel\Manager;

use Illuminate\Support\Facades\Session;

class SessionManager
{
    public function setSession($key, $value){
        Session::put($key,$value);
    }
    public function getSession($key){
        if(Session::has($key)){
            return Session::get($key);
        }
    }
    public function removeSession($key){
        if(Session::has($key)){
            Session::forget($key);
        }
    }
}
