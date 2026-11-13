# Roadmap ZenCar

> Règle : **3 objectifs « En cours » maximum.** Une nouvelle idée va dans « En attente », jamais directement en cours.

## 🔥 En cours (3 max)

| # | Objectif | Chantier | Branche | Prochaine étape |
|---|---|---|---|---|
| 1 | Facturation (factures + fiche société) | 🧩 Fonctionnalités | `feature/facturation` | Tester dans EasyAdmin → PR vers `main` → mise en prod. Puis `feature/facturation-emission` (voir En attente) |
| 2 | Campagne Google Ads — 5 groupes | 📣 Marketing | — | Créer les 5 groupes dans l'interface Google Ads |
| 3 | _(libre)_ | | | |

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
- [ ] `SiteSettings` (téléphone, textes SMS…) en BDD
- [ ] `GuestInfo` : sortir le JSON `guestInfo` de `Reservation`

### ⚛️ React — en pause (décision du 2026-11-13)
- [ ] Reprendre la migration React (`feature/react`) : ux-react installé, premier vrai composant à écrire (c'est toi qui codes)
- [ ] Nettoyer `assets/styles/app.css` (thème sombre en cours, non commité, erreurs CSS)
- [ ] Remplacer `public/js/reservation.js` (vanilla) par des composants React, étape par étape
  (attention : il sert aussi à la page admin « réservation téléphone », mode `data-admin-mode="phone"`)

### 📣 Marketing / SEO
- [ ] Compléter le footer (mentions légales, RGPD, liens, téléphone)
- [ ] Remplacer les 3 avis statiques par de vrais avis Google

### 🚀 Infra
- [ ] Mettre Git à jour (`brew install git`, version actuelle 2.15 de 2017)
- [ ] **Aligner la base de prod sur le code** : `doctrine:schema:update --dump-sql` montre 7 retouches (champs NULL/NOT NULL, 2 index sur `verification_code`).
  Vérifier d'abord en local la taille du champ `verification_code.code` (VARCHAR(6) alors que le code OTP est hashé ?), puis écrire une vraie migration.
- [ ] Sécurité : `/reservation/calculate-trip` renvoie la **commission** et le **net chauffeur** à n'importe quel visiteur → ne renvoyer que le prix total
- [ ] Vérifier que le `.env` local ne pointe pas vers la base de prod (seul `.env.local` doit définir la base locale)

## ✅ Terminé (récent)
- 2026-11-13 — **Mise en prod** de la réservation admin par téléphone + registre des migrations réparé + mode debug coupé en prod
- 2026-11-13 — Procédure de déploiement Infomaniak (`docs/deploiement.md`), testée en vrai
- 2026-11-13 — Réservation admin par téléphone (PR #1)
- 2026-11-10 — Organisation : Pilote + sections, dossier `docs/`, branches Facturation / React séparées
- 2026-11 — Retrait AssetMapper et Turbo, Webpack Encore seul outil JS
- 2026-11 — `createdAt` éditable sur les réservations, bon de commande horodaté
- 2026 — Pages SEO (aéroport, gare, calanques, seniors), OG + JSON-LD, sitemap, Forfaits VTC en BDD, login admin sécurisé
