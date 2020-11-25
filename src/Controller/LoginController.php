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
            if (isset($params['token']))
            {
                $user = $this->handle2FAAuthentication($params);
            }
            else
            {
                $user = $this->handleBasicAuthentication($params);
            }

            return new Response(
                $user,
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

    private function handleBasicAuthentication($params, $user = null)
    {
        Validator::make($params, ['email', 'password']);

        if ($user == null) $user = User::get($params);
        unset($user{'password'});

        if ($user['auth']['enabled'])
        {
            return $this->handle2FAAuthentication($params, $user);
        }

        return json_encode($user);
    }

    private function handle2FAAuthentication($params, $user = null)
    {
        Validator::make($params, ['token', '_id']);
        
        if ($user == null) $user = User::get([ '_id' => $params['_id'] ]);
        unset($user{'password'});

        if (!$user['auth']['enabled'])
        {
            return $this->handleBasicAuthentication($params, $user);
        }
        
        $token = $params['token'];
        $secret = $user['auth']['secret'];

        if (!TFAC::verifyToken($token, $secret))
        {
            throw new InvalidInputException('Invalid Token.', 403);
        }

        return json_encode($user);
    }
}