<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Transporter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data']['user'];

        // dd($options);

        $builder
            ->add('address', EntityType::class, [
                'class' => User::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'choices' => [$user],
                'expanded' => true
            ])
            ->add('transporter', EntityType::class, [
                'class' => Transporter::class,
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider ma commande',
                'attr' => [
                    'class' => 'inline-block border-2 border-custom-violet rounded-full py-3 px-6 bg-transaparent text-custom-violet font-bold leading-tight shadow-md hover:bg-gradient-to-r from-custom-dark-orange to-custom-light-orange hover:shadow-lg hover:text-white hover:border-white focus:bg-custom-dark-orange focus:shadow-lg focus:outline-none focus:ring-0 active:bg-custom-dark-orange active:shadow-lg transition duration-150 ease-in-out'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => User::class,
        ]);
    }
}
