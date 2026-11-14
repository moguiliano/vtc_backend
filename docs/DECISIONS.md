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


### D-022 — Prix de l'abonnement
- **Chantier** : 📣 Marketing
- **Contexte** : apparue avec D-014. Le paiement est manuel, donc le prix ne bloque pas le développement. **Reportée** le 2026-11-14 : à fixer quand les espaces abonnés approcheront (étape 4 de D-020).
- **Options** : montant ? mensuel / annuel ? période d'essai gratuite (ex. 1 mois) ?
- **Avis de Claude** : à fixer avant le premier abonné ; regarder les prix des logiciels VTC concurrents.

## ✅ Tranché

### D-021 — Que peut voir et faire le régulateur ? — 2026-11-14
- **Décision** :
  - **Voit** : prix client, prix chauffeur, commission, factures.
  - **Fait** : changer les statuts, créer une réservation (téléphone), modifier ou annuler une course, télécharger les bons de commande.
  - **Interdit** (réservé au super-admin) : tarifs et forfaits, « Ma société », comptes admin, **création et émission des factures**.
  - **Partage des courses en 2 temps, sur WhatsApp et Telegram** (complément validé par l'utilisateur dans une autre conversation, 2026-11-14 ; remplace « prix client + prix chauffeur dans le message ») :
    1. **Annonce dans les groupes** : zone **approximative** de prise en charge (pas d'adresse exacte), date et heure, véhicule, **prix chauffeur uniquement** (pas le prix client).
    2. Les chauffeurs répondent dans le groupe avec leur **temps d'approche**.
    3. Au chauffeur retenu, la **course complète en message privé** : nom, prénom, téléphone, adresse exacte, infos complémentaires, prix, commission, etc.
- **Raison** : le régulateur gère toute l'exploitation quotidienne ; la configuration, le légal et la facturation restent au super-admin. Le partage en 2 temps garde les données client chez le seul chauffeur retenu, et les chauffeurs du groupe ne voient plus la commission.
- **Impact** : nouveau rôle `ROLE_REGULATEUR` dans EasyAdmin (s'appuyer sur les rôles existants `ROLE_RESERVATIONS_*`) ; deux boutons de partage par course (« Annonce groupe » et « Envoi au chauffeur retenu »), pour WhatsApp et Telegram. **À préciser plus tard** : qui choisit le chauffeur parmi les réponses (a priori le régulateur).

### D-020 — Par quoi commencer ? — 2026-11-14
- **Décision** : **fondation d'abord** (« coller les étiquettes »), puis les fonctionnalités, dans cet ordre :
  0. **Fondation** : notion d'**Entreprise** (SIMPLICI CAB = entreprise n°1, seule pour l'instant) ; chaque réservation, facture, bon de commande, société et grille de prix **appartient à une entreprise**. Briques centralisées et réutilisables : *créer une réservation* (site, téléphone, collaborateur, abonné), *générer un PDF* (bon ou facture au nom de l'entreprise), *calculer un prix* (grille de l'entreprise). **Aucun changement visible** pour les clients.
  1. **Facturation** en prod, posée sur la fondation.
  2. **Régulateur** + partage WhatsApp.
  3. **Collaborateurs**.
  4. **Espaces abonnés** (inscription, validation, leur admin).
- **Raison** : idée de l'utilisateur : centraliser une seule fois pour ne pas refaire la Facturation et les Collaborateurs au moment du multi-entreprises (gain de temps et de tokens). La fondation reste invisible et sans risque pour le site.
- **Impact** : roadmap réorganisée ; `feature/facturation` (pas encore en prod) sera adaptée à la fondation avant sa mise en prod.

### D-019 — Collaborateurs : prix convenus et paiement — 2026-11-13
- **Décision** :
  - Prix : des **forfaits propres au collaborateur** (ex. Hôtel X → Aéroport = 55 €) **+ une remise en %** sur la grille SIMPLICI CAB pour les autres trajets.
  - Paiement : **au choix à chaque commande** : « sur le compte » du collaborateur (regroupé sur une **facture mensuelle**) ou **payé par le passager** au chauffeur.
- **Raison** : couvre les trajets récurrents (forfaits) et les trajets ponctuels (remise) ; le choix par course s'adapte aux hôtels (course offerte au client ou non).
- **Impact** : le collaborateur a un compte de connexion, ses forfaits, son taux de remise ; chaque réservation note le mode « compte collaborateur » ou « passager » ; il faut une **facture mensuelle** des courses « sur le compte ». À préciser au développement : saisie du nom et du téléphone du passager à la commande.

### D-018 — Collaborateurs : à qui sont-ils rattachés ? — 2026-11-13
- **Décision** : au démarrage, les collaborateurs sont ceux de **SIMPLICI CAB uniquement**. Les abonnés pourront avoir les leurs plus tard.
- **Raison** : faisable **sur le site actuel**, sans attendre le multi-entreprises ; valeur commerciale immédiate.
- **Impact** : module Collaborateurs construit avant le multi-entreprises, en prévoyant qu'un collaborateur sera un jour rattaché à un abonné.

### D-017 — Factures et bons de commande des abonnés — 2026-11-13
- **Décision** : les bons de commande et factures d'un abonné sont émis **à son nom** (sa société validée, ses mentions légales), avec **sa propre numérotation** (`FAC-AAAA-0001` propre à chaque abonné). Les règles D-001 à D-004 s'appliquent à chacun. ZenCar n'est que l'éditeur du logiciel et **facture l'abonnement** à chaque abonné.
- **Raison** : obligation légale, la numérotation est propre à chaque entreprise émettrice.
- **Impact** : `Facture` et la numérotation deviennent **par abonné** ; `Societe` (aujourd'hui unique) devient une société par abonné.

### D-016 — Tarifs des abonnés — 2026-11-13
- **Décision** : une **liste commune de catégories** de véhicules (éco-berline, berline, grand coffre, van…, gérée par le super-admin). Chaque abonné **coche** celles qu'il propose et fixe **ses propres prix** (prise en charge, prix au km, seuil).
- **Raison** : les slugs restent cohérents dans tout le code (convention underscore) ; chaque abonné garde la main sur ses prix.
- **Impact** : séparer la *catégorie* (commune) de la *grille de prix* (par abonné ; SIMPLICI CAB a la sienne). Majoration de nuit et commission par abonné : à voir avec D-006.

### D-015 — Validation de la société d'un abonné — 2026-11-13
- **Décision** : **aucune pièce justificative** demandée. L'abonné saisit **une fois** les informations de sa société (raison sociale, SIREN/SIRET, adresse, TVA, REVTC…), puis le super-admin les **valide**. Avant validation, l'abonné n'a accès **qu'à ce formulaire** ; le reste de son espace est fermé.
- **Raison** : ZenCar vend un outil, l'abonné reste responsable de ses informations légales ; la validation sert à vérifier la cohérence avant qu'elles apparaissent sur ses bons et factures.
- **Impact** : états d'un abonné : **en attente de validation** → **actif** → **lecture seule** (D-014). Les infos validées alimentent ses bons de commande et factures (comme `Societe` aujourd'hui, mais une par abonné).

### D-014 — Abonnement : paiement et impayés — 2026-11-13
- **Décision** : paiement **manuel** (virement…) au démarrage ; le super-admin **active / désactive** le compte à la main. En cas d'impayé, le compte passe en **lecture seule** : consultation et téléchargement des factures et bons passés, mais plus de création.
- **Raison** : aucun développement de paiement pour les premiers abonnés ; la lecture seule respecte l'obligation de conservation des factures. Stripe pourra venir au-delà de 5-10 abonnés.
- **Impact** : un abonné a un **état** (en attente de validation / actif / lecture seule). Prix : question D-022.

### D-013 — Rôles : qui est qui ? — 2026-11-13
- **Décision** : 4 rôles.
  - **Super-admin** (toi) : valide les sociétés des abonnés, gère les abonnements, voit tout ce qui concerne SIMPLICI CAB.
  - **Régulateur** : travaille **pour SIMPLICI CAB** (pas un abonné). Il voit **uniquement les courses de SIMPLICI CAB** (dzencar.fr, réservations téléphone) et les **dispatche dans des groupes WhatsApp**. L'outil l'aide à partager (message prêt à envoyer).
  - **Abonné** : un chauffeur ou une société qui paie l'abonnement ; **1 abonnement = 1 seul compte** au démarrage (pas de sous-comptes chauffeurs). Il ne voit que ses propres données (D-011).
  - **Collaborateur** : établissement partenaire qui commande aux prix convenus (D-018, D-019).
- **Raison** : le dispatch WhatsApp est déjà la pratique réelle ; un seul compte par abonné garde le démarrage simple. Des comptes chauffeurs pour les sociétés abonnées pourront venir plus tard.
- **Impact** : le rôle Régulateur ne dépend pas du multi-entreprises et peut être construit **sur le site actuel** ; nouvelle question D-021 (droits du régulateur).

### D-012 — Le site public de réservation pour les abonnés — 2026-11-13
- **Décision** : au démarrage, **pas de réservation en ligne** pour les clients des abonnés : l'abonné saisit lui-même les courses dans son espace (sur le modèle de la « réservation téléphone »). Une page publique par abonné pourra venir plus tard.
- **Raison** : rien de public à construire ni à sécuriser au départ ; réutilise un écran qui existe déjà.
- **Impact** : la page « réservation téléphone » devient la base de la saisie de course dans l'espace abonné.

### D-011 — Quel modèle de plateforme ? — 2026-11-13
- **Décision** : ZenCar devient un **outil de gestion** (SaaS) : chaque abonné (chauffeur, régulateur) gère **ses propres** clients, courses, tarifs, bons de commande et factures dans son espace. ZenCar **ne distribue pas** de courses aux abonnés ; les clients de dzencar.fr restent ceux de SIMPLICI CAB.
- **Raison** : modèle le plus simple et le plus clair juridiquement (ZenCar vend un logiciel, pas des courses). Une place de marché pourra s'ajouter plus tard.
- **Impact** : les données de chaque abonné doivent être **séparées** (un abonné ne voit jamais les clients ni les courses d'un autre).

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
