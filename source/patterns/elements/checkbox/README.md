# PS Checkbox

Composant atomique case à cocher avec états. Pixel perfect, BEM strict, tokens uniquement.

## Structure
- `checkbox.twig` : Template principal, props commentées
- `checkbox.css` : Styles BEM, tokens uniquement
- `checkbox.yml` : Données par défaut pour preview
- `checkbox.stories.jsx` : Stories Storybook (HTML/Twig)
- `README.md` : Documentation composant

## Props
- `name` (string, required) : Input name attribute
- `value` (string, required) : Input value
- `label` (string, optional) : Label text
- `checked` (bool) : Checked state
- `disabled` (bool) : Disabled state
- `id` (string, optional) : Input ID (auto-generated)

## BEM
- Block : `ps-checkbox`
- Elements : `ps-checkbox__input`, `ps-checkbox__box`, `ps-checkbox__checkmark`, `ps-checkbox__label`
- Modifiers : `ps-checkbox--disabled`

## Specs
- **Taille** : 20px × 20px
- **Bordure** : 2px solid #D6DBDE (gris neutre)
- **Border-radius** : 2px
- **Checkmark** : Vert BNP #00915A
- **Gap label** : 8px
- **Font** : 14px, line-height 1.5

## États
- Default : Bordure grise, fond blanc
- Hover : Bordure verte
- Checked : Bordure verte, checkmark visible
- Focus : Outline bleu 2px
- Disabled : Opacité 50%, cursor not-allowed

## Accessibilité
- Label cliquable (wraps input)
- Focus visible
- Attributs ARIA (disabled)
- Input natif pour keyboard navigation

## Variants
- Sans label / Avec label
- Unchecked / Checked
- Enabled / Disabled

## Storybook
- Variants : Default, NoLabel, Checked, CheckedNoLabel, WithLongLabel, Disabled, DisabledChecked, Group

## Pixel perfect
Respect strict de la maquette 11-checkboxes.png : dimensions, couleurs, espacements, états interactifs.
