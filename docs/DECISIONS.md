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

### D-001 — Une facture émise peut-elle encore être modifiée ?
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : sur `feature/facturation`, la suppression d'une facture est bloquée, mais la modification reste possible après création. En France, une facture émise ne doit plus être modifiée : on la corrige par un avoir.
- **Options** : A) Verrouiller la facture dès sa création ; les erreurs sont corrigées par un avoir / B) Laisser modifiable tant qu'elle n'est pas envoyée au client (statut « brouillon » → « émise ») / C) Laisser comme aujourd'hui
- **Avis de Claude** : B, souple pour corriger une faute de frappe et conforme une fois la facture émise.

### D-002 — Taux de TVA des factures
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : le formulaire propose 10 % (transport de personnes, par défaut) ou 20 %. Il faut savoir dans quels cas le 20 % s'applique : mise à disposition, frais annexes ?
- **Options** : A) Toujours 10 %, retirer le choix 20 % / B) Garder les deux, 10 % par défaut / C) À valider avec ton comptable
- **Avis de Claude** : C puis A ou B ; c'est une question fiscale, ton comptable tranche.

### D-003 — Format des numéros de facture
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : le code génère `FAC-2026-0001`, avec une numérotation qui repart à 1 chaque année. La loi exige une suite continue, sans trou.
- **Options** : A) Garder `FAC-AAAA-0001`, remise à zéro annuelle / B) Numérotation continue sans remise à zéro / C) Autre format (préciser)
- **Avis de Claude** : A, format courant et accepté.

### D-004 — Nom du client sur la facture
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : une facture créée depuis une réservation reprend uniquement le **prénom** du client invité. Pour une entreprise (SIRET, TVA), il faut une raison sociale et une adresse.
- **Options** : A) Demander nom + prénom dans le formulaire de réservation / B) Saisie manuelle dans l'admin au moment de facturer / C) Les deux
- **Avis de Claude** : B pour l'instant (simple), A plus tard avec la refonte React.

### D-005 — Statuts d'une réservation et annulation
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : une réservation n'a aucun statut aujourd'hui. Proposition : en attente → confirmée → terminée / annulée.
- **Options** : A) Seul l'admin change le statut / B) Le client peut aussi annuler, jusqu'à X heures avant le départ / C) Autre
- **Avis de Claude** : A d'abord ; B quand le Mailer existera. Il faut aussi trancher la valeur de X et l'existence de frais d'annulation.

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

_(rien pour l'instant)_
