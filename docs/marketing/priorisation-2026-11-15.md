# Priorisation marketing ZenCAR — 2026-11-15

> Étape 02 de la [série de prompts](prompts/README.md), à partir du [diagnostic](diagnostic-2026-11-15.md) (29/100).
> Données utilisées, toutes ✅ dans le [registre](prompts/00-contexte.md) : C-07 (objectif 200 courses/mois à 6 mois), C-08 (3 à 6 h/semaine, plus de 600 €/mois si rentable), C-10 (5 à 7 demandes sur 10 deviennent une course), C-11 (pas de vidéos ni de réseaux sociaux), O-14, Q-01, Q-02, A-01 à A-09, S-07.
> **Règle appliquée :** un gain n'est chiffré que s'il repose sur des données ✅. Sinon, il est marqué « non chiffrable aujourd'hui », avec la donnée qui manque et l'indicateur qui le mesurera.

## 0. L'économie d'une course (base de tous les calculs)

| Élément | Valeur | Source |
|---|---|---|
| Prix moyen d'une course ZenCAR | P = 20 à 30 € (« moins de 30 € ») | O-14 |
| Ce que rapporte une course à ZenCAR | R = 0,6 × P + 0,4 × (20 % × P) = **0,68 × P, soit 14 à 20 €** de chiffre d'affaires (avant carburant sur les courses du fondateur) | Q-01, Q-02 [CALCULÉ] |
| Demandes qui deviennent une course | t = 50 à 70 % | C-10 |
| **Coût maximum supportable pour une demande (appel)** | R × t = **7 € (cas prudent : 14 € × 50 %) à 14 € (cas favorable : 20 € × 70 %)** | [CALCULÉ] |
| Coût actuel d'un appel Google Ads | 6,48 € (4 appels sur 30 jours) | A-08 |

**Conséquence :** Google Ads est aujourd'hui **juste sous le seuil de rentabilité du cas prudent** (6,48 € contre 7 €). En augmentant le budget, le coût par appel monte en général : la pub peut vite devenir déficitaire. On ne peut donc pas augmenter le budget à l'aveugle, sans mesurer d'abord le coût par **course**.

## 1. Toutes les actions possibles

