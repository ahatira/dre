import{t as n,T as s}from"./iframe-B-yX16js.js";import{D as a,a as o}from"./twig-CgICq6Dc.js";import"./_base-story-Cp7prtAx.js";o(s);s.cache(!1);n.twig({id:"@base/_base-story.twig",data:[{type:"raw",value:`\r
\r
<article class="ps-base-story">\r
  \r
  `,position:{start:711,end:754}},{type:"raw",value:`\r
  `,position:{start:866,end:870}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"]}],position:{start:870,end:885},output:[{type:"raw",value:`\r
    <header class="ps-base-story__header">\r
      `,position:{start:885,end:937}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.key.period",key:"badge"}],position:{start:937,end:958},output:[{type:"raw",value:`\r
        <span class="ps-story-badge">`,position:{start:958,end:997}},{type:"output",position:{start:997,end:1015},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:997,end:1015}},{type:"Twig.expression.type.key.period",position:{start:997,end:1015},key:"badge"}]},{type:"raw",value:`</span>\r
      `,position:{start:1015,end:1030}}]},position:{open:{start:937,end:958},close:{start:1030,end:1041}}},{type:"raw",value:`\r
      \r
      <h1 class="ps-story-title">`,position:{start:1041,end:1084}},{type:"output",position:{start:1084,end:1102},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:1084,end:1102}},{type:"Twig.expression.type.key.period",position:{start:1084,end:1102},key:"title"}]},{type:"raw",value:`</h1>\r
      \r
      `,position:{start:1102,end:1123}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.key.period",key:"description"}],position:{start:1123,end:1150},output:[{type:"raw",value:`\r
        <p class="ps-story-lead">`,position:{start:1150,end:1185}},{type:"output",position:{start:1185,end:1209},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:1185,end:1209}},{type:"Twig.expression.type.key.period",position:{start:1185,end:1209},key:"description"}]},{type:"raw",value:`</p>\r
      `,position:{start:1209,end:1221}}]},position:{open:{start:1123,end:1150},close:{start:1221,end:1232}}},{type:"raw",value:`\r
      \r
      `,position:{start:1232,end:1248}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.key.period",key:"meta"}],position:{start:1248,end:1268},output:[{type:"raw",value:`\r
        <dl class="ps-story-meta">\r
          `,position:{start:1268,end:1316}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.key.period",key:"meta"}],position:{start:1316,end:1345},output:[{type:"raw",value:`\r
            <div class="ps-meta-item">\r
              <dt>`,position:{start:1345,end:1405}},{type:"output",position:{start:1405,end:1421},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1405,end:1421}},{type:"Twig.expression.type.key.period",position:{start:1405,end:1421},key:"label"}]},{type:"raw",value:`</dt>\r
              <dd>`,position:{start:1421,end:1446}},{type:"output",position:{start:1446,end:1462},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1446,end:1462}},{type:"Twig.expression.type.key.period",position:{start:1446,end:1462},key:"value"}]},{type:"raw",value:`</dd>\r
            </div>\r
          `,position:{start:1462,end:1499}}]},position:{open:{start:1316,end:1345},close:{start:1499,end:1511}}},{type:"raw",value:`\r
        </dl>\r
      `,position:{start:1511,end:1534}}]},position:{open:{start:1248,end:1268},close:{start:1534,end:1545}}},{type:"raw",value:`\r
    </header>\r
  `,position:{start:1545,end:1564}}]},position:{open:{start:870,end:885},close:{start:1564,end:1575}}},{type:"raw",value:`\r
\r
  `,position:{start:1575,end:1581}},{type:"raw",value:`\r
  `,position:{start:1697,end:1701}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"sections",match:["sections"]}],position:{start:1701,end:1718},output:[{type:"raw",value:`\r
    `,position:{start:1718,end:1724}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"section",expression:[{type:"Twig.expression.type.variable",value:"sections",match:["sections"]}],position:{start:1724,end:1753},output:[{type:"raw",value:`\r
      <section class="ps-base-section`,position:{start:1753,end:1792}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"class"}],position:{start:1792,end:1814},output:[{type:"raw",value:" ",position:{start:1814,end:1815}},{type:"output",position:{start:1815,end:1834},stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"],position:{start:1815,end:1834}},{type:"Twig.expression.type.key.period",position:{start:1815,end:1834},key:"class"}]}]},position:{open:{start:1792,end:1814},close:{start:1834,end:1845}}},{type:"raw",value:`">\r
        \r
        `,position:{start:1845,end:1867}},{type:"raw",value:`\r
        `,position:{start:1887,end:1897}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"badge"},{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"title"},{type:"Twig.expression.type.operator.binary",value:"or",precidence:14,associativity:"leftToRight",operator:"or"},{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"description"},{type:"Twig.expression.type.operator.binary",value:"or",precidence:14,associativity:"leftToRight",operator:"or"}],position:{start:1897,end:1959},output:[{type:"raw",value:`\r
          <header class="ps-section-header">\r
            `,position:{start:1959,end:2019}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"badge"}],position:{start:2019,end:2041},output:[{type:"raw",value:`\r
              <span class="ps-section-badge">`,position:{start:2041,end:2088}},{type:"output",position:{start:2088,end:2107},stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"],position:{start:2088,end:2107}},{type:"Twig.expression.type.key.period",position:{start:2088,end:2107},key:"badge"}]},{type:"raw",value:`</span>\r
            `,position:{start:2107,end:2128}}]},position:{open:{start:2019,end:2041},close:{start:2128,end:2139}}},{type:"raw",value:`\r
            \r
            `,position:{start:2139,end:2167}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"title"}],position:{start:2167,end:2189},output:[{type:"raw",value:`\r
              <h2 class="ps-section-title">`,position:{start:2189,end:2234}},{type:"output",position:{start:2234,end:2253},stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"],position:{start:2234,end:2253}},{type:"Twig.expression.type.key.period",position:{start:2234,end:2253},key:"title"}]},{type:"raw",value:`</h2>\r
            `,position:{start:2253,end:2272}}]},position:{open:{start:2167,end:2189},close:{start:2272,end:2283}}},{type:"raw",value:`\r
            \r
            `,position:{start:2283,end:2311}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"description"}],position:{start:2311,end:2339},output:[{type:"raw",value:`\r
              <p class="ps-section-intro">`,position:{start:2339,end:2383}},{type:"output",position:{start:2383,end:2408},stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"],position:{start:2383,end:2408}},{type:"Twig.expression.type.key.period",position:{start:2383,end:2408},key:"description"}]},{type:"raw",value:`</p>\r
            `,position:{start:2408,end:2426}}]},position:{open:{start:2311,end:2339},close:{start:2426,end:2437}}},{type:"raw",value:`\r
          </header>\r
        `,position:{start:2437,end:2468}}]},position:{open:{start:1897,end:1959},close:{start:2468,end:2479}}},{type:"raw",value:`\r
\r
        `,position:{start:2479,end:2491}},{type:"raw",value:`\r
        `,position:{start:2512,end:2522}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"]},{type:"Twig.expression.type.key.period",key:"content"}],position:{start:2522,end:2546},output:[{type:"raw",value:`\r
          <div class="ps-section-content">\r
            `,position:{start:2546,end:2604}},{type:"output",position:{start:2604,end:2629},stack:[{type:"Twig.expression.type.variable",value:"section",match:["section"],position:{start:2604,end:2629}},{type:"Twig.expression.type.key.period",position:{start:2604,end:2629},key:"content"},{type:"Twig.expression.type.filter",value:"raw",match:["|raw","raw"],position:{start:2604,end:2629}}]},{type:"raw",value:`\r
          </div>\r
        `,position:{start:2629,end:2657}}]},position:{open:{start:2522,end:2546},close:{start:2657,end:2668}}},{type:"raw",value:`\r
        \r
      </section>\r
    `,position:{start:2668,end:2702}}]},position:{open:{start:1724,end:1753},close:{start:2702,end:2714}}},{type:"raw",value:`\r
  `,position:{start:2714,end:2718}}]},position:{open:{start:1701,end:1718},close:{start:2718,end:2729}}},{type:"raw",value:`\r
\r
</article>\r
\r
`,position:{start:2729,end:2747}},{type:"raw",value:`\r
<style>\r
/* ========================================\r
   Story Container\r
   ======================================== */\r
.ps-base-story {\r
  max-width: 1400px;\r
  margin: 0 auto;\r
  padding: var(--size-10) var(--size-8);\r
  background: var(--white);\r
}\r
\r
/* ========================================\r
   Story Header\r
   ======================================== */\r
.ps-base-story__header {\r
  margin-block-end: var(--size-12);\r
  padding-block-end: var(--size-8);\r
  border-block-end: 1px solid var(--border-light);\r
}\r
\r
.ps-story-badge {\r
  display: inline-block;\r
  padding: var(--size-1) var(--size-3);\r
  margin-block-end: var(--size-4);\r
  background: var(--primary-subtle);\r
  color: var(--primary-text-emphasis);\r
  font-size: var(--font-size-1);\r
  font-weight: var(--font-weight-600);\r
  text-transform: uppercase;\r
  letter-spacing: 0.05em;\r
  border-radius: var(--radius-pill);\r
}\r
\r
.ps-story-title {\r
  font-size: var(--font-size-10);\r
  font-weight: var(--font-weight-700);\r
  line-height: var(--leading-tight);\r
  color: var(--text-primary);\r
  margin: 0 0 var(--size-5) 0;\r
}\r
\r
.ps-story-lead {\r
  font-size: var(--font-size-5);\r
  line-height: var(--leading-relaxed);\r
  color: var(--text-secondary);\r
  margin: 0 0 var(--size-6) 0;\r
  max-width: 80ch;\r
}\r
\r
.ps-story-meta {\r
  display: grid;\r
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));\r
  gap: var(--size-4);\r
  margin: var(--size-6) 0 0 0;\r
  padding: var(--size-5);\r
  background: var(--gray-50);\r
  border-radius: var(--radius-3);\r
  border: 1px solid var(--border-light);\r
}\r
\r
.ps-meta-item {\r
  display: flex;\r
  flex-direction: column;\r
  gap: var(--size-1);\r
  \r
  dt {\r
    font-size: var(--font-size-2);\r
    font-weight: var(--font-weight-600);\r
    color: var(--text-tertiary);\r
    text-transform: uppercase;\r
    letter-spacing: 0.025em;\r
  }\r
  \r
  dd {\r
    margin: 0;\r
    font-size: var(--font-size-4);\r
    font-weight: var(--font-weight-600);\r
    color: var(--text-primary);\r
  }\r
}\r
\r
/* ========================================\r
   Sections\r
   ======================================== */\r
