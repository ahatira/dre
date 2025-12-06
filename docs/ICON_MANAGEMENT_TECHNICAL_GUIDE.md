# SVG Icon Management: Technical Implementation Guide
## Complete Code Examples for PS Theme

**Version**: 1.0.0  
**Date**: 2025-12-06

---

## 📦 Contents

1. Sprite Generation Scripts
2. Vite Configuration Details
3. Twig Template Patterns
4. CSS Styling Solutions
5. Storybook Story Examples
6. JavaScript Utilities
7. Drupal Integration Guide
8. Performance Optimization

---

## 1️⃣ Sprite Generation Scripts

### 1.1 Basic Sprite Generator

**File**: `scripts/generate-sprite.mjs`

```javascript
import fs from 'fs';
import path from 'path';
import { glob } from 'glob';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CONFIG = {
  iconDir: path.join(__dirname, '../source/assets/icons'),
  outDir: path.join(__dirname, '../dist/sprites'),
  outFile: 'icons.svg',
  prefix: 'icon-',
  namespace: 'http://www.w3.org/2000/svg',
  defaultViewBox: '0 0 24 24',
  prettify: true
};

/**
 * Parse SVG file and extract viewBox and content
 */
function parseSVG(filePath) {
  const content = fs.readFileSync(filePath, 'utf8');
  
  // Extract viewBox attribute
  const viewBoxMatch = content.match(/viewBox="([^"]+)"/);
  const viewBox = viewBoxMatch ? viewBoxMatch[1] : CONFIG.defaultViewBox;
  
  // Extract inner content (everything between <svg> tags)
  const svgMatch = content.match(/<svg[^>]*>([\s\S]*?)<\/svg>/);
  let inner = svgMatch ? svgMatch[1].trim() : '';
  
  // Clean up: remove unnecessary attributes
  inner = inner
    .replace(/xmlns[^"]*"[^"]*"/g, '')  // Remove xmlns
    .replace(/width="[^"]*"/g, '')      // Remove width
    .replace(/height="[^"]*"/g, '')     // Remove height
    .trim();
  
  return { viewBox, inner };
}

/**
 * Generate SVG sprite from all SVG files
 */
async function generateSprite() {
  console.log('🔨 Generating SVG sprite...\n');
  
  // Find all SVG files
  const files = await glob(`${CONFIG.iconDir}/**/*.svg`, { nodir: true });
  
  if (files.length === 0) {
    console.error(`❌ No SVG files found in ${CONFIG.iconDir}`);
    process.exit(1);
  }
  
  console.log(`📁 Found ${files.length} SVG files\n`);
  
  // Sort files alphabetically
  files.sort();
  
  // Build sprite
  let symbols = '';
  const symbols_array = [];
  
  for (const file of files) {
    try {
      const name = path.basename(file, '.svg');
      const symbolId = `${CONFIG.prefix}${name}`;
      const { viewBox, inner } = parseSVG(file);
      
      const symbol = `  <symbol id="${symbolId}" viewBox="${viewBox}">\n    ${inner}\n  </symbol>`;
      
      symbols += symbol + '\n';
      symbols_array.push({ id: symbolId, name, viewBox });
      
      process.stdout.write('.');
    } catch (error) {
      console.error(`\n❌ Error parsing ${file}:`, error.message);
    }
  }
  
  console.log('\n');
  
  // Build final SVG
  const spriteContent = [
    '<?xml version="1.0" encoding="UTF-8"?>',
    `<svg class="ps-icon-sprite" xmlns="${CONFIG.namespace}" style="display: none; width: 0; height: 0;">`,
    '  <!-- Generated sprite - DO NOT EDIT MANUALLY -->',
    symbols.trimEnd(),
    '</svg>'
  ].join('\n');
  
  // Write sprite file
  fs.mkdirSync(CONFIG.outDir, { recursive: true });
  const outPath = path.join(CONFIG.outDir, CONFIG.outFile);
  
  fs.writeFileSync(outPath, spriteContent);
  
  // Write metadata
  const metadata = {
    generated: new Date().toISOString(),
    count: symbols_array.length,
    icons: symbols_array.map(s => s.id),
    categories: extractCategories(symbols_array)
  };
  
  fs.writeFileSync(
    path.join(CONFIG.outDir, 'icons.json'),
    JSON.stringify(metadata, null, 2)
  );
  
  console.log(`✅ Sprite generated successfully!`);
  console.log(`   📄 File: ${outPath}`);
  console.log(`   📊 Icons: ${symbols_array.length}`);
  console.log(`   💾 Size: ${(fs.statSync(outPath).size / 1024).toFixed(2)} KB\n`);
  
  return symbols_array;
}

/**
 * Extract category groupings from icon names
 */
function extractCategories(symbols) {
  const categories = {};
  
  for (const symbol of symbols) {
    // Example: "icon-arrow-right" → "arrow"
    const parts = symbol.id.replace(CONFIG.prefix, '').split('-');
    const category = parts[0];
    
    if (!categories[category]) {
      categories[category] = [];
    }
    categories[category].push(symbol.id);
  }
  
  return categories;
}

// Run
generateSprite().catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
```

