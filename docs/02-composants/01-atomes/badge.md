# Badge (Atom)

**Niveau Atomic Design** : Atom / Label  
**Catégorie** : Status indicator  
**Statut** : ✅ Stable  
**Version** : 1.1.0  
**Dernière mise à jour** : 3 décembre 2025

---

## 📋 Description

Indicateur visuel compact pour afficher des statuts, labels, ou compteurs. Disponible en 8 variantes de couleur sémantiques (default, primary, secondary, gold, info, success, warning, danger), 3 tailles (small/medium/large), et forme pill optionnelle. Supporte icônes décoratives et comportement link cliquable.

**Implémentation** : `source/patterns/elements/badge/`

---

## 🎨 Aperçu visuel

```
[ Badge ]  [ ✓ Verified ]  [ 3 ]  [ Learn more → ]
Default     Primary+icon    Small   Link pill
```

---

## 🏗️ Structure BEM

```html
<!-- Default badge -->
<span class="ps-badge">Default</span>

<!-- Primary badge with icon -->
<span class="ps-badge ps-badge--primary" data-icon="check">Verified</span>

<!-- Small count -->
<span class="ps-badge ps-badge--small">3</span>

<!-- Pill link badge with icon at end -->
<a href="#" class="ps-badge ps-badge--info ps-badge--pill" data-icon="arrow-right" data-icon-position="end">Learn more</a>
```

### Classes BEM

```
ps-badge                                  // Block (pas d'éléments enfants)

Modifiers (couleurs sémantiques):
  (default - pas de classe)               // Gris neutre - ÉTAT PAR DÉFAUT
  ps-badge--primary                       // Primaire (vert brand)
  ps-badge--secondary                     // Secondaire (violet brand)
  ps-badge--gold                          // Or/accent premium (--gold)
  ps-badge--info                          // Info (bleu clair)
  ps-badge--success                       // Succès (vert clair)
  ps-badge--warning                       // Avertissement (jaune)
  ps-badge--danger                        // Danger (rouge clair)
  
Modifiers (tailles):
  ps-badge--small                         // Petite taille (font 10px)
  (medium - pas de classe)                // Taille moyenne (font 12px) - DEFAULT
  ps-badge--large                         // Grande taille (font 14px)
  
Modifiers (forme):
  ps-badge--pill                          // Complètement arrondi (border-radius full)
```

---

## 📐 Props (Component API)

### Drupal Component YAML

```yaml
$schema: https://git.drupalcode.org/project/drupal/-/raw/10.1.x/core/modules/sdc/src/Component/schema.json
name: 'PS Badge'
status: stable
group: atoms
description: 'Indicateur compact pour statuts, dates, labels, ou compteurs.'

props:
  type: object
  properties:
    text:
      type: string
      title: Texte
    variant:
      type: string
      enum: ['primary','secondary','info','success','warning','danger','gold']
      description: 'Couleur sémantique. Omission = état par défaut (gris neutre)'
    type:
      type: string
      enum: ['date','status','label','count']
      default: 'label'
    size:
      type: string
      enum: ['small','medium','large']
      default: 'medium'
    shape:
      type: string
      enum: ['rounded','square','pill']
      default: 'rounded'
    icon:
      type: string
      description: 'Nom d'icône optionnel (sans préfixe icon-)'
    iconPosition:
      type: string
      enum: ['start','end']
      default: 'start'
      description: 'Position de l'icône (start = avant texte, end = après texte)'
    href:
      type: string
      description: 'URL si le badge est un lien (rend automatiquement cliquable)'
    attributes:
      type: Drupal\Core\Template\Attribute
  required:
    - text
```

---

## 🎭 Variants

- **Couleurs** : `primary`|`secondary`|`info`|`success`|`warning`|`danger`|`gold`. 
  - **Note** : Omission du variant = état par défaut (gris neutre, pas de classe `--neutral` nécessaire)