.ps-base-section {\r
  margin-block-end: var(--size-10);\r
  \r
  &:last-child {\r
    margin-block-end: 0;\r
  }\r
}\r
\r
.ps-section-header {\r
  margin-block-end: var(--size-8);\r
}\r
\r
.ps-section-badge {\r
  display: inline-block;\r
  padding: var(--size-1) var(--size-2);\r
  margin-block-end: var(--size-3);\r
  background: var(--gray-100);\r
  color: var(--text-secondary);\r
  font-size: var(--font-size-0);\r
  font-weight: var(--font-weight-600);\r
  text-transform: uppercase;\r
  letter-spacing: 0.05em;\r
  border-radius: var(--radius-1);\r
}\r
\r
.ps-section-title {\r
  font-size: var(--font-size-5);\r
  font-weight: var(--font-weight-400);\r
  line-height: var(--leading-tight);\r
  color: var(--text-primary);\r
  margin: 0 0 var(--size-4) 0;\r
  text-transform: uppercase;\r
  letter-spacing: 0.05em;\r
  opacity: 0.85;\r
}\r
\r
.ps-section-intro {\r
  font-size: var(--font-size-3);\r
  line-height: var(--leading-relaxed);\r
  color: var(--text-secondary);\r
  margin: 0;\r
  max-width: 75ch;\r
}\r
\r
.ps-section-content {\r
  margin-block-start: var(--size-8);\r
}\r
\r
/* ========================================\r
   Responsive\r
   ======================================== */\r
