# 🏗️ Icon System Architecture - Visual Reference

**Purpose**: Single-page architecture reference for the proposed icon system  
**Audience**: Developers, architects, decision makers

---

## 🎯 System Architecture Overview

```
┌────────────────────────────────────────────────────────────────────────────┐
│                          ICON SYSTEM ARCHITECTURE                           │
└────────────────────────────────────────────────────────────────────────────┘

┌─ SOURCES ───────────────────────────────────────────────────────────────────┐
│                                                                              │
│  source/icons-source/                                                       │
│  ├── check.svg                              SINGLE SOURCE OF TRUTH ✨        │
│  ├── search.svg                            (139 SVG files)                  │
│  ├── arrow-right.svg                                                        │
│  └── ... (139 total)                                                        │
│                                                                              │
└─────────────────────────────────────────────────┬──────────────────────────┘
                                                 │
                                 BUILD PROCESS (npm run build)
                                                 │
                ┌────────────────────────────────┼────────────────────────────┐
                │                                │                            │
                ↓                                ↓                            ↓
   
┌──────────────────────────┐  ┌─────────────────────────────┐  ┌──────────────────┐
│   SPRITE SVG             │  │  CSS RULES (GENERATED)      │  │  REGISTRY (JSON) │
│                          │  │                             │  │                  │
│  /icons/icons-sprite.svg │  │  source/props/              │  │  source/patterns/│
│  ├─ symbol#icon-check    │  │  icons-generated.css        │  │  icons-registry  │
│  ├─ symbol#icon-search   │  │                             │  │  .json           │
│  ├─ symbol#icon-arrow-*  │  │  [data-icon="check"] {      │  │  {               │
│  └─ ... (139 symbols)    │  │    background-image:        │  │    "names": [... │
│                          │  │      url('...#icon-check')  │  │    ]             │
│  ✅ Used by all 3        │  │  }                          │  │    "total": 139  │
│     access patterns      │  │                             │  │    "categories"  │
│                          │  │  [data-icon="search"] {     │  │    {...}         │
│                          │  │    background-image:        │  │  }               │
│                          │  │      url('...#icon-search') │  │                  │
│                          │  │  }                          │  │  ✅ Validates    │
│                          │  │                             │  │     icon names   │
│                          │  │  ... (139 total rules)      │  │  ✅ Used by      │
│                          │  │                             │  │     linting      │
│                          │  │  ✅ Auto-generated          │  │  ✅ Consumed by  │
│                          │  │  ✅ Complete (all 139)      │  │     Storybook    │
│                          │  │  ✅ Maintained in source    │  │                  │
│                          │  │  ✅ Zero manual edits       │  │                  │
│                          │  │                             │  │                  │
└──────────────────────────┘  └─────────────────────────────┘  └──────────────────┘
        │                                 │                            │
        └─────────────────────────┬───────┴────────────────────────────┘
                                  │
                    CONSUMPTION LAYER (3 Patterns)
                                  │
    ┌─────────────────────────────┼─────────────────────────────┐
    │                             │                             │
    ↓                             ↓                             ↓

┌───────────────────┐  ┌──────────────────────────┐  ┌────────────────────┐
│ PATTERN A         │  │ PATTERN B                │  │ PATTERN C          │
│ ps-icon Component │  │ data-icon Attribute      │  │ SVG Sprite Direct  │
│                   │  │                          │  │                    │
│ {% include        │  │ <span                    │  │ <svg aria-hidden>  │
│   '@elements/     │  │   data-icon="check"      │  │   <use href="...   │
│   icon/icon.twig' │  │   aria-hidden="true">    │  │   #icon-check">    │
│   with {          │  │ </span>                  │  │   </use>           │
│     name: 'check',│  │                          │  │ </svg>             │
│     size: 'lg',   │  │ CSS-driven sizing (1em)  │  │                    │
│     color:        │  │ Minimal markup           │  │ Full control       │
│     'primary'     │  │ Good for decorative      │  │ Animations OK      │
│   }               │  │                          │  │                    │
│ only %}           │  │ ✅ SIMPLE                │  │ ✅ FLEXIBLE        │
│                   │  │ ✅ LIGHTWEIGHT           │  │                    │
│ <span class="     │  │ ✅ ACCESSIBLE            │  │ ⚠️ Manual ARIA     │
│  ps-icon          │  │                          │  │                    │
│  ps-icon--lg      │  │                          │  │                    │
│  ps-icon--primary"│  │                          │  │                    │
│  aria-label="...">│  │                          │  │                    │
│   <svg>           │  │                          │  │                    │
│     <use ...>     │  │                          │  │                    │
│   </svg>          │  │                          │  │                    │
│ </span>           │  │                          │  │                    │
│                   │  │                          │  │                    │
│ ✅ RECOMMENDED    │  │                          │  │                    │
│ ✅ FULL CONTROL   │  │                          │  │                    │
│ ✅ ACCESSIBLE     │  │                          │  │                    │
│                   │  │                          │  │                    │
│ Use when:         │  │ Use when:                │  │ Use when:          │
│ - Need styling    │  │ - Simple icon            │  │ - Custom SVG       │
│ - ARIA label      │  │ - Decorative             │  │ - Animations       │
│ - Color variant   │  │ - No variations          │  │ - Raw control      │
│ - Size variant    │  │                          │  │                    │
└───────────────────┘  └──────────────────────────┘  └────────────────────┘

        │                       │                          │
        │                       │                          │
        └─────────────┬─────────┴──────────────┬──────────┘
                      │                        │
                      ↓                        ↓
            
            VALIDATION & BUILD
            
            ✅ Build checks:
               • All code names in registry?
               • All sprites have CSS rules?
               • All CSS rules have sprites?
               
            ✅ Optional Linting:
               • Twig: icon name in registry?
               • TypeScript: type-safe icon names?
```

