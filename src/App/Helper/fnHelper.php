<?php

namespace App\App\Helper;

class fnHelper 
{

    public static function generateGetter(string $property): string
    {
        $getter = str_replace(['_', '-'], ' ', $property);
        $getter = ucwords($getter);
        $getter = str_replace(' ', '', $getter);
        $getter = 'get'.$getter;
        return $getter;
    }

}