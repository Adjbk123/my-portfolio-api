<?php

namespace App\Entity;

use App\Repository\CategoriesBlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoriesBlogRepository::class)]
#[ORM\Table(name: 'categories_blog')]
class CategoriesBlog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['categories:read', 'articles:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['categories:read', 'articles:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['categories:read', 'articles:read'])]
    private ?string $slug = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['categories:read', 'articles:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 7, nullable: true)]
    #[Groups(['categories:read', 'articles:read'])]
    private ?string $couleur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateCreation = null;

    #[ORM\ManyToMany(targetEntity: ArticlesBlog::class, mappedBy: 'categories')]
    private Collection $articles;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, ArticlesBlog>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(ArticlesBlog $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addCategory($this);
        }

        return $this;
    }

    public function removeArticle(ArticlesBlog $article): static
    {
        if ($this->articles->removeElement($article)) {
            $article->removeCategory($this);
        }

        return $this;
    }
}
