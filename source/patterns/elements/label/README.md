
# PS Label

## Description

Libellé de champ de formulaire accessible, associant une étiquette à un champ via l’attribut `for` et gérant l’indication de champ requis.

## Props

| Prop        | Type    | Default   | Description                                 |
|-------------|---------|-----------|---------------------------------------------|
| text        | string  | (requis)  | Intitulé du label                           |
| forId       | string  |           | Attribut for liant le label au champ ciblé  |
| required    | boolean | false     | Affiche l’astérisque et le texte caché      |
| disabled    | boolean | false     | Style atténué, non interactif               |
| attributes  | object  | {}        | Attributs HTML additionnels                 |

## BEM Structure

- `.ps-label` : Block principal
- `.ps-label__text` : Texte du label
- `.ps-label__required` : Indicateur requis
- Modifiers :
  - `.ps-label--required` : Champ requis
  - `.ps-label--disabled` : Champ désactivé

## Design Tokens Utilisés

- `--ps-color-text` : Couleur principale du texte
- `--ps-color-text-muted` : Couleur texte désactivé
- `--ps-color-error-600` : Couleur indicateur requis
- `--ps-font-family-primary` : Police principale
- `--ps-font-size-sm` : Taille du texte
- `--ps-font-weight-medium` : Poids du texte
- `--ps-font-weight-bold` : Poids indicateur requis
- `--ps-spacing-1` : Espacement horizontal
- `--ps-spacing-2` : Espacement vertical

## Exemples d’utilisation

```twig
{% include '@ps_theme/ps-label/ps-label.twig' with { text: 'Email', forId: 'edit-email', required: true } %}
{% include '@ps_theme/ps-label/ps-label.twig' with { text: 'Téléphone', forId: 'edit-phone', disabled: true } %}
```

## Cas d’usage réels

- Formulaires de contact
- Inscription utilisateur
- Saisie d’informations personnelles

## Accessibilité

- Attribut `for` requis et doit correspondre à l’id du champ
- Indication `(champ obligatoire)` annoncée par les lecteurs d’écran
- Ne pas se reposer uniquement sur la couleur pour indiquer l’état

## Notes

- Modifiers CSS fonctionnent indépendamment sur la base class
- HTML minimal : seule la classe de base par défaut
- Les tokens sont strictement utilisés, aucune valeur en dur

## Liens

- [Spécification complète](../../../docs/design/atoms/label.md)
- [Design tokens](../../../source/props/)
- [Exemples Drupal](../../../templates/block/)
