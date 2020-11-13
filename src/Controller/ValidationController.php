<?php

namespace App\Controller;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

use Exception;

class ValidationController extends Exception
{
    private $error;

    public function getErrorMessage()
    {
        return $this->error;
    }

    public function make($params)
    {
        if ($params == null) return 'Missing fields.';
        if (!isset($params['password']) || !isset($params['email'])) return false;

        foreach($params as $key => $value)
        {
            if ($value == null || $value == '')
            {
                $this->error = 'Missing ' . ucfirst($key) . '.';
                return false;
            }
        }

        unset($value);

        return true;
    }
}