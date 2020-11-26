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
            
            $error = isset($jsonData['error']) ? "Invalid Email or Password." : $ex->getMessage();
            $details = isset($jsonData['details']) ? [
                'basic' => $jsonData['error'],
                'extensive' => $jsonData['details']
            ] : null;

            throw new RequestException($error, $response->getStatusCode(), $details, null);
        }
    }
}