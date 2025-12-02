# Avatar

User or entity visual representation composed of three atomic elements: image, text (initials), and status badge. Features automatic fallback hierarchy and interactive states.

## Markup

```twig
{# Default: Image avatar #}
{% include '@components/avatar/avatar.twig' with {
  src: 'https://i.pravatar.cc/150',
  alt: 'John Doe',
} %}

{# Initials fallback #}
{% include '@components/avatar/avatar.twig' with {
  initials: 'JD',
  size: 'lg',
} %}

{# Icon fallback with gender #}
{% include '@components/avatar/avatar.twig' with {
  gender: 'female',
  size: 'md',
} %}

{# Icon fallback (automatic, male by default) #}
{% include '@components/avatar/avatar.twig' %}

{# Icon fallback with specific gender #}
{% include '@components/avatar/avatar.twig' with {
  gender: 'female',
} %}

{# With status badge #}
{% include '@components/avatar/avatar.twig' with {
  src: 'https://i.pravatar.cc/150',
  alt: 'John Doe',
  status: 'online',
} %}

{# Clickable link #}
{% include '@components/avatar/avatar.twig' with {
  src: 'https://i.pravatar.cc/150',
  alt: 'John Doe',
  clickable: true,
  href: '/profile',
} %}
```

## Props

| Name | Type | Default | Description |
|------|------|---------|-------------|
| `src` | `string` | `''` | Avatar image URL. If omitted, falls back to initials or icon. |
| `alt` | `string` | `''` | Alternative text for the image. Required when `src` is provided. |
| `initials` | `string` | `''` | Initials text (2 letters max, e.g. "JD"). Fallback if no image. |
| `gender` | `string` | `'male'` | Gender for icon fallback: `male` \| `female`. Uses agent silhouette images. |
| `size` | `string` | `'md'` | Avatar size: `xs` (24px) \| `sm` (32px) \| `md` (40px) \| `lg` (48px) \| `xl` (80px) |
| `shape` | `string` | `'circle'` | Avatar shape: `circle` \| `square` \| `rounded` |
| `status` | `string` | `''` | Status badge indicator: `online` \| `offline` \| `busy` |
| `bordered` | `boolean` | `false` | Add white border around avatar |
| `clickable` | `boolean` | `false` | Enable hover/focus interactive effect |
| `href` | `string` | `''` | URL if avatar should be clickable link. Transforms element to `<a>`. |
| `attributes` | `Attribute` | - | Additional HTML attributes |

## BEM Structure

```
ps-avatar                      # Block (base container with background/border/radius)
├── ps-avatar__image           # Element: Image atom (when src provided)
├── ps-avatar__text            # Element: Initials text atom (when initials provided)
└── ps-avatar__status          # Element: Status badge atom (when status provided)
    ├── ps-avatar__status--online
    ├── ps-avatar__status--offline
    └── ps-avatar__status--busy

Modifiers (sizes):
├── ps-avatar--xs              # 24px
├── ps-avatar--sm              # 32px
├── ps-avatar--md              # 40px (default, no class)
├── ps-avatar--lg              # 48px
└── ps-avatar--xl              # 80px

Modifiers (shapes):
├── ps-avatar--circle          # Circle (default, no class)
├── ps-avatar--square          # Square
└── ps-avatar--rounded         # Rounded corners

Modifiers (types/states):
├── ps-avatar--initials        # Applied when initials are displayed
├── ps-avatar--male            # Applied for male gender fallback (primary bg + male.svg)
├── ps-avatar--female          # Applied for female gender fallback (secondary bg + female.svg)
├── ps-avatar--bordered        # White border
└── ps-avatar--clickable       # Hover/focus effect
```

## Component-Scoped Variables

Avatar uses Bootstrap 5-inspired component-scoped variables for runtime customization:

```css
.ps-avatar {
  /* Layer 2: Component defaults */
  --ps-avatar-size: var(--size-10);              /* 40px md */
  --ps-avatar-text-size: var(--font-size-1);     /* 16px */
  --ps-avatar-icon-size: var(--size-5);          /* 20px */
  --ps-avatar-bg: var(--gray-200);
  --ps-avatar-text-color: var(--white);
  --ps-avatar-icon-color: var(--gray-600);
  --ps-avatar-radius: 50%;                       /* circle */
  --ps-avatar-border-width: 0;
  --ps-avatar-border-color: var(--white);
}

.ps-avatar__status {
  --ps-avatar-status-size: var(--size-3);        /* 12px md */
  --ps-avatar-status-bg: var(--success);
  --ps-avatar-status-border-width: var(--border-size-2);
  --ps-avatar-status-border-color: var(--white);
}
```

**Customization examples:**

