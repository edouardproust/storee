<?php

namespace App\App\Helper;

use Symfony\Component\HttpKernel\KernelInterface;

class UrlHelper 
{

    /** @var string */
    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getProjectDir(): string
    {
        return $this->kernel->getProjectDir();
    }

    /**
     * Check an url before the user access it. 
     * WARNING: lows down a lot the page load if used several times on a same page!
     * @param string $url 
     * @return bool 
     */
    public static function isReturningError(string $url): bool
    {
        $array = @get_headers($url);
        if(!$array) {
            return true;
        }
        $string = $array[0];
        if(strpos($string, "200") || strpos($string, "302")) {
            return false;
        } 
        return true;
    }

}