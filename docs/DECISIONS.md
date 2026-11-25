# Décisions business ZenCar

> ⚖️ Les choix business : ce qui reste à trancher et ce qui est décidé, avec la raison.
> Séance de décisions : taper `/decisions` dans une conversation Claude.
> Les décisions techniques (code, BDD) ne vont pas ici : elles vont dans `CLAUDE.md`.

<!--
FORMAT ATTENDU — à respecter pour chaque entrée

## ❓ À trancher
### D-XXX — Titre court de la question
- **Chantier** : 🧩 Fonctionnalités | 🗄️ Données | ⚛️ React | 📣 Marketing | 🚀 Infra
- **Contexte** : pourquoi la question se pose (1 à 3 lignes, faits constatés)
- **Options** : A) … / B) … / C) …
- **Avis de Claude** : option recommandée + raison en 1 ligne

## ✅ Tranché  (le plus récent en haut)
### D-XXX — Titre court — AAAA-MM-JJ
- **Décision** : ce qui est décidé, en une phrase
- **Raison** : pourquoi
- **Impact** : fichiers docs/ mis à jour, tâche ajoutée dans ROADMAP.md si du code est nécessaire

Règles : numéro D-XXX unique et jamais réutilisé ; une question tranchée est DÉPLACÉE
(pas recopiée) de « À trancher » vers « Tranché ».
-->

## ❓ À trancher

### D-002 — Taux de TVA des factures
- **Chantier** : 🧩 Fonctionnalités
- **Contexte** : le formulaire propose 10 % (transport de personnes, par défaut) ou 20 %. Il faut savoir dans quels cas le 20 % s'applique : mise à disposition, frais annexes ?
- **Options** : A) Toujours 10 %, retirer le choix 20 % / B) Garder les deux, 10 % par défaut / C) À valider avec ton comptable
- **Avis de Claude** : C puis A ou B ; c'est une question fiscale, ton comptable tranche.
- **⏸ En attente (2026-11-12)** : demander au comptable dans quels cas facturer à 20 %. D'ici là, rien ne change : 10 % par défaut, 20 % possible.

### D-006 — Commission et majoration de nuit : qui peut les changer ?
- **Chantier** : 🗄️ Données
- **Contexte** : commission 20 %, majoration de nuit 20 % entre 23 h et 7 h. Ces valeurs sont aujourd'hui dans `config/services.yaml` : les changer demande une mise en prod.
- **Options** : A) Rester dans le fichier de config (changement rare) / B) Les rendre modifiables depuis EasyAdmin
- **Avis de Claude** : A si tu ne les changes qu'une fois par an, B si tu fais des promotions ou des ajustements saisonniers.

### D-007 — Google Ads : exclure le mot-clé `[taxi]` ?
- **Chantier** : 📣 Marketing
- **Contexte** : Google le signale pour « CTR faible ». Le mot-clé est très générique et peut déclencher des recherches hors Marseille, même avec le ciblage géographique.
- **Options** : A) L'exclure / B) Le garder en surveillant le coût par conversion pendant 2 semaines / C) Le remplacer par `[taxi près de moi]`
- **Avis de Claude** : B puis décision chiffrée ; ne rien couper sans données de conversion.

### D-008 — Copies d'annonces Google Ads perdues
- **Chantier** : 📣 Marketing
- **Contexte** : les copies rédigées en avril 2026 sont introuvables (voir `docs/marketing/google-ads.md`).
- **Options** : A) Tu les retrouves (interface Google Ads, notes) et tu les colles / B) On les réécrit en session Marketing
- **Avis de Claude** : regarder d'abord dans Google Ads (les annonces déjà saisies y sont), sinon B.

<!-- ─── Bloc « Plateforme multi-entreprises » (ajouté le 2026-11-13) ───
Projet : chauffeurs / régulateurs abonnés avec leur propre espace (tarifs, société validée par l'admin ZenCar,
bons de commande, factures) + collaborateurs (établissements partenaires avec prix convenus).
Ordre conseillé : D-011 (modèle) d'abord, car il conditionne toutes les autres. -->


## ✅ Tranché

### D-093 — « Mes prix » : le même écran que la Grille de prix de l'admin — 2026-11-25
- **Décision** (question de l'utilisateur : « pourquoi n'as-tu pas mis exactement le même template prix que le mien ? », puis choix A) : « Mes prix » reprend **le même écran** que « Grille de prix » de l'admin (liste, puis « Modifier » par véhicule, mêmes colonnes), avec seulement : ses propres lignes, ni « Ajouter » ni « Supprimer », une colonne « Prix ZenCAR / Mes prix » et une action « Revenir aux prix ZenCAR ». La majoration de nuit passe dans une petite page à part, « Ma majoration de nuit ».
- **Raison** : un seul écran à entretenir (esprit de D-088) ; le super-admin voit la même chose que le chauffeur et peut l'aider au téléphone.
- **Impact** : remplace la page « simple d'abord » de D-092 pour « Mes prix » (le reste de D-092 ne change pas). Champs communs aux deux écrans (un seul endroit à modifier).

