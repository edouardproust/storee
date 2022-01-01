<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\DeliveryCountry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\CountryToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{

    private $countryTransformer;

    public function __construct(CountryToStringTransformer $countryTransformer)
    {
        $this->countryTransformer = $countryTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->setRequired(false); // 'novalidate' parameter added to <form> tag in the twig template
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Email'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('password', PasswordType::class, [
                // 'mapped' => false, // process this field manually inside the controller
                'attr' => ['placeholder' => 'Password'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('firstname', TextType::class, [
                'attr' => ['placeholder' => 'Firstname'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['placeholder' => 'Lastname'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('street', TextType::class, [
                'attr' => ['placeholder' => 'Street'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('postcode', TextType::class, [
                'label' => "Postal code",
                'attr' => ['placeholder' => 'Postal code'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => 'City'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
            ->add('country', EntityType::class, [
                'class' => DeliveryCountry::class,
                'choice_label' => 'name',
                'placeholder' => false,
                'row_attr' => ['class' => 'form-floating mb-3']
            ])
            ->add('phone', TextType::class, [
                'attr' => ['placeholder' => 'Phone'],
                'row_attr' => ['class' => 'form-floating mb-3'],
            ])
        ;
        $builder->get('country')
            ->addModelTransformer($this->countryTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
