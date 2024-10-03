<?php

namespace App\Entity;

use App\Repository\ShowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[UniqueEntity('show_name')]
#[ORM\Entity(repositoryClass: ShowRepository::class)]
#[ORM\Table(name: '`show`')]
class Show
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    private ?string $uuid = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $show_name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull()]
    private ?int $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull()]
    private ?\DateTimeInterface $release_date = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $running_time = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $budget = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull()]
    private ?int $box_office = null;

    private ?string $director = null;
    private ?array $producer = null;
    private ?string $writer = null;
    private ?string $composer = null;

    private ?array $bot = null;
    private ?array $human = null;
    private ?array $concept_art = null;
    private ?array $voice_line = null;
    private ?array $artefact = null;

    public function __construct()
    {
        $this->type = 0;
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getShowName(): ?string
    {
        return $this->show_name;
    }

    public function setShowName(string $show_name): static
    {
        $this->show_name = $show_name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(string $release_date): static
    {
        $this->release_date = new DateTimeImmutable($release_date);

        return $this;
    }

    public function getRunningTime(): ?int
    {
        return $this->running_time;
    }

    public function setRunningTime(int $running_time): static
    {
        $this->running_time = $running_time;

        return $this;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(int $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getBoxOffice(): ?int
    {
        return $this->box_office;
    }

    public function setBoxOffice(int $box_office): static
    {
        $this->box_office = $box_office;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): static
    {
        $this->director = $director;

        return $this;
    }

    public function getProducer(): ?array
    {
        return $this->producer;
    }

    public function setProducer(array $producer): static
    {
        $this->producer = $producer;

        return $this;
    }

    public function getWriter(): ?string
    {
        return $this->writer;
    }

    public function setWriter(string $writer): static
    {
        $this->writer = $writer;

        return $this;
    }

    public function getComposer(): ?string
    {
        return $this->composer;
    }

    public function setComposer(string $composer): static
    {
        $this->composer = $composer;

        return $this;
    }

    public function getBot(): ?array
    {
        return $this->bot;
    }

    public function setBot(array $bot): static
    {
        $this->bot = $bot;

        return $this;
    }

    public function getHuman(): ?array
    {
        return $this->human;
    }

    public function setHuman(array $human): static
    {
        $this->human = $human;

        return $this;
    }

    public function getConceptArt(): ?array
    {
        return $this->concept_art;
    }

    public function setConceptArt(array $concept_art): static
    {
        $this->concept_art = $concept_art;

        return $this;
    }

    public function getVoiceLine(): ?array
    {
        return $this->voice_line;
    }

    public function setVoiceLine(array $voice_line): static
    {
        $this->voice_line = $voice_line;

        return $this;
    }

    public function getArtefact(): ?array
    {
        return $this->artefact;
    }

    public function setArtefact(array $artefact): static
    {
        $this->artefact = $artefact;

        return $this;
    }
}
