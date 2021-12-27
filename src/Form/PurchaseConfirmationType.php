<?php

namespace App\Form;

use App\Entity\Purchase;
use App\Entity\PaymentMethod;
use App\Entity\DeliveryMethod;
use App\Entity\DeliveryCountry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\CountryToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PurchaseConfirmationType extends AbstractType
{

    private $transformer;

    public function __construct(CountryToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => ['placeholder' => 'Firstname'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['placeholder' => 'Lastname'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('street', TextType::class, [
                'attr' => ['placeholder' => 'Street'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('postcode', TextType::class, [
                'label' => 'ZIP',
                'attr' => ['placeholder' => 'Postal code'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => 'City'],
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('country', EntityType::class, [
                'class' => DeliveryCountry::class,
                'choice_label' => 'name',
                'placeholder' => false,
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Email'],
                'row_attr' => ['class' => 'form-floating mb-3'],
                'help' => "To receive delivery updates."
            ])
            ->add('phone', TextType::class, [
                'attr' => ['placeholder' => 'Phone number'],
                'row_attr' => ['class' => 'form-floating mb-3'],
                'help' => "For the delivery person."
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password (Optional)',
                'attr' => ['placeholder' => 'Password (Optional)'],
                'row_attr' => ['class' => 'form-floating mb-3'],
                'help' => "Enter a password to create an account (to track your orders and deliveries easily).",
            ])
            ->add('deliveryMethod', EntityType::class, [
                'class' => DeliveryMethod::class,
                'label' => 'Choose a method',
                'choice_label' => 'name_with_price',
                'placeholder' => false,
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('paymentMethod', EntityType::class, [
                'label' => 'Choose a service',
                'class' => PaymentMethod::class,
                'choice_label' => 'name',
                'placeholder' => false,
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            //->setRequired(false); // 'novalidate' parameter added to <form> tag in the twig template
        ;
        
        $builder->get('country')
            ->addModelTransformer($this->transformer);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class
            //'inherit_data' => true
        ]);
    }
}
