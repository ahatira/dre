import{t as a,T as s}from"./iframe-B-yX16js.js";import{D as i,a as o}from"./twig-CgICq6Dc.js";o(s);s.cache(!1);const n=e=>e,p=(e={})=>{const r=a.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/backgrounds/backgrounds.twig",data:[{type:"logic",token:{type:"Twig.logic.type.set",key:"header",expression:[{type:"Twig.expression.type.variable",value:"header",match:["header"]}],position:{start:0,end:25}},position:{start:0,end:25}},{type:"raw",value:`\r
`,position:{start:25,end:27}},{type:"logic",token:{type:"Twig.logic.type.set",key:"semantic_backgrounds",expression:[{type:"Twig.expression.type.variable",value:"semantic_backgrounds",match:["semantic_backgrounds"]}],position:{start:27,end:80}},position:{start:27,end:80}},{type:"raw",value:`\r
`,position:{start:80,end:82}},{type:"logic",token:{type:"Twig.logic.type.set",key:"neutral_backgrounds",expression:[{type:"Twig.expression.type.variable",value:"neutral_backgrounds",match:["neutral_backgrounds"]}],position:{start:82,end:133}},position:{start:82,end:133}},{type:"raw",value:`\r
`,position:{start:133,end:135}},{type:"logic",token:{type:"Twig.logic.type.set",key:"utilities_reference",expression:[{type:"Twig.expression.type.variable",value:"utilities_reference",match:["utilities_reference"]}],position:{start:135,end:186}},position:{start:135,end:186}},{type:"raw",value:`\r
\r
<!DOCTYPE html>\r
<html lang="en">\r
<head>\r
  <meta charset="UTF-8">\r
  <style>\r
    .ps-backgrounds-doc {\r
      padding: var(--size-10) var(--size-8);\r
      max-width: 1400px;\r
      margin: 0 auto;\r
    }\r
\r
    .ps-doc-header {\r
      margin-bottom: var(--size-10);\r
    }\r
\r
    .ps-doc-title {\r
      font-size: var(--font-size-8);\r
      font-weight: var(--font-weight-700);\r
      color: var(--text-primary);\r
      margin-bottom: var(--size-4);\r
    }\r
\r
    .ps-doc-lead {\r
      font-size: var(--font-size-4);\r
      color: var(--text-secondary);\r
      max-width: 80ch;\r
      line-height: var(--leading-relaxed);\r
    }\r
\r
    .ps-doc-meta {\r
      display: grid;\r
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));\r
      gap: var(--size-6);\r
      margin-top: var(--size-8);\r
      padding-top: var(--size-8);\r
      border-top: 1px solid var(--light);\r
    }\r
\r
    .ps-doc-meta__item {\r
      display: flex;\r
      flex-direction: column;\r
      gap: var(--size-2);\r
    }\r
\r
    .ps-doc-meta__label {\r
      font-size: var(--font-size-1);\r
      font-weight: var(--font-weight-600);\r
      text-transform: uppercase;\r
      letter-spacing: 0.05em;\r
      color: var(--text-tertiary);\r
    }\r
\r
    .ps-doc-meta__value {\r
      font-size: var(--font-size-4);\r
      font-weight: var(--font-weight-600);\r
      color: var(--text-primary);\r
    }\r
\r
    .ps-doc-section {\r
      margin-bottom: var(--size-10);\r
      padding-bottom: var(--size-10);\r
      border-bottom: 1px solid var(--light);\r
    }\r
\r
    .ps-doc-section:last-child {\r
      border-bottom: none;\r
      margin-bottom: 0;\r
      padding-bottom: 0;\r
    }\r
\r
    .ps-doc-section__header {\r
      margin-bottom: var(--size-8);\r
    }\r
\r
    .ps-doc-section__title {\r
      font-size: var(--font-size-5);\r
      font-weight: var(--font-weight-400);\r
      color: var(--text-primary);\r
      text-transform: uppercase;\r
      letter-spacing: 0.05em;\r
      opacity: 0.85;\r
      margin-bottom: var(--size-4);\r
    }\r
\r
    .ps-doc-section__description {\r
      font-size: var(--font-size-3);\r
      color: var(--text-secondary);\r
      line-height: var(--leading-relaxed);\r
      max-width: 75ch;\r
    }\r
