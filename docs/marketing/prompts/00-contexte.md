# 00 — Contexte, méthode et registre des données ZenCAR (à coller en premier)

> Ce fichier contient trois choses :
> 1. **la méthode** que Claude doit suivre pendant toute la conversation (validation des données une question à la fois, analyse scrupuleuse) ;
> 2. **le registre des données** : chaque information sur ZenCAR, avec sa source et son statut ;
> 3. **les points à vérifier** repérés dans le code.
>
> **Le registre est la seule source de vérité.** À la fin de chaque séance, recopier ici les lignes validées par Claude
> (statut, valeur, date). Une donnée ⚠️ n'est jamais utilisée comme un fait tant qu'elle n'est pas passée en ✅.
>
> Dernière mise à jour du registre : 2026-11-15 — première séance de validation terminée : toutes les données du registre sont confirmées par le fondateur ou constatées (code, site en ligne, Google Ads, Search Console). Restent ouvertes les questions « À trancher » non bloquantes.

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
| E-01 | Nom commercial | **ZenCAR Taxi & VTC** (nom du site). Société exploitante : **SIMPLICI CAB** (voir E-08). | site + mémoire projet | ✅ 2026-11-15 |
| E-02 | Ville | Marseille ; siège au 12 rue Antoine Ré, 13010 Marseille | mémoire projet | ✅ 2026-11-15 |
| E-03 | Site | https://dzencar.fr (répond en ligne ; voir S-09 pour les doublons http/www) | test HTTP | ✅ constaté |
| E-04 | Téléphone et WhatsApp | 06 74 03 96 94, **partagé avec l'activité réparation de smartphones** (Q-06) | site + séance | ✅ 2026-11-15 |
| E-05 | Modèle | **Mixte : chauffeur + dispatcheur.** Le fondateur conduit lui-même (Eco-Berline) et garde pour lui les réservations qu'il peut honorer. Il dispatche au chauffeur le plus proche, dans des groupes de chauffeurs partenaires VTC et taxi : (1) les courses qui arrivent pendant qu'il est déjà en course ; (2) les courses demandant une autre gamme que l'Eco-Berline (Grand Coffre, Berline Premium, Van) ; (3) les courses taxi. (Le site disait « nos taxis agréés, nos chauffeurs » et les données structurées « plateforme de mise en relation » : les deux sont à corriger.) | confirmé en séance | ✅ 2026-11-15 |
| E-06 | Licence(s) taxi détenue(s) | **Aucune : la société est uniquement VTC.** Les courses taxi sont réalisées par des taxis partenaires des groupes de dispatch. Toute formule laissant croire que ZenCAR est un taxi (« Taxi agréé Ville de Marseille », « nos taxis ») est à reformuler. **Formulation décidée (2026-11-15) : « Réseau de taxis partenaires agréés », au compteur, tarifs Ville de Marseille.** | confirmé en séance | ✅ 2026-11-15 |
| E-07 | Nombre de chauffeurs et de véhicules, salariés ou partenaires | En propre : 1 véhicule (Toyota hybride, Eco-Berline) conduit par le fondateur, aucun salarié. **Partenaires : plus de 50 chauffeurs VTC et taxi** dans les groupes de dispatch : il y a presque toujours quelqu'un de disponible en journée. | confirmé en séance | ✅ 2026-11-15 |
| E-08 | Statut juridique et ancienneté | **SAS SIMPLICI CAB**, créée le 01/01/2024 (moins de 2 ans), SIREN 982 953 382, registre VTC EVTC094240137, code NAF 49.32Z | mémoire projet (infos fournies par le fondateur) | ✅ 2026-11-15 |
| E-09 | Zone desservie | **Prise en charge : Marseille + aéroport Marseille-Provence (Marignane) uniquement.** Destinations hors zone possibles et à développer (longue distance, voir O-11). **Exception (Q-09) : le forfait Aix → aéroport (59 €) est maintenu.** Aix est à retirer de la zone desservie générale des données structurées. | confirmé en séance | ✅ 2026-11-15 |
| E-10 | Horaires réels | Jusqu'au 2026-11-15 : environ 7 h – 21 h, réponse quand le fondateur le peut, personne la nuit. **À partir de la semaine du 2026-11-16 : un régulateur répond aussi la nuit → 24h/24 réel** (déclaré). Le fondateur coupe la pub à la main quand il ne travaille pas (déclaré ; le calendrier Google Ads est pourtant réglé sur « tout le temps », voir Q-13). Décision : **le site garde « 24h/24 »** ; les horaires des données structurées passent à 00:00 – 23:59 partout. | confirmé en séance | ✅ 2026-11-15 |
| E-11 | Délai de prise en charge réel | **5 à 10 min à Marseille en journée, tenu la plupart du temps** (fondateur ou partenaire). Promesse valable dans les horaires de E-10 seulement. | confirmé en séance | ✅ 2026-11-15 |

