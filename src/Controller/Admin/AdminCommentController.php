<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\News;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminCommentController extends AbstractController
{
    #[Route('/admin/comment', name: 'app_admin_comment')]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/admin/comment/{id}/delete', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $newsId = null;
        if ($comment->getNews()) {
            $newsId = $comment->getNews()->getId();
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success', 'Comment deleted successfully');
        }

        return $this->redirectToRoute('app_admin_news_comments', ['id' => $newsId]);
    }


    #[Route('/admin/news/{id}/comments', name: 'app_admin_news_comments')]
    public function comments(News $news): Response
    {
        return $this->render('admin/comment/comments.html.twig', [
            'news' => $news,
            'comments' => $news->getComments()
        ]);
    }
}
