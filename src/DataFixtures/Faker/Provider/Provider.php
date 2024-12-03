<?php

namespace App\DataFixtures\Faker\Provider;

use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class Provider extends Base
{
    public function __construct(
        Generator $generator,
        readonly private Filesystem $filesystem,
        readonly private MimeTypeGuesserInterface $mimeTypeGuesser,
        readonly private UserPasswordHasherInterface $passwordHasher,
        readonly private array $config,
    ) {
        parent::__construct($generator);
    }

    /**
     * Simulate file upload using VichUploader.
     *
     * @param string $path the file path relative to the fixtures directory
     */
    public function uploadFile(string $path)
    {
        $sourcePath = $this->config['project_dir'].'/fixtures/'.$path;
        if (!file_exists($sourcePath)) {
            throw new \InvalidArgumentException(sprintf('File source path %s does not exist', $sourcePath));
        }

        // The uploaded file will be deleted, so we create a copy of the input file.
        $tmpPath = $this->filesystem->tempnam(sys_get_temp_dir(), 'upload');
        $this->filesystem->copy($sourcePath, $tmpPath, true);

        return new UploadedFile(
            $tmpPath,
            basename($sourcePath),
            null,
            null,
            true
        );
    }

    /**
     * Generate user password.
     *
     * Usage:
     *   App\Entity\User:
     *     password: '<password(@self, "apassword")>'
     *
     * @return string
     */
    public function password(PasswordAuthenticatedUserInterface $user, string $plaintextPassword)
    {
        return $this->passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
    }
}
