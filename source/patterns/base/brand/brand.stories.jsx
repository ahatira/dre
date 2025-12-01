import brand from './brand.twig';
import data from './brand.yml';

/**
 * BNP Paribas RealEstate Brand System
 *
 * Complete theme system with 52 design tokens organized in 5 categories:
 * - Semantic Colors (30 tokens): Bootstrap Base-Modifier system
 * - Text Colors (4 tokens): Typography hierarchy
 * - Border Colors (5 tokens): UI boundaries
 * - Background Colors (7 tokens): Surface hierarchy
 * - Overlay Colors (6 tokens): Modals and shadows
 *
 * All tokens defined in `source/props/brand.css`
 * Official BNP Paribas RealEstate maquette colors
 */

const settings = {
  title: 'Base/Brand',
  parameters: {
    docs: {
      description: {
        component: `
## BNP Paribas RealEstate Brand System

Complete design token system (52 tokens) aligned with official maquette.

### Usage

\`\`\`css
/* Semantic colors */
color: var(--primary); /* BNP green #00915A */
background: var(--secondary); /* BNP magenta #A12B66 */

/* Text colors */
color: var(--text-primary); /* Main text #434F57 */
color: var(--text-secondary); /* Secondary text #777E83 */

/* Borders */
border-color: var(--border-focus); /* Dark gray #333333 */

/* Backgrounds */
background: var(--bg-section); /* Light gray #F9F9FB */

/* Overlays */
background: var(--overlay-dark-heavy); /* Modal backdrop */
\`\`\`

### Reference

- **Source**: \`source/props/brand.css\` (52 tokens)
- **Official colors**: BNP Paribas RealEstate design system
- **Accessibility**: WCAG AAA compliant text colors
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
