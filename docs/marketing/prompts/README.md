# Prompts marketing enchaînés — ZenCAR

Une série de 5 prompts à lancer **dans l'ordre**, dans la même conversation Claude.
Chaque prompt est court et focalisé : les réponses sont plus précises qu'avec un seul prompt géant
([version tout-en-un](../prompt-audit-marketing.md)).

## Mode d'emploi

1. **Préparer les données** (liste dans chaque prompt, rubrique « Données à joindre »). Sans données réelles, l'audit reste générique.
2. **Coller d'abord [00-contexte.md](00-contexte.md)**, puis le prompt 01. Pour les suivants, le contexte est déjà dans la conversation.
3. **Répondre aux questions** que Claude pose avant de commencer : c'est voulu.
4. **Lancer chaque prompt dans l'ordre.** Les prompts 03 à 05 se basent sur la priorisation du 02 : on peut sauter ceux qui ne sont pas prioritaires.
5. **Garder les résultats** : coller les livrables validés dans `docs/marketing/` (ex. annonces dans [google-ads.md](../google-ads.md)).

| # | Fichier | Objectif | Données clés |
|---|---|---|---|
| 0 | [00-contexte.md](00-contexte.md) | Tout ce que Claude doit savoir sur ZenCAR | — (à tenir à jour) |
| 1 | [01-diagnostic.md](01-diagnostic.md) | Constater, noter, sans encore proposer de solution | Search Console, Ads, fiche Google, chiffres |
| 2 | [02-priorisation.md](02-priorisation.md) | Choisir les 5 chantiers qui rapportent le plus | Résultat du 01 |
| 3 | [03-google-ads.md](03-google-ads.md) | Campagne Ads : structure, annonces, suivi | Export Ads 90 jours |
| 4 | [04-seo-local.md](04-seo-local.md) | Fiche Google, pages, mots-clés | Search Console, captures Google |
| 5 | [05-avis-fidelisation.md](05-avis-fidelisation.md) | Plus d'avis, clients qui reviennent, clients pros | Avis Google, liste clients |

## Le plus efficace : le faire ici, dans le projet

Lancés dans une session Claude Code ouverte sur `vtc_backend`, ces prompts permettent à Claude de :
- **vérifier** chaque affirmation dans les templates Twig, au lieu de supposer ;
- **appliquer** directement les corrections du site sur la branche `feature/marketing`.

Ajouter alors à la fin du prompt : « Tu as accès au code du site : vérifie dans `templates/` avant d'affirmer, et propose les modifications de fichiers à faire. »

## Règles communes à tous les prompts

- Taxi = prix au compteur, **jamais** de prix fixe affiché ; VTC = prix affiché avant réservation.
- Aucune donnée inventée : toute supposition est marquée `[HYPOTHÈSE]`.
- Chaque recommandation cite sa source (donnée, page, capture) et son indicateur de réussite.
