# 04 — SEO local et fiche Google

> Objectif : apparaître dans le bloc carte et dans les premiers résultats sur les recherches qui rapportent des courses.
> Référence : [seo.md](../seo.md) (pages et mots-clés actuels).

## Données à joindre
- [ ] Search Console, 3 mois : requêtes et pages (impressions, clics, position)
- [ ] Fiche Google Business Profile : captures complètes (catégories, services, horaires, photos, posts, questions-réponses)
- [ ] Captures du bloc carte Google pour « taxi marseille », « vtc marseille », « taxi aéroport marseille », « taxi gare saint charles »
- [ ] Les fiches Google des 3 concurrents qui apparaissent en premier
- [ ] Les annuaires où ZenCAR est inscrit (Pages Jaunes, etc.)

## Prompt

```
# ÉTAPE 4 — SEO LOCAL ET FICHE GOOGLE

Données jointes : [liste].

Applique la méthode du contexte, dans cet ordre.

PHASE A — INVENTAIRE de chaque export et capture : période, nombre de requêtes et de pages, filtres appliqués (pays, appareil, type de recherche). Compare la fiche Google avec le registre (nom, téléphone, horaires, zone, catégories) : chaque différence devient une question.

PHASE B — VALIDATION, une question à la fois, sans passer à la suivante tant qu'elle n'est pas tranchée. Au minimum : horaires réels (E-10), zone desservie (E-09), modèle exploitant ou plateforme (E-05), Search Console installée (S-06). Avant de recommander une catégorie, un service ou une page, demande-moi si ZenCAR propose réellement ce service.

PHASE C — ANALYSE, seulement quand les questions bloquantes sont tranchées. Analyse TOUTES les requêtes de Search Console, pas seulement les premières.

1. FICHE GOOGLE BUSINESS PROFILE — la priorité pour un taxi local :
   - catégorie principale et secondaires recommandées ;
   - services à déclarer, avec leur description ;
   - horaires : alignés avec la réalité et avec le site ;
   - description de la fiche (750 caractères max), rédigée ;
   - photos à ajouter (liste précise : véhicules, chauffeur, aéroport…) ;
   - 10 questions-réponses à publier ;
   - 8 posts Google rédigés (un par semaine pendant 2 mois).
   Compare avec les fiches concurrentes jointes : qu'ont-elles de plus ?

2. MOTS-CLÉS — à partir de Search Console :
   tableau : requête | impressions | position | page actuelle | action (optimiser la page, créer une page, rien).
   Repère les requêtes en position 5 à 20 : ce sont les gains les plus rapides.

3. PAGES :
   - pour chaque page existante : nouveau title (60 caractères max), meta description (155 max), H1, et ce qu'il manque dans le contenu ;
   - pages à créer, par ordre de priorité (ex. Marignane, Cassis, Aix, gare TGV Aix, terminal croisières, hôpitaux, longue distance) : mot-clé, plan de la page, et ce qui la rend différente des pages déjà existantes (pas de pages copiées-collées en changeant la ville).

4. COHÉRENCE : nom, adresse, téléphone et horaires identiques sur le site, les données structurées, la fiche Google et les annuaires. Liste toutes les différences.

5. DONNÉES STRUCTURÉES : le JSON-LD corrigé, prêt à coller (type, horaires réels, zone desservie réelle).

6. LIENS LOCAUX : 10 sites marseillais où obtenir un lien ou une mention (partenaires, hôtels, offices de tourisme, associations seniors…).

AUTO-CRITIQUE : retire toute recommandation qui n'est pas justifiée par Search Console ou par les captures jointes. Vérifie que les horaires, la zone et les services recommandés correspondent aux données ✅ du registre.

FIN DE L'ÉTAPE : ce qui n'a pas été analysé, questions restées ouvertes, puis le bloc « REGISTRE MIS À JOUR ».
```
