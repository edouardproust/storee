<?php

namespace App\Controller\CRUD;

use App\App\Path;
use App\App\Service\AdminSettingService;
use App\Entity\Product;
use App\Form\ProductType;
use App\Event\ProductViewEvent;
use App\App\Entity\Collection;
use App\App\Service\UploadService;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    /** @var SluggerInterface */
    private $slugger;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var ProductRepository */
    private $productRepository;
    /** @var UploadService */
    private $uploadService;
    /** @var Path */
    private $path;
    /** @var AdminSettingService */
    private $adminSettingService;

    public function __construct(
        SluggerInterface $slugger, 
        EntityManagerInterface $entityManager,  
        ProductRepository $productRepository,
        UploadService $uploadService, 
        Path $path,
        AdminSettingService $adminSettingService
    ){
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->uploadService = $uploadService;
        $this->path = $path;
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product", priority=-1)
     */
    public function show($slug, EventDispatcherInterface $dispatcher): Response
    {
        $product = $this->productRepository->findOneBy(['slug' => $slug]);
        if(!$product) {
            throw $this->createNotFoundException("The product does not exist.");
        }
        
        // Event hook
        $productViewEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productViewEvent, 'product.view');

        return $this->render('crud/product/show.html.twig', [
            'product' => $product,
            'slugger' => $this->slugger
        ]);
    }

    /**
     * @Route("/all-products/{page<\d+>?1}/{orderBy?}", name="catalog")
     */
    public function list($page, $orderBy, Request $request): Response  
    {
        $collection = new Collection(
            $this->productRepository->findForCollection(null, null, $orderBy),
            $this->adminSettingService->getValue('collectionItemsPerPage'), 
            $this->generateUrl($request->get('_route')),
            $page,
            $orderBy
        );
        if($collection->getRedirect()) return $this->redirectToRoute($request->get('_route'));

        return $this->render('crud/product/list.html.twig', [
            'collection' => $collection,
            'itemColWidth' => $this->adminSettingService->getBoostrapColWidth()
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $req, CategoryRepository $catRepo): Response
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            if($product->getCategory() === null) {
                $product->setCategory($catRepo->findOneBy(['slug' => 'undefined']));
            }
            $product->setSlug(strtolower($this->slugger->slug($product->getName())));
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            return $this->redirectToRoute("admin_products");
        }
        return $this->renderForm('crud/product/create.html.twig', [
            'productForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/product/edit/{id}", name="product_edit")
     */
    public function edit($id, Request $req): Response
    {
        $product = $this->productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product, [
            "validation_groups" => ["Default", "edit"]
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted()) {
            $previousMainImage = $product->getMainImage();
            // upload mainImage
            $upload = $this->uploadService->upload($form->get('mainImage')->getData(), $this->path->UPLOADS_PRODUCTS_IMG_ABS());
            if($upload) $product->setMainImage($upload);
            // update product in database
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            // try to purge database from previous upload
            if($upload) $this->uploadService->removeUpload($previousMainImage);
            // redirect with flash
            $this->addFlash('success', 'The product "'.$product->getName().'" has been updated.');
            return $this->redirectToRoute("admin_products");
        }
        return $this->renderForm('crud/product/edit.html.twig', [
            'productForm' => $form,
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="product_delete")
     */
    public function delete($id): Response
    {
        $product = $this->productRepository->find($id);
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return $this->redirectToRoute("admin_products");
    }
    
    /**
     * Show list of products
     * 
     * @Route("/admin/products/{page<\d+>?1}/{orderBy?}_{order?}", name="admin_products")
     */
    public function adminList($page, $orderBy, $order, Request $request, ProductRepository $productRepository): Response
    {
        $orderBy = $orderBy ?? 'id';
        $order = $order ?? 'desc';
    
        $collection = new Collection(
            $productRepository->findForCollection(null, null, $orderBy, $order),
            $this->adminSettingService->getValue('entitiesPerAdminListPage'),
            $this->generateUrl($request->get('_route')),
            $page,
            $orderBy,
            $order
        );
        if($collection->getRedirect()) return $this->redirectToRoute($request->get('_route'));
        // render
        return $this->render('crud/product/admin-list.html.twig', [
            'collection' => $collection
        ]);
    }

}