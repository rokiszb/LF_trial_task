<?php

namespace App\Controller\Public;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category')]
final class CategoryViewController extends AbstractController
{
    #[Route(name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('public/category/show.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category, Request $request, NewsRepository $newsRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $news = $newsRepository->findByCategoryPaginated($category, $page, $limit);

        return $this->render('public/category/show.html.twig', [
            'category' => $category,
            'news' => $news,
        ]);
    }
}
