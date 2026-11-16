# Prompts marketing enchaînés — ZenCAR

Une série de prompts à lancer **dans l'ordre**, dans la même conversation Claude.
Chaque prompt est court et focalisé : les réponses sont plus précises qu'avec un seul prompt géant
([version tout-en-un](../prompt-audit-marketing.md)).

## Mode d'emploi

1. **Préparer les données** (liste dans chaque prompt, rubrique « Données à joindre »). Sans données réelles, l'audit reste générique.
2. **Coller d'abord [00-contexte.md](00-contexte.md)** : il contient la méthode et le **registre des données**. Puis le prompt 01. Pour les suivants, le contexte est déjà dans la conversation.
3. **Chaque étape se déroule en 3 phases**, sans en sauter :
   - **A — Inventaire** : Claude décrit et contrôle chaque fichier joint (période, totaux, unités, contradictions) ;
   - **B — Validation** : Claude pose **une question à la fois** sur chaque donnée non confirmée ou contradictoire, et **ne passe pas à la suivante tant qu'elle n'est pas tranchée** (même logique que `/decisions`) ;
   - **C — Analyse** : seulement quand toutes les questions bloquantes sont tranchées.
4. **Lancer chaque prompt dans l'ordre.** Les prompts 03 à 05 se basent sur la priorisation du 02 : on peut sauter ceux qui ne sont pas prioritaires.
5. **Mettre à jour le registre** : à la fin de chaque étape, Claude donne un bloc « REGISTRE MIS À JOUR » ; recopier ces lignes dans [00-contexte.md](00-contexte.md). Une donnée ✅ n'est plus redemandée aux séances suivantes.
6. **Garder les résultats** : coller les livrables validés dans `docs/marketing/` (ex. annonces dans [google-ads.md](../google-ads.md)).

| # | Fichier | Objectif | Données clés |
|---|---|---|---|
| 0 | [00-contexte.md](00-contexte.md) | Tout ce que Claude doit savoir sur ZenCAR | — (à tenir à jour) |
| 1 | [01-diagnostic.md](01-diagnostic.md) | Constater, noter, sans encore proposer de solution | Search Console, Ads, fiche Google, chiffres |
| 2 | [02-priorisation.md](02-priorisation.md) | Choisir les 5 chantiers qui rapportent le plus | Résultat du 01 |
| 3 | [03-google-ads.md](03-google-ads.md) | Campagne Ads : structure, annonces, suivi | Export Ads 90 jours |
| 4 | [04-seo-local.md](04-seo-local.md) | Fiche Google, pages, mots-clés | Search Console, captures Google |
| 5 | [05-avis-fidelisation.md](05-avis-fidelisation.md) | Plus d'avis, clients qui reviennent, clients pros | Avis Google, liste clients |
| 6 | [06-concurrents.md](06-concurrents.md) | Veille concurrentielle avec des outils gratuits | Insights sur les enchères, captures bloc carte, [concurrents.md](../concurrents.md) |

## Le plus efficace : le faire ici, dans le projet

Lancés dans une session Claude Code ouverte sur `vtc_backend`, ces prompts permettent à Claude de :
- **vérifier** chaque affirmation dans les templates Twig, au lieu de supposer ;
- **appliquer** directement les corrections du site sur la branche `feature/marketing`.

Ajouter alors à la fin du prompt : « Tu as accès au code du site : vérifie dans `templates/` avant d'affirmer, et propose les modifications de fichiers à faire. »

## Règles communes à tous les prompts

- Taxi = prix au compteur, **jamais** de prix fixe affiché ; VTC = prix affiché avant réservation.
- **Rien n'est bâti sur une donnée non confirmée** (⚠️ ou ❓) : si Claude ne sait pas, il demande.
- Toutes les lignes des fichiers joints sont analysées, pas un échantillon ; chaque chiffre cite sa source (fichier + ligne ou colonne).
- Chaque affirmation est classée : constaté, calculé (avec la formule) ou supposé (interdit sans accord).
- Chaque recommandation cite sa source et son indicateur de réussite.
