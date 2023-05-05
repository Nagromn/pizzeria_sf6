<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserPasswordType extends AbstractType
{
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
            $builder
                  ->add('plainPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'first_options' => [
                              'label' => 'Mot de passe',
                              'label_attr' => [
                                    'class' => 'text-custom-violet'
                              ],
                              'attr' => [
                                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                              ]
                        ],
                        'second_options' => [
                              'label' => 'Confirmation du mot de passe',
                              'label_attr' => [
                                    'class' => 'text-custom-violet'
                              ],
                              'attr' => [
                                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                              ]
                        ],
                        'invalid_message' => 'Les mots de passe ne correspondent pas.'
                  ])
                  ->add('newPassword', PasswordType::class, [
                        'label' => 'Nouveau mot de passe',
                        'label' => 'Confirmation du mot de passe',
                        'label_attr' => [
                              'class' => 'text-custom-violet'
                        ],
                        'attr' => [
                              'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                        ],
                        'constraints' => [
                              new Assert\NotBlank(),
                        ]
                  ])
                  ->add('submit', SubmitType::class, [
                        'label' => 'Valider les modifications',
                        'attr' => [
                              'class' => 'w-full inline-block border-2 border-custom-violet rounded-full py-3 px-6 bg-transaparent text-custom-violet font-bold leading-tight shadow-md hover:bg-gradient-to-r from-custom-dark-orange to-custom-light-orange hover:shadow-lg hover:text-white hover:border-white focus:bg-custom-dark-orange focus:shadow-lg focus:outline-none focus:ring-0 active:bg-custom-dark-orange active:shadow-lg transition duration-150 ease-in-out'
                        ]
                  ]);
      }
}