### 1.2 Advanced Sprite with Optimization

**File**: `scripts/generate-sprite-optimized.mjs`

```javascript
import fs from 'fs';
import path from 'path';
import { glob } from 'glob';
import { fileURLToPath } from 'url';
import SVGO from 'svgo';  // npm install svgo

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const CONFIG = {
  iconDir: path.join(__dirname, '../source/assets/icons'),
  outDir: path.join(__dirname, '../dist/sprites'),
  outFile: 'icons.svg',
  prefix: 'icon-',
  optimize: true,  // Run SVGO optimization
  removeViewBox: false  // Keep viewBox for responsive scaling
};

/**
 * Optimize SVG with SVGO
 */
async function optimizeSVG(content) {
  const svgo = new SVGO({
    multipass: true,
    plugins: [
      {
        name: 'preset-default',
        params: {
          overrides: {
            removeViewBox: CONFIG.removeViewBox,
            convertShapeToPath: false,
            convertStyleToAttrs: false,
            removeUselessStrokeAndFill: false,
          }
        }
      },
      'removeComments',
      'removeDoctype',
      'removeMetadata',
      'removeXMLNS',
    ]
  });
  
  try {
    const result = await svgo.optimize(content);
    return result.data;
  } catch (error) {
    console.warn('SVGO optimization failed, using original:', error.message);
    return content;
  }
}

/**
 * Parse and optimize SVG
 */
async function parseSVG(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  
  // Optimize if enabled
  if (CONFIG.optimize) {
    content = await optimizeSVG(content);
  }
  
  const viewBoxMatch = content.match(/viewBox="([^"]+)"/);
  const viewBox = viewBoxMatch ? viewBoxMatch[1] : '0 0 24 24';
  
  const svgMatch = content.match(/<svg[^>]*>([\s\S]*?)<\/svg>/);
  const inner = svgMatch ? svgMatch[1].trim() : '';
  
  return { viewBox, inner };
}

/**
 * Generate optimized sprite
 */
async function generateOptimizedSprite() {
  console.log('🚀 Generating optimized SVG sprite...\n');
  
  const files = await glob(`${CONFIG.iconDir}/**/*.svg`);
  
  if (files.length === 0) {
    console.error(`❌ No SVG files found`);
    process.exit(1);
  }
  
  files.sort();
  
  let symbols = '';
  const icons = [];
  let totalOriginalSize = 0;
  
  for (const file of files) {
    try {
      totalOriginalSize += fs.statSync(file).size;
      
      const name = path.basename(file, '.svg');
      const symbolId = `${CONFIG.prefix}${name}`;
      const { viewBox, inner } = await parseSVG(file);
      
      symbols += `  <symbol id="${symbolId}" viewBox="${viewBox}">\n    ${inner}\n  </symbol>\n`;
      icons.push(symbolId);
      
      process.stdout.write('.');
    } catch (error) {
      console.error(`\n❌ Error: ${file}: ${error.message}`);
    }
  }
  
  const spriteContent = [
    '<?xml version="1.0" encoding="UTF-8"?>',
    '<svg class="ps-icon-sprite" xmlns="http://www.w3.org/2000/svg" style="display: none;">',
    symbols.trimEnd(),
    '</svg>'
  ].join('\n');
  
  fs.mkdirSync(CONFIG.outDir, { recursive: true });
  const outPath = path.join(CONFIG.outDir, CONFIG.outFile);
  fs.writeFileSync(outPath, spriteContent);
  
  const finalSize = fs.statSync(outPath).size;
  
  console.log(`\n\n✅ Optimized sprite generated!`);
  console.log(`   Icons: ${icons.length}`);
  console.log(`   Original size: ${(totalOriginalSize / 1024).toFixed(2)} KB`);
  console.log(`   Sprite size: ${(finalSize / 1024).toFixed(2)} KB`);
  console.log(`   Compression: ${((1 - finalSize / totalOriginalSize) * 100).toFixed(1)}%\n`);
}

generateOptimizedSprite();
```

---

## 2️⃣ Vite Configuration

### 2.1 Basic Vite Config with Sprite Support

