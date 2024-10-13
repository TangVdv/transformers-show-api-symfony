<?php

namespace App\Entity;

use App\Repository\VoiceLineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoiceLineRepository::class)]
#[ORM\Table(name: '`voice_line`')]
class VoiceLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    private ?string $content = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?int $number = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Entity $entity = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Show $show = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

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

    public function getShow(): ?Show
    {
        return $this->show;
    }

    public function setShow(Show $show): static
    {
        $this->show = $show;

        return $this;
    }
}
