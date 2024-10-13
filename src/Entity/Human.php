<?php

namespace App\Entity;

use App\Repository\HumanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HumanRepository::class)]
#[ORM\Table(name: '`human`')]
class Human
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $image = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Entity $entity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Actor $actor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Show $show = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?ScreenTime $screen_time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(Entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    public function getActor(): ?Actor
    {
        return $this->actor;
    }

    public function setActor(Actor $actor): static
    {
        $this->actor = $actor;

        return $this;
    }

    public function getShow(): ?Show
    {
        return $this->show;
    }

    public function setShow(Show $show): static
    {
        $this->show = $show;

        return $this;
    }

    public function getScreenTime(): ?ScreenTime
    {
        return $this->screen_time;
    }

    public function setScreenTime(ScreenTime $screen_time): static
    {
        $this->screen_time = $screen_time;

        return $this;
    }
}
