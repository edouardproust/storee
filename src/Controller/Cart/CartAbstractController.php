<?php

namespace App\Controller\Cart;

use App\Repository\ProductRepository;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartAbstractController extends AbstractController
{

    /**
     * Check if the product exists. Return its name if true. Exception otherwise.
     * @param int $productId Id of the product to check
     * @param ProductRepository $productRepository 
     * @param null|string $exceptionMessage 
     * @return string|NotFoundException The Product name. Or exception if product does not exist.
     */
    protected function checkIfProductExists(int $productId, ProductRepository $productRepository, ?string $exceptionMessage = null): string 
    {
        $defaultMessage = "Product does not exist.";
        $product = $productRepository->find($productId);
        if(!$product) {
            $this->redirectToRoute("404", [
                'error' => $this->createNotFoundException($exceptionMessage ?? $defaultMessage)
            ]);
        } else {
            return $product->getName();
        }
    }
    
}