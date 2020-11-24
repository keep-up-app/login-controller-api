<?php

namespace App\Controller;

use App\Controller\EndpointRequestController as ERC;
use App\Controller\Enum\HttpMethod;
use App\Controller\Enum\Endpoint;

class UserController
{
    /**
     * Makes a request to the user Api endpoint
     * 
     * This will get a user by eamil and password. 
     * By communicating with the enpoint, it can get 
     * error feedback such as when a user was not 
     * found or if there are any missing parramters
     * 
     * @param Array         // payload
     * @return UserObject   // User
     */
    
    public static function get(Array $payload = []) : Array
    {
        return ERC::request(
            HttpMethod::POST,
            Endpoint::USER,
            '/user/find',
            $payload
        );
    }
}