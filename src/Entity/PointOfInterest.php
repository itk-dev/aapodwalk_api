<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PointOfInterestRepository;
use App\Trait\BlameableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PointOfInterestRepository::class)]
#[ApiResource(
    operations: [
    ],
    normalizationContext: ['groups' => ['read']],
)]
#[Vich\Uploadable]
class PointOfInterest implements BlameableInterface
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['read'])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10000, nullable: false)]
    #[Groups(['read'])]
    private ?string $subtitles = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Groups(['read'])]
    private ?int $poiOrder = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: 'uploads', fileNameProperty: 'image')]
    #[Assert\File(
        mimeTypes: [
            'image/jpeg',
            'image/png',
        ],
        maxSize: '2M'
    )]
    private ?File $imageFile = null;

    // Set by serializer (cf. FileNormalizer).
    #[Groups(['read'])]
    #[SerializedName('image')]
    public ?string $imageUrl = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 16)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 16)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    private ?string $longitude = null;

    #[ORM\ManyToMany(targetEntity: Route::class, mappedBy: 'pointsOfInterest')]
    private Collection $routes;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    private ?int $proximityToUnlock = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Url()]
    #[Groups(['read'])]
    private ?string $mediaUrl = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read'])]
    private ?bool $isAudio = true;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubtitles(): ?string
    {
        return $this->subtitles;
    }

    public function setSubtitles(string $subtitles): static
    {
        $this->subtitles = $subtitles;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLocation(): array
    {
        return [$this->getLatitude(), $this->getLongitude()];
    }

    public function setLocation(array $location): static
    {
        return $this
            ->setLatitude($location[0])
            ->setLongitude($location[1]);
    }

    /**
     * @return Collection<int, Route>
     */
    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function addRoute(Route $route): static
    {
        if (!$this->routes->contains($route)) {
            $this->routes->add($route);
            $route->addPointsOfInterest($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): static
    {
        if ($this->routes->removeElement($route)) {
            $route->removePointsOfInterest($this);
        }

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getPoiOrder(): ?int
    {
        return $this->poiOrder;
    }

    public function setPoiOrder(int $poiOrder): static
    {
        $this->poiOrder = $poiOrder;

        return $this;
    }

    public function getProximityToUnlock(): ?int
    {
        return $this->proximityToUnlock;
    }

    public function setProximityToUnlock(?int $proximityToUnlock): static
    {
        $this->proximityToUnlock = $proximityToUnlock;

        return $this;
    }

    public function getMediaUrl(): ?string
    {
        return $this->mediaUrl;
    }

    public function setMediaUrl(string $mediaUrl): static
    {
        $this->mediaUrl = $mediaUrl;

        return $this;
    }

    public function isAudio(): ?bool
    {
        return $this->isAudio;
    }

    public function setIsAudio(bool $isAudio): static
    {
        $this->isAudio = $isAudio;

        return $this;
    }
}
