<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Exception\InvalidInputException;
use App\Controller\Exception\RequestException;
use App\Controller\ValidationController as Validator;
use App\Controller\TwoFactorAuthController as TFAC;
use App\Controller\UserController as User;

class LoginController extends AbstractController
{
    /**
     * @Route("/", methods={"POST"})
     */
    public function index(Request $request) : Response
    {
        $params = json_decode($request->getContent(), true);

        try
        {
            Validator::make($params, ['email', 'password']);

            $userData = [
                'email' => $params['email'],
                'password' => $params['password'],
            ];
            
            $user = User::get($userData, true);
            unset($user{'password'});

            //mail('greffnoah@gmail.com', 'test', 'test message');

            if ($user['auth']['enabled'])
            {
                
                if (isset($params['token']))
                {
                    $token = $params['token'];
                    $secret = $user['auth']['secret'];

                    if (!TFAC::verifyToken($token, $secret)['valid'])
                    {
                        return new Response(
                            json_encode([
                                'error' => 'Invalid token.',
                                '_id' => $user['_id']
                            ]),
                            Response::HTTP_UNAUTHORIZED,
                            ['content-type' => 'application/json']
                        );
                    }
                }
                else
                {
                    return new Response(
                        json_encode([ 'error' => 'Missing token for login validation.' ]),
                        Response::HTTP_UNAUTHORIZED,
                        ['content-type' => 'application/json']
                    );
                }
            }

            return new Response(
                json_encode($user),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
        catch(InvalidInputException | RequestException $ex)
        {
            $errorContent = [
                'error' => $ex->getMessage(),
                'details' => $ex->getDetails()
            ];

            return new Response(
                json_encode($errorContent),
                $ex->getCode(),
                ['content-type' => 'application/json']
            );
        }
    }
}