**File**: `vite.config.js` (excerpt)

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
        components: path.join(__dirname, './source/patterns/components'),
      },
    }),
    yml(),
  ],

  resolve: {
    alias: {
      '/@icons': path.resolve(__dirname, './source/assets/icons'),
      '/@sprites': path.resolve(__dirname, './dist/sprites'),
    },
  },

  // Include SVG files as assets
  assetsInclude: ['**/*.svg'],

  build: {
    // SVG output configuration
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          // Organize SVG files
          if (assetInfo.name.endsWith('.svg')) {
            // Sprite vs individual SVGs
            if (assetInfo.name.includes('sprite')) {
              return 'sprites/[name][extname]';
            }
            return 'assets/icons/[name][extname]';
          }
          // Regular assets
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
  },
});
```

### 2.2 Advanced Config with Custom Plugin

**File**: `vite.config.js` (with sprite plugin)

```javascript
import { defineConfig } from 'vite';
import { readFileSync } from 'fs';
import path from 'path';

const spritePlugin = () => ({
  name: 'svg-sprite',
  resolveId(id) {
    if (id === 'virtual-sprite') {
      return id;
    }
  },
  load(id) {
    if (id === 'virtual-sprite') {
      // Load generated sprite
      const spritePath = path.resolve(__dirname, 'dist/sprites/icons.svg');
      try {
        return readFileSync(spritePath, 'utf-8');
      } catch (e) {
        console.warn('Sprite not found, run npm run sprite:generate');
        return '<svg></svg>';
      }
    }
  },
});

