# UI Suite BNP Starterkit

Starterkit monolithique pour generer un sous-theme BNPPRE avec compilation
Sass centralisee.

## Objectif

Ce starterkit est adapte si vous souhaitez:

- compiler les styles dans un pipeline unique;
- garder une couche d override simple;
- rester proche du socle `ui_suite_bnp`.

## Generation du sous-theme

```bash
php core/scripts/drupal generate-theme my_theme --starterkit ui_suite_bnp_starterkit --path themes/custom
```

Reference Drupal:

- <https://www.drupal.org/docs/core-modules-and-themes/core-themes/starterkit-theme>

## Workflow front recommande

Dans le sous-theme genere:

```bash
cd themes/custom/my_theme
npm install
npm run build
npm run watch
```

Le workflow npm est prefere a Yarn pour rester homogene avec BNPPRE.

## Notes techniques

- Les chemins vers Bootstrap peuvent etre ajustes selon votre installation locale.
- Les integrations CKEditor, utilitaires et gradients restent des exemples de
  depart, a adapter au design system du site.

## Compatibilite

Ce starterkit evolue avec le theme parent et peut introduire des changements
structurants entre versions.
