<?php

namespace App\Entity;

use App\Repository\DeliveryMethodRepository;
use App\Twig\AppExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryMethodRepository::class)
 */
class DeliveryMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $carrier;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity=DeliveryCountry::class, inversedBy="deliveryMethods")
     */
    private $countries;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getNameWithPrice(): ?string
    {
        $price = (new AppExtension)->formatPrice($this->price);
        return $this->name . ' ('. $price . ')';
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCarrier(): ?string
    {
        return $this->carrier;
    }

    public function setCarrier(string $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|DeliveryCountry[]
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    /**
     * @param DeliveryCountry[] $deliveryCountries
     * @return DeliveryMethod 
     */
    public function setCountries(array $deliveryCountries): self
    {
        foreach($deliveryCountries as $country) {
            $this->countries[] = $country;
        }

        return $this;
    }

    public function addCountry(DeliveryCountry $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
        }

        return $this;
    }

    public function removeCountry(DeliveryCountry $country): self
    {
        $this->countries->removeElement($country);

        return $this;
    }

}