### D-092 — L'espace chauffeur en style « admin EasyAdmin » — 2026-11-25
- **Décision** (mots de l'utilisateur : « je le trouve trop complexe », « pourquoi pas mettre une page similaire à admin symfony ? », puis choix A) : l'espace abonné passe dans un **2ᵉ tableau de bord EasyAdmin** sous `/espace` (menu sur le côté : Accueil, Ma société, Mes prix, puis Mes courses et Mes factures). « Mes prix » devient **« simple d'abord »** : une ligne par véhicule (case, prix ZenCAR « à partir de », bouton « Personnaliser » qui ouvre les 4 chiffres) et une phrase pour la nuit. Inscription et connexion restent des pages simples.
- **Raison** : page actuelle trop chargée ; un cadre connu, plus clair ; listes, recherche et filtres offerts pour les étapes 2 et 4 ; factorisation avec l'admin (D-088).
- **Impact** : nouveau `EspaceDashboardController` (`#[AdminDashboard]` sous `/espace`, `allowedControllers` = seulement les écrans de l'espace, donc aucun écran admin accessible) ; accueil, Ma société et Mes prix déplacés dedans ; à faire avant la mise en prod de « Mes prix ».

### D-091 — Quand SIMPLICI CAB change ses prix, ceux des abonnés suivent-ils ? — 2026-11-25
- **Décision** (mots de l'utilisateur : « c'est plutôt ma facturation qui change et celle des abonnés qui suit », puis « Oui, ils suivent ») : chaque véhicule d'un abonné est **« comme ZenCAR »** au départ et **suit les changements de prix de SIMPLICI CAB**. Dès que l'abonné modifie lui-même un chiffre de ce véhicule, il passe en **« mes prix »** et ne suit plus ; un bouton **« Revenir aux prix ZenCAR »** le raccroche.
- **Raison** : l'abonné n'a rien à faire pour rester aligné sur ZenCAR, tout en gardant la liberté de fixer ses prix (D-016).
- **Impact** : **corrige l'impact de D-087** (la phrase « un changement de prix de SIMPLICI CAB ne modifie pas sa grille » avait été ajoutée par Claude, pas par l'utilisateur). Seuls les 4 chiffres de prix suivent ; les véhicules cochés et la majoration de nuit (D-089) restent propres à l'abonné.

### D-090 — « Prix base > seuil » : réglage ou simple indication ? — 2026-11-25
- **Décision** (choix de l'utilisateur : A ; ses mots : « prix base seuil ne devrait pas être touché, c'est juste une indication ») : le **prix de base au-delà du seuil est calculé automatiquement** = prise en charge + seuil × prix/km jusqu'au seuil. Il est **affiché pour information**, plus saisi à la main. Il reste 4 chiffres à régler par véhicule.
- **Raison** : c'est la conséquence des autres chiffres (vérifié : 45 = 20 + 10 × 2,50 ; 55 ; 35 ; 63 pour les 4 véhicules de SIMPLICI CAB) ; saisi à la main, une erreur ferait « sauter » le prix au passage du seuil.
- **Impact** : « Grille de prix » de l'admin et « Mes prix » des abonnés ; `GrilleTarif` recalcule la valeur à chaque modification. Prix actuels inchangés (ils respectent déjà la règle).

### D-089 — Majoration de nuit des abonnés — 2026-11-25
- **Décision** (choix de l'utilisateur : « Oui, chaque abonné ») : **chaque abonné décide** dans « Mes prix » s'il applique une majoration de nuit (case à cocher) et son pourcentage. Les heures de nuit sont les mêmes pour tous : **à partir de 23 h** (jusqu'à 7 h, comme SIMPLICI CAB).
- **Raison** : cohérent avec D-016 et D-087 : chaque chauffeur maîtrise ses prix, nuit comprise.
- **Impact** : réglage « majoration de nuit : oui / non + % » par entreprise abonnée (au départ : comme SIMPLICI CAB, 20 %, D-087) ; SIMPLICI CAB garde sa règle actuelle (`services.yaml`, D-006 inchangée).

### D-088 — Un seul module de facturation pour SIMPLICI CAB et les abonnés — 2026-11-25
- **Décision** (mots de l'utilisateur) : « on va factoriser le module de facturation pour moi comme pour les chauffeurs ».
- **Raison** : ne construire et n'entretenir qu'une seule facturation ; chaque entreprise garde ses mentions légales et sa numérotation (D-017).
- **Impact** : à l'étape 4 (« ses bons et factures »), les écrans de factures et le PDF de l'espace abonné réutilisent les mêmes briques que l'admin (`Facture`, numérotation par entreprise, `PdfGenerator`), au lieu d'une copie à part. Toute amélioration (ex. brouillon → émise, D-001, D-003) profitera aux deux.

### D-087 — Grille par défaut d'un abonné : les prix de SIMPLICI CAB — 2026-11-25
- **Décision** (mots de l'utilisateur) : « les prix de leurs grilles par défaut seront les mêmes prix que moi ».
- **Raison** : changement d'avis par rapport à D-086 ; l'abonné part d'une grille complète et l'ajuste.
- **Impact** : **remplace D-086** (grille vide). À la création de la grille d'un abonné, ses prix sont **copiés** de ceux de SIMPLICI CAB. ~~Ensuite sa grille lui appartient (un changement de prix de SIMPLICI CAB ne la modifie pas).~~ → corrigé par **D-091** : ils suivent tant que l'abonné ne les modifie pas. Aucun véhicule coché au départ (plan de l'étape 3).

### D-086 — Avec quels prix démarre la grille d'un nouvel abonné ? — 2026-11-25 — ⚠️ remplacée par D-087
- **Décision** (choix de l'utilisateur : A) : **une grille vide** : tous les prix à 0, aucun véhicule coché. L'abonné remplit lui-même ses prix avant de pouvoir saisir une course.
- **Raison** : choix de l'utilisateur ; chaque abonné fixe ses prix lui-même, sans partir de ceux de SIMPLICI CAB.
- **Impact** : l'écran « Mes prix » doit être très clair (exemple chiffré, aide sur le seuil et les 2 formules) ; tant qu'aucun véhicule n'est coché avec des prix, « Mes courses » (étape 2) affichera un message qui renvoie vers « Mes prix ».

### D-085 — Étape 2 ou étape 3 d'abord ? — 2026-11-25
- **Décision** (choix de l'utilisateur : A) : **les prix d'abord**. L'étape 3 (l'abonné coche ses véhicules et règle ses prix dans son espace) passe avant l'étape 2 (« Mes courses »).
- **Raison** : le formulaire « Mes courses » calcule le prix depuis la grille de l'abonné ; sans grille, il ne saurait ni quels véhicules proposer ni quel prix afficher. Le formulaire n'est ainsi construit qu'une fois.
- **Impact** : ordre de `ROADMAP.md` (étape 4 « Espaces abonnés ») : 1 ✔, puis 3 (ses prix), puis 2 (ses courses).

### D-084 — L'abonné retenu reçoit-il la course dans son espace ? — 2026-11-25
- **Décision** (choix de l'utilisateur : B) : **oui, avec l'étape 2**. La page « Mes courses » de l'abonné aura 2 parties : les courses qu'il saisit lui-même et les **courses reçues de ZenCAR** (celles où il est « abonné retenu », D-083). Pour ces dernières : date, départ, arrivée, véhicule et **prix chauffeur seulement** (ni prix client, ni commission), en **copie** : il ne touche jamais la réservation de SIMPLICI CAB. Le partage WhatsApp / Telegram continue en attendant.
- **Raison** : ne construire qu'une fois (pendant l'étape 2) ; premier pas vers la bourse aux courses (D-079 C).
- **Impact** : précise D-079 (au-delà de A) ; étape 2 de `ROADMAP.md` complétée. Point à garder pour l'étape 2 : le tri automatique par entreprise cache les réservations de SIMPLICI CAB à l'abonné, la lecture des « courses reçues » devra passer par une copie ou une lecture dédiée, limitée aux champs autorisés.

### D-083 — Liste des abonnés dans « Chauffeur retenu » : le plan — 2026-11-25
- **Décision** (mot de l'utilisateur : « Ok ») : sur la page Partager (onglet « 2. Envoi au chauffeur retenu »), une liste **« Abonné ZenCAR »** avec les seuls abonnés **validés** (essai ou actif) ; le choix remplit le texte « Nom — téléphone » (modifiable) ; « — Pas un abonné — » garde le texte libre. Filtre **« Abonné retenu »** sur la liste des réservations. Rien de nouveau dans l'espace de l'abonné.
- **Raison** : mettre en œuvre D-079 A et préparer la bourse aux courses (savoir qui roule beaucoup et bien).
- **Impact** : lien facultatif `reservation.abonne_retenu_id` (1 migration, colonne vide) ; page Partager ; `ReservationCrudController`.

### D-082 — Les chauffeurs auront-ils un formulaire de réservation avec la carte ? — 2026-11-25
- **Décision** (mots de l'utilisateur : « ok pour le 1, non pour le 2 », à propos des 2 formulaires) : **oui** au formulaire de saisie **dans l'espace** de l'abonné (étape 2 : adresses suggérées, carte et trajet, prix calculé depuis sa grille, sur le modèle de la réservation par téléphone) ; **non** pour l'instant à un formulaire **public** pour les clients de l'abonné (confirme D-012). Plan de la page `/chauffeurs-vtc` accepté tel quel (non référencée, sans parler des futures courses confiées).
- **Raison** : le chauffeur saisit vite les courses de ses clients ; une page publique par abonné reste à protéger et viendra plus tard.
- **Impact** : étape 2 de `ROADMAP.md` précisée ; la page chauffeurs annonce « saisir vos courses avec calcul automatique du trajet et du prix ».

### D-081 — Où mettre le lien « Créer mon espace » ? — 2026-11-25
- **Décision** (choix de l'utilisateur : B) : une **page de présentation pour les chauffeurs** (l'outil, les 2 prix, le mois d'essai, bouton « Créer mon espace ») + un **lien discret dans le pied de page**. Pas de bouton dans le menu du haut.
- **Raison** : le chauffeur comprend l'offre avant de s'inscrire ; les clients (et les visiteurs Google Ads) ne voient presque rien de changé ; l'adresse de la page peut être partagée dans les groupes WhatsApp de chauffeurs.
- **Impact** : nouvelle page publique + lien dans `partials/_footer.html.twig`.

### D-080 — Après la mise en prod de l'étape 1 : par quoi continuer ? — 2026-11-25
- **Décision** (choix de l'utilisateur : C) : d'abord **placer le lien « Créer mon espace »** sur dzencar.fr, pour recruter les premiers abonnés ; ensuite la liste des abonnés dans « Chauffeur retenu » (D-079), puis l'étape 2 (les courses de l'abonné).
- **Raison** : sans abonnés, les autres étapes ne servent à personne ; on accroche l'enseigne avant de meubler la boutique.
- **Impact** : remplace le « pas encore liée » de D-078 ; emplacement du lien à trancher.

### D-079 — Les abonnés, futurs chauffeurs de ZenCAR ? — 2026-11-25
- **Décision** (mots de l'utilisateur) : « A pour l'instant, le but sera de passer à C plus tard ». Les abonnés d'aujourd'hui seront les chauffeurs de demain sur ZenCAR.
  - **Maintenant (A)** : les courses de SIMPLICI CAB restent partagées par WhatsApp / Telegram ; le champ « Chauffeur retenu » propose la **liste des abonnés validés** (en plus du texte libre, pour les chauffeurs qui ne sont pas abonnés).
  - **Plus tard (C)** : « bourse aux courses » : chaque course de dzencar.fr proposée à tous les abonnés validés, le premier qui accepte la prend.
- **Raison** : savoir dès maintenant quel abonné fait quelles courses, sans rien construire de lourd ; la bourse viendra quand il y aura assez d'abonnés de confiance.
- **Impact** : complète D-011 (ZenCAR pourra un jour distribuer des courses : D-011 sera à revoir au moment de C). A : lien optionnel entre une réservation et l'entreprise abonnée retenue (migration), page Partager et fiche réservation. C : à préparer (sous-traitance, assurance, qui facture le client, cloisonnement : l'abonné ne reçoit qu'une copie de la course, jamais la réservation de SIMPLICI CAB). `ROADMAP.md` mis à jour.

### D-078 — Plan de l'étape 1 et deux choix par défaut — 2026-11-25
- **Décision** (mot de l'utilisateur : « Ok ») : plan de l'étape 1 accepté, avec ses deux choix par défaut : **après validation, l'abonné ne modifie plus « Ma société » lui-même** (il demande au super-admin) ; **la page d'inscription n'est pas encore liée depuis dzencar.fr** (emplacement du lien à décider quand tout marchera).
- **Raison** : éviter qu'un SIRET change après le contrôle du super-admin ; ne rien montrer au public avant que l'espace soit testé.
- **Impact** : cloisonnement en 3 verrous (connexion séparée `/espace`, tri automatique des données par entreprise, écrans propres à l'espace) ; menu « Abonnés » au super-admin ; migration sur `entreprise` + table `compte_abonne`.

### D-077 — Quand démarre le mois d'essai ? — 2026-11-25
- **Décision** (choix de l'utilisateur : A) : **le jour où le super-admin valide la société** de l'abonné.
- **Raison** : l'abonné profite de 30 vrais jours d'utilisation, sans perdre de jours à attendre la validation.
- **Impact** : la validation enregistre la date de début d'essai (fin = début + 1 mois) et fait passer l'abonné de « en attente » à « essai ».

### D-076 — Où l'abonné travaille-t-il ? — 2026-11-25
- **Décision** (choix de l'utilisateur : B) : dans un **espace séparé** (`/espace`) avec son propre menu, distinct de l'admin `/admin`.
- **Raison** : on n'y met que les écrans prévus pour les abonnés, chacun filtré sur ses données dès le départ ; aucun risque d'oublier de fermer un écran de l'admin SIMPLICI CAB. L'admin et le régulateur ne changent pas.
- **Impact** : nouveau tableau de bord EasyAdmin pour les abonnés ; `/admin` reste fermé aux abonnés.

### D-075 — Inscription : comment écarter les robots ? — 2026-11-25
- **Décision** (choix de l'utilisateur : A) : **pas de code SMS ni d'e-mail** à l'inscription. Un champ piège invisible (que seuls les robots remplissent) et une limite de quelques inscriptions par heure ; la validation de la société par le super-admin (D-015) sert de filtre final.
- **Raison** : un faux compte ne voit que « Ma société » et n'est jamais validé ; le crédit Twilio est épuisé et l'envoi d'e-mails n'existe pas encore. Le code SMS pourra s'ajouter plus tard.
- **Impact** : formulaire d'inscription avec champ piège et limite de fréquence ; rien à brancher sur Twilio ni sur un Mailer.

### D-074 — Comment l'abonné obtient-il son compte ? — 2026-11-24
- **Décision** (choix de l'utilisateur : B) : **inscription publique**. Une page « Créer mon espace » sur dzencar.fr : la personne crée elle-même son compte, puis le super-admin valide sa société (D-015).
- **Raison** : choix de l'utilisateur ; l'abonné n'a pas besoin d'attendre le super-admin pour commencer à remplir ses informations.
- **Impact** : page publique d'inscription à protéger contre les robots et les abus ; un compte inscrit n'a accès qu'à « Ma société » tant qu'il n'est pas validé (D-015).

### D-073 — Espaces abonnés : par quelle étape commencer ? — 2026-11-24
- **Décision** (choix de l'utilisateur : A) : le chantier est découpé en 6 petites étapes livrables ; on commence par l'**étape 1 « le compte et la porte d'entrée »** : création de l'abonné (type + état), connexion, formulaire « Ma société » seul accessible avant validation, validation par le super-admin qui lance l'essai, cloisonnement des données.
- **Raison** : toutes les autres étapes en dépendent (« la serrure avant les meubles »).
- **Impact** : `ROADMAP.md` (étape 4 découpée). Étapes suivantes : 2. ses courses, 3. ses prix, 4. ses bons et factures, 5. Partager la course pour le dispatcheur, 6. fin d'essai / actif / lecture seule.

### D-072 — Espaces abonnés avant les Collaborateurs ? — 2026-11-24
- **Décision** (mots de l'utilisateur) : « je veux d'abord l'espace abonné ». L'étape 4 **Espaces abonnés** passe **avant** l'étape 3 **Collaborateurs**.
- **Raison** : choix de l'utilisateur ; la Fondation « Entreprise » et le Régulateur (étapes 0 et 2) sont en prod, les espaces abonnés peuvent s'appuyer dessus.
- **Impact** : modifie l'ordre de D-020. `ROADMAP.md` : Espaces abonnés devient l'étape en cours, Collaborateurs ensuite.

### D-022 — Prix de l'abonnement — 2026-11-23
- **Décision** : **chauffeur indépendant 10 € TTC / mois**, **dispatcheur 30 € TTC / mois** (le partage des courses en plus, D-063). **1 mois d'essai gratuit**, facturation à partir du 2e mois. **Paiement au mois uniquement**, sans engagement.
- **Raison** : prix d'entrée bas pour attirer les premiers abonnés ; le paiement mensuel reste simple à suivre à la main (D-014).
- **Impact** : un abonné a un type, une date de début d'essai et un état (en attente / essai / actif / lecture seule, D-014, D-015). À intégrer à l'étape 4 « Espaces abonnés » de `ROADMAP.md`.

### D-071 — Annonce groupe : l'adresse d'arrivée est-elle cachée ? — 2026-11-23
- **Décision** (mots de l'utilisateur) : « pas besoin qu'elle soit cachée ». L'adresse d'**arrivée** (`{arrivee}`) s'affiche dans l'annonce groupe. Restent toujours retirés : nom, téléphone et adresse exacte de **départ** du client, arrêt, informations complémentaires, prix client et commission.
- **Raison** : D-021 ne demande qu'une zone approximative de **prise en charge** ; le risque de contact direct avec le client vient du départ, pas de la destination.
- **Impact** : précise D-066 ; `{arrivee}` retirée de `PartageCourseService::INTERDITES_GROUPE`. Le modèle « Annonce groupe » de prod n'a pas besoin d'être modifié.

### D-070 — Une page dédiée au régulateur ? — 2026-11-23
- **Décision** (choix de l'utilisateur : B) : **non pour l'instant**. La liste des réservations suffit pour démarrer ; l'idée d'un tableau de bord régulateur (courses du jour et du lendemain, courses sans chauffeur retenu, bouton Partager par ligne) va dans « En attente ».
- **Raison** : mettre en prod ce qui est fait, puis voir comment le régulateur travaille vraiment.
- **Impact** : rien à coder ; `ROADMAP.md` (En attente) mis à jour.

### D-069 — Le régulateur voit-il les « Messages reçus » ? — 2026-11-23
- **Décision** (choix de l'utilisateur : A) : **oui**, il voit les messages du formulaire de contact et peut les marquer comme lus (comme aujourd'hui). Le **Journal des appels** (suivi Google Ads) reste au super-admin.
- **Raison** : un client qui écrit pour réserver, c'est de l'exploitation quotidienne.
- **Impact** : aucun changement sur `ContactCrudController`.

### D-068 — Garder une trace des actions du régulateur ? — 2026-11-23
- **Décision** (choix de l'utilisateur : B) : sur chaque réservation, **« Modifiée par »** et **« Modifiée le »** (dernière modification seulement). Pas de journal complet.
- **Raison** : savoir qui a touché une course en dernier (ex. annulation par erreur), sans nouvel écran. Un journal complet pourra venir plus tard.
- **Impact** : 2 colonnes sur `Reservation`, dans la même migration que D-067.

### D-067 — Noter le chauffeur retenu sur la réservation ? — 2026-11-23
- **Décision** (choix de l'utilisateur : B) : un champ **texte libre « Chauffeur retenu »** sur la réservation (ex. « Karim — 06 12 34 56 78 »), rempli par le régulateur au moment de l'envoi privé.
- **Raison** : si un client rappelle, on sait tout de suite qui vient le chercher. Une vraie liste de chauffeurs en base pourra venir plus tard.
- **Impact** : nouvelle colonne sur `Reservation` → **migration** à jouer en prod.

### D-066 — Partage en 2 temps : imposé ou conseillé ? — 2026-11-23
- **Décision** (choix de l'utilisateur : A) : **ordre conseillé**, pas imposé. Les onglets deviennent « 1. Annonce groupe » et « 2. Envoi au chauffeur retenu » ; ajout d'un bouton **Telegram** ; l'annonce groupe n'affiche **que le prix chauffeur** (`{net_chauffeur}`), jamais le prix client, la commission, ni le nom, le téléphone ou l'adresse exacte du client, quel que soit le modèle.
- **Raison** : simple, rapide en cas d'urgence ; reprend la page « Partager la course » qui marche déjà (D-023 à D-027).
- **Impact** : page Partager (onglets, bouton Telegram) ; `PartageCourseService` vide les variables interdites pour l'annonce groupe.

### D-065 — Régulateur : que voit-il des factures ? — 2026-11-23
- **Décision** (choix de l'utilisateur : A) : **liste, fiche détail et PDF**, en **lecture seule**. Ni création, ni modification, ni émission (D-021).
- **Raison** : il peut renvoyer sa facture à un client qui la redemande, sans déranger le super-admin.
- **Impact** : `FactureCrudController` : créer et modifier réservés au super-admin ; bouton « Créer une facture » masqué sur les réservations pour le régulateur.

### D-064 — Régulateur : comment créer son compte ? — 2026-11-23
- **Décision** (choix de l'utilisateur : A) : dans le menu **« Administrateurs »** existant, réservé au super-admin. La liste des droits est simplifiée à **2 choix : « Super Admin » et « Régulateur »**. Le super-admin tape lui-même le mot de passe.
- **Raison** : on reste dans l'admin déjà connue ; plus de cases techniques qui prêtent à confusion.
- **Impact** : `AdminCrudController` (liste des droits réduite, accès réservé au super-admin même en tapant l'adresse). Pas de commande terminal.

### D-063 — Abonné : chauffeur indépendant ou dispatcheur ? — 2026-11-23
- **Décision** (choix de l'utilisateur : C) : **deux abonnements différents** : « chauffeur indépendant » et « dispatcheur ». L'abonnement dispatcheur est **plus cher** et **débloque le partage** des courses (bouton « Partager la course » : WhatsApp, Telegram, SMS, partage).
- **Raison** : le partage est un vrai service en plus pour ceux dont le métier est de distribuer des courses ; il justifie un prix plus élevé.
- **Impact** : un abonné a un **type** (chauffeur indépendant / dispatcheur). Le bouton Partager n'est visible que pour le type dispatcheur. Les deux montants restent à fixer avec D-022 (reportée).

### D-062 — Dispatcheur : qui fixe la commission et le prix chauffeur ? — 2026-11-23
- **Décision** (choix de l'utilisateur : A) : **le dispatcheur lui-même, course par course**. Il règle une fois son taux par défaut, puis peut le modifier à chaque partage, en € ou en % (comme D-024).
- **Raison** : chaque dispatcheur a ses propres accords avec ses chauffeurs ; la souplesse du bouton Partager actuel lui convient.
- **Impact** : le taux de commission par défaut devient un réglage **par abonné** (lien avec D-006 et D-016). La commission choisie reste enregistrée sur la réservation.

### D-061 — Dispatcheur : ses chauffeurs ont-ils un compte ? — 2026-11-23
- **Décision** (choix de l'utilisateur : A) : **non, pas de compte**. Les chauffeurs d'un dispatcheur reçoivent les courses par WhatsApp, Telegram ou SMS, comme les chauffeurs de SIMPLICI CAB aujourd'hui.
- **Raison** : rien de plus à construire au démarrage ; c'est déjà la pratique réelle.
- **Impact** : confirme D-013 (pas de comptes chauffeurs). Pas d'écran « mes courses » côté chauffeur.

### D-060 — Dispatcheur : comment partage-t-il une course ? — 2026-11-23
- **Décision** (choix de l'utilisateur : B) : **partage libre**. Le dispatcheur choisit lui-même ce qu'il envoie (course complète ou partielle), comme le bouton « Partager la course » actuel. Pas de partage en 2 temps imposé.
- **Raison** : plus souple ; ce sont les clients et les chauffeurs du dispatcheur, c'est à lui de décider ce qu'il partage.
- **Impact** : l'espace dispatcheur reprend le bouton « Partager la course » tel quel (modes complet / partiel / client). Le partage en 2 temps reste propre au régulateur interne (D-021).

### D-059 — Le dispatcheur : quel rôle ? — 2026-11-23
- **Décision** (mots de l'utilisateur) : « le dispatcheur a les mêmes fonctionnalités qu'un chauffeur indépendant + possibilité de partager les courses sur WhatsApp, Telegram, SMS et Partager ».
- **Raison** : un dispatcheur gère ses propres clients et courses comme un abonné, mais son métier est de les distribuer à des chauffeurs.
- **Impact** : complète D-013. Rôles prévus : super-admin, régulateur (interne SIMPLICI CAB, D-021), **abonné chauffeur indépendant**, **abonné dispatcheur** (= chauffeur indépendant + bouton « Partager la course » : WhatsApp, Telegram, SMS, partage natif), collaborateur. Le module « Partager la course » (déjà en prod) devient réutilisable dans l'espace abonné. `ROADMAP.md` (étape 4 « Espaces abonnés ») mis à jour. Numéro D-059 choisi car D-033 à D-058 sont déjà pris sur d'autres branches non fusionnées (traçage des visites, optimisation Ads).

### D-032 — Réservation par téléphone : date et heure par défaut — 2026-11-17
- **Décision** : sur `/admin/reservation-telephone`, le champ date/heure est **prérempli avec maintenant**, modifiable. Une **date passée est acceptée** (course oubliée puis dispatchée à la main sur les groupes, à saisir après coup).
- **Raison** : la plupart des appels sont pour tout de suite ; garder une trace complète des courses, même saisies en retard.
- **Impact** : correction sur une **branche séparée** `fix/reservation-telephone` partie de `main` (sans lien avec Partager), sa propre PR.

### D-031 — Partager : zone de texte réglable — 2026-11-17
- **Décision** : la zone de texte de « Partager la course » **s'agrandit toute seule** pour montrer tout le message, et reste **étirable à la souris**.
- **Raison** : la zone était trop petite pour relire le message avant l'envoi.
- **Impact** : `feature/partage-course`.

### D-030 — Menu « Messages » dans l'admin — 2026-11-17
- **Décision** : un **sous-menu « Messages »** qui se déplie en 2 entrées : « Catégories » et « Modèles de messages ». Il reste séparé de « Messages reçus » (formulaire de contact).
- **Raison** : ranger les textes de partage au même endroit, sans les confondre avec les messages des clients.
- **Impact** : `feature/partage-course`.

### D-029 — Catégories de messages libres — 2026-11-17
- **Décision** : l'admin **crée ses propres catégories** (ex. « Rappel », « Remerciement + promo », « Confirmation »), chacune rangée dans un des 3 onglets de Partager (Chauffeur, Groupe, Client). Chaque modèle de message appartient à une catégorie. Complète D-028.
- **Raison** : classer librement les messages au fil des besoins (promos, rappels…), sans passer par le code.
- **Impact** : nouvelle table des catégories + lien depuis les modèles, sur `feature/partage-course` (migration à adapter avant la mise en prod).

### D-023 — Partager une course : comment envoyer ? — 2026-11-17
- **Décision** : bouton « Partager la course » dans EasyAdmin, avec 3 modes : **complet** (chauffeur), **partiel** (groupe de chauffeurs), **message client**. Sous chaque texte prêt, 3 boutons : **SMS** (app Messages, numéro + texte remplis), **WhatsApp** (texte rempli), **Copier**. **Pas de Twilio** : l'admin envoie lui-même.
- **Raison** : envoi depuis le smartphone comme depuis l'ordinateur, sans coût SMS ; Copier couvre Telegram et les groupes.
- **Impact** : tâche « Partager la course » ajoutée dans `ROADMAP.md` (🧩 Fonctionnalités). Brique réutilisable pour le régulateur (D-021).

### D-024 — Commission modifiable au partage — 2026-11-17
- **Décision** : dans le partage complet et partiel, la commission se modifie en **€ ou en %** (l'autre valeur et le net chauffeur se recalculent). La valeur choisie est **enregistrée sur la réservation**. Sans saisie, on reste sur le taux par défaut (20 %, D-006).
- **Raison** : garder une trace de ce qui a été promis au chauffeur (factures, futur régulateur).
- **Impact** : nouveau champ commission sur `Reservation` + migration à lancer en prod.

### D-025 — Partage partiel : zone approximative — 2026-11-17
- **Décision** : le partage partiel montre une **zone approximative** préremplie automatiquement (code postal + ville tirés de l'adresse, ou nom du lieu connu : aéroport, gare), **modifiable avant l'envoi**. Ni nom ni téléphone du client.
- **Raison** : éviter qu'un chauffeur contacte le client en direct ; pas d'appel HERE Maps en plus.
- **Impact** : inclus dans la tâche « Partager la course ».

### D-026 — Remise de 10 % sur le trajet retour — 2026-11-17
- **Décision** : la remise est **appliquée à la main** par l'admin (prix modifiable de la réservation par téléphone). Pas de code promo. Elle récompense la réservation, **jamais l'avis Google** (règle Google : pas d'avis contre récompense) ; la demande d'avis reste une phrase à part dans le message.
- **Raison** : rien à coder en plus pour démarrer ; un code unique reste possible plus tard.
- **Impact** : aucun code ; texte du modèle « Remerciement ».

### D-027 — Bouton « Partager » toujours visible — 2026-11-17
- **Décision** : le bouton est visible quel que soit le statut, avec un **avertissement** quand le statut ne colle pas (ex. remerciement alors que la course n'est pas « Terminée », partage chauffeur d'une course annulée).
- **Raison** : l'admin garde la main, même s'il a oublié de changer le statut.
- **Impact** : inclus dans la tâche « Partager la course ».

### D-028 — Modèles de messages en base — 2026-11-17
- **Décision** : nouvelle sous-section EasyAdmin **« Messages »** : modèles de textes en base, modifiables, pour les 3 modes (chauffeur complet, groupe partiel, client). Variables remplacées automatiquement (`{prenom}`, `{date_course}`, `{lien_avis_google}`, `{telephone}`, `{depart}`, `{prix}`, `{commission}`, `{net_chauffeur}`, etc.). Modèles prêts au départ : **Remerciement** (texte validé ci-dessous), **Confirmation**, **Rappel la veille**, **Course chauffeur (complet)**, **Annonce groupe (partiel)**.
  Texte « Remerciement » : « Bonjour {prenom}, merci pour votre confiance ! Nous espérons que votre trajet du {date_course} s'est bien passé. Bonne nouvelle : vous bénéficiez de 10 % de réduction sur votre trajet retour. Appelez-nous ou répondez à ce message pour réserver. Votre avis compte pour nous : {lien_avis_google}. L'équipe ZenCAR — {telephone} »
- **Raison** : changer un texte sans passer par le code ni une mise en prod. Le lien avis vient de EasyAdmin > Avis Google (D-009).
- **Impact** : nouvelle entité + migration ; suggestions non retenues (lien GPS, trace des envois, chauffeur assigné) mises « En attente » dans `ROADMAP.md`.

### D-009 — Vrais avis Google sur le site — 2026-11-15
- **Décision** : les 3 faux avis de l'accueil sont retirés. Le site affiche **uniquement la note globale et le nombre d'avis Google** (aujourd'hui 5,0 · 124 avis) avec le bouton « Voir nos avis Google ». **Pas d'avis individuels.** La note et le nombre d'avis se modifient dans **EasyAdmin**, sans passer par le code. Pas d'API Google payante.
- **Raison** : les avis affichés étaient fictifs (rédigés par Claude), donc risque de pratique commerciale trompeuse ; la vraie fiche a 124 avis à 5,0, invisibles sur le site. Mise à jour manuelle simple et gratuite.
- **Impact** : code sur `feature/marketing` : réglage « note Google + nombre d'avis » en base (début de la table `SiteSettings` prévue dans la ROADMAP), écran EasyAdmin, bloc avis de l'accueil. Registre marketing S-05 et C-01.

### D-021 — Que peut voir et faire le régulateur ? — 2026-11-14
- **Décision** :
  - **Voit** : prix client, prix chauffeur, commission, factures.
  - **Fait** : changer les statuts, créer une réservation (téléphone), modifier ou annuler une course, télécharger les bons de commande.
  - **Interdit** (réservé au super-admin) : tarifs et forfaits, « Ma société », comptes admin, **création et émission des factures**.
  - **Partage des courses en 2 temps, sur WhatsApp et Telegram** (complément validé par l'utilisateur dans une autre conversation, 2026-11-14 ; remplace « prix client + prix chauffeur dans le message ») :
    1. **Annonce dans les groupes** : zone **approximative** de prise en charge (pas d'adresse exacte), date et heure, véhicule, **prix chauffeur uniquement** (pas le prix client).
    2. Les chauffeurs répondent dans le groupe avec leur **temps d'approche**.
    3. Le **régulateur choisit** le chauffeur parmi ceux qui ont répondu, puis lui envoie la **course complète en message privé** : nom, prénom, téléphone, adresse exacte, infos complémentaires, prix, commission, etc.
- **Raison** : le régulateur gère toute l'exploitation quotidienne ; la configuration, le légal et la facturation restent au super-admin. Le partage en 2 temps garde les données client chez le seul chauffeur retenu, et les chauffeurs du groupe ne voient plus la commission.
- **Impact** : nouveau rôle `ROLE_REGULATEUR` dans EasyAdmin (s'appuyer sur les rôles existants `ROLE_RESERVATIONS_*`) ; deux boutons de partage par course (« Annonce groupe » et « Envoi au chauffeur retenu »), pour WhatsApp et Telegram. Le choix du chauffeur est fait par le régulateur (précision confirmée par l'utilisateur, 2026-11-14).

### D-020 — Par quoi commencer ? — 2026-11-14
- **Décision** : **fondation d'abord** (« coller les étiquettes »), puis les fonctionnalités, dans cet ordre :
  0. **Fondation** : notion d'**Entreprise** (SIMPLICI CAB = entreprise n°1, seule pour l'instant) ; chaque réservation, facture, bon de commande, société et grille de prix **appartient à une entreprise**. Briques centralisées et réutilisables : *créer une réservation* (site, téléphone, collaborateur, abonné), *générer un PDF* (bon ou facture au nom de l'entreprise), *calculer un prix* (grille de l'entreprise). **Aucun changement visible** pour les clients.
  1. **Facturation** en prod, posée sur la fondation.
  2. **Régulateur** + partage WhatsApp.
  3. **Collaborateurs**.
  4. **Espaces abonnés** (inscription, validation, leur admin).
- **Raison** : idée de l'utilisateur : centraliser une seule fois pour ne pas refaire la Facturation et les Collaborateurs au moment du multi-entreprises (gain de temps et de tokens). La fondation reste invisible et sans risque pour le site.
- **Impact** : roadmap réorganisée ; `feature/facturation` (pas encore en prod) sera adaptée à la fondation avant sa mise en prod.
- **Modification (2026-11-15)** : la **Facturation passe en prod seule, avant la fondation** (demande de l'utilisateur : besoin de facturer tout de suite ; la fondation était bloquée par le manque de place disque). Elle sera rattachée à l'entreprise n°1 lors de la fondation (coût faible). En attendant, D-001 (brouillon → émise) et D-003 (numéro à l'émission) **ne sont pas encore appliqués** : une facture reste modifiable après création, donc **ne jamais modifier une facture déjà envoyée** à un client.

### D-019 — Collaborateurs : prix convenus et paiement — 2026-11-13
- **Décision** :
  - Prix : des **forfaits propres au collaborateur** (ex. Hôtel X → Aéroport = 55 €) **+ une remise en %** sur la grille SIMPLICI CAB pour les autres trajets.
  - Paiement : **au choix à chaque commande** : « sur le compte » du collaborateur (regroupé sur une **facture mensuelle**) ou **payé par le passager** au chauffeur.
- **Raison** : couvre les trajets récurrents (forfaits) et les trajets ponctuels (remise) ; le choix par course s'adapte aux hôtels (course offerte au client ou non).
- **Impact** : le collaborateur a un compte de connexion, ses forfaits, son taux de remise ; chaque réservation note le mode « compte collaborateur » ou « passager » ; il faut une **facture mensuelle** des courses « sur le compte ». À préciser au développement : saisie du nom et du téléphone du passager à la commande.

### D-018 — Collaborateurs : à qui sont-ils rattachés ? — 2026-11-13
- **Décision** : au démarrage, les collaborateurs sont ceux de **SIMPLICI CAB uniquement**. Les abonnés pourront avoir les leurs plus tard.
- **Raison** : faisable **sur le site actuel**, sans attendre le multi-entreprises ; valeur commerciale immédiate.
- **Impact** : module Collaborateurs construit avant le multi-entreprises, en prévoyant qu'un collaborateur sera un jour rattaché à un abonné.

### D-017 — Factures et bons de commande des abonnés — 2026-11-13
- **Décision** : les bons de commande et factures d'un abonné sont émis **à son nom** (sa société validée, ses mentions légales), avec **sa propre numérotation** (`FAC-AAAA-0001` propre à chaque abonné). Les règles D-001 à D-004 s'appliquent à chacun. ZenCar n'est que l'éditeur du logiciel et **facture l'abonnement** à chaque abonné.
- **Raison** : obligation légale, la numérotation est propre à chaque entreprise émettrice.
- **Impact** : `Facture` et la numérotation deviennent **par abonné** ; `Societe` (aujourd'hui unique) devient une société par abonné.

### D-016 — Tarifs des abonnés — 2026-11-13
- **Décision** : une **liste commune de catégories** de véhicules (éco-berline, berline, grand coffre, van…, gérée par le super-admin). Chaque abonné **coche** celles qu'il propose et fixe **ses propres prix** (prise en charge, prix au km, seuil).
- **Raison** : les slugs restent cohérents dans tout le code (convention underscore) ; chaque abonné garde la main sur ses prix.
- **Impact** : séparer la *catégorie* (commune) de la *grille de prix* (par abonné ; SIMPLICI CAB a la sienne). Majoration de nuit et commission par abonné : à voir avec D-006.

### D-015 — Validation de la société d'un abonné — 2026-11-13
- **Décision** : **aucune pièce justificative** demandée. L'abonné saisit **une fois** les informations de sa société (raison sociale, SIREN/SIRET, adresse, TVA, REVTC…), puis le super-admin les **valide**. Avant validation, l'abonné n'a accès **qu'à ce formulaire** ; le reste de son espace est fermé.
- **Raison** : ZenCar vend un outil, l'abonné reste responsable de ses informations légales ; la validation sert à vérifier la cohérence avant qu'elles apparaissent sur ses bons et factures.
- **Impact** : états d'un abonné : **en attente de validation** → **actif** → **lecture seule** (D-014). Les infos validées alimentent ses bons de commande et factures (comme `Societe` aujourd'hui, mais une par abonné).

### D-014 — Abonnement : paiement et impayés — 2026-11-13
- **Décision** : paiement **manuel** (virement…) au démarrage ; le super-admin **active / désactive** le compte à la main. En cas d'impayé, le compte passe en **lecture seule** : consultation et téléchargement des factures et bons passés, mais plus de création.
- **Raison** : aucun développement de paiement pour les premiers abonnés ; la lecture seule respecte l'obligation de conservation des factures. Stripe pourra venir au-delà de 5-10 abonnés.
- **Impact** : un abonné a un **état** (en attente de validation / actif / lecture seule). Prix : question D-022.

### D-013 — Rôles : qui est qui ? — 2026-11-13
- **Décision** : 4 rôles.
  - **Super-admin** (toi) : valide les sociétés des abonnés, gère les abonnements, voit tout ce qui concerne SIMPLICI CAB.
  - **Régulateur** : travaille **pour SIMPLICI CAB** (pas un abonné). Il voit **uniquement les courses de SIMPLICI CAB** (dzencar.fr, réservations téléphone) et les **dispatche dans des groupes WhatsApp**. L'outil l'aide à partager (message prêt à envoyer).
  - **Abonné** : un chauffeur ou une société qui paie l'abonnement ; **1 abonnement = 1 seul compte** au démarrage (pas de sous-comptes chauffeurs). Il ne voit que ses propres données (D-011).
  - **Collaborateur** : établissement partenaire qui commande aux prix convenus (D-018, D-019).
- **Raison** : le dispatch WhatsApp est déjà la pratique réelle ; un seul compte par abonné garde le démarrage simple. Des comptes chauffeurs pour les sociétés abonnées pourront venir plus tard.
- **Impact** : le rôle Régulateur ne dépend pas du multi-entreprises et peut être construit **sur le site actuel** ; nouvelle question D-021 (droits du régulateur).

### D-012 — Le site public de réservation pour les abonnés — 2026-11-13
- **Décision** : au démarrage, **pas de réservation en ligne** pour les clients des abonnés : l'abonné saisit lui-même les courses dans son espace (sur le modèle de la « réservation téléphone »). Une page publique par abonné pourra venir plus tard.
- **Raison** : rien de public à construire ni à sécuriser au départ ; réutilise un écran qui existe déjà.
- **Impact** : la page « réservation téléphone » devient la base de la saisie de course dans l'espace abonné.

### D-011 — Quel modèle de plateforme ? — 2026-11-13
- **Décision** : ZenCar devient un **outil de gestion** (SaaS) : chaque abonné (chauffeur, régulateur) gère **ses propres** clients, courses, tarifs, bons de commande et factures dans son espace. ZenCar **ne distribue pas** de courses aux abonnés ; les clients de dzencar.fr restent ceux de SIMPLICI CAB.
- **Raison** : modèle le plus simple et le plus clair juridiquement (ZenCar vend un logiciel, pas des courses). Une place de marché pourra s'ajouter plus tard.
- **Impact** : les données de chaque abonné doivent être **séparées** (un abonné ne voit jamais les clients ni les courses d'un autre).

### D-010 — Qui passe une réservation à « Terminée » ? — 2026-11-12
- **Décision** : pour l'instant, **l'admin** passe la réservation à « Terminée » à la main, dans EasyAdmin.
- **Raison** : simple, pas d'automatisme à construire ; on pourra automatiser plus tard (passage auto quelques heures après le départ).
- **Impact** : bouton « Terminée » ajouté à la tâche « Statut de réservation » dans `ROADMAP.md`.

### D-005 — Statuts d'une réservation et annulation — 2026-11-12
- **Décision** :
  - Statuts : **En attente** (code SMS pas encore validé) → **Confirmée** → **Terminée**, ou **Annulée**.
  - Confirmation **automatique** dès que le client valide son code SMS ; l'admin reçoit un SMS, comme aujourd'hui.
  - **Seul l'admin annule**, dans EasyAdmin ; le client le demande par téléphone ou WhatsApp.
  - Annulation **gratuite**, sans frais.
- **Raison** : rien à construire côté client ; le paiement se fait au chauffeur, donc des frais seraient difficiles à encaisser. À revoir si les annulations de dernière minute deviennent un problème.
- **Impact** : tâche « Statut de réservation » précisée dans `ROADMAP.md` ; nouvelle question D-010 (passage à « Terminée »).
- **Correction (2026-11-13)** : le contexte de départ (« aucun statut ») était faux, il venait d'un ancien prompt. Le code avait déjà 7 statuts (`ReservationStatus` : en attente, confirmée, chauffeur assigné, chauffeur arrivé, client à bord, terminée, annulée) et la confirmation automatique. La décision reste valable et est en grande partie déjà appliquée.

### D-004 — Nom du client sur la facture — 2026-11-12
- **Décision** : le prénom est pré-rempli depuis la réservation ; l'admin complète nom, adresse, SIRET et TVA dans le brouillon avant d'émettre. Le formulaire de réservation ne change pas.
- **Raison** : simple, pas de friction pour le client ; les factures entreprise sont rares.
- **Impact** : aucun code, les champs existent déjà dans le formulaire de facture (EasyAdmin).

### D-003 — Format des numéros de facture — 2026-11-12
- **Décision** : on garde `FAC-AAAA-0001`, avec une remise à 0001 chaque 1er janvier. Le numéro est attribué **à l'émission** (cf. D-001), pas à la création du brouillon.
- **Raison** : format courant et lisible ; numéroter à l'émission évite les trous si un brouillon est abandonné.
- **Impact** : format déjà en place dans le code ; le moment d'attribution est ajouté à la tâche D-001 dans `ROADMAP.md`.

### D-001 — Une facture émise peut-elle encore être modifiée ? — 2026-11-12
- **Décision** : une facture naît en **brouillon** (modifiable), puis passe à **émise** via un bouton « Émettre » : elle est alors verrouillée. Une erreur sur une facture émise se corrige par un **avoir**.
- **Raison** : souple pour corriger une faute de frappe avant envoi, conforme à la loi une fois la facture envoyée au client.
- **Impact** : tâche ajoutée dans `ROADMAP.md` (🧩 statut brouillon/émise + verrouillage + avoir).
