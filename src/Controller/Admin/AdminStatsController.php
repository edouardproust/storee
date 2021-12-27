<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStatsController extends AbstractController
{

    /**
     * @Route("/admin/stats", name="admin_stats")
     * @return Response 
     */
    public function show($projectDir, ProductRepository $productRepository): Response
    {
        $mostViewedProducts = $productRepository->findForCollection(null, null, 'views', 'DESC');
        $bestSellers = $productRepository->findForCollection(null, null, 'purchases', 'DESC');
        
        return $this->render('admin/stats.html.twig', [
            'bestSellers' => $bestSellers,
            'mostViewedProducts' => $mostViewedProducts
        ]);
    }

}