\r
    /* Background swatches */\r
    .bg-swatch {\r
      display: flex;\r
      flex-direction: column;\r
      gap: var(--size-3);\r
      padding: var(--size-4);\r
      border-radius: var(--radius-2);\r
      border: 1px solid var(--light);\r
    }\r
\r
    .bg-swatch__preview {\r
      height: 120px;\r
      border-radius: var(--radius-1);\r
      border: 1px solid var(--light);\r
      transition: transform 0.2s ease;\r
    }\r
\r
    .bg-swatch__preview:hover {\r
      transform: scale(1.02);\r
    }\r
\r
    .bg-swatch__info {\r
      display: grid;\r
      gap: var(--size-2);\r
    }\r
\r
    .bg-swatch__label {\r
      font-size: var(--font-size-1);\r
      font-weight: var(--font-weight-600);\r
      color: var(--text-primary);\r
      text-transform: uppercase;\r
      letter-spacing: 0.05em;\r
    }\r
\r
    .bg-swatch__token {\r
      font-size: var(--font-size-0);\r
      font-family: var(--font-mono);\r
      color: var(--text-secondary);\r
    }\r
\r
    .bg-swatch__description {\r
      font-size: var(--font-size-2);\r
      color: var(--text-secondary);\r
    }\r
\r
    .bg-grid {\r
      display: grid;\r
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));\r
      gap: var(--size-5);\r
    }\r
\r
    .bg-group {\r
      display: flex;\r
      flex-direction: column;\r
      gap: var(--size-4);\r
    }\r
\r
    .bg-group__title {\r
      font-size: var(--font-size-3);\r
      font-weight: var(--font-weight-600);\r
      color: var(--text-primary);\r
      margin-bottom: var(--size-2);\r
      padding-bottom: var(--size-2);\r
      border-bottom: 2px solid var(--light);\r
    }\r
\r
    /* Token table */\r
    .ps-token-table {\r
      width: 100%;\r
      border-collapse: collapse;\r
      margin-top: var(--size-6);\r
    }\r
\r
    .ps-token-table th,\r
    .ps-token-table td {\r
      padding: var(--size-4);\r
      text-align: left;\r
      border-bottom: 1px solid var(--light);\r
    }\r
\r
    .ps-token-table th {\r
      background-color: var(--light);\r
      font-weight: var(--font-weight-600);\r
      text-transform: uppercase;\r
      letter-spacing: 0.05em;\r
      font-size: var(--font-size-0);\r
    }\r
\r
    .ps-token-table td {\r
      font-size: var(--font-size-2);\r
    }\r
\r
    .ps-token-table code {\r
      font-family: var(--font-mono);\r
      background-color: var(--light);\r
      padding: var(--size-1) var(--size-2);\r
      border-radius: var(--radius-1);\r
      color: var(--text-primary);\r
    }\r
  </style>\r
</head>\r
<body>\r
  <div class="ps-backgrounds-doc">\r
    <!-- Header -->\r
    <header class="ps-doc-header">\r
      <h1 class="ps-doc-title">`,position:{start:186,end:4951}},{type:"output",position:{start:4951,end:4969},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:4951,end:4969}},{type:"Twig.expression.type.key.period",position:{start:4951,end:4969},key:"title"}]},{type:"raw",value:`</h1>\r
      <p class="ps-doc-lead">`,position:{start:4969,end:5005}},{type:"output",position:{start:5005,end:5029},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:5005,end:5029}},{type:"Twig.expression.type.key.period",position:{start:5005,end:5029},key:"description"}]},{type:"raw",value:`</p>\r
      <dl class="ps-doc-meta">\r
        `,position:{start:5029,end:5075}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"meta",expression:[{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.key.period",key:"meta"}],position:{start:5075,end:5104},output:[{type:"raw",value:`\r
          <div class="ps-doc-meta__item">\r
            <dt class="ps-doc-meta__label">`,position:{start:5104,end:5192}},{type:"output",position:{start:5192,end:5208},stack:[{type:"Twig.expression.type.variable",value:"meta",match:["meta"],position:{start:5192,end:5208}},{type:"Twig.expression.type.key.period",position:{start:5192,end:5208},key:"label"}]},{type:"raw",value:`</dt>\r
            <dd class="ps-doc-meta__value">`,position:{start:5208,end:5258}},{type:"output",position:{start:5258,end:5274},stack:[{type:"Twig.expression.type.variable",value:"meta",match:["meta"],position:{start:5258,end:5274}},{type:"Twig.expression.type.key.period",position:{start:5258,end:5274},key:"value"}]},{type:"raw",value:`</dd>\r
          </div>\r
        `,position:{start:5274,end:5307}}]},position:{open:{start:5075,end:5104},close:{start:5307,end:5319}}},{type:"raw",value:`\r
      </dl>\r
    </header>\r
