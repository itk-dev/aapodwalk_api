<?php

namespace App\DataFixtures\Faker\Provider;

use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class FileUploadProvider extends Base
{
    public function __construct(Generator $generator, private PropertyMappingFactory $propertyMappingFactory, private Filesystem $filesystem, private array $config)
    {
        parent::__construct($generator);
    }

    /**
     * Simulate file upload using VichUploader.
     *
     * @param string $path      the file path relative to the fixtures directory
     * @param string $property  the object property
     * @param string $className the object class name
     *
     * @return string the file path relative to the configured upload destination
     */
    public function uploadFile(string $path, string $targetPath, string $fileName)
    {
        $path = ltrim($path, '/');
        $sourcePath = $this->config['project_dir'].'/fixtures/'.$path;

        if (!file_exists($sourcePath)) {
            throw new \InvalidArgumentException(sprintf('File source path %s does not exist', $sourcePath));
        }
        $this->filesystem->copy($sourcePath, $targetPath);

        return $fileName;
    }


}