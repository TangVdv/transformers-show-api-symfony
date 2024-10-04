<?php

namespace App\Entity;

use App\Repository\BelongingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BelongingRepository::class)]
#[ORM\Table(name: '`belonging`')]
class Belonging
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotNull()]
    private ?int $current = null;

    #[ORM\ManyToOne(targetEntity: Bot::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bot $bot;

    #[ORM\ManyToOne(targetEntity: Faction::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faction $faction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrent(): ?int
    {
        return $this->current;
    }

    public function setCurrent(?int $current): static
    {
        $this->current = $current;

        return $this;
    }

    public function getBot(): ?Bot
    {
        return $this->bot;
    }

    public function setBot(?Bot $bot): static
    {
        $this->bot = $bot;

        return $this;
    }

    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    public function setFaction(?Faction $faction): static
    {
        $this->faction = $faction;

        return $this;
    }
}
