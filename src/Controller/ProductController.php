<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{

    private $slugger;
    private $entityManager;
    private $productRepo;

    public function __construct(SluggerInterface $slugger, EntityManagerInterface $entityManager, ProductRepository $productRepo)
    {
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
        $this->productRepo = $productRepo;
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product", priority=-1)
     */
    public function show($slug, EventDispatcherInterface $dispatcher): Response
    {
        $product = $this->productRepo->findOneBy(['slug' => $slug]);
        if(!$product) {
            throw $this->createNotFoundException("The product does not exist.");
        }
        
        // Event hook
        $productViewEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productViewEvent, 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'slugger' => $this->slugger
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
        return $this->renderForm('product/create.html.twig', [
            'productForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/product/edit/{id}", name="product_edit")
     */
    public function edit($id, Request $req, EntityManagerInterface $em): Response
    {
        $product = $this->productRepo->find($id);
        $form = $this->createForm(ProductType::class, $product, [
            "validation_groups" => ["Default", "edit"]
        ]);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute("admin_products");
        }
        return $this->renderForm('product/edit.html.twig', [
            'productForm' => $form,
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="product_delete")
     */
    public function delete($id, EntityManagerInterface $em): Response
    {
        $product = $this->productRepo->find($id);
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute("admin_products");
    }

}