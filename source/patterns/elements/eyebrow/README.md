# Eyebrow (Atom)

## Description

Texte court contextuel placé au-dessus d'un titre principal pour fournir un contexte ou une catégorie (ex: "Actualités", "Nouveauté", "Étude de cas"). Généralement en petite taille, majuscules, et couleur secondaire. Peut inclure une icône ou un séparateur visuel (trait, point).

## Props

| Name       | Type    | Default   | Description                                 |
|------------|---------|-----------|---------------------------------------------|
| text       | string  | ''        | Texte affiché (obligatoire)                 |
| variant    | string  | neutral   | Couleur: primary, secondary, accent, neutral|
| size       | string  | medium    | Taille: small, medium                       |
| uppercase  | boolean | true      | Majuscules                                  |
| bold       | boolean | false     | Gras                                        |
| withLine   | boolean | false     | Ligne décorative                            |
| withDot    | boolean | false     | Point décoratif                             |
| icon       | string  | ''        | Nom d'icône optionnel                       |
| attributes | object  |           | Attributs HTML supplémentaires              |

## BEM Structure

- ps-eyebrow
- ps-eyebrow__icon
- ps-eyebrow__text
- ps-eyebrow__line
- Modifiers: --primary, --secondary, --accent, --neutral, --uppercase, --bold, --with-line, --with-dot, --small, --medium

## Design Tokens Utilisés

- Couleurs: --ps-color-primary-600, --ps-color-neutral-600, --ps-color-info-600, --ps-color-neutral-500
- Couleurs: --ps-color-primary-600, --ps-color-neutral-600, --ps-color-info-600, --ps-color-neutral-500
- Espacement: --ps-spacing-1, --ps-spacing-2

## Exemples

```twigples
```twig
{# Simple #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with { text: 'Nouveauté', variant: 'primary', uppercase: true } %}
{# Avec icône #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with { text: 'Étude de cas', variant: 'accent', icon: 'document', bold: true } %}
{# Ligne décorative #}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with { text: 'Actualités', variant: 'neutral', withLine: true, size: 'small' } %}
{% include '@ps_theme/ps-eyebrow/ps-eyebrow.twig' with { text: 'Blog', variant: 'secondary', withDot: true } %}
```

## Cas d'usage réels

- Hero de page réels
- Hero de page
- Section "Nouveauté"

## Accessibilité

- Utilisation de `span` ou `div` (pas de heading)
- Décorations aria-hidden
- Contraste texte WCAG AA
- Ordre DOM avant le titre principal


- Contraste texte WCAG AA
- Ordre DOM avant le titre principal