export default defineConfig({
  plugins: [
    spritePlugin(),
    // ... other plugins
  ],

  // Vite raw import support (automatic)
  // import svg from './file.svg?raw' ← works out of the box
});
```

---

## 3️⃣ Twig Template Patterns

### 3.1 Font-Based Icon Template

**File**: `source/patterns/elements/icon/icon.twig`

```twig
{# 
  Icon component - Font-based variant
  
  Props:
    - name (string, required): Icon name (without 'icon-' prefix)
    - size (string): xs|sm|md|lg|xl|xxl (default: md)
    - color (string): default|primary|secondary|success|warning|danger|info (default: default)
    - disabled (bool): Disabled state (default: false)
    - ariaLabel (string): Accessibility label
    - baseClass (string): Custom base class for composition
#}

{% set name = name|default('search') %}
{% set size = size|default('md') %}
{% set color = color|default('default') %}
{% set disabled = disabled|default(false) %}
{% set baseClass = baseClass|default(null) %}

{# Build class list #}
{% set classes = baseClass ? [
  baseClass,
  size != 'md' ? baseClass ~ '--' ~ size : null,
  color != 'default' ? baseClass ~ '--' ~ color : null,
  disabled ? baseClass ~ '--disabled' : null
] : [
  'ps-icon',
  size != 'md' ? 'ps-icon--' ~ size : null,
  color != 'default' ? 'ps-icon--' ~ color : null,
  disabled ? 'ps-icon--disabled' : null
] %}

{# Render font-based icon #}
<span 
  class="{{ classes|join(' ')|trim }}"
  data-icon="{{ name }}"
  {% if ariaLabel %}
    aria-label="{{ ariaLabel }}"
    role="img"
  {% else %}
    aria-hidden="true"
  {% endif %}>
  <span class="ps-icon__icon"></span>
</span>
```

### 3.2 Sprite-Based Icon Template

**File**: `source/patterns/elements/icon/icon-sprite.twig`

```twig
{#
  Icon component - SVG Sprite variant
  
  Props: (same as font variant)
#}

{% set name = name|default('search') %}
{% set size = size|default('md') %}
{% set color = color|default('default') %}
{% set disabled = disabled|default(false) %}
{% set baseClass = baseClass|default(null) %}

{% set classes = baseClass ? [
  baseClass,
  size != 'md' ? baseClass ~ '--' ~ size : null,
  color != 'default' ? baseClass ~ '--' ~ color : null,
  disabled ? baseClass ~ '--disabled' : null
] : [
  'ps-icon',
  size != 'md' ? 'ps-icon--' ~ size : null,
  color != 'default' ? 'ps-icon--' ~ color : null,
  disabled ? 'ps-icon--disabled' : null
] %}

{# Render sprite-based icon #}
<svg 
  class="{{ classes|join(' ')|trim }}"
  {% if ariaLabel %}
    aria-label="{{ ariaLabel }}"
    role="img"
  {% else %}
    aria-hidden="true"
  {% endif %}>
  <use href="#icon-{{ name }}" />
</svg>
```

### 3.3 Dual-Support Template

**File**: `source/patterns/elements/icon/icon-dual.twig`

```twig
{#
  Icon component - Dual support (font OR sprite)
  
  Props:
    - ... (all previous props)
    - useSprite (bool): Use sprite instead of font (default: false)
#}

{% set useSprite = useSprite|default(false) %}

{% if useSprite %}
  {% include 'icon-sprite.twig' only with {
    name: name,
    size: size,
    color: color,
    disabled: disabled,
    ariaLabel: ariaLabel,
    baseClass: baseClass
  } %}
{% else %}
  {% include 'icon.twig' only with {
    name: name,
    size: size,
    color: color,
    disabled: disabled,
    ariaLabel: ariaLabel,
    baseClass: baseClass
  } %}
{% endif %}
```

### 3.4 Icon Gallery Template

**File**: `source/patterns/elements/icon/icon-gallery.twig`

```twig
{#
  Display grid of icons with metadata
  
  Props:
    - icons (array): List of icon names
    - size (string): Icon size (default: lg)
    - showLabels (bool): Show icon names (default: true)
#}

{% set icons = icons|default([]) %}
{% set size = size|default('lg') %}
{% set showLabels = showLabels|default(true) %}

<div class="ps-icon-gallery">
  {% for name in icons %}
    <div class="ps-icon-gallery__item">
      {% include 'icon.twig' only with {
        name: name,
        size: size
      } %}
      
      {% if showLabels %}
        <span class="ps-icon-gallery__label">{{ name }}</span>
      {% endif %}
    </div>
  {% endfor %}
</div>
```

---

## 4️⃣ CSS Styling Solutions

### 4.1 Complete Icon Styling

**File**: `source/patterns/elements/icon/icon.css`

```css
/**
 * Icon Component - Styling
 * 
 * Supports both font-based and sprite-based icons
 * Uses design tokens for sizing and colors
 */

/* ========================================
   Root styles
   ======================================== */

.ps-icon {
  /* Display & layout */
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;

  /* Default sizing */
  width: var(--size-icon-md, 24px);
  height: var(--size-icon-md, 24px);

  /* Default styling */
  color: var(--color-text-default, currentColor);
  fill: currentColor;
  stroke: currentColor;

  /* Font smoothing (for font-based) */
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-feature-settings: 'liga';

  /* Prevent layout shift */
  line-height: 1;

  /* Accessibility */
  &[aria-hidden="true"] {
    pointer-events: none;
  }
}

/* ========================================
   Font-based icon styles
   ======================================== */

.ps-icon[data-icon] {
  font-family: var(--font-icon, 'bnpre-icons');
  font-size: 1em;
  font-weight: 400;
  font-style: normal;
  font-variant: normal;
  text-decoration: inherit;
  text-transform: none;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
}

.ps-icon[data-icon]::before {
  content: attr(data-icon);
}

/* ========================================
   SVG sprite styles
   ======================================== */

.ps-icon svg {
  width: 1em;
  height: 1em;
  /* Allow use element to render */
  overflow: visible;
}

.ps-icon use {
  overflow: visible;
}

/* ========================================
   Size variants
   ======================================== */

.ps-icon--xs {
  width: var(--size-icon-xs, 10px);
  height: var(--size-icon-xs, 10px);
  font-size: 10px;
}

.ps-icon--sm {
  width: var(--size-icon-sm, 16px);
  height: var(--size-icon-sm, 16px);
  font-size: 16px;
}

.ps-icon--lg {
  width: var(--size-icon-lg, 32px);
  height: var(--size-icon-lg, 32px);
  font-size: 32px;
}

.ps-icon--xl {
  width: var(--size-icon-xl, 48px);
  height: var(--size-icon-xl, 48px);
  font-size: 48px;
}

.ps-icon--xxl {
  width: var(--size-icon-xxl, 64px);
  height: var(--size-icon-xxl, 64px);
  font-size: 64px;
}

/* ========================================
   Color variants (semantic)
   ======================================== */

.ps-icon--primary {
  color: var(--color-primary, #00915a);
}

.ps-icon--secondary {
  color: var(--color-secondary, #b3008f);
}

.ps-icon--success {
  color: var(--color-success, #28a745);
}

.ps-icon--warning {
  color: var(--color-warning, #ffc107);
}

.ps-icon--danger {
  color: var(--color-danger, #dc3545);
}

.ps-icon--info {
  color: var(--color-info, #17a2b8);
}

/* ========================================
   States
   ======================================== */

.ps-icon--disabled {
  opacity: 0.5;
  pointer-events: none;
  cursor: not-allowed;
}

.ps-icon:focus-visible {
  outline: 2px solid var(--color-focus, #4d90fe);
  outline-offset: 2px;
  border-radius: 2px;
}

/* ========================================
   Interactive contexts
   ======================================== */

/* In buttons */
button .ps-icon,
a .ps-icon {
  transition: color 150ms ease-in-out;
}

button:hover .ps-icon,
a:hover .ps-icon {
  color: var(--color-text-hover);
}

button:active .ps-icon,
a:active .ps-icon {
  color: var(--color-text-active);
}

/* In form inputs */
input:focus ~ .ps-icon,
input:focus-within ~ .ps-icon {
  color: var(--color-primary);
}

/* ========================================
   Dark mode support
   ======================================== */

@media (prefers-color-scheme: dark) {
  .ps-icon {
    color: var(--color-text-default-dark, #ffffff);
  }
}

/* ========================================
   High contrast mode
   ======================================== */

@media (prefers-contrast: more) {
  .ps-icon {
    stroke-width: 1.5;
  }

  .ps-icon--disabled {
    opacity: 0.3;
    text-decoration: line-through;
  }
}

/* ========================================
   Animation support
   ======================================== */

.ps-icon--spin {
  animation: ps-icon-spin 1s linear infinite;
}

.ps-icon--bounce {
  animation: ps-icon-bounce 0.6s ease-in-out;
}

@keyframes ps-icon-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@keyframes ps-icon-bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-4px);
  }
}

/* ========================================
   Composition patterns (BEM modifiers)
   ======================================== */

/* When used with other components */
.ps-button .ps-icon {
  margin-right: var(--size-2, 4px);
  vertical-align: middle;
}

.ps-badge .ps-icon {
  margin-left: auto;
}

.ps-input-group .ps-icon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  pointer-events: none;
}
```

### 4.2 Icon Gallery Styles

**File**: `source/patterns/elements/icon/icon-gallery.css`

```css
.ps-icon-gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: var(--size-4, 16px);
  padding: var(--size-4, 16px);
}

.ps-icon-gallery__item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--size-2, 8px);
  padding: var(--size-3, 12px);
  border-radius: var(--radius-base, 4px);
  transition: background-color 150ms ease-in-out;
  cursor: pointer;

  &:hover {
    background-color: var(--color-bg-hover, #f5f5f5);
  }
}

.ps-icon-gallery__label {
  font-size: var(--font-size-sm, 12px);
  color: var(--color-text-secondary, #666);
  word-break: break-word;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}
```

---

## 5️⃣ Storybook Story Examples

### 5.1 Basic Icon Stories

**File**: `source/patterns/elements/icon/icon.stories.jsx`

```jsx
import iconTwig from './icon.twig';
import iconGalleryTwig from './icon-gallery.twig';
import data from './icon.yml';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Semantic icon component with multiple sizes, colors, and states.',
      },
    },
  },
  argTypes: {
    name: {
      description: 'Icon name (without "icon-" prefix)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'search' },
      },
    },
    size: {
      description: 'Icon size',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'md' },
      },
    },
    color: {
      description: 'Semantic color',
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'default' },
      },
    },
    disabled: {
      description: 'Disabled state',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        defaultValue: { summary: false },
      },
    },
    ariaLabel: {
      description: 'Accessibility label',
      control: { type: 'text' },
      table: { category: 'Accessibility' },
    },
  },
};

