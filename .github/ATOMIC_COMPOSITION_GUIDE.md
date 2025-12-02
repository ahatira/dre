# Guide de Composition Atomique (Atomic Design)

**Date** : 2025-12-02  
**Objectif** : Définir les règles strictes de composition entre Elements (Atoms), Components (Molecules), et Collections (Organisms)

---

## 🎯 Principe Fondamental

### Une Molecule DOIT composer des Atoms via `{% include %}`

**❌ MAUVAIS** : Écrire le HTML directement
```twig
{# Dans avatar.twig (Molecule) #}
<div class="ps-avatar">
  <img class="ps-avatar__image" src="{{ src }}" alt="{{ alt }}" />
  <span class="ps-avatar__text">{{ initials }}</span>
</div>
```

**✅ BON** : Composer des Atoms via includes
```twig
{# Dans avatar.twig (Molecule) #}
<div class="ps-avatar">
  {% if has_image %}
    {% include '@elements/image/image.twig' with {
      src: src,
      alt: alt,
      fit: 'cover',
      rounded: shape == 'circle' ? 'full' : (shape == 'rounded' ? 'md' : 'none')
    } only %}
  {% endif %}
  
  {% if has_initials %}
    {% include '@elements/text/text.twig' with {
      text: initials,
      tag: 'span',
      size: 'inherit'
    } only %}
  {% endif %}
  
  {% if status %}
    {% include '@elements/badge/badge.twig' with {
      text: '',
      color: status == 'online' ? 'success' : (status == 'busy' ? 'danger' : 'default'),
      pill: true,
      size: 'small'
    } only %}
  {% endif %}
</div>
```

---

## 📊 Hiérarchie et Règles de Composition

### 1️⃣ **Elements (Atoms)** - `source/patterns/elements/`

**Définition** : Composants indivisibles, une seule responsabilité

**Règles** :
- ✅ Peuvent utiliser UNIQUEMENT des tokens CSS et HTML natif
- ✅ Ne composent JAMAIS d'autres composants
- ✅ Exemple : `button`, `image`, `text`, `badge`, `icon`, `link`

**Exemples disponibles** :
- `badge/` - Status indicators ✅
- `button/` - Interactive buttons ✅
- `checkbox/` - Form input ✅
- `divider/` - Horizontal rule ✅
- `eyebrow/` - Small label text ✅
- `field/` - Input field ✅
- `flag/` - Country flag icon ✅
- `heading/` - Titles (h1-h6) ✅
- `icon/` - SVG icons ✅
- `image/` - Responsive images ✅
- `label/` - Form labels ✅
- `link/` - Hyperlinks ✅
- `progress-bar/` - Progress indicator ✅
- `radio/` - Radio input ✅
- `skip-link/` - Accessibility link ✅
- `spinner/` - Loading spinner ✅
- `text/` - Paragraph text ✅
- `toggle/` - Toggle switch ✅

---

### 2️⃣ **Components (Molecules)** - `source/patterns/components/`

**Définition** : Assemblage de 2+ Atoms avec logique conditionnelle

**Règles** :
- ✅ DOIVENT composer des Elements via `{% include @elements/... %}`
- ✅ Peuvent avoir leur propre CSS pour layout/positioning
- ✅ Gèrent la logique de fallback et états conditionnels
- ❌ Ne doivent PAS réécrire le HTML des Atoms

**Exemples en cours** :
- `accordion/` - Needs: heading + icon + text
- `alert/` - Needs: icon + heading + text + button (close)
- `avatar/` - **À REFACTORISER** : image + text + badge
- `breadcrumb/` - Needs: link + icon + text
- `card/` - Needs: image + heading + text + badge + button
- `carousel/` - Needs: image + button (nav)
- `dropdown/` - Needs: button + text + icon
- `form-field/` - Needs: label + field + text (error/help)

---

### 3️⃣ **Collections (Organisms)** - `source/patterns/collections/`

**Définition** : Assemblage de Components + Elements en sections complexes

**Règles** :
- ✅ Composent des Components via `{% include @components/... %}`
- ✅ Peuvent composer des Elements directement si nécessaire
- ✅ Gèrent des structures complexes (grids, lists, sections)
- ❌ Ne doivent PAS dupliquer la logique des Components

