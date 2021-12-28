<?php

namespace App\Controller\CRUD;

use App\App\Path;
use App\App\Entity\Collection;
use App\App\Service\UploadService;
use App\Repository\UploadRepository;
use App\App\Service\AdminSettingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadController extends AbstractController
{

    /** @var UploadService */
    private $uploadService;
    /** @var Path */
    private $path;
    /** @var UploadRepository */
    private $uploadRepository;

    public function __construct(
        UploadService $uploadService, 
        Path $path, 
        UploadRepository $uploadRepository, 
        EntityManagerInterface $entityManager,
        AdminSettingService $adminSettingService
    ){
        $this->uploadService = $uploadService;
        $this->path = $path;
        $this->uploadRepository = $uploadRepository;
        $this->entityManager = $entityManager;
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * Remove an upload from database and storage
     * @Route("/admin/uploads/delete/{id}", name="upload_delete")
     */
    public function delete($id): Response
    {
        $error = false;
        // remove from database
        try {
            $databaseUpload = $this->uploadRepository->find($id);
                // 'adminSetting' table
                $adminSettings = $databaseUpload->getAdminSettings();
                if(!empty($adminSettings)) {
                    foreach($adminSettings as $setting) {
                        if($setting->getUpload() === $databaseUpload) $setting->setUpload(null);
                        $this->entityManager->persist($setting);
                    }
                }
                // 'product' table
                $products = $databaseUpload->getProducts();
                if(!empty($products)) {
                    foreach($products as $product) {
                        if($product->getMainImage() === $databaseUpload) $product->setMainImage(null);
                        $this->entityManager->persist($product);
                    }
                }
                // 'upload' table
                $this->entityManager->remove($databaseUpload);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $error = true;
            $this->addFlash('danger', 'Failed to delete file "'.$databaseUpload->getName().'". Error details: '.$e->getMessage());
        }
        // remove from local folder
        if(!$error) {
            $allLocalFiles = $this->uploadService->getFilesList($this->path->UPLOADS_ABS());
            foreach($allLocalFiles as $fileName => $filePath) {
                if($databaseUpload->getName() === $fileName) {
                    unlink($filePath);
                    $this->addFlash('success', 'File "'.$fileName.'" has been deleted.');
                }
            }
        }
        // redirect
        return $this->redirectToRoute('admin_uploads');
    }

    /**
     * Show list of uploads on the admin panel
     * @Route("/admin/uploads/{page<\d+>?1}/{orderBy?}_{order?}", name="admin_uploads")
     */
    public function adminList($page, $orderBy, $order, Request $request): Response
    {
        $databaseUploads = $this->uploadRepository->findForCollection(null, $orderBy, $order);
        $storageUploads = $this->uploadService->getExistingFilesList($databaseUploads);
        // pagination
        $collection = new Collection(
            $storageUploads,
            $this->adminSettingService->getValue('entitiesPerAdminListPage'),
            $this->generateUrl($request->get('_route')),
            $page,
            $orderBy ?? 'createdAt',
            $order ?? 'desc'
        );
        if($collection->getRedirect()) return $this->redirectToRoute($request->get('_route'));
        // view
        return $this->render('crud/upload/admin-list.html.twig', [
            'collection' => $collection
        ]);
    }

    /**
     * Purge storage from orphaned files (files that are in storage but not indexed in database)
     * @Route("/admin/uploads/purge", name="admin_uploads_purge")
     */
    public function adminPurge(): Response
    {
        $result = $this->uploadService->cleanStorage();
        if(is_int($result)) {
            $this->addFlash('success', 'The storage has been cleaned! '.$result.' files were removed from storage.');
        } else {
            $this->addFlash('danger', 'The storage cleaning has run due to an error: '.$result->getMessage());
        }
        return $this->redirectToRoute('admin_uploads');
    }

}
