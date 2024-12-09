<?php

declare(strict_types=1);

namespace App\DataFixtures\Processor;

use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @see https://github.com/theofidry/AliceBundle/blob/master/doc/advanced-usage.md#validation
 */
final class EntityProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function preProcess(string $id, object $object): void
    {
        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($object);
        if ($violations->count() > 0) {
            $message = sprintf("Error when validating fixture %s, violation(s) detected:\n%s", $id, $violations);
            throw new \DomainException($message);
        }
    }

    public function postProcess(string $id, object $object): void
    {
    }
}
