import{t as n,T as s}from"./iframe-DeCmpQ6I.js";import{D as i,a as o}from"./twig-Cbw8xbjJ.js";o(s);s.cache(!1);const t=e=>e,d=(e={})=>{const r=n.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/brand/brand.twig",data:[{type:"raw",value:`<section class="demo-brands">\r
  `,position:{start:0,end:33}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"title",valueVar:"list",expression:[{type:"Twig.expression.type.variable",value:"brands",match:["brands"]}],position:{start:33,end:64},output:[{type:"raw",value:`\r
    <div class="just-for-gap">\r
      <div class="heading">`,position:{start:64,end:125}},{type:"output",position:{start:125,end:136},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:125,end:136}}]},{type:"raw",value:`</div>\r
      <article class="brand-demo">\r
        `,position:{start:136,end:188}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"list",match:["list"]}],position:{start:188,end:210},output:[{type:"raw",value:`\r
          <div class="brand-demo__swatch">\r
            <div class="swatch" style="background-color: `,position:{start:210,end:313}},{type:"output",position:{start:313,end:329},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:313,end:329}},{type:"Twig.expression.type.key.period",position:{start:313,end:329},key:"value"}]},{type:"raw",value:`"></div>\r
            <div class="meta">\r
              <span class="name">`,position:{start:329,end:404}},{type:"output",position:{start:404,end:419},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:404,end:419}},{type:"Twig.expression.type.key.period",position:{start:404,end:419},key:"name"}]},{type:"raw",value:`</span>\r
              <span class="value">`,position:{start:419,end:462}},{type:"output",position:{start:462,end:478},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:462,end:478}},{type:"Twig.expression.type.key.period",position:{start:462,end:478},key:"value"}]},{type:"raw",value:`</span>\r
              <span class="var">`,position:{start:478,end:519}},{type:"output",position:{start:519,end:533},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:519,end:533}},{type:"Twig.expression.type.key.period",position:{start:519,end:533},key:"var"}]},{type:"raw",value:`</span>\r
              `,position:{start:533,end:556}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"description"}],position:{start:556,end:581},output:[{type:"raw",value:`\r
                <span class="description">`,position:{start:581,end:625}},{type:"output",position:{start:625,end:647},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:625,end:647}},{type:"Twig.expression.type.key.period",position:{start:625,end:647},key:"description"}]},{type:"raw",value:`</span>\r
              `,position:{start:647,end:670}}]},position:{open:{start:556,end:581},close:{start:670,end:681}}},{type:"raw",value:`\r
            </div>\r
          </div>\r
        `,position:{start:681,end:729}}]},position:{open:{start:188,end:210},close:{start:729,end:741}}},{type:"raw",value:`\r
      </article>\r
    </div>\r
  `,position:{start:741,end:775}}]},position:{open:{start:33,end:64},close:{start:775,end:787}}},{type:"raw",value:`\r
</section>\r
`,position:{start:787,end:787}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let a=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),t(r.render({attributes:new i(a),...e}))}catch(a){return t("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/brand/brand.twig: "+a.toString())}},c={brands:{"Primary (Brand Green)":[{name:"Base",var:"--primary",value:"#00915A",description:"Official BNP green - Main brand color"},{name:"Hover",var:"--primary-hover",value:"#017F4F",description:"Hover state"},{name:"Active",var:"--primary-active",value:"#047857",description:"Pressed state"},{name:"Text",var:"--primary-text",value:"#FFFFFF",description:"Text on primary backgrounds"},{name:"Border",var:"--primary-border",value:"#00915A",description:"Primary borders"}],"Secondary (Brand Magenta)":[{name:"Base",var:"--secondary",value:"#A12B66",description:"Official BNP magenta"},{name:"Hover",var:"--secondary-hover",value:"#BA3075",description:"Hover state"},{name:"Active",var:"--secondary-active",value:"#8A2456",description:"Pressed state"},{name:"Text",var:"--secondary-text",value:"#FFFFFF",description:"Text on secondary backgrounds"},{name:"Border",var:"--secondary-border",value:"#A12B66",description:"Secondary borders"}],"Success (Positive)":[{name:"Base",var:"--success",value:"#047857",description:"Success actions/states"},{name:"Hover",var:"--success-hover",value:"#065F46",description:"Hover state"},{name:"Active",var:"--success-active",value:"#064E3B",description:"Pressed state"},{name:"Text",var:"--success-text",value:"#FFFFFF",description:"Text on success backgrounds"},{name:"Border",var:"--success-border",value:"#047857",description:"Success borders"}],"Danger (Status Red)":[{name:"Base",var:"--danger",value:"#EB3636",description:"Official status red - Errors/destructive actions"},{name:"Hover",var:"--danger-hover",value:"#DC2626",description:"Hover state"},{name:"Active",var:"--danger-active",value:"#B91C1C",description:"Pressed state"},{name:"Text",var:"--danger-text",value:"#FFFFFF",description:"Text on danger backgrounds"},{name:"Border",var:"--danger-border",value:"#EB3636",description:"Danger borders"}],"Warning (Caution)":[{name:"Base",var:"--warning",value:"#D97706",description:"Warning states"},{name:"Hover",var:"--warning-hover",value:"#B45309",description:"Hover state"},{name:"Active",var:"--warning-active",value:"#92400E",description:"Pressed state"},{name:"Text",var:"--warning-text",value:"#000000",description:"Text on warning backgrounds"},{name:"Border",var:"--warning-border",value:"#D97706",description:"Warning borders"}],"Info (Informational)":[{name:"Base",var:"--info",value:"#2563EB",description:"Informational states"},{name:"Hover",var:"--info-hover",value:"#1D4ED8",description:"Hover state"},{name:"Active",var:"--info-active",value:"#1E40AF",description:"Pressed state"},{name:"Text",var:"--info-text",value:"#FFFFFF",description:"Text on info backgrounds"},{name:"Border",var:"--info-border",value:"#2563EB",description:"Info borders"}],Text:[{name:"Primary",var:"--text-primary",value:"#434F57",description:"Main text color (WCAG AAA)"},{name:"Secondary",var:"--text-secondary",value:"#777E83",description:"Secondary text (WCAG AA)"},{name:"Disabled",var:"--text-disabled",value:"#B0B8BD",description:"Disabled text"},{name:"Inverse",var:"--text-inverse",value:"#FFFFFF",description:"Text on dark backgrounds"}],Borders:[{name:"Default",var:"--border-default",value:"#D6DBDE",description:"Default borders"},{name:"Light",var:"--border-light",value:"#EBEDEF",description:"Light separators"},{name:"Focus",var:"--border-focus",value:"#333333",description:"Focus ring (dark gray from maquette)"},{name:"Error",var:"--border-error",value:"#EB3636",description:"Error state borders"},{name:"Success Done",var:"--border-success",value:"#00915A",description:"Success/done state borders (BNP green)"}],Backgrounds:[{name:"Page",var:"--bg-page",value:"#FFFFFF",description:"Main page background"},{name:"Section",var:"--bg-section",value:"#F9F9FB",description:"Section backgrounds"},{name:"Disabled",var:"--bg-disabled",value:"#F4F5F6",description:"Disabled inputs/buttons"},{name:"Success Light",var:"--bg-success-light",value:"#99D3BD",description:"Light success backgrounds (alerts)"},{name:"Danger Light",var:"--bg-danger-light",value:"#FEE2E2",description:"Light error backgrounds"},{name:"Warning Light",var:"--bg-warning-light",value:"#FEF3C7",description:"Light warning backgrounds"},{name:"Info Light",var:"--bg-info-light",value:"#DBEAFE",description:"Light info backgrounds"}],Overlays:[{name:"Dark Heavy",var:"--overlay-dark-heavy",value:"rgba(0, 0, 0, 0.6)",description:"Modals, heavy overlays"},{name:"Dark Medium",var:"--overlay-dark-medium",value:"rgba(0, 0, 0, 0.36)",description:"Medium overlays"},{name:"Dark Light",var:"--overlay-dark-light",value:"rgba(0, 0, 0, 0.12)",description:"Light overlays"},{name:"Brand Base",var:"--overlay-brand-base",value:"#1C2D37",description:"Brand overlay base color"},{name:"Brand Medium",var:"--overlay-brand-medium",value:"rgba(28, 45, 55, 0.36)",description:"Medium brand overlay"},{name:"Brand Light",var:"--overlay-brand-light",value:"rgba(28, 45, 55, 0.12)",description:"Light brand overlay"}]}},l={title:"Base/Brand",parameters:{docs:{description:{component:`
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
        `}}}},u={name:"Brand colors",render:e=>d(e),args:{...c}},m=["Brand"];export{u as Brand,m as __namedExportsOrder,l as default};
