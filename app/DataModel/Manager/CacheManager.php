<?php
/**
 * Created by PhpStorm.
 * User: shahyasin
 * Date: 4/8/16
 * Time: 3:07 PM
 */

namespace App\DataModel\Manager;

use App\DataModel\Model\RbsArray;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class CacheManager
{
    public function getFromCache($key)
    {
        if($key)
        {
            if (Cache::has($key)) {
                return Cache::get($key);
            }
        }
    }
    public function removeFromCache($key)
    {
        if($key)
        {
            if (Cache::has($key)) {
                return Cache::forget($key);
            }
        }
    }

    public function setToCache($key, $value)
    {
        /*$current=time();
        $endOfTheDay=strtotime(date("Y-m-d 23:59:59",$current));
        $diff=$endOfTheDay-$current;
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow()->addHours(6);*/
        //$expiresAt = now()->addMinutes(2);
        $expiresAt=$tomorrow = Carbon::tomorrow()->addHours(6);
        if($key)
        {
            Cache::put($key, $value, $expiresAt);
        }
    }

    /*public function setToCache($key, $value)
    {
        if($key)
        {
            //$this->removeFromCache($key);
            Cache::put($key, $value, 1440);
        }
    }*/

    public function setToCacheCounter($key, $value)
    {
        if($key)
        {
            $this->removeFromCache($key);
            Cache::put($key, $value, 525600);
        }
    }

    public function setToCacheForever($key, $value)
    {
        if($key)
        {
            Cache::forever($key, $value);
        }
    }

    public function setToCacheTimeZoneSpecific($key, $value, $timeZone)
    {
        //$ttl = Carbon::createFromTimeString('23:59')->addHours($difference);
        $ttl = Carbon::createFromTimeString('23:59:59',$timeZone)->addHours(6);
        if($key)
        {
            Cache::put($key, $value, $ttl);
        }
    }
}