export const Default = {
  render: (args) => iconTwig(args),
  args: { ...data },
};

export const AllSizes = {
  render: (args) => `
    <div style="display: flex; align-items: center; gap: var(--size-6, 24px);">
      ${iconTwig({ ...args, size: 'xs' })}
      ${iconTwig({ ...args, size: 'sm' })}
      ${iconTwig({ ...args, size: 'md' })}
      ${iconTwig({ ...args, size: 'lg' })}
      ${iconTwig({ ...args, size: 'xl' })}
      ${iconTwig({ ...args, size: 'xxl' })}
    </div>
  `,
  args: { ...data },
  parameters: {
    docs: {
      description: {
        story: 'All available sizes from extra small (10px) to extra large (64px).',
      },
    },
  },
};

export const AllColors = {
  render: (args) => `
    <div style="display: flex; align-items: center; gap: var(--size-6, 24px); flex-wrap: wrap;">
      ${iconTwig({ ...args, color: 'default' })}
      ${iconTwig({ ...args, color: 'primary' })}
      ${iconTwig({ ...args, color: 'secondary' })}
      ${iconTwig({ ...args, color: 'success' })}
      ${iconTwig({ ...args, color: 'warning' })}
      ${iconTwig({ ...args, color: 'danger' })}
      ${iconTwig({ ...args, color: 'info' })}
    </div>
  `,
  args: { ...data, name: 'check', size: 'lg' },
};

