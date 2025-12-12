# Templates Drupal

**Mapper composants Storybook** → **Templates Drupal** pour production

---

## 🎯 Objectif

Créer templates Drupal (`.html.twig`) qui **incluent les composants Storybook** via namespaces SDC, mappent variables Drupal → props composants, et attachent libraries CSS/JS.

---

## 📋 Templates essentiels (18 prioritaires)

### Layout (4 templates)

| Template | Fichier | Rôle | Composants utilisés |
|----------|---------|------|---------------------|
| **Page** | `layout/page.html.twig` | Structure page complète | Header, Footer, régions |
| **Region** | `layout/region.html.twig` | Wrapper régions Drupal | Aucun (wrapper simple) |
| **HTML** | `layout/html.html.twig` | `<html>` + `<head>` + `<body>` | Aucun (structure HTML) |
| **Block** | `block/block.html.twig` | Wrapper blocks Drupal | Heading (titre block) |

### Navigation (5 templates)

| Template | Fichier | Rôle | Composants utilisés |
|----------|---------|------|---------------------|
| **Menu** | `navigation/menu.html.twig` | Menus navigation | Link, Icon (chevron) |
| **Breadcrumb** | `navigation/breadcrumb.html.twig` | Fil d'Ariane | Link, Icon (chevron) |
| **Pager** | `navigation/pager.html.twig` | Pagination | Button, Link |
| **Menu local task** | `navigation/menu-local-task.html.twig` | Tabs contextuels | Button (tabs) |
| **Menu local action** | `navigation/menu-local-action.html.twig` | Boutons actions | Button (primary) |

### Content (4 templates)

| Template | Fichier | Rôle | Composants utilisés |
|----------|---------|------|---------------------|
| **Node** | `content/node.html.twig` | Affichage node (article, page) | Card, Badge, Image, Button |
| **Field** | `content/field.html.twig` | Champs individuels | Label, Text, Image, Link |
| **Taxonomy term** | `content/taxonomy-term.html.twig` | Termes taxonomie | Badge, Heading |
| **View** | `views/views-view.html.twig` | Listes/grids Views | Card (grille items) |

### Forms (5 templates)

| Template | Fichier | Rôle | Composants utilisés |
|----------|---------|------|---------------------|
| **Form** | `form/form.html.twig` | Wrapper formulaire | Form (molecule) |
| **Form element** | `form/form-element.html.twig` | Champs avec label/error | Form-field (molecule) |
| **Input** | `form/input.html.twig` | Inputs texte | Input (atom) |
| **Select** | `form/select.html.twig` | Selects | Select (atom) |
| **Textarea** | `form/textarea.html.twig` | Textareas | Textarea (atom) |

---

## 🛠️ Template 1 : Page (Structure complète)

### Fichier : `templates/layout/page.html.twig`

```twig
{#
/**
 * @file
 * Template pour structure page complète Drupal
 * 
 * Variables disponibles:
 * - page: Régions Drupal (header, content, footer, etc.)
 * - node: Contenu actuel (si page node)
 * - user: Utilisateur connecté
 * - is_front: Boolean (page d'accueil)
 * - logged_in: Boolean (utilisateur connecté)
 */
#}

{# Attacher library globale #}
{{ attach_library('ps/global') }}

<div class="page-wrapper">
  {# Header #}
  {% if page.header %}
    {{ attach_library('ps/header') }}
    <header class="page-header" role="banner">
      {{ page.header }}
    </header>
  {% endif %}

  {# Breadcrumb #}
  {% if page.breadcrumb and not is_front %}
    <div class="page-breadcrumb">
      {{ page.breadcrumb }}
    </div>
  {% endif %}

  {# Main content #}
  <main class="page-content" role="main">
    {# Highlighted (messages, alerts) #}
    {% if page.highlighted %}
      <div class="page-highlighted">
        {{ page.highlighted }}
      </div>
    {% endif %}

    {# Content Above #}
    {% if page.content_above %}
      <div class="page-content-above">
        {{ page.content_above }}
      </div>
    {% endif %}

    {# Content principal #}
    <div class="page-content-main">
      {{ page.content }}
    </div>

    {# Content Below #}
    {% if page.content_below %}
      <div class="page-content-below">
        {{ page.content_below }}
      </div>
    {% endif %}
  </main>

  {# Footer #}
  {% if page.footer %}
    {{ attach_library('ps/footer') }}
    <footer class="page-footer" role="contentinfo">
      {{ page.footer }}
    </footer>
  {% endif %}
</div>
```

