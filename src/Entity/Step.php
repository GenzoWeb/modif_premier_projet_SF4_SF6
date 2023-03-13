<?php

namespace App\Entity;

use App\Repository\StepRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StepRepository::class)]
class Step
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number_step = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $steps = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberStep(): ?int
    {
        return $this->number_step;
    }

    public function setNumberStep(int $number_step): self
    {
        $this->number_step = $number_step;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSteps(): ?Recipe
    {
        return $this->steps;
    }

    public function setSteps(?Recipe $steps): self
    {
        $this->steps = $steps;

        return $this;
    }
}
