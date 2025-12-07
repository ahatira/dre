import brand from './brand.twig';
import data from './brand.yml';

/**
 * BNP Paribas RealEstate Brand System - Semantic Color Tokens
 *
 * 3-Layer Architecture:
 * 1. source/props/colors.css   → Official BNP palettes (9 palettes: GREEN, PINK, TEAL, RED, YELLOW, BLUE, SKY, GREY, GOLD)
 * 2. source/props/brand.css    → Semantic tokens mapping colors to meanings
 * 3. Components               → Reference semantic tokens only
 *
 * 97 tokens total:
 * - Semantic Colors (81 tokens): 9 colors × 9 states each
 * - Text Colors (4 tokens): Primary, Secondary, Disabled, Inverse
 * - Border Colors (6 tokens): Default, Light, Focus, Error, Success, Disabled
 * - Overlay Colors (6 tokens): Dark and Brand variations
 *
 * Key Distinctions:
 * - PRIMARY (Green #00915A): Brand identity, primary actions
 * - SUCCESS (Teal #198754):  System feedback, success states (DISTINCT from primary!)
 * - GOLD (#D1AE6E): Accent color for premium features, highlights, decorative elements
 */

const settings = {
  title: 'Base/Brand',
  parameters: {
    docs: {
      description: {
        component: `
## BNP Paribas RealEstate Brand System - Semantic Color Tokens

Complete design token system (97 tokens) based on official BNP Paribas RealEstate color palette.

### 3-Layer Architecture

\`\`\`
colors.css (Official BNP Palettes)
    ↓
brand.css (Semantic Token Mapping)
    ↓
Components (var() References)
\`\`\`

**Layer 1: Official Palettes** (source/props/colors.css)
- PRIMARY GREEN: #00915A (bnp official)
- SECONDARY PINK: #A12B66 (bnp official)
- SUCCESS TEAL: #198754 (distinct from primary!)
- ERROR RED: #EB3636 (bnp official)
- GOLD: #D1AE6E (accent color)
- WARNING YELLOW, INFO BLUE, SKY, GREY

**Layer 2: Semantic Tokens** (source/props/brand.css)
- 9 semantic colors with 9 states each
- Each color: base, hover, active, text, border, subtle, bg-subtle, border-subtle, text-emphasis

### Usage Examples

\`\`\`css
/* Primary Brand Color */
background: var(--primary);        /* #00915A */
background: var(--primary-hover);  /* #017F4F */
background: var(--primary-active); /* #016B44 */

/* Secondary Brand Color */
background: var(--secondary);        /* #A12B66 */
background: var(--secondary-hover);  /* #8B245A */

/* SUCCESS (distinct from primary) */
background: var(--success);        /* #198754 - System feedback */
border-color: var(--success-border); /* Validation borders */

/* Danger & Error Handling */
background: var(--danger);         /* #EB3636 */
border-color: var(--danger-border); /* Error feedback */

/* Text Colors */
color: var(--text-primary);    /* #434F57 - Main text (WCAG AAA) */
color: var(--text-secondary);  /* #777E83 - Secondary text (WCAG AA) */

/* Backgrounds & Overlays */
background: var(--bg-section); /* #F9F9FB - Section backgrounds */
background: var(--overlay-dark-heavy); /* Modals, rgba(0,0,0,0.6) */
\`\`\`

### Key Features

✅ **Official BNP Colors**: All palettes from official brand guidelines
✅ **Semantic Meaning**: Each color has clear purpose (primary, success, danger, gold, etc.)
✅ **9 States per Color**: Base, Hover, Active + Text, Border + Subtle variants
✅ **Accessibility**: WCAG 2.2 AA minimum contrast on all interactive elements
✅ **Comprehensive**: 97 tokens cover all UI states and patterns

### Documentation

- **Detailed Reference**: See \`source/props/COLORS_REFERENCE.md\`
- **Palette Scales**: View Base/Colors story
- **Color Specification**: \`source/props/colors.css\` & \`brand.css\`
- **Implementation Guide**: See \`.github/instructions/core.instructions.md\`
        `,
      },
    },
  },
};

const Brand = {
  name: 'Brand colors',
  render: (args) => brand(args),
  args: { ...data },
};

export default settings;
export { Brand };
