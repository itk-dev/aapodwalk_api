<?php

namespace App\Security\Voter;

use App\Entity\BlameableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter that checks if a user can manage an entity based on whs has created the entity, i.e. is blameable.
 */
class BlameableVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    public function __construct(
        protected readonly AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof BlameableInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Anybody can view anything.
        if (self::VIEW === $attribute) {
            return true;
        }

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        assert($subject instanceof BlameableInterface);

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }

    protected function canEdit(BlameableInterface $subject, UserInterface $user): bool
    {
        // @todo Allow groups of users to edit?
        return $subject->getCreatedBy() === $user;
    }

    protected function canDelete(BlameableInterface $subject, UserInterface $user): bool
    {
        return $subject->getCreatedBy() === $user;
    }
}
