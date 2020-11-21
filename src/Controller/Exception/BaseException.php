<?php

namespace App\Controller\Exception;

use Exception;

class BaseException extends Exception 
{
    public function __construct(String $message = null, $code = 0, Exception $previous = null) {
        if ($message == null) $message = 'An error occured.';
        parent::__construct($message, $code, $previous);
    }
}