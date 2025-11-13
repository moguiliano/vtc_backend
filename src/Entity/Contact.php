<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est requis.")]
    private string $name;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: "L'email est requis.")]
    #[Assert\Email(message: "Veuillez entrer une adresse email valide.")]
    private string $email;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est requis.")]
    #[Assert\Regex(
        pattern: "/^(\+?\d{1,4})?[\s\-]?\(?\d+\)?([\s\-]?\d+)+$/",
        message: "Veuillez entrer un numéro de téléphone valide."
    )]
    private string $phone;


    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Le message est requis.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Le message doit contenir au moins {{ limit }} caractères."
    )]
    private string $message;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
