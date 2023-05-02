<?php

namespace App\Form;

use App\Entity\Mark;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MarkType extends AbstractType
{
      public function buildForm(FormBuilderInterface $builder, array $options): void
      {
            $builder
                  ->add('mark', ChoiceType::class, [
                        'label' => false,
                        'choices' => [
                              '★★★★★' => 5,
                              '★★★★☆' => 4,
                              '★★★☆☆' => 3,
                              '★★☆☆☆' => 2,
                              '★☆☆☆☆' => 1,
                        ],
                        'choice_attr' => [
                              'class' => 'far fa-star text-xl text-custom-violet',
                        ],
                        'attr' => [
                              'class' => 'far fa-star text-xl text-custom-violet border-1 border-custom-violet rounded-full w-fit-content px-2 w-36',
                        ],
                  ])
                  ->add('submit', SubmitType::class, [
                        'label' => "Noter",
                        'attr' => [
                              'class' => 'inline-block border-2 border-custom-violet rounded-full py-1 px-10 bg-transaparent text-custom-violet font-bold leading-tight shadow-md hover:bg-gradient-to-r from-custom-dark-orange to-custom-light-orange hover:shadow-lg hover:text-white hover:border-white focus:bg-custom-dark-orange focus:shadow-lg focus:outline-none focus:ring-0 active:bg-custom-dark-orange active:shadow-lg transition duration-150 ease-in-out',
                        ],
                  ]);
      }

      public function configureOptions(OptionsResolver $resolver)
      {
            $resolver->setDefaults([
                  'data_class' => Mark::class,
            ]);
      }
}
