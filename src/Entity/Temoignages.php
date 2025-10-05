<?php

namespace App\Entity;

use App\Repository\TemoignagesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TemoignagesRepository::class)]
#[ORM\Table(name: 'temoignages')]
class Temoignages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['temoignages:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['temoignages:read'])]
    private ?string $nomClient = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['temoignages:read'])]
    private ?string $posteClient = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['temoignages:read'])]
    private ?string $entrepriseClient = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['temoignages:read'])]
    private ?string $avatarClient = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['temoignages:read'])]
    private ?string $contenu = null;

    #[ORM\Column]
    #[Groups(['temoignages:read'])]
    private ?int $note = null;

    #[ORM\Column]
    #[Groups(['temoignages:read'])]
    private ?bool $enVedette = false;

    #[ORM\Column]
    #[Groups(['temoignages:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->enVedette = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomClient(): ?string
    {
        return $this->nomClient;
    }

    public function setNomClient(string $nomClient): static
    {
        $this->nomClient = $nomClient;

        return $this;
    }

    public function getPosteClient(): ?string
    {
        return $this->posteClient;
    }

    public function setPosteClient(?string $posteClient): static
    {
        $this->posteClient = $posteClient;

        return $this;
    }

    public function getEntrepriseClient(): ?string
    {
        return $this->entrepriseClient;
    }

    public function setEntrepriseClient(?string $entrepriseClient): static
    {
        $this->entrepriseClient = $entrepriseClient;

        return $this;
    }

    public function getAvatarClient(): ?string
    {
        return $this->avatarClient;
    }

    public function setAvatarClient(?string $avatarClient): static
    {
        $this->avatarClient = $avatarClient;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

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
}
