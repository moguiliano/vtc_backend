# Journal de bord

> 3 lignes par session, la plus récente en haut. Écrit à chaque « clôture ».
> Format : **Fait** / **Bloqué** / **Prochaine étape**

---

### 2026-11-13 — Mise en prod réservation téléphone (Pilote, 🚀 Infra)
- **Fait** : sauvegarde BDD (844 Ko) ; `git pull` 353a2dc → 393df89 ; `serverVersion` corrigé (MariaDB réelle : 10.6.28) ; registre des migrations réparé (4 migrations déjà appliquées à la main, marquées faites ; 1 jouée : 20260412000000) ; `APP_DEBUG=1` trouvé en prod et coupé ; accueil et calcul de trajet vérifiés en ligne. `deploiement.md` complété avec tout ça.
- **Bloqué** : 7 retouches de schéma en attente (voir ROADMAP Infra) ; test de la page admin « réservation téléphone » en prod à faire par toi (connexion admin).
- **Prochaine étape** : tester l'admin en prod ; puis Facturation (test EasyAdmin → PR → mise en prod).

### 2026-11-13 — Réservation admin par téléphone (🧩 Fonctionnalités)
- **Fait** : page `/admin/reservation-telephone` (menu « Nouvelle réservation (téléphone) ») : onglets 1-2 du site public réutilisés, onglet 3 admin sans OTP, prix calculé modifiable, SMS client optionnel, statut « Confirmée » ; récap partagé dans `reservation/_recap.html.twig` ; testée dans le navigateur, formulaire public OK. Branche `feature/reservation-admin` (non poussée).
- **Bloqué** : rien. L'envoi réel du SMS n'a pas été testé (même `SmsNotifier` que le site public). Réservation de test n° 169 « TestClaude » à supprimer dans la base locale.
- **Prochaine étape** : pousser `feature/reservation-admin` et ouvrir la PR vers `main`, puis mettre en prod.

### 2026-11-12 — Séance de décisions Facturation + Réservation (Pilote)
- **Fait** : D-001 (brouillon → émise, avoir), D-003 (`FAC-AAAA-0001`, numéro à l'émission), D-004 (client complété dans l'admin), D-005 (statuts, confirmation auto, annulation admin gratuite), D-010 (« Terminée » à la main par l'admin) tranchées ; tâches ajoutées à la roadmap.
- **Bloqué** : D-002 (TVA 10 % / 20 %) en attente du comptable.
- **Prochaine étape** : demander au comptable pour la TVA ; tester `feature/facturation` dans EasyAdmin.

### 2026-11-10 — Organisation du projet (Pilote)
- **Fait** : sections de conversations par chantier + Pilote épinglé ; travail non commité séparé en 2 branches poussées sur GitHub (`feature/facturation` depuis `main`, `feature/react`) ; création de `docs/`.
- **Bloqué** : copies d'annonces Google Ads introuvables (ancienne session terminal) ; procédure de déploiement Infomaniak pas encore écrite.
- **Prochaine étape** : tester la Facturation dans EasyAdmin puis ouvrir la PR vers `main`.
