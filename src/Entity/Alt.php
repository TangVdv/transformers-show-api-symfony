<?php

namespace App\Entity;

use App\Repository\AltRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AltRepository::class)]
#[ORM\Table(name: '`alt`')]
class Alt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 45)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private ?string $alt_name = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $model_year = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToMany(targetEntity: Bot::class, inversedBy: "alts")]
    #[ORM\JoinTable(name: 'bot_alt')]
    private ?Collection $bots;

    public function __construct()
    {
        $this->bots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAltName(): ?string
    {
        return $this->alt_name;
    }

    public function setAltName(string $alt_name): static
    {
        $this->alt_name = $alt_name;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModelYear(): ?int
    {
        return $this->model_year;
    }

    public function setModelYear(?int $model_year): static
    {
        $this->model_year = $model_year;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Bot>
     */
    public function getBots(): ?Collection
    {
        return $this->bots;
    }

    public function addBot(Bot $bot): static
    {
        if (!$this->bots->contains($bot)) {
            $this->bots->add($bot);
            $bot->addAlt($this);
        }

        return $this;
    }

    public function removeBot(Bot $bot): static
    {
        $this->bots->removeElement($bot);

        return $this;
    }
}