- **Types** : `date`|`status`|`label`|`count` (affecte l'icône par défaut et le style).
- **Tailles** : `small`|`medium`|`large`.
- **Formes** : `rounded` (coins arrondis), `square`, `pill` (complètement arrondi).
- **Icône** : Optionnelle via `data-icon`, position `start` (défaut) ou `end`.
- **Lien** : Si `href` fourni, badge devient cliquable avec hover/focus automatiques.

---

## 🎨 Design Tokens (réels)

- Typo : `--font-body`, `--font-size-0|1`, `--font-weight-600`, `--leading-tight`
- Couleurs (bg/text/border) en sémantique existante :
  - (État par défaut - sans classe) : `--gray-200`, `--gray-600`, `--border-default`
  - Primary : `--primary`, `--primary-hover`, `--primary-active`, `--primary-text`, `--primary-border`
  - Secondary : `--secondary`, `--secondary-hover`, `--secondary-active`, `--secondary-text`, `--secondary-border`
  - Info : `--info`, `--info-hover`, `--info-active`, `--info-text`, `--info-border`
  - Success : `--success`, `--success-hover`, `--success-active`, `--success-text`, `--success-border`
  - Warning : `--warning`, `--warning-hover`, `--warning-active`, `--warning-text`, `--warning-border`
  - Danger : `--danger`, `--danger-hover`, `--danger-active`, `--danger-text`, `--danger-border`
  - Gold : `--gold`, `--gold-hover`, `--gold-active`, `--gold-text`, `--gold-border`
- Rayon : `--radius-2` (4px), `--radius-3` (6px), `--radius-round` (pill)
- Bordure : `--border-size-1` (1px) ou `--border-size-2` (2px)
- Espacements : `--size-1|2|3` pour les paddings internes
- Ombre optionnelle : `--shadow-1` ou `--shadow-2`
- Transition : `--duration-fast` + `--ease-3`

---

## 🔧 Template Twig

```twig
{#
 * Template for Badge atom.
 * Variables: voir API YAML
 #}

{% set variant = variant|default(null) %}
{% set type = type|default('label') %}
{% set size = size|default('medium') %}
{% set shape = shape|default('rounded') %}
{% set iconPosition = iconPosition|default('start') %}

{% set icon_map = {
  'date': 'calendar',
  'status': 'check-circle',
  'count': null
} %}
{% set default_icon = icon ?? icon_map[type] %}

{% set root_classes = [
  'ps-badge',
  variant ? 'ps-badge--' ~ variant : null,
  'ps-badge--' ~ type,
  size != 'medium' ? 'ps-badge--' ~ size : null,
  shape != 'rounded' ? 'ps-badge--' ~ shape : null
] %}

{% set tag = href ? 'a' : 'span' %}

<{{ tag }} 
  {{ attributes.addClass(root_classes) }}
  {% if href %}href="{{ href }}"{% endif %}
  {% if default_icon %}data-icon="{{ default_icon }}"{% endif %}
  {% if default_icon and iconPosition != 'start' %}data-icon-position="{{ iconPosition }}"{% endif %}
>{{ text }}</{{ tag }}>
```

---

## 🎨 Styles SCSS

```scss
.ps-badge {
  /* Layer 2: composant (variables overridables) */
  --badge-bg: var(--gray-200);
  --badge-color: var(--gray-600);
  --badge-padding-y: var(--size-1);
  --badge-padding-x: var(--size-2);
  --badge-font-size: var(--font-size--1); // 12px
  --badge-font-weight: var(--font-weight-500);
  --badge-radius: var(--radius-2);
  --badge-icon-size: 1em;
  --badge-gap: var(--size-1);

  display: inline-flex;
  align-items: center;
  gap: var(--badge-gap);
  padding: var(--badge-padding-y) var(--badge-padding-x);
  font-family: var(--font-sans);
  font-size: var(--badge-font-size);
  font-weight: var(--badge-font-weight);
  line-height: 1.2;
  border-radius: var(--badge-radius);
  text-decoration: none;
  white-space: nowrap;
  background: var(--badge-bg);
  color: var(--badge-color);
  transition: background var(--duration-fast) var(--ease-4);

  // Icône via data-icon (géré par icons.css avec ::before par défaut)
  // Position end via data-icon-position="end" utilise ::after
  &[data-icon] {
    // Le CSS global icons.css gère le rendu avec ::before (start) ou ::after (end)
  }

  // Tailles
  &--small {
    --badge-font-size: var(--font-size--2);
    --badge-padding-y: var(--size-05);
    --badge-padding-x: var(--size-1);
  }

  &--large {
    --badge-font-size: var(--font-size-0);
    --badge-padding-y: var(--size-105);
    --badge-padding-x: var(--size-3);
  }

  // Formes
  &--pill { --badge-radius: var(--radius-round); }

  // Variantes sémantiques (état par défaut = sans classe, gris neutre défini dans variables de base)
  &--primary { --badge-bg: var(--primary-subtle); --badge-color: var(--primary-text-emphasis); }
  &--secondary { --badge-bg: var(--secondary-subtle); --badge-color: var(--secondary-text-emphasis); }
  &--info { --badge-bg: var(--info-subtle); --badge-color: var(--info-text-emphasis); }
  &--success { --badge-bg: var(--success-subtle); --badge-color: var(--success-text-emphasis); }
  &--warning { --badge-bg: var(--warning-subtle); --badge-color: var(--warning-text-emphasis); }
  &--danger { --badge-bg: var(--danger-subtle); --badge-color: var(--danger-text-emphasis); }
  &--gold { --badge-bg: var(--gold-subtle); --badge-color: var(--gold-text-emphasis); }

  // État cliquable (lien)
  &:is(a) {
    &:hover { filter: brightness(0.95); }
    &:focus-visible {
      outline: var(--border-size-2) solid var(--primary);
      outline-offset: var(--size-05);
    }
  }
}
```

---

## ♿ Accessibilité

- Texte lisible avec contraste suffisant (WCAG AA).
- Si cliquable : `<a>` avec `href` ou `<button>` avec `type="button"`.
- Si décoratif (compteur) : texte visible suffit; pas d'`aria-label` nécessaire sauf contexte ambigu.
- Focus visible pour variantes cliquables.

---

## 📱 Comportement responsive

**Mobile-first** : Badge utilise `inline-flex` qui s'adapte automatiquement au contenu parent.

- **Base (mobile)** : Padding et font-size en unités relatives (em) s'adaptent au contexte
- **400px+** : Aucun ajustement nécessaire
- **640px+** : Aucun ajustement nécessaire  
- **768px+** : Aucun ajustement nécessaire
- **1024px+** : Aucun ajustement nécessaire
- **1280px+** : Aucun ajustement nécessaire
- **1440px+** : Aucun ajustement nécessaire

**Flexibilité native** :
- Tailles (small/medium/large) définies via modificateurs, pas via breakpoints
- Badge count : `min-width` assure un cercle régulier sur tous devices
- Icônes : Taille relative (1em) s'adapte à la taille du badge

**Note** : Tous les breakpoints sont présents dans `badge.css` (même vides) pour cohérence avec la convention design system.

---

## 🧪 Exemples d'usage

```twig
{# Status badge #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: 'Actif',
  variant: 'success',
  type: 'status',
  size: 'medium'
} %}

{# Date badge - default state (no variant) #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: '15 Jan 2025',
  type: 'date'
} %}

{# Count badge #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: '3',
  variant: 'primary',
  type: 'count',
  size: 'small'
} %}

{# Clickable label with icon at end #}
{% include '@ps_theme/ps-badge/ps-badge.twig' with {
  text: 'Immobilier',
  variant: 'info',
  type: 'label',
  icon: 'arrow-right',
  iconPosition: 'end',
  href: '/category/immobilier'
} %}
```

---

## 📚 Ressources

- Figma: 27 occurrences détectées (date, status, label variants)
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/typography.yml`, `/design/tokens/borders.yml`
