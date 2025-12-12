# Preprocess Functions

**Transformer données Drupal** (entities, fields, render arrays) → **Props composants PS Theme** via hooks PHP

---

## 🎯 Objectif

Utiliser **preprocess hooks** pour :
1. **Mapper données Drupal** → Props composants (variables Twig)
2. **Attacher libraries CSS/JS** conditionnellement
3. **Ajouter classes BEM** dynamiquement
4. **Transformer structures complexes** (taxonomy terms, images, dates)

---

## 🏗️ Architecture Preprocess

### Workflow données

```
1. Entity Drupal              2. Preprocess Hook           3. Template Twig
┌─────────────────┐          ┌──────────────────┐         ┌────────────────┐
│ Node             │          │ hook_preprocess_ │         │ node.html.twig │
│ - title          │          │ node()           │         │                │
│ - field_image    │ ──────> │ - Mapper fields  │ ─────> │ {% include     │
│ - field_category │          │ - Attacher lib   │         │ @components/   │
│ - created        │          │ - Ajouter classes│         │ card %}        │
└─────────────────┘          └──────────────────┘         └────────────────┘
```

**Hooks disponibles** :
- `hook_preprocess_HOOK(&$variables)` : Modifier variables template spécifique
- `hook_preprocess(&$variables)` : Modifier variables tous templates

**Fichier** : `ps.theme` (racine thème)

---

## 📋 Hooks preprocess essentiels

### Tableau hooks par template

| Hook | Template | Usage | Variables clés |
|------|----------|-------|----------------|
| **hook_preprocess_page** | `page.html.twig` | Ajouter classes body, attacher libraries globales | `$variables['page']`, `$variables['attributes']` |
| **hook_preprocess_node** | `node.html.twig` | Mapper node → card props (view mode teaser) | `$variables['node']`, `$variables['view_mode']` |
| **hook_preprocess_field** | `field.html.twig` | Transformer valeurs champs (dates, taxonomy) | `$variables['items']`, `$variables['field_name']` |
| **hook_preprocess_menu** | `menu.html.twig` | Ajouter icons, classes active trail | `$variables['items']` |
| **hook_preprocess_block** | `block.html.twig` | Ajouter classes région, attacher JS | `$variables['elements']['#id']` |
| **hook_preprocess_form** | `form.html.twig` | Attacher libraries formulaires | `$variables['element']['#form_id']` |
| **hook_preprocess_input** | `input.html.twig` | Ajouter data-error, aria-invalid | `$variables['element']['#errors']` |
| **hook_preprocess_breadcrumb** | `breadcrumb.html.twig` | Ajouter icons séparateurs | `$variables['breadcrumb']` |
| **hook_preprocess_views_view** | `views-view.html.twig` | Attacher JS grilles (Masonry, Isotope) | `$variables['view']` |

---

## 🛠️ Exemple 1 : Node → Card (View Mode Teaser)

### Fichier : `ps.theme`

