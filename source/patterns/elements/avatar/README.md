# PS Avatar

Composant atomique pour représenter un utilisateur ou une entité (image, initiales, icône fallback). Pixel perfect, BEM strict, tokens uniquement.

## Structure
- `avatar.twig` : Template principal, props commentées
- `avatar.css` : Styles BEM, tokens uniquement
- `avatar.yml` : Données par défaut pour preview
- `avatar.stories.jsx` : Stories Storybook (HTML/Twig)
- `README.md` : Documentation composant

## Props
- `src` (string) : URL image
- `alt` (string) : Texte alternatif
- `initials` (string) : Initiales fallback
- `size` (string) : xs|sm|md|lg|xl
- `shape` (string) : circle|square|rounded
- `status` (string) : online|offline|busy
- `bordered` (bool) : Bordure
- `clickable` (bool) : Cliquable
- `href` (string) : URL si cliquable

## BEM
- Block : `ps-avatar`
- Elements : `ps-avatar__image`, `ps-avatar__text`, `ps-avatar__icon`, `ps-avatar__status`
- Modifiers : tailles, formes, types, bordure, clickable, statut

## Tokens utilisés
- Tailles : --size-6, --size-8, --size-10, --size-14, --size-20
- Couleurs : --ps-color-primary-600, --ps-color-neutral-0, --ps-color-neutral-100, --ps-color-neutral-200, --ps-color-neutral-400, --ps-color-neutral-600, --ps-color-success-600, --ps-color-error-600
- Bordures : --ps-border-radius-full, --ps-border-radius-sm, --ps-border-width-default
- Transitions : --ps-transition-duration-fast

## Accessibilité
- alt sur image
- aria-label sur badge
- focus visible si clickable

## Variants
- Image, Initiales, Icône fallback, Statut, Bordure, Cliquable

## Storybook
- Variants : Default, Initials, IconFallback, WithStatus, Clickable

## Pixel perfect
Respect strict des dimensions, couleurs, typographie, espacements, états interactifs.
