<?php

namespace App\Form;

use App\Entity\Product\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
      public function buildForm(FormBuilderInterface $builder, array $options): void
      {
            $builder
                  ->add('title', TextType::class, [
                        'label' => 'Nom du produit',
                  ])
                  ->add('description', TextareaType::class, [
                        'label' => 'Description du produit',
                  ])
                  ->add('price', TextType::class, [
                        'label' => 'Prix',
                  ]);
      }

      public function configureOptions(OptionsResolver $resolver): void
      {
            $resolver->setDefaults([
                  'data_class' => Product::class,
            ]);
      }
}
