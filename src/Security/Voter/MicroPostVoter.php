<?php

namespace App\Security\Voter;

use App\Entity\MicroPost;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class MicroPostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const WRITE = 'POST_WRITE';

    public function __construct(
        private SecurityBundleSecurity $security
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        if ($attribute === self::WRITE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof MicroPost;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $isAuth = $user instanceof UserInterface;

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $isAuth && $subject->getAuthor()->getId() === $user->getId()
                    || $this->security->isGranted('ROLE_EDITOR');
                break;
            case self::VIEW:
                if(!$subject->isExtraPrivacy()) {
                    return true;
                }
                // If the post has extra privacy, check if the user follows the author
                return $isAuth &&
                    ($subject->getAuthor()->getId() === $user->getId()
                        || $subject->getAuthor()->getFollows()->contains($user)
                    );
                
            case self::WRITE:
                return $this->security->isGranted('ROLE_WRITER');
        }

        return false;
    }
}
