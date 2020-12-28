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
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/login/basic", methods={"POST"})
     */
    public function loginBasicAuth(MailerInterface $mailer, Request $request) : Response
    {
        try
        {
            $params = json_decode($request->getContent(), true);
            Validator::make($params, ['email', 'password']);

            $user = User::get($params);

            if ($user['auth']['enabled'])
            {
                $secret = $user['auth']['secret'];
                $tokenData = TFAC::getTokenFromSecret($secret);

                $email = (new Email())
                    ->from('greffnoah@gmail.com')
                    ->to($user['email'])
                    ->subject("Here is your login token")
                    ->html("<h1>Token: {$tokenData['token']}</h1><p>This token will expire in {$tokenData['remaining']}s</p>");

                $mailer->send($email);

                return new Response(
                    json_encode([ '_id' => $user['_id'] ]),
                    Response::HTTP_TEMPORARY_REDIRECT,
                    ['content-type' => 'application/json']
                );
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

    /**
     * @Route("/login/2fa", methods={"POST"})
     */
    public function loaginTwoFactorAuth(Request $request) : Response
    {
        try
        {
            $params = json_decode($request->getContent(), true);
            Validator::make($params, ['token', '_id']);

            $user = User::get([ '_id' => $params['_id'] ]);

            $token = $params['token'];
            $secret = $user['auth']['secret'];
    
            if (!TFAC::verifyToken($token, $secret))
            {
                throw new InvalidInputException('Invalid Token.', 403);
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
}