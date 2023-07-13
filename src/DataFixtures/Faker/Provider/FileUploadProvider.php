<?php

namespace App\DataFixtures\Faker\Provider;

use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\Filesystem\Filesystem;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

class FileUploadProvider extends Base
{
    public function __construct(Generator $generator, private PropertyMappingFactory $propertyMappingFactory, private Filesystem $filesystem, private array $config)
    {
        parent::__construct($generator);
    }

    /**
     * Simulate file upload.
     *
     * @param string $path       the file path relative to the fixtures directory
     * @param string $targetPath the target of the uploaded file
     * @param string $fileName   the file name
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
