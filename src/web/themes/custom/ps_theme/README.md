# Property Search Theme (`ps_theme`)

Sous-thème front **Property Search** héritant de [`ui_suite_bnp`](../ui_suite_bnp/README.md).
Aligné sur la maquette Figma **BNP PRE Stellar**.

## Rôle

| Couche | Thème | Contenu |
|---|---|---|
| Générique BNPPRE | `ui_suite_bnp` | Bootstrap 5, ~40 SDC, tokens RE/BNPP, Layout Builder companion |
| Spécifique PS | `ps_theme` | Composants métier offre/recherche, templates node/view, overrides Stellar |

## Génération

Généré depuis le starterkit split :

```bash
php web/core/scripts/drupal generate-theme ps_theme \
  --name "Property Search Theme" \
  --path themes/custom \
  --starterkit ui_suite_bnp_starterkit_split
```

## Build assets

```bash
cd web/themes/custom/ps_theme
npm install
npm run gulp-prod   # production
npm run gulp-dev    # dev + sourcemaps
```

Le parent `ui_suite_bnp` doit être compilé au préalable (`npm run build` dans son répertoire).

Prérequis Sass : lien `web/libraries/bootstrap` → bootstrap du parent (non versionné) :

```bash
ln -sfn ../themes/custom/ui_suite_bnp/node_modules/bootstrap web/libraries/bootstrap
```

## Documentation

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — vision dual-theme, phases, conventions
- [`docs/COMPONENT_MATRIX.md`](docs/COMPONENT_MATRIX.md) — mapping Figma Stellar → composants Drupal
- [`docs/ASSETS.md`](docs/ASSETS.md) — logos, icônes, exports Figma

## Activation

```bash
make drush -- theme:enable ps_theme
make drush -- config:set system.theme default ps_theme -y
make drush-cr
```
