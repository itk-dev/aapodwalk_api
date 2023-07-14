<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\File;

class EasyAdminHelper
{
    public static function getFileInputAttributes(\ReflectionProperty $fileRefl){
        $attr = [];
        foreach ($fileRefl->getAttributes() as $attribute) {
            if (File::class === $attribute->getName()) {
                foreach ($attribute->getArguments() as $name => $value) {
                    if ('mimeTypes' === $name) {
                        $attr['accept'] = implode(',', $value);
                    }
                }
            }
        }
        return $attr;
    }
}
