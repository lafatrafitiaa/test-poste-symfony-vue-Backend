<?php

namespace App\Entity;

use App\Repository\FriendsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendsRepository::class)]
class Friends
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $myId = null;

    #[ORM\Column]
    private ?int $firendId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateRequest = null;

    #[ORM\Column]
    private ?bool $isAccepted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyId(): ?int
    {
        return $this->myId;
    }

    public function setMyId(?int $myId): static
    {
        $this->myId = $myId;

        return $this;
    }

    public function getFirendId(): ?int
    {
        return $this->firendId;
    }

    public function setFirendId(int $firendId): static
    {
        $this->firendId = $firendId;

        return $this;
    }

    public function getDateRequest(): ?\DateTimeInterface
    {
        return $this->dateRequest;
    }

    public function setDateRequest(\DateTimeInterface $dateRequest): static
    {
        $this->dateRequest = $dateRequest;

        return $this;
    }

    public function isIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(bool $isAccepted): static
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }
}
