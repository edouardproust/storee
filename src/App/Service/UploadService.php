<?php

namespace App\App\Service;

use App\App\Path;
use App\Entity\Upload;
use App\Event\UploadSuccessEvent;
use App\Repository\UploadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UploadService
{
    /** @var SluggerInterface */
    private $slugger;
    /** @var Path */
    private $path;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var UploadRepository */
    private $uploadRepository;

    public function __construct(
        SluggerInterface $slugger, 
        Path $path, 
        EntityManagerInterface $entityManager, 
        FlashBagInterface $flashBag, 
        UrlGeneratorInterface $urlGenerator,
        UploadRepository $uploadRepository
    ){
        $this->slugger = $slugger;
        $this->path= $path;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
        $this->uploadRepository = $uploadRepository;
    }

    /**
     * @param UploadedFile|null $file 
     * @param string $targetDirectory Absolute path of the directory where the file will be save
     * @return Upload|FileException|null
     */
    public function upload(?UploadedFile $file, string $targetDirectory)
    {
        if($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            // save file in 'uploads' folder
            try {
                $file->move($targetDirectory, $fileName);
            } catch (FileException $e) {
                return $e;
            }
            // add entry in database
            $upload = (new Upload)
                ->setName($fileName)
                ->setUrl($this->path->rel($targetDirectory.$fileName));
            $this->entityManager->persist($upload);
            $this->entityManager->flush();
            return $upload;
        }
        return null;
    }

    /**
     * - Get product's main image url (absolute path) from the Upload object
     * @param Upload|string|null $upload Upload for a manually uploaded image. String for a setting or a fixture image. If null, method will return a default image placeholder.
     * @return null|string The image URL (absolute path)
     */
    public function getUploadedImageUrl($image): ?string
    {
        if(!empty($image)) {
            if($image instanceof Upload) { // is manually uploaded file
                return $image->getUrl();
            } elseif(is_string($image)) {
                if(strpos($image, 'picsum.photos') || strpos($image, $this->path->IMG_SETTINGS_DEFAULT_REL())) { // is fixture
                    return $image;
                } else { // is a setting image
                    return $this->path->UPLOADS_SETTINGS_REL().$image;
                }
            }
        } else {
            return '/img/image-placeholder.png';
        }
    }

    /**
     * Remove an Upload entry from database ('upload' table)
     * @param null|Upload $previousUpload The upload to be removed
     * @return bool Return TRUE on success / FALSE on failure 
     */
    public function removeUpload(?Upload $upload): bool
    {
        if($upload) {
            // check if the upload is not used more than once (to prevent database constraints violation)
            if(
                count($count1 = $upload->getProducts()->toArray()) > 0 || 
                count($count2 = $upload->getAdminSettings()->toArray()) > 0 ||
                count($count1 + $count2) > 0
            ){
                $this->flashBag->add('primary','The previous image was not removed from <a href="'.$this->urlGenerator->generate('admin_uploads').'">library</a> as it is used somewhere else.');
                return true; // return success status even if no file was deleted (because no error occured)
            }
            // proceed to removal
            try{
                $ddbUpload = $this->uploadRepository->findOneBy(['id' => $upload->getId()]);
                $this->entityManager->remove($ddbUpload);
                $this->entityManager->flush();
            } catch(Exception $e) {
                $this->flashBag->add('danger', 'An error occured while trying to delete the previous image: '.$e->getMessage());
                return false; // return failure status
            }
        }
        return true;
    }

    /**
     * Get a list of files of a directory and sub-directories
     * @param string $directory Folder of the files. Must be an absolute path.
     * @return array The files list containing the name of the file and its url (relative path). Eg. [ fileName1 => fileUrl1, fileName2 => fileUrl2, ... ]
     */
    public function getFilesList(string $directory): array
    {
        $list = [];

        function nestedList($directory) {
            $nestedList = [];
            $files = scandir($directory) ?? [];

            unset($files[array_search('.', $files, true)]);
            unset($files[array_search('..', $files, true)]);

            foreach($files as $file){
                if(is_dir($directory.$file)) {
                    $newDir = $directory.$file . '/';
                    $nestedList[] = [$file => nestedList($newDir)];
                } else {
                    $nestedList[] = [$file => $directory.$file];
                }
            }
            return $nestedList;
        };

        $nestedList = nestedList($directory);
        array_walk_recursive($nestedList, function($a, $b) use (&$list) { $list[$b] = $a; });
        return $list;
    }

    /**
     * Check if a file exists
     * @param string|Upload $file 
     * @return bool
     */
    public function checkIfFileExists($file): bool
    {
        if($file instanceof Upload) {
            $file = $this->path->ROOT() . $file->getUrl();
        } else {
            if($file[0] === '/') $file = ltrim($file, $file[0]); // remove slash at the begening of url
        }
        if(file_exists($file) && is_file($file)) {
            return true;
        }
        return false;
    }

    public function getExistingFilesList(array $files): array
    {
        $existingFiles = [];
        foreach($files as $file) {
            if($this->checkIfFileExists($file)) {
                $existingFiles[] = $file;
            }
        }
        return $existingFiles;
    }

    /**
     * Clean storage: remove all files stored in the 'upload' folder (or any other folder of your choice) that are not indexed in database
     * @param null|string $storageDir The directory to scan
     * @return int|Exception int on success (number of removed files) / Exception object on failure.
     */
    public function cleanStorage(?string $storageDir = null)
    {
        if(!$storageDir) $storageDir = $this->path->UPLOADS_ABS();

        try{
            $allLocalFiles = $this->getFilesList($storageDir);
            $allDatabaseUploads = $this->uploadRepository->findAll();
            // get a list of names of database uploads
            $databaseUploadsNames = [];
            foreach($allDatabaseUploads as $upload) {
                $databaseUploadsNames[] = $upload->getName();
            }
            // compare storage files to this list + purge!
            $removedFiles = 0;
            foreach($allLocalFiles as $fileName => $filePath) {
                if(!in_array($fileName, $databaseUploadsNames)) {
                    unlink($filePath);
                    $removedFiles ++;
                }
            }
        } catch (\Exception $error) {
            return $error;
        }

        return $removedFiles;
    }

}