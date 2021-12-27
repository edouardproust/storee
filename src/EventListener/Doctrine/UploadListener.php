<?php 
namespace App\EventListener\Doctrine;

use App\Entity\Upload;
use App\Repository\UploadRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/** 
 * Used to add functionalities when these events are fired: Creation on a Upload object via Doctrine
 */
class UploadListener
{

    /** @var UploadRepository */
    private $uploadRepository;

    public function __construct(UploadRepository $uploadRepository)
    {
        $this->uploadRepository = $uploadRepository;
    }

    public function prePersist(Upload $upload, LifecycleEventArgs $args): void
    {
        $this->setCreatedAt($upload);
    }

    private function setCreatedAt(Upload $upload): void
    {
        if(!$upload->getCreatedAt()) {
            $upload->setCreatedAt(new \DateTime());
        }
    }

}