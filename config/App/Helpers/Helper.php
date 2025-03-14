<?php

namespace App\Helpers;

function globalResponse($data, $message, $status, $statusCode): \Illuminate\Http\JsonResponse
{
    return response()->json(['data'=>$data, 'message'=>$message, 'status'=>$status], $statusCode);
}
