import borders from './borders.twig';

/**
 * Border Tokens (Widths & Radius)
 *
 * Complete border system with 13 tokens:
 * - 5 Border widths: 1px to 5px
 * - 8 Border radius: 2px to 1e5px (round)
 *
 * All tokens defined in `source/props/borders.css`
 */

const settings = {
  title: 'Base/Borders',
  parameters: {
    docs: {
      description: {
        component: `
## Border System (Widths & Radius)

Complete border width and radius system.

### Usage

\`\`\`css
/* Border widths */
border-width: var(--border-size-1); /* 1px - default */
border-width: var(--border-size-2); /* 2px - emphasis */
border-width: var(--border-size-5); /* 5px - heavy */

/* Border radius */
border-radius: var(--radius-1); /* 2px - subtle */
border-radius: var(--radius-2); /* 4px - default */
border-radius: var(--radius-4); /* 8px - cards */
border-radius: var(--radius-6); /* 12px - pronounced */
border-radius: var(--radius-round); /* 100000px - fully round */

/* Common patterns */
border: var(--border-size-1) solid var(--border-default);
border-radius: var(--radius-2);
\`\`\`

### Width Levels

- **Level 1**: 1px - Default, subtle
- **Level 2**: 2px - Emphasis, focus states
- **Level 3**: 3px - Strong emphasis
- **Level 4**: 4px - Very strong
- **Level 5**: 5px - Heavy borders

### Radius Levels

- **Level 1-2**: Subtle (2px-4px) - Buttons, inputs
- **Level 3-4**: Medium (6px-8px) - Cards, panels
- **Level 5-7**: Large (10px-16px) - Hero sections
- **Round**: Fully circular - Avatars, badges

### Reference

- **Source**: \`source/props/borders.css\` (13 tokens)
- **Usage**: All bordered elements
- **Colors**: Use \`--border-*\` from \`brand.css\`
        `,
      },
    },
  },
};

const Borders = {
  name: 'Borders',
  render: (args) => borders(args),
};

export default settings;
export { Borders };
