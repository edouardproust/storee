<?php

namespace App\Entity;

use App\Repository\UploadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UploadRepository::class)
 */
class Upload
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=AdminSetting::class, mappedBy="upload")
     */
    private $adminSettings;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="mainImage")
     */
    private $products;

    public function __construct()
    {
        $this->adminSettings = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|AdminSetting[]
     */
    public function getAdminSettings(): Collection
    {
        return $this->adminSettings;
    }

    public function addAdminSetting(AdminSetting $adminSetting): self
    {
        if (!$this->adminSettings->contains($adminSetting)) {
            $this->adminSettings[] = $adminSetting;
            $adminSetting->setUpload($this);
        }

        return $this;
    }

    public function removeAdminSetting(AdminSetting $adminSetting): self
    {
        if ($this->adminSettings->removeElement($adminSetting)) {
            // set the owning side to null (unless already changed)
            if ($adminSetting->getUpload() === $this) {
                $adminSetting->setUpload(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setMainImage($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getMainImage() === $this) {
                $product->setMainImage(null);
            }
        }

        return $this;
    }
}