```php
<?php

/**
 * @file
 * Preprocess functions pour PS Theme
 */

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * Implements hook_preprocess_node().
 */
function ps_theme_preprocess_node(&$variables) {
  $node = $variables['node'];
  $view_mode = $variables['view_mode'];

  // Si view mode teaser ou card, mapper vers composant Card
  if ($view_mode === 'teaser' || $view_mode === 'card') {
    
    // Attacher library card
    $variables['#attached']['library'][] = 'ps/card';

    // Mapper title
    $variables['card_title'] = $node->getTitle();

    // Mapper description (body résumé)
    if ($node->hasField('body') && !$node->get('body')->isEmpty()) {
      $body = $node->get('body')->value;
      $variables['card_description'] = strip_tags($body);
      // Tronquer à 150 caractères
      if (mb_strlen($variables['card_description']) > 150) {
        $variables['card_description'] = mb_substr($variables['card_description'], 0, 147) . '...';
      }
    }

    // Mapper image (field_image)
    if ($node->hasField('field_image') && !$node->get('field_image')->isEmpty()) {
      $image_field = $node->get('field_image')->first();
      if ($image_field) {
        $file = File::load($image_field->target_id);
        if ($file) {
          // Utiliser image style 'card_thumbnail' (à créer)
          $image_uri = $file->getFileUri();
          $image_style = ImageStyle::load('card_thumbnail');
          if ($image_style) {
            $variables['card_image'] = $image_style->buildUrl($image_uri);
          } else {
            // Fallback : URL originale
            $variables['card_image'] = file_create_url($image_uri);
          }
          $variables['card_image_alt'] = $image_field->alt ?? $node->getTitle();
        }
      }
    }

    // Mapper badge (field_category taxonomy)
    if ($node->hasField('field_category') && !$node->get('field_category')->isEmpty()) {
      $category = $node->get('field_category')->entity;
      if ($category) {
        $variables['card_badge_text'] = $category->getName();
        $variables['card_badge_variant'] = 'primary';
      }
    }

    // Mapper eyebrow (date création)
    $created = $node->getCreatedTime();
    $date = DrupalDateTime::createFromTimestamp($created);
    $variables['card_eyebrow'] = $date->format('d/m/Y');

    // Mapper CTA
    $variables['card_cta_text'] = t('Lire la suite');
    $variables['card_cta_href'] = $node->toUrl()->toString();

    // Ajouter classes BEM
    $variables['attributes']['class'][] = 'node';
    $variables['attributes']['class'][] = 'node--' . $node->bundle();
    $variables['attributes']['class'][] = 'node--' . $view_mode;
  }

  // View mode full : Attacher libraries spécifiques
  if ($view_mode === 'full') {
    $variables['#attached']['library'][] = 'ps/heading';
    $variables['#attached']['library'][] = 'ps/badge';
  }
}
```

**Variables créées** :
- `card_title`, `card_description`, `card_image`, `card_image_alt`
- `card_badge_text`, `card_badge_variant`, `card_eyebrow`
- `card_cta_text`, `card_cta_href`

**Template** (`node.html.twig`) :
```twig
{% if view_mode == 'teaser' or view_mode == 'card' %}
  {% include '@components/card/card.twig' with {
    title: card_title,
    description: card_description,
    image: card_image,
    image_alt: card_image_alt,
    badge_text: card_badge_text,
    badge_variant: card_badge_variant,
    eyebrow: card_eyebrow,
    cta_text: card_cta_text,
    cta_href: card_cta_href,
    attributes: attributes
  } only %}
{% endif %}
```

---

## 🛠️ Exemple 2 : Field → Badge (Taxonomy Term)

### Fichier : `ps.theme`

```php
/**
 * Implements hook_preprocess_field().
 */
function ps_theme_preprocess_field(&$variables) {
  $field_name = $variables['field_name'];
  $field_type = $variables['field_type'];

  // Field taxonomy_term_reference → Badges
  if ($field_type === 'entity_reference' && $field_name === 'field_tags') {
    
    // Attacher library badge
    $variables['#attached']['library'][] = 'ps/badge';

    // Transformer items en props badges
    $badge_items = [];
    foreach ($variables['items'] as $delta => $item) {
      $term = $item['content']['#plain_text'] ?? '';
      if ($term) {
        $badge_items[] = [
          'text' => $term,
          'variant' => 'secondary', // Ou mapper term ID → variant
        ];
      }
    }
    $variables['badge_items'] = $badge_items;

    // Ajouter classes
    $variables['attributes']['class'][] = 'field-badges';
  }

  // Field date → Formater français
  if ($field_type === 'datetime') {
    foreach ($variables['items'] as $delta => $item) {
      if (isset($item['content']['#date'])) {
        $date = $item['content']['#date'];
        if ($date instanceof DrupalDateTime) {
          $variables['items'][$delta]['formatted_date'] = $date->format('d/m/Y à H:i');
        }
      }
    }
  }
}
```

**Template** (`field--field-tags.html.twig`) :
```twig
{% if badge_items %}
  <div{{ attributes.addClass('field', 'field--name-' ~ field_name) }}>
    {% for badge in badge_items %}
      {% include '@elements/badge/badge.twig' with {
        text: badge.text,
        variant: badge.variant
      } only %}
    {% endfor %}
  </div>
{% endif %}
```

---

## 🛠️ Exemple 3 : Menu → Icons Navigation

### Fichier : `ps.theme`

