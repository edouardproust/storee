<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{

    /**
     * @Route("/category/{slug}", name="category")
     */
    public function show(CategoryRepository $catRepo, $slug): Response
    {
        $cat = $catRepo->findOneBy(['slug' => $slug]);
        if(!$cat) {
            throw $this->createNotFoundException("The category does not exist.");
        }
        return $this->render('category/show.html.twig', [
            'category' => $cat
        ]);
    }

    /**
     * @Route("/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute("admin_categories");
        }
        return $this->render('category/create.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/edit/{id}", name="category_edit")
     */
    public function edit($id, CategoryRepository $catRepo, Request $request, EntityManagerInterface $em): Response
    {
        $category = $catRepo->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $em->flush();
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
    public function delete($id, CategoryRepository $catRepo, EntityManagerInterface $em): Response
    {
        $category = $catRepo->find($id);
        // Update category of products to "Undefined"
        $products = $category->getProducts();
        $undefinedCategory = $catRepo->findOneBy(['slug' => 'undefined']);
        foreach($products as $product) {
            $product->setCategory($undefinedCategory);
            $em->persist($product);
        }
        // remove category
        $em->remove($category);
        $em->flush();
        // redirect
        return $this->redirectToRoute("admin_categories");
    }

}
