<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Category;
use App\Entity\Product\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
      public function load(ObjectManager $manager): void
      {
            $faker = Factory::create('fr_FR');
            $category = $manager->getRepository(Category::class)->findAll();

            for ($i = 0; $i < 20; $i++) {
                  $product = new Product();
                  $product->setTitle($faker->words(4, true))
                        ->setCategory($faker->randomElement($category))
                        ->setDescription($faker->realText(1800))
                        ->setPrice(12.99)
                        ->setIsVending(mt_rand(0, 1))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->datetime('Europe/Paris')))
                        ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->datetime('Europe/Paris')));

                  // dd($product);
                  $manager->persist($product);
            }

            $manager->flush();
      }

      public function getDependencies(): array
      {
            return [CategoryFixtures::class];
      }
}
