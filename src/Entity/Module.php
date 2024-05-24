<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?bool $isOperating = null;




    #[ORM\Column]
    private ?bool $isNew = null;

    #[ORM\Column]
    private ?int $fixCount = null;

   // #[Assert\NotNull(message: 'The buying date cannot be null.')]
    //#[Assert\LessThanOrEqual("today", message: 'The buying date must be today or in the past.')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prediction = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $buyingDate = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Data::class, mappedBy: 'module', orphanRemoval: true)]
    private Collection $data;

    #[ORM\Column(nullable: true)]
    private ?bool $actual = null;

    public function __construct()
    {
        $this->data = new ArrayCollection();
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isIsOperating(): ?bool
    {
        return $this->isOperating;
    }

    public function setIsOperating(bool $isOperating): static
    {
        $this->isOperating = $isOperating;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }


    public function isIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getFixCount(): ?int
    {
        return $this->fixCount;
    }

    public function setFixCount(int $fixCount): static
    {
        $this->fixCount = $fixCount;

        return $this;
    }

    public function getPrediction(): ?string
    {
        return $this->prediction;
    }

    public function setPrediction(?string $prediction): static
    {
        $this->prediction = $prediction;

        return $this;
    }

    public function getBuyingDate(): ?\DateTimeInterface
    {
        return $this->buyingDate;
    }

    public function setBuyingDate(?\DateTimeInterface $buyingDate): static
    {
        $this->buyingDate = $buyingDate;

        return $this;
    }

    /**
     * @return Collection<int, Data>
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    public function addData(Data $data): static
    {
        if (!$this->data->contains($data)) {
            $this->data->add($data);
            $data->setModule($this);
        }

        return $this;
    }

    public function removeData(Data $data): static
    {
        if ($this->data->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getModule() === $this) {
                $data->setModule(null);
            }
        }

        return $this;
    }

    public function isActual(): ?bool
    {
        return $this->actual;
    }

    public function setActual(?bool $actual): static
    {
        $this->actual = $actual;

        return $this;
    }

    public function incrementFixCount(): self
    {
        $this->fixCount++;
        return $this;
    }



}
