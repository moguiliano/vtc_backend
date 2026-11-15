# Roadmap ZenCar

> Règle : **3 objectifs « En cours » maximum.** Une nouvelle idée va dans « En attente », jamais directement en cours.

## 🔥 En cours (3 max)

| # | Objectif | Chantier | Branche | Prochaine étape |
|---|---|---|---|---|
| 1 | **Fondation « Entreprise »** (D-020 étape 0) | 🗄️ Données | `feature/fondation-entreprise` | Libérer de la place disque (session bloquée), puis plan avant code ; inclut le rattachement des factures et de la société déjà en prod à l'entreprise n°1 |
| 2 | Facturation (factures + fiche société) | 🧩 Fonctionnalités | `feature/facturation` | **Mise en prod seule le 2026-11-15** (D-020 modifiée) ; ensuite : « brouillon → émise » (D-001, D-003) |
| 3 | Campagne Google Ads — 5 groupes | 📣 Marketing | — | Créer les 5 groupes dans l'interface Google Ads |

## 🏗️ Plateforme multi-entreprises (D-011 à D-021) — ordre décidé (D-020)

0. **Fondation** : entité Entreprise ; réservations, factures, bons, société, grille de prix rattachés à une entreprise ; briques centrales *créer une réservation* / *générer un PDF* / *calculer un prix* → **En cours n°1**
1. **Facturation** → mise en prod **avant** la fondation (modification du 2026-11-15) ; sera rattachée à l'entreprise n°1 à l'étape 0
2. [ ] **Régulateur** (D-013, D-021) : `ROLE_REGULATEUR`, voit tout sur les courses de SIMPLICI CAB, ne touche ni aux tarifs, ni à « Ma société », ni aux comptes, ni à la création de factures ; partage en 2 temps sur WhatsApp et Telegram : annonce groupe (zone approximative, date/heure, véhicule, prix chauffeur seul) → réponses avec temps d'approche → le régulateur choisit → course complète en message privé au chauffeur retenu
3. [ ] **Collaborateurs** (D-018, D-019) : comptes établissements de SIMPLICI CAB, forfaits propres + remise %, paiement « sur le compte » (facture mensuelle) ou par le passager, au choix par commande
4. [ ] **Espaces abonnés** (D-011 à D-017) : outil de gestion, 1 compte par abonné, saisie société une fois puis validation super-admin (rien d'accessible avant), états en attente / actif / lecture seule, paiement manuel, catégories communes + prix propres, bons et factures à son nom avec sa numérotation. Prix de l'abonnement : D-022 (reporté)

## ⏳ En attente

### 🧩 Fonctionnalités
- [ ] **Facture brouillon → émise** (D-001, D-003) — branche `feature/facturation-emission` :
  statut brouillon/émise, bouton « Émettre », facture verrouillée une fois émise,
  numéro `FAC-AAAA-0001` attribué à l'émission (pas à la création), avoir pour corriger une facture émise
- [ ] TVA des factures : en attente de la réponse du comptable (D-002)
- [ ] **Statut de réservation** (D-005, D-010) : les 7 statuts existent déjà (`App\Enum\ReservationStatus`) et sont modifiables dans EasyAdmin ;
  confirmation auto déjà en place. Reste seulement : boutons rapides « Annuler » et « Terminée » + filtre par statut (à vérifier)
- [ ] Service Mailer : confirmation client, notification admin, accusé contact
- [ ] Séparer prénom / nom sur `User` (`getFirstname()` manquant dans SmsNotifier — à vérifier)
- [ ] Supprimer les données de test fictives dans `ReservationController` (fallback « Karim ») — à vérifier
- [ ] Page À propos
- [ ] Tests PHPUnit : PhoneNormalizerService, calcul de prix HereMapsService, Mailer

### 🗄️ Données
- [ ] `PricingRule` (majoration nuit, commission) en BDD au lieu de `services.yaml`
- [ ] `SiteSettings` (téléphone, textes SMS…) en BDD — **commencé** : la table existe avec la note et le nombre d'avis Google (D-009)
- [ ] `GuestInfo` : sortir le JSON `guestInfo` de `Reservation`

### ⚛️ React — en pause (décision du 2026-11-13)
- [ ] Reprendre la migration React (`feature/react`) : ux-react installé, premier vrai composant à écrire (c'est toi qui codes)
- [ ] Nettoyer `assets/styles/app.css` (thème sombre en cours, non commité, erreurs CSS)
- [ ] Remplacer `public/js/reservation.js` (vanilla) par des composants React, étape par étape
  (attention : il sert aussi à la page admin « réservation téléphone », mode `data-admin-mode="phone"`)

### 📣 Marketing / SEO
- [ ] Compléter le footer (mentions légales, RGPD, liens, téléphone)
- [x] Remplacer les 3 avis statiques par la vraie note Google (D-009) — `feature/marketing`, 2026-11-15 : note + nombre d'avis modifiables dans EasyAdmin > Avis Google (table `site_settings`, **migration à lancer en prod**)
- [x] Urgences de la priorisation marketing (2026-11-15, `feature/marketing`) : promesses « taxi agréé », « plateforme », photo, horaires et zone des données structurées, FAQ seniors « non remboursé », bannière cookies + Consent Mode v2 (GTM chargé seulement après accord)

### 🚀 Infra
- [ ] 🔴 **Confidentialité (prioritaire)** : `/reservation/{id}/bon-commande.pdf` est **public** et les numéros se suivent → n'importe qui peut télécharger le bon de commande d'un autre client (nom, téléphone, adresses) en changeant le numéro. Le lien est donné au client sur la page de succès. Correction : lien **signé** (`UriSigner`) ou identifiant aléatoire, et accès libre seulement pour l'admin. Vérifier aussi `/reservation/{id}/confirm` et `/success`.
- [ ] Formulaire admin EasyAdmin : choix `typeVehicule` codés en dur dans `ReservationCrudController` → les lire depuis `VehicleCategory`
- [ ] Mettre Git à jour (`brew install git`, version actuelle 2.15 de 2017)
- [ ] **Aligner la base de prod sur le code** : `doctrine:schema:update --dump-sql` montre 7 retouches (champs NULL/NOT NULL, 2 index sur `verification_code`).
  Vérifier d'abord en local la taille du champ `verification_code.code` (VARCHAR(6) alors que le code OTP est hashé ?), puis écrire une vraie migration.
- [ ] Sécurité : `/reservation/calculate-trip` renvoie la **commission** et le **net chauffeur** à n'importe quel visiteur → ne renvoyer que le prix total
- [ ] Vérifier que le `.env` local ne pointe pas vers la base de prod (seul `.env.local` doit définir la base locale)

## ✅ Terminé (récent)
- 2026-11-15 — **Mise en prod** des corrections d'affichage (PR #3) : champs vides masqués (récap + fiche admin), zoom mobile, débordement mobile, marge de la carte
- 2026-11-13 — **Mise en prod** de la réservation admin par téléphone + registre des migrations réparé + mode debug coupé en prod
- 2026-11-13 — Procédure de déploiement Infomaniak (`docs/deploiement.md`), testée en vrai
- 2026-11-13 — Réservation admin par téléphone (PR #1)
- 2026-11-10 — Organisation : Pilote + sections, dossier `docs/`, branches Facturation / React séparées
- 2026-11 — Retrait AssetMapper et Turbo, Webpack Encore seul outil JS
- 2026-11 — `createdAt` éditable sur les réservations, bon de commande horodaté
- 2026 — Pages SEO (aéroport, gare, calanques, seniors), OG + JSON-LD, sitemap, Forfaits VTC en BDD, login admin sécurisé
