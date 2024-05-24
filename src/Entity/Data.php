<?php

namespace App\Entity;

use App\Repository\DataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataRepository::class)]
class Data
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $measuredValue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $workingTime = null;

    #[ORM\Column]
    private ?float $speed = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\ManyToOne(inversedBy: 'data')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeasuredValue(): ?float
    {
        return $this->measuredValue;
    }

    public function setMeasuredValue(float $measuredValue): static
    {
        $this->measuredValue = $measuredValue;

        return $this;
    }

    public function getWorkingTime(): ?\DateTimeInterface
    {
        return $this->workingTime;
    }

    public function setWorkingTime(\DateTimeInterface $workingTime): static
    {
        $this->workingTime = $workingTime;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): static
    {
        $this->module = $module;

        return $this;
    }


}
