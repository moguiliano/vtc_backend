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

<!-- ─── Bloc « Plateforme multi-entreprises » (ajouté le 2026-11-13) ───
Projet : chauffeurs / régulateurs abonnés avec leur propre espace (tarifs, société validée par l'admin ZenCar,
bons de commande, factures) + collaborateurs (établissements partenaires avec prix convenus).
Ordre conseillé : D-011 (modèle) d'abord, car il conditionne toutes les autres. -->

### D-011 — Quel modèle de plateforme ?
- **Chantier** : 🗄️ Données (architecture)
- **Contexte** : deux visions très différentes derrière « chaque chauffeur a son propre admin ».
- **Options** : A) **Logiciel de gestion** (SaaS) : chaque abonné gère *ses propres* clients et courses avec l'outil ZenCar ; ZenCar ne lui envoie pas de courses / B) **Place de marché** : les clients réservent sur dzencar.fr et ZenCar distribue les courses aux abonnés / C) Les deux
- **Avis de Claude** : A d'abord ; le modèle le plus simple et le plus clair juridiquement (tu vends un outil). B pourra s'ajouter ensuite.

### D-012 — Le site public de réservation pour les abonnés
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : si un abonné a ses propres clients (D-011 A), comment ses clients réservent-ils en ligne ?
- **Options** : A) Pas de site public pour les abonnés : ils saisissent les courses dans leur espace (comme ta « réservation téléphone ») / B) Une page de réservation par abonné (ex. `dzencar.fr/r/nom-du-chauffeur`) / C) Leur propre nom de domaine
- **Avis de Claude** : A pour démarrer, B ensuite ; C est coûteux et rarement nécessaire.

### D-013 — Rôles : qui est qui ?
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : il faut définir précisément chaque rôle et ce qu'il peut faire.
- **Options** : proposition à valider : **Super-admin** (toi : valide les sociétés, gère les abonnements) / **Régulateur** (une entreprise avec plusieurs chauffeurs, voit et attribue toutes les courses de son entreprise) / **Chauffeur** (indépendant = une entreprise d'un seul chauffeur, ou salarié d'un régulateur : ne voit que ses courses) / **Collaborateur** (établissement partenaire : commande des courses aux prix convenus)
- **Avis de Claude** : garder ces 4 rôles ; un chauffeur indépendant est simplement un régulateur avec un seul chauffeur, ce qui évite deux systèmes.

### D-014 — Abonnement : prix et paiement
- **Chantier** : 🗄️ Données
- **Contexte** : l'espace perso s'ouvre « suite à abonnement ».
- **Options** : prix et périodicité (mensuel / annuel) ? Période d'essai ? Paiement : A) en ligne automatique (Stripe) / B) manuel (virement, tu actives le compte à la main) ? Que se passe-t-il en cas d'impayé (lecture seule, blocage) ?
- **Avis de Claude** : B pour les premiers abonnés (aucun développement de paiement), A quand il y en aura plus de 5-10.

### D-015 — Validation de la société d'un abonné
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : l'abonné saisit les données de sa société, tu les valides.
- **Options** : quelles pièces exiger (Kbis, carte VTC ou taxi, inscription REVTC, assurance) ? Que peut faire l'abonné **avant** validation : A) rien / B) configurer ses tarifs mais pas émettre de bons ni de factures ?
- **Avis de Claude** : B, et pas de bons de commande ni de factures tant que la société n'est pas validée, car ils portent ses mentions légales.

### D-016 — Tarifs des abonnés
- **Chantier** : 🗄️ Données
- **Contexte** : chaque abonné décide de ses tarifs (« véhicules et prix »).
- **Options** : A) Chaque abonné part de zéro / B) Il part d'une copie de la grille ZenCar et la modifie / C) Il choisit ses catégories de véhicules parmi une liste commune et fixe ses prix
- **Avis de Claude** : C, avec une liste commune de catégories (éco-berline, berline, van…) et des prix propres à chaque abonné. Ça garde les slugs cohérents.

### D-017 — Factures et bons de commande des abonnés
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : aujourd'hui, la facturation suppose **une seule** société (la tienne). Avec des abonnés, chaque facture est émise **au nom de l'abonné**.
- **Options** : confirmer : A) chaque abonné a sa propre numérotation (`FAC-AAAA-0001` par abonné) et ses mentions légales ; ZenCar n'est que l'éditeur du logiciel / B) autre
- **Avis de Claude** : A, c'est la règle légale : la numérotation est propre à chaque entreprise émettrice. Il faudra aussi que ZenCar facture l'abonnement à chaque abonné.

### D-018 — Collaborateurs : à qui sont-ils rattachés ?
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : un collaborateur est un établissement (hôtel, clinique, entreprise) avec des prix négociés.
- **Options** : A) Collaborateurs de ZenCar uniquement (toi) / B) Chaque abonné peut avoir ses propres collaborateurs / C) Les deux
- **Avis de Claude** : A d'abord, faisable **sur le site actuel** sans attendre le multi-entreprises ; B ensuite.

### D-019 — Collaborateurs : forme des prix convenus et paiement
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : « on se met d'accord sur les prix ».
- **Options** : prix sous quelle forme : A) forfaits par trajet (comme tes forfaits VTC) / B) remise en % sur la grille / C) les deux ? Paiement : à chaque course, ou **facture mensuelle groupée** au collaborateur ? Qui monte dans la voiture : le collaborateur saisit le nom de son client ?
- **Avis de Claude** : A + B, avec une facture mensuelle groupée (c'est l'usage avec les hôtels).

### D-020 — Par quoi commencer ?
- **Chantier** : 🗄️ Données
- **Contexte** : le multi-entreprises est le plus gros chantier de ZenCar ; les collaborateurs (D-018 A) sont faisables plus vite. La Facturation actuelle (`feature/facturation`) n'est pas encore en prod.
- **Options** : A) Facturation actuelle en prod → Collaborateurs → Multi-entreprises / B) Tout de suite le multi-entreprises (et reprendre la Facturation dedans) / C) Autre ordre
- **Avis de Claude** : A ; chaque étape apporte une valeur réelle tout de suite, et le multi-entreprises se construit sur des bases déjà testées.

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
- **Correction (2026-11-13)** : le contexte de départ (« aucun statut ») était faux, il venait d'un ancien prompt. Le code avait déjà 7 statuts (`ReservationStatus` : en attente, confirmée, chauffeur assigné, chauffeur arrivé, client à bord, terminée, annulée) et la confirmation automatique. La décision reste valable et est en grande partie déjà appliquée.

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
