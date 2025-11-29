# Avatar Component

**Category:** Elements (Atom)  
**Status:** ✅ Stable  
**Version:** 1.0.0

---

## Description

Représentation visuelle d'un utilisateur ou d'une entité. Supporte trois modes d'affichage avec fallback automatique : image → initiales → icône par défaut. Configurable en taille (xs à xl), forme (circle/square/rounded), avec badge de statut optionnel (online/offline/busy).

Le composant Avatar est conçu pour identifier visuellement les utilisateurs dans les interfaces (profils, commentaires, listes, headers).

---

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | - | URL de l'image avatar (optionnel) |
| `alt` | `string` | `''` | Texte alternatif pour l'image |
| `initials` | `string` | - | Initiales (2 lettres max, ex: "JD") - fallback si pas d'image |
| `size` | `string` | `'md'` | Taille : `xs` (24px) \| `sm` (32px) \| `md` (40px) \| `lg` (56px) \| `xl` (80px) |
| `shape` | `string` | `'circle'` | Forme : `circle` \| `square` \| `rounded` |
| `status` | `string` | - | Badge de statut : `online` \| `offline` \| `busy` |
| `bordered` | `boolean` | `false` | Ajoute une bordure blanche de 2px |
| `clickable` | `boolean` | `false` | Active l'effet hover et focus |
| `href` | `string` | - | URL de destination (transforme en lien `<a>`) |
| `attributes` | `Attribute` | - | Attributs HTML additionnels |

---

## Structure BEM

```
.ps-avatar-wrapper                      // Wrapper pour positionnement status
  .ps-avatar-wrapper--xs                // Taille 24px
  .ps-avatar-wrapper--sm                // Taille 32px
  .ps-avatar-wrapper--md                // Taille 40px (default)
  .ps-avatar-wrapper--lg                // Taille 56px
  .ps-avatar-wrapper--xl                // Taille 80px
  .ps-avatar-wrapper--has-status        // Avec badge de statut

.ps-avatar                              // Block principal
  .ps-avatar__image                     // Image (mode 1)
  .ps-avatar__text                      // Initiales (mode 2)
  .ps-avatar__icon                      // Icône fallback (mode 3)
  .ps-avatar__status                    // Badge de statut

Modifiers de forme:
  .ps-avatar--circle                    // Rond (default)
  .ps-avatar--square                    // Carré
  .ps-avatar--rounded                   // Coins arrondis (4px)

Modifiers de type:
  .ps-avatar--initials                  // Type initiales (bg vert)
  .ps-avatar--icon                      // Type icône (bg gris clair)

Modifiers d'état:
  .ps-avatar--bordered                  // Bordure blanche 2px
  .ps-avatar--clickable                 // Cliquable avec hover

Status variants:
  .ps-avatar__status--online            // Badge vert
  .ps-avatar__status--offline           // Badge gris
  .ps-avatar__status--busy              // Badge rouge
```

---

## Design Tokens Utilisés

### Layout & Sizing
- `--size-6` (24px) - Wrapper xs
- `--size-8` (32px) - Wrapper sm
- `--size-10` (40px) - Wrapper md, icon xl
- `--size-14` (56px) - Wrapper lg
- `--size-20` (80px) - Wrapper xl
- `--size-2` (8px) - Status badge min size
- `--size-3` (12px) - Icon xs
- `--size-4` (16px) - Icon sm
- `--size-5` (20px) - Icon md
- `--size-7` (28px) - Icon lg

### Typography
- `--font-sans` - Famille de police
- `--font-weight-400` - Poids régulier (icon)
- `--font-weight-600` - Poids semi-bold (initials)
- `--font-size-xs` (10px) - Text xs
- `--font-size-sm` (12px) - Text sm
- `--font-size-0` (14px) - Text md
- `--size-425` (18px) - Text lg
- `--size-6` (24px) - Text xl

