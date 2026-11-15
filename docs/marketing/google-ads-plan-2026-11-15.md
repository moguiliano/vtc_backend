# Plan Google Ads ZenCAR — 2026-11-15

> Étape 03 de la [série de prompts](prompts/README.md). Fondé sur 15 mois d'historique lus en entier (7 août 2025 – 15 nov. 2026 : 2 084 termes de recherche, mois, heures, enchères, pages d'arrivée, historique des modifications) — registre A-01 à A-11.
> Décisions prises en séance : Q-05, Q-07, Q-13, Q-14 à Q-17 (registre [00-contexte.md](prompts/00-contexte.md)).
> **Application en cours** — voir le [journal d'application](#journal-dapplication-2026-11-15) en fin de document.

## 0. Ce que dit l'historique (rappel)

| Indicateur | Valeur | Source |
|---|---|---|
| Dépense totale / conversions | 1 383,90 € / 162 → **8,54 € par conversion** | A-10 |
| Seuil de rentabilité par appel | 7 € (prudent) à 14 € (favorable) | priorisation §0 |
| Meilleure période | avril – mai 2026 : **4,55 à 4,82 €/conv.**, campagne en continu, tous groupes actifs | A-11 |
| Période récente (sept. – nov. 2026) | ~16 €/conv., pauses manuelles quasi quotidiennes | A-11 |
| Part des enchères remportées | **25 %** (75 % des recherches possibles sans ZenCAR) | A-11 |
| Intentions les plus rentables | « taxi Marseille » générique 6,43 € · concurrents à Marseille 7,23 € | A-10 |
| Gaspillage identifié | hors zone 245 € (29 %) · conventionné 22 € | A-10 |
| Trou | gare Saint-Charles : 0,87 € dépensé sur 15 mois alors que c'est le trajet n° 1 | A-10, C-03 |

## 1. Suivi des conversions (à faire en premier)

Sans lui, le CPA cible et la règle de budget pilotent à l'aveugle.

| Conversion | Comment | Statut cible |
|---|---|---|
| Appel depuis l'annonce | Déjà en place (« Calls from Smart Campaign Ads ») ; vérifier la **durée minimale d'appel comptée** (recommandé : 60 s) | Principale |
| Clic sur le numéro du site | GTM : déclencheur « Clic — Liens uniquement », URL du clic commence par `tel:` → balise conversion Google Ads | Principale |
| Clic WhatsApp | GTM : URL du clic contient `wa.me` | Principale |
| Réservation en ligne validée | GTM : page vue `/reservation/success` | Principale |
| Numéro dédié ZenCAR | Prendre un numéro séparé de l'activité smartphone (Q-08, action 5 de la priorisation) et l'utiliser dans le bouton d'appel | — |

Avec la bannière cookies, les conversions du site ne remontent que pour les visiteurs qui acceptent. Google complète par modélisation (Consent Mode v2).

## 2. Réglages de la campagne « Google Search-SIte Web Zencar CL »

| Réglage | Aujourd'hui | À mettre | Décision |
|---|---|---|---|
| Calendrier de diffusion | « tout le temps », avec des pauses manuelles quasi quotidiennes | **7 h – 21 h, 7j/7, fixe** ; **plus aucune pause manuelle** ; passer à 24h/24 quand le régulateur répond la nuit | Q-13 |
| Stratégie d'enchères | Maximiser les conversions, CPA cible 4,70 € | Maximiser les conversions, **CPA cible 7 €** | Q-17 |
| Budget | 12 €/jour, modifié à la main | **12 €/jour au départ + règle automatique** (§ 5) | Q-17 |
| Zones | Marseille, Marignane, Aix-en-Provence, Aubagne… | Marseille + Marignane + Aix-en-Provence (pour le seul forfait Aix → aéroport) ; **retirer Aubagne** | E-09, Q-09 |
| Appareils | Mobile uniquement | Inchangé | A-03 |
| Groupes | Groupe d'annonces 1, appel direct, aeroport / Marignane, Urgence (en veille) | **4 groupes** (§ 3) ; mettre en veille les anciens une fois les nouveaux actifs | — |

## 3. Structure : 4 groupes

| Groupe | Mots-clés (exact `[ ]` et expression `" "`) | Page d'arrivée | Pourquoi |
|---|---|---|---|
| **A — Taxi & VTC Marseille** | [taxi marseille], "taxi marseille", [numero taxi marseille], "appeler taxi marseille", [taxi vtc marseille], [vtc marseille], [chauffeur privé marseille], [taxi marseille 13013] | `/` | Intention la plus rentable (6,43 €) |
| **B — Alternative aux applis** | [uber marseille], "uber marseille", [uber taxi], [taxi uber marseille], [uber taxi marseille], [bolt marseille], [heetch marseille], [numero uber marseille] | `/` | 7,23 €/conv. ; suivi à part (garde-fou 10 €) — Q-05 |
| **C — Gare Saint-Charles** *(nouveau)* | [taxi gare saint charles], "taxi gare saint charles", "taxi marseille saint charles", [taxi gare marseille], "vtc gare saint charles", "taxi saint charles" | `/taxi-gare-saint-charles` | Trajet n° 1 (C-03), > 1 800 impressions naturelles, absent des annonces |
| **D — Aéroport Marseille-Provence** | [taxi aéroport marseille], "taxi aeroport marseille", [taxi marignane], "transfert aéroport marseille", "vtc aéroport marseille", "taxi aix aéroport marignane" | `/taxi-aeroport-marseille` (**corrige la 404**) | Forfaits 59 € (Marseille et Aix) |

## 4. Mots-clés à exclure

**Au niveau de la campagne** (expression) :
avignon · nimes · nîmes · toulon · nice · montpellier · béziers · beziers · arles · miramas · istres · manosque · cannes · antibes · valence · hyères · tarascon · sanary · bandol · seyne · six fours · pradet · farlède · ollioules · lambesc · peypin · salon de provence · pélissanne · lauris · gignac · rognac · bouc bel air · luynes · narbonne · biarritz · gisors · paris · le mans · vals les bains · martigues · aubagne · la ciotat · vitrolles · conventionné · conventionne · cpam · vsl · sans avance de frais · emploi · recrutement · uber eats · uber eat · location · hotel · hôtel · crous · horaires · bus · g7 · taxi bleu · driver

**Au niveau des groupes A, B et C seulement** (pas D, pour garder le forfait Aix → aéroport) : aix · aixois

**Conflits à corriger** : ces termes sont à la fois ajoutés et exclus ; retirer le mot-clé négatif qui les bloque : « uber taxi marseille », « bolt marseille », « station taxi marseille », « taxi marseille 13003 », « taxi vieux port », « société taxi marseille », « taxis marseille ».

## 5. Règle automatique de budget (Outils → Règles)

| Règle | Fréquence | Condition (7 derniers jours) | Action | Limite |
|---|---|---|---|---|
| Accélérer | Chaque lundi 8 h | Coût/conv. ≤ 7 € **et** conversions ≥ 2 | Budget +20 % | Plafond 20 €/jour |
| Freiner | Chaque lundi 8 h | Coût/conv. > 12 € **ou** (coût ≥ 30 € et 0 conversion) | Budget -20 % | Plancher 6 €/jour |

Le seuil de 2 conversions évite d'accélérer sur un coup de chance ; la condition « 0 conversion » évite de continuer à payer une semaine blanche.

## 6. Composants (extensions)

| Composant | Contenu |
|---|---|
| Appel | Numéro ZenCAR (dédié dès qu'il existe), **planifié 7 h – 21 h** (24h/24 avec le régulateur) |
| Lieu | Associer la fiche Google « zencar » (5,0 · 124 avis) |
| Liens annexes (6) | Taxi Gare Saint-Charles → `/taxi-gare-saint-charles` · Taxi Aéroport → `/taxi-aeroport-marseille` · Calanques → `/taxi-calanques-marseille` · Transport seniors → `/transport-seniors-marseille` · VTC Marseille → `/vtc-marseille` · Contact → `/contact` |
| Accroches | Prix VTC affiché d'avance · Paiement CB ou espèces · Taxis partenaires agréés · Suivi de vol · Van jusqu'à 8 places · Noté 5,0/5 sur Google |
| Extraits de site — Services | Aéroport, Gare Saint-Charles, Calanques, Seniors, Longue distance |
| Prix (VTC uniquement) | Forfait gare 25 € · Forfait aéroport 59 € · Aix → aéroport 59 € · Calanques 59 € |

Règle : **jamais de prix fixe pour le taxi** ; les prix affichés sont des forfaits VTC.

## 7. Annonces (une annonce responsive par groupe)

Toutes vérifiées par script : titres ≤ 30 caractères, descriptions ≤ 90, 15 titres distincts par groupe.

> **Corrections imposées par les règles Google (constatées à la mise en ligne, 2026-11-15)** :
> - **Pas de numéro de téléphone dans le texte** : le titre « Appelez le 06 74 03 96 94 » est remplacé partout par **« Appelez-Nous 7j/7 »** (le numéro passe par le composant Appel).
> - **Pas de flèche « → »** : remplacée par « vers » (descriptions) ; le titre D « Aix → Aéroport en VTC 59 € » devient **« Aix-Aéroport en VTC : 59 € »**.
> - **Pas de mot-clé visant le numéro d'un autre service** (« services de renseignements téléphoniques ») : [numero uber marseille] est retiré du groupe B, et le titre B « Un Vrai Numéro à Appeler » devient **« Réservez par Téléphone »**. Aucune marque concurrente dans les textes. Le mot « taxi » n'est jamais utilisé pour prétendre que ZenCAR est un taxi (Q-07).

### Groupe A Taxi & VTC Marseille

URL finale : `https://dzencar.fr/` · Titre à épingler en position 1 : **Taxi & VTC Marseille**

| # | Titre (≤ 30) | Car. |
|---|---|---|
| 1 | Taxi & VTC Marseille | 20 |
| 2 | Chauffeur en 5 à 10 min | 23 |
| 3 | ZenCAR Taxi & VTC | 17 |
| 4 | Prix VTC Affiché d'Avance | 25 |
| 5 | Noté 5,0/5 sur Google | 21 |
| 6 | Disponible 7j/7 | 15 |
| 7 | Réservation en Ligne Rapide | 27 |
| 8 | Appelez le 06 74 03 96 94 | 25 |
| 9 | Paiement CB ou Espèces | 22 |
| 10 | Taxis Partenaires Agréés | 24 |
| 11 | Chauffeurs Locaux Marseillais | 29 |
| 12 | Aéroport, Gare, Calanques | 25 |
| 13 | Sans Application à Installer | 28 |
| 14 | VTC Prix Fixe Avant Départ | 26 |
| 15 | Partout dans Marseille | 22 |

| # | Description (≤ 90) | Car. |
|---|---|---|
| 1 | VTC à prix affiché avant de réserver, ou taxi partenaire agréé au compteur. 7j/7. | 81 |
| 2 | Prise en charge en 5 à 10 min dans Marseille. Paiement à bord, CB ou espèces. | 77 |
| 3 | Noté 5,0/5 sur Google par nos clients. Appelez ou réservez en ligne en quelques clics. | 86 |
| 4 | Aéroport, gare Saint-Charles, calanques : forfaits VTC à prix fixe, réservables en ligne. | 89 |

### Groupe B Alternative aux applis

URL finale : `https://dzencar.fr/` · Titre à épingler en position 1 : **Sans Application à Installer**

| # | Titre (≤ 30) | Car. |
|---|---|---|
| 1 | Sans Application à Installer | 28 |
| 2 | Prix VTC Affiché d'Avance | 25 |
| 3 | Chauffeur en 5 à 10 min | 23 |
| 4 | Un Vrai Numéro à Appeler | 24 |
| 5 | Taxi & VTC Marseille | 20 |
| 6 | Prix Connu Avant le Départ | 26 |
| 7 | ZenCAR Taxi & VTC | 17 |
| 8 | Noté 5,0/5 sur Google | 21 |
| 9 | Appelez le 06 74 03 96 94 | 25 |
| 10 | Réservation en Ligne Rapide | 27 |
| 11 | Paiement CB ou Espèces | 22 |
| 12 | Chauffeurs Locaux Marseillais | 29 |
| 13 | Disponible 7j/7 | 15 |
| 14 | Alternative Locale | 18 |
| 15 | Réservez sans Compte | 20 |

| # | Description (≤ 90) | Car. |
|---|---|---|
| 1 | Pas d'application ni de compte : appelez ou réservez en ligne, prix VTC affiché d'avance. | 89 |
| 2 | Chauffeurs marseillais disponibles 7j/7, prise en charge en 5 à 10 minutes à Marseille. | 87 |
| 3 | Noté 5,0/5 sur Google. Paiement à bord par carte ou en espèces. | 63 |
| 4 | Aéroport, gare Saint-Charles, calanques : forfaits VTC à prix fixe, réservables en ligne. | 89 |

### Groupe C Gare Saint-Charles

URL finale : `https://dzencar.fr/taxi-gare-saint-charles` · Titre à épingler en position 1 : **Taxi Gare Saint-Charles**

| # | Titre (≤ 30) | Car. |
|---|---|---|
| 1 | Taxi Gare Saint-Charles | 23 |
| 2 | VTC Gare Saint-Charles | 22 |
| 3 | Forfait VTC Gare : 25 € | 23 |
| 4 | Prise en Charge à la Gare | 25 |
| 5 | Chauffeur en 5 à 10 min | 23 |
| 6 | Évitez la File d'Attente | 24 |
| 7 | Taxi & VTC Marseille | 20 |
| 8 | Noté 5,0/5 sur Google | 21 |
| 9 | Appelez le 06 74 03 96 94 | 25 |
| 10 | Réservez Avant d'Arriver | 24 |
| 11 | Paiement CB ou Espèces | 22 |
| 12 | Véhicules Grand Coffre | 22 |
| 13 | Van jusqu'à 8 Places | 20 |
| 14 | Disponible 7j/7 | 15 |
| 15 | ZenCAR Taxi & VTC | 17 |

| # | Description (≤ 90) | Car. |
|---|---|---|
| 1 | Arrivée en gare Saint-Charles ? VTC à prix affiché d'avance ou taxi partenaire agréé. | 85 |
| 2 | Forfait VTC Marseille centre → gare Saint-Charles : 25 €. Taxi partenaire au compteur. | 86 |
| 3 | Prise en charge rapide à la sortie de la gare. Paiement à bord, CB ou espèces. | 78 |
| 4 | Noté 5,0/5 sur Google. Berline, grand coffre ou van jusqu'à 8 places, 7j/7. | 75 |

### Groupe D Aéroport Marseille-Provence

URL finale : `https://dzencar.fr/taxi-aeroport-marseille` · Titre à épingler en position 1 : **Taxi Aéroport Marseille**

| # | Titre (≤ 30) | Car. |
|---|---|---|
| 1 | Taxi Aéroport Marseille | 23 |
| 2 | VTC Aéroport Marignane | 22 |
| 3 | Forfait VTC Aéroport : 59 € | 27 |
| 4 | Suivi de Votre Vol | 18 |
| 5 | Transfert Marignane 7j/7 | 24 |
| 6 | Aix → Aéroport en VTC 59 € | 26 |
| 7 | Réservez à l'Avance | 19 |
| 8 | Noté 5,0/5 sur Google | 21 |
| 9 | Appelez le 06 74 03 96 94 | 25 |
| 10 | Taxi & VTC Marseille | 20 |
| 11 | Grand Coffre pour Bagages | 25 |
| 12 | Van jusqu'à 8 Places | 20 |
| 13 | Paiement CB ou Espèces | 22 |
| 14 | ZenCAR Taxi & VTC | 17 |
| 15 | Chauffeur à l'Heure | 19 |

| # | Description (≤ 90) | Car. |
|---|---|---|
| 1 | Forfait VTC Marseille → aéroport Marseille-Provence : 59 €, prix fixé avant de réserver. | 88 |
| 2 | Suivi de votre vol. Grand coffre et van jusqu'à 8 places disponibles sur réservation. | 85 |
| 3 | Forfait VTC Aix-en-Provence → aéroport Marignane : 59 €. Réservez en ligne ou par appel. | 88 |
| 4 | Noté 5,0/5 sur Google. Paiement à bord par carte ou espèces. Taxi partenaire au compteur. | 89 |

## 8. Objectifs à 30 jours

| Indicateur | Aujourd'hui | Cible à 30 jours |
|---|---|---|
| Coût par conversion | ~16 € (sept. – nov.) | ≤ 7 € |
| Conversions par mois | 1 à 5 | ≥ 25 |
| Part des enchères remportées | 25 % | ≥ 40 % |
| Pauses manuelles | ~20 par mois | 0 |
| Conversions du site mesurées | 0 | clic téléphone, WhatsApp, réservation |

Repère : avril – mai 2026 a atteint 21 à 45 conversions par mois à moins de 5 € l'une.

## 9. Ordre de mise en place

1. Suivi des conversions dans GTM (§ 1).
2. Réglages : calendrier 7 h – 21 h, zones, CPA 7 € (§ 2).
3. Mots-clés négatifs et conflits (§ 4).
4. Nouveaux groupes et annonces (§ 3, § 7), puis mise en veille des anciens groupes.
5. Composants (§ 6).
6. Règle de budget (§ 5).
7. **Ne plus toucher pendant 14 jours** (apprentissage), sauf incident.

## Non analysé

| Point | Pourquoi |
|---|---|
| Durée minimale d'appel comptée comme conversion | Réglage de l'action « Calls from Smart Campaign Ads » non ouvert |
| Part des appels Google Ads devenus des courses | Non mesuré (Q-01) |
| 527 € de termes de recherche masqués par Google | Non communiqués par Google |
| Score de qualité des mots-clés | Colonne non affichée |

## Auto-critique

- Les coûts par conversion par intention reposent sur des volumes faibles (4 à 43,5 conversions par catégorie) : ce sont des tendances, pas des certitudes.
- Une « conversion » est un appel, pas une course : le seuil de 7 € par appel suppose 5 à 7 appels sur 10 transformés (C-10, déclaré).
- La meilleure période (avril – mai) a aussi pu profiter d'une saison favorable ; la comparaison avec septembre – novembre n'isole pas la seule cause « pauses manuelles ».


## Journal d'application (2026-11-15)

Appliqué par Claude dans le compte 522-918-7346, à la demande du fondateur.

| # | Action | État |
|---|---|---|
| 1 | Calendrier de diffusion : 7 h – 21 h, 7j/7 (anciens créneaux supprimés) | ✅ fait, vérifié |
| 2 | CPA cible de la campagne : 4,70 € → **7 €** (hérité par les groupes) | ✅ fait, vérifié |
| 3 | Zones : **Aubagne retirée**, **ville de Marseille entière ajoutée** (seuls quelques arrondissements étaient ciblés) | ✅ fait, vérifié |
| 4 | 46 mots-clés négatifs ajoutés (villes hors zone, conventionné, hôtel, location…) | ✅ fait, vérifié (116 → 162) |
| 5 | Négatif « heetch » retiré (conflit avec Q-05) | ✅ fait |
| 6 | **Liste partagée « ctr faible » (269 exclusions, dont ~190 recherches utiles : vtc marseille, taxi aéroport marseille, taxi gare saint charles…) détachée de la campagne** — décision du fondateur ; la liste reste dans le compte | ✅ fait, vérifié |
| 7 | 6 négatifs repris de cette liste : navette, shuttle, allauch, ameli, securite sociale, sécurité sociale | ✅ fait, vérifié |
| 8 | Groupe **C - Gare Saint-Charles** créé : 6 mots-clés, annonce (URL /taxi-gare-saint-charles), CPA 7 € | ✅ fait — annonce « Éligible » après correction (flèche, téléphone) |
| 9 | Groupe **B - Alternative aux applis** créé (7 mots-clés, annonce) ; [numero uber marseille] supprimé, titre « Un Vrai Numéro à Appeler » → « Réservez par Téléphone » | ✅ fait — annonce « Éligible » |
| 10 | Groupe A (Groupe d'annonces 1) : **nouvelle annonce A** créée ; 5 mots-clés concurrents mis en veille (uber taxi, uber marseille, bolt marseille, [taxi uber marseille], "uber taxi marseille") ; « taxi marseille telephone » et « appeler taxi marseille » ajoutés (repris d'« appel direct ») | ✅ fait — annonce « Éligible » |
| 10b | **Incident corrigé** : un clic décalé a changé le type de correspondance de « taxi marseille 13003 » (expression → large) ; rétabli en expression exacte | ✅ corrigé |
| 11 | Groupe D (aeroport / Marignane) : **nouvelle annonce D** (URL /taxi-aeroport-marseille) ; ancienne annonce (URL /taxi/aeroport en 404) **mise en veille** | ✅ fait |
| 12 | Groupe « appel direct » **mis en veille** ; négatifs « aix » et « aixois » ajoutés aux groupes A, B et C (pas D) | ✅ fait |
| 13a | **6 liens annexes** créés (campagne) : Taxi Gare Saint-Charles · Taxi Aéroport Marignane · Taxi Calanques · Transport Seniors · VTC Marseille · Nous Contacter — textes et URL vérifiés sur le site en ligne (pages en 200, prix 25 € / 59 € affichés) | ✅ créés — en cours d'examen Google |
| 13b | **5 anciens liens annexes mis en veille** (pas supprimés, réactivables) : « Commander maintenant » (« Réponse rapide 24/7 »), « Transfert gare », « Transfert Aéroport », « taxi-marseille » (« Chauffeur à Marseille 24/7 »), « taxi urgent » (« prise en charge immédiate ») — promesses non tenues avec une diffusion 7 h – 21 h | ✅ fait, vérifié |
| 13c | **6 accroches** : Prix VTC affiché d'avance · Paiement CB ou espèces · Taxis partenaires agréés · Suivi de vol · Van jusqu'à 8 places · Noté 5,0/5 sur Google | ✅ créées — en cours d'examen |
| 13d | **Extrait structuré** « Services » : Transfert aéroport, Gare Saint-Charles, Calanques, Transport seniors, Longue distance | ✅ créé — en cours d'examen |
| 13e | **Prix** (type Services, VTC uniquement) : Forfait Gare St-Charles 25 € · Forfait Aéroport 59 € · Aix vers Aéroport 59 € · Forfait Calanques 59 € (URL = accueil, seule page qui affiche ce prix) | ✅ créé — en cours d'examen |
| 13f | Composant Appel : **non planifié volontairement** — la campagne entière ne diffuse déjà que 7 h – 21 h ; planifier aussi l'appel ferait un second réglage à penser lors du passage en 24h/24 | ➖ écarté (écart au plan) |
| 13g | Constat : le composant « Nom de l'entreprise » (ZenCAR) est **refusé** par Google depuis oct. 2025 (« Nom de l'entreprise non pertinent ») — sans doute faute de vérification de l'annonceur au nom de SIMPLICI CAB | ⚠️ à traiter par le fondateur |
| 14a | Règle **« ZenCAR - Accélérer budget (coût/conv ≤ 7 €) »** : chaque lundi 8 h, données des 7 derniers jours, si Coût/conv. ≤ 7 € **et** Conversions > 1,9 → budget quotidien +20 %, plafond 20 € | ✅ enregistrée, vérifiée |
| 14b | Règle **« ZenCAR - Freiner budget (coût/conv > 12 €) »** : chaque lundi 8 h, données des 7 derniers jours, si Coût/conv. > 12 € → budget quotidien -20 %, plancher 6 € | ✅ enregistrée, vérifiée |
| 14c | Règle **« ZenCAR - Freiner budget (30 € sans conversion) »** : chaque lundi 8 h, données des 7 derniers jours, si Coût > 30 € **et** Conversions < 0,5 → budget quotidien -20 %, plancher 6 € (indispensable : avec 0 conversion, la règle 14b ne se déclenche pas) | ✅ enregistrée, vérifiée |
| 15 | Suivi des conversions dans GTM (§ 1) | ⏳ hors Google Ads (accès GTM) — numéro : on garde le 06 74 03 96 94 (Q-08) |

Les 3 règles (14a, 14b, 14c) apparaissent dans Outils → Règles, toutes sur la seule campagne « Google Search-SIte Web Zencar CL », avec un e-mail en cas de changement ou d'erreur. Première exécution : lundi 16 nov. 2026, 8 h.

Effet attendu le plus important : le détachement de « ctr faible » rouvre la diffusion sur les recherches principales bloquées depuis le 14 oct. 2026 — à surveiller dans les 7 premiers jours (dépense, coût par conversion).
