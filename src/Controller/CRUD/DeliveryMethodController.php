<?php

namespace App\Controller\CRUD;

use App\Repository\DeliveryMethodRepository;
use App\Repository\DeliveryCountryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeliveryMethodController extends AbstractController
{

    /**
     * Show delivery options
     * 
     * @Route("/admin/delivery-options", name="admin_delivery")
     */
    public function adminList(DeliveryMethodRepository $methodRepository, DeliveryCountryRepository $countriesRepository): Response
    {
        $methods = $methodRepository->findAll();
        $countries = $countriesRepository->findBy([], [
            'name' => 'ASC'
        ]);
        return $this->render('crud/deliveryMethod/admin-list.html.twig', [
            'deliveryMethods' => $methods,
            'countries' => $countries
        ]);
    }

}