<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Text;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Created_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userComment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=BlogArticle::class, inversedBy="artcileComment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(string $Text): self
    {
        $this->Text = $Text;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->Created_at;
    }

    public function setCreatedAt(\DateTimeInterface$Created_at): self
    {
        $this->Created_at = $Created_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getArticle(): ?BlogArticle
    {
        return $this->Article;
    }

    public function setArticle(?BlogArticle $Article): self
    {
        $this->Article = $Article;

        return $this;
    }
}
