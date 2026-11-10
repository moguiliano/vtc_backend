# Déploiement — Infomaniak

> ⚠️ **À compléter ensemble** lors d'une session 🚀 Infra : seules les infos connues sont notées ici.
> L'objectif est une check-list qu'on suit sans réfléchir à chaque mise en prod.

## Ce qu'on sait

- Hébergement : **Infomaniak**
- Base de données production : **MariaDB**
- Domaine : `dzencar.fr`
- Variables d'environnement requises : voir `CLAUDE.md` (`DATABASE_URL`, `HERE_API_KEY`, `TWILIO_*`)

## À renseigner

- [ ] Comment le code arrive sur le serveur (git pull en SSH ? FTP ? autre ?)
- [ ] Chemin du projet sur le serveur
- [ ] Version de PHP sur le serveur
- [ ] Où est le `.env.local` de production

## Check-list de mise en prod (brouillon)

1. La branche est fusionnée dans `main` et poussée sur GitHub
2. Sur le serveur : récupérer `main`
3. `composer install --no-dev --optimize-autoloader`
4. `npm ci && npm run build` (ou envoyer `public/build/` déjà construit)
5. `php bin/console doctrine:migrations:migrate --no-interaction` (⚠️ sauvegarder la BDD avant)
6. `php bin/console cache:clear --env=prod`
7. Vérifier : page d'accueil, calcul d'un trajet, envoi d'un OTP, accès `/admin`
