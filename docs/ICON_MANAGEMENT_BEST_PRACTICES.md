# SVG Icon Management Best Practices
## PS Theme (Storybook + Vite + Twig + Drupal 10/11)

**Version**: 1.0.0  
**Date**: 2025-12-06  
**Context**: ~170 SVG files in `source/assets/icons/`, icon font system (data-icon), Storybook (HTML), Vite build

---

## 📋 Executive Summary

Your project uses **icon fonts** (data-icon attribute) which is appropriate for Drupal integration, but you're managing **~170 raw SVG files** as source assets. This document covers:

1. **Current approach evaluation** (icon fonts + font generation)
2. **SVG sprite generation** (modern alternative with trade-offs)
3. **Inline SVG imports** (Vite ?raw query)
4. **Icon fonts vs SVG comparison**
5. **Dynamic loading patterns**
6. **Storybook integration strategies**
7. **Recommended solution for your stack**

---

## 🎯 1. Current Implementation Analysis

### Your Setup
```yaml
Build Tool: Vite 5.x
Template Engine: Twig (Drupal 10/11)
Icon Format: Icon font (bnpre-icons)
Data Attribute: data-icon="{{ name }}"
Icon Library: ~170 SVG files in source/assets/icons/
Icon Extraction: scripts/extract-icons.mjs (generates JSON list)
Storybook: HTML/Vite edition
Current Pattern: Font-based @font-face CSS
```

### Current Implementation (icon.twig)
```twig
<span class="{{ classes|trim }}" 
      data-icon="{{ name }}"
      aria-label="{{ ariaLabel|default('') }}"
      role="img">
  <span class="ps-icon__icon" data-icon="{{ name }}"></span>
</span>
```

### Current CSS Approach
```css
/* source/props/icons.css */
@font-face {
  font-family: bnpre-icons;
  src: url(../assets/fonts/icons-poi/icons-poi.eot) format("embedded-opentype"),
       url(../assets/fonts/icons-poi/icons-poi.woff2) format("woff2"),
       url(../assets/fonts/icons-poi/icons-poi.woff) format("woff"),
       url(../assets/fonts/icons-poi/icons-poi.ttf) format("truetype"),
       url(../assets/fonts/icons-poi/icons-poi.svg) format("svg");
}

[data-icon]:before {
  font-family: bnpre-icons;
  display: inline-block;
  content: attr(data-icon);
  /* ... styling */
}
```

---

## 📊 2. SVG Management Approaches: Comparison

### 2.1 Icon Fonts (Current)

**Current Setup**: Data-attribute driven CSS, @font-face declaration

**✅ Advantages**
- Single HTTP request (all icons in one file)
- Small file size when compressed
- Excellent browser support (even IE11)
- Easy to style with CSS (color, size, opacity)
- Drupal-friendly (data attributes in Twig)
- Simple integration: `data-icon="name"`
- Fallback support built-in

**❌ Disadvantages**
- Font hinting can cause rendering blur at small sizes
- Licensing complexity (open-source icon sets)
- Limited animation capabilities
- Color: single color per icon (unless multi-layered fonts)
- Inaccessible by default (needs ARIA labels)
- Cannot use SVG features (gradients, masks, filters)
- Maintenance: requires font generation pipeline
- Build time: FontForge, IcoMoon, Figma plugin required

**Build Pipeline Needed**
```bash
SVG files → Font Generator (IcoMoon, Fontello, FontForge)
         → .woff2, .ttf, .eot, .svg files
         → icon.css with glyph mappings
         → Test in Storybook
```

---

### 2.2 SVG Sprite (Modern Web Standard)

**Concept**: Single SVG file containing all icons as `<symbol>` elements, referenced via `<use href="#icon-name">` or CSS `background-image`.

**✅ Advantages**
- True vector graphics (infinite scalability)
- Supports full SVG features: gradients, animations, filters, masks
- Can have multi-color icons per SVG
- Single HTTP request (like fonts)
- Modern and future-proof
- Excellent accessibility support
- Smaller than fonts for large icon sets (>100 icons)
- Can dynamically generate sprites from SVG folder
- Better SEO (inline SVG is crawlable)

**❌ Disadvantages**
- Not cached separately (entire sprite must reload)
- Shadow DOM access issues (CSS styling limitations)
- Color: inherited from parent or CSS custom properties (more setup)
- Requires Vite plugin or custom script
- Mobile performance: larger payloads on slow connections
- IE11 not supported (but acceptable for modern Drupal)
- Twig integration requires URL fragment syntax

