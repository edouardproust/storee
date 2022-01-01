<?php

namespace App\App\Service;

use App\App\Entity\CartItem;
use App\Repository\ProductRepository;
use App\Controller\Cart\CartAbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService extends CartAbstractController
{

    const CART_BAG = 'cart';
    private $session;
    private $productRepository;
    private $sessionCart;
    private $detailedCart;
    private $total;
    private $productsNumber;

    /**
     * @param SessionInterface $session
     * @return void 
     */
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    /**
     * @param mixed $value 
     * @return array 
     */
    private function setSessionCart($value): void
    {
        $this->session->set(self::CART_BAG, $value);
    }

    /** @return array  */
    private function getSessionCart(): ?array
    {
        $this->sessionCart = $this->session->get(self::CART_BAG, []);
        return $this->sessionCart;
    }

    public function setDetailedCart(): void
    {
        $sessionCart = $this->getSessionCart();
        $detailedCart = [];
        foreach($sessionCart as $productId => $qty) {
            $this->checkIfProductExists($productId, $this->productRepository);
            $product = $this->productRepository->find($productId);
            $item = new CartItem($product, $qty);
            $detailedCart[] = $item;
        }
        $this->detailedCart = $detailedCart;
    }

    public function getDetailedCart(): ?array
    {
        if(!$this->detailedCart) $this->setDetailedCart();
        return $this->detailedCart;
    }

    public function setTotal(): void
    {
        $sessionCart = $this->getSessionCart();
        $cartTotal = 0;
        foreach($sessionCart as $productId => $qty) {
            $this->checkIfProductExists($productId, $this->productRepository);
            $product = $this->productRepository->find($productId);
            $cartTotal += $product->getPrice() * $qty;
        }
        $this->total = $cartTotal;
    }

    public function getTotal(): ?float
    {
        if(!$this->total) $this->setTotal();
        return $this->total;
    }

    public function setProductsNumber(): void
    {
        $sessionCart = $this->getSessionCart();
        $productsNumber = 0;
        foreach($sessionCart as $productId => $qty) {
            $productsNumber += $qty;
        }
        $this->productsNumber = $productsNumber;
    }

    public function getProductsNumber(): ?int
    {
        if(!$this->productsNumber) $this->setProductsNumber();
        return $this->productsNumber;
    }

    /**
     * @param int $productId 
     * @return void 
     */
    public function addItems(int $productId, int $unitsToAdd = 1): void
    {
        $cart = $this->getSessionCart();
        if(!isset($cart[$productId])) {
            $cart[$productId] = 0;
        }
        $cart[$productId] += $unitsToAdd;
        $this->setSessionCart($cart);
    }

    /**
     * @param int $productId 
     * @return void 
     */
    public function removeOneItem(int $productId): void
    {
        $cart = $this->getSessionCart();
        if(!isset($cart[$productId])) { // no product to delete
            // flash message
            return;
        }
        $cart[$productId]--;
        if($cart[$productId] <= 0) {
            unset($cart[$productId]);
        }
        $this->setSessionCart($cart);
    }

    public function removeItemsRow(int $productId, ?array $cart = null): void
    {
        if(!$cart) {
            $cart = $this->getSessionCart();
        }
        if(array_key_exists($productId, $cart)) {
            unset($cart[$productId]);
            $this->setSessionCart($cart);
        }
    }

}