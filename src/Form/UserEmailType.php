<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserEmailType extends AbstractType
{
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
            $builder
                  ->add('email', EmailType::class, [
                        'label' => 'Nouveau email',
                        'constraints' => [
                              new Assert\NotBlank(),
                        ]
                  ])
                  ->add('plainPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'first_options' => [
                              'label' => 'Mot de passe',
                        ],
                        'second_options' => [
                              'label' => 'Confirmation du mot de passe',
                        ],
                        'invalid_message' => 'Les mots de passe ne correspondent pas.'
                  ]);
      }
}
