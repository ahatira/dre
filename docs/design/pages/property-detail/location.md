# Section Localisation

Adresse, informations de transport, accès routiers + carte intégrée.

## Modèle de contenu
- Adresse : `string`
- Transports : `array<{type:string, label:string}>`
- Carte : `{lat:number, lng:number}`

## UX
- Carte d’adresse au-dessus de la carte ; carte en pleine largeur.

## Accessibilité
- La carte possède un fallback textuel ; les marqueurs ont des noms accessibles.

## Tokens
- Espacement de la carte ; tokens d’aspect pour le conteneur de carte.
