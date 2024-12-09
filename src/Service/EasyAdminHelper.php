<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\File;

class EasyAdminHelper
{
    public static function getFileInputAttributes(?object $entity, string $key)
    {
        $attr = [];

        if ($entity) {
            $refl = new \ReflectionProperty($entity, $key);
            foreach ($refl->getAttributes() as $attribute) {
                if (File::class === $attribute->getName()) {
                    foreach ($attribute->getArguments() as $name => $value) {
                        if ('mimeTypes' === $name) {
                            $attr['accept'] = implode(',', $value);
                        }
                    }
                }
            }
        }

        return $attr;
    }
}
