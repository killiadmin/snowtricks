<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(normalizer="trim")
     * @ORM\Column(type="text")
     */
    private $content_comment;

    /**
     * @ORM\Column(type="date")
     */
    private $date_create;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     */
    private $user_associated;

    /**
     * @ORM\ManyToOne(targetEntity=Figure::class, inversedBy="comments")
     */
    private $figure_associated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentComment(): ?string
    {
        return $this->content_comment;
    }

    public function setContentComment(string $content_comment): self
    {
        $this->content_comment = $content_comment;

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

    public function getFigureAssociated(): ?Figure
    {
        return $this->figure_associated;
    }

    public function setFigureAssociated(?Figure $figure_associated): self
    {
        $this->figure_associated = $figure_associated;

        return $this;
    }
}
