<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDateTime = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationDeadline = null;

    #[ORM\Column]
    private ?int $nbRegistrationMax = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $info = null;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?State $state = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $relativeCampus = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'enrolledTrips')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'organizedTrips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $organizer = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getStartDateTime(): ?\DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationDeadline(): ?\DateTimeInterface
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(\DateTimeInterface $registrationDeadline): static
    {
        $this->registrationDeadline = $registrationDeadline;

        return $this;
    }

    public function getNbRegistrationMax(): ?int
    {
        return $this->nbRegistrationMax;
    }

    public function setNbRegistrationMax(int $nbRegistrationMax): static
    {
        $this->nbRegistrationMax = $nbRegistrationMax;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getRelativeCampus(): ?Campus
    {
        return $this->relativeCampus;
    }

    public function setRelativeCampus(?Campus $relativeCampus): static
    {
        $this->relativeCampus = $relativeCampus;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addEnrolledTrip($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeEnrolledTrip($this);
        }

        return $this;
    }

    public function getOrganizer(): ?Participant
    {
        return $this->organizer;
    }

    public function setOrganizer(?Participant $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }
}