```php
/**
 * Implements hook_preprocess_menu().
 */
function ps_theme_preprocess_menu(&$variables) {
  $menu_name = $variables['menu_name'] ?? '';

  // Menu principal : Attacher library + ajouter icons
  if ($menu_name === 'main') {
    
    // Attacher libraries
    $variables['#attached']['library'][] = 'ps/menu';
    $variables['#attached']['library'][] = 'ps/icon';

    // Ajouter icon chevron pour items avec sous-menus
    ps_theme_add_menu_icons($variables['items']);
  }
}

/**
 * Helper : Ajouter icons récursivement
 */
function ps_theme_add_menu_icons(&$items) {
  foreach ($items as &$item) {
    // Si item a sous-menu, ajouter icon chevron
    if (!empty($item['below'])) {
      $item['icon'] = 'chevron-down';
      $item['has_submenu'] = TRUE;

      // Récursif pour sous-menus
      ps_theme_add_menu_icons($item['below']);
    }

    // Ajouter classe active
    if ($item['in_active_trail']) {
      $item['attributes']->addClass('is-active');
    }
  }
}
```

**Template** (`menu.html.twig`) :
```twig
{% macro menu_links(items, attributes, menu_level) %}
  {% if items %}
    <ul{{ attributes.addClass('ps-menu', 'ps-menu--level-' ~ menu_level) }}>
      {% for item in items %}
        <li class="ps-menu__item{{ item.in_active_trail ? ' is-active' : '' }}">
          
          {# Lien #}
          <a href="{{ item.url }}" class="ps-menu__link"{% if item.icon %} data-icon="{{ item.icon }}" data-icon-position="end"{% endif %}>
            {{ item.title }}
          </a>

          {# Sous-menu récursif #}
          {% if item.below %}
            {{ _self.menu_links(item.below, attributes.removeClass('ps-menu'), menu_level + 1) }}
          {% endif %}

        </li>
      {% endfor %}
    </ul>
  {% endif %}
{% endmacro %}

{{ _self.menu_links(items, attributes, 0) }}
```

---

## 🛠️ Exemple 4 : Page → Classes Body Dynamiques

### Fichier : `ps.theme`

```php
/**
 * Implements hook_preprocess_page().
 */
function ps_theme_preprocess_page(&$variables) {
  
  // Ajouter classes body selon contexte
  $attributes = &$variables['attributes'];

  // Page front
  if (\Drupal::service('path.matcher')->isFrontPage()) {
    $attributes['class'][] = 'page-front';
  }

  // Utilisateur connecté
  if (\Drupal::currentUser()->isAuthenticated()) {
    $attributes['class'][] = 'user-logged-in';
  }

  // Node type (si page node)
  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node instanceof \Drupal\node\NodeInterface) {
    $attributes['class'][] = 'page-node';
    $attributes['class'][] = 'page-node--' . $node->bundle();
    $attributes['class'][] = 'page-node--' . $node->id();

    // View mode
    $view_mode = \Drupal::routeMatch()->getParameter('view_mode') ?? 'full';
    $attributes['class'][] = 'page-node--view-mode-' . $view_mode;
  }

  // Taxonomy term (si page term)
  $term = \Drupal::routeMatch()->getParameter('taxonomy_term');
  if ($term instanceof \Drupal\taxonomy\TermInterface) {
    $attributes['class'][] = 'page-taxonomy';
    $attributes['class'][] = 'page-taxonomy--' . $term->bundle();
  }

  // Attacher library header/footer conditionnellement
  if (!empty($variables['page']['header'])) {
    $variables['#attached']['library'][] = 'ps/header';
  }
  if (!empty($variables['page']['footer'])) {
    $variables['#attached']['library'][] = 'ps/footer';
  }
}
```

**Résultat HTML** :
```html
<body class="page-front user-logged-in page-node page-node--article page-node--123 page-node--view-mode-full">
```

---

## 🛠️ Exemple 5 : Input → États Validation

### Fichier : `ps.theme`

