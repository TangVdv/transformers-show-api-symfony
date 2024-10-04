<?php

namespace App\Entity;

use App\Repository\HumanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HumanRepository::class)]
#[ORM\Table(name: '`human`')]
class Human
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entity $entity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Actor $actor = null;

    /**
     * @var Collection<int, ScreenTime>
     */
    #[ORM\ManyToMany(targetEntity: ScreenTime::class, mappedBy: "humans")]
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
        return $this->entity;
    }

    public function setEntity(?Entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getActor(): ?Actor
    {
        return $this->actor;
    }

    public function setActor(?Actor $actor): static
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * @return Collection<int, ScreenTime>
     */
    public function getScreenTimes(): ?Collection
    {
        return $this->screen_times;
    }

    public function addScreenTime(?ScreenTime $screenTime): static
    {
        if (!$this->screen_times->contains($screenTime)) {
            $this->screen_times->add($screenTime);
            $screenTime->addHuman($this);
        }

        return $this;
    }

    public function removeScreenTime(?ScreenTime $screenTime): static
    {
        $this->screen_times->removeElement($screenTime);

        return $this;
    }
}
