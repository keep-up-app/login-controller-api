<?php


namespace App\Controller;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Exception\RequestException;

class EndpointRequestController
{
    public static function request(String $method, String $endpoint, String $path = '/', Array $payload = [])
    {
        try
        {
            $httpClient = HttpClient::create();
            $response = $httpClient->request($method, $endpoint . $path, [ 'json' => $payload ]);
            return json_decode($response->getContent(), true);
        }
        catch(ClientException $ex)
        {
            $jsonData = json_decode($response->getContent(false), true);
            
            $error = isset($jsonData['error']) 
                ? $jsonData['error'] == 'User already exists.' 
                    ? 'Email already taken.' 
                    : $jsonData['error'] 
                : $ex->getmessage();
                
            $details = isset($jsonData['details']) ? $jsonData['details'] : "No details.";

            throw new RequestException($error, $ex->getCode(), $details, null, $ex);
        }
    }
}