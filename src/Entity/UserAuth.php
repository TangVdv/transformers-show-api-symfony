<?php

namespace App\Entity;

use App\Repository\UserAuthRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAuthRepository::class)]
class UserAuth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $issued_at = null;

    #[ORM\Column]
    private ?int $expires_in = null;

    #[ORM\Column(length: 45)]
    private ?string $token_type = null;

    #[ORM\Column(length: 255)]
    private ?string $access_token = null;

    #[ORM\Column]
    private ?int $User_id = null;

    public function __construct(){
        $this->issued_at = time();
        $this->expires_in = 3000;
        $this->token_type = "Bearer";
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

    public function getIssuedAt(): ?int
    {
        return $this->issued_at;
    }

    public function setIssuedAt(int $issued_at): static
    {
        $this->issued_at = $issued_at;

        return $this;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expires_in;
    }

    public function setExpiresIn(int $expires_in): static
    {
        $this->expires_in = $expires_in;

        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->token_type;
    }

    public function setTokenType(string $token_type): static
    {
        $this->token_type = $token_type;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(string $access_token): static
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->User_id;
    }

    public function setUserId(int $User_id): static
    {
        $this->User_id = $User_id;

        return $this;
    }
}
