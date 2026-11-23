<?php

namespace App\Entity\Traits;

use App\Entity\Entreprise;
use Doctrine\ORM\Mapping as ORM;

/** Colonne `entreprise_id` obligatoire : l'étiquette « à qui c'est » (D-020). */
trait EntrepriseTrait
{
    #[ORM\ManyToOne(targetEntity: Entreprise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    public function getEntreprise(): ?Entreprise { return $this->entreprise; }

    public function setEntreprise(Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;
        return $this;
    }
}