---

## 🎯 Decision Matrix: Which Pattern to Use?

```
START: You need to use an icon
  │
  ├─ Need styling control (size, color, state)?
  │  │
  │  ├─ YES → PATTERN A: ps-icon Component ✅
  │  │        {% include '@elements/icon/icon.twig' with {...} %}
  │  │        ✓ Recommended for most cases
  │  │        ✓ Full styling, accessibility, encapsulation
  │  │
  │  └─ NO
  │     │
  │     ├─ Need accessibility label (informative icon)?
  │     │  │
  │     │  ├─ YES → PATTERN A: ps-icon Component with ariaLabel
  │     │  │        {% include icon.twig with { name: 'info', ariaLabel: '...' } %}
  │     │  │
  │     │  └─ NO → continue to next question
  │     │
  │     ├─ Simple static decorative icon?
  │     │  │
  │     │  ├─ YES → PATTERN B: data-icon Attribute ✅
  │     │  │        <span data-icon="arrow" aria-hidden="true"></span>
  │     │  │        ✓ Lightweight, CSS-driven
  │     │  │
  │     │  └─ NO
  │     │     │
  │     │     ├─ Need custom SVG animations/styling?
  │     │     │  │
  │     │     │  ├─ YES → PATTERN C: SVG Sprite Direct ✅
  │     │     │  │        <svg><use href="...#icon-spinner"></use></svg>
  │     │     │  │        ✓ Full control, animations OK
  │     │     │  │
  │     │     │  └─ NO → Prefer PATTERN A for consistency
```

---

## 📊 Patterns Comparison

