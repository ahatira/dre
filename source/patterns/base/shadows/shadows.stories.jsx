import shadows from './shadows.twig';

/**
 * Shadow Tokens (Elevation System)
 *
 * Complete elevation system with 16 shadow tokens:
 * - 6 Outer shadows: elevation levels 1-6
 * - 5 Inner shadows: inset depth levels
 * - 3 Focus rings: default, error, success states
 * - 2 Custom: glow effects
 *
 * All tokens defined in `source/props/shadows.css`
 */

const settings = {
  title: 'Base/Shadows',
  parameters: {
    docs: {
      description: {
        component: `
## Shadow System (Elevation & Focus)

Complete elevation and focus ring system.

### Usage

\`\`\`css
/* Elevation levels */
box-shadow: var(--shadow-1); /* Subtle - cards */
box-shadow: var(--shadow-3); /* Medium - dropdowns */
box-shadow: var(--shadow-6); /* Heavy - modals */

/* Focus rings (uses brand tokens) */
box-shadow: var(--shadow-focus); /* Default dark gray */
box-shadow: var(--shadow-focus-error); /* Red for errors */
box-shadow: var(--shadow-focus-success); /* Green for success */

/* Inner shadows */
box-shadow: var(--inner-shadow-1); /* Subtle inset */
box-shadow: var(--inner-shadow-4); /* Deep inset */

/* Glow effects */
box-shadow: var(--shadow-glow); /* Subtle glow */
\`\`\`

### Shadow Levels

- **Level 1-2**: Subtle elevation (cards, buttons)
- **Level 3-4**: Medium elevation (dropdowns, popovers)
- **Level 5-6**: Heavy elevation (modals, drawers)

### Focus Rings

- Uses \`color-mix()\` with brand tokens for consistency
- Aligns with \`--border-focus\`, \`--border-error\`, \`--border-success\`
- WCAG compliant contrast ratios

### Reference

- **Source**: \`source/props/shadows.css\` (16 tokens)
- **Focus integration**: \`source/props/brand.css\` (border tokens)
- **Usage**: Elevation, focus states, depth
        `,
      },
    },
  },
};

const Shadows = {
  name: 'Shadows',
  render: (args) => shadows(args),
};

export default settings;
export { Shadows };
