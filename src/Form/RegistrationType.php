<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormError;

class RegistrationType extends AbstractType
{
    /**
     * Formulaire d'inscription d'un utilisateur
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Input 'Nom/Prénom'
            ->add('fullName', TextType::class, [
                'label' => 'Nom / Prénom',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'placeholder' => 'Dupont Jean',
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])

            // Input 'Nom d'utilisateur'
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'placeholder' => 'Pseudo',
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ],
                'row_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'constraints' => [
                    new Assert\NotBlank($options = [
                        'message' => 'Veuillez renseigner votre nom d\'utilisateur.',
                    ]),
                ],

            ])

            // Input 'Email'
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'placeholder' => 'exemple@pizzeria.fr',
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])

            // Input 'Mot de passe' et 'Confirmation de mot de passe' (2 inputs)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'first_options' => [
                    'label' => 'Mot de passe',
                    'label_attr' => [
                        'class' => 'text-custom-violet'
                    ],
                    'attr' => [
                        'placeholder' => '*******',
                        'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'label_attr' => [
                        'class' => 'text-custom-violet'
                    ],
                    'attr' => [
                        'placeholder' => '*******',
                        'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                    ],

                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.'
            ])

            // Bouton 'S'inscrire'
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'row_attr' => [
                    'class' => 'w-full rounded-full px-4 py-3 border-2 border-custom-violet bg-custom-violet text-white text-center  font-bold leading-tight uppercase shadow-md hover:bg-gradient-to-r from-custom-dark-orange to-custom-light-orange hover:shadow-lg hover:text-white hover:border-white focus:bg-custom-dark-orange focus:shadow-lg focus:outline-none focus:ring-0 active:bg-custom-dark-orange active:shadow-lg transition duration-150 ease-in-out'
                ],
                'validate' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
