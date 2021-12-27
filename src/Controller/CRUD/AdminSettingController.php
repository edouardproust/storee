<?php

namespace App\Controller\CRUD;

use App\App\Helper\SlugHelper;
use App\Form\AdminSettingsType;
use App\App\Service\UploadService;
use App\App\Service\AdminSettingService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AdminSettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminSettingController extends AbstractController
{

    /** @var AdminSettingService */
    private $adminSettingService;

    /** @var UploadService */
    private $uploadService;

    public function __construct(AdminSettingService $adminSettingService, AdminSettingRepository $settingRepository, EntityManagerInterface $entityManager, UploadService $uploadService)
    {
        $this->adminSettingService = $adminSettingService;
        $this->settingRepository = $settingRepository;
        $this->entityManager = $entityManager;
        $this->uploadService = $uploadService;
    }

    /**
     * Edit settings
     * @Route("/admin/settings", name="admin_settings")
     */
    public function edit(Request $request): Response
    { 
        $data = $this->adminSettingService->getFormData();
        $form = $this->createForm(AdminSettingsType::class, $data);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if($form->isValid()) {
                $this->adminSettingService->updateSettings($form);
                $this->addFlash('success', 'Settings have been updated.');
            } else {
                $this->addFlash('danger', 'Some of the settings are not valid. Please correct them below.');
            }
        }
        // get images for twig
        $images = [];
        foreach(['logo', 'homeHero'] as $img) {
            $images[$img] = $this->adminSettingService->getUpload($img);
            if($images[$img]) $images[$img]->deleteUrl = $this->generateUrl('admin_setting_delete', ['slug' => $img]);
        }
        // twig
        return $this->render('crud/adminSetting/admin-list.html.twig', [
            'settingsForm' => $form->createView(),
            'images' => $images
        ]);
    }

    /**
     * Set this setting's value to NULL
     * @Route("/admin/settings/delete/{slug}", name="admin_setting_delete")
     */
    public function delete($slug): Response
    { 
        $setting = $this->settingRepository->findOneBy(['slug' => $slug]);
        $previousUpload = $setting->getUpload();
        // set setting values to NULL
        $setting
            ->setValue(null)
            ->setUpload(null);
        $this->entityManager->persist($setting);
        $this->entityManager->flush();
        // if upload exists, then remove it and flash
        $this->uploadService->removeUpload($previousUpload);
        if($previousUpload) {
            $this->addFlash('primary', 'Image unset for the '.SlugHelper::nameFromSlug($setting->getSlug()).' and removed from the library.');
        }
        // redirect
        return $this->redirectToRoute('admin_settings');
    }

}