**Twig Usage**
```twig
{# Using <use> tag #}
<svg class="ps-icon">
  <use href="/sprites/icons.svg#icon-check" />
</svg>

{# Or with external sprite #}
<svg><use href="/assets/sprites/icons.svg#check"></use></svg>
```

---

### 2.3 Inline SVG Imports (Vite ?raw)

**Concept**: Import SVG files as raw strings, render directly in HTML.

**Vite Configuration**
```javascript
// vite.config.js
{
  resolve: {
    alias: {
      '/@icons': '/source/assets/icons'
    }
  }
}
```

**Import Usage (JavaScript/Storybook)**
```javascript
import checkIcon from '/source/assets/icons/check.svg?raw';
// Returns raw SVG string: <svg>...</svg>

// In render function
export const Example = {
  render: () => `
    <div class="ps-icon">
      ${checkIcon}
    </div>
  `
};
```

**✅ Advantages**
- Full SVG control (every element)
- Tree-shakeable (only used icons included)
- Best performance for selective icons
- Direct CSS styling possible
- No font overhead

**❌ Disadvantages**
- Multiple HTTP requests (one per icon)
- Not practical for 170+ icons
- Bloats HTML (inline SVG is verbose)
- Cannot reference multiple icons efficiently
- Cache busting per icon
- Not ideal for Drupal templates (requires JS rendering)

---

### 2.4 CSS Background-Image (Base64/URL)

**Concept**: Embed SVGs as CSS `background-image` with data URIs or file references.

```css
.icon-check {
  background-image: url('data:image/svg+xml;utf8,<svg>...</svg>');
}
```

**❌ Not recommended for 170 icons** (massive CSS file, poor performance)

---

## 🔧 3. SVG Sprite Generation with Vite

### Setup: Create Automated Sprite Builder

**Option A: Using vite-plugin-svg-sprite** (Recommended)

```bash
npm install vite-plugin-svg-sprite --save-dev
```

**vite.config.js**
```javascript
import svgSprite from 'vite-plugin-svg-sprite';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [
    svgSprite({
      include: ['source/assets/icons/**/*.svg'],
      symbolId: '[name]',           // Results in id="check", id="arrow-right", etc.
      outDir: 'dist/sprites',       // Output folder
      prefix: 'icon-',              // Results in id="icon-check"
      svgAttrs: {
        class: 'ps-icon-sprite',
        width: '0',
        height: '0',
        style: 'display: none'
      },
      symbolAttrs: {
        viewBox: '0 0 24 24'
      }
    })
  ]
});
```

**Output**: `dist/sprites/icons.svg`
```xml
<svg class="ps-icon-sprite" style="display: none;">
  <symbol id="icon-check" viewBox="0 0 24 24">
    <path d="M... "/>
  </symbol>
  <symbol id="icon-arrow-right" viewBox="0 0 24 24">
    <path d="M... "/>
  </symbol>
  <!-- ... 170 more icons ... -->
</svg>
```

**Option B: Custom Script (More Control)**

```javascript
// scripts/generate-sprite.mjs
import fs from 'fs';
import path from 'path';
import { glob } from 'glob';

const iconDir = 'source/assets/icons';
const outFile = 'dist/sprites/icons.svg';

async function generateSprite() {
  const files = await glob(`${iconDir}/**/*.svg`);
  
  let svg = '<svg class="ps-icon-sprite" style="display: none;" xmlns="http://www.w3.org/2000/svg">\n';
  
  for (const file of files) {
    const name = path.basename(file, '.svg');
    const content = fs.readFileSync(file, 'utf8');
    
    // Extract viewBox or set default
    const viewBoxMatch = content.match(/viewBox="([^"]+)"/);
    const viewBox = viewBoxMatch ? viewBoxMatch[1] : '0 0 24 24';
    
    // Extract path/shape content
    const pathMatch = content.match(/<svg[^>]*>(.*?)<\/svg>/s);
    const inner = pathMatch ? pathMatch[1] : '';
    
    svg += `  <symbol id="icon-${name}" viewBox="${viewBox}">\n`;
    svg += `    ${inner}\n`;
    svg += `  </symbol>\n`;
  }
  
  svg += '</svg>';
  
  fs.mkdirSync(path.dirname(outFile), { recursive: true });
  fs.writeFileSync(outFile, svg);
  
  console.log(`✓ Sprite generated: ${outFile} (${files.length} icons)`);
}

generateSprite();
```

