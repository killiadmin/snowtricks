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
    private $video_figure;

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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_update;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="med_figure_associated")
     */
    private $medias;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->medias = new ArrayCollection();
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

    public function getPictureFigure(): ?array
    {
        return $this->picture_figure;
    }

    public function setPictureFigure(array $picture_figure): self
    {
        $this->picture_figure = $picture_figure;

        return $this;
    }

    public function getVideoFigure(): ?string
    {
        return $this->video_figure;
    }

    public function setVideoFigure(string $video_figure): self
    {
        $this->video_figure = $video_figure;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setMedFigureAssociated($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getMedFigureAssociated() === $this) {
                $media->setMedFigureAssociated(null);
            }
        }

        return $this;
    }
}