```php
/**
 * Implements hook_preprocess_input().
 */
function ps_theme_preprocess_input(&$variables) {
  $element = $variables['element'];
  
  // Ajouter attribut data-error si erreur validation
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
    
    // Lier au message erreur (si ID présent)
    if (!empty($element['#id'])) {
      $error_id = $element['#id'] . '-error';
      $variables['attributes']['aria-describedby'] = $error_id;
    }
  }
  
  // Ajouter classes BEM
  $variables['attributes']['class'][] = 'ps-input';
  
  // Classe disabled
  if (!empty($element['#disabled'])) {
    $variables['attributes']['class'][] = 'ps-input--disabled';
  }
}

/**
 * Implements hook_preprocess_select().
 */
function ps_theme_preprocess_select(&$variables) {
  $element = $variables['element'];
  
  // Même logique pour select
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
  
  $variables['attributes']['class'][] = 'ps-select__input';
}

/**
 * Implements hook_preprocess_textarea().
 */
function ps_theme_preprocess_textarea(&$variables) {
  $element = $variables['element'];
  
  // Même logique pour textarea
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
  
  $variables['attributes']['class'][] = 'ps-textarea';
}
```

**CSS automatique** (via `[data-error]` selector) :
```css
/* input.css */
.ps-input[data-error] {
  border-color: var(--danger);
  background-color: var(--danger-bg-subtle);
}
```

---

## 🔧 Utilitaires courants

### 1. Charger image avec Image Style

```php
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

function ps_theme_get_image_url($fid, $style_name = 'large') {
  $file = File::load($fid);
  if (!$file) {
    return NULL;
  }

  $image_uri = $file->getFileUri();
  $image_style = ImageStyle::load($style_name);
  
  if ($image_style) {
    return $image_style->buildUrl($image_uri);
  }
  
  return file_create_url($image_uri);
}

// Usage dans preprocess
$variables['card_image'] = ps_theme_get_image_url($image_fid, 'card_thumbnail');
```

---

### 2. Formater date locale français

```php
use Drupal\Core\Datetime\DrupalDateTime;

function ps_theme_format_date($timestamp, $format = 'd/m/Y') {
  $date = DrupalDateTime::createFromTimestamp($timestamp);
  return $date->format($format);
}

// Usage dans preprocess
$variables['formatted_date'] = ps_theme_format_date($node->getCreatedTime(), 'd/m/Y à H:i');
```

---

### 3. Extraire premier paragraphe body

```php
function ps_theme_extract_first_paragraph($html) {
  $text = strip_tags($html, '<p>');
  preg_match('/<p>(.*?)<\/p>/', $text, $matches);
  return $matches[1] ?? strip_tags($html);
}

// Usage dans preprocess
$variables['card_description'] = ps_theme_extract_first_paragraph($node->body->value);
```

---

### 4. Mapper taxonomy term → Badge variant

```php
function ps_theme_map_term_to_variant($term_id) {
  // Mapping statique ou config
  $mapping = [
    1 => 'primary',    // Actualités
    2 => 'secondary',  // Conseils
    3 => 'success',    // Succès client
    4 => 'info',       // Guides
  ];
  
  return $mapping[$term_id] ?? 'neutral';
}

// Usage dans preprocess
$category = $node->get('field_category')->entity;
$variables['card_badge_variant'] = ps_theme_map_term_to_variant($category->id());
```

---

## 🚨 Pièges courants

### 1. Oublier vérifier champ existe

```php
// ❌ Fatal error si field_image n'existe pas sur node type
$image = $node->get('field_image')->first();

// ✅ Vérifier avec hasField() + isEmpty()
if ($node->hasField('field_image') && !$node->get('field_image')->isEmpty()) {
  $image = $node->get('field_image')->first();
}
```

---

### 2. Charger entité dans boucle (N+1)

```php
// ❌ Requête SQL par item (lent si 100+ nodes)
foreach ($nodes as $node) {
  $category = $node->get('field_category')->entity; // Load SQL
}

// ✅ Charger toutes entités une fois (entityQuery + load multiple)
$category_ids = [];
foreach ($nodes as $node) {
  if (!$node->get('field_category')->isEmpty()) {
    $category_ids[] = $node->get('field_category')->target_id;
  }
}
$categories = \Drupal::entityTypeManager()
  ->getStorage('taxonomy_term')
  ->loadMultiple(array_unique($category_ids));
```

---

