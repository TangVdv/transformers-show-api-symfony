<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[ORM\Column(length: 100)]
    private ?string $uuid = null;

    
    #[ORM\Column(length: 16)]
    private ?string $username = null;

    
    #[ORM\Column(length: 255)]
    private ?string $email = null;


    #[ORM\Column(length: 255)]
    private ?string $password = null;

    
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $email_verified = null;

    
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $created_at = null;

    
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column]
    private ?int $current_request = null;

    #[ORM\Column]
    private ?int $max_request = null;

    public function __construct(){
        $this->email_verified = 0;
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
        $this->current_request = 0;
        $this->max_request = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmailVerified(): ?int
    {
        return $this->email_verified;
    }

    public function setEmailVerified(int $email_verified): static
    {
        $this->email_verified = $email_verified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCurrentRequest(): ?int
    {
        return $this->current_request;
    }

    public function setCurrentRequest(int $current_request): static
    {
        $this->current_request = $current_request;

        return $this;
    }

    public function getMaxRequest(): ?int
    {
        return $this->max_request;
    }

    public function setMaxRequest(int $max_request): static
    {
        $this->max_request = $max_request;

        return $this;
    }
}
