import aspects from './aspects.twig';

/**
 * Aspect Ratio Tokens
 *
 * 7 common aspect ratios for images and media:
 * - box (1:1), photo (4:3), portrait (3:4)
 * - landscape (3:2), widescreen (16:9), cinemascope (21:9), golden (1.618:1)
 *
 * All tokens defined in `source/props/aspects.css`
 */

const settings = {
  title: 'Base/Aspect ratios',
  parameters: {
    docs: {
      description: {
        component: `
## Aspect Ratio System

Common aspect ratios for images, videos, and containers.

### Usage

\`\`\`css
/* Images */
aspect-ratio: var(--ratio-box); /* 1:1 - Square */
aspect-ratio: var(--ratio-photo); /* 4:3 - Classic photo */
aspect-ratio: var(--ratio-portrait); /* 3:4 - Vertical */

/* Video */
aspect-ratio: var(--ratio-widescreen); /* 16:9 - HD video */
aspect-ratio: var(--ratio-cinemascope); /* 21:9 - Ultra-wide */

/* Special */
aspect-ratio: var(--ratio-landscape); /* 3:2 - Photography */
aspect-ratio: var(--ratio-golden); /* 1.618:1 - Golden ratio */
\`\`\`

### Ratio Reference

- **box**: 1 / 1 - Square avatars, thumbnails
- **photo**: 4 / 3 - Classic photography, presentations
- **portrait**: 3 / 4 - Vertical images, mobile
- **landscape**: 3 / 2 - SLR cameras, print
- **widescreen**: 16 / 9 - HD video, monitors
- **cinemascope**: 21 / 9 - Ultra-wide displays
- **golden**: 1.618 / 1 - Aesthetic compositions

### Reference

- **Source**: \`source/props/aspects.css\` (7 tokens)
- **Usage**: Images, videos, containers
- **Browser support**: Modern browsers (IE11 fallback needed)
        `,
      },
    },
  },
};

export const Aspects = {
  name: 'Aspect ratios',
  render: (args) => aspects(args),
};

export default settings;
