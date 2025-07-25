<?php
namespace App\Security;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserChecker implements UserCheckerInterface{
    /**
     * Checks the user account before authentication.
     *
     * @param User $user
     */

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if($user->getBannedUntil() === null){
            return;
        }

        $now = new DateTime();

        if($now < $user->getBannedUntil()){
            throw new AccessDeniedException('The user is banned');
        }

        $user->setBannedUntil(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Checks the user account after authentication.
     *
     * @param User $user
     */
    public function checkPostAuth(UserInterface $user /* , TokenInterface $token */): void
    {

    }
}