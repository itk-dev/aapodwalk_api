<?php

namespace App\Security\Voter;

use App\Entity\BlameableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class EasyAdminVoter extends BlameableVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [Permission::EA_ACCESS_ENTITY, Permission::EA_EXECUTE_ACTION])
            && $subject instanceof EntityDto
            && $subject->getInstance() instanceof BlameableInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        return match ($attribute) {
            Permission::EA_ACCESS_ENTITY => $this->canEdit($subject->getInstance(), $user),
            //            Permission::EA_EXECUTE_ACTION => $this->canExecute($subject, $user),
            default => throw new \LogicException('This code should not be reached!'),
        };
    }
}
