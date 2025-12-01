# Avatar

User or entity visual representation with automatic fallback hierarchy.

## Overview
- **Purpose**: Identify users/entities visually in profiles, comments, lists, headers.
- **Display modes**: image → initials → gender icon (automatic fallback).
- **Sizes**: xs (24px), sm (32px), md (40px, default), lg (48px), xl (80px).
- **Shapes**: circle (default), square, rounded (adaptive radius scaling).
- **Status badge**: online (green), offline (gray), busy (red) at bottom-right.
- **Border**: optional white outline for dark backgrounds.
- **Interactive**: clickable variant with hover scale and focus outline.
- **Accessibility**: alt required for images; initials as text; icon aria-hidden; status with descriptive label.

## Markup
```twig
{# Image avatar (default) #}
<div class="ps-avatar-wrapper">
  <div class="ps-avatar">
    <img class="ps-avatar__image" src="/user.jpg" alt="John Doe" loading="lazy" />
  </div>
</div>

{# Initials fallback #}
<div class="ps-avatar-wrapper ps-avatar-wrapper--lg">
  <div class="ps-avatar ps-avatar--initials">
    <span class="ps-avatar__text">JD</span>
  </div>
</div>

{# Icon fallback with status #}
<div class="ps-avatar-wrapper ps-avatar-wrapper--has-status">
  <div class="ps-avatar ps-avatar--icon">
    <span class="ps-avatar__icon" data-agent="male" aria-hidden="true"></span>
  </div>
  <span class="ps-avatar__status ps-avatar__status--online" aria-label="Online"></span>
</div>
```

## Props
| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `src` | string | `''` | Image URL (triggers image mode). |
| `alt` | string | `''` | Image alt text (required when src present). |
| `initials` | string | `''` | 2-letter initials (fallback when no image). |
| `gender` | enum | `male` | Icon gender variant (male \| female). |
| `size` | enum | `md` | Avatar size (xs \| sm \| md \| lg \| xl). |
| `shape` | enum | `circle` | Shape variant (circle \| square \| rounded). |
| `status` | enum | `''` | Status badge (online \| offline \| busy). |
| `bordered` | boolean | `false` | White border (2px). |
| `clickable` | boolean | `false` | Hover/focus effects. |
| `href` | string | `''` | Link URL (renders `<a>`). |
| `attributes` | Attribute | — | Additional HTML attributes. |

## BEM Structure
- Block: `.ps-avatar-wrapper` (sizing container), `.ps-avatar` (visual block)
- Elements: `.ps-avatar__image`, `.ps-avatar__text`, `.ps-avatar__icon`, `.ps-avatar__status`
- Modifiers (size): `--xs` `--sm` (md default) `--lg` `--xl`
- Modifiers (shape): `--square` `--rounded` (circle default)
- Modifiers (type): `--initials` `--icon`
- Modifiers (state): `--bordered` `--clickable` `--has-status`
- Status variants: `--online` `--offline` `--busy`

## Display Mode Hierarchy
1. **Image** (if `src` provided) → `<img class="ps-avatar__image">`
2. **Initials** (if `initials` provided, no src) → `<span class="ps-avatar__text">`
3. **Icon fallback** (neither src nor initials) → `<span class="ps-avatar__icon" data-agent="male|female">`

## Design Tokens Used
- Sizing: `--size-6` (xs), `--size-8` (sm), `--size-10` (md), `--size-12` (lg), `--size-20` (xl)
- Icon sizing: `--size-3` to `--size-10` (50% of wrapper per size)
- Typography: `--font-size--2` (10px xs), `--font-size-0` (14px sm), `--font-size-2` (18px md), `--font-size-4` (22px lg), `--size-9` (36px xl), `--font-weight-600`
- Colors: `--brand-primary` (initials bg), `--gray-*` (backgrounds/icon), `--green/red-600` (status), `--white` (border/text)
- Radius: `--radius-2/3/4/5/6` (adaptive rounded scaling: 4px xs → 16px xl)
- Border: `--border-size-1` (status), `--border-size-2` (avatar/focus)

## Accessibility
- **Image mode**: `alt` attribute required (screen reader label).
- **Initials mode**: Text rendered directly (readable, no aria needed).
- **Icon mode**: `aria-hidden="true"` (decorative fallback only).
- **Status badge**: `aria-label` with descriptive text ("Online", "Busy", "Offline").
- **Focus**: Outline 2px only when `clickable=true`; keyboard navigable.
- **Contrast**: Initials white on green (7.2:1 AAA); icon gray on light (4.8:1 AA).

## Usage Examples
```twig
{# Profile header with status #}
{{ include('@elements/avatar/avatar.twig', { 
  src: user.picture, 
  alt: user.name, 
  size: 'lg', 
  status: user.online ? 'online' : 'offline' 
}) }}

{# Comment author (initials fallback) #}
{{ include('@elements/avatar/avatar.twig', { 
  src: comment.author.picture, 
  initials: comment.author.initials, 
  alt: comment.author.name, 
  size: 'sm', 
  shape: 'circle' 
}) }}

{# Clickable team member #}
{{ include('@elements/avatar/avatar.twig', { 
  initials: member.initials, 
  size: 'lg', 
  shape: 'rounded', 
  bordered: true, 
  clickable: true, 
  href: member.profile_url 
}) }}
```

## Do & Don't
| Do | Don't |
|----|-------|
| Always provide alt when image present | Use avatar as sole identifier (pair with name) |
| Use initials (2 chars) as primary fallback | Omit gender when icon fallback likely |
| Prefer circle for profiles; rounded for groups | Mix multiple shape modifiers |
| Add border on dark/busy backgrounds | Hardcode sizes or colors |

## Migration Notes
- Icon fallback uses CSS mask with gender data-attribute; SVG files at `source/assets/images/agent/male.svg` and `female.svg`.
- Rounded radius adapts per size via responsive BEM nesting (4px xs → 16px xl).
- Status badge sized at 30% wrapper with 8px minimum.

## Audit Checklist
- No hardcoded dimensions/colors.
- Alt text present when image used.
- Initials or icon fallback always available.
- Status aria-label in English.
- Each modifier works independently.
- README in English.
