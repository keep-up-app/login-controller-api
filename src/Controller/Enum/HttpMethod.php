<?php

namespace App\Controller\Enum;

abstract class HttpMethod 
{
    const GET       = 'GET';
    const POST      = 'POST';
    const PUT       = 'PUT';
    const PATCH     = 'PATCH';
    const DELETE    = 'DELETE';
}