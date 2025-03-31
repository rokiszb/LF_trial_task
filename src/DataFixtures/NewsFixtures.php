<?php

namespace App\DataFixtures;

use App\Entity\News;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\Finder\Finder;

class NewsFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(public string $projectDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $uploadsDir = $this->projectDir . '/public/uploads/news';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }

        $stockImages = $this->getStockImages();
        if (empty($stockImages)) {
            throw new \RuntimeException('No stock images found in /public/media/stock directory');
        }

        for ($i = 0; $i < 50; $i++) {
            $news = new News();
            $news->setTitle($faker->sentence(6, true));
            $news->setShortDescription($faker->paragraph(2));
            $news->setContent($faker->paragraphs(5, true));

            $categoryCount = rand(1, 3);
            for ($j = 0; $j < $categoryCount; $j++) {
                $categoryRef = 'category_' . rand(0, 9);
                $news->addCategory($this->getReference($categoryRef, Category::class));
            }

            $manager->persist($news);

            $randomStockImage = $stockImages[array_rand($stockImages)];
            $this->addStockImage($news, $randomStockImage, $uploadsDir);
            $this->addReference('news_' . $i, $news);
        }

        $manager->flush();
    }

    private function getStockImages(): array
    {
        $stockDir = $this->projectDir . '/public/media/stock';
        if (!is_dir($stockDir)) {
            return [];
        }

        $finder = new Finder();
        $finder->files()->in($stockDir)->name(['*.jpg']);

        $images = [];
        foreach ($finder as $file) {
            $images[] = $file->getRealPath();
        }

        return $images;
    }

    private function addStockImage(News $news, string $stockImagePath, string $uploadsDir): void
    {
        $originalFilename = basename($stockImagePath);
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);

        $newFilename = 'image' . ($news->getId() ?: rand(1, 1000)) . '-' . uniqid() . '.' . $extension;
        $newFilePath = $uploadsDir . '/' . $newFilename;

        if (!copy($stockImagePath, $newFilePath)) {
            throw new \RuntimeException("Failed to copy $stockImagePath to $newFilePath");
        }

        chmod($newFilePath, 0666);
        $news->setPictureFilename($newFilename);
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
