<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "verification_code")]
#[ORM\Index(columns: ["phone_number"], name: "idx_verifcode_phone")]
#[ORM\Index(columns: ["phone_number", "code", "is_verified"], name: "idx_verifcode_check")]
class VerificationCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: "phone_number", type: "string", length: 32)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: "string", length: 6)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: "is_verified", type: "boolean")]
    private bool $isVerified = false;

    public function getId(): ?int { return $this->id; }
    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(string $phoneNumber): self { $this->phoneNumber = $phoneNumber; return $this; }
    public function getCode(): ?string { return $this->code; }
    public function setCode(string $code): self { $this->code = $code; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): self { $this->isVerified = $isVerified; return $this; }
}
