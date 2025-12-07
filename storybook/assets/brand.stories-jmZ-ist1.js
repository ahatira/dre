import{t as s,T as i}from"./iframe-GGfdoSBx.js";import{D as o,a as n}from"./twig-Dqrk-56N.js";n(i);i.cache(!1);const a=e=>e,p=(e={})=>{const r=s.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/brand/brand.twig",data:[{type:"raw",value:`<section class="demo-brands">\r
  `,position:{start:0,end:33}},{type:"raw",value:`\r
  <div class="theme-colors-section">\r
    <h3 class="section-title">Theme colors</h3>\r
    <p class="section-intro">Official BNP Paribas Real Estate semantic color palette for UI components and states.</p>\r
    <div class="theme-colors-grid">\r
      `,position:{start:73,end:325}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"title",valueVar:"list",expression:[{type:"Twig.expression.type.variable",value:"brands",match:["brands"]}],position:{start:325,end:356},output:[{type:"raw",value:`\r
        `,position:{start:356,end:366}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"list",match:["list"]}],position:{start:366,end:388},output:[{type:"raw",value:`\r
          <div class="theme-color-card theme-color-`,position:{start:388,end:441}},{type:"output",position:{start:441,end:456},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:441,end:456}},{type:"Twig.expression.type.key.period",position:{start:441,end:456},key:"slug"}]},{type:"raw",value:`">\r
            <span class="theme-color-name">`,position:{start:456,end:503}},{type:"output",position:{start:503,end:518},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:503,end:518}},{type:"Twig.expression.type.key.period",position:{start:503,end:518},key:"name"}]},{type:"raw",value:`</span>\r
          </div>\r
        `,position:{start:518,end:553}}]},position:{open:{start:366,end:388},close:{start:553,end:565}}},{type:"raw",value:`\r
      `,position:{start:565,end:573}}]},position:{open:{start:325,end:356},close:{start:573,end:585}}},{type:"raw",value:`\r
    </div>\r
  </div>\r
\r
  `,position:{start:585,end:613}},{type:"raw",value:`\r
  <div class="brand-grid">\r
    <h3 class="section-title">All colors and variants</h3>\r
    <p class="section-intro">Complete semantic token system with base, subtle, border, and text-emphasis variants for each theme color.</p>\r
    `,position:{start:656,end:891}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"title",valueVar:"list",expression:[{type:"Twig.expression.type.variable",value:"brands",match:["brands"]}],position:{start:891,end:922},output:[{type:"raw",value:`\r
      <div class="brand-group">\r
        <h3 class="brand-group__title">`,position:{start:922,end:996}},{type:"output",position:{start:996,end:1007},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:996,end:1007}}]},{type:"raw",value:`</h3>\r
        <table class="brand-table">\r
          <thead>\r
            <tr>\r
              <th style="width: 50%;">Description</th>\r
              <th style="width: 200px;" class="ps-0">Swatch</th>\r
              <th>Variables</th>\r
            </tr>\r
          </thead>\r
          <tbody>\r
            `,position:{start:1007,end:1314}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"list",match:["list"]}],position:{start:1314,end:1336},output:[{type:"raw",value:`\r
              `,position:{start:1336,end:1352}},{type:"raw",value:`\r
              <tr>\r
                <td rowspan="`,position:{start:1415,end:1466}},{type:"output",position:{start:1466,end:1492},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1466,end:1492}},{type:"Twig.expression.type.key.period",position:{start:1466,end:1492},key:"variants"},{type:"Twig.expression.type.filter",value:"length",match:["|length","length"],position:{start:1466,end:1492}}]},{type:"raw",value:`">\r
                  <p><strong>`,position:{start:1492,end:1525}},{type:"output",position:{start:1525,end:1540},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1525,end:1540}},{type:"Twig.expression.type.key.period",position:{start:1525,end:1540},key:"name"}]},{type:"raw",value:" —</strong> ",position:{start:1540,end:1552}},{type:"output",position:{start:1552,end:1574},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1552,end:1574}},{type:"Twig.expression.type.key.period",position:{start:1552,end:1574},key:"description"}]},{type:"raw",value:`</p>\r
                </td>\r
                `,position:{start:1574,end:1619}},{type:"raw",value:`\r
                `,position:{start:1638,end:1656}},{type:"logic",token:{type:"Twig.logic.type.set",key:"first_variant",expression:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"variants"},{type:"Twig.expression.type.key.brackets",stack:[{type:"Twig.expression.type.number",value:0,match:["0",null]}]}],position:{start:1656,end:1698}},position:{start:1656,end:1698}},{type:"raw",value:`\r
                <td class="ps-0">\r
                  <div class="p-3 rounded-2`,position:{start:1698,end:1778}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"base"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1778,end:1815},output:[{type:"raw",value:" bg-",position:{start:1815,end:1819}},{type:"output",position:{start:1819,end:1834},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1819,end:1834}},{type:"Twig.expression.type.key.period",position:{start:1819,end:1834},key:"slug"}]}]},position:{open:{start:1778,end:1815},close:{start:1834,end:1845}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1845,end:1884},output:[{type:"raw",value:" border",position:{start:1884,end:1891}}]},position:{open:{start:1845,end:1884},close:{start:1891,end:1902}}},{type:"raw",value:`" \r
                    `,position:{start:1902,end:1926}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"subtle"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1926,end:1965},output:[{type:"raw",value:'style="background-color: ',position:{start:1965,end:1990}},{type:"output",position:{start:1990,end:2015},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:1990,end:2015}},{type:"Twig.expression.type.key.period",position:{start:1990,end:2015},key:"value"}]},{type:"raw",value:'"',position:{start:2015,end:2016}}]},position:{open:{start:1926,end:1965},close:{start:2016,end:2027}}},{type:"raw",value:`\r
                    `,position:{start:2027,end:2049}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2049,end:2088},output:[{type:"raw",value:'style="border: 5px ',position:{start:2088,end:2107}},{type:"output",position:{start:2107,end:2132},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2107,end:2132}},{type:"Twig.expression.type.key.period",position:{start:2107,end:2132},key:"value"}]},{type:"raw",value:' solid"',position:{start:2132,end:2139}}]},position:{open:{start:2049,end:2088},close:{start:2139,end:2150}}},{type:"raw",value:`\r
                    `,position:{start:2150,end:2172}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2172,end:2209},output:[{type:"raw",value:'style="color: ',position:{start:2209,end:2223}},{type:"output",position:{start:2223,end:2248},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2223,end:2248}},{type:"Twig.expression.type.key.period",position:{start:2223,end:2248},key:"value"}]},{type:"raw",value:'"',position:{start:2248,end:2249}}]},position:{open:{start:2172,end:2209},close:{start:2249,end:2260}}},{type:"raw",value:`>\r
                    `,position:{start:2260,end:2283}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2283,end:2320},output:[{type:"raw",value:'<span class="fw-bold">Text</span>',position:{start:2320,end:2353}}]},position:{open:{start:2283,end:2320},close:{start:2353,end:2363}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2353,end:2363},output:[{type:"raw",value:"&nbsp;",position:{start:2363,end:2369}}]},position:{open:{start:2353,end:2363},close:{start:2369,end:2380}}},{type:"raw",value:`\r
                  </div>\r
                </td>\r
                <td>\r
                  <p><code>`,position:{start:2380,end:2480}},{type:"output",position:{start:2480,end:2503},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2480,end:2503}},{type:"Twig.expression.type.key.period",position:{start:2480,end:2503},key:"var"}]},{type:"raw",value:`</code></p>\r
                </td>\r
              </tr>\r
              \r
              `,position:{start:2503,end:2590}},{type:"raw",value:`\r
              `,position:{start:2623,end:2639}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"variant",expression:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"variants"},{type:"Twig.expression.type.filter",value:"slice",match:["|slice","slice"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.number",value:1,match:["1",null]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2639,end:2682},output:[{type:"raw",value:`\r
                <tr>\r
                  <td class="ps-0">\r
                    <div class="p-3 rounded-2`,position:{start:2682,end:2788}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2788,end:2821},output:[{type:"raw",value:" border",position:{start:2821,end:2828}}]},position:{open:{start:2788,end:2821},close:{start:2828,end:2839}}},{type:"raw",value:`" \r
                      `,position:{start:2839,end:2865}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"subtle"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2865,end:2898},output:[{type:"raw",value:'style="background-color: ',position:{start:2898,end:2923}},{type:"output",position:{start:2923,end:2942},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:2923,end:2942}},{type:"Twig.expression.type.key.period",position:{start:2923,end:2942},key:"value"}]},{type:"raw",value:'"',position:{start:2942,end:2943}}]},position:{open:{start:2865,end:2898},close:{start:2943,end:2954}}},{type:"raw",value:`\r
                      `,position:{start:2954,end:2978}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2978,end:3011},output:[{type:"raw",value:'style="border: 5px ',position:{start:3011,end:3030}},{type:"output",position:{start:3030,end:3049},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3030,end:3049}},{type:"Twig.expression.type.key.period",position:{start:3030,end:3049},key:"value"}]},{type:"raw",value:' solid"',position:{start:3049,end:3056}}]},position:{open:{start:2978,end:3011},close:{start:3056,end:3067}}},{type:"raw",value:`\r
                      `,position:{start:3067,end:3091}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3091,end:3122},output:[{type:"raw",value:'style="color: ',position:{start:3122,end:3136}},{type:"output",position:{start:3136,end:3155},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3136,end:3155}},{type:"Twig.expression.type.key.period",position:{start:3136,end:3155},key:"value"}]},{type:"raw",value:'"',position:{start:3155,end:3156}}]},position:{open:{start:3091,end:3122},close:{start:3156,end:3167}}},{type:"raw",value:`>\r
                      `,position:{start:3167,end:3192}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3192,end:3223},output:[{type:"raw",value:'<span class="fw-bold">Text</span>',position:{start:3223,end:3256}}]},position:{open:{start:3192,end:3223},close:{start:3256,end:3266}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:3256,end:3266},output:[{type:"raw",value:"&nbsp;",position:{start:3266,end:3272}}]},position:{open:{start:3256,end:3266},close:{start:3272,end:3283}}},{type:"raw",value:`\r
                    </div>\r
                  </td>\r
                  <td>\r
                    <p><code>`,position:{start:3283,end:3391}},{type:"output",position:{start:3391,end:3408},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3391,end:3408}},{type:"Twig.expression.type.key.period",position:{start:3391,end:3408},key:"var"}]},{type:"raw",value:`</code></p>\r
                  </td>\r
                </tr>\r
              `,position:{start:3408,end:3483}}]},position:{open:{start:2639,end:2682},close:{start:3483,end:3495}}},{type:"raw",value:`\r
            `,position:{start:3495,end:3509}}]},position:{open:{start:1314,end:1336},close:{start:3509,end:3521}}},{type:"raw",value:`\r
          </tbody>\r
        </table>\r
      </div>\r
    `,position:{start:3521,end:3579}}]},position:{open:{start:891,end:922},close:{start:3579,end:3591}}},{type:"raw",value:`\r
  </div>\r
</section>\r
\r
`,position:{start:3591,end:3591}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),a(r.render({attributes:new o(t),...e}))}catch(t){return a("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/brand/brand.twig: "+t.toString())}},l={brands:{"Semantic Colors":[{name:"Primary",slug:"primary",description:"Main theme color, used for hyperlinks, focus styles, and component and form active states. Official BNP green #00915A.",variants:[{type:"base",var:"--primary",value:"#00915A"},{type:"subtle",var:"--primary-bg-subtle",value:"rgba(0, 145, 90, 0.1)"},{type:"border",var:"--primary-border-subtle",value:"rgba(0, 145, 90, 0.3)"},{type:"text",var:"--primary-text-emphasis",value:"#016B44"}]},{name:"Secondary",slug:"secondary",description:"Secondary brand color for alternative actions and accents. BNP pink #A12B66.",variants:[{type:"base",var:"--secondary",value:"#A12B66"},{type:"subtle",var:"--secondary-bg-subtle",value:"rgba(161, 43, 102, 0.1)"},{type:"border",var:"--secondary-border-subtle",value:"rgba(161, 43, 102, 0.3)"},{type:"text",var:"--secondary-text-emphasis",value:"#7A2050"}]},{name:"Success",slug:"success",description:"Theme color used for positive or successful actions and information. Distinct from Primary - uses teal #198754.",variants:[{type:"base",var:"--success",value:"#198754"},{type:"subtle",var:"--success-bg-subtle",value:"rgba(25, 135, 84, 0.1)"},{type:"border",var:"--success-border-subtle",value:"rgba(25, 135, 84, 0.3)"},{type:"text",var:"--success-text-emphasis",value:"#0F5132"}]},{name:"Danger",slug:"danger",description:"Theme color used for errors and dangerous actions. Red #EB3636.",variants:[{type:"base",var:"--danger",value:"#EB3636"},{type:"subtle",var:"--danger-bg-subtle",value:"rgba(235, 54, 54, 0.1)"},{type:"border",var:"--danger-border-subtle",value:"rgba(235, 54, 54, 0.3)"},{type:"text",var:"--danger-text-emphasis",value:"#A02626"}]},{name:"Warning",slug:"warning",description:"Theme color used for non-destructive warning messages. Yellow #FFC107.",variants:[{type:"base",var:"--warning",value:"#FFC107"},{type:"subtle",var:"--warning-bg-subtle",value:"rgba(255, 193, 7, 0.1)"},{type:"border",var:"--warning-border-subtle",value:"rgba(255, 193, 7, 0.3)"},{type:"text",var:"--warning-text-emphasis",value:"#997404"}]},{name:"Info",slug:"info",description:"Theme color used for neutral and informative content. Blue #0DCAF0.",variants:[{type:"base",var:"--info",value:"#0DCAF0"},{type:"subtle",var:"--info-bg-subtle",value:"rgba(13, 202, 240, 0.1)"},{type:"border",var:"--info-border-subtle",value:"rgba(13, 202, 240, 0.3)"},{type:"text",var:"--info-text-emphasis",value:"#087990"}]},{name:"Light",slug:"light",description:"Additional theme option for less contrasting colors. Light gray #F8F9FA.",variants:[{type:"base",var:"--light",value:"#F8F9FA"},{type:"subtle",var:"--light-bg-subtle",value:"rgba(248, 249, 250, 0.5)"},{type:"border",var:"--light-border-subtle",value:"rgba(0, 0, 0, 0.1)"},{type:"text",var:"--light-text-emphasis",value:"#495057"}]},{name:"Dark",slug:"dark",description:"Additional theme option for higher contrasting colors. Dark gray #212529.",variants:[{type:"base",var:"--dark",value:"#212529"},{type:"subtle",var:"--dark-bg-subtle",value:"rgba(33, 37, 41, 0.1)"},{type:"border",var:"--dark-border-subtle",value:"rgba(33, 37, 41, 0.3)"},{type:"text",var:"--dark-text-emphasis",value:"#000000"}]}]}},c={title:"Base/Brand",parameters:{docs:{description:{component:`
## BNP Paribas RealEstate Brand System - Semantic Color Tokens

Complete design token system (88 tokens) based on official BNP Paribas RealEstate color palette.

### 3-Layer Architecture

\`\`\`
colors.css (Official BNP Palettes)
    ↓
brand.css (Semantic Token Mapping)
    ↓
Components (var() References)
\`\`\`

**Layer 1: Official Palettes** (source/props/colors.css)
- PRIMARY GREEN: #00915A (bnp official)
- SECONDARY PINK: #A12B66 (bnp official)
- SUCCESS TEAL: #198754 (distinct from primary!)
- ERROR RED: #EB3636 (bnp official)
- WARNING YELLOW, INFO BLUE, SKY, GREY

**Layer 2: Semantic Tokens** (source/props/brand.css)
- 8 semantic colors with 9 states each
- Each color: base, hover, active, text, border, subtle, bg-subtle, border-subtle, text-emphasis

### Usage Examples

\`\`\`css
/* Primary Brand Color */
background: var(--primary);        /* #00915A */
background: var(--primary-hover);  /* #017F4F */
background: var(--primary-active); /* #016B44 */

/* Secondary Brand Color */
background: var(--secondary);        /* #A12B66 */
background: var(--secondary-hover);  /* #8B245A */

/* SUCCESS (distinct from primary) */
background: var(--success);        /* #198754 - System feedback */
border-color: var(--success-border); /* Validation borders */

/* Danger & Error Handling */
background: var(--danger);         /* #EB3636 */
border-color: var(--danger-border); /* Error feedback */

/* Text Colors */
color: var(--text-primary);    /* #434F57 - Main text (WCAG AAA) */
color: var(--text-secondary);  /* #777E83 - Secondary text (WCAG AA) */

/* Backgrounds & Overlays */
background: var(--bg-section); /* #F9F9FB - Section backgrounds */
background: var(--overlay-dark-heavy); /* Modals, rgba(0,0,0,0.6) */
\`\`\`

### Key Features

✅ **Official BNP Colors**: All palettes from official brand guidelines
✅ **Semantic Meaning**: Each color has clear purpose (primary, success, danger, etc.)
✅ **9 States per Color**: Base, Hover, Active + Text, Border + Subtle variants
✅ **Accessibility**: WCAG 2.2 AA minimum contrast on all interactive elements
✅ **Comprehensive**: 88 tokens cover all UI states and patterns

### Documentation

- **Detailed Reference**: See \`source/props/COLORS_REFERENCE.md\`
- **Palette Scales**: View Base/Colors story
- **Color Specification**: \`source/props/colors.css\` & \`brand.css\`
- **Implementation Guide**: See \`.github/instructions/core.instructions.md\`
        `}}}},u={name:"Brand colors",render:e=>p(e),args:{...l}},v=["Brand"];export{u as Brand,v as __namedExportsOrder,c as default};
