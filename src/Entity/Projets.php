<?php

namespace App\Entity;

use App\Repository\ProjetsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjetsRepository::class)]
#[ORM\Table(name: 'projets')]
class Projets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['projets:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['projets:read'])]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $descriptionComplete = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $categorie = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $imagePrincipale = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['projets:read'])]
    private ?array $galerie = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['projets:read'])]
    private ?array $technologies = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['projets:read'])]
    private ?array $fonctionnalites = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $duree = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $client = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $lienGithub = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['projets:read'])]
    private ?string $lienProjet = null;

    #[ORM\Column(length: 50)]
    #[Groups(['projets:read'])]
    private ?string $statut = null;

    #[ORM\Column]
    #[Groups(['projets:read'])]
    private ?bool $enVedette = false;

    #[ORM\Column]
    #[Groups(['projets:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['projets:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->enVedette = false;
        $this->statut = 'brouillon';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDescriptionComplete(): ?string
    {
        return $this->descriptionComplete;
    }

    public function setDescriptionComplete(?string $descriptionComplete): static
    {
        $this->descriptionComplete = $descriptionComplete;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getImagePrincipale(): ?string
    {
        return $this->imagePrincipale;
    }

    public function setImagePrincipale(?string $imagePrincipale): static
    {
        $this->imagePrincipale = $imagePrincipale;

        return $this;
    }

    public function getGalerie(): ?array
    {
        return $this->galerie;
    }

    public function setGalerie(?array $galerie): static
    {
        $this->galerie = $galerie;

        return $this;
    }

    public function getTechnologies(): ?array
    {
        return $this->technologies;
    }

    public function setTechnologies(?array $technologies): static
    {
        $this->technologies = $technologies;

        return $this;
    }

    public function getFonctionnalites(): ?array
    {
        return $this->fonctionnalites;
    }

    public function setFonctionnalites(?array $fonctionnalites): static
    {
        $this->fonctionnalites = $fonctionnalites;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(?string $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getLienGithub(): ?string
    {
        return $this->lienGithub;
    }

    public function setLienGithub(?string $lienGithub): static
    {
        $this->lienGithub = $lienGithub;

        return $this;
    }

    public function getLienProjet(): ?string
    {
        return $this->lienProjet;
    }

    public function setLienProjet(?string $lienProjet): static
    {
        $this->lienProjet = $lienProjet;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isEnVedette(): ?bool
    {
        return $this->enVedette;
    }

    public function setEnVedette(bool $enVedette): static
    {
        $this->enVedette = $enVedette;

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