### 3. Oublier traduire strings

```php
// ❌ Texte hardcodé (pas translatable)
$variables['card_cta_text'] = 'Read more';

// ✅ Utiliser t() (translatable)
$variables['card_cta_text'] = t('Read more');
```

---

### 4. Modifier $variables sans références

```php
// ❌ Modifications perdues (pas de référence &)
function ps_theme_preprocess_node($variables) {
  $variables['card_title'] = 'Test'; // Perdu
}

// ✅ Passer par référence &$variables
function ps_theme_preprocess_node(&$variables) {
  $variables['card_title'] = 'Test'; // Persisté
}
```

---

## 📋 Checklist preprocess

### Avant de coder

- [ ] Identifier données source (node fields, taxonomy, date)
- [ ] Identifier composant cible (card, badge, button)
- [ ] Lister props composant requis (README.md)
- [ ] Déterminer hook approprié (node, field, menu, page)

### Dans le hook

- [ ] Vérifier champs existent (`hasField()`, `isEmpty()`)
- [ ] Mapper données → variables Twig (noms explicites : `card_title` vs `title`)
- [ ] Attacher libraries (`$variables['#attached']['library'][]`)
- [ ] Ajouter classes BEM (`$variables['attributes']['class'][]`)
- [ ] Traduire strings (`t()`)
- [ ] Gérer cas vides (valeurs par défaut)

### Tests

- [ ] Clear cache (`drush cr`)
- [ ] Vérifier variables disponibles (Twig debug, `dump()`)
- [ ] Tester avec données réelles (node créé)
- [ ] Tester cas vides (champ vide, image manquante)
- [ ] Vérifier performance (pas de N+1 queries)

---

## 🎯 Exemple complet : ps.theme

