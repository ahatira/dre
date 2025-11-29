# PS Toggle

Interrupteur on/off accessible pour options binaires (activer/désactiver). Utilise un `<input type="checkbox">` stylisé avec `role="switch"`.

## Props

| Prop         | Type     | Default   | Description                                 |
|--------------|----------|-----------|---------------------------------------------|
| name         | string   |           | Nom du champ                                |
| label        | string   |           | Label externe                               |
| description  | string   |           | Description optionnelle                     |
| checked      | boolean  | false     | État activé                                 |
| disabled     | boolean  | false     | État désactivé                              |
| size         | string   | medium    | Taille: small, medium, large                |
| showLabels   | boolean  | false     | Affiche labels internes on/off              |
| onLabel      | string   | On        | Label interne ON                            |
| offLabel     | string   | Off       | Label interne OFF                           |

## BEM Structure

- `ps-toggle` (block)
- `ps-toggle__input` (input caché)
- `ps-toggle__track` (rail)
- `ps-toggle__thumb` (bouton mobile)
- `ps-toggle__label` (label externe)
- `ps-toggle__description` (description)
- Modifiers: `ps-toggle--small`, `ps-toggle--medium`, `ps-toggle--large`, `ps-toggle--disabled`
- Note: `medium` est la valeur par défaut et n'ajoute pas de classe (HTML minimal). Utiliser `--small` ou `--large` uniquement si différent du défaut.

## Tokens utilisés

- Couleurs: `--ps-color-neutral-100`, `--ps-color-neutral-300`, `--brand-primary`, `--white`, `--ps-color-neutral-900`, `--ps-color-neutral-600`, `--ps-color-interactive-focus-outline`
- Espacements: `--ps-spacing-1`, `--ps-spacing-2`
- Bordures: `--ps-border-radius-full`, `--ps-border-radius-lg`, `--ps-border-width-focus`
- Font: `--font-family-base`, `--font-size-1`, `--font-size-2`
- Transitions: `--ps-transition-duration-normal`, `--ps-transition-easing-default`
- Tailles: `--ps-toggle-width-small`, `--ps-toggle-width-medium`, `--ps-toggle-width-large`, `--ps-toggle-height-small`, `--ps-toggle-height-medium`, `--ps-toggle-height-large`, `--ps-toggle-thumb-small`, `--ps-toggle-thumb-medium`, `--ps-toggle-thumb-large`

## Exemples

```twig
{% include '@ps_theme/ps-toggle/ps-toggle.twig' with {
  name: 'notifications',
  label: 'Activer les notifications',
  checked: true,
  size: 'medium'
} %}
{% include '@ps_theme/ps-toggle/ps-toggle.twig' with {
  name: 'dark_mode',
  label: 'Mode sombre',
  checked: false,
  disabled: true,
  size: 'small'
} %}
{% include '@ps_theme/ps-toggle/ps-toggle.twig' with {
  name: 'auto_save',
  label: 'Enregistrement automatique',
  showLabels: true,
  onLabel: 'On',
  offLabel: 'Off',
  size: 'large'
} %}
```

## Cas d’usage réels

- Préférences utilisateur (notifications, newsletter)
- Paramètres de compte (profil public, mode sombre)

## Accessibilité

- Utilise `role="switch"` et `aria-checked`
- Focus visible via token
- Label externe ou interne obligatoire
- Compatible clavier et lecteur d’écran
