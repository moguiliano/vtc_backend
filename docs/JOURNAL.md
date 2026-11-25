# Journal de bord

> 3 lignes par session, la plus récente en haut. Écrit à chaque « clôture ».
> Format : **Fait** / **Bloqué** / **Prochaine étape**

---

### 2026-11-25 — Espaces abonnés, étape 3 « Mes prix » (🧩 Fonctionnalités, `feature/mes-prix`)
- **Fait** : D-085 (prix avant courses), D-086 remplacée par D-087 (grille par défaut = prix SIMPLICI CAB), D-088 (« factoriser le module de facturation pour moi comme pour les chauffeurs », pour l'étape 4), D-089 (majoration de nuit choisie par chaque abonné, dès 23 h), D-090 (« prix base seuil… juste une indication » : prix au seuil calculé, plus saisi, aussi dans votre Grille de prix), D-091 (les prix « comme ZenCAR » suivent les vôtres tant que l'abonné ne les modifie pas). Page `/espace/mes-prix` : véhicules à cocher, 4 prix par véhicule, prix au seuil et exemple calculés en direct, badge « Comme ZenCAR » / « Mes prix » + « revenir aux prix ZenCAR », majoration de nuit oui/non + % ; lecture seule si l'espace l'est ; fermée tant que la société n'est pas validée. Grille créée à la validation. **1 migration** (`Version20261125102953`). Testé en local : A coche et modifie → seule sa berline passe en « mes prix » (prix au seuil 37,50 €), valeurs fausses refusées sans rien enregistrer, A ne peut toucher ni la grille de SIMPLICI CAB ni celle de B ; vous passez l'éco-berline à 22 € → A suit, sa berline non ; « revenir aux prix ZenCAR » OK ; B en attente → page fermée, validé → 4 lignes créées ; prix publics identiques à la prod ; 13 tests PHPUnit OK (2 nouveaux : nuit par abonné, prix au seuil). La page `/chauffeurs-vtc` annonce maintenant « vos véhicules et vos propres prix » dans « Dès aujourd'hui ».
- **Bloqué** : rien. Non testé : un vrai téléphone ; l'ajout d'une nouvelle catégorie de véhicule par le super-admin (prévu : elle arrive chez les abonnés validés, non cochée).
- **Refonte D-092** (« je le trouve trop complexe », « pourquoi pas mettre une page similaire à admin symfony ? ») : l'espace passe dans un 2ᵉ tableau de bord EasyAdmin `EspaceDashboardController` (`/espace`, menu sur le côté, `allowedControllers: []` → aucun écran admin sous `/espace`) ; « Mes prix » en version simple d'abord (une ligne par véhicule avec interrupteur, badge « Prix ZenCAR » / « Mes prix », « à partir de … », bouton « Personnaliser » qui ouvre les 4 chiffres ; une phrase pour la nuit). Inscription et connexion inchangées. Testé : A voit Accueil / Ma société / Mes prix ; `/espace/reservation`, `/espace/abonne`, `/espace/grille-tarif`, `/espace/facture` → 404 ; `/admin` → connexion admin ; en attente → pas de « Mes prix » ; lecture seule → champs grisés ; admin inchangé (aucun lien vers `/espace`) ; 13 tests PHPUnit OK.
- **D-093** (« pourquoi n'as-tu pas mis exactement le même template prix que le mien ? », choix A) : « Mes prix » = **le même écran** que « Grille de prix » de l'admin (`MesPrixCrudController`, colonnes communes dans `GrilleTarifChampsTrait`), limité à ses lignes, sans Ajouter / Supprimer, colonne « Prix ZenCAR » et action « Revenir aux prix ZenCAR » (lien protégé par jeton) ; nuit dans « Ma majoration de nuit » (`/espace/ma-nuit`). Écran refusé dans `/admin` (`deniedControllers`). Testé : A modifie sa berline → « Mes prix » (30 €), SIMPLICI CAB et B inchangés ; lignes n°1 (SIMPLICI CAB) et n°15 (B) → 404 ; Ajouter → 403 ; revenir aux prix ZenCAR OK, sans jeton → rien ; nuit 12,5 % / désactivée enregistrée ; Grille de prix admin inchangée (colonne « Prix au seuil (calculé) ») ; 13 tests PHPUnit OK.
- **Prochaine étape** : PR vers `main` puis mise en prod (sauvegarde + 1 migration ; la grille de « TEST SIMPLICI CAB » sera créée par la migration). Ensuite étape 2 « Mes courses » (D-082, D-084).

### 2026-11-25 — Abonné retenu en prod (PR #20)
- **Fait** : PR #20 fusionnée (`5365bd3`) ; en prod : sauvegarde (`sauvegarde-20261125-0915.sql`), `git pull` (`059f857..5365bd3`), 1 seule migration listée puis jouée (`Version20261125015415`), cache vidé. Vérifié en ligne par Claude : accueil, page chauffeurs, réservation, calcul de trajet, connexion abonné, redirection de `/admin`. Expliqué en séance : sauvegarde (photo avant travaux), `pull` / `push`, ordre sauvegarde → pull → migration → cache.
- **Test en prod par l'utilisateur** : inscription d'un abonné « TEST SIMPLICI CAB » (dispatcheur) via `/chauffeurs-vtc`, société remplie, validation dans Admin > Abonnés : « validé ! » ; il apparaît bien dans la liste « Abonné ZenCAR » de Partager la course. À passer en « Lecture seule » après les tests (pas de suppression d'abonné dans l'admin).
- **Bloqué** : rien. `public/.htaccess` non suivi sur le serveur : normal (propre à Infomaniak).
- **Prochaine étape** : partager `https://dzencar.fr/chauffeurs-vtc` et valider les premiers abonnés ; étape 2 des espaces abonnés (Mes courses : saisie avec carte + courses reçues de ZenCAR, D-082, D-084).

### 2026-11-25 — Abonné retenu dans « Partager la course » (🧩 Fonctionnalités, `feature/abonne-retenu`)
- **Fait** : D-083 (plan accepté : « Ok »). Page Partager, onglet « 2. Envoi au chauffeur retenu » : liste « Abonné ZenCAR » (abonnés validés seulement : essai ou actif), le choix remplit « Nom — téléphone » (modifiable), « — Pas un abonné — » garde le texte libre ; ligne « Abonné retenu » sur la fiche réservation ; filtre « Abonné retenu » sur la liste des réservations. **1 migration** (`Version20261125015415`, colonne vide `reservation.abonne_retenu_id`). Testé en local : A (en essai) proposé, B (en attente) absent et refusé même en forçant l'envoi, texte libre d'un non-abonné OK, filtre → la bonne course, zones de message intactes (un conflit de nom `data-texte` avec le script de la page a été corrigé), 11 tests PHPUnit OK.
- **Bloqué** : rien. Non testé : la page avec un compte régulateur (mêmes droits que l'admin sur cette page).
- **Prochaine étape** : PR vers `main` puis mise en prod (sauvegarde + 1 migration) ; ensuite étape 2 des espaces abonnés (les courses de l'abonné, D-082).

### 2026-11-25 — Page « Espace chauffeurs pro » en prod (PR #19)
- **Fait** : PR #19 fusionnée (`059f857`) ; en prod : `git pull` puis cache vidé (pas de migration). Vérifié en ligne par Claude : `https://dzencar.fr/chauffeurs-vtc` s'affiche (noindex, absente du sitemap), lien « Espace chauffeurs pro » dans le pied de page, `/espace/inscription` et `/espace/connexion` OK, `/espace` et `/admin` toujours fermés.
- **Bloqué** : rien. Non testé : un vrai téléphone.
- **Prochaine étape** : partager le lien dans les groupes de chauffeurs et valider les premiers inscrits (admin > Abonnés) ; ensuite D-079 A (liste des abonnés dans « Chauffeur retenu »), puis l'étape 2 (courses de l'abonné).

### 2026-11-25 — Page « Espace chauffeurs pro » (🧩 Fonctionnalités, `feature/page-chauffeurs`)
- **Fait** : D-079 (« A pour l'instant, le but sera de passer à C plus tard » : liste des abonnés dans « Chauffeur retenu » maintenant, bourse aux courses plus tard), D-080 (lien d'inscription d'abord), D-081 (page de présentation + lien en pied de page), D-082 (formulaire avec carte dans l'espace à l'étape 2, pas de formulaire public). Page `/chauffeurs-vtc` (non référencée) : l'outil, les 2 abonnements (10 € / 30 € TTC), le mois d'essai, 3 étapes, boutons « Créer mon espace » et « Me connecter » ; lien « Espace chauffeurs pro » dans le pied de page (colonne Informations). Porte `/espace` resserrée à `/espace` et `/espace/…` exactement. Testé en local : page en largeur téléphone et ordinateur sans débordement, lien présent, `/espace` et `/admin` toujours fermés, 11 tests PHPUnit OK. Pas de migration.
- **Bloqué** : rien. Non testé : un vrai téléphone.
- **Prochaine étape** : PR vers `main` (avec ton accord) puis mise en prod (pull + cache, pas de migration) ; partager `https://dzencar.fr/chauffeurs-vtc` dans les groupes de chauffeurs ; ensuite D-079 A (liste des abonnés dans « Chauffeur retenu »).

### 2026-11-25 — Espaces abonnés étape 1 en prod (PR #18)
- **Fait** : PR #18 fusionnée (`82558fd`) ; en prod : sauvegarde (887 Ko), `git pull` (`acab3bc..82558fd`, apporte aussi le correctif D-071 de la PR #16, qui n'avait pas encore été tiré sur le serveur), 1 seule migration listée puis jouée (`Version20261125003725`), cache vidé. Vérifié en ligne par Claude : pages publiques et prix « à partir de », calcul de trajet Gare Saint-Charles → aéroport (4 prix renvoyés), `/espace/inscription` et `/espace/connexion` affichées (noindex), `/espace` et `/espace/ma-societe` renvoient vers la connexion abonné, `/admin` et `/admin/abonne` vers `/login`.
- **Bloqué** : rien. Aucun abonné réel créé en prod ; la page d'inscription n'est liée nulle part (D-078). Non testé : l'affichage sur téléphone.
- **Prochaine étape** : décider où mettre le lien « Créer mon espace » ; étape 2 des espaces abonnés : les courses de l'abonné (D-012).

### 2026-11-25 — Espaces abonnés, étape 1 (🧩 Fonctionnalités, `feature/espaces-abonnes`)
- **Fait** : décisions D-072 (« je veux d'abord l'espace abonné » : abonnés avant collaborateurs) à D-078 (découpage en 6 étapes, inscription publique, piège à robots + limite par heure, espace séparé `/espace`, essai à partir de la validation, plan accepté). Étape 1 codée : inscription `/espace/inscription`, connexion `/espace/connexion`, « Ma société » (vide au départ, figée après validation), page « en attente », tableau de bord avec fin d'essai ; menu **Abonnés** du super-admin (fiche, bouton « Valider » qui lance l'essai, changement d'état à la main). Cloisonnement : comptes abonnés séparés des admins (porte `/espace`), filtre Doctrine « entreprise » activé à chaque page (SIMPLICI CAB sur le site et `/admin`, l'abonné dans `/espace`). **1 migration** (`Version20261125003725` : 3 colonnes sur `entreprise`, SIMPLICI CAB mise « actif », table `compte_abonne`). Testé en local sur une copie de la base (`zencar_abonnes`) avec 2 abonnés de test et un super-admin de test : `/admin` (8 adresses) renvoie l'abonné vers la connexion ; ses identifiants refusés sur `/login` ; chaque entreprise ne voit que sa société, ses réservations, factures, grilles et forfaits (réservation n°167 introuvable pour un abonné) ; robot piégé sans compte créé ; 4ᵉ inscription de l'heure refusée ; fiche/modification de SIMPLICI CAB via « Abonnés » → 404 ; pages publiques, bons de commande et factures PDF de SIMPLICI CAB OK ; 11 tests PHPUnit OK.
- **Bloqué** : rien. Non testé : le calcul de trajet public (appel HERE Maps) avec le filtre actif ; l'affichage sur téléphone ; la page d'inscription n'est liée nulle part (D-078).
- **Prochaine étape** : PR vers `main` (avec ton accord), puis mise en prod (sauvegarde + 1 migration). Ensuite étape 2 : les courses de l'abonné (à partir de la réservation par téléphone, D-012).

### 2026-11-23 — Régulateur en prod (PR #15 et #16)
- **Fait** : PR #15 fusionnée (`acab3bc`) ; en prod : sauvegarde (881 Ko), `git pull --ff-only`, 1 seule migration listée puis jouée (`Version20261123214444`), cache vidé. Compte n°3 (hotmail) qui avait d'anciens droits (`ROLE_ADMIN`, `ROLE_RESERVATIONS_*`) passé en **Régulateur pur** depuis l'admin. Correctif D-071 (« pas besoin qu'elle soit cachée ») : l'annonce groupe affiche à nouveau l'adresse d'arrivée ; PR #16 fusionnée (`9c8cf19`) et mise en prod (pull + cache, sans migration). Vérifié par toi en ligne : arrivée visible dans l'annonce, menu régulateur, Telegram, `/admin/admin` refusé en régulateur. Vérifié par Claude : pages publiques et redirection de `/admin` vers la connexion.
- **Bloqué** : rien. Toujours non testé : le bouton Telegram sur un vrai téléphone ; la trace « site » lors d'une modification par le site public.
- **Prochaine étape** : laisser le régulateur travailler quelques semaines, puis revoir le tableau de bord régulateur (D-070, en attente). Étape suivante de la plateforme : Collaborateurs (D-018, D-019).

### 2026-11-23 — Régulateur (🧩 Fonctionnalités, `feature/regulateur`)
- **Fait** : décisions D-064 à D-069. Trou de sécurité fermé : un régulateur pouvait ouvrir « Administrateurs » (et se créer un compte Super Admin), la grille de prix, les véhicules, les forfaits, « Avis Google » et créer des factures en tapant l'adresse. Désormais réservés au super-admin (`#[IsGranted('ROLE_SUPER_ADMIN')]`). Droits simplifiés à 2 (Super Admin / Régulateur). Factures en lecture seule pour le régulateur. Page Partager : « 1. Annonce groupe » (prix chauffeur seul, client et prix client toujours retirés), « 2. Envoi au chauffeur retenu » avec champ « Chauffeur retenu », bouton Telegram. Trace « Modifiée par / le » (heure de Marseille). Corrigé en passant : fiche détail facture en erreur 500 (taux de TVA). **1 migration** (`Version20261123214444`, 3 colonnes vides sur `reservation`). Testé en local sur une copie de la base (`zencar_regulateur`) avec 2 comptes de test : 16 adresses interdites → 403 pour le régulateur, 200 pour le super-admin.
- **Bloqué** : rien. Non testé : le bouton Telegram sur un vrai téléphone ; la trace « site » quand un client modifie sa réservation sur le site public.
- **Prochaine étape** : PR vers `main` (avec ton accord), puis mise en prod : sauvegarde, vérifier les droits des comptes admin existants, migration. En prod, corriger le modèle « Annonce groupe » : remplacer `{arrivee}` par `{zone_arrivee}` (sinon la ligne Arrivée disparaît).

### 2026-11-23 — Fondation « Entreprise » en prod (PR #12)
- **Fait** : PR #12 mise à jour avec `main` (PR #13, sans conflit) puis fusionnée (`e87be67`) ; en prod : sauvegarde (877 Ko, `sauvegarde-20261123-2041.sql`), `git pull --ff-only` (`f70355a..e87be67`), exactement 7 migrations listées puis jouées sans erreur, cache vidé. Base : 1 entreprise, 5 grilles de prix (une 5ᵉ catégorie inactive en prod), 331 réservations et 3 factures rattachées à SIMPLICI CAB. Vérifié en ligne : pages publiques, prix « À partir de » (20/30/35/63 €), calcul de trajet ; admin vérifié par toi.
- **Bloqué** : rien.
- **Prochaine étape** : Régulateur (D-021). Les prix se modifient désormais dans l'admin > « Grille de prix ».

### 2026-11-23 — Fondation « Entreprise » (🗄️ Données, PR #12)
- **Fait** : plan refait sur le `main` du jour (forfaits, messages et appels étiquetés aussi, ton choix « Oui, tout ») ; entité `Entreprise` (SIMPLICI CAB = n°1) ; `entreprise_id` sur réservations, factures (numéro `FAC-AAAA-0001` par entreprise), société (une fiche par entreprise), forfaits, catégories de messages, appels ; nouvelle **Grille de prix** (prix par entreprise, catégories communes, D-016) ; 4 briques (`EntrepriseCourante`, `ReservationCreator`, `PdfGenerator`, `Tarification`). 7 migrations testées sur une copie de la base (`zencar_fondation`) : aller-retour sans perte (12 tables identiques), 60 prix identiques, parcours public / téléphone / quick-book / bon PDF / facture + PDF / Partager vérifiés, 11 tests PHPUnit. PR #12 ouverte.
- **Bloqué** : rien. Constatés en passant (déjà là avant) : fiche détail facture en erreur 500 en mode dev (taux de TVA en nombres à virgule) ; route `/reservation/{id}/confirm` déclarée deux fois.
- **Prochaine étape** : fusionner la PR #12 puis mise en prod (sauvegarde, `git pull`, **7 migrations** juste après) ; dans l'admin, les prix se modifient désormais dans « Grille de prix ». Ensuite : Régulateur (D-021).

### 2026-11-19 — Partager la course et réservation téléphone en prod (🧩 Fonctionnalités)
- **Fait** : PR #8 (date passée, réservation par téléphone) puis PR #7 (Partager la course + messages) fusionnées dans `main` ; en prod avec la PR #9 Marketing (`f70355a`) : sauvegarde de la base, code déjà à jour, migrations `Version20261117100000` (messages + commission) et `Version20261117140000` (journal des appels) déjà jouées le 2026-11-17, cache vidé. Vérifié en ligne par toi : tout fonctionne.
- **Bloqué** : rien.
- **Prochaine étape** : idées « Partager, suite » en attente (lien GPS, trace des envois, chauffeur assigné) ; la session « Fondation Entreprise » doit partir de ce `main` (nouvelles migrations).

### 2026-11-17 — Partager la course + réservation téléphone (🧩 Fonctionnalités)
- **Fait** : décisions D-023 à D-032. **`feature/partage-course`** (commit c047458) : bouton « Partager la course » (`/admin/reservation/{id}/partager`) avec partage complet / partiel (zone approximative, sans le client) / message client ; boutons SMS, WhatsApp, Copier ; commission modifiable en € ou % et enregistrée (`reservation.commission`) ; sous-menu Messages (catégories libres + modèles en base, 5 prêts) ; zone de texte auto + étirable. **`fix/reservation-telephone`** (commit 48b8eb4) : date passée acceptée, date du jour à l'heure de Marseille. Les deux testés en local par toi : ça marche.
- **Bloqué** : rien. Migration `Version20261117100000` à lancer en prod avec Partager. La session « Fondation Entreprise » touchera aussi `Reservation` : surveiller l'ordre des migrations à la fusion.
- **Prochaine étape** : pousser les 2 branches et ouvrir 2 PR vers `main` (avec ton accord), puis mise en prod (`deploiement.md`, migration comprise).

### 2026-11-16 — Bons de commande protégés (Pilote)
- **Fait** : jeton secret dans les liens du bon de commande PDF et de la page de succès ; confirmation limitée au navigateur qui a créé la réservation ; testé en local (sans jeton → refusé, faux jeton → refusé, bon jeton → PDF, admin libre).
- **Bloqué** : rien.
- **Prochaine étape** : PR vers `main` puis mise en prod (pas de migration) ; plus tard, vérifier l'OTP côté serveur.

### 2026-11-15 — Facturation en prod (PR #5, Pilote)
- **Fait** : Facturation mise en prod **avant** la fondation (D-020 modifiée) : `feature/facturation` mise à jour avec `main` (2 conflits d'imports résolus), testée en local sur un dossier séparé, PR #5 fusionnée ; en prod : sauvegarde, `git pull`, 3 migrations (`created_at`, tables `facture` et `societe`), cache vidé, sans erreur. Accueil, calcul de trajet et redirection `/admin` vérifiés en ligne.
- **Bloqué** : D-001/D-003 pas encore appliqués (ne pas modifier une facture envoyée) ; fuite possible des bons de commande PDF (voir ROADMAP Infra, prioritaire) ; session Fondation bloquée par le manque de place disque.
- **Prochaine étape** : tester « Ma société » et une facture dans l'admin de prod ; corriger le lien public des bons de commande ; libérer de la place puis relancer la Fondation.

### 2026-11-15 — Corrections d'affichage en prod (PR #3, 🧩 Fonctionnalités)
- **Fait** : PR #3 mise en prod (SSH, procédure `deploiement.md`) et vérifiée en ligne sur dzencar.fr : récap (site + admin téléphone) et fiche EasyAdmin n'affichent que les champs remplis ; plus de zoom auto iOS (champs à 16px sur écran tactile) ; plus de débordement horizontal de l'accueil sur mobile ; marge autour du trajet sur la carte (`viewPort.setPadding`).
- **Bloqué** : test sur un vrai iPhone (zoom au toucher du champ adresse) à faire par toi ; réservations de test n° 172 et 173 dans la base locale à supprimer.
- **Prochaine étape** : session 🗄️ « Fondation Entreprise » (plan d'abord).

### 2026-11-14 — Séance de décisions « Plateforme multi-entreprises » (Pilote)
- **Fait** : D-011 à D-021 tranchées : outil de gestion (SaaS), pas de réservation en ligne pour les clients des abonnés au départ, 4 rôles (super-admin, régulateur interne + WhatsApp, abonné à compte unique, collaborateur), paiement manuel + lecture seule si impayé, validation société sans pièces, catégories communes + prix propres, factures au nom de l'abonné, collaborateurs SIMPLICI CAB d'abord (forfaits + remise, paiement au choix), droits du régulateur. **Ordre (D-020) : fondation « Entreprise » d'abord**, puis Facturation, Régulateur, Collaborateurs, Espaces abonnés. En parallèle, corrections d'affichage fusionnées (PR #3).
- **Bloqué** : D-022 (prix de l'abonnement) reporté ; D-002 (TVA) toujours chez le comptable. Mise en prod de la PR #3 à faire en SSH.
- **Prochaine étape** : mise en prod PR #3 ; puis session 🗄️ « Fondation Entreprise » : plan d'abord, code ensuite.

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
