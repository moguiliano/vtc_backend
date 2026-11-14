# 03 — Google Ads

> Objectif : une campagne qui génère des appels rentables, avec un suivi fiable.
> Référence : [google-ads.md](../google-ads.md) (structure 5 groupes, mots-clés négatifs, décisions). Y recopier les annonces validées.

## Données à joindre
- [ ] Export 90 jours : **termes de recherche** (le plus important), mots-clés, annonces, conversions par groupe
- [ ] Capture des réglages : zone, horaires, appareils, enchères, budget
- [ ] Capture des **actions de conversion** (ce qui est compté comme conversion et comment)
- [ ] Captures des annonces concurrentes sur « taxi marseille » et « taxi aéroport marseille »
- [ ] Combien d'appels deviennent une course (estimation)
- [ ] Prix moyen d'une course par type (taxi, VTC, aéroport)

## Prompt

```
# ÉTAPE 3 — GOOGLE ADS

Données jointes : [liste].

1. SUIVI DES CONVERSIONS (d'abord, car sans lui tout le reste est faussé) :
   - ce qui est compté aujourd'hui, et ce qui manque (appels depuis l'annonce, clics sur le numéro du site, clics WhatsApp, réservations en ligne validées par SMS) ;
   - le plan de suivi exact via Google Tag Manager : déclencheurs, balises, valeur attribuée à chaque conversion ;
   - Consent Mode v2 et bannière de consentement : ce qui est obligatoire, la solution la plus simple.

2. ANALYSE DES TERMES DE RECHERCHE :
   - les termes qui coûtent sans convertir → nouveaux mots-clés négatifs (liste prête à coller) ;
   - les termes qui convertissent → à ajouter en mots-clés ;
   - décision chiffrée sur le mot-clé [taxi] : garder, exclure ou remplacer.

3. STRUCTURE : faut-il garder 5 groupes avec 10 €/jour, ou concentrer le budget ? Réponds avec les chiffres.

4. ANNONCES — pour chaque groupe retenu, une annonce responsive complète :
   - 15 titres (30 caractères max, compte-les) et 4 descriptions (90 caractères max) ;
   - au moins 3 titres avec le mot-clé principal ;
   - Taxi : jamais de prix fixe. VTC : le prix affiché est un argument.
   - indique les titres à épingler en position 1.

5. EXTENSIONS (composants) : appel, lieu (fiche Google), accroches, extraits de site, liens annexes vers les pages du site, avec le texte exact.

6. RÉGLAGES : horaires de diffusion, ajustements, budget par groupe, stratégie d'enchères et seuil à partir duquel la changer.

7. Cible à 30 jours : coût par appel, nombre d'appels, coût par course.

AUTO-CRITIQUE : vérifie le nombre de caractères de chaque titre et de chaque description. Vérifie qu'aucune annonce taxi n'affiche de prix fixe.
```
