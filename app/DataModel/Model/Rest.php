<?php

/**
 * Rest API:
 */

namespace App\DataModel\Model;

use App\DataModel\Manager\ShopManager;
use App\DataModel\Manager\UserManager;
use Exception;

class Rest {
    public function validateToken(): array
    {
        try
        {
            $token = $this->getBearerToken();
            $payload = JWT::decode($token, TaskArray::$apiSecretKey, ['HS256']);

            return array(
                'userId'=>$payload->userId,
                'status'=>1
            );

        } catch (Exception $e) {
            return array('msg'=>$e->getMessage(),'status'=>-1);
        }
    }

    /**
     * Get header Authorization
     * */
    public function getAuthorizationHeader(): ?string
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    public function getBearerToken(): string
    {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return 'Access Token Not found';
    }
}
