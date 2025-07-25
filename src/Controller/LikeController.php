<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class LikeController extends AbstractController
{
    #[Route('/micro-post/like/{id}', name: 'app_like')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = $entityManager->getRepository(MicroPost::class)->find($id);
        $post->addLikedBy($user);
        $entityManager->persist($post);
        $entityManager->flush();
        
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/micro-post/unlike/{id}', name: 'app_unlike')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unlike(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = $entityManager->getRepository(MicroPost::class)->find($id);
        $post->removeLikedBy($user);
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
