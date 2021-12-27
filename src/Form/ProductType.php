<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\Type\PriceType;


class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => "Name",
                'attr' => ['placeholder' => 'Choose a name for this product'],
            ])  
            ->add('price', PriceType::class, [
                'attr' => ['placeholder' => 'Type a price without currency']
                ])
            ->add('shortDescription', Type\TextareaType::class, [
                'attr' => ['placeholder' => "Type a description to present this product, short (max 255 char.) but evocative."]
            ])
            ->add('mainImage', Type\FileType::class, [
                'label' => 'Main image',
                'mapped' => false,
                'required' => false,
                'help' => 'Format: jpg, png | Max. size: 1Mo'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => '-- Choose a category --'
            ])
            ->setRequired(false)  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}
