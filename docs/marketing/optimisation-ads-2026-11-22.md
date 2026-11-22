# Optimisation Google Ads — 2026-11-22

> Compte 522-918-7346, campagne « Google Search-SIte Web Zencar CL » (id 23139442069).
> Objectif : le coût le plus bas **par appel utile et par course**, sans perdre de clients.
> Période analysée : 21 nov. 2025 → 21 nov. 2026, **jours actifs uniquement** (D-042).
> Aucune IP ni aucun numéro de téléphone dans ce fichier (données personnelles).

## 1. Calendrier de la campagne (12 mois)

Une seule campagne a dépensé sur l'année (vérifié en incluant les campagnes supprimées).
155 jours avec au moins une impression, **137 jours actifs retenus** : la période à 0,50 €/jour
(22 sept. → 9 oct.) est exclue, car c'était presque un arrêt (D-042).

| Période | Ce qui s'est passé | Budget/jour | Jours | Coût | Conv. | Coût/conv. |
|---|---|---|---|---|---|---|
| P1 21 nov. → 14 déc. 2025 | En marche | 7,70 → 10 → 20 → 19 € | 24 | 325 € | 38 | 8,56 € |
| *15 → 18 déc.* | Pause | | | | | |
| P2 19 déc. → 23 janv. | En marche, avec des coupures | 19 → 7,90 → 12 → 20 € | 23 | 307 € | 25 | 12,28 € |
| *24 janv. → 1er févr.* | Pause | | | | | |
| P3 2 → 12 févr. | En marche | 20 → 7 € | 11 | 138 € | 7 | 19,67 € |
| *13 févr. → 8 avr.* | Pause (55 j) | | | | | |
| P4 9 avr. → 6 juin | En marche, petites pauses quotidiennes | 10 € le jour, 1 € la nuit | 50 | 312 € | 67 | **4,66 €** |
| *7 juin → 16 sept.* | Pause (été, 102 j) | | | | | |
| P5 17 → 21 sept. | En marche | 10 € | 5 | 14 € | 0 | — |
| ~~P6 22 sept. → 9 oct.~~ | Exclue : budget 0,50 €, CPA cible 0,10 € | 0,50 € | (18) | (12 €) | 0 | — |
| P7 10 → 17 oct. | En marche | 10 → 5 → 12 € | 5 | 30 € | 1 | 29,53 € |
| *18 oct. → 1er nov.* | Pause | | | | | |
| P8 2 → 15 nov. | Liste « ctr faible » bloquante | 12 € | 13 | 25 € | 4 | 6,19 € |
| P9 16 → 21 nov. | Refonte du 15 nov. | 12 → 14,40 € | 6 | 73 € | 10 | 7,28 € |

**Sur l'année (137 jours actifs)** : 1 224 €, 740 clics, 193 appels depuis l'annonce, 152 conversions.
En moyenne par jour actif : 8,93 €, 5,4 clics, CPC 1,65 €, 1,4 appel, 1,1 conversion.
**Coût par appel : 6,34 €. Coût par conversion : 8,05 €.**

