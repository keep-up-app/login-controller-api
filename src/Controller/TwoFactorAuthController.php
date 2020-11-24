<?php

namespace App\Controller;

use App\Controller\EndpointRequestController as ERC;
use App\Controller\Enum\HttpMethod;
use App\Controller\Enum\Endpoint;

class TwoFactorAuthController
{
    public static function generateSecret($length = 20) : String
    {
        return ERC::request(
            HttpMethod::GET,
            Endpoint::AUTH,
            '/auth/generate/secret/base32/' . $length
        )['secret'];
    }

    public static function verifyToken($token, $secret)
    {
        return ERC::request(
            HttpMethod::POST,
            Endpoint::AUTH,
            '/auth/verify/token/base32',
            [
                'token' => $token,
                'secret' => $secret
            ]
        );
    }
}