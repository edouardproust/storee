<?php

namespace App\Controller\Cart;

use App\App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartController extends CartAbstractController
{

    private $cartService;
    private $productRepository;

    public function __construct(CartService $cartService, ProductRepository $productRepository)
    {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function show(): Response
    {
        return $this->render('cart/show.html.twig', [
            'cart' => $this->cartService->getDetailedCart(),
            'total' => $this->cartService->getTotal()
        ]);
    }

    /**
     * Add 1 product to a cart row (CartItem)
     * 
     * @Route("/cart/{id}/plus", name="cart_plus")
     */
    public function plus($id): Response
    {
        $this->checkIfProductExists($id, $this->productRepository);
        $this->cartService->addOneItem($id);
        $this->addFlash("success", "The product has been added to your cart.");
        return $this->redirectToRoute('cart');
    }

    /**
     * Remove 1 product from a cart row (CartItem)
     * 
     * @Route("/cart/{id}/minus", name="cart_minus")
     */
    public function minus($id): Response
    {
        $this->checkIfProductExists($id, $this->productRepository);
        $this->cartService->removeOneItem($id);
        $this->addFlash("success", "The product has been removed from your cart.");
        return $this->redirectToRoute('cart');
    }

    /**
     * Remove a whole cart row (CartItem)
     * 
     * @Route("/cart/{id}/remove", name="cart_remove")
     */
    public function delete($id): Response
    {
        $this->checkIfProductExists($id, $this->productRepository);
        $this->cartService->removeItemsRow($id);
        $this->addFlash("success", "The product has been removed from your cart.");
        return $this->redirectToRoute('cart');
    }

}