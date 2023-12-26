<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Figure::class, inversedBy="medias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $med_figure_associated;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $med_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $med_video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $med_image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedFigureAssociated(): ?Figure
    {
        return $this->med_figure_associated;
    }

    public function setMedFigureAssociated(?Figure $med_figure_associated): self
    {
        $this->med_figure_associated = $med_figure_associated;

        return $this;
    }

    public function getMedType(): ?string
    {
        return $this->med_type;
    }

    public function setMedType(string $med_type): self
    {
        $this->med_type = $med_type;

        return $this;
    }

    public function getMedVideo(): ?string
    {
        return $this->med_video;
    }

    public function setMedVideo(?string $med_video): self
    {
        $this->med_video = $med_video;

        return $this;
    }

    public function getMedImage(): ?string
    {
        return $this->med_image;
    }

    public function setMedImage(?string $med_image): self
    {
        $this->med_image = $med_image;

        return $this;
    }
}
