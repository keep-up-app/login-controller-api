<?php

namespace App\Controller;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Exception\RequestException;
use App\Controller\Exception\InvalidInputException;

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
     * @param Object
     * @return UserObject
     */

    public static function get(Array $payload = [], $toJson = true) : Array
    {
        try
        {
            $httpClient = HttpClient::create();

            $response = $httpClient->request('POST', 'https://user-api-endpoint.herokuapp.com' . '/user/find', [ 'json' => $payload ]);

            $jsonData = json_decode($response->getContent(false), true);

            if ($response->getStatusCode() == Response::HTTP_OK)
            {
                return $toJson ? $jsonData : json_encode($jsonData);
            }
            else
            {
                if ($response->getStatusCode() == Response::HTTP_BAD_REQUEST)
                {
                    throw new InvalidInputException('Invalid Email or Password.' , $response->getStatusCode());
                }
                else
                {
                    $error = $jsonData['error'];
                    $details = isset($jsonData['details']) ? $jsonData['details'] : "No details.";
                    throw new RequestException($error, $response->getStatusCode());
                }
            }
        }
        catch(ClientException $ce)
        {
            throw new RequestException($ce->getMessage(), $ce->getCode(), $ce);
        }
    }
}