<?php

namespace App\Validator;

use App\Service\MediaProcessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MediaUrlValidator extends ConstraintValidator
{
    public function __construct(
        private readonly MediaProcessorInterface $mediaProcessor,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var MediaUrl $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (null === $this->mediaProcessor->getTemplateByUrl($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
