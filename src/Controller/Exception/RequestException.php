<?php

namespace App\Controller\Exception;

use App\Controller\Exception\BaseException;

class RequestException extends BaseException 
{
    private $errorDetail;

    public function getDetails()
    {
        return $this->errorDetail;
    }

    public function __construct(String $message = null, $code = 0, $details = null, Exception $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->errorDetail = $details;
    }
}