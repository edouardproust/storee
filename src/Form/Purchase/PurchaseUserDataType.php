<?php

namespace App\Form\Purchase;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseUserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Firstname'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Lastname'
                ]
            ])
            ->add('street', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Street'
                ]
            ])
            ->add('postcode', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Postal code'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'City'
                ]
            ])
            ->add('country', ChoiceType::class, [
                'label' => false,
                'placeholder' => 'Country'
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'help' => "To receive delivery notifications. We keep this information private.",
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => false,
                'help' => "To be joined by the delivery person. We keep this information private.",
                'attr' => [
                    'placeholder' => 'Phone number'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'help' => "Enter a password to create a free account. An account allows you to track your orders and deliveries easily.",
                'attr' => [
                    'placeholder' => 'Password (Optional)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
