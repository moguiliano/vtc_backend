# ZenCar — VTC Backend

Plateforme de réservation de véhicules avec chauffeur (VTC). Formulaire multi-étapes, tarification dynamique depuis la BDD, OTP par SMS, intégration HERE Maps et EasyAdmin.

## Stack technique

- **Backend** : Symfony 7.2, PHP 8.2, Doctrine ORM 3
- **Base de données** : MariaDB (production Infomaniak) / MySQL via MAMP en local (port 8889)
- **Frontend** : Twig + Bootstrap 5.3 + Webpack Encore
- **Admin** : EasyAdmin 4
- **SMS** : Twilio
- **Cartographie & tarification** : HERE Maps API (geocoding, routing, autocomplete)

## Structure du projet

```
src/
├── Controller/
│   ├── Admin/          # EasyAdmin (DashboardController, ReservationCrudController, VehicleCategoryCrudController)
│   ├── HomeController.php
│   ├── OtpController.php
│   └── ReservationController.php
├── Entity/             # Entreprise, Reservation, Facture, Societe, GrilleTarif, VehicleCategory, Forfait…
├── EventListener/      # EntrepriseParDefautListener (étiquette entreprise par défaut)
├── Form/
├── Repository/
├── Service/
│   ├── EntrepriseCourante.php    # Brique « Quelle entreprise ? » (aujourd'hui toujours le n°1)
│   ├── ReservationCreator.php    # Brique « Créer une réservation »
│   ├── Tarification.php          # Brique « Calculer un prix » (grille de l'entreprise)
│   ├── PdfGenerator.php          # Brique « Générer un PDF » (bon de commande, facture)
│   ├── HereMapsService.php       # Geocoding, routing (plus de calcul de prix)
│   ├── OtpService.php            # Cycle de vie OTP complet
│   ├── PhoneNormalizerService.php # Normalisation E.164
│   ├── SmsNotifier.php           # Envoi SMS réservation
│   └── TwilioService.php
├── DataFixtures/       # VehicleCategoryFixtures (4 véhicules + grille de prix de l'entreprise 1)
templates/
├── reservation/
│   ├── tab1.html.twig  # Départ / arrivée / date
│   ├── tab2.html.twig  # Choix véhicule (catégories proposées + prix_depart(vehicle))
│   └── tab3.html.twig  # Infos client + OTP
public/js/
└── reservation.js      # Logique frontend (calcul trajet, maj prix, labels dynamiques)
migrations/             # Doctrine migrations (lues depuis le dossier principal, pas le worktree)
```

## Entités principales

| Entité | Rôle |
|---|---|
| `Entreprise` | Propriétaire des données. SIMPLICI CAB = n°1 (seule pour l'instant, D-020) |
| `Reservation` | Réservation client (départ, arrivée, véhicule, distance, prix) — par entreprise |
| `Facture` / `Societe` | Factures (numéro unique par entreprise) / fiche légale (une par entreprise) |
| `VehicleCategory` | Catégories de véhicules **communes** (slug, nom, places, bagages) |
| `GrilleTarif` | Prix d'une catégorie pour une entreprise (D-016) |
| `VerificationCode` | OTP : code hashé, expiry, anti-spam, tentatives |
| `User` | Utilisateur (nom, prenom, email, téléphone) |
| `Admin` | Compte admin (login email + mot de passe) |

## Conventions importantes

### Slugs véhicules
Toujours en **underscore** : `eco_berline`, `grand_coffre`, `berline`, `van`.
Ne jamais utiliser de tirets (`eco-berline`) — cela casse la correspondance JS ↔ PHP ↔ BDD.

### Numéros de téléphone
Toujours normalisés en **E.164** via `PhoneNormalizerService`. Ne jamais dupliquer la logique de normalisation.

### OTP
Tout passe par `OtpService` : 5 min TTL, 60s anti-spam, 5 tentatives max. Les paramètres sont dans `config/services.yaml`.

### Entreprise (fondation multi-entreprises, D-020)
- Réservations, factures, société, grille de prix, forfaits, catégories de messages et appels portent un `entreprise_id`
  (interface `AppartientAEntreprise` + `EntrepriseTrait`). Toute nouvelle donnée « métier » doit faire pareil.
- « Pour quelle entreprise ? » → toujours `EntrepriseCourante::get()`, jamais `find(1)` ni « la première ligne ».
- Une donnée enregistrée sans entreprise reçoit l'entreprise courante (`EntrepriseParDefautListener`).
- Créer une réservation → `ReservationCreator` ; générer un PDF → `PdfGenerator` ; société → `SocieteRepository::pourEntreprise()`.
- Numéro de facture : `FactureRepository::nextNumero($entreprise, $date)` (suite propre à chaque entreprise, D-017).

### Tarification
- Les prix viennent de la BDD (`GrilleTarif` de l'entreprise), jamais hardcodés dans le PHP ou le Twig.
- Tout calcul de prix passe par `Tarification` : `estimerToutesCategories()` dans `calculateTrip()`,
  `categoriesProposees()` pour la liste des véhicules, `prix_depart(vehicle)` dans Twig.
- Paramètres configurables dans `services.yaml` : commission %, majoration nuit %, heures nuit.

### Frontend tab2
- `window.ZenCarVehicles` est injecté par Twig pour les labels dynamiques JS.
- Les `.vehicle-price[data-type]` et les `.vehicle-card[data-type]` doivent toujours être identiques (underscore).

## Serveurs de développement

```bash
# Symfony (PHP)
symfony serve --no-tls --port=8000

# Webpack (assets)
npm run watch

# Base de données MySQL via MAMP : port 8889
```

Ou via `.claude/launch.json` : serveurs "Symfony PHP Server" et "Webpack Encore (watch)".

## Commandes utiles

```bash
# Migrations
php bin/console doctrine:migrations:migrate

# Fixtures (données de test véhicules)
APP_ENV=dev php bin/console doctrine:fixtures:load --append

# Vider le cache
php bin/console cache:clear

# Linter Twig
php bin/console lint:twig templates/
```

## Variables d'environnement requises

```
DATABASE_URL=mysql://user:pass@127.0.0.1:8889/zencar
HERE_API_KEY=...
TWILIO_SID=...
TWILIO_AUTH_TOKEN=...
TWILIO_FROM=+33...
```

Définies dans `.env.local` (jamais commitées).

## Git

- **Branche principale** : `main`
- **Branche de travail** : `feature/contribution`
- **Remote** : `https://github.com/moguiliano/vtc_backend.git`
- Les migrations générées dans un worktree doivent être copiées dans `migrations/` du projet principal.

## Points d'attention

- Worktrees Claude : **pas de symlink `vendor/`** (PHP suit le lien et charge alors le `src/` et le `.env` du dossier principal).
  Copier `vendor/` puis `composer dump-autoload` ; `var/` propre au worktree ; copier aussi `.env`, `public/build/`
  et `assets/vendor/`. `.env.local` du worktree : pointer sur une **copie** de la base (ex. `zencar_fondation`),
  car le `.env` contient la base de prod.
- EasyAdmin : ne pas utiliser `->setExtra('firewall_name', ...)` sur `LogoutMenuItem` (non supporté).
- Les fixtures `VehicleCategoryFixtures` doivent tourner en `APP_ENV=dev`.
