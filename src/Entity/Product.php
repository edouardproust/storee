<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
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
     * @Assert\Length(min=3, minMessage="Your product name is too short. It should have {{ limit }} characters or more.")
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="A main image must be defined.")
     * @Assert\Url(message="Main picture must be a valid URL.")
     */
    private $mainImage;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @Assert\NotBlank(message="You have to choose a category", groups={"edit"})
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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

    public function getFloatPrice(): ? float
    {
        return $this->price / 100;
    }

    public function getPriceWithCurrency($currencySign = "$", $before = true): ?string
    {
        $floatPrice = $this->getFloatPrice();
        if($currencySign == "€") {
            $floatPrice = str_replace(".", ",", $this->getFloatPrice());
        }
        if($before) {
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

    public function setSlug(string $slug): self
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

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(?string $mainImage): self
    {
        $this->mainImage = $mainImage;

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
    
}
