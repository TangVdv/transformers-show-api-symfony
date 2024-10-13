<?php

namespace App\Entity;

use App\Repository\BotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BotRepository::class)]
#[ORM\Table(name: '`bot`')]
class Bot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $transformation_count = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $alt_to_robot = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $robot_to_alt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $death_count = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $kill_count = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Entity $entity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Show $show = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?ScreenTime $screen_time = null;

    #[ORM\OneToMany(targetEntity: Membership::class, mappedBy: "bot")]
    private ?Collection $memberships;

    #[ORM\ManyToMany(targetEntity: Alt::class, mappedBy: "bots")]
    private ?Collection $alts;

    #[ORM\ManyToMany(targetEntity: VoiceActor::class, mappedBy: "bots")]
    private ?Collection $voice_actors;

    public function __construct()
    {
        $this->memberships = new ArrayCollection();
        $this->alts = new ArrayCollection();
        $this->voice_actors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getTransformationCount(): ?int
    {
        return $this->transformation_count;
    }

    public function setTransformationCount(?int $transformation_count): static
    {
        $this->transformation_count = $transformation_count;

        return $this;
    }

    public function getAltToRobot(): ?int
    {
        return $this->alt_to_robot;
    }

    public function setAltToRobot(?int $alt_to_robot): static
    {
        $this->alt_to_robot = $alt_to_robot;

        return $this;
    }

    public function getRobotToAlt(): ?int
    {
        return $this->robot_to_alt;
    }

    public function setRobotToAlt(?int $robot_to_alt): static
    {
        $this->robot_to_alt = $robot_to_alt;

        return $this;
    }

    public function getDeathCount(): ?int
    {
        return $this->death_count;
    }

    public function setDeathCount(?int $death_count): static
    {
        $this->death_count = $death_count;

        return $this;
    }

    public function getKillCount(): ?int
    {
        return $this->kill_count;
    }

    public function setKillCount(?int $kill_count): static
    {
        $this->kill_count = $kill_count;

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

    public function getShow(): ?Show
    {
        return $this->show;
    }

    public function setShow(?Show $show): static
    {
        $this->show = $show;

        return $this;
    }

    public function removeShow(): static
    {
        $this->show = null;

        return $this;
    }

    public function getScreenTime(): ?ScreenTime
    {
        return $this->screen_time;
    }

    public function setScreenTime(?ScreenTime $screen_time): static
    {
        $this->screen_time = $screen_time;

        return $this;
    }

    /**
     * @return Collection<int, Faction>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Membership $membership): static
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
        }

        return $this;
    }

    /**
     * @return Collection<int, Alt>
     */
    public function getAlts(): ?Collection
    {
        return $this->alts;
    }

    public function addAlt(Alt $alt): static
    {
        if (!$this->alts->contains($alt)) {
            $this->alts->add($alt);
        }

        return $this;
    }

    public function removeAlt(Alt $alt): static
    {
        $this->alts->removeElement($alt);

        return $this;
    }


    /**
     * @return Collection<int, VoiceActor>
     */
    public function getVoiceActors(): ?Collection
    {
        return $this->voice_actors;
    }

    public function addVoiceActor(VoiceActor $voice_actor): static
    {
        if (!$this->voice_actors->contains($voice_actor)) {
            $this->voice_actors->add($voice_actor);
            $voice_actor->addBot($this);
        }

        return $this;
    }

    public function removeVoiceActor(VoiceActor $voice_actor): static
    {
        $this->voice_actors->removeElement($voice_actor);

        return $this;
    }
}
