<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom / Prénom',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ]
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'label_attr' => [
                    'class' => 'text-custom-violet'
                ],
                'attr' => [
                    'class' => 'form-control block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider les modifications',
                'attr' => [
                    'class' => 'w-full inline-block border-2 border-custom-violet rounded-full py-3 px-6 bg-transaparent text-custom-violet font-bold leading-tight shadow-md hover:bg-gradient-to-r from-custom-dark-orange to-custom-light-orange hover:shadow-lg hover:text-white hover:border-white focus:bg-custom-dark-orange focus:shadow-lg focus:outline-none focus:ring-0 active:bg-custom-dark-orange active:shadow-lg transition duration-150 ease-in-out'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
