# Diagnostic marketing ZenCAR — 2026-11-15

> Étape 01 de la [série de prompts](prompts/README.md). **Constats uniquement, aucune solution** : les solutions viennent à l'étape 02 (priorisation).
> Toutes les données utilisées sont ✅ dans le [registre](prompts/00-contexte.md) ; les identifiants (E-05, A-07…) y renvoient.
> Chaque affirmation est classée **[CONSTATÉ]** (lu dans une donnée), **[CALCULÉ]** (formule donnée) ou **[DÉCLARÉ]** (réponse du fondateur en séance). Aucune supposition.

## Phase A — Inventaire des sources

| Source | Période | Contenu lu | Limites |
|---|---|---|---|
| Séance de validation avec le fondateur | 2026-11-15 | 58 données du registre | Ordres de grandeur, pas de comptage (Q-01, C-02, O-14) |
| Google Ads (compte 522-918-7346), lecture seule | 16 oct. – 14 nov. 2026 (+ comparaison 30 j précédents) | campagnes, groupes, 149 mots-clés, termes de recherche, 11 actions de conversion, zones, calendrier, appareils, 116 négatifs, 4 annonces | 30 jours ; 19 clics : volume trop faible pour conclure statistiquement ; Google masque 8 clics sur 19 dans les termes de recherche |
| Search Console (dzencar.fr) | 13 août – 12 nov. 2026 | les 167 requêtes et les 9 adresses de pages, lues en entier | Pas de données au-delà de 3 mois consultées |
| Site en ligne + code | 2026-11-15 | pages, redirections, forfaits, balises, poids des pages | Parcours de réservation non testé de bout en bout ; pas de mesure PageSpeed |
| Résultats Google (navigateur, localisation non contrôlée) | 2026-11-15 | « taxi gare saint charles marseille », « zencar marseille », « ZenCAR Taxi VTC Marseille avis » | 3 recherches seulement ; résultats personnalisés possibles |

**Contrôles :** totaux Google Ads cohérents entre vues (407 impressions, 19 clics, 25,94 €) ; total Search Console 61 clics = somme des pages (31 + 15 + 6 + 4 + 3 + 2) [CALCULÉ]. Écart mineur : le fondateur a déclaré 125 avis Google, la fiche en affiche **124** [CONSTATÉ].

## 1. Parcours client, étape par étape

### Étape 1 — Recherche Google
- Les gens cherchent **« taxi »**, presque jamais « vtc » : sur 167 requêtes, les plus volumineuses contiennent toutes « taxi » (S-07) [CONSTATÉ].
- **Gare Saint-Charles** : une vingtaine de variantes, plus de 1 800 impressions, le site en **position 8 à 10** (S-07) [CONSTATÉ]. C'est aussi le trajet ZenCAR n° 1 (C-03) [DÉCLARÉ].
- **Aéroport** : position **20 à 37** (2e à 4e page) ; « taxi marseille » : position **31** (S-07) [CONSTATÉ].
- **Bloc carte (Google Maps)** sur « taxi gare saint charles marseille » : ZenCAR **absent** ; présents « Taxi Marseille gare saint Charles » (4,6 / 19 avis) et « Taxi Faten » (4,9 / 111 avis) [CONSTATÉ].
- **Annonces payantes** sur cette même recherche : 4 concurrents VTC locaux (am-driver, Naveco, lh-transport, AKF Transport), **pas ZenCAR** [CONSTATÉ]. La campagne n'a dépensé que **0,86 €/jour** pour un budget de 12 €/jour, avec un CPA cible de 4,70 € (A-01, A-04) [CONSTATÉ / CALCULÉ : 25,94 € ÷ 30].
- **Recherche sur la marque** : ZenCAR sort bien en 1re position, mais la marque est diluée : sociétés homonymes « ZENCAR S » et « ZENCAR A » (fret, 13013), un « Zencar » de location de voitures de luxe, et une fiche MapQuest « Zencar » au **257 bd Danielle Casanova (13014)**, alors que le siège est au **12 rue Antoine Ré (13010)** (E-02) [CONSTATÉ].

