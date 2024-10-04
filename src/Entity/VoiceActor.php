<?php

namespace App\Entity;

use App\Repository\VoiceActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoiceActorRepository::class)]
#[ORM\Table(name: '`voice_actor`')]
class VoiceActor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 45)]
    #[Assert\NotBlank()]
    private ?string $voiceactor_firstname = null;

    #[ORM\Column(type: 'string', length: 45, unique: true)]
    #[Assert\NotBlank()]
    private ?string $voiceactor_lastname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Nationality $Nationality = null;

    #[ORM\ManyToMany(targetEntity: Bot::class, inversedBy: "voice_actors")]
    #[ORM\JoinTable(name: 'voice_actor_bot')]
    private ?Collection $bots;

    public function __construct()
    {
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

    public function getVoiceactorFirstname(): ?string
    {
        return $this->voiceactor_firstname;
    }

    public function setVoiceactorFirstname(string $voiceactor_firstname): static
    {
        $this->voiceactor_firstname = $voiceactor_firstname;

        return $this;
    }

    public function getVoiceactorLastname(): ?string
    {
        return $this->voiceactor_lastname;
    }

    public function setVoiceactorLastname(string $voiceactor_lastname): static
    {
        $this->voiceactor_lastname = $voiceactor_lastname;

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
        return $this->Nationality;
    }

    public function setNationality(?Nationality $Nationality): static
    {
        $this->Nationality = $Nationality;

        return $this;
    }

    /**
     * @return Collection<int, Bot>
     */
    public function getBots(): ?Collection
    {
        return $this->bots;
    }

    public function addBot(?Bot $bot): static
    {
        if (!$this->bots->contains($bot)) {
            $this->bots->add($bot);
        }

        return $this;
    }

    public function removeBot(?Bot $bot): static
    {
        $this->bots->removeElement($bot);

        return $this;
    }
}
