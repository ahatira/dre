# UI Suite BNP Starterkit Split

Starterkit split pour un sous-theme BNPPRE avec Sass par composant.

## Objectif

Ce starterkit est adapte si vous souhaitez:

- une architecture front orientee composants;
- isoler les styles metier par bloc UI;
- faciliter les revues et regressions CSS par zone.

## Generation du sous-theme

```bash
php core/scripts/drupal generate-theme my_theme --starterkit ui_suite_bnp_starterkit_split --path themes/custom
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

- Les imports Sass restent decoupes par composant pour simplifier les overrides.
- Ajuster les chemins Bootstrap selon la structure locale du projet.
- Garder les tokens globaux dans un point d entree unique pour eviter les
  divergences entre composants.

## Compatibilite

Ce starterkit evolue avec le theme parent et peut introduire des changements
structurants entre versions.