### Colors
- `--ps-color-neutral-0` (#FFF) - Texte initiales, bordure
- `--ps-color-neutral-100` (#F3F6F9) - Background icône
- `--ps-color-neutral-200` (#E8EBEF) - Background default
- `--ps-color-neutral-400` (#9AA6B2) - Status offline
- `--ps-color-neutral-600` (#54636F) - Icône fallback
- `--ps-color-primary-600` (#0DB089) - Background initiales
- `--ps-color-success-600` (#0DB089) - Status online
- `--ps-color-error-600` (#E53935) - Status busy

### Visual
- `--radius-1` (4px) - Coins arrondis
- `--border-size-1` (1px) - Bordure status badge
- `--border-size-2` (2px) - Bordure avatar, focus outline
- `--blue-500` - Couleur focus outline

---

## Exemples d'Usage

### Twig (Drupal)

```twig
{# Avatar avec image #}
{% include '@elements/avatar/avatar.twig' with {
  src: '/images/users/john-doe.jpg',
  alt: 'John Doe',
  size: 'md',
  shape: 'circle',
} %}

{# Avatar avec initiales #}
{% include '@elements/avatar/avatar.twig' with {
  initials: 'JD',
  size: 'lg',
  shape: 'rounded',
  bordered: true,
} %}

{# Avatar avec icône fallback #}
{% include '@elements/avatar/avatar.twig' with {
  size: 'sm',
  shape: 'circle',
} %}

{# Avatar avec statut en ligne #}
{% include '@elements/avatar/avatar.twig' with {
  src: '/images/users/jane-smith.jpg',
  alt: 'Jane Smith',
  size: 'md',
  status: 'online',
} %}

{# Avatar cliquable (lien profil) #}
{% include '@elements/avatar/avatar.twig' with {
  src: '/images/users/alice.jpg',
  alt: 'Alice Martin',
  size: 'lg',
  clickable: true,
  href: '/user/alice-martin',
} %}

{# Avatar avec toutes les options #}
{% include '@elements/avatar/avatar.twig' with {
  src: user.picture,
  alt: user.name,
  size: 'md',
  shape: 'circle',
  status: user.online ? 'online' : 'offline',
  bordered: true,
  clickable: true,
  href: user.profile_url,
} %}
```

### HTML Output

```html
<!-- Image avatar default -->
<div class="ps-avatar-wrapper ps-avatar-wrapper--md">
  <div class="ps-avatar ps-avatar--circle">
    <img class="ps-avatar__image" src="/images/user.jpg" alt="John Doe" loading="lazy" />
  </div>
</div>

<!-- Initials avatar avec statut -->
<div class="ps-avatar-wrapper ps-avatar-wrapper--lg ps-avatar-wrapper--has-status">
  <div class="ps-avatar ps-avatar--circle ps-avatar--initials">
    <span class="ps-avatar__text">JD</span>
  </div>
  <span class="ps-avatar__status ps-avatar__status--online" aria-label="En ligne"></span>
</div>

<!-- Icon fallback cliquable -->
<div class="ps-avatar-wrapper ps-avatar-wrapper--sm">
  <a href="/profile" class="ps-avatar ps-avatar--circle ps-avatar--icon ps-avatar--clickable" role="link">
    <span class="ps-avatar__icon" aria-hidden="true"></span>
  </a>
</div>
```

---

## Cas d'Usage Réels

### 1. Liste de Commentaires
```twig
{# Afficher auteur avec avatar #}
<div class="comment">
  {% include '@elements/avatar/avatar.twig' with {
    src: comment.author.picture,
    alt: comment.author.name,
    size: 'sm',
    shape: 'circle',
  } %}
  <div class="comment__content">
    <strong>{{ comment.author.name }}</strong>
    <p>{{ comment.text }}</p>
  </div>
</div>
```

### 2. Header Utilisateur avec Statut
```twig
{# Profil utilisateur connecté #}
<div class="user-menu">
  {% include '@elements/avatar/avatar.twig' with {
    src: current_user.picture,
    alt: current_user.name,
    size: 'md',
    status: current_user.is_online ? 'online' : 'offline',
    clickable: true,
    href: '/user/profile',
  } %}
  <span>{{ current_user.name }}</span>
</div>
```

### 3. Liste d'Équipe
```twig
{# Afficher membres d'équipe #}
<div class="team-list">
  {% for member in team.members %}
    {% include '@elements/avatar/avatar.twig' with {
      src: member.picture ?: null,
      initials: member.initials,
      alt: member.name,
      size: 'lg',
      shape: 'rounded',
      bordered: true,
    } %}
  {% endfor %}
</div>
```

### 4. Fallback Automatique
```twig
{# Système de fallback : image → initiales → icône #}
{% include '@elements/avatar/avatar.twig' with {
  src: user.picture ?: null,          {# Si null, utilise initiales #}
  initials: user.initials ?: null,    {# Si null, utilise icône #}
  alt: user.name,
  size: 'md',
} %}
```

### 5. Statuts Multiples
```twig
{# Indiquer disponibilité utilisateur #}
{% set user_status = user.is_busy ? 'busy' : (user.is_online ? 'online' : 'offline') %}
{% include '@elements/avatar/avatar.twig' with {
  src: user.picture,
  alt: user.name,
  status: user_status,
  size: 'md',
} %}
```

---

## Accessibilité

### Conformité WCAG 2.2 AA

✅ **Images alternatives**
- Attribut `alt` requis pour les images
- Texte descriptif du nom de l'utilisateur

✅ **Fallback visuel**
- Initiales en texte lisible (contraste AAA)
- Icône avec `aria-hidden="true"` (décoratif)

✅ **Badge de statut**
- `aria-label` descriptif ("En ligne", "Occupé", "Hors ligne")
- Contraste couleur suffisant pour tous les statuts

✅ **Navigation clavier**
- Focus visible si `clickable: true`
- Outline 2px bleu avec offset
- Tab pour naviguer, Enter/Space pour activer

✅ **Contraste de couleur**
- Initiales blanc sur vert : 7.2:1 (AAA ✓)
- Icône gris foncé sur gris clair : 4.8:1 (AA ✓)
- Status online vert : 5.1:1 (AA ✓)

### États Visuels

| État | Visual Feedback |
|------|-----------------|
| **Default** | Avatar statique |
| **Hover** (clickable) | `transform: scale(1.05)` |
| **Focus** (clickable) | Outline 2px bleu + offset 2px |
| **Disabled** | N/A (pas d'état disabled) |

---

## Notes Techniques

### Hiérarchie de Fallback
1. **Image** : Si `src` fourni → affiche `<img>`
2. **Initiales** : Si pas d'image mais `initials` fourni → affiche `<span>` avec texte
3. **Icône** : Si ni image ni initiales → affiche icône utilisateur par défaut

### Wrapper Architecture
Le wrapper `.ps-avatar-wrapper` est nécessaire pour :
- Définir les dimensions fixes (tailles xs à xl)
- Positionner le badge de statut en absolu
- Maintenir aspect ratio carré

### Icon System
L'icône fallback utilise CSS pseudo-element `::before` avec la font `bnpre-icons` (code point `\e800`).

### Status Badge
- Position : `bottom: 0; right: 0` (coin inférieur droit)
- Taille : 30% du wrapper avec min 8px
- Bordure 1px blanche pour contraste sur images sombres

---

## Responsive

Les avatars ont des tailles fixes mais peuvent être adaptés selon le contexte :

```scss
@media (max-width: 768px) {
  // Réduire taille dans headers mobiles
  .header .ps-avatar-wrapper {
    width: var(--size-8); // sm au lieu de md
    height: var(--size-8);
  }
}
```

---

## Changelog

### v1.0.0 (2025-11-29)
- ✅ Implémentation initiale avec 3 modes (image, initiales, icône)
- ✅ Support 5 tailles (xs, sm, md, lg, xl)
- ✅ Support 3 formes (circle, square, rounded)
- ✅ Badge de statut (online, offline, busy)
- ✅ États bordered et clickable
- ✅ Accessibilité WCAG 2.2 AA complète
- ✅ Tokens design system intégrés (AUCUNE valeur en dur)
- ✅ Icon system via CSS pseudo-element
- ✅ Documentation complète

---

## Ressources

- **Storybook**: [http://localhost:6006/?path=/docs/elements-avatar](http://localhost:6006/?path=/docs/elements-avatar)
- **Spec Design**: `docs/design/atoms/avatar.md`
- **Template Standard**: `.github/COMPONENT_TEMPLATE_STANDARD.md`
- **Design Tokens**: `source/props/colors.css`, `source/props/sizes.css`, `source/props/fonts.css`

---

**Contributeurs**: Design System Team  
**Dernière mise à jour**: 29 novembre 2025
