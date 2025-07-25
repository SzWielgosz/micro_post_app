<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private array $messages = [
        ['message' => 'Hello', 'created' => '2025/03/12'],
        ['message' => 'Hi', 'created' => '2025/04/12'],
        ['message' => 'Bye!', 'created' => '2024/05/12']
    ];

    #[Route(path: '/', name: 'app_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, UserRepository $repo): Response
    {
        // return new Response(implode(', ', array_slice($this->messages, 0, $limit)));
        // $user = new User();
        // $user->setEmail('john_doe@example.com');
        // $user->setPassword('12345678');
        // $entityManager->persist($user);

        // $userProfile = new UserProfile();
        // $userProfile->setUser($user);
        // $entityManager->persist($userProfile);

        // $entityManager->flush();

        // $user = $repo->find(1);
        // $entityManager->remove($user);
        // $entityManager->flush();

        // $post = new MicroPost();
        // $post->setTitle('Hello');
        // $post->setText('Hello');
        // $post->setCreated(new DateTime());
        // $entityManager->persist($post);

        // $comment = new Comment();
        // $comment->setText('Hello');
        // //$comment->setMicroPost($post);
        
        // $post->addComment($comment);
        // $entityManager->persist($comment);
        // $entityManager->flush();


        return $this->render(
            'hello/index.html.twig',
            [
                'messages' => $this->messages,
                'limit' => 3
            ]
        );
    }

    #[Route(path: '/messages/{id<\d+>}', name: 'app_show_one', methods: ['GET'])]
    public function showOne($id): Response
    {
        return $this->render(
            'hello/show_one.html.twig',
            [
                'message' => $this->messages[$id]
            ]
        );
    }
}
