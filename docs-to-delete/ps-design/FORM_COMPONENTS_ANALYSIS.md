# Analyse Approfondie - Composants de Formulaire PS Theme

**Date**: 2025-12-07  
**Objectif**: Harmoniser l'architecture des composants de formulaire pour faciliter l'intégration Drupal 11  
**Scope**: Checkbox, Radio, Input, Select, Textarea, Label, Field, Form-Element, Dropdown, Search-Bar

---

## 🔍 État Actuel - Incohérences Identifiées

### 1. **PROBLÈME MAJEUR : Doublon Field vs Input/Select/Textarea**

#### Situation actuelle (INCOHÉRENTE)

**Atomes existants** :
- `input` (Atom) - Input natif basique sans label
- `select` (Atom) - Select natif basique sans label  
- `textarea` (Atom) - Textarea natif basique sans label
- `field` (Atom) - **Gère text/email/number/select/textarea + icons + error inline**

**Problème** :
- ❌ `field` fait **doublon** avec `input`, `select`, `textarea`
- ❌ `field` est trop complexe pour un atome (gère types multiples, icônes, erreurs)
- ❌ Architecture confuse : 2 façons de faire la même chose
- ❌ Non conforme à Atomic Design : un atome = un élément de base

**Drupal 11 attend** :
```twig
{# Drupal rend séparément : #}
<label>{{ label }}</label>
<input type="text" /> {# OU select, textarea #}
<div class="error">{{ error }}</div>
```

### 2. **Architecture Actuelle vs Drupal 11**

| Composant | Niveau Actuel | Devrait Être | Problème |
|-----------|---------------|--------------|----------|
| **Checkbox** | Atom ✅ | Atom | Label intégré ⚠️ (devrait être séparable) |
| **Radio** | Atom ✅ | Atom | Label intégré ⚠️ (devrait être séparable) |
| **Input** | Atom ✅ | Atom | OK mais redondant avec Field |
| **Select** | Atom ✅ | Atom | OK mais redondant avec Field |
| **Textarea** | Atom ✅ | Atom | OK mais redondant avec Field |
| **Label** | Atom ✅ | Atom | ✅ Parfait (séparé, réutilisable) |
| **Field** | Atom ❌ | **À SUPPRIMER** | Doublon + trop complexe |
| **Form-Element** | Molecule ✅ | Molecule | ✅ Utilise Field (à migrer vers Input/Select/Textarea) |
| **Dropdown** | Molecule ✅ | Molecule | ✅ Custom stylé (use case spécifique) |
| **Search-Bar** | Molecule ⚠️ | Molecule | Manque README.md, tokens non conformes |

### 3. **Gestion des Couleurs - INEXISTANTE**

**Couleurs attendues** (standard Bootstrap/Drupal) :
- Default (text color)
- Primary
- Secondary  
- Info
- Warning
- Success ✅ (partiellement dans Field via `done`)
- Danger ✅ (partiellement dans Field via `error`)
- Dark
- Light

**État actuel** :
- ❌ Aucun modificateur de couleur sur Input/Select/Textarea
- ❌ Field a uniquement `--error` (danger) et `--done` (success)
- ❌ Pas de support pour info/warning/primary variants
- ❌ Checkbox/Radio utilisent uniquement `--primary` (hardcodé)

### 4. **Gestion des Tailles - INEXISTANTE**

**Tailles attendues** (standard Bootstrap) :
- xs (extra small)
- sm (small)
- md (medium) - default
- lg (large)
- xl (extra large)
- xxl (extra extra large)

**État actuel** :
- ❌ Input/Select/Textarea : taille fixe unique
- ✅ Dropdown : `--small`, `--large` (mais pas de md/xl/xxl)
- ❌ Checkbox/Radio : taille fixe unique
- ❌ Pas de système cohérent

### 5. **Système d'Icônes - PARTIEL**

| Composant | Icônes | Problème |
|-----------|--------|----------|
| Checkbox | ✅ SVG sprite (`icon-check`) | OK - Migration effectuée |
| Radio | ⚠️ CSS mask + URL hardcodée | À migrer vers sprite SVG |
| Field | ⚠️ `data-icon` attribute | Méthode non standard |
| Dropdown | ⚠️ `data-icon="chevron-down"` | Méthode non standard |
| Search-Bar | ❌ SVG inline | À migrer vers sprite |

### 6. **Variables CSS à 3 Couches - INCOHÉRENT**

**Conformité actuelle** :