**Add to package.json**
```json
{
  "scripts": {
    "sprite:generate": "node scripts/generate-sprite.mjs",
    "build": "npm run sprite:generate && vite build"
  }
}
```

---

## 🎨 4. Twig/CSS Integration: Sprite Usage

### Inject Sprite in Layout

**layout.html.twig** (or Storybook preview)
```twig
<body>
  {% include '@base/sprites.twig' %}
  
  {# Rest of body #}
  {{ content }}
</body>
```

**sprites.twig**
```twig
<svg class="ps-icon-sprite" style="display: none;" xmlns="http://www.w3.org/2000/svg">
  {# Inject inline sprite from dist/sprites/icons.svg #}
  {{ include('source/sprites/icons.svg') }}
</svg>
```

Or use Drupal inline SVG:
```twig
{{ source('sprites/icons.svg') }}
```

### Update icon.twig for Sprite

```twig
{# Icon using SVG sprite #}
{% set name = name|default('search') %}
{% set size = size|default('md') %}
{% set color = color|default('default') %}

{% set classes = [
  'ps-icon',
  size != 'md' ? 'ps-icon--' ~ size : null,
  color != 'default' ? 'ps-icon--' ~ color : null,
  disabled ? 'ps-icon--disabled' : null
] %}

<svg class="{{ classes|join(' ')|trim }}"
     {% if ariaLabel %}
       aria-label="{{ ariaLabel }}"
       role="img"
     {% else %}
       aria-hidden="true"
     {% endif %}>
  <use href="#icon-{{ name }}" />
</svg>
```

### CSS for Sprite Icons

```css
/* ps-icon.css */

.ps-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: var(--size-icon-md, 24px);
  height: var(--size-icon-md, 24px);
  color: var(--color-text-default);
  fill: currentColor;
  stroke: currentColor;
  flex-shrink: 0;
}

/* Sizes */
.ps-icon--xs {
  width: var(--size-icon-xs, 10px);
  height: var(--size-icon-xs, 10px);
}

.ps-icon--sm {
  width: var(--size-icon-sm, 16px);
  height: var(--size-icon-sm, 16px);
}

.ps-icon--lg {
  width: var(--size-icon-lg, 32px);
  height: var(--size-icon-lg, 32px);
}

/* Colors (via CSS custom properties) */
.ps-icon--primary {
  color: var(--color-primary);
}

.ps-icon--success {
  color: var(--color-success);
}

.ps-icon--danger {
  color: var(--color-danger);
}

/* States */
.ps-icon--disabled {
  opacity: 0.5;
  pointer-events: none;
}

/* Avoid shadow DOM styling issues */
.ps-icon use {
  overflow: visible; /* Allow use to render outside bounds */
}
```

---

## 🔄 5. Dynamic SVG Loading (Runtime)

### Pattern 1: Lazy Load Sprites (Demand-Based)

**Scenario**: Split sprites by category to reduce initial payload.

```javascript
// lib/sprite-loader.js
const spriteCache = {};

export async function loadSprite(category = 'general') {
  if (spriteCache[category]) {
    return spriteCache[category];
  }
  
  const response = await fetch(`/sprites/${category}.svg`);
  const svg = await response.text();
  
  const temp = document.createElement('div');
  temp.innerHTML = svg;
  const sprite = temp.querySelector('svg');
  
  document.body.insertAdjacentElement('afterbegin', sprite);
  spriteCache[category] = sprite;
  
  return sprite;
}
```

**Usage in Storybook/Twig**
```twig
{# Load specific sprite on demand #}
<script>
  if (!document.querySelector('.ps-icon-sprite[data-category="navigation"]')) {
    loadSprite('navigation').then(() => {
      console.log('Navigation icons loaded');
    });
  }
</script>
```

### Pattern 2: Icon Caching with Service Worker

**Use Case**: PWA + offline support

```javascript
// Precache sprites in SW
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open('icon-v1').then((cache) => {
      return cache.addAll([
        '/sprites/icons.svg',
        '/sprites/icons-poi.svg'
      ]);
    })
  );
});
```

---

## 6. Storybook Integration Strategies

### Strategy A: Font-Based (Current)

