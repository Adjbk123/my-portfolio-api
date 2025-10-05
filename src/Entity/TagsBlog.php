<?php

namespace App\Entity;

use App\Repository\TagsBlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TagsBlogRepository::class)]
#[ORM\Table(name: 'tags_blog')]
class TagsBlog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tags:read', 'articles:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tags:read', 'articles:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['tags:read', 'articles:read'])]
    private ?string $slug = null;

    #[ORM\Column(length: 7, nullable: true)]
    #[Groups(['tags:read', 'articles:read'])]
    private ?string $couleur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToMany(targetEntity: ArticlesBlog::class, mappedBy: 'tags')]
    private Collection $articles;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

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
            $article->addTag($this);
        }

        return $this;
    }

    public function removeArticle(ArticlesBlog $article): static
    {
        if ($this->articles->removeElement($article)) {
            $article->removeTag($this);
        }

        return $this;
    }
}
