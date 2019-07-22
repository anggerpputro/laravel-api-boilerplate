<?php

namespace App\Traits;

trait Staticable
{
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}