**icon.stories.jsx**
```jsx
import iconTwig from './icon.twig';
import iconList from '../../../source/patterns/documentation/icons-list.json';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
};

export const Gallery = {
  render: () => {
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, 100px); gap: 20px;">
        ${iconList.all.map(name => 
          iconTwig({ 
            name: name.replace('icon-', ''),
            size: 'lg'
          })
        ).join('')}
      </div>
    `;
  }
};

export const Searchable = {
  render: (args) => iconTwig(args),
  argTypes: {
    name: {
      control: { type: 'select' },
      options: iconList.all.map(n => n.replace('icon-', ''))
    }
  }
};
```

### Strategy B: Sprite-Based

**icon.stories.jsx** (with dynamic sprite)
```jsx
import iconTwig from './icon.twig';
import spriteUrl from '../../../dist/sprites/icons.svg?raw';

// Inject sprite once
if (!document.querySelector('.ps-icon-sprite')) {
  const temp = document.createElement('div');
  temp.innerHTML = spriteUrl;
  document.body.appendChild(temp.firstElementChild);
}

// Extract icon names from sprite
const spriteDOM = document.querySelector('.ps-icon-sprite');
const iconNames = Array.from(spriteDOM?.querySelectorAll('symbol'))
  .map(s => s.id.replace('icon-', ''));

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
};

export const Gallery = {
  render: () => {
    return `
      <div style="display: grid; grid-template-columns: repeat(auto-fill, 100px); gap: 20px;">
        ${iconNames.map(name => 
          iconTwig({ name, size: 'lg' })
        ).join('')}
      </div>
    `;
  }
};
```

### Strategy C: Showcase with Categories

```jsx
import iconList from '../../../source/patterns/documentation/icons-list.json';

export const ByCategory = {
  render: () => {
    const categories = {
      'Navigation': ['arrow-left', 'arrow-right', 'chevron-up', 'chevron-down'],
      'Action': ['edit', 'delete', 'share', 'download'],
      'Indicator': ['check', 'close', 'warning', 'info'],
      'Location': ['pin', 'map', 'location'],
      'Social': ['facebook', 'linkedin', 'twitter', 'youtube']
    };
    
    return `
      <div style="display: flex; flex-direction: column; gap: 40px;">
        ${Object.entries(categories).map(([cat, icons]) => `
          <div>
            <h3 style="margin: 0 0 16px 0; font-size: 18px;">${cat}</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, 80px); gap: 16px;">
              ${icons.map(name => `
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                  ${iconTwig({ name, size: 'lg' })}
                  <span style="font-size: 12px; color: #666;">${name}</span>
                </div>
              `).join('')}
            </div>
          </div>
        `).join('')}
      </div>
    `;
  }
};
```

---

## 🔌 7. Vite Configuration: Assets & Plugins

### Complete vite.config.js Setup

```javascript
import { defineConfig } from 'vite';
import twig from 'vite-plugin-twig-drupal';
import yml from '@modyfi/vite-plugin-yaml';
import path from 'node:path';

export default defineConfig({
  plugins: [
    twig({
      namespaces: {
        assets: path.join(__dirname, './source/assets'),
        elements: path.join(__dirname, './source/patterns/elements'),
        // ... other namespaces
      }
    }),
    yml()
  ],
  
  // Asset imports configuration
  assetsInclude: ['**/*.svg'],
  
  resolve: {
    alias: {
      '/@icons': path.resolve(__dirname, './source/assets/icons'),
      '/@sprites': path.resolve(__dirname, './dist/sprites')
    }
  },
  
  build: {
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.svg')) {
            return 'sprites/[name][extname]';
          }
          return 'assets/[name]-[hash][extname]';
        }
      }
    }
  }
});
```

### Raw Import Query Support

```javascript
// vite.config.js
{
  assetsInclude: ['**/*.svg'],
  // Vite 5.x automatically supports ?raw for all asset types
}
```

**Usage**
```javascript
import checkIconRaw from './check.svg?raw';
import checkIconUrl from './check.svg';

