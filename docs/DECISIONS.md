# Décisions business ZenCar

> ⚖️ Les choix business : ce qui reste à trancher et ce qui est décidé, avec la raison.
> Séance de décisions : taper `/decisions` dans une conversation Claude.
> Les décisions techniques (code, BDD) ne vont pas ici : elles vont dans `CLAUDE.md`.

<!--
FORMAT ATTENDU — à respecter pour chaque entrée

## ❓ À trancher
### D-XXX — Titre court de la question
- **Chantier** : 🧩 Fonctionnalités | 🗄️ Données | ⚛️ React | 📣 Marketing | 🚀 Infra
- **Contexte** : pourquoi la question se pose (1 à 3 lignes, faits constatés)
- **Options** : A) … / B) … / C) …
- **Avis de Claude** : option recommandée + raison en 1 ligne

## ✅ Tranché  (le plus récent en haut)
### D-XXX — Titre court — AAAA-MM-JJ
- **Décision** : ce qui est décidé, en une phrase
- **Raison** : pourquoi
- **Impact** : fichiers docs/ mis à jour, tâche ajoutée dans ROADMAP.md si du code est nécessaire

Règles : numéro D-XXX unique et jamais réutilisé ; une question tranchée est DÉPLACÉE
(pas recopiée) de « À trancher » vers « Tranché ».
-->

## ❓ À trancher

### D-002 — Taux de TVA des factures
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : le formulaire propose 10 % (transport de personnes, par défaut) ou 20 %. Il faut savoir dans quels cas le 20 % s'applique : mise à disposition, frais annexes ?
- **Options** : A) Toujours 10 %, retirer le choix 20 % / B) Garder les deux, 10 % par défaut / C) À valider avec ton comptable
- **Avis de Claude** : C puis A ou B ; c'est une question fiscale, ton comptable tranche.
- **⏸ En attente (2026-11-12)** : demander au comptable dans quels cas facturer à 20 %. D'ici là, rien ne change : 10 % par défaut, 20 % possible.

### D-006 — Commission et majoration de nuit : qui peut les changer ?
- **Chantier** : 🗄️ Données
- **Contexte** : commission 20 %, majoration de nuit 20 % entre 23 h et 7 h. Ces valeurs sont aujourd'hui dans `config/services.yaml` : les changer demande une mise en prod.
- **Options** : A) Rester dans le fichier de config (changement rare) / B) Les rendre modifiables depuis EasyAdmin
- **Avis de Claude** : A si tu ne les changes qu'une fois par an, B si tu fais des promotions ou des ajustements saisonniers.

### D-007 — Google Ads : exclure le mot-clé `[taxi]` ?
- **Chantier** : 📣 Marketing
- **Contexte** : Google le signale pour « CTR faible ». Le mot-clé est très générique et peut déclencher des recherches hors Marseille, même avec le ciblage géographique.
- **Options** : A) L'exclure / B) Le garder en surveillant le coût par conversion pendant 2 semaines / C) Le remplacer par `[taxi près de moi]`
- **Avis de Claude** : B puis décision chiffrée ; ne rien couper sans données de conversion.

### D-008 — Copies d'annonces Google Ads perdues
- **Chantier** : 📣 Marketing
- **Contexte** : les copies rédigées en avril 2026 sont introuvables (voir `docs/marketing/google-ads.md`).
- **Options** : A) Tu les retrouves (interface Google Ads, notes) et tu les colles / B) On les réécrit en session Marketing
- **Avis de Claude** : regarder d'abord dans Google Ads (les annonces déjà saisies y sont), sinon B.

### D-009 — Vrais avis Google sur le site
- **Chantier** : 📣 Marketing
- **Contexte** : la page d'accueil affiche 3 avis statiques.
- **Options** : A) Copier à la main quelques vrais avis (avec prénom et date) / B) Widget ou API Google (automatique, plus technique, clé API) / C) Bouton « Voir nos avis sur Google » seulement
- **Avis de Claude** : A + C, rapide, sans dépendance externe.

## ✅ Tranché

### D-010 — Qui passe une réservation à « Terminée » ? — 2026-11-12
- **Décision** : pour l'instant, **l'admin** passe la réservation à « Terminée » à la main, dans EasyAdmin.
- **Raison** : simple, pas d'automatisme à construire ; on pourra automatiser plus tard (passage auto quelques heures après le départ).
- **Impact** : bouton « Terminée » ajouté à la tâche « Statut de réservation » dans `ROADMAP.md`.

### D-005 — Statuts d'une réservation et annulation — 2026-11-12
- **Décision** :
  - Statuts : **En attente** (code SMS pas encore validé) → **Confirmée** → **Terminée**, ou **Annulée**.
  - Confirmation **automatique** dès que le client valide son code SMS ; l'admin reçoit un SMS, comme aujourd'hui.
  - **Seul l'admin annule**, dans EasyAdmin ; le client le demande par téléphone ou WhatsApp.
  - Annulation **gratuite**, sans frais.
- **Raison** : rien à construire côté client ; le paiement se fait au chauffeur, donc des frais seraient difficiles à encaisser. À revoir si les annulations de dernière minute deviennent un problème.
- **Impact** : tâche « Statut de réservation » précisée dans `ROADMAP.md` ; nouvelle question D-010 (passage à « Terminée »).

### D-004 — Nom du client sur la facture — 2026-11-12
- **Décision** : le prénom est pré-rempli depuis la réservation ; l'admin complète nom, adresse, SIRET et TVA dans le brouillon avant d'émettre. Le formulaire de réservation ne change pas.
- **Raison** : simple, pas de friction pour le client ; les factures entreprise sont rares.
- **Impact** : aucun code, les champs existent déjà dans le formulaire de facture (EasyAdmin).

### D-003 — Format des numéros de facture — 2026-11-12
- **Décision** : on garde `FAC-AAAA-0001`, avec une remise à 0001 chaque 1er janvier. Le numéro est attribué **à l'émission** (cf. D-001), pas à la création du brouillon.
- **Raison** : format courant et lisible ; numéroter à l'émission évite les trous si un brouillon est abandonné.
- **Impact** : format déjà en place dans le code ; le moment d'attribution est ajouté à la tâche D-001 dans `ROADMAP.md`.

### D-001 — Une facture émise peut-elle encore être modifiée ? — 2026-11-12
- **Décision** : une facture naît en **brouillon** (modifiable), puis passe à **émise** via un bouton « Émettre » : elle est alors verrouillée. Une erreur sur une facture émise se corrige par un **avoir**.
- **Raison** : souple pour corriger une faute de frappe avant envoi, conforme à la loi une fois la facture envoyée au client.
- **Impact** : tâche ajoutée dans `ROADMAP.md` (🧩 statut brouillon/émise + verrouillage + avoir).
