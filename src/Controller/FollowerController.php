<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FollowerController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follow(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userToFollow = $entityManager->getRepository(User::class)->find($id);

        if ($user !== $userToFollow)
        {
            $user->addFollow($userToFollow);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        
        return $this->redirect($request->headers->get('referer'));
    }


    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unfollow(EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userToFollow = $entityManager->getRepository(User::class)->find($id);

        if ($user !== $userToFollow)
        {
            $user->removeFollow($userToFollow);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        
        return $this->redirect($request->headers->get('referer'));
    }
}
