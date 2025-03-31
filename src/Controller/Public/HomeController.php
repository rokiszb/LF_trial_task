<?php

namespace App\Controller\Public;

use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository, NewsRepository $newsRepository): Response
    {
        $categories = $categoryRepository->findAll();

        $latestNewsByCategory = [];
        foreach ($categories as $category) {
            $latestNewsByCategory[$category->getId()] = $newsRepository->findLatestByCategory($category, 3);
        }

        return $this->render('public/home/index.html.twig', [
            'categories' => $categories,
            'latestNewsByCategory' => $latestNewsByCategory,
        ]);
    }
}
