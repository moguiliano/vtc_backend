<?php

namespace App\Entity;

/**
 * Une donnée qui porte l'étiquette « à qui c'est » (D-020).
 * Si elle est enregistrée sans entreprise, EntrepriseParDefautListener lui met l'entreprise courante.
 */
interface AppartientAEntreprise
{
    public function getEntreprise(): ?Entreprise;

    public function setEntreprise(Entreprise $entreprise): static;
}
