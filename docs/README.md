# docs/ — la mémoire du projet ZenCar

Tout ce qu'il faut savoir pour reprendre le projet sans rien chercher.

| Fichier | À quoi il sert | Quand le lire / l'écrire |
|---|---|---|
| [ROADMAP.md](ROADMAP.md) | Les 3 objectifs en cours + la liste d'attente | Lire en ouvrant le Pilote |
| [JOURNAL.md](JOURNAL.md) | Ce qui a été fait, session par session | Écrire à chaque « clôture » |
| [deploiement.md](deploiement.md) | Mettre en production sur Infomaniak | Avant chaque mise en prod |
| [git.md](git.md) | Les branches et les commandes de base | Quand tu hésites |
| [marketing/google-ads.md](marketing/google-ads.md) | Campagne Google Ads | Session Marketing |
| [marketing/seo.md](marketing/seo.md) | Pages SEO et mots-clés ciblés | Session Marketing |

Les règles techniques du code (slugs, OTP, tarification…) sont dans [`CLAUDE.md`](../CLAUDE.md) à la racine.
Les tables : les entités dans `src/Entity/` sont la référence.

## Organisation des conversations Claude

- **📌 ZenCar — Pilote** (épinglée) : faire le point, choisir la prochaine tâche, lancer les sessions.
- Une session par tâche, rangée dans sa section : 🧩 Fonctionnalités · ⚛️ React · 📣 Marketing · 🚀 Infra.
- En fin de session, dire **« clôture »** → mise à jour de `JOURNAL.md` (+ `ROADMAP.md` si besoin), commit, archivage.