```
╔════════════════════════════════════════════════════════════════════════════╗
║                   ICON PATTERN COMPARISON MATRIX                            ║
╠════╦═════════════════╦═════════════════╦════════════════════╦═══════════════╣
║ #  ║ ps-icon Component ║ data-icon       ║ SVG Direct         ║ Criteria      ║
║    ║ (PATTERN A)       ║ (PATTERN B)     ║ (PATTERN C)        ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║    ║                   ║                 ║                    ║              ║
║ 📝 ║ {% include        ║ <span           ║ <svg>              ║ Syntax       ║
║    ║ '@elements/       ║   data-icon="X" ║   <use href="..."/> ║              ║
║    ║ icon/icon.twig' %} ║ </span>        ║ </svg>             ║              ║
║    ║                   ║                 ║                    ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ 🎨 ║ ✅✅ Full         ║ ✅ Size (1em)    ║ ✅✅ Full control   ║ Styling      ║
║    ║ (size, color,    ║ ⚠️  Color (need  ║ (SVG properties)   ║ Control      ║
║    ║  state)          ║    parent class) ║                    ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ 🔤 ║ ✅ Simple         ║ ✅✅ Very simple ║ ⚠️  Manual          ║ Markup       ║
║    ║ (component API)  ║                 ║ (need aria)        ║ Complexity   ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ ♿ ║ ✅✅ WCAG AA      ║ ✅ WCAG AA       ║ ⚠️  Manual          ║ Accessibility║
║    ║ (aria-label,     ║ (data-icon +    ║ (must add aria)    ║              ║
║    ║  roles)          ║  aria-hidden)   ║                    ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ 🚀 ║ ✅ Medium         ║ ✅✅ Low         ║ ⚠️  Medium          ║ Performance  ║
║    ║ (SVG element)    ║ (CSS only)      ║ (SVG element)      ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ 🎭 ║ ✅✅ Full         ║ ✅ Limited       ║ ✅✅ Full           ║ Animations   ║
║    ║ (SMIL, CSS)      ║ (CSS only)      ║ (SMIL + CSS)       ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ 🔧 ║ ✅ With component║ ✅✅ In CSS      ║ ✅ In SVG           ║ Customization║
║    ║  props           ║                 ║ attributes         ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║ 📱 ║ ✅ Responsive    ║ ✅ Responsive   ║ ✅ Responsive      ║ Responsive   ║
║    ║ (em-based)       ║ (em-based)      ║ (viewBox-based)    ║              ║
╠════╬═════════════════╬═════════════════╬════════════════════╬═══════════════╣
║    ║ ✅✅ Most cases  ║ ✅ Decorative   ║ ✅ Animations      ║ Best For     ║
║    ║ ✅ Need styling  ║    icons        ║    Edge cases      ║              ║
║    ║ ✅ ARIA labels   ║ ✅ Static       ║ ✅ Raw control     ║              ║
║    ║ ✅ Consistency   ║    badges       ║                    ║              ║
║    ║                 ║ ✅ Simple       ║                    ║              ║
║    ║                 ║    contexts     ║                    ║              ║
╚════╩═════════════════╩═════════════════╩════════════════════╩═══════════════╝
```

---

## 🔄 Data Flow: Icon from Source to Consumption

```
┌─────────────────────────────────────────────────────────────────────┐
│  STEP 1: Development                                                 │
│                                                                      │
│  Developer drops new SVG                                            │
│  $ cp my-icon.svg source/icons-source/                             │
│  → Adds ONE file                                                    │
│                                                                      │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│  STEP 2: Build Process ($ npm run build)                           │
│                                                                      │
│  scripts/build-icons.mjs                                           │
│  ├─ Scans: source/icons-source/*.svg                              │
│  ├─ Extracts names: ['check', 'search', 'my-icon', ...]           │
│  └─ Generates (AUTO):                                              │
│     ├─ Sprite: icons-sprite.svg with all symbols                  │
│     ├─ CSS: icons-generated.css with all [data-icon="..."] rules  │
│     └─ JSON: icons-registry.json with validation map              │
│                                                                      │
│  ✅ ALL outputs synchronized automatically                         │
│  ✅ No manual edits needed                                         │
│                                                                      │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│  STEP 3: Validation (Optional)                                      │
│                                                                      │
│  Build checks:                                                      │
│  - All names in registry? ✅                                       │
│  - All sprites have CSS? ✅                                        │
│  - All CSS have sprites? ✅                                        │
│                                                                      │
│  Optional Linting:                                                  │
│  - Twig: icon 'my-icon' in registry? ✅                           │
│  - TypeScript: type IconName = 'check' | 'my-icon' | ... ✅       │
│                                                                      │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│  STEP 4: Consumption (Developer uses icon)                         │
│                                                                      │
│  Pattern A: Component (RECOMMENDED)                                 │
│  {% include '@elements/icon/icon.twig' with                        │
│    { name: 'my-icon', size: 'lg', color: 'primary' }              │
│  %}                                                                  │
│                                                                      │
│  Pattern B: Attribute                                               │
│  <span data-icon="my-icon" aria-hidden="true"></span>             │
│                                                                      │
│  Pattern C: Direct SVG                                              │
│  <svg><use href="/icons/icons-sprite.svg#icon-my-icon"/></svg>    │
│                                                                      │
│  ✅ All 3 work automatically                                       │
│  ✅ Icon name guaranteed to exist (validated in build)            │
│  ✅ Sprite + CSS + Registration all in sync                       │
│                                                                      │
└──────────────────────────────┬──────────────────────────────────────┘
                               │
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│  STEP 5: Browser Rendering                                          │
│                                                                      │
│  Pattern A Output:                                                   │
│  <span class="ps-icon ps-icon--lg ps-icon--primary">              │
│    <svg><use href="/icons/icons-sprite.svg#icon-my-icon"/></svg>  │
│  </span>                                                             │
│                                                                      │
│  Pattern B Output:                                                   │
│  <span data-icon="my-icon">                                        │
│    (CSS: background-image: url('...#icon-my-icon'))               │
│  </span>                                                             │
│                                                                      │
│  Pattern C Output:                                                   │
│  <svg><use href="/icons/icons-sprite.svg#icon-my-icon"/></svg>    │
│                                                                      │
│  ✅ Icon displays correctly across all patterns                   │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ Build System Block Diagram

```
INPUT                   PROCESS                  OUTPUT
─────────────────────────────────────────────────────────