\r
    <!-- Semantic Backgrounds Section -->\r
    <section class="ps-doc-section">\r
      <header class="ps-doc-section__header">\r
        <h2 class="ps-doc-section__title">Semantic Backgrounds</h2>\r
        <p class="ps-doc-section__description">Color-coded backgrounds for semantic meaning. Each color has three levels: subtle (light), base (standard), and emphasis (dark).</p>\r
      </header>\r
      `,position:{start:5319,end:5752}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"bg",expression:[{type:"Twig.expression.type.variable",value:"semantic_backgrounds",match:["semantic_backgrounds"]}],position:{start:5752,end:5788},output:[{type:"raw",value:`\r
        <div class="bg-group">\r
          <h3 class="bg-group__title">`,position:{start:5788,end:5860}},{type:"output",position:{start:5860,end:5873},stack:[{type:"Twig.expression.type.variable",value:"bg",match:["bg"],position:{start:5860,end:5873}},{type:"Twig.expression.type.key.period",position:{start:5860,end:5873},key:"name"}]},{type:"raw",value:`</h3>\r
          <div class="bg-grid">\r
            `,position:{start:5873,end:5925}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"variant",expression:[{type:"Twig.expression.type.variable",value:"bg",match:["bg"]},{type:"Twig.expression.type.key.period",key:"variants"}],position:{start:5925,end:5957},output:[{type:"raw",value:`\r
              <div class="bg-swatch">\r
                <div class="bg-swatch__preview" style="background-color: var(`,position:{start:5957,end:6075}},{type:"output",position:{start:6075,end:6094},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:6075,end:6094}},{type:"Twig.expression.type.key.period",position:{start:6075,end:6094},key:"token"}]},{type:"raw",value:`);"></div>\r
                <div class="bg-swatch__info">\r
                  <div class="bg-swatch__label">.`,position:{start:6094,end:6202}},{type:"output",position:{start:6202,end:6221},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:6202,end:6221}},{type:"Twig.expression.type.key.period",position:{start:6202,end:6221},key:"class"}]},{type:"raw",value:`</div>\r
                  <div class="bg-swatch__token">`,position:{start:6221,end:6277}},{type:"output",position:{start:6277,end:6296},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:6277,end:6296}},{type:"Twig.expression.type.key.period",position:{start:6277,end:6296},key:"token"}]},{type:"raw",value:`</div>\r
                  <div class="bg-swatch__description">`,position:{start:6296,end:6358}},{type:"output",position:{start:6358,end:6383},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:6358,end:6383}},{type:"Twig.expression.type.key.period",position:{start:6358,end:6383},key:"description"}]},{type:"raw",value:`</div>\r
                </div>\r
              </div>\r
            `,position:{start:6383,end:6449}}]},position:{open:{start:5925,end:5957},close:{start:6449,end:6461}}},{type:"raw",value:`\r
          </div>\r
        </div>\r
      `,position:{start:6461,end:6503}}]},position:{open:{start:5752,end:5788},close:{start:6503,end:6515}}},{type:"raw",value:`\r
    </section>\r
