# UI Suite BNP

UI Suite BNP est le theme Drupal 11 de reference pour BNPPRE International.

Le theme est base sur Bootstrap 5, UI Patterns et UI Styles, avec un objectif
clair:

- offrir des composants BO administrables et reutilisables;
- appliquer une base de design system commune BNPPRE;
- permettre des variantes pays sans couplage fort inter-sites.

## Principes techniques

- Stack cible: Drupal 11+, PHP 8.3+, Bootstrap 5+.
- Runtime front: local only, sans CDN.
- Design system: tokens CSS/Sass et variantes de framework.
- Multi-site: un socle commun + variantes locales explicitement versionnees.

## Prerequis

Modules Drupal requis:

- UI Patterns
- UI Styles

Les librairies front externes sont installees via npm puis generees dans le
theme (`assets/vendor/*`).

## Frameworks CSS disponibles

Le theme expose les bibliotheques suivantes dans les settings:

- `ui_suite_bnp/framework_css_bnpp` (identite BNP Paribas)
- `ui_suite_bnp/framework_css_re` (socle Real Estate)

Par defaut, la configuration d installation charge:

- JS: `ui_suite_bnp/framework_js`
- CSS: `ui_suite_bnp/framework_css_re`

## Workflow assets npm

Le workflow assets est centralise a la racine du theme.

Separation stricte:

- `work/*`: sources de travail versionnees.
- `assets/*`: sorties de build locales non versionnees (sauf `assets/images/*`).

Structure de travail:

- `work/fonts/custom`: fontes custom a versionner/coller pour la marque.
- `work/styles/scss`: toutes les sources styles (framework + legacy converti).
- `work/scripts`: JS source Drupal behaviors.
- `work/tools`: scripts de build npm (manifest, helpers).
- `work/icons`: SVG a optimiser/integrer.

1. Installer les dependances:

   ```bash
   cd web/themes/custom/ui_suite_bnp
   npm install
   ```

2. Compiler les assets CSS/JS/vendor:

   ```bash
   npm run build
   ```

   En mode source-only, cette etape est obligatoire apres clone/pull avant
   execution du theme en local/CI.

3. Lancer la recompilation automatique en dev:

   ```bash
   npm run watch
   ```

Sources Sass:

- `work/styles/scss/framework/framework_css_bnpp.scss`
- `work/styles/scss/framework/framework_css_re.scss`
- `work/styles/scss/framework/_fonts.scss`
- `work/styles/scss/framework/_foundation.scss`

Workflow polices:

- `Open Sans` est installee via npm (`@fontsource/open-sans`) et recopies localement dans `assets/fonts/open-sans` au build.
- Les variantes custom `BNP Sans` sont attendues dans `work/fonts/custom/bnp-sans/` en `woff2` et `woff`.
- Nommage attendu pour `BNP Sans`: `bnp-sans-light`, `bnp-sans-light-italic`, `bnp-sans-regular`, `bnp-sans-italic`, `bnp-sans-semibold`, `bnp-sans-semibold-italic`, `bnp-sans-bold`, `bnp-sans-bold-italic`.
- Compilation/copie: `npm run build` ou `npm run build:css` recopient les fontes vers `assets/fonts/*`.

Sorties CSS generees (non versionnees):

- `assets/css/framework/framework_css_bnpp.css`
- `assets/css/framework/framework_css_re.css`

Sorties vendor generees dans le theme (non versionnees):

- `assets/vendor/bootstrap/bootstrap.min.css`
- `assets/vendor/bootstrap/bootstrap.bundle.min.js`
- `assets/vendor/bootstrap-icons/icons/*.svg`
- `assets/fonts/open-sans/*`
- `assets/fonts/custom/bnp-sans/*`

Workflow icones:

- Entrants: `work/icons/*.svg`
- Optimisation: `npm run build:icons:svgo`
- Manifest: `npm run build:icons`
- Sortie: `assets/icons/custom` (non versionnee)

## Alignement design

Le framework `framework_css_re` prepare le socle visuel en coherence avec les
maquettes de `docs/design/system` (palette, contraste, surfaces, hierarchie
typographique) et les pages `docs/design/pages`.

Les ajustements pays doivent etre integres de maniere maitrisee sur le socle
`framework_css_re`, sans multiplier les variantes de framework au niveau theme.

## Starterkits

- [Starterkit monolithique](./starterkits/ui_suite_bnp_starterkit/README.md)
- [Starterkit split par composant](./starterkits/ui_suite_bnp_starterkit_split/README.md)

## Documentation complementaire

Voir `docs/` pour les details fonctionnels et limites techniques:

- [Details](./docs/Details.md)
- [Forms](./docs/Forms.md)
- [Modal](./docs/Modal.md)
- [Out of scope](./docs/Out-of-scope.md)
- [Limitations](./docs/Limitations.md)
