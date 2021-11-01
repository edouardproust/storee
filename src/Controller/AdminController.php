<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * @Route("/admin/products", name="admin_products")
     */
    public function products(ProductRepository $productRepo): Response
    {
        $products = $productRepo->findBy([], [
            'createdAt' => "DESC"
        ]);
        return $this->render('admin/products.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/admin/categories", name="admin_categories")
     */
    public function categories(CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findBy([], [
            'name' => 'ASC'
        ]);
        return $this->render('admin/categories.html.twig', [
            'categories' => $categories
        ]);
    }

}
