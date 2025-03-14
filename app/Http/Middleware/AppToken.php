<?php

namespace App\Http\Middleware;

use App\DataModel\Model\Rest;
use Closure;

class AppToken
{
    public function handle($request, Closure $next)
    {
        $rest= new Rest();
        $response = $rest->validateToken();

        if($response['status']==1)
        {
            $request->merge(array('userId'=> $response['userId']));
            return $next($request);
        }
        else
        {
           return response()->json(['status'=>-1]);
        }
    }
}
