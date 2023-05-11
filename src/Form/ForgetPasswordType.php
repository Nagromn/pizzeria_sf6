<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ForgetPasswordType extends AbstractType
{
      public function buildForm(FormBuilderInterface $builder, array $options)
      {
            $builder
                  ->add('email', EmailType::class, [
                        'label' => 'Email',
                        'label_attr' => [
                              'class' => 'text-custom-violet'
                        ],
                        'attr' => [
                              'placeholder' => 'exemple@pizzeria.fr',
                              'class' => 'block w-full text-base font-normal mb-6 text-gray-700 bg-white bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none'
                        ],
                        'constraints' => [
                              new Assert\NotBlank(),
                        ]
                  ])
                  ->add('submit', SubmitType::class, [
                        'label' => 'Envoyer',
                        'row_attr' => [
                              'class' => 'w-48 rounded-full px-4 py-3 border-2 border-custom-violet bg-custom-violet text-white text-center  font-bold leading-tight uppercase shadow-md hover:bg-gradient-to-r from-custom-dark-orange to-custom-light-orange hover:shadow-lg hover:text-white hover:border-white focus:bg-custom-dark-orange focus:shadow-lg focus:outline-none focus:ring-0 active:bg-custom-dark-orange active:shadow-lg transition duration-150 ease-in-out'
                        ]
                  ]);
      }
}
