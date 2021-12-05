<?php

namespace App\Form\Purchase;

use App\Entity\DeliveryMethod;
use App\Form\DataTransformer\deliveryCountriesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseDeliveryType extends AbstractType
{
    
    public function __construct(deliveryCountriesTransformer $dataTransformer)
    {
        $this->dataTransformer = $dataTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('method', EntityType::class, [
                'class' => DeliveryMethod::class,
                'choice_label' => 'name_with_price',
                'required' => false,
                'placeholder' => false,
                'row_attr' => ['class' => 'form-floating mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true
        ]);
    }
}
