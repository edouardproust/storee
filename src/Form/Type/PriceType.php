<?php namespace App\Form\Type;

use App\Form\DataTransformer\CentsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @link https://symfony.com/doc/current/form/data_transformers.html
 * @package App\Form\Type
 */
class PriceType extends AbstractType {

    public function getParent()
    {
        return MoneyType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['divide'] === false) return;
        $builder->addModelTransformer(new CentsTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'divide' => true,
            'currency' => "USD",
        ]);
    }

}