| # | Action | Problème résolu (diagnostic) | Gain estimé | Effort | Coût | Délai | Risque si on ne fait rien |
|---|---|---|---|---|---|---|---|
| 1 | Retirer les faux avis et afficher les vrais (5,0 · 124 avis + lien vers la fiche) | V-01, nouveaux problèmes 2 | Non chiffrable (confiance). Supprime un risque juridique. | 1 h | 0 € | Immédiat | Sanction DGCCRF ; faux avis visibles jusque dans Google |
| 2 | Corriger les promesses fausses : « 24h/24 », « Taxi agréé », photo « Flotte ZenCAR », « plateforme » | V-02, V-04, O-10, E-05 | Non chiffrable (moins de déception, d'avis négatifs, d'appels de nuit perdus) | 1 à 2 h | 0 € | Immédiat | Publicité trompeuse ; avis négatifs |
| 3 | Bannière de consentement aux cookies + Consent Mode | V-07 | Non chiffrable. Conformité RGPD, mesure Google Ads fiable en Europe. | 1 à 2 h | 0 € (outil gratuit) | Immédiat | Sanction CNIL ; conversions sous-mesurées |
| 4 | Mesurer : clic sur le numéro, WhatsApp, réservation validée (GTM → Google Ads) | V-08 | Indirect : condition de tout le reste | 2 à 3 h | 0 € | 1 semaine | Impossible de savoir si la pub est rentable |
| 5 | Numéro dédié à ZenCAR (séparé de l'activité smartphone) | Q-06, Q-08 | Indirect : mesure fiable, fiche Google cohérente | 1 h | ~2 à 10 €/mois | 1 semaine | Appels et conversions mélangés entre deux activités |
| 6 | Google Ads : diffusion 7 h – 21 h, page 404 corrigée, Aubagne retiré, annonces « VTC » au lieu de « taxi » | A-03, A-09, Q-07 | Économie de la part payée la nuit et hors zone (non chiffrable : pas de découpage horaire des 19 clics) | 1 h | 0 € | Immédiat | Argent payé pour des appels sans réponse ; clics perdus sur la 404 |
| 7 | Google Ads : lever le frein du CPA cible et monter le budget **par paliers**, groupe dédié « gare » | A-01, A-04, nouveaux problèmes 3 | Si les 12 €/jour sont dépensés (360 €/mois) : **55 appels à 6,48 €** → 28 à 39 courses ; **cas prudent** (appel à 13 €) : 28 appels → 14 à 19 courses. Rentabilité : de -165 € à +420 €/mois. | 2 h + 30 min/semaine | 360 €/mois puis plus | 2 à 4 semaines | Absent de la recherche n° 1 où 4 concurrents paient |
| 8 | Fiche Google : nom « ZenCAR », catégorie, services, horaires réels, photos du vrai véhicule, posts, lien vers le site | Nouveaux problèmes 1 et 6, Q-11, Q-12 | Non chiffrable aujourd'hui (il manque les statistiques de la fiche : vues, appels, itinéraires) | 2 à 3 h + 20 min/semaine | 0 € | 1 à 3 mois | Reste hors du bloc carte malgré la meilleure note |
| 9 | Page gare : passer de la position 8–10 au top 3 (contenu, forfait 25 €, accueil, liens internes) | S-07 | Non chiffrable sans le taux de contact du site (action 4). Demande mesurée : ~600 impressions/mois sur la gare [CALCULÉ : 1 800 ÷ 3]. | 3 h | 0 € | 1 à 3 mois | Demande déjà présente laissée aux concurrents |
| 10 | SEO technique : redirections http/www → https, image de 2,5 Mo compressée, 3 pages invisibles à corriger | S-08, S-09, S-11 | Non chiffrable (indexation, vitesse) | 2 h | 0 € | 2 à 6 semaines | Doublons, pages lentes et invisibles |
| 11 | Page longue distance | O-11 | Non chiffrable (aucune donnée de recherche) | 2 h | 0 € | 1 à 3 mois | Service à développer sans vitrine |
| 12 | Après chaque course : SMS de remerciement avec le contact WhatsApp ZenCAR et une demande d'avis | V-10, C-04 | Non chiffrable aujourd'hui (taux de retour inconnu). Base : ~45 nouveaux clients par mois qui ne reviennent pas. | 2 h + 5 min/jour | ~0,05 €/SMS | 1 à 3 mois | Chaque client conquis est perdu après la course |
| 13 | Prospection pro : hôtels près de la gare, EHPAD et résidences seniors, conciergeries | C-05, O-11 | Non chiffrable (aucun client pro aujourd'hui) | 2 h/semaine | 0 € | 1 à 3 mois | Aucune source de courses régulières |
| 14 | Page « Entreprises » et offre pro | C-05 | Non chiffrable | 2 h | 0 € | Après l'action 13 | — |

## 2. Les 5 chantiers retenus

| Rang | Chantier | Actions | Pourquoi lui, avant les autres | Indicateur de réussite | Cible 30 j | Cible 90 j | Suite |
|---|---|---|---|---|---|---|---|
| 1 | **Confiance et conformité** | 1, 2, 3 | Risque juridique immédiat ; peu de travail ; tout le reste envoie du trafic vers ce site | Faux avis, fausses promesses et absence de bannière | 0 restant | 0 restant | Correction directe du site |
| 2 | **Mesure** | 4, 5 | Sans elle, impossible de savoir si une dépense rapporte (seuil : 7 à 14 € par appel) | Demandes mesurées par canal (appel annonce, appel site, WhatsApp, réservation) | 100 % des canaux mesurés | Coût par course connu | Prompt 03 + correction du site |
| 3 | **Google : fiche et page gare** | 8, 9, 10 | Gratuit ; la demande existe (gare = trajet n° 1, position 8–10) ; la fiche a la meilleure note du marché local | Présence dans le bloc carte « taxi gare saint charles » ; position de la page gare | Fiche corrigée ; page gare enrichie | Bloc carte ; page gare dans le top 5 | Prompt 04 |
| 4 | **Google Ads remis en ordre, puis montée par paliers** | 6, 7 | La pub actuelle est presque à l'arrêt ; les corrections (horaires, 404, zone) sont rapides ; la montée de budget attend la mesure | Coût par course (et non par appel) | ≤ 14 € par course | ≤ 12 € par course, budget relevé si tenu | Prompt 03 |
| 5 | **Fidélisation, avis et clients pros** | 12, 13 | Le levier le moins cher pour aller vers 200 courses : 45 nouveaux clients par mois qui ne reviennent pas | Courses de clients déjà venus ; nouveaux avis ; rendez-vous pros | SMS après course en place | 15 % de courses de clients revenus ; 2 clients pros | Prompt 05 |

## 3. Les 5 actions urgentes de cette semaine (moins de 2 h chacune)

1. **Retirer les 3 faux avis et la mention « Avis Google vérifiés »** de l'accueil, et afficher la vraie note (5,0 · 124 avis, recopiée le jour même) avec un lien vers la fiche. *Risque juridique.*
2. **Corriger les promesses fausses** sur le site : « 24h/24 » → horaires réels ; « Taxi agréé Ville de Marseille » et « nos taxis » → formulation exacte (VTC, et taxis partenaires agréés) ; légende de la photo d'illustration ; « plateforme » dans les données structurées. *Risque juridique.*
3. **Google Ads, 3 réglages** : diffusion de 7 h à 21 h ; URL de l'annonce aéroport corrigée (/taxi-aeroport-marseille) ; Aubagne retiré des zones.
4. **Bannière de consentement aux cookies** avec Consent Mode. *Risque juridique.*
5. **Fiche Google** : horaires réels, nom écrit « ZenCAR » si c'est le nom utilisé partout, lien vers dzencar.fr. (Le choix de catégorie attend l'étape 04, Q-11.)

## 4. Ce qu'il ne faut PAS faire maintenant

| Action | Pourquoi |
|---|---|
| Augmenter le budget Google Ads au-delà des 12 €/jour | Tant qu'on ne mesure pas le coût par **course**, on peut perdre de l'argent : le seuil est de 7 à 14 € par appel et le coût actuel (6,48 €) est déjà proche du bas de la fourchette. |
| Réseaux sociaux, vidéos | Refusé (C-11), et aucune donnée ne montre que les clients ZenCAR viennent de là. |
| Proposer aux clients Uber, Bolt ou Heetch de réserver ZenCAR en direct pendant une course plateforme | Les règles des plateformes l'interdisent en général : risque de désactivation du compte, c'est-à-dire de la source n° 1 d'activité du fondateur (P-06). À vérifier dans les conditions de chaque plateforme avant toute action. |
| Campagne Performance Max | Ses composants ont déjà été refusés (A-05), et la mesure n'est pas en place. |
| Créer des pages par ville copiées-collées | Zone de prise en charge limitée à Marseille + aéroport (E-09). |
| Application mobile | Aucune donnée n'en montre le besoin ; coût élevé. |

## 5. Plan sur 90 jours (3 à 6 h par semaine)

| Période | Actions | Temps | Indicateur de fin de période |
|---|---|---|---|
| **Semaine 1** | Urgences 1 à 5 | ~6 h | Plus aucun faux avis ni fausse promesse ; bannière en ligne ; Ads limité à 7 h – 21 h |
| **Semaine 2** | Numéro dédié ZenCAR (5) ; suivi des conversions dans GTM (4) ; début du comptage manuel « moi / dispatchée / canal » (Q-01) | ~5 h | Chaque demande attribuée à un canal |
| **Semaine 3** | Fiche Google complète (8) ; SEO technique (10) | ~5 h | Fiche à jour ; redirections et image corrigées |
| **Semaine 4** | Page gare enrichie (9) ; Google Ads restructuré avec un groupe « gare » et des annonces VTC (prompt 03) | ~5 h | Annonces visibles sur « taxi gare saint charles » |
| **Mois 2** | SMS après course + demande d'avis (12) ; page longue distance (11) ; budget Ads au palier suivant **si** le coût par course est sous 14 € | ~4 h/semaine | 1er bilan coût par course ; nouveaux avis |
| **Mois 3** | Prospection pro, 2 h/semaine (13) ; page Entreprises (14) ; ajustement du budget selon les chiffres du mois 2 | ~4 h/semaine | 2 clients pros ; part des clients revenus mesurée |

## 6. L'objectif de 200 courses par mois

Avec les données ✅, **seul le levier Google Ads est chiffrable** : à 12 €/jour, entre +14 et +39 courses par mois (voir l'action 7), soit au plus ~65 à ~90 courses au total au lieu de < 50 [CALCULÉ]. Les autres leviers (fiche, page gare, fidélisation, pros) ne peuvent pas être chiffrés honnêtement avant 30 jours de mesure.
**Conclusion :** 200 courses par mois dans 6 mois n'est **ni confirmé ni exclu**. Ce sera réévalué à la fin du mois 2, avec les vrais chiffres par canal.

## Auto-critique (relecture d'investisseur sceptique)

- **Estimation la plus fragile :** le coût par appel de 6,48 € repose sur 4 appels. Il peut être 2 fois plus élevé ; d'où le cas prudent à 13 € de l'action 7, qui donne une perte possible de 165 €/mois [CALCULÉ : 360 € − 14 × 14 €].
- **Optimisme retiré :** aucun gain n'est attribué à la fiche Google, à la page gare, à la fidélisation ni aux pros, faute de données. Les estimer à partir de taux « moyens du marché » serait une supposition : c'est donc volontairement écarté.
- **Biais possible :** le chantier 1 est classé premier pour le risque juridique et pas pour son gain, qui n'est pas mesurable. C'est assumé.
- **Formules et données :** R = 0,68 × P (Q-01, Q-02, O-14) ; seuil = R × t (C-10) ; appels = budget ÷ coût par appel (A-01, A-08).

## Questions restées ouvertes

Q-04 (Quick Talk : date de lancement, qui conditionne le 24h/24), Q-05 (mots-clés « uber »), Q-07 (annonces « taxi »), Q-08 (séparation de l'activité smartphone, en partie traitée par l'action 5), Q-10 (forfaits Sormiou), Q-11 (catégorie et nom de la fiche), Q-12 (adresse de la fiche et des annuaires). Aucune ne bloque la semaine 1.
