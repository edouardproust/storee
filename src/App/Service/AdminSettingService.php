<?php

namespace App\App\Service;

use App\App\Path;
use App\Entity\Upload;
use App\Entity\AdminSetting;
use App\App\Helper\SlugHelper;
use Symfony\Component\Form\Form;
use App\App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdminSettingRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminSettingService
{

    /** @var Path */
    private $path;

    /** @var UploadService */
    private $uploadService;

    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(EntityManagerInterface $entityManager, Path $path, AdminSettingRepository $adminSettingRepository, UploadService $uploadService, FlashBagInterface $flashBag, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->path = $path;
        $this->adminSettingRepository = $adminSettingRepository;
        $this->uploadService = $uploadService;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Show value of an AdminSetting object, based on its slug
     * @param string $settingSlug The slug of the setting. Eg. 'siteName', 'siteDescription', 'logo',...
     * @return mixed The setting's value
     */
    public function getValue(string $slug)
    {
        $settingObject = $this->adminSettingRepository->getOne($slug);
        return $settingObject->getValue();
    }

    /**
     * Get the Upload object of a setting (logo, etc.). This method is used to pass variable to twig templates.
     * @param string $settingSlug The slug of the setting. Eg. 'logo', 'homeHero',...
     * @return null|Upload The Upload object (for display in template, using $upload->getUrl() for example)
     */
    public function getUpload(string $slug): ?Upload
    {
        $settingObject = $this->adminSettingRepository->getOne($slug);
        return $settingObject->getUpload();
    }

    /** 
     * Return settings from database in an assoc. array. Used to fill the formType from database.
     * @return array Array of simplified settings [ [slug1 => value1], [slug2 => value2]... ]
     */
    public function getFormData()
    {
        $settings = $this->adminSettingRepository->findAll();
        $arr = [];
        foreach($settings as $setting) {
            $arr[$setting->getSlug()] = $setting->getValue();
        }
        return $arr;
    }

    /**
     * Handle the whole update process of admin settings based on submitted form data
     * @param Form $form Previously handled form. We need to get the original form in order to filter files from other data: "$form->getData()->logo" returns a string whereas "$form->get($slug)->getData()" returns an UploadedFile object.
     * @return bool
     */
    public function updateSettings(Form $form): bool
    {
        $settings = $this->adminSettingRepository->findAll();

        foreach($form->getData() as $slug => $value) {
            $upload = null;
            // upload files
            $detailedValue = $form->get($slug)->getData();
            if($detailedValue instanceof UploadedFile) {
                $upload = $this->uploadService->upload($detailedValue, $this->path->UPLOADS_SETTINGS_ABS());
            }

            // set a new AdminSetting object
            foreach($settings as $setting) {
                if($setting->getSlug() === $slug) {
                    // 'file' inputs flash error
                    if($upload) {
                        if($upload instanceof FileException) {
                            $this->flashBag->add('danger', 'Upload failed: ' . $upload->getMessage());
                        } else {
                            /** @var AdminSetting $setting */
                            $previousUpload = $setting->getUpload();
                            // set new Upload
                            $setting->setUpload($upload);
                            $this->entityManager->persist($setting);
                            $this->entityManager->flush();
                            // remove previous Upload from database
                            $this->uploadService->removeUpload($previousUpload);
                            // remove from storage
                            if($previousUpload) {
                                if($this->uploadService->checkIfFileExists($file = $this->path->ROOT().$previousUpload->getUrl())) {
                                    unlink($file);
                                }
                                // $this->flashBag->add('primary', 'Image unset for the '.SlugHelper::nameFromSlug($setting->getSlug()).' and removed from the library.');
                            }
                        }
                    } else {
                        $setting->setValue($value);
                    }
                }
            }
        }
        $this->entityManager->flush();
        return true;
    }

    public function getBoostrapColWidth(): int
    {
        $itemsPerRow = $this->getValue('collectionItemsPerRow');
        return 12 / $itemsPerRow;
    }

    public function getImagesForTwig(): array
    {
        $settingsWithImg = $this->adminSettingRepository->findWithUploadValue();
        $images = [];
        foreach($settingsWithImg as $setting) {
            $upload = $setting->getUpload();
            $images[$setting->getSlug()] = $upload;
            $upload->deleteUrl = $this->urlGenerator->generate('admin_setting_delete', ['id' => $setting->getId()]);
        }
        return $images;
    }

}