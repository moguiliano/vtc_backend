---
name: decisions
description: Séance de décisions business ZenCar — pose une question à la fois depuis docs/DECISIONS.md, enregistre chaque décision et met à jour les docs concernées. À utiliser quand l'utilisateur tape /decisions ou demande de « trancher les sujets business ».
---

# Séance de décisions business ZenCar

## Avant de commencer

1. Lis `CLAUDE.md`, `docs/README.md`, `docs/ROADMAP.md` et `docs/DECISIONS.md`.
   Si `docs/` n'existe pas sur la branche courante, dis-le et propose `git merge main` ; ne continue pas sans ces fichiers.
2. Respecte les règles métier de la mémoire (Taxi = prix compteur, VTC = prix affiché avant réservation, etc.).
3. Annonce en une ligne combien de questions sont « À trancher » et laquelle tu prends en premier (la première de la liste, sauf si l'utilisateur en choisit une).

## Déroulé, une question à la fois

1. Pose **une seule** question D-XXX : contexte en 1 à 2 lignes, options, ton avis. Utilise AskUserQuestion quand les options s'y prêtent.
2. Attends la réponse.
   - Si elle **tranche vraiment** la question : passe à l'étape 3.
   - Si elle est floue, partielle ou ouvre un doute : reformule ou précise, et **ne passe pas à la suivante**.
3. Enregistre la décision dans `docs/DECISIONS.md` :
   - **déplace** l'entrée de « ❓ À trancher » vers « ✅ Tranché » (en haut), avec la date du jour, la décision, la raison et l'impact ;
   - respecte le format décrit dans le commentaire en tête du fichier.
4. Mets à jour le ou les fichiers `docs/` concernés (`ROADMAP.md`, `marketing/google-ads.md`, `marketing/seo.md`, `deploiement.md`…).
   Si un fichier a un format documenté (commentaire `FORMAT ATTENDU` ou structure existante), respecte-le.
5. Si la décision demande du code : ajoute une tâche dans `docs/ROADMAP.md` (section « En attente », bon chantier), **sans écrire de code**.
6. Si une nouvelle question apparaît en chemin : ajoute-la à « ❓ À trancher » avec le numéro D-XXX suivant, sans la traiter maintenant.
7. Passe à la question suivante.

## Interdits

- Ne pas toucher au code : `src/`, `migrations/`, `templates/`, `assets/`, `config/`.
- Ne pas modifier les entités ni le modèle de données, même si une décision l'implique ; c'est une tâche dans la ROADMAP.
- Ne pas trancher à la place de l'utilisateur ; l'avis de Claude reste un avis.
- Ne traiter que des questions business (prix, règles, légal, marketing, organisation), pas des choix techniques.

## Fin de séance

Quand l'utilisateur arrête ou qu'il n'y a plus de question :
1. Ajoute une entrée dans `docs/JOURNAL.md` (Fait / Bloqué / Prochaine étape) listant les D-XXX tranchées.
2. Propose un commit `docs: décisions D-XXX…` (sans pousser sans accord).
