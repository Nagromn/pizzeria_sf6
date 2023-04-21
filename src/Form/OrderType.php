<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Transporter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data']['user'];
        // $transporter = $options['transporter'];
        // dd($user);
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => User::class,
            'transporter' => Transporter::class
        ]);
    }
}