\r
    <!-- Neutral Backgrounds Section -->\r
    <section class="ps-doc-section">\r
      <header class="ps-doc-section__header">\r
        <h2 class="ps-doc-section__title">Neutral Backgrounds</h2>\r
        <p class="ps-doc-section__description">Neutral color options for flexible layout and content presentation.</p>\r
      </header>\r
      <div class="bg-grid">\r
        `,position:{start:6515,end:6904}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"bg",expression:[{type:"Twig.expression.type.variable",value:"neutral_backgrounds",match:["neutral_backgrounds"]}],position:{start:6904,end:6939},output:[{type:"raw",value:`\r
          <div class="bg-swatch">\r
            <div class="bg-swatch__preview" style="background-color: var(`,position:{start:6939,end:7049}},{type:"output",position:{start:7049,end:7063},stack:[{type:"Twig.expression.type.variable",value:"bg",match:["bg"],position:{start:7049,end:7063}},{type:"Twig.expression.type.key.period",position:{start:7049,end:7063},key:"token"}]},{type:"raw",value:`);"></div>\r
            <div class="bg-swatch__info">\r
              <div class="bg-swatch__label">.`,position:{start:7063,end:7163}},{type:"output",position:{start:7163,end:7177},stack:[{type:"Twig.expression.type.variable",value:"bg",match:["bg"],position:{start:7163,end:7177}},{type:"Twig.expression.type.key.period",position:{start:7163,end:7177},key:"class"}]},{type:"raw",value:`</div>\r
              <div class="bg-swatch__token">`,position:{start:7177,end:7229}},{type:"output",position:{start:7229,end:7243},stack:[{type:"Twig.expression.type.variable",value:"bg",match:["bg"],position:{start:7229,end:7243}},{type:"Twig.expression.type.key.period",position:{start:7229,end:7243},key:"token"}]},{type:"raw",value:`</div>\r
              <div class="bg-swatch__description">`,position:{start:7243,end:7301}},{type:"output",position:{start:7301,end:7315},stack:[{type:"Twig.expression.type.variable",value:"bg",match:["bg"],position:{start:7301,end:7315}},{type:"Twig.expression.type.key.period",position:{start:7301,end:7315},key:"usage"}]},{type:"raw",value:`</div>\r
            </div>\r
          </div>\r
        `,position:{start:7315,end:7369}}]},position:{open:{start:6904,end:6939},close:{start:7369,end:7381}}},{type:"raw",value:`\r
      </div>\r
    </section>\r
\r
    <!-- Reference Section -->\r
    <section class="ps-doc-section">\r
      <header class="ps-doc-section__header">\r
        <h2 class="ps-doc-section__title">Usage Reference</h2>\r
        <p class="ps-doc-section__description">Complete reference for all background utility classes and their applications.</p>\r
      </header>\r
      <table class="ps-token-table">\r
        <thead>\r
          <tr>\r
            <th>Utility Class</th>\r
            <th>Description</th>\r
            <th>Example</th>\r
          </tr>\r
        </thead>\r
        <tbody>\r
          `,position:{start:7381,end:7976}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"row",expression:[{type:"Twig.expression.type.variable",value:"utilities_reference",match:["utilities_reference"]}],position:{start:7976,end:8012},output:[{type:"raw",value:`\r
            <tr>\r
              <td><code>`,position:{start:8012,end:8056}},{type:"output",position:{start:8056,end:8073},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:8056,end:8073}},{type:"Twig.expression.type.key.period",position:{start:8056,end:8073},key:"utility"}]},{type:"raw",value:`</code></td>\r
              <td>`,position:{start:8073,end:8105}},{type:"output",position:{start:8105,end:8126},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:8105,end:8126}},{type:"Twig.expression.type.key.period",position:{start:8105,end:8126},key:"description"}]},{type:"raw",value:`</td>\r
              <td><code>`,position:{start:8126,end:8157}},{type:"output",position:{start:8157,end:8174},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:8157,end:8174}},{type:"Twig.expression.type.key.period",position:{start:8157,end:8174},key:"example"}]},{type:"raw",value:`</code></td>\r
            </tr>\r
          `,position:{start:8174,end:8217}}]},position:{open:{start:7976,end:8012},close:{start:8217,end:8229}}},{type:"raw",value:`\r
        </tbody>\r
      </table>\r
    </section>\r
  </div>\r
