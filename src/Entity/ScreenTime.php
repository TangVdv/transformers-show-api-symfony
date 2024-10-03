<?php

namespace App\Entity;

use App\Repository\ScreenTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScreenTimeRepository::class)]
#[ORM\Table(name: '`screen_time`')]
class ScreenTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $hour = null;

    #[ORM\Column]
    private ?int $minute = null;

    #[ORM\Column]
    private ?int $second = null;

    #[ORM\Column]
    private ?int $total = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Show $show = null;

    /**
     * @var Collection<int, Artefact>
     */
    #[ORM\ManyToMany(targetEntity: Artefact::class, inversedBy: "screen_times")]
    #[ORM\JoinTable(name: "artefact_screen_time")]
    private Collection $artefacts;

    public function __construct()
    {
        $this->artefacts = new ArrayCollection();
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

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(int $hour): static
    {
        $this->hour = $hour;

        return $this;
    }

    public function getMinute(): ?int
    {
        return $this->minute;
    }

    public function setMinute(int $minute): static
    {
        $this->minute = $minute;

        return $this;
    }

    public function getSecond(): ?int
    {
        return $this->second;
    }

    public function setSecond(int $second): static
    {
        $this->second = $second;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getShow(): ?Show
    {
        return $this->show;
    }

    public function setShow(?Show $show): static
    {
        $this->show = $show;

        return $this;
    }

    /**
     * @return Collection<int, Artefact>
     */
    public function getArtefacts(): Collection
    {
        return $this->artefacts;
    }

    public function addArtefact(Artefact $artefact): static
    {
        if (!$this->artefacts->contains($artefact)) {
            $this->artefacts->add($artefact);
        }

        return $this;
    }

    public function removeArtefact(Artefact $artefact): static
    {
        $this->artefacts->removeElement($artefact);

        return $this;
    }
}