**Variables clés** :
- `page.header/content/footer` : Régions Drupal
- `is_front` : Boolean page d'accueil
- `logged_in` : Boolean utilisateur connecté
- `node` : Contenu actuel (si disponible)

---

## 🛠️ Template 2 : Node (Affichage article)

### Fichier : `templates/content/node.html.twig`

```twig
{#
/**
 * @file
 * Template pour affichage node (article, page, bien immobilier)
 * 
 * Variables disponibles:
 * - node: Entity node complète
 * - content: Render array champs
 * - view_mode: Mode affichage (full, teaser, card)
 * - attributes: Classes/ID wrapper
 */
#}

{# Attacher library card selon view_mode #}
{% if view_mode == 'teaser' or view_mode == 'card' %}
  {{ attach_library('ps/card') }}
{% endif %}

{# Mode Card (teaser) : Utiliser composant Card #}
{% if view_mode == 'teaser' or view_mode == 'card' %}
  
  {% include '@components/card/card.twig' with {
    title: label,
    description: content.body.0['#text']|striptags|truncate(150),
    image: node.field_image.entity.uri.value ? file_url(node.field_image.entity.uri.value) : null,
    cta_text: 'Lire la suite'|t,
    cta_href: url,
    badge_text: node.field_category.entity.name.value ?? null,
    badge_variant: 'primary',
    eyebrow: node.created.value|format_date('custom', 'd/m/Y'),
    attributes: attributes.addClass('node', 'node--' ~ node.bundle, 'node--' ~ view_mode)
  } only %}

{# Mode Full : Affichage complet traditionnel #}
{% else %}
  
  <article{{ attributes.addClass('node', 'node--' ~ node.bundle, 'node--full') }}>
    
    {# Titre #}
    {% if label and not page %}
      {{ attach_library('ps/heading') }}
      <h1 class="ps-heading ps-heading--h1">{{ label }}</h1>
    {% endif %}

    {# Meta informations #}
    <div class="node-meta">
      {{ attach_library('ps/badge') }}
      {% if node.field_category.entity %}
        {% include '@elements/badge/badge.twig' with {
          text: node.field_category.entity.name.value,
          variant: 'primary'
        } only %}
      {% endif %}
      
      <time datetime="{{ node.created.value|format_date('html_datetime') }}">
        {{ node.created.value|format_date('medium') }}
      </time>
    </div>

    {# Image principale #}
    {% if content.field_image %}
      {{ attach_library('ps/image') }}
      <div class="node-image">
        {{ content.field_image }}
      </div>
    {% endif %}

    {# Contenu body #}
    {% if content.body %}
      <div class="node-body">
        {{ content.body }}
      </div>
    {% endif %}

    {# Autres champs #}
    {{ content|without('field_image', 'body', 'field_category') }}

  </article>

{% endif %}
```

**Mapping ViewMode → Composant** :
- `teaser` / `card` → Composant Card (molecule)
- `full` → HTML traditionnel avec atoms (Badge, Heading)

---

## 🛠️ Template 3 : Form Element (Champs formulaire)

### Fichier : `templates/form/form-element.html.twig`

