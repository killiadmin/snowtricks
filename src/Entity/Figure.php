<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FigureRepository::class)
 */
class Figure
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
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content_figure;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $picture_figure;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $video_picture;

    /**
     * @ORM\Column(type="date")
     */
    private $date_create;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="figures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_associated;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="figure_associated")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContentFigure(): ?string
    {
        return $this->content_figure;
    }

    public function setContentFigure(string $content_figure): self
    {
        $this->content_figure = $content_figure;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPictureFigure(): ?string
    {
        return $this->picture_figure;
    }

    public function setPictureFigure(string $picture_figure): self
    {
        $this->picture_figure = $picture_figure;

        return $this;
    }

    public function getVideoPicture(): ?string
    {
        return $this->video_picture;
    }

    public function setVideoPicture(string $video_picture): self
    {
        $this->video_picture = $video_picture;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->date_create;
    }

    public function setDateCreate(\DateTimeInterface $date_create): self
    {
        $this->date_create = $date_create;

        return $this;
    }

    public function getUserAssociated(): ?User
    {
        return $this->user_associated;
    }

    public function setUserAssociated(?User $user_associated): self
    {
        $this->user_associated = $user_associated;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFigureAssociated($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFigureAssociated() === $this) {
                $comment->setFigureAssociated(null);
            }
        }

        return $this;
    }
}
