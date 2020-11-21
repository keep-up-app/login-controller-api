<?php

namespace App\Controller\Exception;

use App\Controller\Exception\BaseException;

class InvalidInputException extends BaseException 
{
    private $inputInQuestion;
    private $inputContent;

    public function getInvalidInput()
    {
        return $this->inputInQuestion;
    }

    public function getInputContent()
    {
        return $this->inputContent;
    }

    public function __construct(String $message = null, $code = 0, $input = null, $value = null, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    
        $this->inputInQuestion = $input;
        $this->inputContent = $value;
    }
}