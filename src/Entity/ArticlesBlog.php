<?php

namespace App\Entity;

use App\Repository\ArticlesBlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ArticlesBlogRepository::class)]
#[ORM\Table(name: 'articles_blog')]
class ArticlesBlog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['articles:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['articles:read'])]
    private ?string $titre = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['articles:read'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['articles:read'])]
    private ?string $extrait = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['articles:read'])]
    private ?string $contenu = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['articles:read'])]
    private ?string $imagePrincipale = null;


    #[ORM\Column(length: 50)]
    #[Groups(['articles:read'])]
    private ?string $statut = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['articles:read'])]
    private ?\DateTimeImmutable $datePublication = null;

    #[ORM\Column]
    #[Groups(['articles:read'])]
    private ?int $nombreVues = 0;

    #[ORM\Column]
    #[Groups(['articles:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['articles:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: CategoriesBlog::class, inversedBy: 'articles')]
    #[Groups(['articles:read'])]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: TagsBlog::class, inversedBy: 'articles')]
    #[Groups(['articles:read'])]
    private Collection $tags;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->statut = 'brouillon';
        $this->nombreVues = 0;
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getExtrait(): ?string
    {
        return $this->extrait;
    }

    public function setExtrait(?string $extrait): static
    {
        $this->extrait = $extrait;

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

    public function getImagePrincipale(): ?string
    {
        return $this->imagePrincipale;
    }

    public function setImagePrincipale(?string $imagePrincipale): static
    {
        $this->imagePrincipale = $imagePrincipale;

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

    public function getDatePublication(): ?\DateTimeImmutable
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeImmutable $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getNombreVues(): ?int
    {
        return $this->nombreVues;
    }

    public function setNombreVues(int $nombreVues): static
    {
        $this->nombreVues = $nombreVues;

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

    /**
     * @return Collection<int, CategoriesBlog>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(CategoriesBlog $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(CategoriesBlog $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, TagsBlog>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(TagsBlog $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(TagsBlog $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
