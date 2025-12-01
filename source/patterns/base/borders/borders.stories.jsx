import borders from './borders.twig';

/**
 * Border Tokens (Widths, Radius & Colors)
 *
 * Complete border system with 19 tokens:
 * - 6 Border widths: 1px to 5px (+ 1.5px)
 * - 8 Border radius: 2px to round (1e5px)
 * - 5 Border colors: default, light, focus, error, success (from brand.css)
 *
 * Tokens defined in:
 * - `source/props/borders.css` (widths & radii)
 * - `source/props/brand.css` (semantic colors)
 */

const settings = {
  title: 'Base/Borders',
  parameters: {
    docs: {
      description: {
        component: `
## Border System (Widths, Radius & Colors)

Complete border system with 19 tokens across widths, radii, and semantic colors.

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

/* Border colors (semantic) */
border-color: var(--border-default); /* #D6DBDE - standard borders */
border-color: var(--border-light);   /* #EBEDEF - light separators */
border-color: var(--border-focus);   /* #333333 - focus rings */
border-color: var(--border-error);   /* #EB3636 - error states */
border-color: var(--border-success); /* #00915A - success states */

/* Common patterns */
.card {
  border: var(--border-size-1) solid var(--border-default);
  border-radius: var(--radius-3);
}

.input:focus {
  border-color: var(--border-focus);
  border-width: var(--border-size-2);
}

.input--error {
  border-color: var(--border-error);
}
\`\`\`

### Width Levels

- **Level 1**: 1px - Default, subtle borders
- **Level 2**: 2px - Emphasis, focus states
- **Level 3**: 3px - Strong emphasis
- **Level 4**: 4px - Very strong borders
- **Level 5**: 5px - Heavy, decorative borders

### Radius Levels

- **Level 1-2**: Subtle (2px-4px) - Buttons, inputs
- **Level 3-4**: Medium (6px-8px) - Cards, panels
- **Level 5-7**: Large (10px-16px) - Hero sections
- **Round**: Fully circular - Avatars, badges, pills

### Color Semantics

- **Default** (#D6DBDE): Standard borders for cards, inputs, table cells
- **Light** (#EBEDEF): Subtle separators, light dividers
- **Focus** (#333333): Focus rings on interactive elements (dark gray, not green)
- **Error** (#EB3636): Invalid inputs, destructive actions
- **Success** (#00915A): Valid inputs, completed actions (BNP primary green)

### Reference

- **Widths & Radii**: \`source/props/borders.css\` (14 tokens)
- **Colors**: \`source/props/brand.css\` (5 semantic tokens)
- **Total**: 19 border tokens
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