| Composant | Layer 2 Variables | Layer 3 Modifiers | Tokens Only |
|-----------|-------------------|-------------------|-------------|
| Checkbox | ✅ Complet | ⚠️ Couleurs seules | ✅ |
| Radio | ✅ Complet | ⚠️ Couleurs seules | ✅ |
| Input | ✅ Complet | ⚠️ Couleurs seules | ✅ |
| Select | ✅ Complet | ⚠️ Couleurs seules | ✅ |
| Textarea | ✅ Complet | ⚠️ Couleurs seules | ✅ |
| Label | ✅ Complet | ✅ | ✅ |
| Field | ✅ Complet | ⚠️ Partiel | ✅ |
| Form-Element | ✅ Complet | ✅ | ✅ |
| Dropdown | ⚠️ Tokens mélangés | ⚠️ Tailles seules | ❌ |
| Search-Bar | ❌ Tokens non-standards | ❌ Aucun | ❌ |

**Tokens non-standards détectés** :
```css
/* Search-Bar - À CORRIGER */
--border-1 (devrait être --border-size-1)
--radius-md (devrait être --radius-3)
--duration-short (devrait être --duration-fast)
--ease-out-quad (devrait être --ease-3)
--primary-200 (n'existe pas, utiliser color-mix)
--layer-dropdown (devrait être --layer-40)

/* Dropdown - À CORRIGER */
--ps-dropdown-min-width-medium (créer si besoin)
--ps-icon-size-16, --ps-icon-size-20 (devrait être --size-4, --size-5)
--ps-font-size-sm (devrait être --font-size-0 ou -1)
--ps-color-text (devrait être --text-primary)
--ps-color-text-muted (devrait être --text-secondary)
```

### 7. **BEM Structure - INCONSISTANT**

#### Checkbox/Radio (label intégré)
```html
<!-- Actuel -->
<div class="ps-checkbox">
  <input class="ps-checkbox__input" />
  <span class="ps-checkbox__box"></span>
  <label class="ps-checkbox__label"></label>
</div>

<!-- Drupal 11 préfère séparé -->
<label class="ps-label" for="id">Label</label>
<div class="ps-checkbox">
  <input class="ps-checkbox__input" id="id" />
  <span class="ps-checkbox__box"></span>
</div>
```

#### Input/Select/Textarea (wrappers inutiles)
```html
<!-- Actuel (wrapper redondant) -->
<div class="ps-input__wrapper">
  <input class="ps-input" />
</div>

<!-- Devrait être (Drupal applique classes directement) -->
<input class="ps-input" />
```

### 8. **Accessibilité - PARTIELLE**

| Composant | ARIA | Keyboard | Focus-visible | Contrast |
|-----------|------|----------|---------------|----------|
| Checkbox | ✅ | ✅ | ✅ | ✅ |
| Radio | ✅ | ✅ | ⚠️ Focus sur label | ✅ |
| Input | ⚠️ Minimal | ✅ | ✅ | ✅ |
| Select | ⚠️ Minimal | ✅ | ✅ | ✅ |
| Textarea | ⚠️ Minimal | ✅ | ✅ | ✅ |
| Label | ✅ | N/A | N/A | ✅ |
| Field | ✅ Complet | ✅ | ✅ | ✅ |
| Form-Element | ✅ Complet | ✅ | ✅ | ✅ |
| Dropdown | ✅ Complet | ✅ | ✅ | ✅ |
| Search-Bar | ⚠️ Basique | ✅ | ⚠️ | ⚠️ |

---

## 📋 Architecture Drupal 11 - Référence

### Structure Attendue

```
ATOMES (Éléments de base natifs)
├── input (type: text, email, password, number, tel, url, search)
├── select (options[])
├── textarea
├── checkbox (avec/sans label séparé)
├── radio (avec/sans label séparé)
└── label (séparé, réutilisable)

MOLÉCULES (Compositions)
├── form-element (label + input/select/textarea + helper + error)
├── checkbox-group (label + multiple checkboxes)
├── radio-group (label + multiple radios)
└── dropdown (custom select stylé)

ORGANISMES (Formulaires complets)
└── form (contact, search, filter, login, etc.)
```

### Propriétés Drupal Standards

