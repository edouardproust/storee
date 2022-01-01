<?php 

namespace App\App;

/** 
 * - PATH_NAME_REL for a relative path. Eg. 'img/products'
 * - PATH_NAME_ABS for an absolute path (directory). Eg. '/var/www/my-website/public/img/products'
*/
class Path
{

    private $projectDir;

    /**
     * @param mixed $appDir Absolute root path of the app 
     */
    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function PROJECT_DIR() { return $this->projectDir; }

    public function ROOT() { return $this->PROJECT_DIR()."/public"; }

    public function IMG_REL() { return "/img/"; }
    public function IMG_ABS() { return $this->abs($this->IMG_REL()); }

    public function IMG_SETTINGS_DEFAULT_REL() { return "/img/settings/default/"; }
    public function IMG_SETTINGS_DEFAULT_ABS() { return $this->abs($this->IMG_SETTINGS_DEFAULT_REL()); }

    public function UPLOADS_REL() { return "/uploads/"; }
    public function UPLOADS_ABS() { return $this->abs($this->UPLOADS_REL()); }

    public function UPLOADS_SETTINGS_REL() { return "/uploads/settings/"; }
    public function UPLOADS_SETTINGS_ABS() { return $this->abs($this->UPLOADS_SETTINGS_REL()); }

    public function UPLOADS_PRODUCTS_IMG_REL() { return "/uploads/products/img/"; }
    public function UPLOADS_PRODUCTS_IMG_ABS() { return $this->abs($this->UPLOADS_PRODUCTS_IMG_REL()); }
    

    /**
     * Get Relative path from the absolute one
     * @param null|string $absolutePath 
     * @return string 
     */
    public function rel(?string $absolutePath = null): string
    {
        $startPos = strlen($this->ROOT());
        return substr($absolutePath, $startPos);
    }

    /**
     * Get Absolute path from the relative one
     * @param string|null $relativePath Default: null (= 'public' directory)
     * @return string The absolute path 
     */
    private function abs(?string $relativePath = null)
    {
        return $this->ROOT().$relativePath;
    }

}