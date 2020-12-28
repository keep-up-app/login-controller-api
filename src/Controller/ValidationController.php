<?php

namespace App\Controller;

use App\Controller\Exception\InvalidInputException;

class ValidationController
{
    public static function make($params = [], $reqs = [])
    {
        $params = self::flatten($params);
        $reqs = self::flatten($reqs);
        
        for ($i = 0; $i < count($reqs); $i++)
        {
            if (!array_key_exists($reqs[$i], $params))
            {
                throw new InvalidInputException("Missing " . ucfirst($reqs[$i]) . ".", 400); 
            }
        }
    }

    private static function flatten($params, $flattened = [])
    {
        $keys = array_keys($params);

        for ($i = 0; $i < count($keys); $i++)
        {
            $value = $params[$keys[$i]];

            if (is_array($value)) 
            {
                return self::flatten($value, $flattened);
            }
            else
            {
                $flattened[$keys[$i]] = $value;
            }
        }
        
        return $flattened;
    }
}