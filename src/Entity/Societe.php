<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/** Informations légales de l'exploitant (une seule ligne), utilisées sur les PDF. */
#[ORM\Entity(repositoryClass: SocieteRepository::class)]
class Societe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $raisonSociale = 'SIMPLICI CAB';

    #[ORM\Column(length: 150)]
    private string $nomCommercial = 'ZenCAR';

    #[ORM\Column(length: 50)]
    private string $formeJuridique = 'SAS';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $capitalSocial = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $rcs = null;

    #[ORM\Column(length: 255)]
    private string $adresse = '12 Rue Antoine Re';

    #[ORM\Column(length: 10)]
    private string $codePostal = '13010';

    #[ORM\Column(length: 100)]
    private string $ville = 'Marseille';

    #[ORM\Column(length: 30)]
    private string $telephone = '06 74 03 96 94';

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    private string $siren = '982 953 382';

    #[ORM\Column(length: 25)]
    private string $siret = '982 953 382 00024';

    #[ORM\Column(length: 30)]
    private string $tvaIntra = 'FR18 982 953 382';

    #[ORM\Column(length: 10)]
    private string $codeNaf = '49.32Z';

    #[ORM\Column(length: 30)]
    private string $numeroRevtc = 'EVTC094240137';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int { return $this->id; }

    public function getRaisonSociale(): string { return $this->raisonSociale; }
    public function setRaisonSociale(string $v): static { $this->raisonSociale = $v; return $this; }

    public function getNomCommercial(): string { return $this->nomCommercial; }
    public function setNomCommercial(string $v): static { $this->nomCommercial = $v; return $this; }

    public function getFormeJuridique(): string { return $this->formeJuridique; }
    public function setFormeJuridique(string $v): static { $this->formeJuridique = $v; return $this; }

    public function getCapitalSocial(): ?string { return $this->capitalSocial; }
    public function setCapitalSocial(?string $v): static { $this->capitalSocial = $v; return $this; }

    public function getRcs(): ?string { return $this->rcs; }
    public function setRcs(?string $v): static { $this->rcs = $v; return $this; }

    public function getAdresse(): string { return $this->adresse; }
    public function setAdresse(string $v): static { $this->adresse = $v; return $this; }

    public function getCodePostal(): string { return $this->codePostal; }
    public function setCodePostal(string $v): static { $this->codePostal = $v; return $this; }

    public function getVille(): string { return $this->ville; }
    public function setVille(string $v): static { $this->ville = $v; return $this; }

    public function getTelephone(): string { return $this->telephone; }
    public function setTelephone(string $v): static { $this->telephone = $v; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $v): static { $this->email = $v; return $this; }

    public function getSiren(): string { return $this->siren; }
    public function setSiren(string $v): static { $this->siren = $v; return $this; }

    public function getSiret(): string { return $this->siret; }
    public function setSiret(string $v): static { $this->siret = $v; return $this; }

    public function getTvaIntra(): string { return $this->tvaIntra; }
    public function setTvaIntra(string $v): static { $this->tvaIntra = $v; return $this; }

    public function getCodeNaf(): string { return $this->codeNaf; }
    public function setCodeNaf(string $v): static { $this->codeNaf = $v; return $this; }

    public function getNumeroRevtc(): string { return $this->numeroRevtc; }
    public function setNumeroRevtc(string $v): static { $this->numeroRevtc = $v; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function touch(): static { $this->updatedAt = new \DateTimeImmutable(); return $this; }

    public function __toString(): string { return $this->raisonSociale; }
}
