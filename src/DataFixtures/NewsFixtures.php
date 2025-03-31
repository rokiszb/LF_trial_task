<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\News;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class NewsFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private Filesystem $filesystem,
//        private FilterService $filterService,
//        private CacheManager $cacheManager,

    ){}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Define the directory where stock images are located
        $stockImagesDir = $this->parameterBag->get('kernel.project_dir') . '/public/media/stock';

        $uploadDir = $this->parameterBag->get('kernel.project_dir') . '/public/uploads/news';
        if (!$this->filesystem->exists($uploadDir)) {
            $this->filesystem->mkdir($uploadDir, 0755);
        }

        $finder = new Finder();
        $finder->files()->in($stockImagesDir)->name(['*.jpg', '*.jpeg', '*.png']);

        // Convert to array for random access
        $stockImages = iterator_to_array($finder);
        $stockImagesCount = count($stockImages);


        // Create news articles
        for ($i = 0; $i < 50; $i++) {
            $news = new News();
            $news->setTitle($faker->sentence(rand(2, 5)));
            $content = '';

            for ($p = 0; $p < rand(3, 6); $p++) {
                $sentences = $faker->sentences(rand(3, 8));
                $content .= "<p>" . implode(' ', $sentences) . "</p>";
            }

            $content .= "<h4>" . $faker->sentence() . "</h4>";

            for ($p = 0; $p < rand(2, 4); $p++) {
                $sentences = $faker->sentences(rand(3, 6));
                $content .= "<p>" . implode(' ', $sentences) . "</p>";
            }

            $content .= "<blockquote>" . implode(' ', $faker->sentences(rand(1, 3))) . "</blockquote>";

            for ($p = 0; $p < rand(1, 3); $p++) {
                $sentences = $faker->sentences(rand(2, 5));
                $content .= "<p>" . implode(' ', $sentences) . "</p>";
            }

            $news->setContent($content);

            $shortSentences = $faker->sentences(rand(2, 3));
            $news->setShortDescription(implode(' ', $shortSentences));

            $news->setInsertDate(new \DateTimeImmutable($faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')));

            // Add 1-3 random categories to each news article
            $categoryCount = rand(1, 3);
            for ($j = 0; $j < $categoryCount; $j++) {
                $randomCategoryId = rand(0, 9); // Based on 10 categories created in CategoryFixtures
                $category = $this->getReference('category_' . $randomCategoryId, Category::class);
                $news->addCategory($category);
            }

            $randomIndex = rand(0, $stockImagesCount - 1);
            $stockImage = array_values($stockImages)[$randomIndex];

            $originalExtension = $stockImage->getExtension();
            $pictureFileName = 'news_' . $i . '_' . uniqid() . '.' . $originalExtension;
            $destinationPath = $uploadDir . '/' . $pictureFileName;
            copy($stockImage->getRealPath(), $destinationPath);

            // Set the pictureFileName for VichUploader
            $news->setPictureFileName($pictureFileName);
//            $webPath = 'uploads/news/' . $pictureFileName;
//            try {
//                // Generate thumbnails for configured filter sets
//                $this->filterService->getUrlOfFilteredImage($webPath, 'thumbnail');
//                $this->filterService->getUrlOfFilteredImage($webPath, 'news_medium');
//            } catch (\Exception $e) {
//                // Handle or log error
//            }


            $manager->persist($news);

            // Store reference for comments
            $this->addReference('news_' . $i, $news);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
