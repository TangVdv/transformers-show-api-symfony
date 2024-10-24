<?php

namespace App\Entity;

use App\Repository\ScreenTimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScreenTimeRepository::class)]
#[ORM\Table(name: '`screen_time`')]
class ScreenTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $hour = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $minute = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $second = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $total = null;

    #[ORM\OneToMany(targetEntity: Bot::class, mappedBy: "screen_time")]
    private ?Collection $bots = null;

    #[ORM\OneToMany(targetEntity: Artefact::class, mappedBy: "screen_time")]
    private ?Collection $artefacts = null;

    #[ORM\OneToMany(targetEntity: Human::class, mappedBy: "screen_time")]
    private ?Collection $humans = null;

    public function __construct()
    {
        $this->artefacts = new ArrayCollection();
        $this->humans = new ArrayCollection();
        $this->bots = new ArrayCollection();
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

    public function getArtefacts(): ?Collection
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

    function getHumans(): ?Collection
    {
        return $this->humans;
    }

    public function addHuman(Human $human): static
    {
        if (!$this->humans->contains($human)) {
            $this->humans->add($human);
        }

        return $this;
    }

    public function getBots(): ?Collection
    {
        return $this->bots;
    }

    public function addBot(Bot $bot): static
    {
        if (!$this->bots->contains($bot)) {
            $this->bots->add($bot);
        }

        return $this;
    }
}