⚠️ Les conversions n'ont pas compté la même chose toute l'année : en mai, il y a surtout des clics sur le numéro du site
(45 conv. pour 11 appels depuis l'annonce). Il faut comparer les mois en regardant les appels **et** les conversions.

## 2. IP, appels, clics non valides (étape 3)

- **Journaux Infomaniak** (`~/ik-logs`, SSH) : ils couvrent du 14 juil. au 22 nov. 2026, soit 38 jours actifs. La durée
  de conservation annoncée par Infomaniak est d'au moins 7 jours : ne pas compter sur plus.
- Sur cette période, **89 clics** sont arrivés sur le site (gclid différents). 26 adresses appartiennent à **Google**
  (AdsBot, préchargement) : à ne jamais exclure. 6 personnes ont cliqué 2 ou 3 fois **dans la même heure**, aucune
  ne revient un autre jour : ce sont des clients qui comparent, pas des concurrents. Un bloc d'adresses voisines
  (réseau d'entreprise ou d'opérateur) a cliqué 4 fois, toujours vers 13 h.
- Coût des clics en double : ≈ 15 € sur 38 jours actifs, soit **≈ 2 à 3 € par semaine**.
- **Décision : aucune exclusion d'IP** ; on relance la commande SSH après 14 jours actifs (D-043).
- **Appels** (rapport « Détails de l'appel ») : 195 appels, 178 décrochés, 15 manqués. Le numéro de l'appelant n'est
  visible que depuis fin sept. 2026. Aucun numéro n'appelle souvent avec des appels très courts ; le seul appelant
  fréquent (5 appels du 18 au 20 nov.) est un vrai client. Les appels de moins de 10 s ont presque disparu depuis le 16 nov.
- **Clics non valides** : 58 sur 740 (7,8 %), retirés de la facture par Google ; 18,2 % sur la semaine du 16 nov.
  (pic du 18 nov.). **Pas d'enquête pour l'instant** (D-044).

## 3. Audit (étape 4) : ce qu'on retient

| Constat | Chiffre |
|---|---|
| Mois rentables | avr.-mai (≈ 4,70 €/conv., CPC ≈ 1,40 €), fin nov.-déc. (≈ 4-5 €/appel) |
| Mois chers | janv.-févr. (CPC 2,17 → 2,99 €, 14,50 → 19,70 €/conv.) |
| Été, croisières | **Aucune donnée** (campagne à l'arrêt) |
| Recherches « concurrents » (uber, bolt…) | 334 €, 7,95 €/conv. (dans la moyenne) : à garder |
| Recherches « numéro / téléphone » | 5,13 €/conv. : les plus rentables |
| Recherches d'autres villes | 75 €, 14,92 €/conv. |
| Types de correspondance | exact 5,91 € · large 8,36 € (79 % de la dépense) · expression 9,05 € |
| Heures | nuit 0 h-5 h **3,45 €/conv.** (coupée depuis le 15 nov.) · 13 h 21 € · 19 h 14 € |
| Jours | lundi le plus cher (11,54 €), mardi et dimanche les moins chers |
| Appareils / réseau | 100 % mobile, 100 % Recherche Google |
| Zones | Marseille 7,26 € · 13e 4,47 € · Aix 9,37 € · **Marignane 17,54 €** · hors zone ≈ 230 € à ≈ 11 € |
| Niveau de qualité | **non relevé** (le rapport ne l'a pas donné) |

Le gaspillage vient surtout des **réglages qui bougent sans arrêt** (≈ 190 € en janv.-févr., plus 2 mois presque sans
diffusion) et du **hors zone** (≈ 70 €), pas des IP.

## 4. Recherches de niche (étape 5, Planificateur, volumes France)

| Recherche | Volume/mois | CPC estimé | Page |
|---|---|---|---|
| numero taxi marseille | 320 | 0,51 – 1,53 € | Accueil |
| taxi saint charles marseille | 260 | 0,46 – 2,20 € | /taxi-gare-saint-charles |
| taxi gare saint charles | 110 | 0,47 – 2,14 € | /taxi-gare-saint-charles |
| vtc gare saint charles | 30 (hausse) | 0,41 – 2,01 € | /taxi-gare-saint-charles (forfait VTC) |
| taxi marseille 13008 | 110 | 0,53 – 1,52 € | /taxi-marseille |
| taxi aeroport marseille | 1 300 | 0,68 – 2,55 € | /taxi-aeroport-marseille |
| vtc aeroport marseille | 210 (hausse) | 0,62 – 2,03 € | /taxi-aeroport-marseille (forfait VTC) |
| taxi vitrolles | 260 | 0,48 – 2,09 € | pas de page dédiée |
| taxi gare tgv aix en provence | 170 | 0,40 – 8,73 € | pas de page dédiée |

Hôpitaux, Vélodrome, croisières, Joliette, Pointe-Rouge, seniors : volume non mesurable, à ne pas acheter (à citer dans
les annonces et les pages). Le Planificateur a enregistré un plan « Plan créé le nov. 22 » dans le compte (sans effet).

## 5. État avant → actions faites (étape 7, 2026-11-22)

| # | Action | Avant | Après | Accord |
|---|---|---|---|---|
| A | Règle « ZenCAR - Accélérer budget » | Active (+20 %/lundi, plafond 20 €) | En veille | Autorisée d'avance |
| A | Budget quotidien | 14,40 € | **12,00 €** | Autorisée d'avance |
| A | Recommandations auto liées au budget | Aucune active | Rien à couper | — |
| B | Zones | 12 (Aix déjà présente), option « Présence » | **13 (+ Vitrolles)**, « Présence » | Autorisée d'avance |
| 2 | Règles « Freiner budget » (×2) | Actives | En veille | « Oui, les 2 » (D-045) |
| 4 | Mots-clés à exclure (campagne, expression) | 172 | 179 : + manosque, nice, nimes, nîmes, pelissanne, central taxi, location de voiture | « Oui, la liste » (D-046) |
| 4 bis | Exclusion « vitrolles » (campagne) | Présente | Retirée (178) | « Oui, la retirer » (D-046) |
| 5 | Recommandations appliquées automatiquement | 2 (requête large, mots-clés redondants) | **0** | « B : les deux » (D-047) |

Notes :
- D'où venaient les 14,40 € : la règle « Accélérer » le 16 nov., puis une recommandation « Augmentation de budget »
  appliquée à la main le 19 nov. à 20 h 15, juste après une remise à 12 €.
- Pas de budget partagé. Le « Total compte 19,40 € » additionne des budgets de campagnes en veille, qui ne dépensent rien.
- Un simulateur Google proposant 16,91 €/jour a été ouvert puis **annulé** sans rien appliquer.

**Programmé** : test 6 (mots-clés exacts « numéro ») **après le 29 nov.** (D-048), élargi aux variantes « numéro » (D-054, voir section 7).
**Reporté à « À trancher »** : tests 7, 8 et 9 (D-049, D-050, D-051).

## 6. Chiffres à revérifier

| Quand | Quoi | Seuil d'alerte |
|---|---|---|
| **J+7 actifs** (≈ 29 nov.) | Coût par appel, coût par conversion, appels décrochés / manqués, part de la dépense hors zone, impressions perdues (budget) | Coût/appel > 8 € ; impressions perdues (budget) > 10 % ; 30 € sans appel |
| **J+14 actifs** (≈ 6 déc.) | Les mêmes, plus : clics non valides (%), relance de la commande SSH « gclid » (une même IP sur plusieurs jours ?) | Clics non valides > 15 % → demander une enquête ; une IP sur plusieurs jours → proposer l'exclusion |
| Après le 29 nov. | Lancer le test 6 avec ton « oui » le jour J | — |

Rappel : les 3 règles de budget sont en veille. Plus rien ne freine tout seul, c'est le bilan J+7 qui sert de frein.

## 7. Mots-clés de niche (Planificateur « nouvelles idées », 22 nov.)

492 idées relevées puis filtrées : taxi / VTC / chauffeur, pas de ville hors zone, concurrence faible ou moyenne,
CPC haut < 2,60 €, absentes du compte. Volumes France entière (le volume réel en zone « Présence » sera plus petit).
**Une famille à la fois, en exact, après le 29 nov., avec un « oui » le jour J.**

| Ordre | Famille | Mots-clés (exact) | Volume/mois | CPC estimé |
|---|---|---|---|---|
| 1 ✅ (D-054, test 6) | « numéro » | [numero taxi marseille], [taxis marseille numéro], [taxi marseille telephone], [taxi urgent], [numéro taxi], [taxi numéro], [numéro taxis], [numero des taxi], [taxi marseillais numéro], [numéro téléphone taxi], [taxi numero telephone], [numéro d un taxi], [numéro du taxi] | 880 + 390 + 50 + 40… | 0,51 – 2,58 € |
| à choisir | Alternative à Uber | [uber marseille prix], [prix uber marseille], [tarif uber marseille], [contact uber marseille], [uber marseille aeroport], [prix uber marseille aeroport], [uber taxi marseille gare] | 20 – 70 chacun | **0,05 – 1,02 €** |
| à choisir | Prix VTC aéroport / gare / croisière | [prix taxi marseille], [tarif taxi marseille aéroport], [prix taxi aéroport marseille port croisière], [prix taxi aéroport marseille vieux port], [prix taxi marignane marseille], [prix taxi aix tgv centre ville] | 20 – 170 | 0,15 – 1,43 € |
| à choisir | Aix et VTC | [taxi aix], [tarif taxi aix en provence] (bloqués par D-053), [taxi vtc marseille], [reserver vtc marseille], [chauffeur privé vtc marseille], [chauffeur aéroport marseille] | 50 – 480 | 0,36 – 2,01 € |

Règle métier : pour la famille « prix », l'annonce n'affiche **que le forfait VTC** (25 € gare, 59 € aéroport), jamais un prix de taxi.
Écartés : « taxi g7 » (national), conventionné / VSL (non proposé), « 24/24 », « pas cher », noms d'autres compagnies,
autres villes ; « taxi pmr marseille » seulement si le van accueille un fauteuil roulant (à confirmer).
Le Planificateur a enregistré deux brouillons de plan dans le compte (sans effet sur la campagne).

## 8. Famille « seniors » (D-055 : 2e test, après la famille « numéro »)

Page : `/transport-seniors-marseille` (porte à porte, aide à monter, rendez-vous médicaux, EHPAD, réservation par
téléphone ; **pas de transport conventionné**). Nouveau groupe « E - Seniors », annonce dédiée, sans prix taxi ni
promesse de remboursement. Mots-clés en **expression exacte** (volume trop faible pour l'exact).

| Mot-clé (expression) | Volume/mois (France) | CPC estimé | Concurrence |
|---|---|---|---|
| "taxi pour personnes âgées" · "taxi personne âgée" | 90 | 0,31 – 1,12 € | Faible |
| "taxi personnes âgées" | 30 | 0,47 – 1,03 € | Faible |
| "taxi senior" | 20 | 0,38 – 2,18 € | Faible |
| "abonnement taxi senior" | 50 | 0,20 – 1,09 € | Faible |
| "service de taxi pour personnes âgées" | 10 | 0,26 – 0,81 € | Moyenne |
| "chauffeur personne âgée" | 320 | 0,51 – 2,11 € | Faible |
| "accompagnement personnes âgées en voiture" | 390 | 0,49 – 2,48 € | Faible |
| "transport personnes âgées" | 390 | 0,47 – 2,16 € | Faible |
| "transport de personne âgée" | 40 | 0,50 – 1,96 € | Faible |

Exclusions du groupe : gratuit, bon de transport, aide, association, bénévole, emploi, offre, recrutement, devenir,
salaire, formation, réglementation, conventionné, vsl, remboursé, navigo.
Volume réel attendu dans la zone : quelques recherches par mois (petite niche, clients fidèles).
Hôpitaux et EHPAD : volume non mesurable, à citer dans l'annonce plutôt qu'à acheter.
