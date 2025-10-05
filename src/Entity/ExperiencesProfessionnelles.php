<?php

namespace App\Entity;

use App\Repository\ExperiencesProfessionnellesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExperiencesProfessionnellesRepository::class)]
#[ORM\Table(name: 'experiences_professionnelles')]
class ExperiencesProfessionnelles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['experiences:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['experiences:read'])]
    private ?string $periode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['experiences:read'])]
    private ?string $entreprise = null;

    #[ORM\Column(length: 255)]
    #[Groups(['experiences:read'])]
    private ?string $poste = null;

    #[ORM\Column(length: 50)]
    #[Groups(['experiences:read'])]
    private ?string $type = 'professionnelle';

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['experiences:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['experiences:read'])]
    private ?int $ordreAffichage = 0;

    #[ORM\Column]
    #[Groups(['experiences:read'])]
    private ?bool $actif = true;

    #[ORM\Column]
    #[Groups(['experiences:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['experiences:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->actif = true;
        $this->ordreAffichage = 0;
        $this->type = 'professionnelle';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(string $periode): static
    {
        $this->periode = $periode;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): static
    {
        $this->poste = $poste;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOrdreAffichage(): ?int
    {
        return $this->ordreAffichage;
    }

    public function setOrdreAffichage(int $ordreAffichage): static
    {
        $this->ordreAffichage = $ordreAffichage;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
