<?php

namespace App\Controller\Cart;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartAbstractController extends AbstractController
{

    protected function checkIfProductExists(int $productId, ProductRepository $productRepository, ?string $exceptionMessage = null) 
    {
        $defaultMessage = "Product does not exist.";
        $productExists = $productRepository->find($productId);
        if(!$productExists) {
            $this->redirectToRoute("404", [
                'error' => $this->createNotFoundException($exceptionMessage ?? $defaultMessage)
            ]);
        };
    }
    
}