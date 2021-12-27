<?php

namespace App\Form;

use App\App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
                'constraints' => [
                    new Assert\Length(['min' => 3, 'max' => 255])
                ]
            ])
            ->add('email', EmailType::class, [
                'help' => "We will answer you back on this address.",
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => 'Optional', 'novalidate' => "novalidate"],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['message' => "The email '{{ value }}' is not a valid email."]),
                    new Assert\Length(['max' => 255])
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone (optional)',
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => '123-456-789'],
                'constraints' => [
                    new Assert\Length(['max' => 255])
                ]
            ])
            ->add('subject', TextType::class, [
                'help' => "Please be explicit but not too long about your demand.",
                'row_attr' => ['class' => 'form-floating mb-3'],
                'attr' => ['placeholder' => 'I need more informations about a product'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255])
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Your message',
                'help' => "Maximum 2000 characters.",
                'row_attr' => ['class' => 'form-floating mb-3',],
                'attr' => [
                    'rows' => 10, 
                    'style' => 'height:100%', 
                    'placeholder' => 'Please send me more informations about the product on this page: https://...'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 2000])
                ]
            ])
            // ->setRequired(false); // 'novalidate' parameter added to <form> tag in the twig template
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
