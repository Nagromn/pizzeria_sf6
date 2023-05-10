<?php

namespace App\DataFixtures;

// use Faker\Factory;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{

    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create('fr_FR');

        $categoryNames = ['Pizza', 'Boisson', 'Dessert'];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name)
                ->setSlug($this->slugger->slug($category->getName())->lower());

            $manager->persist($category);
        }

        $manager->flush();
    }
}
