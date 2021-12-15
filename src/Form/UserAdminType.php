<?php

namespace App\Form;

use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserAdminType extends AbstractType
{

    /** @var UserRepository */
    private $userRepository;

    public function __construct(TokenStorageInterface $token, UserRepository $userRepository)
    {
        $this->token = $token;
        $this->UserRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstname')
            ->add('lastname')
            ->add('street')
            ->add('postcode')
            ->add('city')
            ->add('country')
            ->add('phone')
            ->add('is_admin', CheckboxType::class, [
                'label' => "Set as Administrator",
                'mapped' => false,
                'required' => false
            ])
            ->add('created_at', DateType::class, [
                'label' => "Registration date"
            ])
        ;
    }

}
