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
├── Entity/             # Reservation, VehicleCategory, VerificationCode, User, Admin, Contact
├── Form/
├── Repository/
├── Service/
│   ├── HereMapsService.php       # Geocoding, routing, calcul tarifaire
│   ├── OtpService.php            # Cycle de vie OTP complet
│   ├── PhoneNormalizerService.php # Normalisation E.164
│   ├── SmsNotifier.php           # Envoi SMS réservation
│   └── TwilioService.php
├── DataFixtures/       # VehicleCategoryFixtures (4 véhicules avec prix)
templates/
├── reservation/
│   ├── tab1.html.twig  # Départ / arrivée / date
│   ├── tab2.html.twig  # Choix véhicule (dynamique depuis VehicleCategory)
│   └── tab3.html.twig  # Infos client + OTP
public/js/
└── reservation.js      # Logique frontend (calcul trajet, maj prix, labels dynamiques)
migrations/             # Doctrine migrations (lues depuis le dossier principal, pas le worktree)
```

## Entités principales

| Entité | Rôle |
|---|---|
| `Reservation` | Réservation client (départ, arrivée, véhicule, distance, prix) |
| `VehicleCategory` | Catégories de véhicules + grille tarifaire (depuis la BDD) |
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

### Tarification
- Les prix viennent de la BDD (`VehicleCategory`), jamais hardcodés dans le PHP ou le Twig.
- `HereMapsService::estimerToutesCategoriesActives()` retourne tous les véhicules actifs — utiliser cette méthode dans `calculateTrip()`.
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

- Le dossier `vendor/` et `var/` ne sont pas présents dans les worktrees Claude → symlinks nécessaires.
- EasyAdmin : ne pas utiliser `->setExtra('firewall_name', ...)` sur `LogoutMenuItem` (non supporté).
- Les fixtures `VehicleCategoryFixtures` doivent tourner en `APP_ENV=dev`.
