<?php

namespace App\Entity;

use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[ORM\Table(name: '`actor`')]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 45)]
    #[Assert\NotBlank()]
    private ?string $actor_firstname = null;

    #[ORM\Column(type: 'string', length: 45, unique: true)]
    #[Assert\NotBlank()]
    private ?string $actor_lastname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Nationality $nationality = null;

    /**
     * @var Collection<int, Human>
     */
    #[ORM\OneToMany(targetEntity: Human::class, mappedBy: 'actor')]
    private ?Collection $humans;

    public function __construct()
    {
        $this->humans = new ArrayCollection();
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

    public function getActorFirstname(): ?string
    {
        return $this->actor_firstname;
    }

    public function setActorFirstname(string $actor_firstname): static
    {
        $this->actor_firstname = $actor_firstname;

        return $this;
    }

    public function getActorLastname(): ?string
    {
        return $this->actor_lastname;
    }

    public function setActorLastname(string $actor_lastname): static
    {
        $this->actor_lastname = $actor_lastname;

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

    public function getNationality(): ?Nationality
    {
        return $this->nationality;
    }

    public function setNationality(?Nationality $nationality): static
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection<int, Human>
     */
    public function getHumans(): ?Collection
    {
        return $this->humans;
    }
}
