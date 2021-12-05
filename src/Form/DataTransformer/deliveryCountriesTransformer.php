<?php

namespace App\Form\DataTransformer;

use App\Repository\DeliveryCountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class deliveryCountriesTransformer implements DataTransformerInterface
{

    private $entityManager;

    public function __construct(DeliveryCountryRepository $deliveryCountryRepository)
    {
        $this->deliveryCountryRepository = $deliveryCountryRepository;
    }
    
    /**
     * Transforms the numeric array (1,2,3,4) to a collection of DeliveryCountry (DeliveryCountry[])
     * 
     * @param Array|null $categories
     * @return array
     */
    public function transform($countryNumber): array
    {
        $result = [];
        if (null === $countryNumber) {
            return $result;
        }
        return $this->deliveryCountryRepository->find($countryNumber);
    }

    /**
     * No reverse transform operation.
     * 
     * @param type $value
     * @return array
     */
    public function reverseTransform($value): array
    {
        return [];
    }

}