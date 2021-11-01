<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Name",
                'attr' => ['placeholder' => 'Choose a name for this product']
            ])
            ->add('price', MoneyType::class, [
                'currency' => "USD",
                'attr' => ['placeholder' => 'Type a price without currency']])
            // ->add('slug', TextType::class, [
            //     'attr' => ['placeholder' => 'Choose a slug (or leave empty for the auto-generated one)']
            // ])
            ->add('shortDescription', TextareaType::class, [
                'attr' => ['placeholder' => "Type a description to present this product, short (max 255 char.) but evocative."]
            ])
            ->add('mainImage', UrlType::class, [
                'attr' => ['placeholder' => 'Type an image URL']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => '-- Choose a category --'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}