### Étape 2 — Ce que voit l'internaute dans les résultats
- **L'extrait Google de dzencar.fr affiche les faux avis** du site (« Chauffeur très ponctuel pour mon vol à Marignane… », « Mon père âgé a été… ») [CONSTATÉ] : les avis fictifs (S-05) sont donc diffusés jusque dans Google.
- Le titre et la description du site promettent « Taxi… disponible en 5 min » et « Tarifs officiels Ville de Marseille » [CONSTATÉ], alors que ZenCAR n'a pas de licence taxi (E-06) [DÉCLARÉ].
- **Fiche Google** : nom « **zencar** » (minuscules), **5,0/5, 124 avis**, catégorie « **Service de taxi** » [CONSTATÉ]. La société est inscrite comme VTC (E-08) ; les concurrents VTC utilisent « Service de chauffeur privé » [CONSTATÉ].
- **Annonces** : 3 sur 4 vendent du « Taxi immédiat / Taxi Marseille / Taxi Aéroport » (A-09) [CONSTATÉ].

### Étape 3 — Arrivée sur le site
- Numéro, bouton d'appel et WhatsApp visibles partout ; forfaits VTC à prix fixe sur l'accueil ; formulaire « Réservation rapide » (P-01, O-09) [CONSTATÉ].
- Image principale de l'accueil : **2,5 Mo** (PNG) pour un public 100 % mobile (A-03) [CONSTATÉ].
- Promesses non tenues ou fausses : « 24h/24 » (réel : ~7 h – 21 h sans garantie, E-10), « Taxi agréé Ville de Marseille » (E-06), photo « Flotte ZenCAR » Viano / Classe S qui est une illustration (O-10), avis fictifs (S-05) [CONSTATÉ + DÉCLARÉ].
- L'annonce aéroport mène à **/taxi/aeroport : erreur 404** (A-09) [CONSTATÉ].
- 3 pages n'apparaissent jamais dans Google en 3 mois : /taxi-marseille, /vtc-marseille, /transport-seniors-marseille (S-08) [CONSTATÉ]. Aucune page longue distance ni entreprises, alors que la longue distance est à développer (O-11, S-01) [CONSTATÉ + DÉCLARÉ].
- Le site répond en http:// et en www. sans redirection : doublons indexés (S-09) [CONSTATÉ].
- Aucune bannière de consentement aux cookies alors que Google Tag Manager est chargé (S-04) [CONSTATÉ].

### Étape 4 — Prise de contact
- Le numéro 06 74 03 96 94 sert **aussi à l'activité réparation de smartphones** (Q-06) [DÉCLARÉ], et cette activité a sa propre campagne dans le même compte Google Ads (A-05) [CONSTATÉ].
- La nuit (21 h – 7 h), **personne ne répond** (E-10) [DÉCLARÉ], alors que les annonces sont diffusées 24h/24 (A-03) et que le site promet 24h/24 [CONSTATÉ].
- En journée, la réponse dépend de la disponibilité du fondateur, qui conduit aussi (E-10) [DÉCLARÉ].
- Rien ne mesure les contacts venus du site : ni clic sur le numéro, ni WhatsApp, ni réservation en ligne (A-07, V-08) [CONSTATÉ].

### Étape 5 — La course
- Délai de 5 à 10 min tenu en journée (E-11) [DÉCLARÉ]. 6 courses sur 10 faites par le fondateur, 4 dispatchées à plus de 50 partenaires (Q-01, E-07) [DÉCLARÉ].
- La qualité des courses dispatchées n'est pas mesurée (non évalué).

### Étape 6 — Après la course
- Pas d'email de confirmation (P-04), pas de relance, **presque aucun client ne revient** (C-04, V-10) [CONSTATÉ + DÉCLARÉ].
- Paradoxe : 124 avis à 5,0 [CONSTATÉ], mais aucun client fidélisé [DÉCLARÉ], et le site n'affiche aucun lien vers ces avis (V-09) [CONSTATÉ].

## 2. Notes par domaine

