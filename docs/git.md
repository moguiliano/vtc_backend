# Git — mode d'emploi ZenCar

## Les branches

| Branche | Rôle |
|---|---|
| `main` | Ce qui tourne en production. On n'y code jamais directement. |
| `feature/<sujet>` | Une branche par sujet (`feature/facturation`, `feature/react`…). Courte durée. |

Une branche est terminée quand elle est fusionnée dans `main` via une Pull Request sur GitHub.

## Les commandes du quotidien

> Ton Git est en version 2.15 : `git switch` n'existe pas, utilise `git checkout`.

```bash
git status                          # où j'en suis ?
git checkout main                   # changer de branche
git checkout -b feature/mon-sujet   # créer une branche (depuis la branche actuelle)
git add <fichiers>                  # préparer un commit
git commit -m "feat: ..."           # enregistrer
git push                            # envoyer sur GitHub
git merge main                      # récupérer dans ma branche ce qui a été ajouté à main
```

## Messages de commit

- `feat:` nouvelle fonctionnalité
- `fix:` correction de bug
- `chore:` maintenance (migrations, dépendances, nettoyage)
- `docs:` documentation

## Règles

1. **Un sujet par branche.** Facturation et React ne se mélangent pas.
2. **Commiter dès qu'un petit morceau marche**, pas à la fin de la semaine.
3. Les migrations générées dans un worktree Claude doivent être copiées dans `migrations/` du projet principal.
4. Jamais de secrets dans Git : `.env.local` et `.claude/settings.local.json` restent locaux.