**Tous les champs doivent accepter** :
```yaml
# Core Drupal props
attributes: {}          # Drupal Attribute object
name: ''               # Form field name
id: ''                 # HTML ID
value: ''              # Current value
placeholder: ''        # Placeholder text
disabled: false        # Disabled state
required: false        # Required state
readonly: false        # Readonly state (à ajouter)

# Validation/State
error: ''              # Error message
description: ''        # Helper text (devrait être "helper")

# Variants (à ajouter)
color: 'default'       # default|primary|secondary|info|warning|success|danger|dark|light
size: 'md'             # xs|sm|md|lg|xl|xxl
```

---

## 🎯 Plan de Correction - Composant par Composant

### PHASE 1 : Refonte Architecture (Priorité CRITIQUE)

#### 1.1 **SUPPRIMER Field Atom**

**Action** : Supprimer complètement le composant `field`

**Raison** :
- Doublon avec Input/Select/Textarea
- Trop complexe pour un atome
- Confusion architecturale

**Migration** :
- `form-element` doit utiliser directement `input`, `select`, `textarea`
- Les icônes doivent être gérées via un wrapper externe (molecule)

**Fichiers à supprimer** :
```
source/patterns/elements/field/
├── field.twig
├── field.css
├── field.yml
├── field.stories.jsx
└── README.md
```

**Impact** :
- ✅ Form-Element à mettre à jour (remplacer includes)
- ✅ Autres composants à vérifier (search-bar si dépendance)

#### 1.2 **Standardiser Input/Select/Textarea**

**Objectif** : Atomes purs compatibles Drupal 11

**Input** :
```yaml
# Nouvelles props
color: 'default'    # default|primary|secondary|info|warning|success|danger|dark|light
size: 'md'          # xs|sm|md|lg|xl|xxl
readonly: false     # Readonly state
```

**CSS à ajouter** :
```css
/* Modificateurs de couleur */
.ps-input--primary { --ps-input-border-color: var(--primary); }
.ps-input--danger { --ps-input-border-color: var(--danger); }
/* etc. */

/* Modificateurs de taille */
.ps-input--xs { --ps-input-min-height: var(--size-7); --ps-input-padding-x: var(--size-2); }
.ps-input--sm { --ps-input-min-height: var(--size-8); --ps-input-padding-x: var(--size-3); }
.ps-input--md { --ps-input-min-height: var(--size-10); } /* default */
.ps-input--lg { --ps-input-min-height: var(--size-12); --ps-input-padding-x: var(--size-5); }
/* etc. */
```

**Twig à modifier** :
```twig
{%- set classes = [
  'ps-input',
  size and size != 'md' ? 'ps-input--' ~ size : null,
  color and color != 'default' ? 'ps-input--' ~ color : null,
  disabled ? 'ps-input--disabled' : null,
  readonly ? 'ps-input--readonly' : null,
] -%}
```

**Répéter pour Select et Textarea** avec la même structure.

#### 1.3 **Checkbox/Radio - Label Optionnel**

**Objectif** : Permettre usage avec/sans label intégré (flexibilité Drupal)

**Checkbox Twig** :
```twig
{# Si label fourni : affiche intégré #}
{% if label %}
  <label class="ps-checkbox__label" for="{{ id }}">{{ label }}</label>
{% endif %}
```

**Props à ajouter** :
```yaml
color: 'primary'    # primary|secondary|info|warning|success|danger
size: 'md'          # xs|sm|md|lg|xl
```

#### 1.4 **Label - Ajout Variants**

**Props à ajouter** :
```yaml
color: 'default'    # default|primary|secondary|info|warning|success|danger|dark|light
size: 'md'          # xs|sm|md|lg|xl (correspond à la taille du champ associé)
```

---

### PHASE 2 : Harmonisation Systèmes (Priorité HAUTE)

#### 2.1 **Icônes - Migration SVG Sprite**

**Radio** :
```css
/* Actuel (mask + URL) */
&::before {
  mask-image: url('/icons/icons-sprite.svg#icon-radio-off');
}

/* À REMPLACER par */
<svg class="ps-radio__icon" aria-hidden="true">
  <use xlink:href="#icon-radio-off" />
</svg>
```

**Search-Bar** :
```twig
<!-- Actuel (inline SVG) -->
<svg class="ps-search-bar__icon" viewBox="0 0 24 24">
  <path d="..." />
</svg>

<!-- À REMPLACER par -->
<svg class="ps-search-bar__icon" aria-hidden="true">
  <use xlink:href="#icon-search" />
</svg>
```

**Vérifier existence des icônes** :
- [ ] `icon-radio-off`
- [ ] `icon-radio-on`
- [ ] `icon-search`
- [ ] `icon-chevron-down` (dropdown)

