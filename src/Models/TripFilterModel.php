<?php

namespace App\Models;

use DateTime;
use Proxies\__CG__\App\Entity\Campus;

class TripFilterModel
{
    private ?Campus $relativeCampus = null;
    private ?string $tripName = null;
    private ?DateTime $startDateTime = null;
    private ?DateTime $registrationDeadline = null;
    private ?bool $iOrganized = null;
    private ?bool $iParticipate = null;
    private ?bool $imRegistered = null;
    private ?bool $oldTrips = null;

    public function getRelativeCampus(): ?Campus
    {
        return $this->relativeCampus;
    }

    public function setRelativeCampus(?Campus $relativeCampus): self
    {
        $this->relativeCampus = $relativeCampus;
        return $this;
    }

    public function getTripName(): ?string
    {
        return $this->tripName;
    }

    public function setTripName(?string $tripName): self
    {
        $this->tripName = $tripName;
        return $this;
    }

    public function getStartDateTime(): ?DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(?DateTime $startDateTime): self
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }

    public function getRegistrationDeadline(): ?DateTime
    {
        return $this->registrationDeadline;
    }

    public function setRegistrationDeadline(?DateTime $registrationDeadline): self
    {
        $this->registrationDeadline = $registrationDeadline;
        return $this;
    }

    public function getIOrganized(): ?bool
    {
        return $this->iOrganized;
    }

    public function setIOrganized(?bool $iOrganized): self
    {
        $this->iOrganized = $iOrganized;
        return $this;
    }

    public function getIParticipate(): ?bool
    {
        return $this->iParticipate;
    }

    public function setIParticipate(?bool $iParticipate): self
    {
        $this->iParticipate = $iParticipate;
        return $this;
    }

    public function getImRegistered(): ?bool
    {
        return $this->imRegistered;
    }

    public function setImRegistered(?bool $imRegistered): self
    {
        $this->imRegistered = $imRegistered;
        return $this;
    }

    public function getOldTrips(): ?bool
    {
        return $this->oldTrips;
    }

    public function setOldTrips(?bool $oldTrips): self
    {
        $this->oldTrips = $oldTrips;
        return $this;
    }

}