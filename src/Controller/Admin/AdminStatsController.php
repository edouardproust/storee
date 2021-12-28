<?php

namespace App\Controller\Admin;

use App\App\Entity\Collection;
use App\App\Service\AdminSettingService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStatsController extends AbstractController
{

    public function __construct(ProductRepository $productRepository, AdminSettingService $adminSettingService)
    {
        $this->productRepository = $productRepository;
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * @Route("/admin/stats/{page<\d+>?1}/{orderBy?}_{order?}", name="admin_stats")
     * @return Response 
     */
    public function show($page, $orderBy, $order, Request $request): Response
    {
        $orderBy = $orderBy ?? 'purchases';
        $order = $order ?? 'desc';

        $collection = new Collection(
            $this->productRepository->findForCollection(null, null, $orderBy, $order),
            $this->adminSettingService->getValue('entitiesPerAdminListPage'),
            $this->generateUrl($request->get('_route')),
            $page,
            $orderBy ?? 'purchases',
            $order ?? 'desc'
        );
        if($collection->getRedirect()) return $this->redirectToRoute($request->get('_route'));
        
        return $this->render('admin/stats.html.twig', [
            'collection' => $collection
        ]);
    }

}
