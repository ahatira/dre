# Tableau des surfaces

Liste tabulaire des unités : lot, étage, nature, surface, disponibilité.

## Modèle de contenu
- Lignes : `array<{lot:string, floor:string, nature:string, surface:number, availability:string}>`

## UX
- Colonnes triables ; repli responsive sur mobile.

## Accessibilité
- Sémantique `<table>` appropriée ; en-têtes, `scope` ; contrôles de tri focalisables.

## Tokens
- Espacement du tableau ; lignes zébrées via tokens neutres.
