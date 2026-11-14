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

AUTO-CRITIQUE : retire toute recommandation qui n'est pas justifiée par Search Console ou par les captures jointes.
```
