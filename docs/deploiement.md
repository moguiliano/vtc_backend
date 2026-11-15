# Déploiement — Infomaniak

> Check-list à suivre **dans l'ordre** à chaque mise en prod. Méthode : **SSH + `git pull`**.
> Les valeurs entre `< >` sont à remplacer ; note-les une fois pour toutes dans « Mes infos serveur ».

## Mes infos serveur (à remplir une fois)

| Info | Valeur | Comment la trouver |
|---|---|---|
| Connexion SSH | `<identifiant>@<serveur>.ftp.infomaniak.com` | Manager Infomaniak → Hébergement → FTP / SSH |
| Dossier du site | `~/sites/dzencar.fr` | Relevé le 2026-11-13 |
| Version de PHP | `<8.2 ou plus>` | En SSH : `php -v` (doit être ≥ 8.2) |
| Fichier `.env.local` de prod | `~/sites/dzencar.fr/.env.local` | Il existe aussi `.env.local.bak` (copie du 2026-11-13) |
| Version de la base | **MariaDB 10.6.28** | `php bin/console dbal:run-sql "SELECT VERSION()"` |

- Hébergement : **Infomaniak** · Base de données : **MariaDB 10.6.28** · Domaine : `dzencar.fr`
- Le `.env.local` de prod contient `DATABASE_URL`, `HERE_API_KEY`, `TWILIO_*` (voir `CLAUDE.md`). **Il ne va jamais dans Git.**
- Dans `DATABASE_URL`, `serverVersion` doit être la **vraie** version : `serverVersion=10.6.28-MariaDB`.
  Une version trop récente (ex. `mariadb-10.11.2`) fait échouer les migrations (`Unknown column 'ccsa.FULL_COLLATION_NAME'`).
- `APP_DEBUG` doit rester à **0** en prod (`grep '^APP_DEBUG' .env.local`). Si tu l'actives pour chercher une erreur, remets-le à 0 juste après, puis `cache:clear --env=prod --no-debug`.

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
   cd ~/sites/dzencar.fr
   ```
4. **Sauvegarder la base**, à chaque fois. Le bloc lit lui-même les infos dans `.env.local` (rien à recopier) :
   ```bash
   L=$(grep '^DATABASE_URL' .env.local | tail -1)
   H=$(echo "$L" | sed -E 's#.*@([^:/]+).*#\1#')
   U=$(echo "$L" | sed -E 's#.*mysql://([^:]+):.*#\1#')
   D=$(echo "$L" | sed -E 's#.*/([^/?"]+).*$#\1#')
   echo "hote=$H  utilisateur=$U  base=$D"
   mysqldump -h "$H" -u "$U" -p "$D" > ~/sauvegarde-$(date +%Y%m%d-%H%M).sql
   ls -lh ~/sauvegarde-*        # la dernière doit faire plusieurs centaines de Ko, pas 0
   ```
   Le mot de passe demandé est celui de la base (dans `DATABASE_URL`, entre `utilisateur:` et `@`). Rien ne s'affiche quand on le tape.
5. Récupérer le code :
   ```bash
   git status          # doit dire « On branch main » et rien de modifié
   git pull origin main
   ```
6. **Seulement si `composer.json` ou `composer.lock` font partie du `git pull`** : installer les dépendances PHP
   (lance aussi `cache:clear`, `assets:install` et `importmap:install`) :
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
7. **Regarder** les migrations avant de les lancer (le `2>/dev/null` masque le journal de débogage) :
   ```bash
   php bin/console doctrine:migrations:list 2>/dev/null
   ```
   S'il n'y a aucune ligne `not migrated`, passer à l'étape 8. Sinon :
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```
   ⚠️ Avant le 2026-11-13, le registre des migrations de prod était incomplet (tables créées hors migrations).
   Il a été remis à jour ; si une migration réclame de créer une table qui existe déjà, voir « En cas de problème ».
8. Vider le cache de prod :
   ```bash
   php bin/console cache:clear --env=prod --no-debug
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
| `Unknown column 'ccsa.FULL_COLLATION_NAME'` | `serverVersion` faux dans `.env.local` : mettre la vraie version (voir « Mes infos serveur »), puis étape 8 |
| `Table '…' already exists` / `Duplicate column` | Le registre ne connaît pas une migration déjà faite à la main. **Ne rien forcer** : comparer avec `php bin/console doctrine:schema:update --dump-sql 2>/dev/null` (lecture seule) ; si ce que crée la migration existe déjà, la marquer faite : `php bin/console doctrine:migrations:version 'DoctrineMigrations\VersionXXXX' --add --no-interaction` |
| Page d'erreur Symfony détaillée (avec liens vers symfony.com) | Mode debug actif : `APP_DEBUG=0` dans `.env.local`, puis étape 8 |

**Revenir à la version précédente** (si la nouvelle version casse le site) :
```bash
git log --oneline -5              # repérer le commit d'avant
git checkout <commit-d-avant>     # remettre l'ancien code
php bin/console cache:clear --env=prod --no-debug
```
Puis revenir sur `main` (`git checkout main`) une fois le problème corrigé.
