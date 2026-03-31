# Homepage Implementation Plan (maquette BNPRE)

## 1) Objectif et principe directeur

Objectif: implementer la homepage de la maquette en gardant une architecture Drupal 11 maintenable.

Principes:
- Garder le template global de page generique pour toutes les pages.
- Isoler la homepage dans un rendu dedie frontpage.
- Eviter le hardcode de contenu dans Twig: structure dans Twig, contenu dans blocks/views/entities.
- Reutiliser au maximum les composants UI Suite existants (navbar, grid_row, card, accordion, carousel).
- Ajouter uniquement le minimum necessaire en hooks et JS.

## 2) Analyse rapide de l'existant (theme ui_suite_bnppre)

Constats verifies:
- Le theme actif custom est ui_suite_bnppre (l'ancien ps_theme est retire du repo).
- Le layout global est gere par templates/system/page.html.twig (header/nav/main/footer generiques).
- Les styles globaux sont portes par assets/scss/styles.scss (Realestate) et assets/scss/styles-ps.scss (Property Search).
- Le choix du CSS charge est pilote en settings via src/Hook/ThemeSettings.php et injecte via src/Hook/LibraryInfoAlter.php.
- Le preprocess page est minimal (container seulement) dans src/Hook/PreprocessPage.php.
- Les composants de base existent deja: navbar, card, accordion, grid_row, carousel, etc.
- Aucun template frontpage dedie n'est present actuellement.

Implication: la homepage de la maquette doit etre creee par une couche frontpage dediee + conventions de composants + variantes SCSS/JS.

## 3) Cible d'architecture homepage

### 3.1 Structure cible

- Creer un template frontpage dedie:
  - templates/system/page--front.html.twig
- Garder templates/system/page.html.twig intact pour les autres pages.
- Assembler la homepage en sections semantiques:
  - Hero + moteur de recherche
  - Bloc promesse/services
  - Section "best way" (texte + accordion + visuel)
  - Carrousel d'annonces
  - Bande de recherches commerciales
  - Section experts
  - News cards
  - Etudes de marche
  - FAQ accordion
  - Footer enrichi

### 3.2 Strategie de contenu Drupal

Ne pas coder les textes/images en dur dans Twig.

Source de donnees recommandee:
- Hero et sections editoriales: custom block content (ou paragraphs via block)
- Listings immobiliers: Views display dedie + unformatted/list style adapte
- News: Views (3 cartes)
- FAQ: contenu type paragraph accordions ou view dediee
- Footer links: menus Drupal + blocks

## 4) Plan de modifications par couche

## 4.1 Templates Twig

Fichiers a creer/modifier:
- Creer templates/system/page--front.html.twig
- Conserver templates/system/page.html.twig comme fallback generique
- Ajouter des templates de blocks/views cibles quand necessaire:
  - templates/block/block--ID_MACHINE.html.twig
  - templates/views/views-view--NOMVIEW--DISPLAY.html.twig
  - templates/views/views-view-unformatted--NOMVIEW--DISPLAY.html.twig

Pattern recommande dans page--front.html.twig:
- Wrapper principal avec classes homepage dediees.
- Include de composants existants pour eviter duplication markup.
- Regions/blocks rendus via placeholders structurels (pas de texte hardcode).
- Landmarks a11y: header, nav, main, section[aria-labelledby], footer.

Livrable Twig attendu:
- Un squelette frontpage lisible, sectionne, stable, et independant des pages internes.

## 4.2 Components

Strategie:
- Reutiliser les composants existants pour 70-80% du markup.
- Creer des composants "metier homepage" uniquement pour les blocs repetitifs specifiques.

Composants candidats a creer (si necessaire):
- components/homepage_hero/homepage_hero.twig + .component.yml
- components/homepage_service_card/homepage_service_card.twig + .component.yml
- components/homepage_listing_card/homepage_listing_card.twig + .component.yml
- components/homepage_news_card/homepage_news_card.twig + .component.yml
- components/homepage_study_card/homepage_study_card.twig + .component.yml
- components/homepage_faq/homepage_faq.twig + .component.yml

Regle de decision:
- Si la variation peut etre geree par classes/props des composants existants: ne pas creer de nouveau composant.
- Creer un composant seulement si repetition de structure + besoin d'API de props claire.

## 4.3 SCSS

Objectif SCSS:
- Introduire un scope homepage clair sans impacter le reste du site.

Plan SCSS:
- Ajouter un partiel dedie homepage:
  - assets/scss/components/_homepage.scss
- L'importer dans:
  - assets/scss/styles.scss
  - assets/scss/styles-ps.scss (si la homepage existe aussi en variante PS)

Convention de nommage conseillee:
- .hp-hero
- .hp-search-panel
- .hp-service-grid
- .hp-listings-carousel
- .hp-news-grid
- .hp-faq

Points UX a couvrir:
- Desktop + mobile (breakpoints Bootstrap)
- Hierarchie typo conforme tokens BNPPRE
- Etats hover/focus visibles
- Contrastes sur overlays hero
- Espacements verticaux constants section-to-section

Important:
- Ne jamais modifier assets/css/*.css directement (fichiers generes).

## 4.4 JavaScript

Objectif JS:
- Garder un JS faible, oriente comportements reels de maquette.

Besoins probables:
- Header mobile/offcanvas (ouverture/fermeture + etat focus)
- Eventuel carrousel custom si non couvert 100% par Bootstrap
- Eventuel comportement tabs/filters du moteur de recherche

Plan:
- Creer assets/js/homepage/homepage.js
- Declarer une librairie dediee dans ui_suite_bnppre.libraries.yml:
  - homepage
- Attacher la librairie uniquement sur frontpage (via preprocess page).

Regles:
- Utiliser Drupal behaviors + once
- Aucune logique metier lourde en JS
- Priorite accessibilite clavier et aria-state

## 4.5 Hooks PHP (Drupal 11 OOP hooks)

Hooks a prevoir:
- src/Hook/PreprocessPage.php
  - Ajouter variable is_homepage_layout
  - Attacher library homepage seulement sur frontpage
  - Injecter classes utilitaires de page (ex: hp-page)

- src/Hook/ThemeSuggestionsAlter.php (optionnel)
  - Ajouter suggestions specifiques bloc/view uniquement si necessaire

- src/Hook/Views.php (si forms exposes de recherche homepage)
  - Ajuster classes des formulaires exposes specifiques au display homepage

Contrainte DI:
- Rester en injection de dependances, pas de nouveau service locator dans logique metier.

## 4.6 Configuration Drupal / contenu

Chantiers config associes (hors code theme strict):
- Definir les blocks de chaque section homepage et leur ordre.
- Creer/adapter les Views:
  - Listings
  - News
  - Etudes
  - FAQ (si basee sur contenu structure)
- Verifier menus header/footer (desktop + mobile)
- Verifier langues et labels CTA

## 5) Decoupage en phases (execution)

Phase 0 - Cadrage technique
- Valider si homepage est 100% block-driven ou mix page--front + views.
- Nommer les sections et identifiers machine (blocks/views).

Phase 1 - Squelette frontpage
- Creer page--front.html.twig
- Integrer structure semantique et wrappers sections
- Brancher contenu existant minimal

Phase 2 - Composants et variantes Twig
- Introduire templates block/views cibles
- Creer composants homepage seulement si repetition

Phase 3 - Styling
- Ajouter _homepage.scss
- Ajuster responsive desktop/mobile
- Aligner tokens BNPPRE et densite visuelle maquette

Phase 4 - Behaviors JS
- Ajouter homepage.js (si necessaire)
- Attacher librairie uniquement frontpage

Phase 5 - Validation
- Build theme et checks
- Test visuel multi-breakpoints
- Controle a11y de base (focus, contraste, navigation clavier)

## 6) Validation et commandes

Depuis web/themes/custom/ui_suite_bnppre:
- npm run build:css
- npm run build:theme-yaml:check
- npm run build:bnppre-icons:check (si icones touchees)

Depuis racine projet:
- vendor/bin/drush cr

## 7) Risques et garde-fous

Risques:
- Couplage fort du markup homepage avec contenu editorial mouvant.
- Regressions globales si styles non scopes.
- Duplication Twig si trop de templates block/view derives sans convention.

Mitigations:
- Scope CSS strict sous classe racine homepage.
- Regles de nommage block/view explicites.
- Reuse composants UI Suite avant creation de nouveaux composants.
- Introduire une checklist de QA frontpage avant merge.

## 8) Definition of Done

La homepage est consideree implementee quand:
- Le rendu frontpage suit la maquette desktop et mobile.
- Les autres pages restent inchangees visuellement.
- Les contenus sont pilotables via Drupal (pas hardcodes en Twig).
- Les checks build passent et les caches sont regeneres sans erreur.
- Les interactions critiques (menu mobile, CTA, accordion, carrousel, FAQ) fonctionnent clavier + souris.