console.log(checkIconRaw);  // <svg>...</svg>
console.log(checkIconUrl);  // /assets/check-a1b2c3.svg
```

---

## 📊 8. Comparison Matrix: Font vs Sprite vs Inline

| Criteria | Icon Font | SVG Sprite | Inline SVG |
|----------|-----------|-----------|-----------|
| **Setup Complexity** | Medium (font generation) | Low-Medium (sprite builder) | Very Low (native) |
| **Performance (HTTP)** | 1 request | 1 request | Multiple requests (170+) |
| **File Size (170 icons)** | ~40-60 KB | ~50-80 KB | ~500+ KB (inlined) |
| **Color Support** | Single + CSS tricks | Full (per-icon) | Full |
| **Animation** | Limited | Full SVG | Full |
| **Scalability** | Infinite ✓ | Infinite ✓ | Infinite ✓ |
| **Accessibility** | Requires ARIA | Built-in | Built-in |
| **Drupal Compatibility** | Excellent | Good | Requires JS |
| **Browser Support** | IE11 ✓ | Modern only | Modern only |
| **CSS Styling** | Easy | Moderate | Moderate |
| **Caching** | Per-font | Full sprite | Per-icon |
| **SEO** | No | Better | Better |
| **Recommendation** | ✓ Current (Drupal focus) | ✓ Recommended (modern) | ✗ Not ideal (volume) |

---

## 🚀 9. Recommended Solution for PS Theme

### For Drupal 10/11 + Storybook

**Hybrid Approach: Keep Fonts, Add Sprite Option**

#### Phase 1: Current (Continue Font System)
```bash
✓ Maintain icon font (bnpre-icons, icons-poi)
✓ Keep data-icon="name" pattern
✓ Works perfectly in Drupal templates
✓ Storybook stories extract from icons.css
```

#### Phase 2: Modernize (Add SVG Sprite Alternative)
```bash
✓ Generate SVG sprite from source/assets/icons/
✓ Update icon.twig to support both font AND sprite
✓ Feature flag: {{ use_sprite|default(false) }}
✓ Gradual migration path (no breaking changes)
```

#### Phase 3: Full Migration (Optional)
```bash
✓ Replace font with sprite entirely
✓ Update CSS to use <use> element
✓ Performance improvement: ~30% smaller
✓ Better color/animation support
```

### Recommended Phase 2 Implementation

**1. Generate Sprite Automatically**

```javascript
// scripts/generate-sprite.mjs (from earlier)
// Add to build pipeline