</body>\r
</html>\r
\r
\r
`,position:{start:8229,end:8229}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),n(r.render({attributes:new i(t),...e}))}catch(t){return n("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/backgrounds/backgrounds.twig: "+t.toString())}},d={header:{title:"Background Utilities",description:"Comprehensive background color system for consistent visual hierarchy and semantic meaning. All backgrounds are built from the brand color tokens and include support for subtle, standard, and emphasis levels.",badge:"Utilities",meta:[{label:"Total Classes",value:"27+"},{label:"Color Variants",value:"9 semantic"},{label:"Levels",value:"Subtle, Base, Emphasis"}]},semantic_backgrounds:[{name:"Primary",slug:"primary",base_token:"--primary",variants:[{class:"bg-primary-subtle",token:"--primary-bg-subtle",description:"Light background for primary sections"},{class:"bg-primary",token:"--primary",description:"Standard primary background"},{class:"bg-primary-emphasis",token:"--primary",description:"Emphasis/dark primary background"}]},{name:"Secondary",slug:"secondary",base_token:"--secondary",variants:[{class:"bg-secondary-subtle",token:"--secondary-bg-subtle",description:"Light background for secondary sections"},{class:"bg-secondary",token:"--secondary",description:"Standard secondary background"},{class:"bg-secondary-emphasis",token:"--secondary",description:"Emphasis/dark secondary background"}]},{name:"Success",slug:"success",base_token:"--success",variants:[{class:"bg-success-subtle",token:"--success-bg-subtle",description:"Light background for success messages"},{class:"bg-success",token:"--success",description:"Standard success background"},{class:"bg-success-emphasis",token:"--success",description:"Emphasis success background"}]},{name:"Danger",slug:"danger",base_token:"--danger",variants:[{class:"bg-danger-subtle",token:"--danger-bg-subtle",description:"Light background for error messages"},{class:"bg-danger",token:"--danger",description:"Standard danger background"},{class:"bg-danger-emphasis",token:"--danger",description:"Emphasis danger background"}]},{name:"Warning",slug:"warning",base_token:"--warning",variants:[{class:"bg-warning-subtle",token:"--warning-bg-subtle",description:"Light background for warning messages"},{class:"bg-warning",token:"--warning",description:"Standard warning background"},{class:"bg-warning-emphasis",token:"--warning",description:"Emphasis warning background"}]},{name:"Info",slug:"info",base_token:"--info",variants:[{class:"bg-info-subtle",token:"--info-bg-subtle",description:"Light background for informational content"},{class:"bg-info",token:"--info",description:"Standard info background"},{class:"bg-info-emphasis",token:"--info",description:"Emphasis info background"}]},{name:"Gold",slug:"gold",base_token:"--gold",variants:[{class:"bg-gold-subtle",token:"--gold-bg-subtle",description:"Light background for premium content"},{class:"bg-gold",token:"--gold",description:"Standard gold background"},{class:"bg-gold-emphasis",token:"--gold",description:"Emphasis gold background"}]}],neutral_backgrounds:[{name:"White",class:"bg-white",token:"--white",usage:"Primary content background"},{name:"Light Gray",class:"bg-light",token:"--light",usage:"Subtle section backgrounds"},{name:"Dark",class:"bg-dark",token:"--dark",usage:"High contrast inverse backgrounds"}],utilities_reference:[{utility:".bg-{color}",description:"Apply semantic background color",example:".bg-primary, .bg-success, .bg-danger"},{utility:".bg-{color}-subtle",description:"Light/muted background variant",example:".bg-primary-subtle, .bg-success-subtle"},{utility:".bg-{color}-emphasis",description:"Dark/saturated background variant",example:".bg-primary-emphasis, .bg-success-emphasis"},{utility:".bg-white, .bg-light, .bg-dark",description:"Neutral background colors",example:"Static utilities for neutral backgrounds"}]},g={title:"Base/Backgrounds"},u={name:"Backgrounds",render:e=>p(e),args:{...d},parameters:{tags:["autodocs"],layout:"fullscreen",docs:{description:{component:"Comprehensive background color utilities system for semantic and neutral backgrounds.",story:"Complete reference for all background utility classes, including semantic colors (primary, secondary, success, danger, warning, info, gold) at three levels (subtle, base, emphasis), and neutral backgrounds."}}}},y=["Backgrounds"];export{u as Backgrounds,y as __namedExportsOrder,g as default};
