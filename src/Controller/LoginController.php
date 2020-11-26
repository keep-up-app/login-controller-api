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

            unset($user['password']);
            unset($user['auth']['secret']);

            return new Response(
                json_encode($user),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
        catch(InvalidInputException | RequestException $ex)
        {
            return new Response(
                json_encode([
                    'error' => $ex->getMessage(),
                    'details' => $ex->getDetails()
                ]),
                $ex->getCode(),
                ['content-type' => 'application/json']
            );
        }
    }

    private function handleBasicAuthentication($params, $user = null)
    {
        Validator::make($params, ['email', 'password']);

        if ($user == null) $user = User::get($params);

        if ($user['auth']['enabled'])
        {
            return $this->handle2FAAuthentication($params, $user);
        }

        return $user;
    }

    private function handle2FAAuthentication($params, $user = null)
    {
        $_id = isset($user) ? $user['_id'] : $params['_id'];

        Validator::make($params, ['token', '_id'], [ '_id' => $_id ]);
        
        if ($user == null) $user = User::get([ '_id' => $params['_id'] ]);

        if (!$user['auth']['enabled'])
        {
            return $this->handleBasicAuthentication($params, $user);
        }
        
        $token = $params['token'];
        $secret = $user['auth']['secret'];

        if (!TFAC::verifyToken($token, $secret))
        {
            throw new InvalidInputException('Invalid Token.', 403, [ '_id' => $user['_id'] ]);
        }

        return $user;
    }
}