<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    security: "is_granted('ROLE_API_USER')"
)]
class Route
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $distance = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: PointOfInterest::class, inversedBy: 'routes')]
    private Collection $pointsOfInterest;

    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'routes')]
    private Collection $tags;

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
        return '/uploads/images/'.$this->image;
    }

    public function setImage(string $image): static
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
}