export const States = {
  render: (args) => `
    <div style="display: flex; gap: var(--size-8, 32px);">
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2, 8px);">
        ${iconTwig({ ...args, disabled: false })}
        <span style="font-size: 12px; color: #666;">Enabled</span>
      </div>
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2, 8px);">
        ${iconTwig({ ...args, disabled: true })}
        <span style="font-size: 12px; color: #666;">Disabled</span>
      </div>
    </div>
  `,
  args: { ...data, size: 'lg' },
};
```

### 5.2 Icon Gallery Story with Dynamic Loading

**File**: `source/patterns/elements/icon/icon-gallery.stories.jsx`

```jsx
import iconGalleryTwig from './icon-gallery.twig';
import iconList from '../../../dist/sprites/icons.json';

export default {
  title: 'Elements/Icon/Gallery',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Complete gallery of all available icons.',
      },
    },
  },
};

export const AllIcons = {
  render: (args) => {
    const icons = iconList.icons.map(id => id.replace('icon-', ''));
    return iconGalleryTwig({ icons, size: 'lg', showLabels: true });
  },
  parameters: {
    layout: 'fullscreen',
  },
};

export const ByCategory = {
  render: () => {
    const categories = iconList.categories;
    
    return `
      <div style="padding: var(--size-6, 24px);">
        ${Object.entries(categories).map(([category, icons]) => {
          const iconNames = icons.map(id => id.replace('icon-', ''));
          return `
            <div style="margin-bottom: var(--size-8, 32px);">
              <h3 style="margin: 0 0 var(--size-4, 16px) 0; font-size: 18px; font-weight: 600; text-transform: capitalize;">
                ${category}
              </h3>
              ${iconGalleryTwig({ icons: iconNames, size: 'md' })}
            </div>
          `;
        }).join('')}
      </div>
    `;
  },
  parameters: {
    layout: 'fullscreen',
  },
};

export const Searchable = {
  render: (args) => {
    const allIcons = iconList.icons.map(id => id.replace('icon-', ''));
    
    return `
      <div>
        <input 
          type="search" 
          id="icon-search" 
          placeholder="Search icons..."
          style="width: 100%; padding: var(--size-3, 12px); margin-bottom: var(--size-4, 16px); border: 1px solid #ddd; border-radius: 4px;"
        />
        <div id="icon-results">
          ${iconGalleryTwig({ icons: allIcons, size: 'lg' })}
        </div>
      </div>
      
      <script>
        document.getElementById('icon-search').addEventListener('input', (e) => {
          const query = e.target.value.toLowerCase();
          const results = document.querySelectorAll('.ps-icon-gallery__item');
          
          results.forEach(item => {
            const label = item.querySelector('.ps-icon-gallery__label')?.textContent || '';
            item.style.display = label.includes(query) ? '' : 'none';
          });
        });
      </script>
    `;
  },
  parameters: {
    layout: 'fullscreen',
  },
};
```

---

## 6️⃣ JavaScript Utilities

### 6.1 Icon Sprite Loader

**File**: `source/patterns/elements/icon/icon-sprite-loader.js`

```javascript
/**
 * SVG Sprite Loader Utility
 * 
 * Handles injection and caching of SVG sprites
 */

class SpriteLoader {
  constructor(config = {}) {
    this.config = {
      spritePath: config.spritePath || '/dist/sprites/icons.svg',
      className: config.className || 'ps-icon-sprite',
      cacheKey: config.cacheKey || 'ps_icon_sprite_loaded',
      timeout: config.timeout || 5000,
      ...config
    };
    this.cache = new Map();
  }

  /**
   * Check if sprite is already loaded
   */
  isLoaded() {
    return document.querySelector(`.${this.config.className}`) !== null;
  }

  /**
   * Load and inject sprite
   */
  async load(category = 'all') {
    // Return cached sprite if available
    if (this.isLoaded()) {
      return this.cache.get(category);
    }

    try {
      const response = await Promise.race([
        fetch(this.config.spritePath),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error('Sprite load timeout')), this.config.timeout)
        )
      ]);

      if (!response.ok) {
        throw new Error(`Failed to load sprite: ${response.statusText}`);
      }

      const svg = await response.text();
      const sprite = this.parseSVG(svg);
      
      // Inject into DOM
      this.injectSprite(sprite);
      
      // Cache result
      this.cache.set(category, sprite);
      
      return sprite;
    } catch (error) {
      console.error('Sprite loader error:', error);
      return null;
    }
  }

  /**
   * Parse SVG string into DOM element
   */
  parseSVG(svgString) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(svgString, 'image/svg+xml');
    
    if (doc.querySelector('parsererror')) {
      throw new Error('Invalid SVG syntax');
    }
    
    return doc.querySelector('svg');
  }

  /**
   * Inject sprite into document
   */
  injectSprite(sprite) {
    if (!sprite) return;

    // Add class if not present
    sprite.classList.add(this.config.className);
    
    // Insert at beginning of body
    if (!document.body.firstChild) {
      document.body.appendChild(sprite);
    } else {
      document.body.insertBefore(sprite, document.body.firstChild);
    }
  }

  /**
   * Get available icon IDs
   */
  getIconIds() {
    const sprite = document.querySelector(`.${this.config.className}`);
    if (!sprite) return [];
    
    return Array.from(sprite.querySelectorAll('symbol'))
      .map(s => s.id);
  }

  /**
   * Check if icon exists
   */
  hasIcon(iconId) {
    const sprite = document.querySelector(`.${this.config.className}`);
    if (!sprite) return false;
    
    return sprite.querySelector(`#${iconId}`) !== null;
  }

  /**
   * Unload sprite (cleanup)
   */
  unload() {
    const sprite = document.querySelector(`.${this.config.className}`);
    if (sprite) {
      sprite.remove();
    }
    this.cache.clear();
  }
}

