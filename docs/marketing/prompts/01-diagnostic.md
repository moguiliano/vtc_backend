# 01 — Diagnostic

> Objectif : constater et noter, **sans encore proposer de solutions**. Séparer le constat de la solution évite les conseils plaqués.

## Données à joindre
- [ ] Search Console, 3 derniers mois : export « Requêtes » et « Pages » (CSV ou captures)
- [ ] Google Ads, 90 jours : captures campagnes + mots-clés + termes de recherche
- [ ] Fiche Google : capture (note, nombre d'avis, catégories) + 20 avis copiés tels quels, négatifs compris
- [ ] Captures mobile de l'accueil de dzencar.fr et du formulaire de réservation (les 3 étapes)
- [ ] Captures des résultats Google pour « taxi marseille », « vtc marseille », « taxi aéroport marseille »
- [ ] Chiffres du mois : appels, WhatsApp, réservations en ligne, courses, CA

## Prompt

```
# ÉTAPE 1 — DIAGNOSTIC

Données jointes : [liste ce que tu joins].

Applique la méthode du contexte, dans cet ordre, sans sauter de phase.

PHASE A — INVENTAIRE des données jointes (méthode 2, points 1 à 3) : fichier par fichier, période, lignes, contrôles, contradictions avec le registre. Ne commence aucune analyse.

PHASE B — VALIDATION :
- Liste toutes les données du registre ⚠️, ❓ ou contradictoires, les points V-01 à V-10 et les anomalies trouvées en phase A. Pour chacune : bloquante ou non pour le diagnostic, et pourquoi.
- Pose-les UNE PAR UNE, en commençant par les bloquantes (méthode 1). Ne passe pas à la suivante tant que la question n'est pas tranchée.
- Pour chaque point V-XX : CONFIRMÉ / INFIRMÉ, d'après ma réponse ou la donnée qui le prouve.

PHASE C — DIAGNOSTIC, seulement quand toutes les questions bloquantes sont tranchées. Commence par : « Questions bloquantes tranchées, je commence le diagnostic. »
Règle stricte : à cette étape, tu CONSTATES, tu ne proposes PAS de solution. Tu n'utilises que des données ✅ ou des données jointes contrôlées.

1. Parcours client, étape par étape : recherche Google → résultat (annonce, fiche, site) → page d'arrivée → contact (appel, WhatsApp, formulaire) → course → après la course. Pour chaque étape : ce qui fonctionne, ce qui fait fuir, avec la preuve (donnée ou capture).

2. Note sur 10, avec justification en 2 lignes, pour :
   positionnement et offre · message et confiance · site et conversion mobile · SEO local et fiche Google · Google Ads · suivi des conversions · avis et réputation · fidélisation · clients professionnels · conformité (taxi/VTC, avis, RGPD).

3. Les points V-01 à V-10 : résultat (CONFIRMÉ / INFIRMÉ) et preuve, tels que tranchés en phase B.

4. Nouveaux problèmes trouvés dans mes données (pas des problèmes génériques), chacun avec sa source.

5. Comparaison avec les 3 concurrents les plus visibles sur mes captures Google : ce qu'ils font mieux, ce qu'on fait mieux.

6. Note globale sur 100.

7. Ce que tu n'as pas pu évaluer, et la donnée qu'il te faudrait.

AUTO-CRITIQUE : relis ton diagnostic. Retire tout constat qui pourrait s'appliquer à n'importe quel taxi en France sans lien avec mes données. Vérifie que chaque chiffre a sa source et qu'aucun ne repose sur une donnée ⚠️ ou ❓.

FIN DE L'ÉTAPE : questions restées ouvertes, puis le bloc « REGISTRE MIS À JOUR ».
```
