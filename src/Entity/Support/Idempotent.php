<?php

namespace App\Entity\Support;

use App\Common\Idempotent\IdempotentInterface;
use App\Repository\Support\IdempotentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IdempotentRepository::class)]
#[ORM\Table(name: 'support_idempotent')]
#[ORM\UniqueConstraint(columns: ['request_token', 'request_name'])]
class Idempotent implements IdempotentInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $requestToken = '';

    #[ORM\Column(length: 255)]
    private ?string $requestName = '';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $requestDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestToken(): ?string
    {
        return $this->requestToken;
    }

    public function setRequestToken(string $requestToken): self
    {
        $this->requestToken = $requestToken;

        return $this;
    }

    public function getRequestName(): ?string
    {
        return $this->requestName;
    }

    public function setRequestName(string $requestName): self
    {
        $this->requestName = $requestName;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeInterface $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function getTokenValueFieldName(): string
    {
        return 'requestToken';
    }

    public function getTokenNameFieldName(): string
    {
        return 'requestName';
    }

    public function getTokenDateFieldName(): string
    {
        return 'requestDate';
    }
}