source/icons-source/        Scan SVGs
├── *.svg (139)  ────────→  Extract names  ────→  iconNames array
│                          
│                  ┌─ Generate Sprite ──────→  icons-sprite.svg
│                  │        (symbols)
│                  │
├─ build process ─┼─ Generate CSS ─────────→  icons-generated.css
│   (npm build)   │        (rules)            (139 [data-icon] rules)
│                  │
│                  └─ Generate Registry ──→  icons-registry.json
│                          (JSON)            (validation map)
│
│                  Optional:
│                  ├─ Generate TypeScript ─→  icon-types.d.ts
│                  │        (types)          (type IconName = ...)
│                  │
│                  └─ Validate Outputs ────→  Build status
│                          (checks)          (success/fail)
```

---

## 📈 Migration Path (No Breaking Changes)

```
BEFORE: Fragmented System
┌────────────────────┐
│  data-icon rules   │  ← Manual icons.css (35 icons)
│  ps-icon component │  ← Modern approach (139 icons)
│  SVG direct        │  ← Edge cases
│  ⚠️ Inconsistent   │
└────────────────────┘

  ↓ (Apply solution)

AFTER: Unified System
┌────────────────────────────────┐
│  ✅ ALL 139 icons available    │
│  ✅ data-icon (auto CSS)       │  ← Complete (139 rules)
│  ✅ ps-icon component          │  ← Unchanged
│  ✅ SVG direct                 │  ← Unchanged
│  ✅ Validated backend          │  ← New: registry
│  ✅ Clear decision matrix       │  ← New: docs
│  ✅ Auto-generated assets      │  ← New: no manual edits
└────────────────────────────────┘

  ✅ BACKWARD COMPATIBLE
  ✅ ZERO BREAKING CHANGES
  ✅ ALL OLD CODE STILL WORKS
  ✅ PHASED MIGRATION POSSIBLE
```

---

## 🎯 Key Metrics

```
BEFORE                              AFTER
──────────────────────────────────────────────────────

Icons available via data-icon:      Icons available via data-icon:
  35/139 (25%)                        139/139 (100%) ✅

Icons.css lines:
  ~35 manual rules                  ~500 auto-generated rules ✅

Maintenance per new icon:
  Edit 3 files (svg, json, css)     Drop SVG only (auto-sync) ✅

Developer confusion:
  Which pattern to use? 🤔          Clear decision tree ✅

Build validation:
  None                              Registry + CI checks ✅

WCAG compliance:
  Partial (some icons missing)      Complete (all patterns) ✅

Scalability:
  Hard (manual edits)               Easy (auto-generated) ✅
```

---

**This architecture ensures**:
- ✅ Single source of truth (SVG folder)
- ✅ Automatic synchronization (no manual sync)
- ✅ Zero breaking changes (backward compatible)
- ✅ Clear patterns (decision matrix)
- ✅ Full validation (registry checks)
- ✅ Better DX (easy to add icons)
