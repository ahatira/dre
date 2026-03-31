# UI Suite BNP PRE Starterkit

Starterkit de base pour generer des sous-themes a partir du theme autonome UI Suite BNP PRE.

## Usage

```bash
php core/scripts/drupal generate-theme my_theme --starterkit ui_suite_bnppre_starterkit --path themes/custom
```

## Compilation

```bash
cd themes/custom/my_theme
npm install
npm run gulp-prod
```

## Notes

Les fichiers SCSS fournis permettent de repartir d'une base Bootstrap 5 compilee et surchargeable pour les futurs sous-themes.
