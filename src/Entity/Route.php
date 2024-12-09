<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\RouteRepository;
use App\Trait\BlameableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Order;
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
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['read']],
)]
#[Vich\Uploadable]
class Route implements BlameableInterface
{
    use BlameableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['read'])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    #[SerializedName('title')]
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

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'routes')]
    #[Groups(['read'])]
    private Collection $tags;

    #[ORM\Column(length: 255)]
    #[Groups(['read'])]
    private ?string $totalDuration = null;

    /**
     * @var Collection<int, PointOfInterest>
     */
    #[ORM\OneToMany(mappedBy: 'route', targetEntity: PointOfInterest::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['poiOrder' => Order::Ascending->value])]
    #[Groups(['read'])]
    private Collection $points;

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->points = new ArrayCollection();
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
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addRoute($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
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

    public function getTotalDuration(): ?string
    {
        return $this->totalDuration;
    }

    public function setTotalDuration(string $totalDuration): static
    {
        $this->totalDuration = $totalDuration;

        return $this;
    }

    /**
     * @return Collection<int, PointOfInterest>
     */
    public function getPoints(): Collection
    {
        return $this->points;
    }

    public function addPoint(PointOfInterest $point): static
    {
        if (!$this->points->contains($point)) {
            $this->points->add($point);
            $point->setRoute($this);
        }

        return $this;
    }

    public function removePoint(PointOfInterest $point): static
    {
        if ($this->points->removeElement($point)) {
            // set the owning side to null (unless already changed)
            if ($point->getRoute() === $this) {
                $point->setRoute(null);
            }
        }

        return $this;
    }
}
