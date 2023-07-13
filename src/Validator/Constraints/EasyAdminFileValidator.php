<?php

namespace App\Validator\Constraints;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Model\FileUploadState;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\FileValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * https://github.com/EasyCorp/EasyAdminBundle/issues/5227#issuecomment-1167821550 :)
 * This constraint is created for adding validation support to the easyAdmin field type "ImageField".
 * The Symfony constraint file validation is expecting an object of type "UploadedFile" or "FileObject" for
 * handling the validation but EasyAdmin only returned the filename.
 * Therefore, we have to load the object first before calling the symfony file validator.
 * Created for versions:
 * symfony: 6.1
 * easycorp/easyadmin-bundle: 4.3.2.
 *
 * Class EasyAdminFileValidator
 */
class EasyAdminFileValidator extends FileValidator
{
    /**
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof EasyAdminFile) {
            throw new UnexpectedTypeException($constraint, EasyAdminFile::class);
        }

        $object = $this->context->getObject();
        if (null !== $value
            && null !== $object
            && $object instanceof Form
            && $object->getConfig() instanceof FormBuilder
        ) {
            $config = $object->getConfig();

            /** @var FileUploadState $state */
            $state = $config->getAttribute('state');

            if (!$state instanceof FileUploadState
                || !$state->isModified()
            ) {
                return;
            }

            // On the upload field we can set the option for multiple uploads, so we need to take care of this
            foreach ($state->getUploadedFiles() as $index => $file) {
                parent::validate($file, $constraint);
            }
        }
    }
}
