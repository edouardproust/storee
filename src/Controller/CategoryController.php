<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\App\Component\Collection;
use App\Repository\AdminSettingRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $em, 
        CategoryRepository $categoryRepository, 
        ProductRepository $productRepository,
        SluggerInterface $slugger,
        AdminSettingRepository $settings
    ){
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->slugger = $slugger;
        $this->settings = $settings;
    }

    /**
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));
            $this->em->persist($category);
            $this->em->flush();
            return $this->redirectToRoute("admin_categories");
        }
        return $this->render('category/create.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/{slug}/{page<\d+>?1}", name="category")
     */
    public function show($slug, $page): Response
    {
        $category = $this->categoryRepository->findOneBy(['slug' => $slug]);
        if(!$category) {
            throw $this->createNotFoundException("The category does not exist.");
        } else {
            $collection = (new Collection($this->productRepository, ['category' => $category]))
                ->build(
                    $this->settings->get('productPerCollectionPage'), 
                    $this->generateUrl('category', ['slug' => $slug]),
                    $page
                );
            if(@$collection['redirectToPage']) {
                $page = $collection['redirectToPage'];
                return $this->redirectToRoute('category', ['slug' => $slug, 'page' => $page]);
            }
        }
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'collection' => $collection
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function edit($id, Request $request): Response
    {
        $category = $this->categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute(("admin_categories"));
        }
        return $this->render('category/edit.html.twig', [
            'categoryForm' => $form->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/catagory/delete/{id}", name="category_delete")
     */
    public function delete($id): Response
    {
        $category = $this->categoryRepository->find($id);
        // Update category of products to "Undefined"
        $products = $category->getProducts();
        $undefinedCategory = $this->categoryRepository->findOneBy(['slug' => 'undefined']);
        foreach($products as $product) {
            $product->setCategory($undefinedCategory);
            $this->em->persist($product);
        }
        // remove category
        $this->em->remove($category);
        $this->em->flush();
        // redirect
        return $this->redirectToRoute("admin_categories");
    }

}