## Offre et prix
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| O-01 | Taxi | Courses taxi réalisées par des **taxis partenaires** (E-06) : compteur, tarifs Ville de Marseille, CB ou espèces à bord. Jamais de prix fixe affiché. | templates + E-06 | ✅ 2026-11-15 |
| O-02 | VTC | Prix calculé (HERE Maps, grille O-03 à O-07) et affiché avant réservation | code | ✅ constaté |
| O-03 | Eco-Berline (4 pl., 2 bag.) | < 10 km : 20 € + 2,50 €/km ; > 10 km : 45 € + 2 €/km | fixtures, confirmé en séance | ✅ 2026-11-15 |
| O-04 | Grand Coffre (4 pl., 4 bag.) | < 10 km : 30 € + 2,50 €/km ; > 10 km : 55 € + 2 €/km | fixtures, confirmé en séance | ✅ 2026-11-15 |
| O-05 | Berline Premium (4 pl., 3 bag.) | 35 € jusqu'à 4 km, puis 3,10 €/km | fixtures, confirmé en séance | ✅ 2026-11-15 |
| O-06 | Van (8 pl., 6 bag.) | 63 € jusqu'à 7 km, puis 3,20 €/km | fixtures, confirmé en séance | ✅ 2026-11-15 |
| O-07 | Majoration de nuit | +20 % de 23 h à 7 h | config, confirmé en séance | ✅ 2026-11-15 |
| O-08 | Commission | **Environ 20 % du prix de la course, reversés à ZenCAR par le partenaire (VTC ou taxi) qui fait une course dispatchée.** Une course faite par le fondateur lui rapporte 100 % du prix. | confirmé en séance (Q-02) | ✅ 2026-11-15 |
| O-09 | Forfaits VTC à prix fixe | Affichés sur l'accueil le 2026-11-15 : **Marseille centre → Gare Saint-Charles 25 €** · **Marseille → Aéroport Marignane 59 €** · **Aix-en-Provence → Aéroport Marignane 59 €** · **Marseille → Calanques 59 €** · « chemin de sormiou → chateau sormiou, calanque de sormiou » 30 € · « chateau sormiou, calanque de sormiou 13009 marseille → chemin de sormiou 13009 marseille » 40 €. Un formulaire « Réservation rapide » propose une prise en charge « Maintenant – 10 min ». À noter : le forfait Aix → Aéroport sort de la zone E-09 (Q-09) ; les deux forfaits Sormiou ont des libellés bruts (minuscules, adresses complètes) et des prix différents à l'aller et au retour (Q-10). | site en ligne | ✅ constaté |
| O-10 | Véhicules réellement utilisés | **Véhicule du fondateur : Toyota hybride (gamme Prius / Corolla / C-HR), catégorie Eco-Berline.** Les autres gammes (Grand Coffre, Berline Premium, Van) sont assurées par des partenaires. **La photo « Mercedes Viano et Classe S — Flotte ZenCAR » de l'accueil est une image d'illustration** : ce ne sont ni les véhicules du fondateur ni ceux des partenaires. Légende et image à remplacer (promesse trompeuse). | confirmé en séance | ✅ 2026-11-15 |
| O-11 | Services | **Tous réels :** aéroport avec suivi de vol, gare Saint-Charles, Calanques et Cassis, **service spécial seniors** (aide à monter et descendre, accompagnement, attente), **longue distance au départ de Marseille, à mettre davantage en avant** (aucune page dédiée aujourd'hui). (Première réponse incomplète, corrigée par confirmation dans la même séance.) | confirmé en séance | ✅ 2026-11-15 |
| O-12 | Options | Siège bébé, réservation pour un tiers (invité) : champs présents dans le formulaire | code | ✅ constaté |
| O-13 | Hors offre | **Aucun transport conventionné** (taxi conventionné CPAM, VSL) : ni ZenCAR ni via les partenaires. Le service seniors (O-11) n'est donc pas remboursé par la Sécurité sociale : à dire clairement sur la page seniors. | confirmé en séance | ✅ 2026-11-15 |
| O-14 | Prix moyen réel d'une course (taxi, VTC, aéroport) | **Moins de 30 € en moyenne, toutes courses confondues** (surtout des courses courtes dans Marseille). Ordre de grandeur, pas un calcul. Conséquence calculée : avec Q-01 et Q-02, une course rapporte au plus 0,6 × 30 + 0,4 × (20 % × 30) ≈ 20 € de chiffre d'affaires à ZenCAR en moyenne (avant carburant et charges). | confirmé en séance | ✅ 2026-11-15 |

## Parcours de réservation
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| P-01 | Réservation en ligne | 3 étapes → récapitulatif → code SMS → confirmation automatique → SMS à l'admin. Plus un formulaire « Réservation rapide » des forfaits (prise en charge « Maintenant – 10 min ») | code + site | ✅ constaté |
| P-02 | Paiement | Au chauffeur (CB ou espèces), pas de paiement en ligne | décision D-005 du fondateur | ✅ 2026-11-12 |
| P-03 | Annulation | Gratuite, par téléphone ou WhatsApp uniquement ; seul l'admin annule | décision D-005 du fondateur | ✅ 2026-11-12 |
| P-04 | Email de confirmation au client | N'existe pas (tâche « Service Mailer » en attente dans la ROADMAP) | code + ROADMAP | ✅ constaté |
| P-05 | Application mobile | Aucune | code | ✅ constaté |
| P-06 | Part des courses par canal (appel, WhatsApp, site, plateformes) | **Sources actives : clients réguliers, Google (pub, fiche, site), groupes de chauffeurs, plateformes (Uber, Bolt, Heetch…). La source n° 1 de l'activité du fondateur, ce sont les plateformes.** Les courses plateformes sont **en plus** des < 50 courses ZenCAR par mois (C-02) et ne sont pas des clients ZenCAR. Enjeu marketing : transformer une partie de cette activité plateforme en clients directs ZenCAR (sans commission plateforme). **Pour les courses ZenCAR en direct, la source n° 1 est Google** (annonce, fiche Google Maps ou site, sans détail entre les trois). | confirmé en séance | ✅ 2026-11-15 |

## Site
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| S-01 | Pages | /, /taxi-marseille, /vtc-marseille, /taxi-aeroport-marseille, /taxi-gare-saint-charles, /transport-seniors-marseille, /taxi-calanques-marseille, /services, /contact (toutes en ligne, code 200). Aucune page longue distance ni entreprises. | sitemap + test HTTP | ✅ constaté |
| S-02 | SEO technique | OG, JSON-LD TaxiService (accueil, services), canonical + robots index/follow, FAQ (seniors), sitemap daté d'avril 2026, robots.txt. Pas de redirection http/www (S-09). | code + site | ✅ constaté |
| S-03 | Suivi | Google Tag Manager GTM-WXJ6V2W4 chargé sur toutes les pages ; aucune conversion du site ne remonte dans Google Ads (A-07). Contenu du conteneur GTM non vérifié. | code + Google Ads | ✅ constaté |
| S-04 | Bannière de consentement aux cookies | **Absente du site en ligne** (aucun outil de consentement détecté) alors que GTM est chargé | site en ligne | ✅ constaté |
| S-05 | Avis sur l'accueil | **Fictifs : les 3 avis et la mention « 4,9/5 — Avis Google vérifiés » ont été rédigés par Claude lors d'une session précédente ; ce ne sont pas de vrais avis.** À retirer en priorité (pratique commerciale trompeuse). | confirmé en séance | ✅ 2026-11-15 |
| S-06 | Search Console installée ? | **Oui, dzencar.fr est déclaré dans Search Console** (propriété de domaine). | confirmé en séance | ✅ 2026-11-15 |
| S-07 | Résultats Google naturels (13 août – 12 nov. 2026, 3 mois, les 167 requêtes lues) | **61 clics, 7 600 impressions, CTR 0,8 %, position moyenne 11,5.** Clics : surtout la marque (« zencar » 14, « dzen car » 3, « zen car » 2, « dzencar » 1 = 20 clics) et la gare (« taxi marseille saint charles » 4 clics / 508 impressions, position 9,2). **Gare Saint-Charles = le plus gros volume, en position 8 à 10** (bas de 1re page) : ~20 variantes, plus de 1 800 impressions, presque aucun clic. **Aéroport : position 20 à 37** (2e à 4e page) : « taxi aéroport marseille » 104 impr. pos. 20,6 ; « taxi marignane » 72 impr. pos. 29,2 ; « prix taxi aeroport marseille » 61 impr. pos. 34,5. **« taxi marseille » : position 31.** Presque toutes les recherches contiennent « taxi », très peu « vtc ». | Search Console | ✅ constaté |
| S-08 | Pages qui apparaissent dans Google (3 mois) | Accueil 31 clics / 794 impr. (pos. 8,0) ; taxi-gare-saint-charles 19 clics / 5 274 impr. (pos. ~9,3) ; taxi-calanques-marseille 9 clics / 474 impr. ; taxi-aeroport-marseille 2 clics / 1 054 impr. (pos. 27 à 48) ; services 0 clic / 98 impr. **/taxi-marseille, /vtc-marseille, /transport-seniors-marseille et /contact : aucune impression en 3 mois.** | Search Console | ✅ constaté |
| S-09 | Doublons d'adresses | **Le site répond en http:// et en www. sans rediriger vers https://dzencar.fr** (testé : code 200 partout). Google indexe donc des doublons (ex. http://dzencar.fr/taxi-gare-saint-charles, https://www.dzencar.fr/taxi-aeroport-marseille). La balise canonical est bien présente, mais ne suffit pas. | test HTTP + Search Console | ✅ constaté |
| S-10 | Présence dans les résultats Google (3 recherches, 2026-11-15) | « taxi gare saint charles marseille » : ZenCAR **absent du bloc carte et des annonces** (4 annonces de VTC concurrents) ; site en 8e position naturelle. **L'extrait Google de dzencar.fr affiche les faux avis du site.** Recherche « zencar marseille » : homonymes ZENCAR S / ZENCAR A (fret, 13013), loueur « Zencar », fiche MapQuest au 257 bd Danielle Casanova 13014. | navigateur | ✅ constaté |
| S-11 | Poids des pages | Image principale de l'accueil : 2,5 Mo (PNG) ; HTML accueil 79 Ko | test HTTP | ✅ constaté |

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
| A-10 | Historique complet (7 août 2025 – 15 nov. 2026, ~15 mois, 2 084 termes de recherche lus en entier) | **Compte : 1 383,90 € dépensés, 872 clics, 162 conversions, 8,54 € par conversion.** Termes visibles : 834,53 € / 102,5 conv. (le reste, 527 €, est masqué par Google). Par intention [CALCULÉ] : **taxi/VTC Marseille générique** 192,85 € → 30 conv. (**6,43 €**/conv.) ; **concurrents (Uber, Bolt, Heetch…) à Marseille** 314,49 € → 43,5 conv. (**7,23 €**) ; **hors zone** (autres villes, avec ou sans nom de concurrent) 245,32 € → 23 conv. (**10,67 €**, 29 % de la dépense visible) ; **aéroport** 49,92 € → 4 conv. (**12,48 €**) ; **gare** 0,87 € → 0 (quasi absent) ; **conventionné** (hors offre, O-13) 22,35 € → 2 conv. ; autres 8,72 €. Par groupe : « Groupe d'annonces 1 » 1 244,86 € → 138 conv. (9,02 €) ; « Urgence » (en veille) 96,16 € → 19 conv. (**5,06 €**, le meilleur) ; « appel direct » 12,97 € → 4 conv. (3,24 €) ; « aeroport / Marignane » 7,79 € → 0. Certains termes sont à la fois « ajoutés » et « exclus » (ex. « uber taxi marseille », « bolt marseille »). | Google Ads, lecture seule | ✅ constaté |
| A-11 | Historique : mois, heures, concurrence, pages, gestion | **Par mois** (campagne Recherche, totaux recoupés : 1 361,79 €, 161 conv.) : oct. 2025 58,74 € / 5 conv. (11,75 €) · nov. 304,17 € / 35 (8,69 €) · déc. 221,51 € / 25 (8,86 €) · janv. 2026 246,89 € / 17 (14,52 €) · févr. 137,70 € / 7 (19,67 €) · mars 0 · **avr. 101,19 € / 21 (4,82 €) · mai 204,88 € / 45 (4,55 €)** — meilleure période, tous groupes actifs (« Urgence » 4,34 €, « appel direct » 1,60 €, groupe principal 4,89 €) · juin 6,34 € / 1 · juil.–août 0 · sept. 20,64 € / 0 · oct. 34,97 € / 1 · nov. (au 15) 24,77 € / 4. **Par heure** (toute la période) : 9 h – 18 h = le gros du volume (moyenne ~8 €/conv.) ; **19 h : 44 clics, 1,7 conv. (~27 €/conv.)** ; **nuit 21 h – 7 h : 131 clics, 180,71 €, 31 conv. (5,83 €/conv.)**, dont minuit 33 clics / 11 conv. — alors que le fondateur déclare ne pas répondre la nuit (à éclaircir : appels comptés sans réponse ? appels pris parfois ?). **Concurrence** (insights sur les enchères) : ZenCAR n'apparaît que dans **25 %** des enchères possibles, en haut de page 56 %, en 1re position 20 % ; concurrents : taximarseille13.fr (1re position 41 %), driver-plus.com, lh-transport.com, driveallure.com, rezervao.com, uber.com, taximagdi.com, taxi-vtc-marseille.fr, allo-taxi-marseille.fr, taxivtcmarseille.com, allocab.com. **Pages d'arrivée** : accueil 493 clics (459 €), /taxi-marseille 59 clics ; **281 clics sur 847 (33 %) sont des appels directs depuis l'annonce**, sans passage par le site. **Gestion** (historique des modifications, 10 oct. – 13 nov. 2026) : ~55 modifications en un mois, presque toutes depuis l'application mobile : mises en pause / réactivations quasi quotidiennes, créneaux de diffusion ajoutés puis supprimés, CPA cibles et budget modifiés plusieurs fois. | Google Ads, lecture seule | ✅ constaté |

## Chiffres d'activité
| ID | Donnée | Valeur enregistrée | Source | Statut |
|---|---|---|---|---|
| C-01 | Fiche Google : note et nombre d'avis | **La fiche existe : « zencar » (minuscules), 5,0/5, 124 avis** (constaté le 2026-11-15 ; le fondateur avait déclaré 125). Catégorie affichée : « Service de taxi ». Atout majeur, aujourd'hui invisible sur le site. Avant d'afficher le chiffre sur le site : le recopier exactement depuis la fiche au jour de la mise en ligne. | séance + recherche Google | ✅ 2026-11-15 |
| C-02 | Par mois : appels, WhatsApp, réservations en ligne, courses | **Moins de 50 courses ZenCAR par mois au total** (faites + dispatchées, clients ZenCAR en direct, **hors courses Uber/Bolt/Heetch**), soit moins de 2 par jour. Ordre de grandeur. Calculé : avec O-14 (< 30 €), Q-01 et Q-02, cela représente au plus ~1 000 € de chiffre d'affaires par mois pour ZenCAR (50 × ~20 €). Détail des appels, WhatsApp et réservations en ligne : inconnu. | confirmé en séance | ✅ 2026-11-15 |
| C-03 | CA mensuel et répartition (taxi, VTC, aéroport, seniors, calanques, pros) | **Trajet ZenCAR le plus fréquent : Gare Saint-Charles.** Cohérent avec Search Console (S-07 : la gare est la page la plus vue). CA mensuel : au plus ~1 000 € (calculé, voir C-02). Répartition détaillée : inconnue. | confirmé en séance | ✅ 2026-11-15 |
| C-04 | Clients qui reviennent | **Presque aucun : les courses ZenCAR directes viennent surtout de nouveaux clients.** Aucune fidélisation aujourd'hui (voir V-10). Pourtant la fiche Google affiche 125 avis 5 étoiles : les clients sont satisfaits mais ne reviennent pas, ou ne repassent pas par ZenCAR. | confirmé en séance | ✅ 2026-11-15 |
| C-05 | Clients professionnels | **Aucun : uniquement des particuliers.** | confirmé en séance | ✅ 2026-11-15 |
| C-06 | Concurrents principaux | **N° 1 : les autres VTC locaux** (chauffeurs et petites sociétés VTC marseillaises). Également présents : Uber / Bolt / Heetch, taxis classiques (ex. Taxi Radio Marseille), navettes et transports en commun. Noms des VTC locaux : non donnés (à relever dans les résultats Google pour l'étape 01). | confirmé en séance | ✅ 2026-11-15 |
| C-07 | Objectifs à 3, 6 et 12 mois | **À 6 mois : ~200 courses ZenCAR en direct par mois (contre < 50 aujourd'hui, soit ×4).** Stratégie : d'abord remplir le véhicule du fondateur avec des clients directs (moins de plateformes), puis dispatcher le surplus (commission), en vue du régulateur et du 24h/24. Objectifs à 3 et 12 mois : non fixés. | confirmé en séance | ✅ 2026-11-15 |
| C-08 | Budget marketing total et temps disponible par semaine | **Temps : 3 à 6 h par semaine. Budget publicitaire : plus de 600 €/mois possible, à condition que ce soit rentable** (une course rapporte au plus ~20 € en moyenne, voir O-14). | confirmé en séance | ✅ 2026-11-15 |
| C-09 | Réseaux sociaux | **Aucun compte** (ni Instagram, ni Facebook, ni TikTok). Le site n'a aucun lien vers des réseaux sociaux ni vers la fiche Google. | confirmé en séance + site | ✅ 2026-11-15 |
| C-10 | Taux de transformation des demandes | **5 à 7 demandes sur 10 (appel ou WhatsApp d'un inconnu) deviennent une course.** Ordre de grandeur, pas un comptage. | confirmé en séance | ✅ 2026-11-15 |
| C-11 | Actions refusées | **Pas de vidéos ni de réseaux sociaux.** Accepté : prospection pro, demande d'avis, nouveau numéro. | confirmé en séance | ✅ 2026-11-15 |

# POINTS À VÉRIFIER (repérés dans le code — à confirmer, pas des faits)
| ID | Point | Données liées |
|---|---|---|
| V-01 | **CONFIRMÉ (2026-11-15)** : les avis de l'accueil sont fictifs → risque de pratique commerciale trompeuse, à retirer en priorité | S-05, C-01 |
| V-02 | **CONFIRMÉ (2026-11-15)** : « 24h/24 » et « 6 h – 23 h » sont faux tous les deux ; réalité 7 h – 21 h sans garantie | E-10 |
| V-03 | **TRANCHÉ (2026-11-15)** : modèle mixte, chauffeur + dispatcheur ; ni « plateforme » ni « nos taxis » | E-05, E-06, E-07 |
| V-04 | **TRANCHÉ (2026-11-15)** : ZenCAR n'a pas de licence taxi (E-06) ; la promesse « appel immédiat » du site et des annonces doit être reformulée (taxi partenaire ou VTC réservé) — voir Q-07 | E-06, O-01, O-02 |
| V-05 | **INFIRMÉ (2026-11-15)** : la promesse « 5 à 10 min » est tenue en journée à Marseille ; à limiter aux horaires réels (E-10) | E-11 |
| V-06 | **TRANCHÉ (2026-11-15)** : Marseille + aéroport ; Aix à retirer des données structurées | E-09, A-03 |
| V-07 | **CONFIRMÉ (2026-11-15)** : aucune bannière de consentement sur le site en ligne alors que GTM est chargé | S-03, S-04 |
| V-08 | **CONFIRMÉ (2026-11-15)** : seuls les appels depuis l'annonce sont comptés ; rien sur le site, ni WhatsApp, ni réservation, ni lien avec les courses réelles | A-07, S-03 |
| V-09 | **CONFIRMÉ (2026-11-15)** : aucun lien vers la fiche Google (125 avis 5 étoiles) et aucun réseau social | C-01, C-09 |
| V-10 | **CONFIRMÉ (2026-11-15)** : aucune relance après la course, et presque aucun client ne revient (C-04) | P-04, C-04 |

# À TRANCHER (questions apparues en séance)
| ID | Question | Étape | Bloquante ? |
|---|---|---|---|
| Q-01 | Quelle part des courses le fondateur fait-il lui-même, et quelle part est dispatchée ? Change ce que rapporte une course obtenue par la pub (100 % ou commission). **✅ Estimation (2026-11-15) : environ 6 courses sur 10 faites par le fondateur, 4 sur 10 dispatchées.** Estimation de mémoire, pas un comptage : rien dans le site ne l'enregistre (la réservation n'a ni champ « réalisée par » ni champ « canal »). Pour la fiabiliser : noter pendant 2 semaines, pour chaque course, « moi / dispatchée » et le canal (appel, WhatsApp, site). | Priorisation, Google Ads | Tranché (estimation) |
| Q-02 | Sur une course dispatchée, qui encaisse le client et combien revient à ZenCAR ? **✅ (2026-11-15) : le client paie le partenaire (VTC ou taxi), qui reverse environ 20 % du prix à ZenCAR.** | Priorisation, Google Ads | Tranché |
| Q-04 | « Quick Talk » et l'algorithme de dispatch : qu'est-ce que c'est, et date de lancement prévue ? Conditionne le passage au 24h/24 et l'arrivée d'un régulateur. **Mise à jour 2026-11-15 : le régulateur est mis en place cette semaine et couvre la nuit** ; reste à savoir ce qu'est Quick Talk | Priorisation (plan 90 jours) | Non |
| Q-05 | Mots-clés concurrents (Uber, Bolt, Heetch…). **✅ (2026-11-15, choix délégué : « la plus rentable ») : on les garde, dans un groupe dédié « Alternative aux applis »** avec son annonce (sans citer de marque concurrente). Raison : 314 € → 43,5 conv. (7,23 €, A-10), sous le seuil de 7 à 14 €. Garde-fou : au-delà de 10 €/conv. sur 30 jours, on les réduit. | Étape 03 | Tranché |
| Q-06 | Campagne « réparation smartphone à domicile » dans le même compte et avec le même numéro 06 74 03 96 94 : quelle activité ? **✅ (2026-11-15) : autre activité du fondateur, toujours active, qui utilise le même numéro.** Conséquence : un appel reçu sur ce numéro peut concerner l'une ou l'autre activité. | Diagnostic | Tranché |
| Q-08 | Faut-il séparer les deux activités (numéro distinct, compte Google Ads ou campagnes distinctes) pour que les appels et les conversions de ZenCAR soient mesurables ? **✅ (2026-11-15) : le 06 74 03 96 94 est le numéro ZenCAR ; pas de nouveau numéro.** Limite : tant que l'activité smartphone (Q-06) l'utilise aussi, un appel compté par Google Ads peut venir d'un client smartphone. | Google Ads | Tranché |
| Q-18 | Page seniors : « chauffeurs formés à l'accompagnement » et « conventions possibles avec les EHPAD, facturation mensuelle » sont-ils vrais ? **✅ (2026-11-15) : oui, les deux** — chauffeurs formés ; le fondateur peut proposer une convention avec facture mensuelle aux EHPAD. Textes conservés tels quels. | Site | Tranché |
| Q-07 | Texte des annonces « taxi ». **✅ (2026-11-15, choix délégué : « la plus rentable ») : formulation « Taxi & VTC »** comme le site : VTC à prix affiché + réseau de taxis partenaires agréés ; le mot « taxi » est gardé (c'est l'essentiel des recherches, et les recherches génériques « taxi Marseille » sont les plus rentables : 6,43 €/conv.), sans jamais prétendre que ZenCAR est un taxi ni afficher de prix fixe pour le taxi. | Étape 03 | Tranché |
| Q-09 | Le forfait « Aix-en-Provence → Aéroport 59 € » contredit-il la zone E-09 ? **✅ (2026-11-15) : on garde le forfait, comme exception.** Zone = Marseille + aéroport, plus le seul transfert Aix → aéroport en forfait. Pas d'autres prises en charge à Aix. | Diagnostic | Tranché |
| Q-10 | Forfaits Sormiou (30 € à l'aller, 40 € au retour, libellés bruts) : volontaire ? À garder sur l'accueil ? | Diagnostic | Non |
| Q-11 | Fiche Google : nom « zencar » en minuscules et catégorie « Service de taxi » alors que la société est VTC : quoi changer ? | Étape 04 | Non |
| Q-12 | Quelle adresse affiche la fiche Google, et faut-il corriger l'adresse 13014 de MapQuest (siège : 13010) ? | Étape 04 | Non |
| Q-13 | Diffusion Google Ads et pauses manuelles. **✅ (2026-11-15) : calendrier de diffusion fixe 7 h – 21 h, 7j/7, réglé une fois, plus aucune pause manuelle**, en attendant que le régulateur réponde la nuit ; ensuite passage à 24h/24. Pas de campagne de nuit séparée pour l'instant (les conversions de nuit — réservation en ligne, WhatsApp — ne sont pas encore mesurées). Pas de renvoi d'appel possible (personne pour prendre les appels). Rappel : budget journalier non modulable par heure ; ajustements d'enchères horaires ignorés par « Maximiser les conversions » (sauf -100 %). | Étape 03 | Tranché |
| Q-14 | Pourquoi la campagne s'est-elle arrêtée de juin à août 2026 après sa meilleure période (avril – mai) ? **✅ (2026-11-15) : pause volontaire du fondateur.** Depuis la reprise (sept. – nov.), le coût par conversion est d'environ 16 € (A-11). | Étape 03 | Tranché |
| Q-15 | 31 conversions la nuit (21 h – 7 h) alors que « personne ne répond la nuit » (E-10) : explication ? **✅ (2026-11-15) : le fondateur répondait parfois la nuit.** La nuit convertit bien (5,83 €/conv., A-11) : avec le régulateur 24h/24 (E-10), c'est un créneau à exploiter. | Étape 03 | Tranché |
| Q-16 | Recherches hors zone (autres villes : 245 €, 29 % de la dépense visible, 10,67 €/conv.). **✅ (2026-11-15) : découle de E-09** — exclusion des villes hors zone en mots-clés négatifs, **sauf Aix → aéroport** (forfait maintenu, Q-09). | Étape 03 | Tranché |
| Q-17 | Enchères et budget Google Ads. **✅ (2026-11-15) : CPA cible 7 € (au lieu de 4,70 €) + budget variable automatique** : règle chaque lundi sur les 7 derniers jours — coût/conv. ≤ 7 € → budget +20 % (plafond 20 €/jour) ; > 12 € → -20 % (plancher 6 €/jour) ; budget de départ 12 €/jour. | Étape 03 | Tranché |
| Q-03 | Le site montre une Mercedes Viano et une Classe S : véhicules des partenaires ? **✅ (2026-11-15) : non, photo d'illustration (voir O-10).** | Diagnostic | Tranché |
```
