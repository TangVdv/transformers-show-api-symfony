<?php

namespace App\Entity;

use App\Repository\ShowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[UniqueEntity('show_name')]
#[ORM\Entity(repositoryClass: ShowRepository::class)]
#[ORM\Table(name: '`show`')]
class Show
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

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

    #[ORM\ManyToMany(targetEntity: Creator::class, mappedBy: "shows")]
    private ?Collection $creators = null;

    #[ORM\OneToMany(targetEntity: ScreenTime::class, mappedBy: "show")]
    private ?Collection $screen_times = null;

    #[ORM\OneToMany(targetEntity: ConceptArt::class, mappedBy: "show")]
    private ?Collection $concept_arts = null;

    #[ORM\OneToMany(targetEntity: VoiceLine::class, mappedBy: "show")]
    private ?Collection $voice_lines = null;

    private ?Collection $artefacts = null;
    private ?Collection $humans = null;
    private ?Collection $bots = null;

    public function __construct()
    {
        $this->type = 0;
        $this->creators = new ArrayCollection();
        $this->screen_times  = new ArrayCollection();
        $this->concept_arts  = new ArrayCollection();
        $this->voice_lines  = new ArrayCollection();
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

    /**
     * @return Collection<int, Creator>
     */
    public function getCreators(): ?Collection
    {
        return $this->creators;
    }

    public function addCreator(Creator $creator): static
    {
        if (!$this->creators->contains($creator)) {
            $this->creators->add($creator);
            $creator->addShow($this);
        }

        return $this;
    }

    public function removeCreator(Creator $creator): static
    {
        $this->creators->removeElement($creator);

        return $this;
    }

    public function getScreenTimes(): ?Collection
    {
        return $this->screen_times;
    }

    public function getArtefacts(): ?Collection
    {
        $this->artefacts  = new ArrayCollection();

        foreach($this->screen_times as $screen_time){
            foreach($screen_time->getArtefacts() as $artefact){
                $this->addArtefact($artefact);
            }
        } 

        return $this->artefacts;
    }

    public function addArtefact(Artefact $artefact): static
    {
        if (!$this->artefacts->contains($artefact)) {
            $this->artefacts->add($artefact);
        }

        return $this;
    }

    public function getHumans(): ?Collection
    {
        $this->humans  = new ArrayCollection();

        foreach($this->screen_times as $screen_time){
            foreach($screen_time->getHumans() as $human){
                $this->addHuman($human);
            }
        } 

        return $this->humans;
    }

    public function addHuman(Human $human): static
    {
        if (!$this->humans->contains($human)) {
            $this->humans->add($human);
        }

        return $this;
    }

    public function getBots(): ?Collection
    {
        $this->bots  = new ArrayCollection();

        foreach($this->screen_times as $screen_time){
            foreach($screen_time->getBots() as $bot){
                $this->addBot($bot);
            }
        } 

        return $this->bots;
    }

    public function addBot(Bot $bot): static
    {
        if (!$this->bots->contains($bot)) {
            $this->bots->add($bot);
        }

        return $this;
    }

    public function getConceptArts(): ?Collection
    {
        return $this->concept_arts;
    }

    public function getVoiceLines(): ?Collection
    {
        return $this->voice_lines;
    }
}
