<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
#[ORM\Table(name: '`artist`')]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string' , length: 45)]
    #[Assert\NotBlank()]
    private ?string $artist_firstname = null;

    #[ORM\Column(type: 'string', length: 45, unique: true)]
    #[Assert\NotBlank()]
    private ?string $artist_lastname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $portfolio_link = null;

    /**
     * @var Collection<int, ConceptArt>
     */
    #[ORM\ManyToMany(targetEntity: ConceptArt::class, mappedBy: "artists")]
    private ?Collection $concept_arts = null;

    public function __construct()
    {
        $this->concept_arts = new ArrayCollection();
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

    public function getArtistFirstname(): ?string
    {
        return $this->artist_firstname;
    }

    public function setArtistFirstname(string $artist_firstname): static
    {
        $this->artist_firstname = $artist_firstname;

        return $this;
    }

    public function getArtistLastname(): ?string
    {
        return $this->artist_lastname;
    }

    public function setArtistLastname(string $artist_lastname): static
    {
        $this->artist_lastname = $artist_lastname;

        return $this;
    }

    public function getPortfolioLink(): ?string
    {
        return $this->portfolio_link;
    }

    public function setPortfolioLink(?string $portfolio_link): static
    {
        $this->portfolio_link = $portfolio_link;

        return $this;
    }

    /**
     * @return Collection<int, ConceptArt>
     */
    public function getConceptArts(): Collection
    {
        return $this->concept_arts;
    }

    public function addConceptArt(ConceptArt $concept_art): static
    {
        if (!$this->concept_arts->contains($concept_art)) {
            $this->concept_arts->add($concept_art);
            $concept_art->addArtist($this);
        }

        return $this;
    }

    public function removeConceptArt(ConceptArt $concept_art): static
    {
        $this->concept_arts->removeElement($concept_art);

        return $this;
    }
}
