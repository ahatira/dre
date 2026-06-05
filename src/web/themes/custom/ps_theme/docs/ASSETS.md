# Assets — Figma Stellar → `ps_theme`

> Maquette : [BNP PRE Stellar (Figma)](https://www.figma.com/file/rrA1dlYnJMzcXlwOZ5iuuw/BNP-PRE-Stellar---Livrable-client)

## Source des assets

| Asset | Fichier | Origine |
|---|---|---|
| Logo mark (header) | `logo.svg` (racine thème) + `assets/images/logo/header-logo.svg` | **Export Figma** node `I48:8052;17:429` |
| Logo complet BNPPRE | `assets/images/logo/logo-bnp-re.svg` | `bnp_admin/images/logo-bnp.svg` (fallback) |
| Logo footer | `assets/images/logo/footer-logo.svg` | **Export Figma** node `I48:8170;18:783` |
| Favicon | `favicon.ico` | `bnp_admin/images/favicon.ico` |
| Icônes UI (favoris, menu, compte, réseaux) | Pack `bnp_custom` via **ui_icons** (`ui_suite_bnp/assets/icons/custom/`) | `ui_suite_bnp` — **ne pas dupliquer** dans `ps_theme` |
| Hero homepage | `assets/images/hero/hero-homepage.png` | **Export Figma** node `918:13529` |
| Hero profil | `assets/images/hero/hero-profile.png` | **Export Figma** node `48:7863` |
| Hero fallback | `assets/images/hero/hero-default.jpg` | Copie de hero-homepage |
| Placeholder offre | `assets/images/offer-placeholder.svg` | Généré Stellar (fallback cartes) |

Le dossier `ui_suite_bnp/work/` contient l’intégralité des **icônes, tokens et styles SCSS** alignés Figma RE/BNPP. Pour de nouvelles icônes, copier depuis ce répertoire plutôt que recréer.

## Structure

```
assets/images/
├── logo/
│   ├── header-logo.svg    # Export Figma header
│   ├── footer-logo.svg    # Export Figma footer
│   └── logo-bnp-re.svg    # Logo + wordmark (fallback)
├── hero/
│   └── hero-default.jpg   # Temporaire — remplacer par export maquette
└── offer-placeholder.svg
```

## Icônes (ui_icons)

Utiliser le pack **`bnp_custom`** du thème parent — pas de copies locales.

```twig
{{ icon('bnp_custom', 'fav-stroke', {'size': '20px'}) }}
{{ icon('bnp_custom', 'burger-menu', {'size': '24px'}) }}
{{ icon('bnp_custom', 'account', {'size': '20px'}) }}
{{ icon('bnp_custom', 'linkedin', {'size': '20px'}) }}
```

Source : `ui_suite_bnp/assets/icons/custom/{group}/*.svg` — manifeste `custom-icons.generated.yml`.

## Logo dans Drupal (Config-First)

- **Config** : `config/install/ps_theme.settings.yml` → `logo.use_default: true`
- Drupal utilise `logo.svg` à la racine du thème (symlink logique vers le mark BNP RE)
- Bloc branding : `block.block.ps_theme_branding` → logo + slogan (`use_site_slogan: true`, texte `system.site:slogan`)
- Pas de chemin logo en dur dans Twig

## Ré-export depuis Figma

Fichier Figma : `rrA1dlYnJMzcXlwOZ5iuuw` (BNP PRE Stellar — Livrable client)

| Asset | Node ID Figma | Fichier exporté |
|---|---|---|
| Logo header | `I48:8052;17:429` | `logo/header-logo.svg` |
| Logo footer | `I48:8170;18:783` | `logo/footer-logo.svg` |
| Hero homepage | `918:13529` | `hero/hero-homepage.png` |
| Hero profil connecté | `48:7863` | `hero/hero-profile.png` |

Script automatisé (token via variable d'environnement, **jamais commité**) :

```bash
export FIGMA_TOKEN='figd_...'
bash web/themes/custom/ps_theme/scripts/figma-export.sh
```

Le script met à jour `logo.svg` (racine thème) et `hero/hero-default.png`.

### Export manuel

Figma → sélectionner le calque → Export PNG/SVG 2x → placer dans `assets/images/`.

### Note technique

Les logos exportés en SVG depuis Figma peuvent contenir une image PNG base64 embarquée. Pour le header Drupal, préférer `header-logo.png` (export scale 3) si besoin de netteté.

## Prochaines extractions maquette (Phase B+)

- [ ] Hero homepage (photo full-width + overlay)
- [ ] Logo header version wordmark horizontale (si différent du mark seul)
- [ ] Icônes réseaux sociaux footer (`ui_suite_bnp/work/icons/social-media/`)
- [ ] Images univers métier (bureaux, logistique, retail…)
