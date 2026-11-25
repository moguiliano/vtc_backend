<?php

namespace App\Entity;

use App\Repository\CompteAbonneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Compte de connexion d'un abonné (D-013 : 1 compte par abonné, D-074 : inscription publique).
 *
 * Séparé de `Admin` exprès (D-078, verrou n°1) : un compte abonné ne peut se connecter
 * que sur /espace, et un administrateur que sur /admin.
 * N'implémente PAS AppartientAEntreprise : le compte est rechargé à chaque page
 * avant que le tri automatique par entreprise soit activé.
 */
#[ORM\Entity(repositoryClass: CompteAbonneRepository::class)]
#[ORM\Table(name: 'compte_abonne')]
#[ORM\UniqueConstraint(name: 'uniq_compte_abonne_email', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Un espace existe déjà avec cet e-mail.')]
class CompteAbonne implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    #[ORM\Column(length: 180)]
    private string $email = '';

    #[ORM\Column]
    private string $password = '';

    /** Téléphone au format E.164 (PhoneNormalizerService). */
    #[ORM\Column(length: 20)]
    private string $telephone = '';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getEntreprise(): ?Entreprise { return $this->entreprise; }
    public function setEntreprise(Entreprise $entreprise): static { $this->entreprise = $entreprise; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = mb_strtolower(trim($email)); return $this; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $telephone): static { $this->telephone = $telephone; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getUserIdentifier(): string { return $this->email; }

    /** Toujours et seulement ROLE_ABONNE : jamais de droit sur /admin. */
    public function getRoles(): array { return ['ROLE_ABONNE']; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $hash): static { $this->password = $hash; return $this; }

    public function eraseCredentials(): void {}
}