#### 2.2 **Tokens - Correction Search-Bar**

**Remplacements** :
```css
/* AVANT */
--border-1: var(--gray-300);
--radius-md: 12px;
--duration-short: 150ms;
--ease-out-quad: cubic-bezier(...);

/* APRÈS */
border: var(--border-size-1) solid var(--gray-300);
border-radius: var(--radius-3);
transition: ... var(--duration-fast) var(--ease-3);
```

#### 2.3 **Tokens - Correction Dropdown**

**Créer tokens manquants** (si nécessaire) :
```css
/* Dans sizes.css (SI BESOIN) */
--ps-dropdown-min-width-small: 160px;
--ps-dropdown-min-width-medium: 200px;
--ps-dropdown-min-width-large: 240px;
```

**OU utiliser tailles existantes** :
```css
/* Utiliser directement */
min-width: var(--size-40); /* 160px */
min-width: var(--size-50); /* 200px */
min-width: var(--size-60); /* 240px */
```

---

### PHASE 3 : Couleurs & Tailles (Priorité HAUTE)

#### 3.1 **Système de Couleurs**

**Tokens à utiliser** (vérifier existence dans `colors.css` et `brand.css`) :

```css
/* Couleurs sémantiques (brand.css) */
--primary      /* Vert BNP #00915A */
--secondary    /* À définir si besoin */
--info         /* Bleu information */
--warning      /* Orange/Jaune */
--success      /* Vert validation */
--danger       /* Rouge erreur #EB3636 */
--light        /* Gris clair */
--dark         /* Gris foncé/Noir */
```

**Modificateurs CSS à créer pour TOUS les champs** :

```css
/* Input/Select/Textarea */
.ps-input--primary {
  --ps-input-border-color: var(--primary);
  --ps-input-focus-border-color: var(--primary);
}

.ps-input--success {
  --ps-input-border-color: var(--success);
  --ps-input-focus-border-color: var(--success);
}

.ps-input--danger {
  --ps-input-border-color: var(--danger);
  --ps-input-focus-border-color: var(--danger);
}

.ps-input--warning {
  --ps-input-border-color: var(--warning);
  --ps-input-focus-border-color: var(--warning);
}

.ps-input--info {
  --ps-input-border-color: var(--info);
  --ps-input-focus-border-color: var(--info);
}

/* Checkbox/Radio */
.ps-checkbox--success {
  --ps-checkbox-bg-checked: var(--success);
  --ps-checkbox-border-color-checked: var(--success);
}

/* Label */
.ps-label--primary {
  --ps-label-color: var(--primary);
}
```

#### 3.2 **Système de Tailles**

**Tokens de tailles** (utiliser `sizes.css` existant) :

| Size | Height | Padding X | Padding Y | Font Size | Use Case |
|------|--------|-----------|-----------|-----------|----------|
| **xs** | `--size-7` (28px) | `--size-2` | `--size-1` | `--font-size--1` | Compact tables, inline |
| **sm** | `--size-8` (32px) | `--size-3` | `--size-105` | `--font-size-0` | Sidebar filters |
| **md** | `--size-10` (40px) | `--size-4` | `--size-2` | `--font-size-1` | **DEFAULT** |
| **lg** | `--size-12` (48px) | `--size-5` | `--size-3` | `--font-size-2` | Hero forms |
| **xl** | `--size-14` (56px) | `--size-6` | `--size-4` | `--font-size-3` | CTAs prominents |
| **xxl** | `--size-16` (64px) | `--size-7` | `--size-5` | `--font-size-4` | Landing pages |

**CSS** :

```css
.ps-input--xs {
  --ps-input-min-height: var(--size-7);
  --ps-input-padding-x: var(--size-2);
  --ps-input-padding-y: var(--size-1);
  --ps-input-font-size: var(--font-size--1);
}

.ps-input--sm {
  --ps-input-min-height: var(--size-8);
  --ps-input-padding-x: var(--size-3);
  --ps-input-padding-y: var(--size-105);
  --ps-input-font-size: var(--font-size-0);
}

/* md = default (déjà défini) */

.ps-input--lg {
  --ps-input-min-height: var(--size-12);
  --ps-input-padding-x: var(--size-5);
  --ps-input-padding-y: var(--size-3);
  --ps-input-font-size: var(--font-size-2);
}

.ps-input--xl {
  --ps-input-min-height: var(--size-14);
  --ps-input-padding-x: var(--size-6);
  --ps-input-padding-y: var(--size-4);
  --ps-input-font-size: var(--font-size-3);
}

.ps-input--xxl {
  --ps-input-min-height: var(--size-16);
  --ps-input-padding-x: var(--size-7);
  --ps-input-padding-y: var(--size-5);
  --ps-input-font-size: var(--font-size-4);
}
```