| Domaine | Note | Justification |
|---|---|---|
| Positionnement et offre | **4/10** | Offre solide et vérifiée (grille VTC, 6 forfaits, aéroport, calanques, seniors, longue distance, plus de 50 partenaires) ; mais positionnement contradictoire : « taxi » partout pour une société VTC, « plateforme » dans les données structurées, marque diluée par des homonymes. |
| Message et confiance | **2/10** | Faux avis (repris par Google), photo d'illustration présentée comme la flotte, « 24h/24 » et « taxi agréé » faux ; le vrai atout (124 avis à 5,0) est invisible sur le site. |
| Site et conversion mobile | **5/10** | Appel, WhatsApp et forfaits accessibles partout ; mais image de 2,5 Mo, une page d'annonce en 404, 3 pages jamais vues par Google, pas de page longue distance. Parcours de réservation non testé. |
| SEO local et fiche Google | **4/10** | Fiche excellente en note (5,0, 124 avis) mais absente du bloc carte sur la recherche n° 1, nom en minuscules, catégorie « taxi » ; site en bas de 1re page sur la gare, invisible sur l'aéroport et « taxi marseille » ; doublons http/www ; adresses incohérentes sur le web. |
| Google Ads | **3/10** | Budget presque pas dépensé (0,86 €/jour sur 12 €) ; absent de la recherche gare où 4 concurrents paient ; 4 conversions, toutes sur « uber » ; 3 annonces sur 4 « taxi » ; diffusion la nuit ; Aix et Aubagne ciblés ; une annonce en 404 ; plan d'avril jamais créé ; campagne d'une autre activité dans le même compte. |
| Suivi des conversions | **1/10** | Seuls les appels depuis l'annonce sont comptés ; rien sur le site ; aucun lien entre une conversion et une course ; numéro partagé avec une autre activité. |
| Avis et réputation | **6/10** | 124 avis à 5,0 : meilleur score visible parmi les concurrents observés ; mais faux avis sur le site et absence de lien vers la fiche. Méthode d'obtention des avis : non évaluée. |
| Fidélisation | **1/10** | Presque aucun client ne revient ; aucun email, aucune relance, aucun programme. |
| Clients professionnels | **1/10** | Aucun client pro, aucune offre ni page dédiée. |
| Conformité | **2/10** | Faux avis présentés comme « Avis Google vérifiés » ; « taxi agréé » sans licence ; catégorie Google « Service de taxi » ; pas de consentement aux cookies ; photo présentée comme la flotte. |

## 3. Points à vérifier V-01 à V-10

| ID | Résultat | Preuve |
|---|---|---|
| V-01 Faux avis | **CONFIRMÉ**, et aggravé : repris dans l'extrait Google | S-05 + recherche « ZenCAR Taxi VTC Marseille avis » |
| V-02 Horaires | **CONFIRMÉ** : 24h/24 et 6 h – 23 h faux | E-10 |
| V-03 Modèle | **TRANCHÉ** : chauffeur + dispatcheur | E-05 |
| V-04 Taxi / VTC | **CONFIRMÉ** : pas de licence taxi, promesses « taxi » sur le site, les annonces et la fiche | E-06, A-09, fiche Google |
| V-05 Délai 5–10 min | **INFIRMÉ** : tenu en journée | E-11 |
| V-06 Zone | **TRANCHÉ** : Marseille + aéroport (+ forfait Aix → aéroport) ; Google Ads cible encore Aix et Aubagne | E-09, Q-09, A-03 |
| V-07 Cookies | **CONFIRMÉ** | S-04 |
| V-08 Conversions | **CONFIRMÉ** : appels depuis l'annonce uniquement | A-07 |
| V-09 Liens fiche et réseaux | **CONFIRMÉ** | C-09 |
| V-10 Fidélisation | **CONFIRMÉ** | C-04 |

## 4. Nouveaux problèmes trouvés dans les données

1. **ZenCAR absent du bloc carte** sur « taxi gare saint charles marseille », alors que sa fiche a plus d'avis et une meilleure note que les fiches présentes (124 à 5,0 contre 111 à 4,9 et 19 à 4,6) [CONSTATÉ]. Cause non établie (catégorie, adresse, distance, nom : à analyser à l'étape 04).
2. **Les faux avis sont diffusés par Google** dans l'extrait du site [CONSTATÉ].
3. **Budget Google Ads quasi inutilisé** (7 % du budget dépensé) [CALCULÉ : 25,94 € ÷ (12 € × 30) = 7,2 %], et **absence des annonces sur la recherche qui correspond au trajet n° 1**, où 4 concurrents VTC sont présents [CONSTATÉ].
4. **Toutes les conversions viennent de mots-clés « uber »**, que la doc classait en négatifs (A-06, A-08) [CONSTATÉ].
5. **Marque diluée** : 2 sociétés homonymes « ZENCAR S / A », un loueur « Zencar », une adresse 13014 sur MapQuest différente du siège 13010 [CONSTATÉ].
6. **Fiche Google nommée « zencar » en minuscules, catégorie « Service de taxi »** [CONSTATÉ].
7. **Numéro et compte Google Ads partagés avec l'activité smartphone** [CONSTATÉ + DÉCLARÉ].
8. **Écart d'économie** : une course rapporte au plus ~20 € à ZenCAR [CALCULÉ : 0,6 × 30 + 0,4 × 6] ; une conversion Google Ads (un appel) coûte 6,48 € [CONSTATÉ], sans savoir combien d'appels deviennent des courses.
9. **Écart d'objectif** : < 50 courses ZenCAR par mois aujourd'hui pour ~200 visées dans 6 mois (×4), avec presque aucun client qui revient (C-02, C-07, C-04) [DÉCLARÉ].
10. **Image d'accueil de 2,5 Mo** pour un public 100 % mobile [CONSTATÉ].

