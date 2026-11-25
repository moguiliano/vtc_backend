# Roadmap ZenCar

> Règle : **3 objectifs « En cours » maximum.** Une nouvelle idée va dans « En attente », jamais directement en cours.

## 🔥 En cours (3 max)

| # | Objectif | Chantier | Branche | Prochaine étape |
|---|---|---|---|---|
| 1 | Facturation (factures + fiche société) | 🧩 Fonctionnalités | `feature/facturation` | **Mise en prod seule le 2026-11-15** (D-020 modifiée) ; ensuite : « brouillon → émise » (D-001, D-003) |
| 2 | Campagne Google Ads — 5 groupes | 📣 Marketing | — | Créer les 5 groupes dans l'interface Google Ads |

## 🏗️ Plateforme multi-entreprises (D-011 à D-021) — ordre décidé (D-020, modifié par D-072 : abonnés avant collaborateurs)

0. [x] **Fondation** (PR #12, **en prod le 2026-11-23**) : entité Entreprise ; réservations, factures (numérotation par entreprise), société, grille de prix, forfaits, messages, appels rattachés à une entreprise ; briques `EntrepriseCourante`, `ReservationCreator`, `PdfGenerator`, `Tarification`. **Pas encore fait** (prévu aux étapes 2 à 4) : filtrer les listes admin par entreprise, entreprise de l'utilisateur connecté, état de l'abonné
1. **Facturation** → mise en prod **avant** la fondation (modification du 2026-11-15) ; sera rattachée à l'entreprise n°1 à l'étape 0
2. [x] **Régulateur** (D-013, D-021, D-064 à D-071, PR #15 et #16, **en prod le 2026-11-23**) : compte créé dans « Administrateurs » (2 droits : Super Admin / Régulateur) ; menus interdits fermés même en tapant l'adresse ; factures en lecture seule ; champ « Chauffeur retenu » et trace « Modifiée par / le » ; messages reçus visibles. `ROLE_REGULATEUR`, voit tout sur les courses de SIMPLICI CAB, ne touche ni aux tarifs, ni à « Ma société », ni aux comptes, ni à la création de factures ; partage en 2 temps sur WhatsApp et Telegram : annonce groupe (zone approximative, date/heure, véhicule, prix chauffeur seul) → réponses avec temps d'approche → le régulateur choisit → course complète en message privé au chauffeur retenu
3. [ ] **Collaborateurs** (après les Espaces abonnés, D-072) (D-018, D-019) : comptes établissements de SIMPLICI CAB, forfaits propres + remise %, paiement « sur le compte » (facture mensuelle) ou par le passager, au choix par commande
4. [ ] 🔨 **Espaces abonnés — en cours** (fait avant les Collaborateurs, D-072 ; branche `feature/espaces-abonnes`) (D-011 à D-017, D-059) : 2 types d'abonnés, **chauffeur indépendant** et **dispatcheur** (= chauffeur indépendant + « Partager la course » sur WhatsApp, Telegram, SMS et partage) ; outil de gestion, 1 compte par abonné, saisie société une fois puis validation super-admin (rien d'accessible avant), états en attente / actif / lecture seule, paiement manuel, catégories communes + prix propres, bons et factures à son nom avec sa numérotation. Prix (D-022) : 10 € TTC / mois chauffeur, 30 € TTC / mois dispatcheur, 1 mois d'essai, paiement mensuel
   - Découpage (D-073) : [x] 1. compte et porte d'entrée (inscription `/espace/inscription`, connexion, « Ma société » puis validation, menu « Abonnés », cloisonnement — **en prod le 2026-11-25** (PR #18), 1 migration) · [ ] 2. ses courses (formulaire avec carte et prix automatique dans son espace, D-082 ; pas de formulaire public pour ses clients, D-012) · [ ] 3. ses prix · [ ] 4. ses bons et factures · [ ] 5. Partager la course (dispatcheur) · [ ] 6. fin d'essai / actif / lecture seule
   - Abonnés = futurs chauffeurs de ZenCAR (D-079) : [x] **A** « Chauffeur retenu » choisi parmi les abonnés validés (texte libre gardé, filtre « Abonné retenu », D-083 — testé en local, 1 migration) · [ ] plus tard **C** bourse aux courses (revoir D-011, sous-traitance, assurance, facturation)

## ⏳ En attente

### 🧩 Fonctionnalités
- [ ] **Tableau de bord régulateur** (D-070) : page d'accueil du régulateur avec les courses du jour et du lendemain, les courses sans chauffeur retenu (à dispatcher en priorité) et un bouton « Partager » par ligne. À revoir après quelques semaines d'usage réel.
- [ ] **Partager la course, suite** (idées gardées de D-028) : lien GPS Waze/Maps dans le partage complet ; trace des envois (« Remerciement envoyé le… ») ;
  ~~champ « chauffeur assigné »~~ → fait avec le Régulateur (« Chauffeur retenu », D-067)
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
- [ ] Tests PHPUnit : PhoneNormalizerService, Mailer (calcul de prix et création de réservation : faits avec la fondation, `tests/Service/`)
- [ ] Fiche détail d'une facture (`/admin/facture/{id}`) en erreur 500 en mode dev : `ChoiceField` taux de TVA avec des nombres à virgule (constaté le 2026-11-23, déjà là avant la fondation)
- [ ] Route `/reservation/{id}/confirm` déclarée deux fois (`HomeController` et `ReservationController`, c'est la 2ᵉ qui sert) : supprimer la copie inutile

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
- [x] 🔴 **Confidentialité** (2026-11-16, branche `docs/prod-facturation`, **à mettre en prod**) : bon de commande PDF et page de succès protégés par un jeton secret dans le lien (`?t=…`, `App\Service\ReservationAccess`) ; confirmation (`/reservation/{id}/confirm`) acceptée seulement depuis le navigateur qui a créé la réservation ; l'admin et le régulateur gardent l'accès libre. Les anciens liens sans jeton ne marchent plus (sauf pour l'admin).
- [ ] Sécurité : le code OTP n'est vérifié que dans le navigateur ; `/confirm` ne vérifie pas côté serveur que le téléphone a été validé → lier la vérification OTP à la réservation
- [ ] Formulaire admin EasyAdmin : choix `typeVehicule` codés en dur dans `ReservationCrudController` → les lire depuis `VehicleCategory`
- [ ] Mettre Git à jour (`brew install git`, version actuelle 2.15 de 2017)
- [ ] **Aligner la base de prod sur le code** : `doctrine:schema:update --dump-sql` montre 7 retouches (champs NULL/NOT NULL, 2 index sur `verification_code`).
  Vérifier d'abord en local la taille du champ `verification_code.code` (VARCHAR(6) alors que le code OTP est hashé ?), puis écrire une vraie migration.
- [ ] Sécurité : `/reservation/calculate-trip` renvoie la **commission** et le **net chauffeur** à n'importe quel visiteur → ne renvoyer que le prix total
- [ ] Vérifier que le `.env` local ne pointe pas vers la base de prod (seul `.env.local` doit définir la base locale)

## ✅ Terminé (récent)
- 2026-11-25 — **Mise en prod** de la page « Espace chauffeurs pro » `/chauffeurs-vtc` + lien en pied de page (PR #19)
- 2026-11-25 — **Mise en prod** des espaces abonnés, étape 1 (PR #18) : inscription `/espace/inscription`, « Ma société », menu « Abonnés », cloisonnement par entreprise ; 1 migration
- 2026-11-23 — **Mise en prod** de la fondation « Entreprise » (PR #12) : 7 migrations, prix et données inchangés, nouveau menu « Grille de prix »
- 2026-11-17 — **Mise en prod** de « Partager la course » (PR #7, D-023 à D-031 : partage complet / partiel / client, SMS-WhatsApp-Copier, commission enregistrée, sous-menu Messages avec catégories et modèles) et de la réservation par téléphone avec date passée (PR #8, D-032), migration comprise ; vérifiées en ligne le 2026-11-19
- 2026-11-15 — **Mise en prod** des corrections d'affichage (PR #3) : champs vides masqués (récap + fiche admin), zoom mobile, débordement mobile, marge de la carte
- 2026-11-13 — **Mise en prod** de la réservation admin par téléphone + registre des migrations réparé + mode debug coupé en prod
- 2026-11-13 — Procédure de déploiement Infomaniak (`docs/deploiement.md`), testée en vrai
- 2026-11-13 — Réservation admin par téléphone (PR #1)
- 2026-11-10 — Organisation : Pilote + sections, dossier `docs/`, branches Facturation / React séparées
- 2026-11 — Retrait AssetMapper et Turbo, Webpack Encore seul outil JS
- 2026-11 — `createdAt` éditable sur les réservations, bon de commande horodaté
- 2026 — Pages SEO (aéroport, gare, calanques, seniors), OG + JSON-LD, sitemap, Forfaits VTC en BDD, login admin sécurisé
