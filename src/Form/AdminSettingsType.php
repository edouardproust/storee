<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\CheckboxTransformer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AdminSettingsType extends AbstractType
{
    public function __construct(CheckboxTransformer $checkboxTransformer)
    {
        $this->checkboxTransformer = $checkboxTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteName', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(null, 2, 255)
                ]
            ])
            ->add('storeEmail', Type\EmailType::class, [
                'label' => 'Store email address',
                'help' => "This email will be displayed as contact in the client mail box.",
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(null, 7, 255)
                ]
            ])
            ->add('storeEmailExpeditor', Type\TextType::class, [
                'label' => 'Emails\' expeditor name',
                'help' => "The expeditor name of the emails you send.",
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(null, 2, 255)
                ]
                ])
            ->add('logo', Type\FileType::class, [
                'mapped' => false,
                'label' => 'Store logo',
                'help' => 'Allowed formats: jpg, png, webp, svg. Maximum size: 200Ko.',
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '200k',
                        'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'],
                        'mimeTypesMessage' => 'This image is not valid.',
                    ])
                ],
            ])
            ->add('colorMain', Type\ColorType::class, [
                'help' => 'Color of the main elements of your website (buttons, links,...)'
            ])
            ->add('colorMainHover', Type\ColorType::class, [
                'help' => 'The color of main elements when hovering with the mouse.'
            ])
            ->add('homeHero', Type\FileType::class, [
                'mapped' => false,
                'label' => 'Homepage banner (background image)',
                'help' => 'Allowed formats: jpg, png. Maximum size: 1Mo.',
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/jpg', 'image/png'],
                        'mimeTypesMessage' => 'This image is not valid.',
                    ])
                ],
            ])
            ->add('homeHeroPosition', Type\ChoiceType::class, [
                'label' => 'Position of home\'s hero background image',
                'choices' => [
                    'Top' => 'top',
                    'Center' => 'center',
                    'Bottom' => 'bottom',
                ],
            ])
            ->add('homeHeroLayerOpacity', Type\RangeType::class, [
                'label' => 'Opacity of the home\'s hero layer',
                'help' => 'The more the opacity is high, the more the text will be easy to read.',
            ])
            ->add('homePopularProductsCriteria', Type\ChoiceType::class, [
                'label' => '"Popular Products" section criteria',
                'choices' => [
                    'Best sellers' => 'purchases',
                    'Most visited products' => 'views',
                ],
            ])
            ->add('homeCollectionItemsNumber', Type\IntegerType::class, [
                'label' => 'Number of products per section',
                'help' => 'Min. 2 / Max. 12. For "Popular product" and "Last products" section.',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\LessThanOrEqual(12),
                    new Assert\GreaterThanOrEqual(2)
                ]
            ])
            ->add('collectionItemsPerPage', Type\IntegerType::class, [
                'label' => 'Collections: Products per page',
                'help' => 'Min. 2 / Max. 36. If a collection contains more products than this number, pagination will show up at the bottom of the page.',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\LessThanOrEqual(36),
                    new Assert\GreaterThanOrEqual(2)
                ]
            ])
            ->add('collectionItemsPerRow', Type\IntegerType::class, [
                'label' => 'Collections: Products per row',
                'help' => 'Min. 1 / Max. 4',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\LessThanOrEqual(4),
                    new Assert\GreaterThanOrEqual(1)
                ]
            ])
            ->add('directCheckout', Type\CheckboxType::class, [
                'help' => 'Should clicking on "Add to cart" redirect to checkout page directly?'
            ])
            // Hidden fields 
            // (could be made visible in further updates, to make the store even more customizable by the user)
            ->add('entitiesPerAdminListPage', Type\HiddenType::class, [
                'mapped' => true
            ])
            ->add('collectionFilterDefault', Type\HiddenType::class, [
                'mapped' => true
            ])
            ->add('collectionFilterOptions', Type\HiddenType::class, [
                'mapped' => false
            ])
            ->setRequired(false);

        $builder->get('directCheckout') ->addModelTransformer($this->checkboxTransformer);

    }

}