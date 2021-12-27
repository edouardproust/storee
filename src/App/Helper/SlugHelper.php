<?php

namespace App\App\Helper;

class SlugHelper
{

    /**
     * Get a name from a slug. Eg. get 'Main Image' from 'mainImage'
     * @param string $slug 
     * @param bool $capitalize Capitalize output?
     * @return string 
     */
    public static function nameFromSlug(string $slug, bool $capitalize = false): string
    {
        $name = $slug;
        // dashed
        $name = str_replace(['-', '_'], ' ', $slug);
        // camelcase
        $name = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $name);
        // capitalize
        if($capitalize) {
            $name = ucwords($name);
        } else {
            $name = strtolower($name);
        }

        return $name;
    }

}