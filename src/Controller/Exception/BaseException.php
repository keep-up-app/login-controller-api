<?php

namespace App\Controller\Exception;

use Exception;

class BaseException extends Exception 
{
    private $errorDetail;

    public function getDetails()
    {
        return $this->errorDetail;
    }

    public function __construct(String $message = null, $code = 0, $details = null, Exception $previous = null) {
        if ($message == null) $message = 'An error occured.';
        if ($details == null) $this->errorDetail = 'No details.';
        parent::__construct($message, $code, $previous);
    }
}