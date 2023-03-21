<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordType extends AbstractType
{
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
            $builder
                  ->add('plainPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'first_options' => [
                              'label' => 'Mot de passe',
                        ],
                        'second_options' => [
                              'label' => 'Confirmation du mot de passe',
                        ],
                        'invalid_message' => 'Les mots de passe ne correspondent pas.'
                  ])
                  ->add('newPassword', PasswordType::class, [
                        'label' => 'Nouveau mot de passe',
                        'constraints' => [
                              new Assert\NotBlank(),
                        ]
                  ]);
      }
}
