<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_user_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(UserRepository $userRepo, MicroPostRepository $microPostRepo ,$id): Response
    {
        $user = $userRepo->find($id);
        return $this->render('user_profile/show.html.twig', [
            'user' => $user,
            'microPosts' => $microPostRepo->findAllByAuthor($id)
        ]);
    }

    #[Route('/profile/{id}/follows', name: 'app_profile_follows')]
    public function follows(UserRepository $repo, $id): Response
    {
        $user = $repo->findUserWithFollows($id);
        return $this->render('user_profile/follows.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/{id}/followers', name: 'app_profile_followers')]
    public function followers(UserRepository $repo, $id): Response
    {
        $user = $repo->findUserWithFollowers($id);
        return $this->render('user_profile/followers.html.twig', [
            'user' => $user,
        ]);
    }
}
