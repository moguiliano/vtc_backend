# 05 — Avis, fidélisation et clients professionnels

> Objectif : plus d'avis Google, des clients qui reviennent, et des clients pros qui apportent des courses régulières.

## Données à joindre
- [ ] Tous les avis Google (ou les 30 derniers), copiés tels quels
- [ ] Les avis affichés aujourd'hui sur l'accueil du site, et d'où ils viennent
- [ ] Nombre de clients qui ont fait plus d'une course (estimation)
- [ ] Clients pros actuels ou passés (hôtels, entreprises, EHPAD…) et ce qu'ils commandent
- [ ] Ce que tu peux offrir : réduction, course offerte, facturation mensuelle…

## Prompt

```
# ÉTAPE 5 — AVIS, FIDÉLISATION ET CLIENTS PROFESSIONNELS

Données jointes : [liste].

Applique la méthode du contexte, dans cet ordre.

PHASE A — INVENTAIRE : nombre d'avis joints, période, répartition des notes. Vérifie que la note moyenne calculée correspond à celle affichée par Google. Lis chaque avis en entier.

PHASE B — VALIDATION, une question à la fois, sans passer à la suivante tant qu'elle n'est pas tranchée. Au minimum : origine des avis de l'accueil (S-05, V-01), note et nombre d'avis Google (C-01), clients qui reviennent (C-04), clients professionnels (C-05), ce que je peux offrir. Si un avis décrit un incident que tu ne peux pas interpréter, demande-moi ce qui s'est passé avant de rédiger la réponse.

PHASE C — ANALYSE, seulement quand les questions bloquantes sont tranchées.

1. AVIS SUR LE SITE (urgent, risque juridique) :
   - les avis affichés aujourd'hui sont-ils conformes ? Que dit la loi française sur l'affichage d'avis ?
   - ce qu'il faut afficher à la place : texte exact, avec un lien vers la fiche Google.

2. OBTENIR PLUS D'AVIS GOOGLE :
   - le moment et le canal pour demander (SMS après la course, carte avec QR code dans le véhicule, chauffeur à l'oral) ;
   - 3 versions du SMS de demande d'avis (160 caractères max) ;
   - le texte de la carte avec QR code ;
   - ce qui est interdit (avis récompensés, filtrage des clients mécontents).
   Objectif chiffré : nombre d'avis dans 3 mois.

3. RÉPONDRE AUX AVIS :
   - analyse les avis joints : thèmes positifs et reproches récurrents, avec leur cause probable ;
   - modèles de réponse : avis 5 étoiles, avis moyen, avis négatif sur le retard, sur le prix, sur le chauffeur ;
   - réponses rédigées pour les avis négatifs joints.

4. FIDÉLISATION (pour une petite structure, sans application) :
   - comment donner envie de rappeler ZenCAR plutôt qu'Uber : contact WhatsApp enregistré, carte, tarif client régulier, parrainage ;
   - mécanique simple et son coût ;
   - 3 messages à envoyer (après la 1re course, client inactif, avant les vacances).

5. CLIENTS PROFESSIONNELS :
   - les 5 types de clients pros les plus rentables à Marseille pour ZenCAR (hôtels, conciergeries et Airbnb, EHPAD et résidences seniors, cliniques, entreprises, organisateurs d'événements, croisières), avec le raisonnement ;
   - l'offre pro : tarifs, facturation, engagement de service ;
   - une séquence de prospection rédigée : email, relance, script d'appel, message à laisser lors d'une visite ;
   - une page « Entreprises » pour le site : plan et texte.

AUTO-CRITIQUE : vérifie que chaque proposition respecte la règle taxi/VTC et la loi sur les avis, et qu'elle est réaliste pour une petite équipe. Vérifie que chaque thème tiré des avis cite le nombre d'avis qui le mentionnent.

FIN DE L'ÉTAPE : ce qui n'a pas été analysé, questions restées ouvertes, puis le bloc « REGISTRE MIS À JOUR ».
```