**Répéter pour** : Select, Textarea, Checkbox, Radio, Label

---

### PHASE 4 : Molécules & Intégration (Priorité MOYENNE)

#### 4.1 **Form-Element - Migration**

**Modifier** :
```twig
{# AVANT (utilise Field) #}
{% include '@elements/field/field.twig' with fieldProps %}

{# APRÈS (utilise Input/Select/Textarea directement) #}
{% if type == 'textarea' %}
  {% include '@elements/textarea/textarea.twig' with fieldProps only %}
{% elseif type == 'select' %}
  {% include '@elements/select/select.twig' with fieldProps only %}
{% else %}
  {% include '@elements/input/input.twig' with fieldProps only %}
{% endif %}
```

**Ajouter support des variants** :
```yaml
# form-element.yml
color: 'default'
size: 'md'
```

#### 4.2 **Search-Bar - Conformité Totale**

**Actions** :
1. ✅ Créer `README.md` (manquant)
2. ✅ Corriger tous les tokens non-standards
3. ✅ Migrer icône vers sprite SVG
4. ✅ Ajouter variables CSS Layer 2
5. ✅ Ajouter variantes de taille
6. ✅ Vérifier accessibilité (ARIA, keyboard, focus-visible)

#### 4.3 **Dropdown - Finalisation**

**Actions** :
1. ✅ Corriger tokens (icon-size, font-size, colors)
2. ✅ Ajouter tailles manquantes (md, xl, xxl)
3. ✅ Harmoniser avec Input (même hauteurs pour cohérence visuelle)
4. ✅ Migrer icône vers sprite SVG

---

### PHASE 5 : Nouveaux Composants (Priorité BASSE - À VALIDER)

#### 5.1 **Checkbox-Group (Molecule)**

**Nouveau composant à créer** :
```
source/patterns/components/checkbox-group/
├── checkbox-group.twig
├── checkbox-group.css
├── checkbox-group.yml
├── checkbox-group.stories.jsx
└── README.md
```

**Props** :
```yaml
legend: 'Select options'  # Fieldset legend
options:                  # Array of checkbox configs
  - { name, value, label, checked, disabled }
required: false
error: ''
helper: ''
color: 'default'
size: 'md'
```

**Twig** :
```twig
<fieldset class="ps-checkbox-group">
  <legend class="ps-checkbox-group__legend">{{ legend }}</legend>
  
  {% for option in options %}
    {% include '@elements/checkbox/checkbox.twig' with option only %}
  {% endfor %}
  
  {% if error %}
    <div class="ps-checkbox-group__error">{{ error }}</div>
  {% endif %}
</fieldset>
```

#### 5.2 **Radio-Group (Molecule)**

Même structure que Checkbox-Group.

#### 5.3 **Input-Group (Molecule)**

**Use case** : Input avec addons (prefix/suffix)

```html
<div class="ps-input-group">
  <span class="ps-input-group__addon">$</span>
  <input class="ps-input" />
  <span class="ps-input-group__addon">.00</span>
</div>
```

---

## 📊 Résumé des Actions

### Actions Immédiates (CRITIQUE)

1. **DÉCISION REQUISE** : Supprimer Field atom ?
   - ✅ **RECOMMANDÉ** : Oui, supprimer (doublon, confusion)
   - ⚠️ **ALTERNATIVE** : Renommer en "Input-Wrapper" et réduire scope

2. **Standardiser Input/Select/Textarea** :
   - Ajouter props : `color`, `size`, `readonly`
   - Ajouter modificateurs CSS (couleurs + tailles)
   - Supprimer wrappers redondants

3. **Migrer Form-Element** :
   - Remplacer include Field par Input/Select/Textarea
   - Ajouter support variants

### Actions Importantes (HAUTE)

4. **Système d'icônes** :
   - Radio : SVG sprite (remplacer CSS mask)
   - Search-Bar : SVG sprite (remplacer inline)
   - Dropdown : SVG sprite (si pas déjà fait)

5. **Tokens** :
   - Search-Bar : corriger tous les tokens non-standards
   - Dropdown : corriger icon-size, font-size, colors

