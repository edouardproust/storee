<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('fullname')
            ->add('password', PasswordType::class, [
                'label' => "New Password"
            ])
            ->add('is_admin', CheckboxType::class, [
                'label' => "Set as Administrator"
            ])
            ->add('created_at', DateType::class, [
                'label' => "Registration date"
            ])
        ;
    }

}
