<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Mark;
use App\Entity\User;
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
            $users = $manager->getRepository(User::class)->findAll();
            $products = [];

            // Product
            for ($i = 0; $i < 20; $i++) {
                  $product = new Product();
                  $product->setTitle($faker->words(4, true))
                        ->setCategory($faker->randomElement($category))
                        ->setDescription($faker->realText(1800))
                        ->setPrice((12.99 * 100))
                        ->setIsVending(mt_rand(0, 1))
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->datetime('Europe/Paris')))
                        ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->datetime('Europe/Paris')));

                  // dd($product);
                  $products[] = $product;
                  $manager->persist($product);
            }

            // Mark
            foreach ($products as $product) {
                  for ($i = 0; $i < mt_rand(0, 4); $i++) {
                        $mark = new Mark();
                        $mark->setMark(mt_rand(1, 5))
                              ->setUser($users[mt_rand(0, count($users) - 1)])
                              ->setProduct($product)
                              ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->datetime('Europe/Paris')));

                        $manager->persist($mark);
                  }
            }

            $manager->flush();
      }

      public function getDependencies(): array
      {
            return [CategoryFixtures::class];
      }
}