6. **Couleurs & Tailles** :
   - Créer modificateurs pour TOUS les composants
   - Harmoniser hauteurs entre Input/Select/Dropdown

### Actions Optionnelles (BASSE)

7. **Nouveaux composants** :
   - Checkbox-Group (si besoin Drupal)
   - Radio-Group (si besoin Drupal)
   - Input-Group (addons)

8. **Documentation** :
   - Search-Bar : créer README.md
   - Tous : ajouter exemples variants dans stories

---

## 🎯 Ordre d'Exécution Recommandé

### Sprint 1 : Architecture (3-4h)
1. Supprimer Field atom
2. Migrer Form-Element vers Input/Select/Textarea
3. Tester build + Storybook

### Sprint 2 : Variants (4-5h)
4. Ajouter props color/size à Input/Select/Textarea/Label
5. Créer modificateurs CSS (couleurs)
6. Créer modificateurs CSS (tailles)
7. Mettre à jour stories avec showcases

### Sprint 3 : Systèmes (2-3h)
8. Migrer Radio vers SVG sprite
9. Migrer Search-Bar vers SVG sprite + tokens
10. Corriger Dropdown tokens

### Sprint 4 : Finalisation (1-2h)
11. Checkbox/Radio : rendre label optionnel
12. Search-Bar : créer README.md
13. Tests accessibilité complets
14. Documentation finale

---

## ✅ Checklist de Conformité (Post-Corrections)

### Chaque composant DOIT avoir :

**Fichiers** :
- [ ] `.twig` avec header comment complet
- [ ] `.css` avec variables Layer 2 + tokens only
- [ ] `.yml` avec données Real Estate
- [ ] `.stories.jsx` avec `tags: ['autodocs']`
- [ ] `README.md` en anglais

**Props Drupal Standard** :
- [ ] `attributes` (Drupal Attribute)
- [ ] `name`, `id`, `value`, `placeholder`
- [ ] `disabled`, `required`, `readonly`
- [ ] `error`, `helper` (si applicable)

**Variants** :
- [ ] `color` : default|primary|secondary|info|warning|success|danger|dark|light
- [ ] `size` : xs|sm|md|lg|xl|xxl

**CSS** :
- [ ] Variables Layer 2 (`--ps-{composant}-*`)
- [ ] Modificateurs couleur (`.ps-{composant}--{color}`)
- [ ] Modificateurs taille (`.ps-{composant}--{size}`)
- [ ] Tokens uniquement (aucune valeur hardcodée)
- [ ] Focus-visible obligatoire

**Accessibilité** :
- [ ] ARIA complet (role, aria-*, states)
- [ ] Keyboard navigation
- [ ] Focus-visible 2px minimum
- [ ] Contrast ratios WCAG 2.2 AA

**Icônes** :
- [ ] SVG sprite uniquement (`<use xlink:href="#icon-*">`)
- [ ] `aria-hidden="true"` + `focusable="false"`

**Storybook** :
- [ ] Default story
- [ ] Showcases : couleurs, tailles, états
- [ ] ArgTypes catégorisés

---

## 🚨 Risques & Points d'Attention

### Risque 1 : Suppression Field
**Impact** : Form-Element doit être refactoré
**Mitigation** : Tests exhaustifs après migration

### Risque 2 : Breaking Changes
**Impact** : Composants parents peuvent casser
**Mitigation** : 
- Vérifier toutes les dépendances (grep search)
- Mettre à jour en cascade
- Tester build + Storybook

### Risque 3 : Tokens Manquants
**Impact** : Couleurs sémantiques peuvent ne pas exister
**Mitigation** :
- Vérifier `brand.css` avant d'implémenter
- Créer tokens si nécessaire (processus séparé)
- Documenter besoins

---

## 📝 Notes Finales

**Ce document est un plan d'action**. Chaque section nécessite validation avant implémentation.

**Validation requise pour** :
- ✅ Suppression Field atom (décision architecturale)
- ✅ Création nouveaux composants (Checkbox-Group, Radio-Group, Input-Group)
- ✅ Tokens manquants (secondary, info, warning, dark, light)

**Priorités suggérées** :
1. 🔴 **CRITIQUE** : Architecture (Field suppression)
2. 🟠 **HAUTE** : Variants (couleurs + tailles)
3. 🟡 **MOYENNE** : Systèmes (icônes + tokens)
4. 🟢 **BASSE** : Nouveaux composants

---

**Prêt à commencer ?** Attends la validation pour procéder avec les corrections.
