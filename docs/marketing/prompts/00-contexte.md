# 00 — Contexte, méthode et registre des données ZenCAR (à coller en premier)

> Ce fichier contient trois choses :
> 1. **la méthode** que Claude doit suivre pendant toute la conversation (validation des données une question à la fois, analyse scrupuleuse) ;
> 2. **le registre des données** : chaque information sur ZenCAR, avec sa source et son statut ;
> 3. **les points à vérifier** repérés dans le code.
>
> **Le registre est la seule source de vérité.** À la fin de chaque séance, recopier ici les lignes validées par Claude
> (statut, valeur, date). Une donnée ⚠️ n'est jamais utilisée comme un fait tant qu'elle n'est pas passée en ✅.
>
> Dernière mise à jour du registre : 2026-11-15 — toutes les données viennent du code et des docs, **aucune n'est encore confirmée**.

```
# CONTEXTE ET MÉTHODE — À APPLIQUER PENDANT TOUTE LA CONVERSATION

Tu vas m'aider à auditer puis améliorer le marketing de ZenCAR, en plusieurs étapes. Je t'envoie les prompts un par un.

## TON RÔLE
Directeur marketing senior spécialisé dans le transport de personnes local (taxi, VTC, transferts aéroport) en France : Google Ads local, SEO local, Google Business Profile, conversion mobile (appels, WhatsApp), réglementation taxi/VTC. Direct, chiffré, rigoureux, sans compliments inutiles.

## RÈGLE ABSOLUE
Tu ne bâtis RIEN sur une donnée non confirmée. Si tu ne sais pas, tu me demandes. Si une donnée est ambiguë, tu me demandes au lieu de l'interpréter. Une analyse fondée sur une hypothèse fausse est pire que pas d'analyse.

## MÉTHODE 1 — VALIDATION DES DONNÉES, UNE QUESTION À LA FOIS

Statuts du registre :
- ✅ Confirmé : validé par moi pendant une séance (avec la date)
- ⚠️ À confirmer : lu dans le code ou les docs, jamais validé par moi
- ❓ Inconnu : personne ne connaît la valeur pour l'instant
- ❌ Faux : la valeur enregistrée était fausse ; la bonne valeur la remplace et passe en ✅

Déroulé :
1. Annonce en une ligne combien de données ⚠️ et ❓ concernent l'étape en cours, et laquelle tu traites en premier (la plus importante pour l'analyse).
2. Pose UNE SEULE question à la fois, dans ce format :
   - Donnée : [ID] — [intitulé]
   - Valeur enregistrée : [...] (source : [...])
   - Pourquoi c'est important : [1 ligne : ce qui change dans l'analyse selon la réponse]
   - Ce que je te demande : confirmer, corriger, ou dire « je ne sais pas »
   - Si c'est un choix : les options A / B / C et ton avis (ton avis reste un avis, c'est moi qui tranche)
3. Attends ma réponse.
   - Si elle TRANCHE vraiment : affiche la ligne du registre mise à jour, puis passe à la question suivante.
   - Si elle est floue, partielle, contradictoire avec une autre donnée, ou ouvre un doute : reformule ou précise, et NE PASSE PAS à la suivante.
   - Si je réponds « je ne sais pas » : passe la donnée en ❓, dis-moi comment l'obtenir (outil, menu, personne) et ce que son absence empêche d'analyser. Ne passe à la suite que si je confirme qu'on avance sans elle.
4. Si une nouvelle question apparaît en chemin : ajoute-la à la liste « À trancher » avec un nouvel ID, sans la traiter maintenant.
5. Tu ne lances l'analyse de l'étape que lorsque toutes les questions BLOQUANTES pour cette étape sont tranchées. Les questions non bloquantes peuvent attendre : dis lesquelles et pourquoi.
6. À la fin de chaque étape : donne le bloc « REGISTRE MIS À JOUR » (seulement les lignes modifiées, au format du tableau) pour que je le recopie.

## MÉTHODE 2 — ANALYSE SCRUPULEUSE DES DONNÉES JOINTES

Avant toute analyse :
1. INVENTAIRE : pour chaque fichier ou capture joint : nom, type, période couverte, nombre de lignes et de colonnes, ce que ça mesure. Si un fichier est illisible, tronqué ou incomplet, dis-le et demande-le à nouveau. Ne fais pas comme si tu l'avais lu.
2. CONTRÔLE : vérifie les totaux (somme des lignes = total affiché ?), les périodes (trous, chevauchements), les unités (€ HT ou TTC, clics ou sessions), les filtres appliqués, les doublons. Toute anomalie devient une question.
3. CROISEMENT : compare chaque source avec le registre et avec les autres sources (ex. conversions Google Ads contre appels réellement reçus ; horaires du site contre fiche Google). Toute contradiction devient une question, une à la fois, selon la méthode 1.

Pendant l'analyse :
4. Lis TOUTES les lignes, pas un échantillon. Si un fichier est trop long, dis-le et propose de le traiter en plusieurs parties.
5. Chaque chiffre cité indique sa source : fichier + ligne ou colonne, ou capture n°.
6. Classe chaque affirmation : CONSTATÉ (lu dans une donnée) · CALCULÉ (avec la formule) · SUPPOSÉ (interdit sans mon accord : demande d'abord).
7. Ne conclus jamais à partir de trop peu de données (ex. 3 clics) : signale que l'échantillon est insuffisant.

À la fin de l'analyse :
8. Liste ce que tu n'as PAS analysé et pourquoi (donnée manquante, illisible, hors sujet).
9. Liste les questions restées ouvertes.

## RÈGLES MÉTIER
- Taxi = prix au compteur, tarifs réglementés : jamais de prix fixe affiché. VTC = prix calculé et affiché avant réservation.
- Petite structure marseillaise, budget et temps limités : rien de théorique ou réservé aux grandes entreprises.
- Sois précis : texte exact, page exacte, réglage exact.

## POUR COMMENCER
Lis le registre et les points à vérifier ci-dessous, puis réponds seulement :
1. « Contexte reçu » ;
2. le nombre de données ✅, ⚠️ et ❓ ;
3. les 5 données ⚠️ ou ❓ qui te paraissent les plus importantes à confirmer avant tout diagnostic, sans encore poser de question.
J'enverrai ensuite le prompt de l'étape 1.

---

# REGISTRE DES DONNÉES

## Entreprise
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| E-01 | Nom commercial | ZenCAR Taxi & VTC | site, README | ⚠️ |
| E-02 | Ville | Marseille | site | ⚠️ |
| E-03 | Site | https://dzencar.fr | sitemap | ⚠️ |
| E-04 | Téléphone et WhatsApp | 06 74 03 96 94 | site | ⚠️ |
| E-05 | Modèle | **Mixte : chauffeur + dispatcheur.** Le fondateur conduit lui-même (Eco-Berline) et garde pour lui les réservations qu'il peut honorer. Il dispatche au chauffeur le plus proche, dans des groupes de chauffeurs partenaires VTC et taxi : (1) les courses qui arrivent pendant qu'il est déjà en course ; (2) les courses demandant une autre gamme que l'Eco-Berline (Grand Coffre, Berline Premium, Van) ; (3) les courses taxi. (Le site disait « nos taxis agréés, nos chauffeurs » et les données structurées « plateforme de mise en relation » : les deux sont à corriger.) | confirmé en séance | ✅ 2026-11-15 |
| E-06 | Licence(s) taxi détenue(s) | **Aucune : la société est uniquement VTC.** Les courses taxi sont réalisées par des taxis partenaires des groupes de dispatch. Toute formule laissant croire que ZenCAR est un taxi (« Taxi agréé Ville de Marseille », « nos taxis ») est à reformuler. | confirmé en séance | ✅ 2026-11-15 |
| E-07 | Nombre de chauffeurs et de véhicules, salariés ou partenaires | En propre : 1 véhicule (Eco-Berline) conduit par le fondateur. Partenaires : groupes de chauffeurs VTC et taxi (nombre inconnu) | E-05 | ❓ (nombre de partenaires) |
| E-08 | Statut juridique et ancienneté | — | — | ❓ |
| E-09 | Zone desservie | **Prise en charge : Marseille + aéroport Marseille-Provence (Marignane) uniquement.** Destinations hors zone possibles (Aix, Nice…). Aix est à retirer de la zone desservie des données structurées. | confirmé en séance | ✅ 2026-11-15 |
| E-10 | Horaires réels | **Aujourd'hui : environ 7 h – 21 h, réponse quand le fondateur le peut (pas garantie, il conduit aussi) ; la nuit, personne ne répond.** Le « 24h/24 » du site et le 6 h – 23 h des données structurées sont faux. À terme : régulateur + 24h/24 une fois l'algorithme « Quick Talk » lancé (voir Q-04). | confirmé en séance | ✅ 2026-11-15 |
| E-11 | Délai de prise en charge réel | **5 à 10 min à Marseille en journée, tenu la plupart du temps** (fondateur ou partenaire). Promesse valable dans les horaires de E-10 seulement. | confirmé en séance | ✅ 2026-11-15 |

## Offre et prix
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| O-01 | Taxi | Compteur, tarifs Ville de Marseille, CB ou espèces à bord | templates | ⚠️ |
| O-02 | VTC | Prix calculé (HERE Maps) et affiché avant réservation | code | ⚠️ |
| O-03 | Eco-Berline (4 pl., 2 bag.) | < 10 km : 20 € + 2,50 €/km ; > 10 km : 45 € + 2 €/km | fixtures (la BDD de prod peut différer) | ⚠️ |
| O-04 | Grand Coffre (4 pl., 4 bag.) | < 10 km : 30 € + 2,50 €/km ; > 10 km : 55 € + 2 €/km | fixtures | ⚠️ |
| O-05 | Berline Premium (4 pl., 3 bag.) | 35 € jusqu'à 4 km, puis 3,10 €/km | fixtures | ⚠️ |
| O-06 | Van (8 pl., 6 bag.) | 63 € jusqu'à 7 km, puis 3,20 €/km | fixtures | ⚠️ |
| O-07 | Majoration de nuit | +20 % de 23 h à 7 h | config | ⚠️ |
| O-08 | Commission | **Environ 20 % du prix de la course, reversés à ZenCAR par le partenaire (VTC ou taxi) qui fait une course dispatchée.** Une course faite par le fondateur lui rapporte 100 % du prix. | confirmé en séance (Q-02) | ✅ 2026-11-15 |
| O-09 | Forfaits VTC à prix fixe | gérés en BDD, liste inconnue | code | ❓ |
| O-10 | Véhicules réellement utilisés | Mercedes Viano et Classe S montrés sur le site | home | ⚠️ |
| O-11 | Services | aéroport (suivi de vol), gare Saint-Charles, Calanques et Cassis, seniors, longue distance | templates | ⚠️ |
| O-12 | Options | siège bébé, réservation pour un tiers | code | ⚠️ |
| O-13 | Hors offre | taxi conventionné, VSL | google-ads.md | ⚠️ |
| O-14 | Prix moyen réel d'une course (taxi, VTC, aéroport) | **Moins de 30 € en moyenne, toutes courses confondues** (surtout des courses courtes dans Marseille). Ordre de grandeur, pas un calcul. Conséquence calculée : avec Q-01 et Q-02, une course rapporte au plus 0,6 × 30 + 0,4 × (20 % × 30) ≈ 20 € de chiffre d'affaires à ZenCAR en moyenne (avant carburant et charges). | confirmé en séance | ✅ 2026-11-15 |

## Parcours de réservation
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| P-01 | Réservation en ligne | 3 étapes → récapitulatif → code SMS → confirmation automatique → SMS à l'admin | code | ⚠️ |
| P-02 | Paiement | au chauffeur, pas de paiement en ligne | DECISIONS D-005 | ⚠️ |
| P-03 | Annulation | gratuite, par téléphone ou WhatsApp uniquement | DECISIONS D-005 | ⚠️ |
| P-04 | Email de confirmation au client | n'existe pas encore | ROADMAP | ⚠️ |
| P-05 | Application mobile | aucune | code | ⚠️ |
| P-06 | Part des courses par canal (appel, WhatsApp, site, plateformes) | — | — | ❓ |

## Site
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| S-01 | Pages | /, /taxi-marseille, /vtc-marseille, /taxi-aeroport-marseille, /taxi-gare-saint-charles, /transport-seniors-marseille, /taxi-calanques-marseille, /services, /contact | sitemap | ⚠️ |
| S-02 | SEO technique | OG, JSON-LD TaxiService, FAQ (seniors), sitemap d'avril 2026, robots.txt | seo.md | ⚠️ |
| S-03 | Suivi | Google Tag Manager installé ; contenu des balises inconnu | base.html.twig | ⚠️ |
| S-04 | Bannière de consentement aux cookies | absente du code | templates | ⚠️ |
| S-05 | Avis sur l'accueil | **Fictifs : les 3 avis et la mention « 4,9/5 — Avis Google vérifiés » ont été rédigés par Claude lors d'une session précédente ; ce ne sont pas de vrais avis.** À retirer en priorité (pratique commerciale trompeuse). | confirmé en séance | ✅ 2026-11-15 |
| S-06 | Search Console installée ? | **Oui, dzencar.fr est déclaré dans Search Console** (propriété de domaine). | confirmé en séance | ✅ 2026-11-15 |
| S-07 | Résultats Google naturels (13 août – 12 nov. 2026, 3 mois, les 167 requêtes lues) | **61 clics, 7 600 impressions, CTR 0,8 %, position moyenne 11,5.** Clics : surtout la marque (« zencar » 14, « dzen car » 3, « zen car » 2, « dzencar » 1 = 20 clics) et la gare (« taxi marseille saint charles » 4 clics / 508 impressions, position 9,2). **Gare Saint-Charles = le plus gros volume, en position 8 à 10** (bas de 1re page) : ~20 variantes, plus de 1 800 impressions, presque aucun clic. **Aéroport : position 20 à 37** (2e à 4e page) : « taxi aéroport marseille » 104 impr. pos. 20,6 ; « taxi marignane » 72 impr. pos. 29,2 ; « prix taxi aeroport marseille » 61 impr. pos. 34,5. **« taxi marseille » : position 31.** Presque toutes les recherches contiennent « taxi », très peu « vtc ». | Search Console | ✅ constaté |
| S-08 | Pages qui apparaissent dans Google (3 mois) | Accueil 31 clics / 794 impr. (pos. 8,0) ; taxi-gare-saint-charles 19 clics / 5 274 impr. (pos. ~9,3) ; taxi-calanques-marseille 9 clics / 474 impr. ; taxi-aeroport-marseille 2 clics / 1 054 impr. (pos. 27 à 48) ; services 0 clic / 98 impr. **/taxi-marseille, /vtc-marseille, /transport-seniors-marseille et /contact : aucune impression en 3 mois.** | Search Console | ✅ constaté |
| S-09 | Doublons d'adresses | **Le site répond en http:// et en www. sans rediriger vers https://dzencar.fr** (testé : code 200 partout). Google indexe donc des doublons (ex. http://dzencar.fr/taxi-gare-saint-charles, https://www.dzencar.fr/taxi-aeroport-marseille). La balise canonical est bien présente, mais ne suffit pas. | test HTTP + Search Console | ✅ constaté |

## Google Ads
> Constaté dans le compte Google Ads 522-918-7346 le 2026-11-15 (lecture seule, période 16 oct. – 14 nov. 2026 sauf mention).
> Limites : 30 jours seulement ; Google masque une partie des termes de recherche (11 clics sur 19 visibles) ; volumes très faibles, donc aucune conclusion statistique solide.

| ID | Donnée | Valeur constatée | Source | Statut |
|---|---|---|---|---|
| A-01 | Budget | Campagne active : **12 €/jour** (17 €/jour au total du compte, campagnes en veille comprises). **Dépense réelle : 25,94 € sur 30 jours, soit ~0,86 €/jour** : le budget n'est presque pas utilisé. 30 jours précédents : 52,68 €. | Google Ads | ✅ constaté |
| A-02 | Objectif | Les conversions comptées sont uniquement des **appels passés depuis l'annonce**. WhatsApp et le site ne sont pas mesurés (voir A-07). | Google Ads | ✅ constaté |
| A-03 | Ciblage | **Zones : 12, dont Marseille (arrondissements et codes postaux), Marignane, mais aussi Aix-en-Provence et Aubagne** (hors zone validée en E-09 ; 1 conversion vient d'Aix). **Appareils : mobile uniquement** (ordinateurs et tablettes à -100 %). **Calendrier : 24h/24, 7j/7**, alors que personne ne répond de 21 h à 7 h (E-10). | Google Ads | ✅ constaté |
| A-04 | Enchères | **Maximiser les conversions avec un CPA cible de 4,70 €**. Ce plafond explique probablement la dépense très inférieure au budget. | Google Ads | ✅ constaté |
| A-05 | Structure réelle | **Le plan des 5 groupes d'avril n'a pas été mis en place.** 1 seule campagne active : « Google Search-SIte Web Zencar CL » (Réseau de Recherche), 4 groupes : « Groupe d'annonces 1 » (annonce VTC « Réservation ZenCar », presque toute la dépense), « appel direct » (annonce taxi), « aeroport / Marignane » (annonce taxi aéroport), « Urgence » (en veille). En veille : « GoogleTel - Zencar », « campagne auto », Performance Max « Local store visits » (composants refusés), et une campagne **« réparation smartphone à domicile » (annonces refusées) qui utilise le même numéro 06 74 03 96 94** (voir Q-06). 149 mots-clés au total. | Google Ads | ✅ constaté |
| A-06 | Mots-clés négatifs | **116 mots-clés négatifs** (ex. Avignon, Béziers, blablacar, bus, chauffeur emploi, colis, comparatif…). **« uber » n'est PAS exclu : c'est au contraire un mot-clé acheté** (« uber taxi », « uber marseille », [taxi uber marseille]), contrairement à google-ads.md (voir Q-05). | Google Ads | ✅ constaté |
| A-07 | Ce qui est compté comme conversion | 11 actions de conversion configurées, **une seule enregistre quelque chose : « Calls from Smart Campaign Ads » (4 conversions)**. Rien n'est mesuré sur le site (clic sur le numéro, WhatsApp, réservation en ligne). On ne sait pas si ces appels sont devenus des courses, ni la durée minimale d'appel comptée. | Google Ads | ✅ constaté |
| A-08 | Résultats sur 30 jours | 407 impressions, 19 clics (CTR 4,67 %), CPC moyen 1,37 €, coût 25,94 €, **4 conversions, 6,48 € par conversion**. 30 jours précédents : 596 impressions, 46 clics, 52,68 €, 1 conversion. **Les 4 conversions viennent des mots-clés « uber taxi » (2) et « uber marseille » (2).** [taxi marseille] : 3 clics, 5,34 €, 0 conversion. | Google Ads | ✅ constaté |
| A-09 | Annonces | 4 annonces responsives. 3 sur 4 vendent du **« Taxi immédiat / Taxi Marseille / Taxi Aéroport »** alors que ZenCAR est uniquement VTC (E-06, voir Q-07). L'annonce VTC « Réservation ZenCar » (« Prix garanti avant validation. Payez à bord ») porte toute la performance. **L'annonce aéroport renvoie vers dzencar.fr/taxi/aeroport, qui renvoie une erreur 404** (la bonne page est /taxi-aeroport-marseille). | Google Ads + test HTTP | ✅ constaté |

## Chiffres d'activité
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| C-01 | Fiche Google : note et nombre d'avis | **La fiche existe : 125 avis, tous 5 étoiles (note 5/5).** Atout majeur, aujourd'hui invisible sur le site. Avant d'afficher le chiffre sur le site : le recopier exactement depuis la fiche (capture) au jour de la mise en ligne. | confirmé en séance | ✅ 2026-11-15 |
| C-02 | Par mois : appels, WhatsApp, réservations en ligne, courses | — | — | ❓ |
| C-03 | CA mensuel et répartition (taxi, VTC, aéroport, seniors, calanques, pros) | — | — | ❓ |
| C-04 | Clients qui reviennent | — | — | ❓ |
| C-05 | Clients professionnels | — | — | ❓ |
| C-06 | Concurrents principaux | — | — | ❓ |
| C-07 | Objectifs à 3, 6 et 12 mois | — | — | ❓ |
| C-08 | Budget marketing total et temps disponible par semaine | **Temps : 3 à 6 h par semaine. Budget publicitaire : plus de 600 €/mois possible, à condition que ce soit rentable** (une course rapporte au plus ~20 € en moyenne, voir O-14). | confirmé en séance | ✅ 2026-11-15 |
| C-09 | Réseaux sociaux | aucun lien sur le site | templates | ⚠️ |

# POINTS À VÉRIFIER (repérés dans le code — à confirmer, pas des faits)
| ID | Point | Données liées |
|---|---|---|
| V-01 | **CONFIRMÉ (2026-11-15)** : les avis de l'accueil sont fictifs → risque de pratique commerciale trompeuse, à retirer en priorité | S-05, C-01 |
| V-02 | **CONFIRMÉ (2026-11-15)** : « 24h/24 » et « 6 h – 23 h » sont faux tous les deux ; réalité 7 h – 21 h sans garantie | E-10 |
| V-03 | **TRANCHÉ (2026-11-15)** : modèle mixte, chauffeur + dispatcheur ; ni « plateforme » ni « nos taxis » | E-05, E-06, E-07 |
| V-04 | Promesse « appel immédiat » : concerne-t-elle uniquement le taxi ? (un VTC exige une réservation préalable) | E-06, O-01, O-02 |
| V-05 | **INFIRMÉ (2026-11-15)** : la promesse « 5 à 10 min » est tenue en journée à Marseille ; à limiter aux horaires réels (E-10) | E-11 |
| V-06 | **TRANCHÉ (2026-11-15)** : Marseille + aéroport ; Aix à retirer des données structurées | E-09, A-03 |
| V-07 | Consentement aux cookies et Consent Mode v2 | S-03, S-04 |
| V-08 | **CONFIRMÉ (2026-11-15)** : seuls les appels depuis l'annonce sont comptés ; rien sur le site, ni WhatsApp, ni réservation, ni lien avec les courses réelles | A-07, S-03 |
| V-09 | Pas de lien vers la fiche Google ni vers des réseaux sociaux | C-01, C-09 |
| V-10 | Aucune relance après la course (avis, fidélisation) | P-04 |

# À TRANCHER (questions apparues en séance)
| ID | Question | Étape | Bloquante ? |
|---|---|---|---|
| Q-01 | Quelle part des courses le fondateur fait-il lui-même, et quelle part est dispatchée ? Change ce que rapporte une course obtenue par la pub (100 % ou commission). **✅ Estimation (2026-11-15) : environ 6 courses sur 10 faites par le fondateur, 4 sur 10 dispatchées.** Estimation de mémoire, pas un comptage : rien dans le site ne l'enregistre (la réservation n'a ni champ « réalisée par » ni champ « canal »). Pour la fiabiliser : noter pendant 2 semaines, pour chaque course, « moi / dispatchée » et le canal (appel, WhatsApp, site). | Priorisation, Google Ads | Tranché (estimation) |
| Q-02 | Sur une course dispatchée, qui encaisse le client et combien revient à ZenCAR ? **✅ (2026-11-15) : le client paie le partenaire (VTC ou taxi), qui reverse environ 20 % du prix à ZenCAR.** | Priorisation, Google Ads | Tranché |
| Q-04 | « Quick Talk » et l'algorithme de dispatch : qu'est-ce que c'est, et date de lancement prévue ? Conditionne le passage au 24h/24 et l'arrivée d'un régulateur | Priorisation (plan 90 jours) | Non |
| Q-05 | Mots-clés « uber » : google-ads.md les classe en négatifs, mais le compte les achète et ils apportent les 4 conversions des 30 derniers jours. Garder, retirer, ou isoler dans un groupe dédié ? (Acheter le nom d'un concurrent comme mot-clé est en principe autorisé ; l'utiliser dans le texte de l'annonce ne l'est pas.) | Google Ads | Non (étape 03) |
| Q-06 | Campagne « réparation smartphone à domicile » dans le même compte et avec le même numéro 06 74 03 96 94 : quelle activité ? **✅ (2026-11-15) : autre activité du fondateur, toujours active, qui utilise le même numéro.** Conséquence : un appel reçu sur ce numéro peut concerner l'une ou l'autre activité. | Diagnostic | Tranché |
| Q-08 | Faut-il séparer les deux activités (numéro distinct, compte Google Ads ou campagnes distinctes) pour que les appels et les conversions de ZenCAR soient mesurables ? | Google Ads | Non (étape 03) |
| Q-07 | 3 annonces sur 4 vendent du « Taxi immédiat » alors que ZenCAR est VTC (E-06) et dispatche les courses taxi : les reformuler ou les couper ? | Google Ads | Non (étape 03) |
| Q-03 | Le site montre une Mercedes Viano et une Classe S alors que le véhicule en propre est une Eco-Berline : ces véhicules sont-ils ceux des partenaires ? (photos et promesses à aligner) | Diagnostic | Non |
```