---

## 🔧 Refactoring Prioritaire

### Avatar (Component/Molecule) - URGENT

**État actuel** : ❌ HTML inline pour image/text/badge

**Refactoring nécessaire** :

```twig
{# avatar.twig - VERSION ATOMIQUE #}
<{{ tag }} class="{{ avatar_classes|join(' ')|trim }}"
  {%- if href %} href="{{ href }}"{% endif -%}
  {%- if attributes %} {{ attributes }}{% endif -%}
>
  {# Atom 1: Image #}
  {%- if has_image -%}
    {% include '@elements/image/image.twig' with {
      src: src,
      alt: alt,
      fit: 'cover',
      rounded: shape == 'circle' ? 'full' : (shape == 'rounded' ? 'md' : 'none'),
      loading: 'lazy',
      attributes: create_attribute().addClass('ps-avatar__image')
    } only %}
  {%- endif -%}
  
  {# Atom 2: Initials Text #}
  {%- if has_initials -%}
    {% include '@elements/text/text.twig' with {
      text: initials,
      tag: 'span',
      strong: true,
      attributes: create_attribute().addClass('ps-avatar__text')
    } only %}
  {%- endif -%}
  
  {# Atom 3: Status Badge #}
  {%- if status -%}
    {% include '@elements/badge/badge.twig' with {
      text: '',
      color: status == 'online' ? 'success' : (status == 'busy' ? 'danger' : 'default'),
      pill: true,
      size: 'small',
      attributes: create_attribute()
        .addClass('ps-avatar__status')
        .setAttribute('aria-label', status == 'online' ? 'Online' : (status == 'busy' ? 'Busy' : 'Offline'))
    } only %}
  {%- endif -%}
</{{ tag }}>
```

**Problèmes à résoudre** :
1. Badge actuel n'accepte pas `text: ''` (empty) pour status indicator visuel uniquement
2. Image actuelle ne supporte pas `attributes` pour ajouter classes supplémentaires
3. Text actuel utilise `.merge()` au lieu de ternaire avec `null`

---

## 📋 Checklist de Composition pour Nouveaux Components

Avant de créer/valider un Component (Molecule), vérifier :

- [ ] **Identifie les Atoms nécessaires** : Quels Elements compose-t-il ?
- [ ] **Vérifie la disponibilité** : Les Atoms existent-ils dans `elements/` ?
- [ ] **Si manquants** : Créer les Atoms d'abord
- [ ] **Composition via includes** : Utilise `{% include @elements/... %}` pour chaque Atom
- [ ] **CSS du Component** : Uniquement layout/positioning/spacing, pas de style d'Atom
- [ ] **Props mapping** : Mappe les props du Component vers les Atoms
- [ ] **Documentation** : Liste les Atoms composés dans README

---

## 🎯 Prochains Composants à Analyser

### Alert (Molecule)
**Atoms nécessaires** :
- [ ] `icon` ✅ (existe)
- [ ] `heading` ✅ (existe)
- [ ] `text` ✅ (existe)
- [ ] `button` ✅ (existe - pour close)
- [ ] `link` ✅ (existe - pour .ps-alert-link)

**Action** : Refactoriser pour utiliser includes

---

### Card (Molecule)
**Atoms nécessaires** :
- [ ] `image` ✅ (existe)
- [ ] `eyebrow` ✅ (existe)
- [ ] `heading` ✅ (existe)
- [ ] `text` ✅ (existe)
- [ ] `badge` ✅ (existe)
- [ ] `button` ✅ (existe)
- [ ] `link` ✅ (existe)

**Action** : Refactoriser pour utiliser includes

---

### Breadcrumb (Molecule)
**Atoms nécessaires** :
- [ ] `link` ✅ (existe)
- [ ] `icon` ✅ (existe - separator)
- [ ] `text` ✅ (existe - current item)

**Action** : Refactoriser pour utiliser includes

---

