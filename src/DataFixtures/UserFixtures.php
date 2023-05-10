<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;

class UserFixtures extends Fixture
{
      /**
       * @var Generator
       */
      private Generator $faker;

      public function __construct()
      {
            $this->faker = Factory::create('fr_FR');
      }

      public function load(ObjectManager $manager): void
      {
            $admin = new User;
            $admin->setFullName('Administrateur')
                  ->setUsername('Admin')
                  ->setEmail('admin@pizzeria.fr')
                  ->setRoles(['ROLE_USER', 'ROLE_ADMIN'])
                  ->setPlainPassword('password')
                  ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear('Europe/Paris')))
                  ->setIsVerified(true);

            $users[] = $admin;
            $manager->persist($admin);

            for ($i = 0; $i < 10; $i++) {
                  $user = new User();
                  $user->setFullName($this->faker->name())
                        ->setUsername($this->faker->firstName())
                        ->setEmail($this->faker->email())
                        ->setRoles((['ROLE_USER']))
                        ->setPlainPassword('password')
                        ->setAddress($this->faker->streetAddress())
                        ->setZipcode($this->faker->postcode())
                        ->setCity($this->faker->city())
                        ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear('Europe/Paris')))
                        ->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear('Europe/Paris')));

                  $users[] = $user;
                  $manager->persist($user);
            }

            $manager->flush();
      }
}
