<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Form\CommentForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\MicroPostForm;
use App\Security\Voter\MicroPostVoter;

// #[IsGranted('IS_AUTHENTICATED_FULLY')]
final class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $entityManager->getRepository(MicroPost::class)->findAllWithCommentsAndLikes(),
        ]);
    }

    #[Route(path: '/micro-post/{id<\d+>}', name: 'app_micro_post_show')]
    public function showOne(MicroPostRepository $repo, $id): Response
    {
        $microPost = $repo->find($id);
        return $this->render('micro_post/show.html.twig', [
            'post' => $microPost,
        ]);
    }

    #[Route(path: '/micro-post/add', name: 'app_micro_post_add')]
    #[IsGranted(MicroPostVoter::WRITE)]
    public function add(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
        );

        $form = $this->createForm(MicroPostForm::class, new MicroPost());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost = $form->getData();
            $microPost->setAuthor($this->getUser());
            $entityManager->persist($microPost);
            $entityManager->flush();

            $this->addFlash('success', 'Micro post succesfully created');

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render(
            'micro_post/add.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route(path: '/micro-post/{microPost}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPostVoter::EDIT, 'microPost')]
    public function edit(MicroPost $microPost, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($microPost->getAuthor() !== $this->getUser()){
            $this->addFlash('error', 'You are not the author of the post!');
            return $this->redirectToRoute('app_micro_post');
        }

        $form = $this->createForm(MicroPostForm::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $microPost = $form->getData();
            $entityManager->persist($microPost);
            $entityManager->flush();

            $this->addFlash('success', 'Micro post succesfully edited');

            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render(
            'micro_post/edit.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route(path: '/micro-post/{microPost}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('ROLE_COMMENTER')]
    public function addComment(MicroPost $microPost, EntityManagerInterface $entityManager, Request $request): Response
    {

        $form = $this->createForm(CommentForm::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setMicroPost($microPost);
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Comment succesfully created');


            return $this->redirectToRoute('app_micro_post_show', ['id' => $microPost->getId()]);
        }

        return $this->render(
            'micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $microPost
            ]
        );
    }

    #[Route(path: '/micro-post/top_liked', name: 'app_micro_post_top_liked')]
    public function showTopLikedPosts(MicroPostRepository $repo): Response
    {
        $posts = $repo->findTopLikedPosts();

        return $this->render('micro_post/top_liked_posts.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route(path: '/micro-post/followed', name: 'app_micro_post_followed')]
    public function showFromFollowedPosts(MicroPostRepository $repo): Response
    {
        $user = $this->getUser();
        $posts = $repo->findFollowedPosts($user);

        return $this->render('micro_post/followed_posts.html.twig', [
            'posts' => $posts,
        ]);
    }
}