## 5. Comparaison avec les concurrents visibles

Observé sur « taxi gare saint charles marseille » et « ZenCAR Taxi VTC Marseille avis » [CONSTATÉ] :

| Concurrent | Où | Ce qu'il met en avant |
|---|---|---|
| am-driver | Annonce (2 fois) | « Taxi Marseille en 10 min », 24h/24, devis instantané |
| Naveco | Annonce | « Tarif fixe & bloqué », « évitez la queue aux stations de taxis », longue distance |
| lh-transport | Annonce | 24h/24 7j/7, immédiat ou réservation |
| AKF Transport VTC | Annonce | Véhicule précis (Mercedes EQB électrique), gares, longue distance |
| Taxi Faten / Taxi Marseille gare saint Charles | Bloc carte | 4,9 (111) / 4,6 (19), catégorie « Service de taxi » |
| TaxiNeo | Résultat naturel | Page gare dédiée : accueil nominatif, ferry Corse, calanques, forfait fixe |
| Allocab | Résultat naturel | Plateforme nationale, 4,5 (7 557 avis) |
| Zzcar | Bloc carte (recherche marque) | 5,0 (27), « Ouvert 24h/24 », catégorie « Service de chauffeur privé » |

**Ce qu'ils font mieux** [CONSTATÉ] : ils sont présents en annonce et dans le bloc carte sur la recherche gare ; ils promettent un tarif fixe, l'évitement de la file d'attente ou un accueil nominatif ; ils nomment leur véhicule réel ; les VTC utilisent la catégorie « chauffeur privé ».
**Ce que ZenCAR fait mieux** [CONSTATÉ + DÉCLARÉ] : la meilleure note avec le plus d'avis parmi les fiches locales observées (5,0 / 124) ; un forfait gare affiché à 25 € ; plus de 50 partenaires pour absorber le volume.

## 6. Note globale

**29 / 100** [CALCULÉ : somme des 10 notes sur 10].
Lecture : un vrai capital (avis, réseau de partenaires, offre claire, demande existante sur la gare) masqué par des promesses fausses, un site et des annonces qui parlent de « taxi », et une acquisition payante qui ne tourne presque pas.

## 7. Non évalué, et donnée nécessaire

| Point | Donnée nécessaire |
|---|---|
| Expérience réelle de réservation en ligne (frictions, abandon au code SMS) | Test du parcours sur mobile + nombre de réservations commencées et terminées |
| Vitesse du site (score mobile) | Rapport PageSpeed Insights |
| Contenu du conteneur Google Tag Manager | Accès GTM |
| Détail de la fiche Google (adresse affichée, horaires, photos, posts, questions-réponses, zone desservie) | Accès Google Business Profile (Q-12) |
| Contenu des 124 avis (thèmes, gammes, partenaires) | Lecture des avis (étape 05) |
| Part des appels Google Ads qui deviennent des courses | Comptage de 2 semaines (Q-01) |
| Qualité des courses dispatchées | Retours clients par course |
| Recherches aéroport, VTC et longue distance dans Google | Relevé des résultats (étapes 03 et 04) |
| Données Google Ads sur plus de 30 jours | Export sur 12 mois |

## Auto-critique

- Constats retirés car génériques : aucun ; chaque constat renvoie à une donnée du registre ou à une recherche datée.
- Points fragiles : les résultats Google viennent de 3 recherches dans un seul navigateur (personnalisation possible) ; Google Ads porte sur 19 clics, donc aucune conclusion statistique ; les chiffres d'activité sont des ordres de grandeur déclarés.
- Aucune solution n'est proposée ici, conformément à l'étape 01.

## Questions restées ouvertes

Non bloquantes pour le diagnostic, à traiter aux étapes suivantes : Q-04 (Quick Talk), Q-05 (mots-clés « uber »), Q-07 (annonces « taxi »), Q-08 (séparer l'activité smartphone), Q-10 (forfaits Sormiou), **Q-11** (catégorie et nom de la fiche Google), **Q-12** (adresse affichée par la fiche et les annuaires).
