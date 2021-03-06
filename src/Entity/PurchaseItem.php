<?php

namespace App\Entity;

use App\Repository\PurchaseItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PurchaseItemRepository::class)
 */
class PurchaseItem
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Purchase::class, inversedBy="purchaseItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $purchase;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="purchaseItems")
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="json")
     */
    private $productData;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getProductData(): ?Product
    {
        if (is_array($this->productData)) {
            $product = new Product;
            foreach ($this->productData as $var => $value) {
                if (!in_array($var, ['id', 'category', 'createdAt', 'mainImage', 'purchaseItems'])) {
                    $setFn = 'set' . ucFirst($var);
                    $product->$setFn($value);
                }
            }
        }
        return $product ?? $this->productData;
    }

    public function setProductData($productData): self
    {
        if ($productData instanceof Product) {
            $product = $productData->getVars();
            $product['category']->setSlug($productData->getCategory()->getSlug());
        }
        $this->productData = $product ?? $productData;
        return $this;
    }
}
