# About PS Theme

PS Theme is a custom Drupal theme which is compatible with Drupal 10 and 11. PS Theme is the
default front-end theme for BNP Paribas RealEstate.

PS Theme is built using [Storybook](https://storybook.js.org/) (HTML edition), and [Vite](https://vitejs.dev/) (Vanilla JS edition), with the help of many NodeJS packages to improve automation and make use of the latest Front-End tooling. See `package.json` for specifics about packages being used.

## JavaScript (Drupal-friendly behaviors)

All component scripts MUST be Drupal-ready:

- Use `Drupal.behaviors` and `once()` to prevent multiple attachments and support Ajax/BigPipe re-render.
- Scope DOM queries to the `context` passed to the behavior.
- Register built files in `ps.libraries.yml` with dependencies: `core/drupal`, `core/drupalSettings`, `core/once`.
- Avoid global `document` listeners; attach events to elements discovered in `context`.
- Dispatch custom events when helpful (e.g., `accordion:show|shown|hide|hidden`).

Example skeleton:

```js
/** @file Accordion behavior */
((Drupal, once) => {
  Drupal.behaviors.psAccordion = {
    attach(context) {
      once('ps-accordion', '[data-accordion]', context).forEach((root) => {
        root.querySelectorAll('[data-accordion-trigger]').forEach((trigger) => {
          trigger.addEventListener('click', () => this.toggleItem(root, trigger));
          trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              this.toggleItem(root, trigger);
            }
          });
        });
      });
    },
    toggleItem(root, trigger) {
      // ...
    },
  };
})(Drupal, once);
```

## JavaScript bundling standard

- Single Drupal bundle: `dist/js/scripts.js`.
- Aggregator: `source/patterns/scripts.js` eagerly imports all component behaviors via `import.meta.glob`. Any new `*.js` behavior placed under `source/patterns/**` is auto-included; no config changes needed.
- Vendors: All modules from `node_modules` are emitted into `dist/js/vendors/vendors.js` using Rollup `manualChunks`. If you need a separate vendor split, extend `manualChunks` in `vite.config.js`.

## Running the project

There are several custom npm commands that allows developers to build and run different
tasks during and after development. These commands can be found in `package.json`.
The most common ones to use include:

- `npm run build`: This is the command that builds all your local assets and builds your project. This should be the first command to be executed if you are building your project for the first time.

- `npm run watch`: This will run both `npm run vite:watch` and `npm run storybook:dev`. This is the most common command to run while working with Surface during development. Among the tasks the watch command runs, are:
  - Cleaning out the `dist` folder and compiling a fresh copy of all production code.
  - Linting (CSS and JS) files to ensure code standards are met.
  - Watching for changes to CSS, JS, and Images and compiling them if needed.
  - Recursively globbing through all CSS and JS files within the source directory.

> **NOTE**: Most tasks included in the watch command can be found in `vite.config.js`.

- `npm run storybook:build`: This command will build a local/static instance of Storybook for production in your theme's `/storybook` directory. The `npm run build` command should be executed prior to the `storybook:build` command to ensure all required assets are available before building Storybook.

## Design system

PS Theme uses [Storybook](https://storybook.js.org/) as its design system and that's where all components on the sites are originally built and maintained. Storybook can be accessed on its own by running `npm run watch` and navigating to `http://localhost:6006`.

## Atomic Design Methodology

Although Surface adheres to the Atomic Design methodology, it does not use the same naming conventions for naming its patterns. Our naming convention for the top level categories are:

- **Elements** - equivalent to Atoms
- **Components** - equivalent to Molecules
- **Collections** - equivalent to Organisms
- **Layouts** - equivalent to templates
- **Pages** - same

## Development approach

PS Theme is built using the latest development practices for CSS, JS, and Twig. Within Surface's Storybook, all components are built using BEM methodology for selector classes and ES6 for Javascript.

## Available components

For a simple demonstration of how to build components in Storybook and integrate them with Drupal, we are sharing a couple of components we use on our projects. These components are:

### Elements

- Breadcrum, Button, Date, Date badge, Eyebrow, Images, Readtime, Title

### Components

- Callout, Card, Featured card, Quote

### Layouts

- Block

### Theme

- ckeditor


## Demo of static instance of Storybook

[Static Surface theme built with Storybook](https://dev-ucla-surface-training.pantheonsite.io/themes/custom/surface/storybook/?path=/docs/getting-started-intro--docs)

## About Drupal Theming

For demo purposes, we have included Drupal template suggestions inside `templates/`, which also include examples of how a particular Drupal entity (i.e. content type) is integrated with a Storybook component.

For more information, see Drupal.org [theming guide](https://www.drupal.org/docs/develop/theming-drupal).

Upstream Surface was built with 🩵 by the good folks at [BNP Paribas](https://it.uclahealth.org/about/dgit/teams/web-development).

For a walkthrough on how this project was built, along with other related goodies, take a look a the [blog series](https://mariohernandez.io/series/storybook/) by Mario Hernandez.
