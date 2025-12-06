import { t as d, T as r } from './iframe-D21U4yYN.js';
import { D as n, a as t } from './twig-BPJOkNgt.js';
t(r);
r.cache(!1);
const e = (s) => s,
  w = (s = {}) => {
    const a = d.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/base/shadows/shadows.twig',
      data: [
        {
          type: 'raw',
          value: `<section class="demo-shadows">\r
  <div class="just-for-gap">\r
    <div class="heading">Box Shadow</div>\r
    <article class="shadow-demo">\r
      <div style="box-shadow: var(--shadow-1);">Shadow 1</div>\r
      <div style="box-shadow: var(--shadow-2);">Shadow 2</div>\r
      <div style="box-shadow: var(--shadow-3);">Shadow 3</div>\r
      <div style="box-shadow: var(--shadow-4);">Shadow 4</div>\r
      <div style="box-shadow: var(--shadow-5);">Shadow 5</div>\r
      <div style="box-shadow: var(--shadow-6);">Shadow 6</div>\r
    </article>\r
  </div>\r
\r
  <div class="just-for-gap">\r
    <h3>Inner Shadow</h3>\r
    <article class="shadow-demo">\r
      <div style="box-shadow: var(--inner-shadow-0);">Inner Shadow 1</div>\r
      <div style="box-shadow: var(--inner-shadow-1);">Inner Shadow 2</div>\r
      <div style="box-shadow: var(--inner-shadow-2);">Inner Shadow 3</div>\r
      <div style="box-shadow: var(--inner-shadow-3);">Inner Shadow 4</div>\r
      <div style="box-shadow: var(--inner-shadow-4);">Inner Shadow 5</div>\r
    </article>\r
  </div>\r
</section>\r
`,
          position: { start: 0, end: 0 },
        },
      ],
      precompiled: !0,
    });
    a.options.allowInlineIncludes = !0;
    try {
      let o = s.defaultAttributes ? s.defaultAttributes : [];
      return (
        Array.isArray(o) || (o = Object.entries(o)), e(a.render({ attributes: new n(o), ...s }))
      );
    } catch (o) {
      return e(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/shadows/shadows.twig: ' +
          o.toString()
      );
    }
  },
  l = {
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
  },
  v = { name: 'Shadows', render: (s) => w(s) },
  c = ['Shadows'];
export { v as Shadows, c as __namedExportsOrder, l as default };
