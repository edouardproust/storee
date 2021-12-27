<?php 

namespace App\Event;

use App\Entity\Upload;
use Symfony\Contracts\EventDispatcher\Event;

class UploadSuccessEvent extends Event
{

    /** @var Upload */
    private $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function getUpload(): ?Upload
    {
        return $this->upload;
    }

}