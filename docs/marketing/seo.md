# Référencement (SEO)

Domaine : `https://dzencar.fr` — sitemap : `/sitemap.xml` — `public/robots.txt` bloque `/admin`, `/login`, `/logout`, `/reservation/success`.

## Règles de contenu

- Le site est **ZenCAR Taxi & VTC** : Taxi et VTC mis en avant de façon équilibrée.
- **Taxi** : prix au compteur, tarifs réglementés Ville de Marseille → jamais de prix fixe affiché.
- **VTC** : prix calculé et affiché avant réservation.
- **Forfaits VTC** : uniquement sous le label « Forfaits VTC — Prix fixe affiché avant réservation » (gérés en BDD, partial `templates/partials/_forfaits.html.twig`).
- Un seul `h1` par page, `alt` descriptif sur les images, canonical sur chaque page.

## Pages et mots-clés ciblés

| Page | Template | Mot-clé principal | Groupe Ads |
|---|---|---|---|
| `/` | `home.html.twig` | taxi marseille / vtc marseille | 1 |
| `/taxi-marseille` | `taxi-marseille.html.twig` | taxi marseille | — |
| `/vtc-marseille` | `vtc-marseille.html.twig` | vtc marseille | — |
| `/taxi-aeroport-marseille` | `taxi-aeroport-marseille.html.twig` | taxi aéroport marseille | 2 |
| `/taxi-gare-saint-charles` | `taxi-gare-saint-charles.html.twig` | taxi gare saint charles | 3 |
| `/transport-seniors-marseille` | `transport-seniors-marseille.html.twig` | transport seniors marseille | 4 |
| `/taxi-calanques-marseille` | `taxi-calanques-marseille.html.twig` | taxi calanques marseille | 5 |
| `/services` | `services.html.twig` | — | — |

## Déjà en place

- OG tags + JSON-LD (home, services)
- FAQ sur transport-seniors
- Sitemap.xml + robots.txt
- Téléphone visible dans le héro de la home, section « Taxi — Appel immédiat », section avis clients
- Pages mentions légales et politique de confidentialité

## À faire

- [ ] Compléter le footer (mentions légales, RGPD, liens internes, téléphone)
- [ ] Remplacer les 3 avis statiques par de vrais avis Google
