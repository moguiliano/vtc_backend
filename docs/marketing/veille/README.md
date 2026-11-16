# Veille concurrentielle ZenCAR — mode d'emploi

> Mise en place le 2026-11-16 avec le prompt [06-concurrents.md](../prompts/06-concurrents.md).
> **Objectif n° 1 : baisser le coût Google Ads.** Tout ce qui est relevé ici sert cet objectif.

## Le cadrage (tranché le 2026-11-16)

| ID | Décision |
|---|---|
| V-OBJ | Objectif n° 1 : baisser le coût Google Ads (le reste est secondaire) |
| V-TEMPS | 30 min par semaine + 1 h par mois |
| V-LISTE | Suivis chaque semaine : **Taxi Marseille 13 · Drive Allure · Taxi VTC Marseille · LH Transport · Driver Plus · allo-taxi-marseille.fr** |
| V-LISTE-M | Relevé mensuel : Taxi Radio Marseille, Chauffeur Privé VTC, Best Taxi, Transport 13 Elegant, Allo Taxi (fiche Google) |
| V-OUTILS | Outils publics + exports Google Ads (toi) + extension GMB Everywhere + compte Semrush gratuit |
| V-STOCK | Ici : [releves.csv](releves.csv) (une ligne = un relevé) + une synthèse par relevé |
| V-PRIX | Pas de relevé de prix (tarifs connus du fondateur) ; seuls les prix **écrits dans les annonces** sont notés |
| V-LIEUX | Recherches simulées depuis **Marseille (ville)** et **l'aéroport Marseille-Provence**, sur **mobile** |

## Les fichiers

| Fichier | Contenu |
|---|---|
| [releves.csv](releves.csv) | Tous les relevés : date, concurrent, indicateur, valeur, source, lieu, appareil, remarque. S'ouvre dans Google Sheets |
| [2026-11-16-premier-releve.md](2026-11-16-premier-releve.md) | Premier relevé complet + actions |

## Routine hebdomadaire — 30 min, le lundi matin

| # | Qui | Outil | Ce qu'on relève | Temps |
|---|---|---|---|---|
| 1 | **Toi** | Google Ads → Campagnes → *Insights et rapports* → **Insights sur les enchères**, période « 7 derniers jours » | Pour chaque concurrent : taux d'impressions, taux de chevauchement, taux de 1re position. Copie le tableau et colle-le à Claude | 5 min |
| 2 | **Toi** | Google Ads → *Statistiques* : **Taux d'impressions perdu (budget)** et **(classement)** sur 7 jours | 2 chiffres : ils disent si tu perds des clics faute de budget ou à cause de l'enchère et du niveau de qualité | 3 min |
| 3 | Claude | [Centre de transparence Google Ads](https://adstransparency.google.com/?region=FR) — un domaine à la fois, filtre « 7 derniers jours » | Nombre d'annonces actives + nouveaux messages, offres ou prix | 10 min |
| 4 | Claude ou toi | Recherche simulée **sur mobile** (Google Ads → *Outils* → **Aperçu et diagnostic des annonces**, ou Claude avec un lieu forcé) : 4 requêtes × 2 lieux | Qui apparaît, à quelle place ; ZenCAR est-il là ? | 10 min |
| 5 | Claude | — | Ajout des lignes dans [releves.csv](releves.csv) + 3 lignes de « ce qui a changé » | 2 min |

Les 4 requêtes : `taxi marseille` · `vtc marseille` · `taxi aéroport marseille` · `taxi gare saint charles`.

> ⚠️ Simuler sur **mobile** : Drive Allure, Taxi VTC Marseille et LH Transport utilisent des annonces « appel uniquement », invisibles sur ordinateur. Et 100 % de tes conversions viennent du mobile.
> Pour l'aperçu dans Google Ads : ne jamais cliquer sur une annonce concurrente (ça leur coûte de l'argent et fausse les chiffres).

## Routine mensuelle — 1 h, le 1er lundi du mois

| # | Qui | Outil | Ce qu'on relève | Temps |
|---|---|---|---|---|
| 1 | Toi | Google Ads → Insights sur les enchères **par mot-clé** (tes 4 principaux), 30 jours | Sur quels mots chaque concurrent est le plus présent | 10 min |
| 2 | Toi | Google Ads → Mots-clés → colonne **Niveau de qualité** (+ ses 3 composantes) | Évolution de « Expérience sur la page de destination » après les correctifs du site | 5 min |
| 3 | Claude | [PageSpeed Insights](https://pagespeed.web.dev) mobile : dzencar.fr (accueil + pages d'atterrissage des annonces) + les 6 concurrents | Score et LCP | 10 min |
| 4 | Toi | **Semrush** gratuit → *Recherche organique* puis *Publicité* → domaine de chaque concurrent (10 requêtes/jour) | Mots-clés payants visibles, nouvelles pages | 15 min |
| 5 | Toi | Extension **GMB Everywhere** sur Google Maps | Catégories et nombre d'avis des fiches du relevé mensuel | 10 min |
| 6 | Claude | — | Mise à jour de [concurrents.md](../concurrents.md) + synthèse du mois + 3 actions max | 10 min |

## Alertes à créer (une fois, par toi)

**Google Alerts** ([google.fr/alerts](https://www.google.fr/alerts)) — Fréquence : une fois par semaine · Sources : automatique · Langue : français · Région : France · « Seulement les meilleurs résultats » :
- `"Drive Allure" Marseille`
- `"taximarseille13"`
- `"Taxi VTC Marseille"`
- `"ZenCAR" Marseille` (pour toi : avis, mentions, copies de nom)

**Distill.io** (extension Chrome, formule gratuite, surveillance locale) ou **Visualping** — une vérification par jour, alerte par e-mail si le texte change :
- https://www.driveallure.com/prix-taxi-marseille/ (leurs prix et forfaits)
- https://taxivtcmarseille.com/grille-tarifaire/
- https://www.taximarseille13.fr/ (zone « Forfaits » de l'accueil)
- https://lh-transport.com/

## Règles

- Rien de déloyal : pas de fausse réservation, pas de clic sur les annonces concurrentes, pas de données personnelles collectées.
- Chaque ligne du CSV a sa source. Ce qu'on ne peut pas constater s'écrit « à vérifier », jamais une supposition.
- Taxi = prix au compteur, jamais de prix fixe affiché ; VTC = prix affiché avant réservation (vaut aussi pour les annonces qu'on s'inspire des concurrents).
