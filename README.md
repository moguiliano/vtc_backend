# ZenCAR Taxi & VTC — Marseille

Site de réservation de taxis et VTC à Marseille : [dzencar.fr](https://dzencar.fr)

- Réservation VTC en ligne avec prix calculé avant confirmation (HERE Maps)
- Vérification du téléphone par SMS (Twilio)
- Forfaits VTC à prix fixe (aéroport, gare…)
- Administration EasyAdmin : réservations, forfaits, véhicules, factures

## Stack

Symfony 7.2 · PHP 8.2 · Doctrine · MariaDB / MySQL · Twig + Bootstrap 5 · Webpack Encore · React (ux-react, en cours) · EasyAdmin 4

## Démarrer en local

```bash
composer install
npm install
php bin/console doctrine:migrations:migrate
APP_ENV=dev php bin/console doctrine:fixtures:load --append
symfony serve --no-tls --port=8000
npm run watch
```

Variables à définir dans `.env.local` : voir [`CLAUDE.md`](CLAUDE.md).

## Documentation

| Où | Quoi |
|---|---|
| [`docs/`](docs/README.md) | Roadmap, journal, décisions, git, déploiement, marketing |
| [`CLAUDE.md`](CLAUDE.md) | Règles techniques et conventions du code |