@media (width < 768px) {\r
  .ps-base-story {\r
    padding: var(--size-8) var(--size-5);\r
  }\r
  \r
  .ps-story-title {\r
    font-size: var(--font-size-8);\r
  }\r
  \r
  .ps-story-lead {\r
    font-size: var(--font-size-4);\r
  }\r
  \r
  .ps-story-meta {\r
    grid-template-columns: 1fr;\r
  }\r
  \r
  .ps-section-title {\r
    font-size: var(--font-size-6);\r
  }\r
}\r
\r
/* ========================================\r
   Print Styles\r
   ======================================== */\r
@media print {\r
  .ps-base-story {\r
    padding: 0;\r
  }\r
  \r
  .ps-story-badge,\r
  .ps-section-badge {\r
    border: 1px solid currentColor;\r
  }\r
}\r
</style>\r
`,position:{start:2872,end:2872}}],precompiled:!0});const i=e=>e,p=(e={})=>{const r=n.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/aspects/aspects.twig",data:[{type:"raw",value:`\r
\r
`,position:{start:193,end:197}},{type:"raw",value:`\r
`,position:{start:236,end:238}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"aspect_ratios_content",position:{start:238,end:269},output:[{type:"raw",value:`\r
  <div class="demo-aspect-ratio">\r
    <div class="aspect-demo">\r
      <div style="aspect-ratio: var(--ratio-box)">box<br>1:1</div>\r
      <div style="aspect-ratio: var(--ratio-photo)">photo<br>3:2</div>\r
      <div style="aspect-ratio: var(--ratio-portrait)">portrait<br>3:4</div>\r
      <div style="aspect-ratio: var(--ratio-landscape)">landscape<br>4:3</div>\r
      <div style="aspect-ratio: var(--ratio-widescreen)">widescreen<br>16:9</div>\r
      <div style="aspect-ratio: var(--ratio-cinemascope)">cinemascope<br>21:9</div>\r
      <div style="aspect-ratio: var(--ratio-golden)">golden<br>φ:1</div>\r
    </div>\r
  </div>\r