```php
<?php

/**
 * @file
 * Preprocess functions et hooks pour PS Theme
 */

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * Implements hook_preprocess_page().
 */
function ps_theme_preprocess_page(&$variables) {
  // Classes body dynamiques
  $attributes = &$variables['attributes'];

  if (\Drupal::service('path.matcher')->isFrontPage()) {
    $attributes['class'][] = 'page-front';
  }

  if (\Drupal::currentUser()->isAuthenticated()) {
    $attributes['class'][] = 'user-logged-in';
  }

  // Attacher libraries conditionnellement
  if (!empty($variables['page']['header'])) {
    $variables['#attached']['library'][] = 'ps/header';
  }
  if (!empty($variables['page']['footer'])) {
    $variables['#attached']['library'][] = 'ps/footer';
  }
}

/**
 * Implements hook_preprocess_node().
 */
function ps_theme_preprocess_node(&$variables) {
  $node = $variables['node'];
  $view_mode = $variables['view_mode'];

  // View mode teaser/card → Composant Card
  if ($view_mode === 'teaser' || $view_mode === 'card') {
    $variables['#attached']['library'][] = 'ps/card';

    // Title
    $variables['card_title'] = $node->getTitle();

    // Description (body tronqué)
    if ($node->hasField('body') && !$node->get('body')->isEmpty()) {
      $body = strip_tags($node->get('body')->value);
      $variables['card_description'] = mb_strlen($body) > 150 
        ? mb_substr($body, 0, 147) . '...' 
        : $body;
    }

    // Image
    if ($node->hasField('field_image') && !$node->get('field_image')->isEmpty()) {
      $image_field = $node->get('field_image')->first();
      $file = File::load($image_field->target_id);
      if ($file) {
        $image_style = ImageStyle::load('card_thumbnail');
        $variables['card_image'] = $image_style 
          ? $image_style->buildUrl($file->getFileUri())
          : file_create_url($file->getFileUri());
        $variables['card_image_alt'] = $image_field->alt ?? $node->getTitle();
      }
    }

    // Badge (category)
    if ($node->hasField('field_category') && !$node->get('field_category')->isEmpty()) {
      $category = $node->get('field_category')->entity;
      if ($category) {
        $variables['card_badge_text'] = $category->getName();
        $variables['card_badge_variant'] = 'primary';
      }
    }

    // Eyebrow (date)
    $created = $node->getCreatedTime();
    $date = DrupalDateTime::createFromTimestamp($created);
    $variables['card_eyebrow'] = $date->format('d/m/Y');

    // CTA
    $variables['card_cta_text'] = t('Read more');
    $variables['card_cta_href'] = $node->toUrl()->toString();

    // Classes BEM
    $variables['attributes']['class'][] = 'node';
    $variables['attributes']['class'][] = 'node--' . $node->bundle();
    $variables['attributes']['class'][] = 'node--' . $view_mode;
  }

  // View mode full
  if ($view_mode === 'full') {
    $variables['#attached']['library'][] = 'ps/heading';
    $variables['#attached']['library'][] = 'ps/badge';
  }
}

/**
 * Implements hook_preprocess_field().
 */
function ps_theme_preprocess_field(&$variables) {
  $field_name = $variables['field_name'];
  $field_type = $variables['field_type'];

  // Taxonomy terms → Badges
  if ($field_type === 'entity_reference' && $field_name === 'field_tags') {
    $variables['#attached']['library'][] = 'ps/badge';

    $badge_items = [];
    foreach ($variables['items'] as $delta => $item) {
      $term = $item['content']['#plain_text'] ?? '';
      if ($term) {
        $badge_items[] = [
          'text' => $term,
          'variant' => 'secondary',
        ];
      }
    }
    $variables['badge_items'] = $badge_items;
    $variables['attributes']['class'][] = 'field-badges';
  }

  // Dates → Format français
  if ($field_type === 'datetime') {
    foreach ($variables['items'] as $delta => $item) {
      if (isset($item['content']['#date']) && $item['content']['#date'] instanceof DrupalDateTime) {
        $variables['items'][$delta]['formatted_date'] = $item['content']['#date']->format('d/m/Y à H:i');
      }
    }
  }
}

/**
 * Implements hook_preprocess_menu().
 */
function ps_theme_preprocess_menu(&$variables) {
  $menu_name = $variables['menu_name'] ?? '';

  if ($menu_name === 'main') {
    $variables['#attached']['library'][] = 'ps/menu';
    $variables['#attached']['library'][] = 'ps/icon';
    ps_theme_add_menu_icons($variables['items']);
  }
}

/**
 * Helper : Ajouter icons menu récursivement
 */
function ps_theme_add_menu_icons(&$items) {
  foreach ($items as &$item) {
    if (!empty($item['below'])) {
      $item['icon'] = 'chevron-down';
      $item['has_submenu'] = TRUE;
      ps_theme_add_menu_icons($item['below']);
    }
    if ($item['in_active_trail']) {
      $item['attributes']->addClass('is-active');
    }
  }
}

/**
 * Implements hook_preprocess_input().
 */
function ps_theme_preprocess_input(&$variables) {
  $element = $variables['element'];
  
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
    
    if (!empty($element['#id'])) {
      $variables['attributes']['aria-describedby'] = $element['#id'] . '-error';
    }
  }
  
  $variables['attributes']['class'][] = 'ps-input';
  if (!empty($element['#disabled'])) {
    $variables['attributes']['class'][] = 'ps-input--disabled';
  }
}

/**
 * Implements hook_preprocess_select().
 */
function ps_theme_preprocess_select(&$variables) {
  $element = $variables['element'];
  
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
  
  $variables['attributes']['class'][] = 'ps-select__input';
}

/**
 * Implements hook_preprocess_textarea().
 */
function ps_theme_preprocess_textarea(&$variables) {
  $element = $variables['element'];
  
  if (!empty($element['#errors'])) {
    $variables['attributes']['data-error'] = TRUE;
    $variables['attributes']['aria-invalid'] = 'true';
  }
  
  $variables['attributes']['class'][] = 'ps-textarea';
}
```

---

## 🎯 Prochaines étapes

**Créer ps.theme** :
1. Copier exemples preprocess ci-dessus
2. Adapter selon composants utilisés
3. Créer helpers utilitaires (images, dates, taxonomy)
4. Tester avec clear cache (`drush cr`)

**Poursuivre avec** :
- **[Déploiement](./06-deploiement.md)** → Build production + cache + CI/CD

---

**Navigation** : [← Forms](./04-drupal-forms.md) | [README](./README.md) | [Déploiement →](./06-deploiement.md)
