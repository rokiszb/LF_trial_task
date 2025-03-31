<?php

namespace App\Controller\Public;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NewsViewController extends AbstractController
{
    #[Route('/news/{id}', name: 'news_view')]
    public function view(News $news, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new comment form
        $comment = new Comment();
        $comment->setNews($news);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Your comment has been added!');
            return $this->redirectToRoute('news_view', ['id' => $news->getId()]);
        }

        return $this->render('public/news/view.html.twig', [
            'news' => $news,
            'comment_form' => $form->createView(),
        ]);
    }
}
