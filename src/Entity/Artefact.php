<?php

namespace App\Entity;

use App\Repository\ArtefactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtefactRepository::class)]
#[ORM\Table(name: '`artefact`')]
class Artefact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entity $Entity = null;

    /**
     * @var Collection<int, ScreenTime>
     */
    #[ORM\ManyToMany(targetEntity: ScreenTime::class, mappedBy: "artefacts")]
    private Collection $screen_times;

    public function __construct()
    {
        $this->screen_times = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEntity(): ?Entity
    {
        return $this->Entity;
    }

    public function setEntity(?Entity $Entity): static
    {
        $this->Entity = $Entity;

        return $this;
    }

    /**
     * @return Collection<int, ScreenTime>
     */
    public function getScreenTimes(): Collection
    {
        return $this->screen_times;
    }

    public function addScreenTime(ScreenTime $screenTime): static
    {
        if (!$this->screen_times->contains($screenTime)) {
            $this->screen_times->add($screenTime);
            $screenTime->addArtefact($this);
        }

        return $this;
    }

    public function removeScreenTime(ScreenTime $screenTime): static
    {
        $this->screen_times->removeElement($screenTime);

        return $this;
    }
}
