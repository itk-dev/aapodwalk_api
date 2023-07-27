<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['read']],
)]
#[Vich\Uploadable]
class Route
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['read'])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $distance = null;

    #[ORM\Column(length: 255)]
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

    #[Groups(['read'])]
    #[SerializedName('image')]
    public ?string $imageUrl = null;

    #[ORM\ManyToMany(targetEntity: PointOfInterest::class, inversedBy: 'routes')]
    #[ORM\OrderBy(['poiOrder' => 'ASC'])]
    #[Groups(['read'])]
    private Collection $pointsOfInterest;

    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'routes')]
    private Collection $tags;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $zoomValue = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $centerlatitude = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $centerlongitude = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $partcount = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $totalduration = null;

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function __construct()
    {
        $this->pointsOfInterest = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, PointOfInterest>
     */
    public function getPointsOfInterest(): Collection
    {
        return $this->pointsOfInterest;
    }

    public function addPointsOfInterest(PointOfInterest $pointsOfInterest): static
    {
        if (!$this->pointsOfInterest->contains($pointsOfInterest)) {
            $this->pointsOfInterest->add($pointsOfInterest);
        }

        return $this;
    }

    public function removePointsOfInterest(PointOfInterest $pointsOfInterest): static
    {
        $this->pointsOfInterest->removeElement($pointsOfInterest);

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addRoute($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeRoute($this);
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
    public function setImageFile(File $imageFile = null): void
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

    public function getZoomValue(): ?string
    {
        return $this->zoomValue;
    }

    public function setZoomValue(string $zoomValue): static
    {
        $this->zoomValue = $zoomValue;

        return $this;
    }

    public function getCenterlatitude(): ?string
    {
        return $this->centerlatitude;
    }

    public function setCenterlatitude(string $centerlatitude): static
    {
        $this->centerlatitude = $centerlatitude;

        return $this;
    }

    public function getCenterlongitude(): ?string
    {
        return $this->centerlongitude;
    }

    public function setCenterlongitude(string $centerlongitude): static
    {
        $this->centerlongitude = $centerlongitude;

        return $this;
    }

    public function getPartcount(): ?string
    {
        return $this->partcount;
    }

    public function setPartcount(string $partcount): static
    {
        $this->partcount = $partcount;

        return $this;
    }

    public function getTotalduration(): ?string
    {
        return $this->totalduration;
    }

    public function setTotalduration(string $totalduration): static
    {
        $this->totalduration = $totalduration;

        return $this;
    }
}
