<?php

namespace App\Form\DataTransformer;

use App\Entity\DeliveryCountry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @link https://symfony.com/doc/current/form/data_transformers.html
 * @package App\Form\DataTransformer
 */
class CountryToStringTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * From data to view 
     * @param string $value The country name
     * @return DeliveryCountry|null
     */
    public function transform($value) 
    {
        if(!is_string($value)) return;
        $deliveryCountryRepository = $this->entityManager->getRepository(DeliveryCountry::class);
        $country = $deliveryCountryRepository->findOneBy(['name' => $value]);
        // View needs an object for choiceType
        return $country;
    }

    /**
     * From view to data submited
     * @param DeliveryCountry $value
     * @return string
     */
    public function reverseTransform($value)
    {
        if(!($value instanceof DeliveryCountry)) return;
        // Purchase::country is saved as a string 
        // (to avoid problems later that could occur if an DeliveryCountry object was saved instead)
        return $value->getName(); 
    }

}