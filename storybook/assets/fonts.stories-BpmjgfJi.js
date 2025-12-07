import{t as o,T as a}from"./iframe-GGfdoSBx.js";import{D as n,a as r}from"./twig-Dqrk-56N.js";r(a);a.cache(!1);const s=e=>e,p=(e={})=>{const i=o.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/fonts/fonts.twig",data:[{type:"raw",value:`<section class="demo-fonts">\r
  `,position:{start:0,end:32}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"title",valueVar:"list",expression:[{type:"Twig.expression.type.variable",value:"fonts",match:["fonts"]}],position:{start:32,end:62},output:[{type:"raw",value:`\r
    <div class="just-for-gap">\r
      <div class="heading">`,position:{start:62,end:123}},{type:"output",position:{start:123,end:145},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:123,end:145}},{type:"Twig.expression.type.filter",value:"capitalize",match:["|capitalize","capitalize"],position:{start:123,end:145}}]},{type:"raw",value:`</div>\r
      `,position:{start:145,end:159}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"list",match:["list"]}],position:{start:159,end:181},output:[{type:"raw",value:`\r
        `,position:{start:181,end:191}},{type:"logic",token:{type:"Twig.logic.type.set",key:"font_family",expression:[{type:"Twig.expression.type.string",value:"font-family: "},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"family"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.string",value:"; "},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"}],position:{start:191,end:251}},position:{start:191,end:251}},{type:"raw",value:`\r
        `,position:{start:251,end:261}},{type:"logic",token:{type:"Twig.logic.type.set",key:"font_style",expression:[{type:"Twig.expression.type.string",value:"font-style: "},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"style"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.string",value:"; "},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"}],position:{start:261,end:318}},position:{start:261,end:318}},{type:"raw",value:`\r
        `,position:{start:318,end:328}},{type:"logic",token:{type:"Twig.logic.type.set",key:"font_weight",expression:[{type:"Twig.expression.type.string",value:"font-weight: "},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"weight"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.string",value:";"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"}],position:{start:328,end:387}},position:{start:328,end:387}},{type:"raw",value:`\r
        `,position:{start:387,end:397}},{type:"logic",token:{type:"Twig.logic.type.set",key:"preview_style",expression:[{type:"Twig.expression.type.variable",value:"font_family",match:["font_family"]},{type:"Twig.expression.type.variable",value:"font_style",match:["font_style"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.variable",value:"font_weight",match:["font_weight"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"}],position:{start:397,end:461}},position:{start:397,end:461}},{type:"raw",value:`\r
\r
        <article class="font-demo">\r
          <div class="font-preview" style="`,position:{start:461,end:545}},{type:"output",position:{start:545,end:564},stack:[{type:"Twig.expression.type.variable",value:"preview_style",match:["preview_style"],position:{start:545,end:564}}]},{type:"raw",value:`">AaBbCc</div>\r
          <div class="font-meta">\r
            <div class="font-meta__alpha" style="`,position:{start:564,end:664}},{type:"output",position:{start:664,end:683},stack:[{type:"Twig.expression.type.variable",value:"preview_style",match:["preview_style"],position:{start:664,end:683}}]},{type:"raw",value:`">ABCDEFGHIJKLMNOPQRSTUVWXYZ<br/>abcdefghijklmnopqrstuvwxyz<br/>1234567890(,.;:?!$&*)</div>\r
            <div class="font-meta__preview">\r
              <div><span class="font-meta__label">Family:</span> `,position:{start:683,end:887}},{type:"output",position:{start:887,end:904},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:887,end:904}},{type:"Twig.expression.type.key.period",position:{start:887,end:904},key:"family"}]},{type:"raw",value:`</div>\r
              <div><span class="font-meta__label">Style:</span> `,position:{start:904,end:976}},{type:"output",position:{start:976,end:1003},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:976,end:1003}},{type:"Twig.expression.type.key.period",position:{start:976,end:1003},key:"style"},{type:"Twig.expression.type.filter",value:"capitalize",match:["|capitalize","capitalize"],position:{start:976,end:1003}}]},{type:"raw",value:`</div>\r
              <div><span class="font-meta__label">Weight:</span> `,position:{start:1003,end:1076}},{type:"output",position:{start:1076,end:1093},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1076,end:1093}},{type:"Twig.expression.type.key.period",position:{start:1076,end:1093},key:"weight"}]},{type:"raw",value:`</div>\r
            </div>\r
          </div>\r
        </article>\r
      `,position:{start:1093,end:1165}}]},position:{open:{start:159,end:181},close:{start:1165,end:1177}}},{type:"raw",value:`\r
    </div>\r
  `,position:{start:1177,end:1193}}]},position:{open:{start:32,end:62},close:{start:1193,end:1205}}},{type:"raw",value:`\r
</section>\r
`,position:{start:1205,end:1205}}],precompiled:!0});i.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),s(i.render({attributes:new n(t),...e}))}catch(t){return s("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/fonts/fonts.twig: "+t.toString())}},l={fonts:{"BNPP Sans":[{family:"'BNPP Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"300"},{family:"'BNPP Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"400"},{family:"'BNPP Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"700"},{family:"'BNPP Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"800"}],"BNPP Sans Condensed":[{family:"'BNPP Sans Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"300"}],"Open Sans":[{family:"'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"400"},{family:"'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif",style:"normal",weight:"700"}],system:[{family:"-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Oxygen-Sans', Ubuntu, Cantarell, 'Fira Sans', Droid Sans, sans-serif",style:"normal",weight:"400"}],mono:[{family:"Menlo, Consolas, 'Lucida Console', 'Liberation Mono', 'Courier New', monospace, sans-serif",style:"normal",weight:"400"}]}},g={title:"Base/Fonts",parameters:{docs:{description:{component:`
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
        `}}}},d={name:"Fonts",render:e=>p(e),args:{...l}},f=["Fonts"];export{d as Fonts,f as __namedExportsOrder,g as default};
