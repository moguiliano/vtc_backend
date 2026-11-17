# 06 — Veille concurrentielle (outils gratuits)

> Objectif : mettre en place une **veille régulière** des concurrents de ZenCAR (annonces, bloc carte, SEO, prix, avis), avec des outils gratuits, puis en tirer des actions.
> Point de départ : la liste [concurrents.md](../concurrents.md) (top 10 + « à surveiller »).
> Même méthode que [00-contexte.md](00-contexte.md) et `/decisions` : **une question à la fois, on ne passe à la suite que lorsqu'elle est tranchée.**

## Données à joindre (si disponibles)
- [ ] Google Ads → « Insights sur les enchères » (90 jours, export CSV), par campagne
- [ ] Captures du bloc carte Google pour les 4 recherches clés (taxi marseille, vtc marseille, taxi aéroport marseille, taxi gare saint charles), en notant **le lieu d'où la recherche est faite**
- [ ] Search Console, 3 mois : requêtes (pour savoir sur quels mots on se bat)
- [ ] Les devis relevés sur les sites concurrents, s'il y en a (trajet, date, heure, prix)

## Les outils gratuits retenus

> Les offres gratuites changent souvent : Claude vérifie la limite actuelle avant de proposer un outil.

| Ce qu'on surveille | Outil gratuit | Ce qu'il donne |
|---|---|---|
| **Annonces Google** des concurrents | [Centre de transparence Google Ads](https://adstransparency.google.com) | Toutes les annonces d'un annonceur, par région et par date |
| Qui enchérit contre nous | Google Ads → **Insights sur les enchères** | Taux d'impressions, 1re position, chevauchement |
| Ce que voit un client à un endroit précis | Google Ads → **Aperçu et diagnostic des annonces** · [valentin.app](https://valentin.app) | Résultats et bloc carte simulés depuis un lieu (aéroport, gare, Vieux-Port), sans fausser nos stats |
| Annonces Facebook / Instagram | [Bibliothèque publicitaire Meta](https://www.facebook.com/ads/library) | Pubs actives des concurrents |
| **Fiches Google** (catégories, services) | Extension Chrome **GMB Everywhere** (formule gratuite) | Catégories principale et secondaires des concurrents |
| Mots-clés et pages qui ramènent du trafic | Semrush (10 requêtes/jour) · Ubersuggest (3/jour) · Ahrefs (vérificateur de trafic et de backlinks gratuits) | Mots-clés positionnés, pages fortes, liens entrants |
| Mots-clés à partir du site d'un concurrent | Google Ads → **Planificateur de mots-clés** (onglet « Commencer avec un site web ») | Idées de mots-clés + volumes |
| **Changements sur leurs sites** (prix, forfaits, offres) | Distill.io ou Visualping (formules gratuites) · [Wayback Machine](https://web.archive.org) pour l'historique | Alerte e-mail quand une page change |
| Vitesse et mobile | [PageSpeed Insights](https://pagespeed.web.dev) | Score mobile comparé à dzencar.fr |
| Technologie du site | Wappalyzer / BuiltWith | CMS, outil de réservation, suivi utilisé |
| Statut légal (taxi ? VTC ? société ?) | [annuaire-entreprises.data.gouv.fr](https://annuaire-entreprises.data.gouv.fr) · Pappers | Activité (code NAF), date de création, dirigeants |
| Mentions et nouveaux concurrents | Google Alerts · Google Trends | Alertes sur les noms ; saisonnalité des recherches |
| **Le tableau de veille** | Google Sheets (ou un fichier dans `docs/marketing/`) | Historique mois par mois |

## Prompt

```
# ÉTAPE 6 — VEILLE CONCURRENTIELLE AVEC DES OUTILS GRATUITS

Données jointes : [liste].
Point de départ : docs/marketing/concurrents.md (lis-le en entier avant de commencer).

Applique la méthode du contexte (00-contexte.md), dans cet ordre.

PHASE A — INVENTAIRE
- Résume concurrents.md : combien de concurrents, combien d'infos « à vérifier », date du relevé.
- Pour chaque donnée jointe : période, lieu de la recherche, nombre de lignes, ce qu'elle mesure. Toute contradiction avec concurrents.md devient une question.

PHASE B — CADRAGE, UNE QUESTION À LA FOIS
Pose les questions ci-dessous UNE PAR UNE, dans ce format : question · pourquoi c'est important (1 ligne) · options A / B / C avec ton avis. Attends ma réponse.
Si ma réponse est floue, partielle ou contradictoire : reformule et NE PASSE PAS à la question suivante tant qu'elle n'est pas tranchée.
Si je ne sais pas : dis-moi comment le savoir et ce que ça bloque, et attends mon accord pour avancer sans.
Une nouvelle question qui apparaît en chemin va dans la liste « À trancher » avec un ID (V-01, V-02…), sans la traiter tout de suite.

Questions minimales (bloquantes), dans cet ordre :
1. Objectif n° 1 de la veille : baisser le coût Google Ads ? remonter dans le bloc carte ? ajuster nos prix ? trouver des idées d'offres ? (un seul choix principal)
2. Temps que je peux y consacrer : par semaine et par mois (en minutes).
3. Concurrents suivis : confirme ou modifie une liste de 5 maximum (propose-la à partir du top 10 de concurrents.md, en justifiant chaque nom).
4. Outils : lesquels j'ai déjà (compte Google Ads, Chrome, compte Google, Semrush…) et lesquels j'accepte d'installer ou de créer (un compte = mon accord explicite, je le crée moi-même).
5. Où stocker le tableau de veille : Google Sheets ou fichier Markdown dans docs/marketing/ ?
6. Relevé des prix : on demande un devis sur les sites concurrents SANS valider de réservation — d'accord ? Quels trajets de référence (propose 3 à 5 : aéroport → Vieux-Port, gare Saint-Charles → Prado, etc.) ?
7. Lieu de référence des recherches simulées (le bloc carte change selon l'endroit) : quels 2 ou 3 lieux ?

Avant de recommander un outil : vérifie (ou demande-moi de vérifier) que sa version gratuite existe toujours et ce qu'elle permet.

PHASE C — MISE EN PLACE, seulement quand les 7 questions sont tranchées
1. TABLEAU DE VEILLE : les colonnes exactes (date, concurrent, indicateur, valeur, source/outil, lieu, remarque) et les indicateurs retenus, chacun relié à l'objectif n° 1. Crée-le (fichier dans docs/marketing/ ou modèle à coller dans Google Sheets).
2. ROUTINE : une checklist hebdomadaire et une mensuelle qui tiennent dans le temps donné à la question 2 : pour chaque ligne, l'outil, le lien, ce qu'on relève et en combien de minutes.
3. ALERTES : la liste des alertes à créer (Google Alerts, Distill/Visualping sur les pages de prix et de forfaits) avec les réglages exacts. Je les crée moi-même.
4. PREMIER RELEVÉ : fais-le avec moi, concurrent par concurrent, une ligne à la fois. Ce que tu peux constater toi-même sur le web, tu le constates (avec le lien) ; ce qui demande mon compte (Google Ads, extension Chrome), tu me dis exactement quoi cliquer et tu attends le résultat. Complète au passage les « à vérifier » de concurrents.md.
5. CE QU'ON EN TIRE : 5 actions maximum pour ZenCAR, chacune avec : constat (source), action, page ou réglage exact, indicateur de réussite. Respecte la règle : Taxi = prix au compteur, jamais de prix fixe affiché ; VTC = prix affiché avant réservation.

RÈGLES
- Classe chaque affirmation : CONSTATÉ (avec le lien ou la capture) · CALCULÉ (avec la formule) · SUPPOSÉ (interdit sans mon accord).
- Rien d'illégal ni de déloyal : pas de fausse réservation, pas de faux avis, pas de clic sur les annonces concurrentes (ça leur coûte de l'argent et fausse nos données), pas de collecte de données personnelles.
- Pas d'outil payant sans me le dire ; signale quand une limite gratuite est atteinte.

AUTO-CRITIQUE : retire tout indicateur qu'on ne pourra pas relever dans le temps disponible, et toute action qui ne découle pas d'un constat.

FIN DE L'ÉTAPE : ce qui n'a pas été relevé et pourquoi, questions « À trancher » restées ouvertes, les mises à jour à faire dans concurrents.md, puis le bloc « REGISTRE MIS À JOUR ».
```

## Astuce : en faire une routine automatique

Une fois la routine validée, on peut demander à Claude Code de créer une **tâche planifiée mensuelle** qui refait les relevés faisables sans compte (Centre de transparence, sites concurrents, PageSpeed) et met à jour le tableau. Les relevés qui demandent ton compte Google Ads restent manuels.
