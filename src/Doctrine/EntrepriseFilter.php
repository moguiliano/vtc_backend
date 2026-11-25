<?php

namespace App\Doctrine;

use App\Entity\AppartientAEntreprise;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Verrou n°2 du cloisonnement (D-078) : « seulement les données de l'entreprise X ».
 *
 * Activé à chaque page par CloisonnementEntrepriseListener, avec X = EntrepriseCourante :
 * SIMPLICI CAB sur le site public et dans /admin, l'abonné connecté dans /espace.
 * Il s'applique à toutes les lectures Doctrine (listes, find(), chargement des liens) des
 * entités qui portent l'étiquette entreprise : une donnée d'une autre entreprise devient introuvable.
 */
class EntrepriseFilter extends SQLFilter
{
    public const NOM = 'entreprise';

    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if (!$targetEntity->reflClass?->implementsInterface(AppartientAEntreprise::class)) {
            return '';
        }

        $colonne = $targetEntity->getSingleAssociationJoinColumnName('entreprise');

        return sprintf('%s.%s = %s', $targetTableAlias, $colonne, $this->getParameter('entreprise'));
    }
}
