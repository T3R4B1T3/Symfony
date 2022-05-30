<?php

namespace App\Entity;

use App\Repository\BlogArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass=BlogArticleRepository::class)
 */
class BlogArticle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $descritpion;

    /**
     * @ORM\Column(type="text")
     */
    private $article;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="text")
     */
    private $created_by;

    /**
     * @ORM\ManyToOne(targetEntity=BlogCategory::class, inversedBy="categories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="Article", orphanRemoval=true)
     */
    private $artcileComment;



    public function __construct()
    {
        $this->comment = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->artcileComment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescritpion(): ?string
    {
        return $this->descritpion;
    }

    public function setDescritpion(string $descritpion): self
    {
        $this->descritpion = $descritpion;

        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(string $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getArtcileComment(): Collection
    {
        return $this->artcileComment;
    }

    public function addArtcileComment(Comment $artcileComment): self
    {
        if (!$this->artcileComment->contains($artcileComment)) {
            $this->artcileComment[] = $artcileComment;
            $artcileComment->setArticle($this);
        }

        return $this;
    }

    public function removeArtcileComment(Comment $artcileComment): self
    {
        if ($this->artcileComment->removeElement($artcileComment)) {
            // set the owning side to null (unless already changed)
            if ($artcileComment->getArticle() === $this) {
                $artcileComment->setArticle(null);
            }
        }

        return $this;
    }

}
