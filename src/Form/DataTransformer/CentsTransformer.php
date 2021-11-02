<?php namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CentsTransformer implements DataTransformerInterface {

    public function transform($value) {
        if(null === $value) return;
        return $value / 100;
    }

    public function reverseTransform($value)
    {
        if(null === $value) return;
        return $value * 100;
    }

}