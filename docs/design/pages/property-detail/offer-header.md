# En-tête d’offre

Affiche le titre, la localisation, la surface, le prix, les badges et les actions rapides (photos, visite 3D, plan).

## Modèle de contenu
- Titre: `string`
- Référence: `string`
- Surface: `number` m²
- Localisation: `string`
- Prix: `string` (formaté)
- Disponibilité: `string`
- Type de mandat: `string`
- Badges: `array<string>` (ex: New, Exclusive)
- Actions rapides: compteur photos, lien visite 3D, lien plan

## UX
- Titre proéminent (H1). Métadonnées en petit texte.
- Badges alignés à droite près des actions.
- Prix aligné avec les actions; empilement responsive.

## Accessibilité
- Boutons/liens avec noms accessibles; compteurs annoncés.

## Tokens
- Espacement, typographie, couleurs sémantiques pour les badges.