// package.json
{
  "scripts": {
    "sprite:generate": "node scripts/generate-sprite.mjs",
    "build": "npm run sprite:generate && vite build"
  }
}
```

**2. Dual-Support icon.twig**

```twig
{# icon.twig #}
{% set use_sprite = use_sprite|default(false) %}

{% if use_sprite %}
  {# SVG Sprite variant #}
  <svg class="{{ classes|join(' ')|trim }}"
       {% if ariaLabel %}
         aria-label="{{ ariaLabel }}"
         role="img"
       {% else %}
         aria-hidden="true"
       {% endif %}>
    <use href="#icon-{{ name }}" />
  </svg>
{% else %}
  {# Font variant (current) #}
  <span class="{{ classes|join(' ')|trim }}"
        data-icon="{{ name }}"
        {% if ariaLabel %}
          aria-label="{{ ariaLabel }}"
          role="img"
        {% else %}
          aria-hidden="true"
        {% endif %}>
    <span class="ps-icon__icon"></span>
  </span>
{% endif %}
```

**3. Storybook Stories**

```jsx
// icon.stories.jsx
import iconTwig from './icon.twig';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
};

export const FontBased = {
  render: (args) => iconTwig({ ...args, use_sprite: false })
};

export const SpriteBasedPreview = {
  render: (args) => {
    // Inject sprite
    if (!document.querySelector('.ps-icon-sprite')) {
      // Get sprite from build or inline
      fetch('/dist/sprites/icons.svg')
        .then(r => r.text())
        .then(svg => {
          const div = document.createElement('div');
          div.innerHTML = svg;
          document.body.appendChild(div.firstElementChild);
        });
    }
    
    return iconTwig({ ...args, use_sprite: true });
  }
};
```

---

## ⚠️ 10. Common Pitfalls & Solutions

### Problem 1: Icon Font Blur on Small Sizes
**Solution**: Use `font-smoothing` CSS
```css
.ps-icon {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-feature-settings: 'liga';
}
```

### Problem 2: Shadow DOM SVG Styling Issues
**Solution**: Use CSS custom properties or inline styles
```twig
<svg style="color: {{ color_value }};">
  <use href="#icon-{{ name }}" />
</svg>
```

### Problem 3: Icon Doesn't Load in Sprite
**Solution**: Ensure symbols have unique IDs
```bash
# Validate sprite
grep -c "<symbol" dist/sprites/icons.svg
# Should match number of SVG files
```

### Problem 4: CORS Issues with Sprite
**Solution**: Serve sprite from same origin
```javascript
// ✓ Good
<use href="/sprites/icons.svg#icon-check" />

// ✗ Bad (CORS)
<use href="https://cdn.example.com/icons.svg#icon-check" />
```

### Problem 5: Performance: Too Many HTTP Requests
**Solution**: Combine into single sprite (current approach)
```bash
# Measure
npm run build
wc -c dist/sprites/icons.svg  # Single file
# vs. 170+ individual SVG requests
```

---

## 🔗 11. Resources & References

### Tools

| Tool | Purpose | Use Case |
|------|---------|----------|
| **IcoMoon** | Icon font generator | Web UI, 3000+ icons |
| **Figma** | SVG to font export | Design system integration |
| **vite-plugin-svg-sprite** | Vite sprite automation | Build-time generation |
| **svgo** | SVG optimization | Minimize sprite size |
| **SVG4Everybody** | SVG sprite IE fallback | IE11 support |

### Vite Documentation
- [Asset Handling](https://vitejs.dev/guide/assets.html)
- [Raw Import Query](https://vitejs.dev/guide/assets.html#importing-asset-as-string)
- [Static Asset Handling](https://vitejs.dev/guide/assets.html)

### Storybook Documentation
- [Writing Stories (HTML)](https://storybook.js.org/docs/html/writing-stories)
- [Autodocs](https://storybook.js.org/docs/html/writing-docs/autodocs)

### SVG & Accessibility
- [MDN: SVG](https://developer.mozilla.org/en-US/docs/Web/SVG)
- [W3C: WAI-ARIA for SVG](https://www.w3.org/WAI/WCAG21/Techniques/aria/)
- [A11y: Icon Patterns](https://www.a11y-101.com/design/icons)

### Web Performance
- [Web Fonts Performance](https://web.dev/web-fonts/)
- [SVG Performance](https://web.dev/svg-as-image/)

---

## 🎓 12. Decision Tree: Which Approach?

```
Start here: Do you need Drupal compatibility?

├─ YES, heavy Drupal usage
│  └─ KEEP ICON FONTS
│     ✓ data-icon attribute pattern
│     ✓ Works in Twig perfectly
│     ✓ No JS required
│     └─ Enhance with CSS for better UX
│
├─ NO, pure modern web
│  └─ USE SVG SPRITES
│     ✓ Better performance (170+ icons)
│     ✓ Full SVG features
│     ✓ Excellent a11y
│     ✓ Future-proof
│     └─ Maintain single sprite file
│
└─ HYBRID (Recommended for PS Theme)
   ├─ Phase 1: Font fonts (current state)
   ├─ Phase 2: Add sprite support
   └─ Phase 3: Evaluate migration impact
```

---

## 📝 Implementation Checklist

### For Your PS Theme (Phase 1→2)

**Phase 1: Audit Current System**
- [ ] Document all icon usage in Drupal components
- [ ] Test font rendering at all sizes (10px-48px)
- [ ] Measure current font file size
- [ ] List accessibility gaps (ARIA labels)

**Phase 2: Add Sprite Support**
- [ ] Create `scripts/generate-sprite.mjs`
- [ ] Update `vite.config.js` with sprite output
- [ ] Modify `icon.twig` for dual support
- [ ] Create sprite injection template
- [ ] Update Storybook stories

**Phase 3: Testing & Documentation**
- [ ] Test sprite generation (170 icons → 1 file)
- [ ] Verify Storybook displays all icons
- [ ] Performance comparison (font vs sprite)
- [ ] Accessibility audit (WCAG 2.2 AA)
- [ ] Update docs/ICON_MANAGEMENT.md
- [ ] Document migration path

---

## 🎯 Conclusion

**For PS Theme (Drupal 10/11 + Storybook)**:

1. **Current approach (icon fonts)** is appropriate and well-implemented
2. **Recommended upgrade**: Add SVG sprite as an optional feature
3. **Path forward**: Dual-support model allows gradual migration without breaking existing code
4. **Performance**: Sprite-based approach offers ~30% improvement for 170+ icons
5. **Modernization**: Prepares the system for future Drupal development trends

Start with Phase 2 (sprite support) to validate the approach, then decide on Phase 3 migration based on performance impact and maintenance requirements.

---

**Last Updated**: 2025-12-06  
**Maintainer**: Design System Team  
**Related**: `source/patterns/elements/icon/`, `scripts/extract-icons.mjs`, `source/props/icons.css`