```twig
{#
/**
 * @file
 * Template pour champs formulaire avec label, input, helper, error
 * 
 * Variables disponibles:
 * - element: Render array champ formulaire
 * - title: Label champ
 * - description: Texte helper
 * - errors: Messages erreur
 * - required: Boolean champ obligatoire
 * - disabled: Boolean champ désactivé
 * - type: Type champ (textfield, select, textarea, etc.)
 */
#}

{# Attacher library form-field #}
{{ attach_library('ps/form-field') }}

{# Déterminer type de champ pour mapping #}
{% set field_type = element['#type'] %}

{# Mapper type Drupal → type composant #}
{% if field_type == 'textfield' or field_type == 'email' or field_type == 'password' %}
  {% set component_type = field_type == 'textfield' ? 'text' : field_type %}
{% elseif field_type == 'select' %}
  {% set component_type = 'select' %}
{% elseif field_type == 'textarea' %}
  {% set component_type = 'textarea' %}
{% else %}
  {% set component_type = 'text' %}
{% endif %}

{# Extraire options si select #}
{% set options = [] %}
{% if field_type == 'select' and element['#options'] %}
  {% for key, value in element['#options'] %}
    {% set options = options|merge([{
      value: key,
      label: value,
      selected: key == element['#default_value']
    }]) %}
  {% endfor %}
{% endif %}

{# Utiliser composant Form-field (molecule) #}
{% include '@components/form-field/form-field.twig' with {
  label: title,
  type: component_type,
  name: element['#name'],
  id: element['#id'],
  value: element['#default_value'] ?? element['#value'] ?? '',
  placeholder: element['#placeholder'] ?? '',
  required: required,
  disabled: disabled,
  helper: description ? description|render|striptags : '',
  error: errors ? errors|first|render|striptags : '',
  rows: element['#rows'] ?? 4,
  options: options,
  attributes: element['#attributes']
} only %}
```

**Mapping types Drupal** :
- `textfield` → Input type="text"
- `email` → Input type="email"
- `password` → Input type="password"
- `select` → Select avec options
- `textarea` → Textarea avec rows

---

## 🛠️ Template 4 : Menu (Navigation)

### Fichier : `templates/navigation/menu.html.twig`

```twig
{#
/**
 * @file
 * Template pour menus navigation Drupal
 * 
 * Variables disponibles:
 * - items: Tableau items menu [{title, url, below}]
 * - attributes: Classes wrapper
 * - menu_name: ID menu (main, footer, account)
 */
#}

{# Attacher library menu #}
{{ attach_library('ps/menu') }}

{% macro menu_links(items, attributes, menu_level) %}
  {% if items %}
    <ul{{ attributes.addClass('ps-menu', 'ps-menu--level-' ~ menu_level) }}>
      {% for item in items %}
        <li class="ps-menu__item{{ item.in_active_trail ? ' ps-menu__item--active' : '' }}">
          
          {# Lien menu #}
          {% include '@elements/link/link.twig' with {
            text: item.title,
            href: item.url,
            attributes: create_attribute()
              .addClass('ps-menu__link')
              .addClass(item.in_active_trail ? 'ps-menu__link--active' : null)
          } only %}

          {# Sous-menu récursif #}
          {% if item.below %}
            {{ _self.menu_links(item.below, attributes.removeClass('ps-menu'), menu_level + 1) }}
          {% endif %}

        </li>
      {% endfor %}
    </ul>
  {% endif %}
{% endmacro %}

{# Appeler macro avec items niveau 0 #}
{{ _self.menu_links(items, attributes, 0) }}
```

**Fonctionnalités** :
- Récursif (sous-menus illimités)
- Classes active trail (item actif + parents)
- Utilise composant Link (atom)

---

## 🛠️ Template 5 : Breadcrumb (Fil d'Ariane)

### Fichier : `templates/navigation/breadcrumb.html.twig`

```twig
{#
/**
 * @file
 * Template pour fil d'Ariane Drupal
 * 
 * Variables disponibles:
 * - breadcrumb: Tableau items [{text, url}]
 */
#}

{% if breadcrumb %}
  {# Attacher library breadcrumb #}
  {{ attach_library('ps/breadcrumb') }}

  <nav class="ps-breadcrumb" aria-label="{{ 'Breadcrumb'|t }}">
    <ol class="ps-breadcrumb__list">
      {% for item in breadcrumb %}
        <li class="ps-breadcrumb__item">
          {% if item.url %}
            {# Lien breadcrumb #}
            {% include '@elements/link/link.twig' with {
              text: item.text,
              href: item.url,
              attributes: create_attribute().addClass('ps-breadcrumb__link')
            } only %}
          {% else %}
            {# Item actif (pas de lien) #}
            <span class="ps-breadcrumb__text" aria-current="page">
              {{ item.text }}
            </span>
          {% endif %}

          {# Séparateur (sauf dernier) #}
          {% if not loop.last %}
            <span class="ps-breadcrumb__separator" data-icon="chevron-right" aria-hidden="true"></span>
          {% endif %}
        </li>
      {% endfor %}
    </ol>
  </nav>
{% endif %}
```

