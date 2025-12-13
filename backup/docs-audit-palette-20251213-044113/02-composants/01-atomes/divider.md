# Divider (Atom)

**Niveau Atomic Design** : Atom / Layout  
**Catégorie** : Separator  
**Statut** : 🚧 Draft  
**Version** : 1.0.0

---

## 📋 Description

Séparateur visuel pour délimiter des sections de contenu. Disponible en orientations horizontale et verticale, avec variantes de style (solid, dashed, dotted), épaisseur, espacement, et couleur. Peut inclure du texte ou une icône au centre. Rôle sémantique via `role="separator"` ou simple élément `<hr>`.

---

## 🎨 Aperçu visuel

```
──────────────────────  Horizontal
│                       Vertical
│
──── Texte ────         Avec label
```

---

## 🏗️ Structure BEM

```html
<!-- Simple horizontal divider -->
<hr class="ps-divider ps-divider--horizontal ps-divider--solid" />

<!-- Vertical divider -->
<span class="ps-divider ps-divider--vertical ps-divider--solid" role="separator" aria-orientation="vertical"></span>

<!-- Divider with text -->
<div class="ps-divider ps-divider--horizontal ps-divider--with-text">
  <span class="ps-divider__line"></span>
  <span class="ps-divider__text">ou</span>
  <span class="ps-divider__line"></span>
</div>

<!-- Divider with icon -->
<div class="ps-divider ps-divider--horizontal ps-divider--with-icon">
  <span class="ps-divider__line"></span>
  <span class="ps-divider__icon" data-icon="star" aria-hidden="true"></span>
  <span class="ps-divider__line"></span>
</div>
```
```scss
.ps-divider {
  // Variables composant (Layer 2)
  --divider-thickness: var(--border-size-2);
  --divider-spacing-y: var(--size-4);
  --divider-spacing-x: var(--size-4);
  --divider-content-gap: var(--size-3);
  --divider-color: var(--border-default);
  --divider-text-color: var(--text-secondary);
  --divider-icon-color: var(--text-secondary);
  --divider-text-font-family: var(--font-sans);
  --divider-text-font-size: var(--font-size-0);
  --divider-text-font-weight: var(--font-weight-500);
  --divider-icon-size: var(--font-size-1);
  --divider-style: solid;

  // Base horizontale
  border: none;
  background: none;
  margin: var(--divider-spacing-y) 0;
  padding: 0;
  display: block;
  height: 0;
  border-top: var(--divider-thickness) var(--divider-style) var(--divider-color);

  // Orientation verticale
  &--vertical {
    display: inline-block;
    width: 0;
    height: 100%;
    margin: 0 var(--divider-spacing-x);
    border-top: 0;
    border-left: var(--divider-thickness) var(--divider-style) var(--divider-color);
    vertical-align: middle;
  }

  // Styles de ligne
  &--dashed { --divider-style: dashed; }
  &--dotted { --divider-style: dotted; }

  // Épaisseurs
  &--thin { --divider-thickness: var(--border-size-1); }
  &--thick { --divider-thickness: var(--border-size-4); }

  // Couleurs sémantiques
  &--primary { --divider-color: var(--primary); }
  &--secondary { --divider-color: var(--secondary); }
  &--success { --divider-color: var(--success); }
  &--warning { --divider-color: var(--warning); }
  &--danger { --divider-color: var(--danger); }
  &--info { --divider-color: var(--info); }
  &--neutral { --divider-color: var(--border-default); }

  // Espacement
  &--spacing-sm { --divider-spacing-y: var(--size-2); --divider-spacing-x: var(--size-2); }
  &--spacing-lg { --divider-spacing-y: var(--size-6); --divider-spacing-x: var(--size-6); }

  // Contenu centré (texte ou icône)
  &--with-text,
  &--with-icon {
    display: flex;
    align-items: center;
    gap: var(--divider-content-gap);
    border: none;
    height: auto;
  }

  &__line {
    flex: 1;
    height: 0;
    border-top: var(--divider-thickness) var(--divider-style) var(--divider-color);
  }

  &__text {
    font-family: var(--divider-text-font-family);
    font-size: var(--divider-text-font-size);
    font-weight: var(--divider-text-font-weight);
    color: var(--divider-text-color);
    white-space: nowrap;
    flex-shrink: 0;
  }

  &__icon {
    font-size: var(--divider-icon-size);
    color: var(--divider-icon-color);
    flex-shrink: 0;
    line-height: 1;
  }
}
```
    {% elseif icon %}
      <span class="ps-divider__icon" data-icon="{{ icon }}" aria-hidden="true"></span>
    {% endif %}
    <span class="ps-divider__line"></span>
  </div>
{% else %}
  {% if orientation == 'horizontal' %}
    <hr {{ attributes.addClass(root_classes) }} />
  {% else %}
    <span {{ attributes.addClass(root_classes) }} role="separator" aria-orientation="vertical"></span>
  {% endif %}
{% endif %}
```

---

## 🎨 Styles SCSS

```scss
.ps-divider {
  // Variables composant (Layer 2)
  --divider-thickness: var(--border-size-2);
  --divider-spacing-y: var(--size-4);
  --divider-spacing-x: var(--size-4);
  --divider-content-gap: var(--size-3);
  --divider-color: var(--border-default);
  --divider-text-color: var(--text-secondary);
  --divider-icon-color: var(--text-secondary);
  --divider-text-font-family: var(--font-sans);
  --divider-text-font-size: var(--font-size-0);
  --divider-text-font-weight: var(--font-weight-500);
  --divider-icon-size: var(--font-size-1);
  --divider-style: solid;

  // Base horizontale
  border: none;
  background: none;
  margin: var(--divider-spacing-y) 0;
  padding: 0;
  display: block;
  height: 0;
  border-top: var(--divider-thickness) var(--divider-style) var(--divider-color);

  // Orientation verticale
  &--vertical {
    display: inline-block;
    width: 0;
    height: 100%;
    margin: 0 var(--divider-spacing-x);
    border-top: 0;
    border-left: var(--divider-thickness) var(--divider-style) var(--divider-color);
    vertical-align: middle;
  }

  // Styles de ligne
  &--dashed { --divider-style: dashed; }
  &--dotted { --divider-style: dotted; }

  // Épaisseurs
  &--thin { --divider-thickness: var(--border-size-1); }
  &--thick { --divider-thickness: var(--border-size-4); }

  // Couleurs sémantiques
  &--primary { --divider-color: var(--primary); }
  &--secondary { --divider-color: var(--secondary); }
  &--success { --divider-color: var(--success); }
  &--warning { --divider-color: var(--warning); }
  &--danger { --divider-color: var(--danger); }
  &--info { --divider-color: var(--info); }
  &--neutral { --divider-color: var(--border-default); }

  // Espacement
  &--spacing-sm { --divider-spacing-y: var(--size-2); --divider-spacing-x: var(--size-2); }
  &--spacing-lg { --divider-spacing-y: var(--size-6); --divider-spacing-x: var(--size-6); }

  // Contenu centré (texte ou icône)
  &--with-text,
  &--with-icon {
    display: flex;
    align-items: center;
    gap: var(--divider-content-gap);
    border: none;
    height: auto;
  }

  &__line {
    flex: 1;
    height: 0;
    border-top: var(--divider-thickness) var(--divider-style) var(--divider-color);
  }

  &__text {
    font-family: var(--divider-text-font-family);
    font-size: var(--divider-text-font-size);
    font-weight: var(--divider-text-font-weight);
    color: var(--divider-text-color);
    white-space: nowrap;
    flex-shrink: 0;
  }

  &__icon {
    font-size: var(--divider-icon-size);
    color: var(--divider-icon-color);
    flex-shrink: 0;
    line-height: 1;
  }
}
```

---

## ♿ Accessibilité

- `<hr>` natif pour séparateurs horizontaux simples (sémantique).
- `role="separator"` avec `aria-orientation="vertical"` pour verticaux.
- Pas de focus : élément non-interactif.
- Texte/icône central décoratif : pas d'`aria-label` requis.

---

## 📱 Comportement responsive

- Horizontal : largeur 100% (block).
- Vertical : hauteur héritée du conteneur parent (inline-block ou flex).
- Espacement adapté via modificateurs.

---

## 🧪 Exemples d'usage

```twig
{# Simple horizontal divider #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'horizontal',
  style: 'solid',
  thickness: 'medium',
  color: 'neutral',
  spacing: 'md'
} %}

{# Vertical divider #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'vertical',
  style: 'solid',
  thickness: 'thin',
  color: 'neutral',
  spacing: 'sm'
} %}

{# Divider with text #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'horizontal',
  text: 'ou',
  style: 'solid',
  color: 'secondary'
} %}

{# Divider with icon #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  orientation: 'horizontal',
  icon: 'star',
  style: 'dashed',
  color: 'primary'
} %}

{# Thick primary divider #}
{% include '@ps_theme/ps-divider/ps-divider.twig' with {
  thickness: 'thick',
  color: 'primary',
  spacing: 'lg'
} %}
```

---

## 📚 Ressources

- Figma: Extensive occurrences (section separators)
- Tokens: `/design/tokens/colors.yml`, `/design/tokens/spacing.yml`, `/design/tokens/borders.yml`, `/design/tokens/typography.yml`
