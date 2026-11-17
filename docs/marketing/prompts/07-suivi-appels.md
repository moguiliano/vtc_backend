# 07 — Rentabilité Google Ads : nettoyer, mesurer le gain, piloter

> Objectif : **optimiser l'algorithme Google Ads** de ZenCAR :
> 1. **nettoyer** : enlever les mots-clés, recherches et zones qui coûtent sans rapporter ;
> 2. **mesurer le gain** : renvoyer à Google Ads ce que chaque appel a rapporté (course faite, dispatchée, refusée), pour comparer **gain vs dépense** ;
> 3. **piloter** : passer d'une stratégie « plus d'appels » à une stratégie « plus de chiffre rentable ».
>
> Point de départ : [google-ads.md](../google-ads.md) (objectif = appels + WhatsApp, enchères « Maximiser les conversions », tracking « approximatif ») et [google-ads-plan-2026-11-15.md](../google-ads-plan-2026-11-15.md).
> Même méthode que [00-contexte.md](00-contexte.md) et `/decisions` : **une question à la fois, on ne passe à la suite que lorsqu'elle est tranchée.**

## Données à joindre (si disponibles)

Toutes en export CSV, **90 jours**, depuis Google Ads :
- [ ] **Termes de recherche** (Mots-clés › Termes de recherche) : ce que les gens ont vraiment tapé, avec coût, clics, conversions
- [ ] **Mots-clés** : type de correspondance, coût, clics, conversions, niveau de qualité
- [ ] **Lieux** : « Lieux correspondants » ET « Lieux des utilisateurs » (rapport « Où se trouvaient vos utilisateurs »), avec coût et conversions
- [ ] **Heure de la journée × jour de la semaine** : coût, clics, conversions
- [ ] **Détails des appels** (s'il existe) : heure, durée, campagne, groupe d'annonces, indicatif de l'appelant
- [ ] Capture de **Objectifs › Conversions** : chaque action (nom, source, principale/secondaire, valeur, durée minimale d'appel)
- [ ] Capture des **paramètres de campagne** : zones ciblées et option de ciblage (« présence » ou « présence ou intérêt »), calendrier, budget, stratégie d'enchères
- [ ] Ce que les Ads m'ont rapporté sur la période, même approximatif : nombre de courses venues de Google, panier moyen taxi / VTC / forfait, commission quand je dispatche

## Prompt

```
# ÉTAPE 7 — RENTABILITÉ GOOGLE ADS : NETTOYER, MESURER LE GAIN, PILOTER

Données jointes : [liste].
Lis d'abord docs/marketing/google-ads.md et docs/marketing/google-ads-plan-2026-11-15.md en entier.

Mon objectif : que Google Ads me rapporte plus qu'il ne me coûte, et le PROUVER.
1) Enlever les mots-clés, recherches et zones qui dépensent sans ramener de courses.
2) Donner à Google ce que chaque appel venu des Ads m'a rapporté (course faite, dispatchée, refusée),
   pour comparer gain et dépense, et que l'algorithme cherche des courses rentables, pas des appels.
3) En tirer une vraie stratégie : budget, enchères, horaires, zones, mots-clés.
Contexte : je reçois tout sur un seul numéro (06 74 03 96 94, aussi WhatsApp), je suis souvent au volant,
budget serré. Il me faut une solution simple, tenable au quotidien.

Applique la méthode du contexte (00-contexte.md), dans cet ordre.

PHASE A — INVENTAIRE
- Pour chaque fichier joint : période, nombre de lignes, coût total, conversions totales. Vérifie que les
  totaux concordent entre fichiers ; toute différence devient une question.
- État du suivi actuel : qu'est-ce que Google compte comme « conversion » aujourd'hui (clic sur le numéro ?
  appel de plus de X secondes ? clic WhatsApp ?), et donc ce que l'algorithme optimise VRAIMENT.
  Dis-moi si ces conversions peuvent être fausses (appels courts, doublons, mauvais numéros, appels de clients
  existants) et dans quelle proportion, si les données le permettent.
- Ce qui n'est pas mesuré du tout (WhatsApp, appels depuis la fiche Google, clients qui rappellent plus tard).

PHASE B — CADRAGE, UNE QUESTION À LA FOIS
Pose les questions ci-dessous UNE PAR UNE, dans ce format : question · pourquoi c'est important (1 ligne) ·
options A / B / C avec ton avis. Attends ma réponse.
Si ma réponse est floue, partielle ou contradictoire : reformule et NE PASSE PAS à la question suivante.
Si je ne sais pas : dis-moi comment le savoir et ce que ça bloque, et attends mon accord pour avancer sans.
Une nouvelle question apparue en chemin va dans « À trancher » avec un ID (R-01, R-02…), sans la traiter tout de suite.

Questions minimales (bloquantes), dans cet ordre :
1. Ma zone réelle : où j'accepte de prendre des clients et où je refuse (communes, aéroport, gare, distances max) ?
   Et d'où viennent mes clients touristes (ils cherchent parfois depuis l'étranger avant d'arriver) ?
2. Ce qu'est une « bonne » conversion pour moi : appel de plus de combien de secondes ? Un clic WhatsApp compte-t-il ?
3. Valeur d'une course : quel montant envoyer à Google ?
   A. montant réel de chaque course ; B. montant moyen fixe par type (taxi / VTC / forfait) ;
   C. ma marge (course faite = prix ; dispatchée = ma commission ; refusée ou sans suite = 0).
   Rappel : taxi = prix au compteur, donc montant connu seulement après la course.
4. Relier un appel à un clic Google : quelle solution ?
   A. numéros de transfert Google (gratuits) sur l'extension d'appel et sur le site, puis import des
      « conversions d'appels » avec numéro appelant + heure de l'appel ;
   B. outil de suivi d'appels payant (prix vérifié, ce qu'il apporte en plus) ;
   C. estimation manuelle (« comment nous avez-vous connus ? ») sans import.
   Explique ce que chaque option permet de relier : campagne, groupe d'annonces, mot-clé, terme de recherche.
5. Le journal des appels (seulement ce qui sert à l'import et aux décisions) : où je le remplis, sachant que je
   conduis ? A. formulaire mobile dans mon back-office ZenCAR (EasyAdmin) ; B. Google Forms → Sheets ;
   C. saisie le soir depuis l'historique d'appels. Champs proposés en listes fermées : heure, numéro appelant
   (pour l'import uniquement), issue (faite / dispatchée / refusée / sans suite), motif de refus, montant.
   Temps de saisie réaliste et risque d'oubli pour chaque option. Rappelle-moi les obligations RGPD
   (durée de conservation, mention dans la politique de confidentialité).
6. Fréquence d'import vers Google Ads : chaque semaine en CSV à la main, ou automatique ? Combien de temps
   par semaine je peux y consacrer ?
7. Changer de stratégie d'enchères : à partir de quand passer de « Maximiser les conversions » à
   « Maximiser la valeur de conversion » (puis éventuellement un ROAS cible) ? Vérifie les seuils actuels
   recommandés par Google (nombre de conversions sur 30 jours) avant de répondre.

PHASE C — ACTIONS, seulement quand les 7 questions sont tranchées

1. NETTOYAGE IMMÉDIAT (avec les données déjà jointes, sans attendre le nouveau suivi)
   - Termes de recherche : analyse TOUTES les lignes. Classe chacun : à garder / à exclure / à surveiller
     (trop peu de données). Donne la liste de mots-clés négatifs à ajouter, avec le type de correspondance
     et le niveau (campagne ou liste partagée). Repère les familles inutiles : emploi (« devenir chauffeur »,
     « recrutement »), autres villes, concurrents, Uber/Bolt, prix « pas cher » si non rentable, ambulance,
     location de voiture, etc. — seulement si elles apparaissent dans les données.
   - Mots-clés : ceux à mettre en pause (coût > seuil sans conversion), avec le seuil choisi et pourquoi.
   - Zones : compare « lieux correspondants » et « lieux des utilisateurs ». Propose les zones à exclure
     ou à ajuster (enchère réduite / augmentée), et l'option de ciblage « présence » vs « présence ou intérêt ».
   - Horaires : tranches où l'on paie sans convertir, et tranches où je ne peux pas prendre de course
     (dispo réelle) → calendrier de diffusion et ajustements d'enchères.
   - Pour chaque action : constat (fichier + ligne ou colonne), réglage exact, économie estimée (formule).

2. MISE EN PLACE DU SUIVI DE LA VALEUR
   - Google Ads : pas à pas, les écrans exacts (extension d'appel avec numéro de transfert, conversion
     « appels depuis le site », durée minimale, action importée « course faite » avec valeur, actions
     principales/secondaires). Je clique moi-même ; tu attends ma capture à chaque étape.
   - Le journal : crée-le selon la question 5. Si c'est EasyAdmin : entité, champs, migration, CRUD mobile,
     et une exportation CSV au format d'import Google Ads, en respectant CLAUDE.md.
     Si c'est Google Sheets : colonnes, listes déroulantes, et l'onglet au format d'import.

3. TABLEAU DE BORD HEBDOMADAIRE (5 indicateurs maximum, avec la formule de chacun)
   Au minimum : dépense, chiffre ramené par les Ads, ROAS (chiffre ÷ dépense), coût par course faite,
   taux de refus par motif.

4. STRATÉGIE SUR 8 SEMAINES
   Semaine par semaine : ce qu'on change, ce qu'on attend, et le seuil de données à atteindre avant de
   trancher (ne jamais changer plusieurs réglages majeurs en même temps — l'algorithme réapprend).
   Inclure : quand changer la stratégie d'enchères, quand revoir le budget, quoi faire si le ROAS reste < 1.

5. ROUTINE : ce que je fais à chaque appel (secondes), chaque soir, chaque semaine (import, termes de
   recherche, négatifs), chaque mois (zones, horaires, bilan gain vs dépense).

RÈGLES
- Analyse toutes les lignes des fichiers, pas un échantillon. Chaque chiffre cite sa source.
- Classe chaque affirmation : CONSTATÉ (source) · CALCULÉ (formule) · SUPPOSÉ (interdit sans mon accord).
- Ne propose pas d'exclure un mot-clé ou une zone sur trop peu de données : donne le seuil utilisé.
- Taxi = prix au compteur, jamais de prix fixe affiché ; VTC = prix affiché avant réservation.
- Pas d'enregistrement des conversations. Pas d'outil payant sans son prix ; un compte à créer = je le crée.
- La saisie ne doit jamais se faire en conduisant : prévois toujours une option « après la course ».

AUTO-CRITIQUE : retire toute action qui ne découle pas d'un constat, tout champ du journal qui ne sert
ni à l'import ni à une décision, et toute étape de routine qui dépasse le temps donné à la question 6.

FIN DE L'ÉTAPE : liste des négatifs et exclusions à appliquer (prête à copier), ce qui n'est pas encore
mesuré et pourquoi, questions « À trancher » restées ouvertes, mises à jour à faire dans google-ads.md,
puis le bloc « REGISTRE MIS À JOUR ».
```