### Form-Field (Molecule)
**Atoms nécessaires** :
- [ ] `label` ✅ (existe)
- [ ] `field` ✅ (existe - input/textarea/select)
- [ ] `text` ✅ (existe - help text)
- [ ] `text` ✅ (existe - error message)
- [ ] `icon` ✅ (existe - validation icon)

**Action** : Refactoriser pour utiliser includes

---

### Dropdown (Molecule)
**Atoms nécessaires** :
- [ ] `button` ✅ (existe - trigger)
- [ ] `icon` ✅ (existe - chevron)
- [ ] `text` ✅ (existe - items)
- [ ] `link` ✅ (existe - clickable items)
- [ ] `divider` ✅ (existe - separator)

**Action** : Refactoriser pour utiliser includes

---

### Accordion (Molecule)
**Atoms nécessaires** :
- [ ] `heading` ✅ (existe - trigger)
- [ ] `button` ✅ (existe - interactive trigger)
- [ ] `icon` ✅ (existe - chevron)
- [ ] `text` ✅ (existe - content)

**Action** : Refactoriser pour utiliser includes

---

### Carousel (Molecule)
**Atoms nécessaires** :
- [ ] `image` ✅ (existe - slides)
- [ ] `button` ✅ (existe - nav arrows)
- [ ] `icon` ✅ (existe - arrow icons)
- [ ] Pagination dots : **NOUVEAU ATOM à créer** ?

**Action** : Vérifier si pagination dots = badge ou nouveau atom

---

## 🚫 Anti-Patterns à Éviter

### ❌ Duplication de HTML d'Atom dans Molecule
```twig
{# MAUVAIS - Duplique le HTML de button #}
<button class="ps-card__cta ps-button ps-button--primary">
  {{ cta_text }}
</button>
```

### ✅ Composition via Include
```twig
{# BON - Compose button atom #}
{% include '@elements/button/button.twig' with {
  text: cta_text,
  color: 'primary',
  attributes: create_attribute().addClass('ps-card__cta')
} only %}
```

---

### ❌ Styles d'Atom dans CSS de Molecule
```css
/* MAUVAIS - Style le badge dans card.css */
.ps-card__badge {
  background: var(--primary);
  color: var(--white);
  padding: var(--size-1) var(--size-2);
}
```

### ✅ Layout/Position uniquement
```css
/* BON - Position/layout seulement */
.ps-card__badge {
  position: absolute;
  top: var(--size-3);
  right: var(--size-3);
}
```

---

## 📝 Template de Refactoring

Pour chaque Component à refactoriser :

1. **Identifier les Atoms** : Lister tous les éléments HTML qui pourraient être des Atoms
2. **Vérifier disponibilité** : Checker `source/patterns/elements/`
3. **Créer les Atoms manquants** : Si nécessaire
4. **Adapter les Atoms existants** : Ajouter support `attributes` si besoin
5. **Refactoriser le Twig** : Remplacer HTML inline par `{% include %}`
6. **Adapter le CSS** : Garder uniquement layout/positioning
7. **Mettre à jour README** : Documenter les Atoms composés
8. **Tester** : Vérifier que les stories Storybook fonctionnent

---

## ✅ Checklist de Conformité Atomique

Un Component est **conforme** si :

- [ ] Utilise `{% include @elements/... %}` pour TOUS les Atoms
- [ ] Ne duplique AUCUN HTML d'Atom
- [ ] CSS contient UNIQUEMENT layout/spacing/positioning
- [ ] README liste les Atoms composés
- [ ] Stories Storybook fonctionnent avec la composition
- [ ] Props du Component mappent correctement vers les Atoms
- [ ] Utilise `only` dans les includes pour scope isolation
- [ ] Utilise `create_attribute()` pour passer classes supplémentaires aux Atoms

---

## 🎓 Ressources

- [Atomic Design Methodology](https://atomicdesign.bradfrost.com/)
- [Drupal Component YAML Schema](https://git.drupalcode.org/project/drupal/-/blob/10.1.x/core/modules/sdc/src/Component/schema.json)
- [Twig Include Documentation](https://twig.symfony.com/doc/3.x/tags/include.html)
- `.github/COMPLETE_RULES.md` - Règles complètes du projet
