<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video
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
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="avis_video")
     */
    private $avis_video;

    public function __construct()
    {
        $this->avis_video = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvisVideo(): Collection
    {
        return $this->avis_video;
    }

    public function addAvisVideo(Avis $avisVideo): self
    {
        if (!$this->avis_video->contains($avisVideo)) {
            $this->avis_video[] = $avisVideo;
            $avisVideo->setAvisVideo($this);
        }

        return $this;
    }

    public function removeAvisVideo(Avis $avisVideo): self
    {
        if ($this->avis_video->contains($avisVideo)) {
            $this->avis_video->removeElement($avisVideo);
            // set the owning side to null (unless already changed)
            if ($avisVideo->getAvisVideo() === $this) {
                $avisVideo->setAvisVideo(null);
            }
        }

        return $this;
    }
}
