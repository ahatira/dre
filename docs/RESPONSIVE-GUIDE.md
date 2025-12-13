# Guide Responsive (Projet entier)

Ce guide établit un cadre commun pour documenter et implémenter les variantes responsive (desktop/tablette/mobile) sur l’ensemble du projet, selon la méthodologie Atomic Design et le workflow Token-First.

## Principes clés
- Une **seule spec par page/collection** avec sections par breakpoint (Desktop/Mobile, Tablette si pertinent).
- Les **molecules/organisms** décrivent leurs comportements communs et listent leurs **variantes** (ex: panel vs drawer) sans dupliquer la spec.
- Les **tokens** pilotent la mise en page: tailles, espacements, z-index, aspects, médias.
- Les **interactions** (menus, modales, drawers) gardent les mêmes règles d’accessibilité: focus trap, `aria-*`, ESC, labels.

## Structure standard de spec
- Vue d’ensemble (Responsive)
- Données d’entrée communes
- UX par breakpoint
  - Desktop
  - Mobile (et Tablette si nécessaire)
- Accessibilité (commune + spécifique si variante)
- Tokens
- États & interactions
- Liens vers sous-spécifications (Desktop/Mobile)

## Nommage et navigation
- Pages: `design/pages/<page>/index.md` contient la vue **Responsive**.
- Variantes Desktop: fichiers dans le dossier de la page (ex: `filters-bar.md`).
- Variantes Mobile: fichiers dans `design/pages/<page>-mobile/` (ex: `comparator-drawer.md`).
- `docs/INDEX.md` regroupe sous **(Responsive)** avec sous-sections Desktop/Mobile.

## Checklists d’accessibilité (à réutiliser)
- Modales/Drawers: `role="dialog"`, `aria-modal="true"`, focus trap, ESC, titres, labels.
- Menus/Dropdowns: `aria-haspopup`, `aria-expanded`, navigation clavier, item actif annoncé.
- Cartes: fallback textuel, noms accessibles pour pins/contrôles.
- Listes de cartes: sémantique liste, ordre de tabulation, icônes avec `aria-hidden` + libellés.
- Boutons: focus-visible, contrastes, états hover/active.

## Tokens recommandés
- Layout: `--sizes`, `--aspects`, `--zindex`, `--media`.
- Couleurs sémantiques: `--primary`, `--info`, `--neutral`, etc.
- Overlays & focus: `--overlay-*`, `--border-focus`, `--focus-ring`.
- Espacements: `--size-*` (marge/padding/gaps).

## Application progressive
1. Migrer chaque page vers une spec **Responsive** (ex: Offre, Recherche).
2. Réorganiser les sous-specs en Desktop/Mobile sans duplication.
3. Mettre à jour `docs/INDEX.md` et `docs/README.md` pour refléter l’unification.
4. Pendant l’implémentation, appliquer Token-First et les règles d’accessibilité communes.

## Références
- Voir `docs/design/pages/search-results/index.md` (Responsive) comme exemple.
- Voir `.github/instructions/02-component-development.md` et `03-technical-implementation.md` pour standards.
