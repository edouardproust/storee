<?php

namespace App\App\Entity;

use App\Entity\Product;

class CartItem 
{

    /**
     * @var Product
     */
    private $product;
    private $qty;

    public function __construct(?Product $product, int $qty)
    {
        $this->product = $product;
        $this->qty = $qty;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function getTotal(): ?float
    {
        return $this->product->getPrice() * $this->getQty();
    }

}