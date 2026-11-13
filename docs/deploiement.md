# Déploiement — Infomaniak

> Check-list à suivre **dans l'ordre** à chaque mise en prod. Méthode : **SSH + `git pull`**.
> Les valeurs entre `< >` sont à remplacer ; note-les une fois pour toutes dans « Mes infos serveur ».

## Mes infos serveur (à remplir une fois)

| Info | Valeur | Comment la trouver |
|---|---|---|
| Connexion SSH | `<identifiant>@<serveur>.ftp.infomaniak.com` | Manager Infomaniak → Hébergement → FTP / SSH |
| Dossier du site | `<chemin-du-site>` | En SSH : `cd` dans le dossier du site puis `pwd` |
| Version de PHP | `<8.2 ou plus>` | En SSH : `php -v` (doit être ≥ 8.2) |
| Fichier `.env.local` de prod | `<chemin-du-site>/.env.local` | En SSH : `ls -a` dans le dossier du site |

- Hébergement : **Infomaniak** · Base de données : **MariaDB** · Domaine : `dzencar.fr`
- Le `.env.local` de prod contient `APP_ENV=prod`, `DATABASE_URL`, `HERE_API_KEY`, `TWILIO_*` (voir `CLAUDE.md`). **Il ne va jamais dans Git.**

---

## Avant de commencer (sur ton Mac)

1. La branche est **fusionnée dans `main`** sur GitHub (Pull Request « Merged »).
2. **Seulement si le dossier `assets/` a changé** : construire et envoyer les fichiers CSS/JS.
   > Aujourd'hui, ce n'est **pas nécessaire** : `assets/` n'a pas bougé depuis le premier commit (nov. 2025).
   > Le style du site vient de `public/css/` et le JS de `public/js/`, qui sont dans Git et arrivent avec le `git pull`.
   > Cela deviendra utile avec Webpack Encore / React, qui écrivent dans `public/build/` (dossier hors de Git).

   Pour savoir si `assets/` a changé depuis la dernière mise en prod :
   ```bash
   git log --oneline -5 -- assets/
   ```
   Si un commit récent apparaît, construire puis envoyer `public/build/` :
   ```bash
   npm ci
   npm run build
   scp -r public/build <identifiant>@<serveur>.ftp.infomaniak.com:<chemin-du-site>/public/
   ```

## Sur le serveur (en SSH)

3. Se connecter et aller dans le dossier du site :
   ```bash
   ssh <identifiant>@<serveur>.ftp.infomaniak.com
   cd <chemin-du-site>
   ```
4. **Sauvegarder la base** (seulement s'il y a de nouvelles migrations, mais c'est plus sûr de le faire à chaque fois) :
   ```bash
   mysqldump -h <hote-bdd> -u <utilisateur-bdd> -p <nom-bdd> > ~/sauvegarde-$(date +%Y%m%d-%H%M).sql
   ```
   Les valeurs `<hote-bdd>`, `<utilisateur-bdd>`, `<nom-bdd>` sont dans la ligne `DATABASE_URL` du `.env.local`. Le mot de passe est demandé.
5. Récupérer le code :
   ```bash
   git status          # doit dire « On branch main » et rien de modifié
   git pull origin main
   ```
6. Installer les dépendances PHP (lance aussi `cache:clear`, `assets:install` et `importmap:install`) :
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
7. Appliquer les migrations (s'il n'y en a pas, la commande le dit et ne fait rien) :
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```
8. Vider le cache de prod :
   ```bash
   php bin/console cache:clear --env=prod
   ```

## Vérifier (dans le navigateur)

9. Sur `https://dzencar.fr` :
    - [ ] la page d'accueil s'affiche avec son style ;
    - [ ] calcul d'un trajet dans le formulaire (onglet 2 : les prix apparaissent) ;
    - [ ] `/admin` : connexion et liste des réservations OK ;
    - [ ] la nouveauté de cette mise en prod fonctionne.
10. Noter la mise en prod dans `docs/JOURNAL.md`.

---

## En cas de problème

| Symptôme | À faire |
|---|---|
| Page blanche / erreur 500 | En SSH : `tail -50 var/log/prod.log` pour lire l'erreur |
| Site sans style | Vérifier que `public/css/` est bien à jour (`git status`), puis étape 8 |
| Admin sans style | `php bin/console assets:install public` puis étape 8 |
| `git pull` refuse (fichiers modifiés sur le serveur) | **Ne rien forcer** : `git status`, noter les fichiers, demander de l'aide |
| Migration en erreur | Ne pas relancer en boucle ; la sauvegarde de l'étape 4 permet de revenir en arrière |

**Revenir à la version précédente** (si la nouvelle version casse le site) :
```bash
git log --oneline -5              # repérer le commit d'avant
git checkout <commit-d-avant>     # remettre l'ancien code
php bin/console cache:clear --env=prod
```
Puis revenir sur `main` (`git checkout main`) une fois le problème corrigé.
