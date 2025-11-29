# PS Divider

Séparateur visuel pour délimiter des sections de contenu. Supporte orientations horizontale et verticale, variantes de style (solid, dashed, dotted), épaisseur, espacement, et couleur. Peut inclure du texte ou une icône au centre.

## Structure
- `divider.twig` : Template principal avec support complet des variants
- `divider.css` : Styles BEM avec tokens uniquement
- `divider.yml` : Données par défaut
- `divider.stories.jsx` : Stories Storybook complètes avec tous les variants
- `README.md` : Documentation composant

## Props

| Prop | Type | Valeurs | Default | Description |
|------|------|---------|---------|-------------|
| `orientation` | string | `horizontal`, `vertical` | `horizontal` | Orientation du séparateur |
| `style` | string | `solid`, `dashed`, `dotted` | `solid` | Style de ligne |
| `thickness` | string | `thin`, `medium`, `thick` | `medium` | Épaisseur de ligne |
| `color` | string | `neutral`, `primary`, `secondary`, `success`, `warning`, `danger`, `info` | `neutral` | Couleur de ligne |
| `spacing` | string | `sm`, `md`, `lg` | `md` | Espacement autour du divider |
| `text` | string | - | `''` | Texte central optionnel |
| `icon` | string | - | `''` | Nom d'icône centrale optionnelle |

## BEM

### Block
- `ps-divider`

### Modificateurs
**Orientation:**
- `ps-divider--horizontal` (défaut)
- `ps-divider--vertical`

**Style:**
- `ps-divider--solid` (défaut)
- `ps-divider--dashed`
- `ps-divider--dotted`

**Épaisseur:**
- `ps-divider--thin` (1px)
- `ps-divider--medium` (2px, défaut)
- `ps-divider--thick` (4px)

**Couleur:**
- `ps-divider--neutral` (gray-300, défaut)
- `ps-divider--primary` (green #00915A - brand primary)
- `ps-divider--secondary` (purple #E0388C - brand secondary)
- `ps-divider--success` (green-600)
- `ps-divider--warning` (yellow-500)
- `ps-divider--danger` (red-600)
- `ps-divider--info` (blue-600)

**Espacement:**
- `ps-divider--spacing-sm` (8px)
- `ps-divider--spacing-md` (16px, défaut)
- `ps-divider--spacing-lg` (24px)

**Contenu:**
- `ps-divider--with-text`
- `ps-divider--with-icon`

### Elements (contenu central)
- `ps-divider__line` : Ligne de chaque côté du contenu
- `ps-divider__text` : Texte central
- `ps-divider__icon` : Icône centrale

## Design Tokens

### Couleurs
- Neutral : `--ps-color-neutral-300` fallback `--gray-300`
- Primary : `--brand-primary` (green #00915A)
- Secondary : `--brand-secondary` (purple #E0388C)
- Success : `--btn-success` fallback `--green-600`
- Warning : `--btn-warning` fallback `--yellow-500`
- Danger : `--btn-danger` fallback `--red-600`
- Info : `--btn-info` fallback `--blue-600`
- Texte : `--ps-color-neutral-600` fallback `--gray-600`
- Icône : `--ps-color-neutral-500` fallback `--gray-500`

### Espacements
- Small : `--size-2` (8px)
- Medium : `--size-4` (16px)
- Large : `--size-6` (24px)
- Gap contenu : `--size-3` (12px)

### Typographie (texte central)
- Font : `--font-sans` (BNPP Sans)
- Size : `--font-size-0` (14px)
- Weight : `--font-weight-500` (medium)
- Icon size : `--font-size-1` (16px)

### Épaisseurs
- Thin : 1px
- Medium : 2px
- Thick : 4px

## Exemples d'usage

### Simple horizontal
```twig
{% include '@elements/divider/divider.twig' %}
```

### Avec style et couleur
```twig
{% include '@elements/divider/divider.twig' with {
  style: 'dashed',
  color: 'primary',
  thickness: 'thick'
} %}
```

### Avec texte central
```twig
{% include '@elements/divider/divider.twig' with {
  text: 'ou',
  color: 'secondary'
} %}
```

### Vertical dans un flex
```html
<div style="display: flex; align-items: center;">
  <span>Option 1</span>
  {% include '@elements/divider/divider.twig' with {
    orientation: 'vertical',
    thickness: 'thin',
    spacing: 'sm'
  } %}
  <span>Option 2</span>
</div>
```

### Avec icône
```twig
{% include '@elements/divider/divider.twig' with {
  icon: 'star',
  style: 'dashed'
} %}
```

## Cas d'usage typiques

### 1. Séparation de sections
```twig
<section>Contenu section 1</section>
{% include '@elements/divider/divider.twig' with {
  spacing: 'lg',
  thickness: 'thin'
} %}
<section>Contenu section 2</section>
```

### 2. Formulaire login avec alternatives
```twig
<form>
  <!-- Champs email/password -->
  <button>Se connecter</button>
  {% include '@elements/divider/divider.twig' with {
    text: 'ou',
    spacing: 'md'
  } %}
  <button>Continuer avec Google</button>
</form>
```

### 3. Séparateur vertical dans toolbar
```html
<div class="toolbar">
  <button>Éditer</button>
  {% include '@elements/divider/divider.twig' with {
    orientation: 'vertical',
    thickness: 'thin',
    spacing: 'sm'
  } %}
  <button>Supprimer</button>
</div>
```

### 4. Emphase avec couleur primaire
```twig
<h2>Section importante</h2>
{% include '@elements/divider/divider.twig' with {
  color: 'primary',
  thickness: 'thick',
  spacing: 'md'
} %}
<div>Contenu important...</div>
```

## Accessibilité

- `<hr>` natif pour séparateurs horizontaux simples (sémantique HTML5)
- `role="separator"` avec `aria-orientation="vertical"` pour verticaux
- Élément non-interactif : pas de focus
- Texte/icône central : décoratif, `aria-hidden="true"` sur icône

## Comportement responsive

- **Horizontal** : Largeur 100% (block), s'adapte au conteneur
- **Vertical** : Hauteur héritée du conteneur parent (inline-block ou flex)
- Espacement adapté via modificateurs (`sm`/`md`/`lg`)
- Contenu central : white-space nowrap pour éviter retour ligne

## Storybook Stories

### Variants individuels
- `Default` : Configuration par défaut
- `Dashed`, `Dotted` : Styles de ligne
- `Thin`, `Thick` : Épaisseurs
- `Primary`, `Secondary` : Couleurs
- `WithText`, `WithTextPrimary`, `WithTextDashed` : Avec texte
- `WithIcon` : Avec icône
- `Vertical`, `VerticalThin` : Orientations verticales

### Stories composées
- `AllStyles` : Showcase complet de tous les styles, épaisseurs, couleurs, contenu
- `UseCases` : Cas d'usage réels (formulaire login, sections, emphase)

## Notes d'implémentation

- **Tokens uniquement** : Aucune valeur en dur, respect strict design system
- **BEM strict** : Préfixe `ps-`, nomenclature cohérente
- **Flexibilité** : 7 props pour 100+ combinaisons possibles
- **Sémantique** : `<hr>` pour horizontal simple, `role="separator"` pour vertical
- **Performance** : CSS pur, pas de JavaScript
- **Maintenabilité** : Structure modulaire, variants extensibles

## Pixel perfect

Conformité stricte aux tokens design :
- Épaisseurs exactes (1px, 2px, 4px)
- Couleurs de la palette définie
- Espacements de la grille 8px
- Typographie centrée cohérente avec système
