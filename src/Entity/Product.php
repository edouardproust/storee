<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The product name cannot be blank.")
     * @Assert\Length(min=3, max=255, minMessage="Your product name is too short. It should have {{ limit }} characters or more.")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="The price cannot be blank.")
     * @Assert\Type(type="integer", message="Your product price must be a number.")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="A short description must be defined.")
     * @Assert\Length(min=5, minMessage="The short description should have {{ limit }} characters or more.")
     */
    private $shortDescription;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @Assert\NotBlank(message="You have to choose a category", groups={"edit"})
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=PurchaseItem::class, mappedBy="product")
     */
    private $purchaseItems;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Upload::class, inversedBy="products")
     */
    private $mainImage;

    /**
     * Total of units sold
     * @ORM\Column(type="integer", nullable=true)
     */
    private $purchases;

    public function __construct()
    {
        $this->purchaseItems = new ArrayCollection();
    }

    public function getVars()
    {
        return get_object_vars($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getFloatPrice(): ?float
    {
        return $this->price / 100;
    }

    public function getPriceWithCurrency($currencySign = "$", $before = true): ?string
    {
        $floatPrice = $this->getFloatPrice();
        if ($currencySign == "€") {
            $floatPrice = str_replace(".", ",", $this->getFloatPrice());
        }
        if ($before) {
            return $currencySign . $floatPrice;
        }
        return $floatPrice . $currencySign;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|PurchaseItem[]
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems[] = $purchaseItem;
            $purchaseItem->setProduct($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getProduct() === $this) {
                $purchaseItem->setProduct(null);
            }
        }

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(?int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getMainImage(): ?Upload
    {
        return $this->mainImage;
    }

    public function setMainImage(?Upload $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * Get total of units sold 
     * @return null|int Total number of units sold */
    public function getPurchases(): ?int
    {
        return $this->purchases;
    }


    /**
     * Set total of units sold
     * @param null|int $unitsSold Total number of units sold
     * @return Product 
     */
    public function setPurchases(?int $unitsSold): self
    {
        $this->purchases = $unitsSold;

        return $this;
    }

    /**
     * @param mixed $quantity Units sold for this purchase
     * @return void 
     */
    public function addPurchase($quantity): void
    {
        $this->purchases += $quantity;
    }
}
