# Toggle (Atom)

**Niveau Atomic Design** : Atom / Form control  
**Catégorie** : Input  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Interrupteur on/off pour des options binaires (activer/désactiver). Utilise un `<input type="checkbox">` avec apparence personnalisée et `role="switch"`. Supporte les états checked, disabled, focus, et tailles variées. Peut inclure des labels on/off internes ou externes.

---

## 🎨 Aperçu visuel

```
Off: [○     ]
On:  [     ○]
```

---

## 🏗️ Structure BEM

```html
<label class="ps-toggle ps-toggle--medium">
  <input class="ps-toggle__input" type="checkbox" role="switch" aria-checked="false" name="notifications" />
  <span class="ps-toggle__track">
    <span class="ps-toggle__thumb"></span>
  </span>
  <span class="ps-toggle__label">Activer les notifications</span>
</label>
```

### Classes BEM

```
ps-toggle                                 // Block (label wrapper)
  ps-toggle__input                        // Input checkbox natif (visually hidden)
  ps-toggle__track                        // Piste (fond)
  ps-toggle__thumb                        // Bouton mobile (cercle)
  ps-toggle__label                        // Label optionnel

Modificateurs :
  ps-toggle--small                        // Petite taille
  ps-toggle--medium                       // Taille moyenne (défaut)
  ps-toggle--large                        // Grande taille
  ps-toggle--disabled                     // État désactivé
  is-checked                              // État coché (via JS ou CSS :checked)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Toggle'
status: stable
group: atoms
description: 'Interrupteur on/off accessible avec role=switch et états visuels.'

props:
  type: object
  properties:
    name:
      type: string
      title: Nom du champ
    label:
      type: string
      title: Label
    checked:
      type: boolean
      default: false
    disabled:
      type: boolean
      default: false
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
    showLabels:
      type: boolean
      default: false
      description: 'Afficher "On"/"Off" internes'
    onLabel:
      type: string
      default: 'On'
    offLabel:
      type: string
      default: 'Off'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - name
```

---

## 🎭 Variants

- `size`: `small`|`medium`|`large`.
- `checked`: état initial on/off.
- `disabled`: non-interactif.
- `showLabels`: afficher textes "On"/"Off" intégrés au track.

---

## 🎨 Design Tokens (réels)

- Couleurs :
  - Track off : `--gray-300`
  - Track on : `--primary`
  - Thumb : `--white`
  - Disabled : `--gray-200` (track), thumb `--gray-300`
- Bordures / rayon : thumb arrondi `--radius-round`, track `--radius-3`
- Tailles suggérées : track (sm|md|lg) = 36|44|52px → `--size-9|11|13`; thumb 18|22|24px → `--size-5|6|7`
- Espacements : padding interne du track `--size-1`, gap label `--size-2`
- Focus : outline via `--border-focus` et offset `--border-size-1`
- Transition : `--duration-normal` + `--ease-3`

---

## 🔧 Template Twig

```twig
{#
 * Template for Toggle atom.
 * Variables: voir API YAML
 #}

{% set size = size|default('medium') %}
{% set checked = checked|default(false) %}
{% set disabled = disabled|default(false) %}
{% set showLabels = showLabels|default(false) %}
{% set onLabel = onLabel|default('On') %}
{% set offLabel = offLabel|default('Off') %}

{% set root_classes = [
  'ps-toggle',
  'ps-toggle--' ~ size,
  disabled ? 'ps-toggle--disabled'
] %}

<label {{ attributes.addClass(root_classes) }}>
  <input
    class="ps-toggle__input"
    type="checkbox"
    role="switch"
    aria-checked="{{ checked ? 'true' : 'false' }}"
    name="{{ name }}"
    {% if checked %}checked{% endif %}
    {% if disabled %}disabled{% endif %}
  />
  <span class="ps-toggle__track">
    {% if showLabels %}
      <span class="ps-toggle__on-label">{{ onLabel }}</span>
      <span class="ps-toggle__off-label">{{ offLabel }}</span>
    {% endif %}
    <span class="ps-toggle__thumb"></span>
  </span>
  {% if label %}
    <span class="ps-toggle__label">{{ label }}</span>
  {% endif %}
</label>
```

---

## 🎨 Styles SCSS

```scss
.ps-toggle {
  display: inline-flex; align-items: center; gap: var(--size-2);
  cursor: pointer;

  &--disabled { cursor: not-allowed; opacity: 0.6; }

  &__input {
    position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
    overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0;
  }

  &__track {
    position: relative; display: inline-flex; align-items: center;
    width: 44px; height: 24px;
    background: var(--gray-300);
    border-radius: var(--radius-3);
    transition: background var(--duration-normal) var(--ease-3);
    flex-shrink: 0;
  }

  &__thumb {
    position: absolute; left: 2px; top: 50%; transform: translateY(-50%);
    width: 20px; height: 20px;
    background: var(--white);
    border-radius: var(--radius-round);
    box-shadow: var(--shadow-2);
    transition: left var(--duration-normal) var(--ease-3);
  }

  &__label {
    font-family: var(--font-body);
    font-size: var(--font-size-1);
    color: var(--text-primary);
  }

  // Checked state
  &__input:checked + &__track {
    background: var(--primary);
    .ps-toggle__thumb { left: calc(100% - 22px); }
  }

  // Focus state
  &__input:focus-visible + &__track {
    outline: var(--border-size-2) solid var(--border-focus);
    outline-offset: var(--border-size-1);
  }

  // Disabled state
  &__input:disabled + &__track {
    background: var(--gray-200);
    .ps-toggle__thumb { background: var(--gray-300); }
  }

  // Sizes
  &--small {
    .ps-toggle__track { width: 36px; height: 20px; }
    .ps-toggle__thumb { width: 16px; height: 16px; }
    .ps-toggle__input:checked + .ps-toggle__track .ps-toggle__thumb { left: calc(100% - 18px); }
  }
  &--medium {
    .ps-toggle__track { width: 44px; height: 24px; }
    .ps-toggle__thumb { width: 20px; height: 20px; }
  }
  &--large {
    .ps-toggle__track { width: 52px; height: 28px; }
    .ps-toggle__thumb { width: 24px; height: 24px; }
    .ps-toggle__input:checked + .ps-toggle__track .ps-toggle__thumb { left: calc(100% - 26px); }
  }

  // Optional internal labels
  &__on-label, &__off-label {
    position: absolute;
    font-size: var(--font-size--2);
    font-weight: var(--font-weight-600);
    color: var(--white);
    text-transform: uppercase;
    opacity: 0;
    transition: opacity var(--duration-fast) var(--ease-3);
  }
  &__on-label { left: 6px; }
  &__off-label { right: 6px; }
  &__input:checked + &__track .ps-toggle__on-label { opacity: 1; }
  &__input:not(:checked) + &__track .ps-toggle__off-label { opacity: 1; }
}
```

---

## ♿ Accessibilité

- `role="switch"` sur l'input pour indiquer un interrupteur.
- `aria-checked` synchronisé avec l'état coché.
- Label externe via `<label>` ou associé avec `for`/`id`.
- Focus visible sur la piste.
- Clavier : Space pour basculer.

---

## 📱 Comportement responsive

- Inline-flex : s'adapte au conteneur parent.
- Taille ajustable via modificateurs.

---

## 🧪 Exemples d'usage

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

---

## 📚 Ressources

- WAI-ARIA: `role="switch"`, `aria-checked`
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/borders.yml`, `/design/tokens/transitions.yml`
