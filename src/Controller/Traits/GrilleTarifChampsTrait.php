<?php

namespace App\Controller\Traits;

use App\Entity\GrilleTarif;
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
 * Titres en langage simple et une phrase sous chaque case (D-094, D-095) ; formulaire rangé comme
 * le tableau ; prix à la fin de la distance courte calculé (D-090) ; simulateur de prix (D-095).
 * Les noms internes (thresholdKm…) ne changent pas : seul l'affichage change.
 */
trait GrilleTarifChampsTrait
{
    protected function champsGrille(string $pageName): iterable
    {
        $formulaire = in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW], true);

        yield AssociationField::new('categorie', 'Véhicule')
            ->setColumns('col-md-4')
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT)
            ->setHelp('Catégorie commune à tous, gérée par ZenCAR.');

        yield BooleanField::new('actif', 'Proposé')
            ->setColumns('col-md-3')
            ->setHelp('Vous proposez cette gamme de véhicule à vos clients.');

        if ($formulaire) {
            yield FormField::addRow();
        }

        yield NumberField::new('thresholdKm', 'Distance courte jusqu\'à (km)')
            ->setNumDecimals(1)
            ->setColumns('col-md-2')
            ->setHelp('Au-delà, on passe au tarif longue distance.');

        yield NumberField::new('basePriceUnderThreshold', 'Prise en charge (€)')
            ->setNumDecimals(2)
            ->setColumns('col-md-2')
            ->setHelp('Payée dès que le client monte.');

        yield NumberField::new('pricePerKmUnderThreshold', 'Prix au km, courte distance (€)')
            ->setNumDecimals(2)
            ->setColumns('col-md-2')
            ->setHelp('Ajouté pour chaque km jusqu\'à la distance courte.');

        // D-090 : calculé automatiquement (prise en charge + distance courte × prix au km) ; grisé dans le formulaire
        yield NumberField::new('basePriceOverThreshold', 'Prix à la fin de la distance courte (€)')
            ->setNumDecimals(2)
            ->setColumns('col-md-2')
            ->setFormTypeOption('disabled', true)
            ->setRequired(false)
            ->hideWhenCreating()
            ->setHelp('Calculé tout seul.');

        yield NumberField::new('pricePerKmOverThreshold', 'Prix au km, longue distance (€)')
            ->setNumDecimals(2)
            ->setColumns('col-md-2')
            ->setHelp('Ajouté pour chaque km au-delà, souvent plus bas.');

        yield DateTimeField::new('updatedAt', 'Modifié le')->onlyOnDetail();
    }

    /**
     * Simulateur de prix (D-095), sans carte ni appel HERE Maps :
     * - sous le tableau : prix de tous les véhicules proposés pour une distance, de jour ou de nuit ;
     * - sous le formulaire « Modifier » : prix du véhicule en cours, recalculé pendant la saisie.
     * Même calcul que le service Tarification (prix brut, puis majoration de nuit arrondie au centime).
     *
     * @param GrilleTarif[] $grilles grilles de l'entreprise affichée
     */
    protected function assetsGrille(Assets $assets, array $grilles, float $nuitPourcent): Assets
    {
        $lignes = [];
        foreach ($grilles as $g) {
            if ($g->isActif() && $g->getCategorie()?->isActive()) {
                [$seuil, $pec, $kmSous, $kmSur] = $g->prix();
                $lignes[] = ['nom' => $g->getCategorie()->getLabel(), 's' => $seuil, 'p' => $pec, 'a' => $kmSous, 'b' => $kmSur];
            }
        }
        $donnees = json_encode(['nuit' => $nuitPourcent, 'lignes' => $lignes], JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP);

        // Titres longs (D-095) : ils passent à la ligne au lieu d'élargir le tableau
        $assets = $assets->addHtmlContentToHead('<style>table.datagrid thead th, table.datagrid thead th a { white-space: normal; vertical-align: bottom; }</style>');

        return $assets->addHtmlContentToBody(<<<HTML
<script>
(function () {
  const DONNEES = {$donnees};
  const n = (v) => parseFloat(String(v).replace(',', '.').replace(/\s/g, '')) || 0;
  const eur = (x) => x.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
  const prixBrut = (l, d) => d <= l.s ? l.p + d * l.a : (l.p + l.s * l.a) + (d - l.s) * l.b;
  const prix = (l, d, nuit) => { const p = prixBrut(l, d); return nuit && DONNEES.nuit > 0 ? Math.round(p * (1 + DONNEES.nuit / 100) * 100) / 100 : Math.round(p * 100) / 100; };
  const libelleNuit = DONNEES.nuit > 0 ? `de nuit (23 h – 7 h, +\${DONNEES.nuit.toLocaleString('fr-FR')} %)` : 'de nuit (pas de majoration)';

  function carte(titre) {
    const c = document.createElement('div');
    c.className = 'card mt-4';
    c.innerHTML = `<div class="card-body">
      <h6 class="card-title mb-2"><i class="fa fa-calculator"></i> \${titre}</h6>
      <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
        <label class="d-flex align-items-center gap-2 mb-0">Distance
          <input type="number" min="0" step="0.5" value="25" class="form-control form-control-sm" style="width:90px" data-sim-km> km</label>
        <label class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" data-sim-nuit> <span class="form-check-label">\${libelleNuit}</span></label>
      </div>
      <div data-sim-resultat></div>
      <div class="form-text">Calcul direct depuis la grille, sans passer par la carte.</div></div>`;
    return c;
  }

  // 1) Formulaire « Modifier » : prix à la fin de la distance courte + simulateur du véhicule en cours
  const champ = (p) => document.querySelector(`[name\$="[\${p}]"]`);
  const seuil = champ('thresholdKm'), pec = champ('basePriceUnderThreshold'), ks = champ('pricePerKmUnderThreshold'),
        kr = champ('pricePerKmOverThreshold'), auSeuil = champ('basePriceOverThreshold');
  if (seuil && pec && ks && kr) {
    const c = carte('Simulateur de prix pour ce véhicule');
    (kr.closest('form') || document.querySelector('#main')).after(c);
    const maj = () => {
      const l = { s: n(seuil.value), p: n(pec.value), a: n(ks.value), b: n(kr.value) };
      if (auSeuil) auSeuil.value = (l.p + l.s * l.a).toFixed(2);
      const d = n(c.querySelector('[data-sim-km]').value), nuit = c.querySelector('[data-sim-nuit]').checked;
      c.querySelector('[data-sim-resultat]').innerHTML = `\${d.toLocaleString('fr-FR')} km \${nuit ? 'de nuit' : 'de jour'} : <strong class="fs-5">\${eur(prix(l, d, nuit))}</strong>`;
    };
    [seuil, pec, ks, kr, c.querySelector('[data-sim-km]'), c.querySelector('[data-sim-nuit]')].forEach((el) => el.addEventListener('input', maj));
    c.querySelector('[data-sim-nuit]').addEventListener('change', maj);
    maj();
    return;
  }

  // 2) Tableau : prix de tous les véhicules proposés
  const table = document.querySelector('table.datagrid');
  if (!table) return;
  const c = carte('Simulateur de prix');
  (table.closest('.content-panel') || table.parentElement).after(c);
  const maj = () => {
    const d = n(c.querySelector('[data-sim-km]').value), nuit = c.querySelector('[data-sim-nuit]').checked;
    c.querySelector('[data-sim-resultat]').innerHTML = DONNEES.lignes.length
      ? '<div class="d-flex flex-wrap gap-2">' + DONNEES.lignes.map((l) => `<span class="badge bg-light text-dark border fs-6 fw-normal">\${l.nom} : <strong>\${eur(prix(l, d, nuit))}</strong></span>`).join('') + '</div>'
      : '<span class="text-muted">Aucun véhicule proposé : activez « Proposé » sur au moins un véhicule.</span>';
  };
  c.querySelector('[data-sim-km]').addEventListener('input', maj);
  c.querySelector('[data-sim-nuit]').addEventListener('change', maj);
  maj();
})();
</script>
HTML);
    }
}
