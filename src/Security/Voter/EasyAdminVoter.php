<?php

namespace App\Security\Voter;

use App\Entity\BlameableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class EasyAdminVoter extends BlameableVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [Permission::EA_EXECUTE_ACTION])
            && is_array($subject)
            && isset($subject['entity'])
            && $subject['entity'] instanceof EntityDto
        && $subject['entity']->getInstance() instanceof BlameableInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $action = $subject['action'];
        $attr = match (true) {
            is_string($action) => $action,
            $action instanceof ActionDto => $action->getName(),
            default => throw new \LogicException(__METHOD__),
        };
        $subj = $subject['entity']->getInstance();

        return parent::supports($attr, $subj)
            ? parent::voteOnAttribute($attr, $subj, $token)
            : true;
    }
}
