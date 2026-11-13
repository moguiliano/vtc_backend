# Roadmap ZenCar

> Règle : **3 objectifs « En cours » maximum.** Une nouvelle idée va dans « En attente », jamais directement en cours.

## 🔥 En cours (3 max)

| # | Objectif | Chantier | Branche | Prochaine étape |
|---|---|---|---|---|
| 1 | Facturation (factures + fiche société) | 🧩 Fonctionnalités | `feature/facturation` | Tester dans EasyAdmin → PR vers `main` → mise en prod. Puis `feature/facturation-emission` (voir En attente) |
| 2 | Migration React — formulaire de réservation | ⚛️ React | `feature/react` | ux-react installé ; premier vrai composant à écrire (c'est toi qui codes) |
| 3 | Campagne Google Ads — 5 groupes | 📣 Marketing | — | Créer les 5 groupes dans l'interface Google Ads |

## ⏳ En attente

### 🧩 Fonctionnalités
- [ ] **Facture brouillon → émise** (D-001, D-003) — branche `feature/facturation-emission` :
  statut brouillon/émise, bouton « Émettre », facture verrouillée une fois émise,
  numéro `FAC-AAAA-0001` attribué à l'émission (pas à la création), avoir pour corriger une facture émise
- [ ] TVA des factures : en attente de la réponse du comptable (D-002)
- [ ] **Statut de réservation** (D-005) : en attente → confirmée (auto à la validation SMS) → terminée / annulée ;
  boutons « Annuler » et « Terminée » dans EasyAdmin (admin seulement, D-010), filtre par statut
- [ ] Service Mailer : confirmation client, notification admin, accusé contact
- [ ] Séparer prénom / nom sur `User` (`getFirstname()` manquant dans SmsNotifier — à vérifier)
- [ ] Supprimer les données de test fictives dans `ReservationController` (fallback « Karim ») — à vérifier
- [ ] Page À propos
- [ ] Tests PHPUnit : PhoneNormalizerService, calcul de prix HereMapsService, Mailer

### 🗄️ Données
- [ ] `PricingRule` (majoration nuit, commission) en BDD au lieu de `services.yaml`
- [ ] `SiteSettings` (téléphone, textes SMS…) en BDD
- [ ] `GuestInfo` : sortir le JSON `guestInfo` de `Reservation`

### ⚛️ React
- [ ] Nettoyer `assets/styles/app.css` (thème sombre en cours, non commité, erreurs CSS)
- [ ] Remplacer `public/js/reservation.js` (vanilla) par des composants React, étape par étape
  (attention : il sert aussi à la page admin « réservation téléphone », mode `data-admin-mode="phone"`)

### 📣 Marketing / SEO
- [ ] Compléter le footer (mentions légales, RGPD, liens, téléphone)
- [ ] Remplacer les 3 avis statiques par de vrais avis Google

### 🚀 Infra
- [ ] Écrire la procédure de déploiement réelle dans `deploiement.md`
- [ ] Mettre Git à jour (`brew install git`, version actuelle 2.15 de 2017)

## ✅ Terminé (récent)
- 2026-11-13 — Réservation admin par téléphone (`feature/reservation-admin`, testée) — reste : PR vers `main` + mise en prod
- 2026-11-10 — Organisation : Pilote + sections, dossier `docs/`, branches Facturation / React séparées
- 2026-11 — Retrait AssetMapper et Turbo, Webpack Encore seul outil JS
- 2026-11 — `createdAt` éditable sur les réservations, bon de commande horodaté
- 2026 — Pages SEO (aéroport, gare, calanques, seniors), OG + JSON-LD, sitemap, Forfaits VTC en BDD, login admin sécurisé
