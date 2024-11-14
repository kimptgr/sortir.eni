<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['place_info', 'place_list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['place_info', 'place_list'])]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    #[Assert\Length(
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères de long',
        max: 255
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['place_info', 'place_list'])]
    #[Assert\NotBlank(message: 'La rue ne peut pas être vide')]
    #[Assert\Length(
        maxMessage: 'La rue ne peut pas dépasser {{ limit }} caractères',
        max: 255
    )]
    private ?string $street = null;

    #[ORM\Column]
    #[Groups(['place_info', 'place_list'])]
    #[Assert\NotBlank(message: 'La latitude est requise')]
    #[Assert\Type('float', message:"La latitude doit être un nombre")]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['place_info', 'place_list'])]
    #[Assert\NotBlank(message: 'La longitude est requise')]
#[Assert\Type('float', message:"La longitude doit être un nombre")]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'places')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['place_info', 'place_list'])]
    private ?City $city = null;

    /**
     * @var Collection<int, Trip>
     */
    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'place')]
    private Collection $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setPlace($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getPlace() === $this) {
                $trip->setPlace(null);
            }
        }

        return $this;
    }
}
