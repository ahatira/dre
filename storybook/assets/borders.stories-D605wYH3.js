import{t as n,T as d}from"./iframe-CnHaBuCA.js";import{D as o,a as i}from"./twig-Dp8duUs-.js";i(d);d.cache(!1);const a=r=>r,t=(r={})=>{const e=n.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/borders/borders.twig",data:[{type:"raw",value:`<section class="demo-borders">\r
  <div class="just-for-gap">\r
    <div class="heading">Border Size</div>\r
    <article class="border-demo">\r
      <div style="border-width: var(--border-size-1);">1</div>\r
      <div style="border-width: var(--border-size-2);">2</div>\r
      <div style="border-width: var(--border-size-3);">3</div>\r
      <div style="border-width: var(--border-size-4);">4</div>\r
      <div style="border-width: var(--border-size-5);">5</div>\r
    </article>\r
  </div>\r
\r
  <div class="just-for-gap">\r
    <div class="heading">Border Radius</div>\r
    <article class="border-demo">\r
      <div style="border-radius: var(--radius-1);">1</div>\r
      <div style="border-radius: var(--radius-2);">2</div>\r
      <div style="border-radius: var(--radius-3);">3</div>\r
      <div style="border-radius: var(--radius-4);">4</div>\r
      <div style="border-radius: var(--radius-5);">5</div>\r
      <div style="border-radius: var(--radius-6);">6</div>\r
      <div style="border-radius: var(--radius-7);">7</div>\r
      <div style="border-radius: var(--radius-round);">round</div>\r
    </article>\r
  </div>\r
\r
  <div class="just-for-gap">\r
    <div class="heading">Border Colors (from brand.css)</div>\r
    <article class="border-color-demo">\r
      <div class="border-swatch">\r
        <div class="swatch" style="background-color: var(--border-default);"></div>\r
        <div class="meta">\r
          <span class="name">Default</span>\r
          <span class="var">--border-default</span>\r
          <span class="value">#D6DBDE</span>\r
          <span class="usage">Standard borders</span>\r
        </div>\r
      </div>\r
      <div class="border-swatch">\r
        <div class="swatch" style="background-color: var(--border-light);"></div>\r
        <div class="meta">\r
          <span class="name">Light</span>\r
          <span class="var">--border-light</span>\r
          <span class="value">#EBEDEF</span>\r
          <span class="usage">Light separators</span>\r
        </div>\r
      </div>\r
      <div class="border-swatch">\r
        <div class="swatch" style="background-color: var(--border-focus);"></div>\r
        <div class="meta">\r
          <span class="name">Focus</span>\r
          <span class="var">--border-focus</span>\r
          <span class="value">#333333</span>\r
          <span class="usage">Focus rings</span>\r
        </div>\r
      </div>\r
      <div class="border-swatch">\r
        <div class="swatch" style="background-color: var(--border-error);"></div>\r
        <div class="meta">\r
          <span class="name">Error</span>\r
          <span class="var">--border-error</span>\r
          <span class="value">#EB3636</span>\r
          <span class="usage">Error states</span>\r
        </div>\r
      </div>\r
      <div class="border-swatch">\r
        <div class="swatch" style="background-color: var(--border-success);"></div>\r
        <div class="meta">\r
          <span class="name">Success</span>\r
          <span class="var">--border-success</span>\r
          <span class="value">#00915A</span>\r
          <span class="usage">Success/done</span>\r
        </div>\r
      </div>\r
    </article>\r
  </div>\r
</section>\r
\r
<style>\r
/* Border color swatches */\r
.border-color-demo {\r
  display: grid;\r
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));\r
  gap: var(--size-4);\r
}\r
\r
.border-swatch {\r
  display: flex;\r
  gap: var(--size-3);\r
  padding: var(--size-3);\r
  background: var(--bg-section);\r
  border-radius: var(--radius-2);\r
}\r
\r
.border-swatch .swatch {\r
  width: 48px;\r
  height: 48px;\r
  border-radius: var(--radius-2);\r
  border: 1px solid var(--border-light);\r
  flex-shrink: 0;\r
}\r
\r
.border-swatch .meta {\r
  display: flex;\r
  flex-direction: column;\r
  gap: var(--size-05);\r
  font-size: var(--font-size-0);\r
  min-width: 0;\r
}\r
\r
.border-swatch .name {\r
  font-weight: var(--font-weight-600);\r
  color: var(--text-primary);\r
}\r
\r
.border-swatch .var {\r
  font-family: var(--font-mono);\r
  font-size: var(--font-size--1);\r
  color: var(--primary);\r
  word-break: break-all;\r
}\r
\r
.border-swatch .value {\r
  font-family: var(--font-mono);\r
  font-size: var(--font-size--1);\r
  color: var(--text-secondary);\r
}\r
\r
.border-swatch .usage {\r
  font-size: var(--font-size--1);\r
  color: var(--text-secondary);\r
  font-style: italic;\r
}\r
</style>\r
`,position:{start:0,end:0}}],precompiled:!0});e.options.allowInlineIncludes=!0;try{let s=r.defaultAttributes?r.defaultAttributes:[];return Array.isArray(s)||(s=Object.entries(s)),a(e.render({attributes:new o(s),...r}))}catch(s){return a("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/borders/borders.twig: "+s.toString())}},v={title:"Base/Borders",parameters:{docs:{description:{component:`
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
        `}}}},u={name:"Borders",render:r=>t(r)},p=["Borders"];export{u as Borders,p as __namedExportsOrder,v as default};