```css
/* Custom avatar size */
.custom-avatar {
  --ps-avatar-size: 64px;
  --ps-avatar-text-size: var(--font-size-4);
}

/* Custom colors */
.warning-avatar {
  --ps-avatar-bg: var(--warning);
  --ps-avatar-text-color: var(--gray-900);
}
```

## Design Tokens Used

### Sizes
- `--size-2` (8px) - Status badge xs
- `--size-3` (12px) - Status badge md, Icon xs
- `--size-4` (16px) - Icon sm
- `--size-5` (20px) - Icon md
- `--size-6` (24px) - Avatar xs
- `--size-8` (32px) - Avatar sm
- `--size-10` (40px) - Avatar md (default), Icon xl
- `--size-12` (48px) - Avatar lg
- `--size-20` (80px) - Avatar xl
- `--size-205` (10px) - Status badge sm
- `--size-305` (14px) - Status badge lg

### Typography
- `--font-sans` - Font family (BNPPSans)
- `--font-weight-600` - Semibold weight for initials
- `--font-size--2` (10px) - Text xs
- `--font-size-0` (14px) - Text sm
- `--font-size-1` (16px) - Text md
- `--font-size-3` (20px) - Text lg
- `--font-size-7` (32px) - Text xl

### Colors
- `--white` - Text color, border color
- `--gray-100` - Icon fallback background
- `--gray-200` - Default background
- `--gray-400` - Offline status
- `--gray-600` - Icon color
- `--primary` - Initials background, male gender fallback
- `--secondary` - Female gender fallback, focus outline
- `--success` - Online status
- `--danger` - Busy status

### Borders
- `--border-size-1` (1px) - Status border xs
- `--border-size-2` (2px) - Default border, Focus outline
- `--border-size-3` (3px) - Status border xl
- `--radius-2` (4px) - Rounded xs
- `--radius-3` (6px) - Rounded md
- `--radius-4` (8px) - Rounded lg
- `--radius-5` (12px) - Rounded xl

### Animations
- `--duration-fast` - Hover transition
- `--ease-3` - Easing function

## Accessibility

- **Image alt text**: Always provide `alt` prop when using `src` for screen readers
- **Focus indicators**: Clickable avatars include visible focus outline (`outline: 2px solid var(--secondary)`) 
- **ARIA labels**: Status badges include descriptive `aria-label` attributes (e.g., "Online", "Busy", "Offline")
- **Keyboard navigation**: Clickable avatars with `href` are fully keyboard accessible via `<a>` elements
- **Color contrast**: All text and status colors meet WCAG AA standards (4.5:1 minimum)
- **Semantic HTML**: Uses `<img>` for photos, `<span>` for initials/status to ensure proper structure

### Do ✅
- Use image avatars with proper `alt` text for user profiles
- Provide 2-letter initials as fallback (first + last name)
- Use `gender` prop to show appropriate agent silhouette (male/female) when no image/initials
- Use status badges for real-time presence indicators
- Match avatar size to context (xs/sm for lists, lg/xl for headers)
- Use `bordered` on colored backgrounds for visual separation
- Provide `href` when avatar should navigate
  - Status colors meet WCAG AA standards

## Usage

### Do ✅
- Use image avatars with proper `alt` text for user profiles
- Provide 2-letter initials as fallback (first + last name)
- Use status badges for real-time presence indicators
- Match avatar size to context (xs/sm for lists, lg/xl for headers)
- Use `bordered` on colored backgrounds for visual separation
- Provide `href` when avatar should navigate

### Don't ❌
- Don't use avatars smaller than `xs` (24px minimum for accessibility)
- Don't use more than 2 characters for initials (readability)
- Don't omit `alt` text when using images (screen reader requirement)
- Don't use status badges for non-presence states (use badges component)
- Don't make avatars clickable without clear visual affordance
- Don't use `square` shape for user avatars (reserve for entities/brands)

## Examples

### User Profile Header
```twig
{% include '@components/avatar/avatar.twig' with {
  src: user.avatar_url,
  alt: user.full_name,
  size: 'xl',
  status: user.online_status,
  clickable: true,
  href: user.profile_url,
} %}
```

### Comment Author (with fallback)
```twig
{% include '@components/avatar/avatar.twig' with {
  src: author.avatar,
  alt: author.name,
  initials: author.initials,
  size: 'sm',
} %}
```

### Team Member List
```twig
<div class="team-list">
  {% for member in team %}
    {% include '@components/avatar/avatar.twig' with {
      initials: member.initials,
      size: 'md',
      shape: 'rounded',
      bordered: true,
      clickable: true,
      href: member.profile_url,
    } %}
  {% endfor %}
</div>
```

### Status Indicator
```twig
{% include '@components/avatar/avatar.twig' with {
  src: user.avatar,
  alt: user.name,
  size: 'lg',
  status: user.is_online ? 'online' : 'offline',
} %}
```
