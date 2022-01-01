<?php

namespace App\Form\DataTransformer;

use App\Entity\DeliveryCountry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @link https://symfony.com/doc/current/form/data_transformers.html
 * @package App\Form\DataTransformer
 */
class CheckboxTransformer implements DataTransformerInterface
{

    /**
     * From data to view 
     * @param $value
     * @return void
     */
    public function transform($value) 
    {
        if($value == 1) {
            return true;
        }
        return false;
    }

    /**
     * From view to data submited
     * @param string $value The file path
     * @return File A File object
     */
    public function reverseTransform($value)
    {
        if(!$value) return 0;
        return 1;
    }

}