export default SpriteLoader;
```

### 6.2 Icon Utility Functions

**File**: `source/patterns/elements/icon/icon-utils.js`

```javascript
/**
 * Icon utility functions
 */

/**
 * Generate icon HTML (for use in JS)
 */
export function renderIcon(name, options = {}) {
  const {
    size = 'md',
    color = 'default',
    disabled = false,
    ariaLabel = null,
    classes = ''
  } = options;

  const classList = [
    'ps-icon',
    size !== 'md' && `ps-icon--${size}`,
    color !== 'default' && `ps-icon--${color}`,
    disabled && 'ps-icon--disabled',
    classes
  ].filter(Boolean).join(' ');

  const svg = `
    <svg class="${classList}" 
         ${ariaLabel ? `aria-label="${ariaLabel}" role="img"` : 'aria-hidden="true"'}>
      <use href="#icon-${name}" />
    </svg>
  `;

  return svg;
}

/**
 * Convert icon name for display
 */
export function formatIconName(name) {
  return name
    .replace(/^icon-/, '')
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}

/**
 * Validate icon exists in sprite
 */
export function validateIcon(name, sprite) {
  if (!sprite) return false;
  return sprite.querySelector(`#icon-${name}`) !== null;
}

/**
 * Get all icons from sprite
 */
export function getAllIcons(sprite) {
  if (!sprite) return [];
  
  return Array.from(sprite.querySelectorAll('symbol'))
    .map(s => s.id.replace('icon-', ''));
}

/**
 * Filter icons by keyword
 */
export function searchIcons(keyword, sprite) {
  const icons = getAllIcons(sprite);
  return icons.filter(icon => 
    icon.toLowerCase().includes(keyword.toLowerCase())
  );
}

export default {
  renderIcon,
  formatIconName,
  validateIcon,
  getAllIcons,
  searchIcons
};
```

---

## 7️⃣ Drupal Integration Guide

### 7.1 Drupal Component Declaration (SDC)

**File**: `source/patterns/elements/icon/icon.component.yml`

```yaml
# @see https://www.drupal.org/docs/drupal-apis/single-directory-components

$schema: https://git.drupalcode.org/project/drupal/-/raw/11.x/core/modules/sdc/src/Component/schema.json

name: 'PS Icon'
status: stable
group: atoms
category: atoms
description: 'Semantic icon component with multiple sizes, colors, and states.'
icon: icon.svg

props:
  type: object
  properties:
    name:
      type: string
      title: Icon Name
      description: 'Icon name without "icon-" prefix (e.g., "check", "arrow-right")'
      minLength: 1
      examples:
        - check
        - search
        - arrow-right
    
    size:
      type: string
      enum: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
      default: 'md'
      title: Size
      description: 'Icon size: xs (10px), sm (16px), md (24px), lg (32px), xl (48px), xxl (64px)'
    
    color:
      type: string
      enum: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info']
      default: 'default'
      title: Color
      description: 'Semantic color variant'
    
    disabled:
      type: boolean
      default: false
      title: Disabled
      description: 'Apply disabled state (50% opacity)'
    
    ariaLabel:
      type: string
      title: ARIA Label
      description: 'Accessibility label for informative icons (omit for decorative icons)'

slots: {}

libraryOverrides:
  - '@bnp/ps-icon': '@ps_theme/ps-icon'
