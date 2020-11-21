<?php

namespace App\Controller;

use App\Controller\Exception\InvalidInputException;

class ValidationController
{
    public static function make($params)
    {
        $params = self::flatten($params);

        if ($params == null || !isset($params['password']) || !isset($params['email']))
        {
            throw new InvalidInputException('Missing fields.', 400);
        }

        foreach($params as $key => $value)
        {
            if ($value == null || $value == '' || is_array($value))
            {
                throw new InvalidInputException('Missing ' . ucfirst($key) . '.', 400);
            }
        }

        unset($value);
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