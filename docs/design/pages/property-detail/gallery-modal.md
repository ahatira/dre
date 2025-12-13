# Modal Galerie

Visionneuse en lightbox avec vignettes et contrôles précédent/suivant.

## Modèle de contenu
- Images : `array<{src:string, alt:string, caption?:string}>`

## UX
- Navigation au clavier ; vignettes cliquables ; bouton de fermeture.

## Accessibilité
- `role="dialog"`, piège de focus, ESC pour fermer, libellés pour les contrôles.

## Tokens
- Tokens d’overlay ; espacements ; tailles d’icônes.
