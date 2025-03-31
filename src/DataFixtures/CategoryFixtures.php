<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categoryNames = [
            'Technology',
            'Business',
            'Sports',
            'Health',
            'Entertainment',
            'Science',
            'Politics',
            'Travel',
            'Food',
            'Fashion'
        ];

        foreach ($categoryNames as $index => $name) {
            $category = new Category();
            $category->setTitle($name);

            if (method_exists($category, 'setCreatedAt')) {
                $category->setCreatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 year', '-1 month')->format('Y-m-d H:i:s')));
            }

            if (method_exists($category, 'setUpdatedAt')) {
                $category->setUpdatedAt(new \DateTimeImmutable($faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s')));
            }

            $manager->persist($category);

            $this->addReference('category_' . $index, $category);
        }

        $manager->flush();
    }
}
