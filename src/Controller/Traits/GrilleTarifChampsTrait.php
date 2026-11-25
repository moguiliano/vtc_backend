<?php

namespace App\Controller\Traits;

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

/**
 * Colonnes de la grille de prix, communes à l'admin (« Grille de prix ») et à l'espace abonné
 * (« Mes prix ») : un seul endroit à modifier (D-093).
 * Le formulaire « Modifier » est rangé comme le tableau : même ordre, cases de même largeur,
 * prix au seuil affiché (calculé, D-090) et exemple de prix en direct.
 */
trait GrilleTarifChampsTrait
{
    protected function champsGrille(string $pageName): iterable
    {
        $formulaire = in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW], true);

        yield AssociationField::new('categorie', 'Véhicule')
            ->setColumns('col-md-4')
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT);

        yield BooleanField::new('actif', 'Proposé')
            ->setColumns('col-md-2');

        if ($formulaire) {
            yield FormField::addRow();
        }

        yield NumberField::new('thresholdKm', 'Seuil (km)')
            ->setNumDecimals(1)
            ->setColumns('col-md-2');

        yield NumberField::new('basePriceUnderThreshold', 'Prix base ≤ seuil (€)')
            ->setNumDecimals(2)
            ->setColumns('col-md-2');

        yield NumberField::new('pricePerKmUnderThreshold', '€/km ≤ seuil')
            ->setNumDecimals(2)
            ->setColumns('col-md-2');

        // D-090 : calculé automatiquement (prise en charge + seuil × €/km) ; grisé dans le formulaire
        yield NumberField::new('basePriceOverThreshold', 'Prix au seuil (calculé)')
            ->setNumDecimals(2)
            ->setColumns('col-md-2')
            ->setFormTypeOption('disabled', true)
            ->setRequired(false)
            ->hideWhenCreating();

        yield NumberField::new('pricePerKmOverThreshold', '€/km > seuil')
            ->setNumDecimals(2)
            ->setColumns('col-md-2');

        yield DateTimeField::new('updatedAt', 'Modifié le')->onlyOnDetail();
    }

    /** Recalcule le prix au seuil pendant la saisie et affiche un exemple de prix (D-090). */
    protected function assetsGrille(Assets $assets): Assets
    {
        return $assets->addHtmlContentToBody(<<<'HTML'
<script>
(function () {
  const champ = (p) => document.querySelector(`[name$="[${p}]"]`);
  const seuil = champ('thresholdKm'), pec = champ('basePriceUnderThreshold'), ks = champ('pricePerKmUnderThreshold'),
        kr = champ('pricePerKmOverThreshold'), auSeuil = champ('basePriceOverThreshold');
  if (!seuil || !pec || !ks || !kr) return;
  const n = (el) => parseFloat(String(el.value).replace(',', '.').replace(/\s/g, '')) || 0;
  const eur = (x) => x.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
  const exemple = document.createElement('div');
  exemple.className = 'col-12 form-text mb-3';
  (kr.closest('[class*="col-md-"]') || kr.closest('.form-group') || kr.parentElement).after(exemple);
  const maj = () => {
    const S = n(seuil), P = n(pec), A = n(ks), B = n(kr), auS = P + S * A;
    const prix = (d) => d <= S ? P + d * A : auS + (d - S) * B;
    const d1 = Math.max(1, Math.round(S * 0.8)), d2 = Math.round(S + 15);
    if (auSeuil) auSeuil.value = auS.toFixed(2);
    exemple.textContent = `Exemple de jour : ${d1} km = ${eur(prix(d1))} · ${d2} km = ${eur(prix(d2))}`;
  };
  [seuil, pec, ks, kr].forEach((el) => el.addEventListener('input', maj));
  maj();
})();
</script>
HTML);
    }
}
