<?php

namespace App\Form;

use App\App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, [
                'empty_data' => 'John Doe',
                'label' => 'Your name',
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => 'John Doe'],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'help' => "We will answer you back on this address.",
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => 'Optional'],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Phone (optional)',
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => '123-456-789'],
            ])
            ->add('subject', TextType::class, [
                'help' => "Please be explicit but not too long about your demand.",
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => 'I need more informations about a product'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Your message',
                'help' => "Maximum 1000 characters.",
                'row_attr' => ['class' => 'form-floating mb-3',],
                'attr' => [
                    'rows' => 10, 
                    'style' => 'height:100%', 
                    'placeholder' => 'Please send me more informations about the product on this page: https://...'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