`,position:{start:269,end:899}}]},position:{open:{start:238,end:269},close:{start:899,end:911}}},{type:"raw",value:`\r
\r
`,position:{start:911,end:915}},{type:"raw",value:`\r
`,position:{start:948,end:950}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@base/_base-story.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"header"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Design Tokens"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.variable",value:"title",match:["title"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.variable",value:"description",match:["description"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"meta"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Tokens"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.variable",value:"count",match:["count"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Source"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"props/aspects.css"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"sections"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"CSS Custom Properties"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Standard Ratios"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Common aspect ratios for images, cards, and media containers. All containers maintain consistent height (--size-40) for comparison."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"aspect_ratios_content",match:["aspect_ratios_content"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:950,end:1517}},position:{start:950,end:1517}},{type:"raw",value:`\r
`,position:{start:1517,end:1517}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),i(r.render({attributes:new a(t),...e}))}catch(t){return i("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/aspects/aspects.twig: "+t.toString())}},y={title:"Aspect Ratio System",description:"CSS aspect ratio tokens for consistent image and media container sizing across the design system.",count:7},g={title:"Base/Aspects"},v={name:"Aspect Ratios",render:e=>p(e),args:{...y}},u=["Aspects"];export{v as Aspects,u as __namedExportsOrder,g as default};
