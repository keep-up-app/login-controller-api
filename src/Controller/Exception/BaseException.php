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
        if (!isset($message)) $message = 'An error occured.';
        $this->errorDetail = isset($details) ? $details : 'No details.';

        parent::__construct($message, $code, $previous);
    }
}