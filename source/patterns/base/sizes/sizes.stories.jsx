import sizes from './sizes.twig';
import data from './sizes.yml';

/**
 * Size Tokens (Spacing Scale)
 *
 * Complete spacing system: 33 size tokens from 1px to 24rem
 * Used for padding, margin, gaps, dimensions, etc.
 *
 * All tokens defined in `source/props/sizes.css`
 */

const settings = {
  title: 'Base/Sizes',
  args: { ...data },
  parameters: {
    docs: {
      description: {
        component: `
## Size System (Spacing Scale)

Complete spacing scale with 33 tokens from 1px to 384px.

### Usage

\`\`\`css
/* Spacing */
padding: var(--size-4); /* 16px - base unit */
margin-block-end: var(--size-6); /* 24px */
gap: var(--size-2); /* 8px - tight spacing */

/* Dimensions */
width: var(--size-48); /* 192px */
height: var(--size-32); /* 128px */
max-width: var(--size-max-content-width); /* 1376px */

/* Common patterns */
padding: var(--size-3) var(--size-6); /* 12px 24px */
margin-inline: var(--size-auto); /* Center align */
\`\`\`

### Scale Reference

- **Micro**: --size-px (1px), --size-05 (2px)
- **Tight**: --size-1 to --size-4 (4px to 16px)
- **Normal**: --size-5 to --size-12 (20px to 48px)
- **Loose**: --size-14 to --size-32 (56px to 128px)
- **Extra**: --size-36 to --size-96 (144px to 384px)

### Reference

- **Source**: \`source/props/sizes.css\` (33 tokens)
- **Base unit**: 16px (1rem)
- **Usage**: All spacing, dimensions, gaps
        `,
      },
    },
  },
};

const Sizes = {
  name: 'Sizes',
  render: (args) => sizes(args),
  args: { ...data },
};

export default settings;
export { Sizes };