**Accessibilité** :
- `<nav aria-label="Breadcrumb">` (région navigation)
- `<ol>` (liste ordonnée)
- `aria-current="page"` (item actif)
- `aria-hidden="true"` (séparateurs décoratifs)

---

## 📊 Checklist création template

### Avant de créer

- [ ] Identifier composant(s) Storybook à utiliser
- [ ] Vérifier props composant (README.md)
- [ ] Lister variables Drupal disponibles
- [ ] Déterminer mapping variables → props

### Dans le template

- [ ] Commenter header (rôle, variables disponibles)
- [ ] Attacher library CSS/JS (`{{ attach_library() }}`)
- [ ] Inclure composant avec namespace (`@elements/`, `@components/`)
- [ ] Mapper variables Drupal → props composant
- [ ] Utiliser `only` keyword (sécurité)
- [ ] Gérer cas vides (conditions `{% if %}`)

### Tests

- [ ] Clear cache Drupal (`drush cr`)
- [ ] Vérifier affichage page
- [ ] Inspecter HTML généré (classes BEM)
- [ ] Vérifier CSS chargé (Network tab)
- [ ] Tester responsive (mobile, tablet, desktop)
- [ ] Vérifier accessibilité (screen reader, clavier)

---

## 🚨 Pièges courants

### 1. Oublier `only` keyword

```twig
{# ❌ RISQUE SÉCURITÉ - Toutes variables passées #}
{% include '@components/card/card.twig' with {
  title: node.title
} %}

{# ✅ SÉCURISÉ - Seulement props explicites #}
{% include '@components/card/card.twig' with {
  title: node.title
} only %}
```

### 2. Oublier `attach_library()`

```twig
{# ❌ CSS manquant - Composant non stylisé #}
{% include '@components/card/card.twig' %}

{# ✅ CSS chargé #}
{{ attach_library('ps/card') }}
{% include '@components/card/card.twig' %}
```

### 3. Namespace incorrect

```twig
{# ❌ Chemin relatif (fragile) #}
{% include '../source/patterns/components/card/card.twig' %}

{# ✅ Namespace SDC (configuré ps.info.yml) #}
{% include '@components/card/card.twig' %}
```

### 4. Oublier clear cache

```bash
# ❌ Modifications template non visibles
# → Drupal cache ancien template

# ✅ TOUJOURS clear cache après modif template
drush cr
```

---

## 📋 Templates par priorité

### P0 - CRITIQUE (3 templates)

1. **page.html.twig** - Structure page (header, content, footer)
2. **form-element.html.twig** - Champs formulaires (Form API)
3. **node.html.twig** - Affichage nodes (articles, pages)

### P1 - HAUTE (5 templates)

4. **menu.html.twig** - Menus navigation
5. **breadcrumb.html.twig** - Fil d'Ariane
6. **field.html.twig** - Champs individuels
7. **form.html.twig** - Wrapper formulaires
8. **block.html.twig** - Wrapper blocks

### P2 - MOYENNE (5 templates)

9. **pager.html.twig** - Pagination
10. **views-view.html.twig** - Listes Views
11. **input.html.twig** - Inputs texte
12. **select.html.twig** - Selects
13. **textarea.html.twig** - Textareas

---

## 🎯 Prochaines étapes

**Créer templates P0** (3 essentiels) :
1. Copier exemples ci-dessus dans `templates/`
2. Adapter mapping variables selon besoin
3. Clear cache Drupal (`drush cr`)
4. Tester affichage

**Poursuivre avec** :
- **[Libraries & Assets](./03-libraries-assets.md)** → Configurer CSS/JS
- **[Drupal Forms](./04-drupal-forms.md)** → Intégrer Form API

---

**Navigation** : [← Vue d'ensemble](./01-vue-ensemble.md) | [README](./README.md) | [Libraries →](./03-libraries-assets.md)
