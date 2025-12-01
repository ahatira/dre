import fonts from './fonts.twig';
import data from './fonts.yml';

/**
 * Font System
 *
 * Complete typography system with 3 font families and 15-level scale:
 * - BNPP Sans: 4 weights (300, 400, 700, 800) - Primary
 * - BNPP Sans Condensed: 1 weight (300) - Headings
 * - Open Sans: 2 weights (400, 700) - Fallback
 * - Font scale: --font-size--2 to --font-size-12 (10px to 96px)
 *
 * All tokens defined in `source/props/fonts.css` and `source/props/font-face.css`
 */

const settings = {
  title: 'Base/Fonts',
  parameters: {
    docs: {
      description: {
        component: `
## Font System (Typography)

Complete typography system with BNP Paribas RealEstate fonts.

### Usage

\`\`\`css
/* Font families */
font-family: var(--font-body); /* BNPP Sans - body text */
font-family: var(--font-heading); /* BNPP Sans Condensed - headings */
font-family: var(--font-mono); /* Monospace - code */

/* Font sizes (15-level scale) */
font-size: var(--font-size--2); /* 10px - captions */
font-size: var(--font-size-0); /* 14px - small text */
font-size: var(--font-size-2); /* 18px - base */
font-size: var(--font-size-5); /* 24px - h4 */
font-size: var(--font-size-12); /* 96px - hero */

/* Font weights */
font-weight: var(--font-weight-300); /* Light */
font-weight: var(--font-weight-400); /* Regular */
font-weight: var(--font-weight-700); /* Bold */
font-weight: var(--font-weight-800); /* Extra bold */

/* Line heights */
line-height: var(--leading-none); /* 1 - tight */
line-height: var(--leading-normal); /* 1.5 - default */
line-height: var(--leading-relaxed); /* 1.75 - comfortable */
\`\`\`

### Font Families

- **BNPP Sans**: Official BNP brand font (4 weights)
- **BNPP Sans Condensed**: Headings and emphasis
- **Open Sans**: Fallback for extended glyphs
- **System fonts**: Fallback stack

### Font Scale (15 levels)

- **--font-size--2 to -1**: 10px-12px - Micro text
- **--font-size-0 to 2**: 14px-18px - Body text
- **--font-size-3 to 6**: 20px-28px - Subheadings
- **--font-size-7 to 9**: 32px-48px - Headings
- **--font-size-10 to 12**: 56px-96px - Display

### Reference

- **Families**: \`source/props/font-face.css\` (@font-face rules)
- **Scale**: \`source/props/fonts.css\` (size, weight, leading)
- **Usage**: All typography, headings, body text
        `,
      },
    },
  },
};

const Fonts = {
  name: 'Fonts',
  render: (args) => fonts(args),
  args: { ...data },
};

export default settings;
export { Fonts };