```

### 7.2 Drupal Component Usage in Twig

```twig
{# In a Drupal component that uses icons #}

{# Single icon #}
{% set icon %}
  {% include 'components:ps-icon' only with {
    name: 'check',
    size: 'lg',
    color: 'success',
    ariaLabel: 'Item selected'
  } %}
{% endset %}

{# In a table cell #}
<td>
  {% include 'components:ps-icon' only with {
    name: attributes.hasClass('favorite') ? 'heart' : 'heart-outline',
    color: attributes.hasClass('favorite') ? 'danger' : 'default',
    ariaLabel: 'Toggle favorite'
  } %}
</td>

{# Icon in button (composition) #}
<button class="ps-button">
  {% include 'components:ps-icon' only with {
    name: 'arrow-right',
    size: 'sm'
  } %}
  Next
</button>
```

### 7.3 Drupal Preprocess Function

```php
<?php
// In theme's .theme file

/**
 * Preprocess function for icon component
 */
function ps_theme_preprocess_ps_icon(&$variables) {
  // Ensure name is always set
  if (empty($variables['name'])) {
    $variables['name'] = 'help';
  }

  // Build accessible description if needed
  if (!empty($variables['aria_label']) && $variables['aria_label'] !== '') {
    $variables['attributes']['role'] = 'img';
  } else {
    $variables['attributes']['aria-hidden'] = 'true';
  }

  // Add data attributes for styling
  $variables['attributes']['data-icon-name'] = $variables['name'];
  $variables['attributes']['data-icon-size'] = $variables['size'] ?? 'md';
}
```

---

## 8️⃣ Performance Optimization

### 8.1 Sprite Size Optimization

```bash
# 1. Optimize SVG files with SVGO
npm install svgo --save-dev

# 2. Remove metadata
svgo -f source/assets/icons/ --enable=removeMetadata

# 3. Remove unnecessary attributes
svgo -f source/assets/icons/ --enable=removeDoctype,removeComments

# 4. Generate optimized sprite
npm run sprite:generate
```

### 8.2 Caching Headers (Nginx)

```nginx
# In nginx.conf or vhost config

location ~* \.svg$ {
  expires 1y;
  add_header Cache-Control "public, immutable";
  add_header Access-Control-Allow-Origin "*";
}

location /dist/sprites/ {
  expires 30d;
  add_header Cache-Control "public";
}
```

### 8.3 Performance Monitoring

**File**: `source/patterns/elements/icon/icon-performance.js`

```javascript
/**
 * Monitor icon loading and rendering performance
 */

export class IconPerformanceMonitor {
  constructor() {
    this.metrics = {
      spriteLoadTime: null,
      spriteSize: null,
      iconRenderTime: null
    };
  }

  measureSpriteLoad() {
    const startTime = performance.now();
    
    return fetch('/dist/sprites/icons.svg')
      .then(response => {
        const endTime = performance.now();
        this.metrics.spriteLloadTime = endTime - startTime;
        this.metrics.spriteSize = response.headers.get('content-length');
        
        console.log(`Sprite loaded in ${this.metrics.spriteLloadTime.toFixed(2)}ms`);
        console.log(`Sprite size: ${(this.metrics.spriteSize / 1024).toFixed(2)}KB`);
        
        return response.text();
      });
  }

  measureIconRender(iconName) {
    const startTime = performance.now();
    
    // Simulate icon rendering
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
    use.setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', `#icon-${iconName}`);
    svg.appendChild(use);
    
    const endTime = performance.now();
    this.metrics.iconRenderTime = endTime - startTime;
    
    console.log(`Icon render took ${this.metrics.iconRenderTime.toFixed(4)}ms`);
    
    return svg;
  }

  getReport() {
    return {
      timestamp: new Date().toISOString(),
      ...this.metrics,
      recommendation: this.metrics.spriteLloadTime > 500 
        ? 'Consider lazy loading sprite' 
        : 'Performance is good'
    };
  }
}

// Usage
const monitor = new IconPerformanceMonitor();
monitor.measureSpriteLoad().then(() => {
  monitor.measureIconRender('check');
  console.log(monitor.getReport());
});
```

---

## Summary

This technical guide provides:

✅ **Complete implementation examples** for both font and sprite approaches  
✅ **Vite + Twig integration** for seamless build pipeline  
✅ **Storybook stories** with galleries and search  
✅ **CSS solutions** for all icons variants  
✅ **JavaScript utilities** for dynamic loading  
✅ **Drupal integration** with component declarations  
✅ **Performance optimization** tips and monitoring  

Choose the approach that best fits your project's needs. For PS Theme, **Phase 2 (sprite support) is recommended** to modernize while maintaining backward compatibility.

---

**Related Files**:
- `docs/ICON_MANAGEMENT_BEST_PRACTICES.md` - Strategic overview
- `source/patterns/elements/icon/` - Component directory
- `scripts/generate-sprite.mjs` - Sprite generation
- `vite.config.js` - Build configuration

