<?php

namespace App\Entity;

use App\Repository\CreatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreatorRepository::class)]
class Creator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $creator_firstname = null;

    #[ORM\Column(length: 45)]
    private ?string $creator_lastname = null;

    #[ORM\Column(length: 45)]
    private ?string $category = null;

    #[ORM\ManyToMany(targetEntity: Show::class, inversedBy: "creators")]
    #[ORM\JoinTable(name: 'creator_show')]
    private ?Collection $shows = null;

    public function __construct()
    {
        $this->shows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatorFirstname(): ?string
    {
        return $this->creator_firstname;
    }

    public function setCreatorFirstname(string $creator_firstname): static
    {
        $this->creator_firstname = $creator_firstname;

        return $this;
    }

    public function getCreatorLastname(): ?string
    {
        return $this->creator_lastname;
    }

    public function setCreatorLastname(string $creator_lastname): static
    {
        $this->creator_lastname = $creator_lastname;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Show>
     */
    public function getShows(): ?Collection
    {
        return $this->shows;
    }

    public function addShow(Show $show): static
    {
        if (!$this->shows->contains($show)) {
            $this->shows->add($show);
        }

        return $this;
    }

    public function removeShow(Show $show): static
    {
        $this->shows->removeElement($show);

        return $this;
    }
}
