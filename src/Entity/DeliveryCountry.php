<?php

namespace App\Entity;

use App\Repository\DeliveryCountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeliveryCountryRepository::class)
 */
class DeliveryCountry
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
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity=DeliveryMethod::class, mappedBy="countries")
     */
    private $deliveryMethods;


    public function __construct()
    {
        $this->deliveryMethods = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->purchase = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|DeliveryMethod[]
     */
    public function getDeliveryMethods(): Collection
    {
        return $this->deliveryMethods;
    }

    public function addDeliveryMethod(DeliveryMethod $deliveryMethod): self
    {
        if (!$this->deliveryMethods->contains($deliveryMethod)) {
            $this->deliveryMethods[] = $deliveryMethod;
            $deliveryMethod->addCountry($this);
        }

        return $this;
    }

    public function removeDeliveryMethod(DeliveryMethod $deliveryMethod): self
    {
        if ($this->deliveryMethods->removeElement($deliveryMethod)) {
            $deliveryMethod->removeCountry($this);
        }

        return $this;
    }

}
