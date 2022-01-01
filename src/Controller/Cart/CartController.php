<?php

namespace App\Controller\Cart;

use App\App\Service\AdminSettingService;
use App\App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends CartAbstractController
{

    private $cartService;
    private $productRepository;
    private $adminSettingService;

    public function __construct(CartService $cartService, ProductRepository $productRepository, AdminSettingService $adminSettingService)
    {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
        $this->adminSettingService = $adminSettingService;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function show(): Response
    {
        return $this->render('checkout/cart.html.twig', [
            'cart' => $this->cartService->getDetailedCart(),
            'total' => $this->cartService->getTotal()
        ]);
    }

    /**
     * On product page: add product to cart (flash message and no redirection)
     * @Route("/product/{id}/add/{qty?1}", name="product_atc")
     * @param int $id Product id
     * @param int $qty Number of units (quantity)
     * @return Response
     */
    public function addToCart($id, $qty = 1): Response
    {
        $product = $this->productRepository->find($id);
        if(!$product) {
            return $this->redirectToRoute('home');
        }
        $this->cartService->addItems($id, $qty);
        $this->addFlash("primary", '"'.$product->getName().'" (x'.$qty.') has been added to your cart. <a href="'.$this->generateUrl('cart').'" class="btn btn-primary btn-sm">View cart</a>');
        // redirect depending on 'direct checkout' admin setting
        if($this->adminSettingService->getValue('directCheckout') == 1) {
            return $this->redirectToRoute("checkout_confirmation");
        }
        return $this->redirectToRoute('product', ['category_slug' => $product->getCategory()->getSlug(), 'slug' => $product->getSlug()]);
    }

    /**
     * On cart page: Add 1 product to a cart row (CartItem)
     * @Route("/cart/{id}/plus", name="cart_plus")
     */
    public function plus($id): Response
    {
        $productName = $this->checkIfProductExists($id, $this->productRepository);
        $this->cartService->addItems($id);
        $this->addFlash("success", '"'.$productName.'" (x1) has been added to your cart.');
        return $this->redirectToRoute('cart');
    }

    /**
     * On cart page: Remove 1 product from a cart row (CartItem)
     * @Route("/cart/{id}/minus", name="cart_minus")
     */
    public function minus($id): Response
    {
        $productName = $this->checkIfProductExists($id, $this->productRepository);
        $this->cartService->removeOneItem($id);
        $this->addFlash("success", '"'.$productName.'" (x1) has been removed from your cart.');
        return $this->redirectToRoute('cart');
    }

    /**
     * Remove a whole cart row (CartItem)
     * 
     * @Route("/cart/{id}/remove", name="cart_remove")
     */
    public function delete($id): Response
    {
        $productName = $this->checkIfProductExists($id, $this->productRepository);
        $this->cartService->removeItemsRow($id);
        $this->addFlash("success", '"'.$productName.'" has been removed from your cart.');
        return $this->redirectToRoute('cart');
    }

}