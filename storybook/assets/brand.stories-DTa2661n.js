import{t as a,T as s}from"./iframe-B-yX16js.js";import{D as n,a as o}from"./twig-CgICq6Dc.js";import"./_base-story-Cp7prtAx.js";o(s);s.cache(!1);a.twig({id:"../_base-story.twig",data:[{type:"raw",value:`\r
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
`,position:{start:2872,end:2872}}],precompiled:!0});const i=e=>e,p=(e={})=>{const r=a.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/brand/brand.twig",data:[{type:"raw",value:`\r
\r
`,position:{start:311,end:315}},{type:"raw",value:`\r
`,position:{start:437,end:439}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"theme_colors_section",position:{start:439,end:469},output:[{type:"raw",value:`\r
  <div class="theme-colors-section">\r
    <div class="theme-colors-grid">\r
      `,position:{start:469,end:552}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"title",valueVar:"list",expression:[{type:"Twig.expression.type.variable",value:"brands",match:["brands"]}],position:{start:552,end:583},output:[{type:"raw",value:`\r
        `,position:{start:583,end:593}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"list",match:["list"]}],position:{start:593,end:615},output:[{type:"raw",value:`\r
          <div class="theme-color-card theme-color-`,position:{start:615,end:668}},{type:"output",position:{start:668,end:683},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:668,end:683}},{type:"Twig.expression.type.key.period",position:{start:668,end:683},key:"slug"}]},{type:"raw",value:`">\r
            <span class="theme-color-name">`,position:{start:683,end:730}},{type:"output",position:{start:730,end:745},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:730,end:745}},{type:"Twig.expression.type.key.period",position:{start:730,end:745},key:"name"}]},{type:"raw",value:`</span>\r
          </div>\r
        `,position:{start:745,end:780}}]},position:{open:{start:593,end:615},close:{start:780,end:792}}},{type:"raw",value:`\r
      `,position:{start:792,end:800}}]},position:{open:{start:552,end:583},close:{start:800,end:812}}},{type:"raw",value:`\r
    </div>\r
  </div>\r
`,position:{start:812,end:836}}]},position:{open:{start:439,end:469},close:{start:836,end:848}}},{type:"raw",value:`\r
\r
`,position:{start:848,end:852}},{type:"raw",value:`\r
`,position:{start:983,end:985}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"semantic_colors_section",position:{start:985,end:1018},output:[{type:"raw",value:`\r
  <div class="brand-grid">\r
    `,position:{start:1018,end:1052}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"title",valueVar:"list",expression:[{type:"Twig.expression.type.variable",value:"brands",match:["brands"]}],position:{start:1052,end:1083},output:[{type:"raw",value:`\r
      <div class="brand-group">\r
        <h3 class="brand-group__title">`,position:{start:1083,end:1157}},{type:"output",position:{start:1157,end:1168},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:1157,end:1168}}]},{type:"raw",value:`</h3>\r
        <table class="brand-table">\r
          <thead>\r
            <tr>\r
              <th style="width: 50%;">Description</th>\r
              <th style="width: 200px;" class="ps-0">Swatch</th>\r
              <th>Variables</th>\r
            </tr>\r
          </thead>\r
          <tbody>\r
            `,position:{start:1168,end:1475}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"list",match:["list"]}],position:{start:1475,end:1497},output:[{type:"raw",value:`\r
              `,position:{start:1497,end:1513}},{type:"raw",value:`\r
              <tr`,position:{start:1576,end:1595}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"slug"},{type:"Twig.expression.type.string",value:"secondary"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1595,end:1628},output:[{type:"raw",value:' class="demo-brand-divider"',position:{start:1628,end:1655}}]},position:{open:{start:1595,end:1628},close:{start:1655,end:1666}}},{type:"raw",value:`>\r
                <td rowspan="`,position:{start:1666,end:1698}},{type:"output",position:{start:1698,end:1724},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1698,end:1724}},{type:"Twig.expression.type.key.period",position:{start:1698,end:1724},key:"variants"},{type:"Twig.expression.type.filter",value:"length",match:["|length","length"],position:{start:1698,end:1724}}]},{type:"raw",value:`">\r
                  <p><strong>`,position:{start:1724,end:1757}},{type:"output",position:{start:1757,end:1772},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1757,end:1772}},{type:"Twig.expression.type.key.period",position:{start:1757,end:1772},key:"name"}]},{type:"raw",value:" —</strong> ",position:{start:1772,end:1784}},{type:"output",position:{start:1784,end:1806},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1784,end:1806}},{type:"Twig.expression.type.key.period",position:{start:1784,end:1806},key:"description"}]},{type:"raw",value:`</p>\r
                </td>\r
                `,position:{start:1806,end:1851}},{type:"raw",value:`\r
                `,position:{start:1870,end:1888}},{type:"logic",token:{type:"Twig.logic.type.set",key:"first_variant",expression:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"variants"},{type:"Twig.expression.type.key.brackets",stack:[{type:"Twig.expression.type.number",value:0,match:["0",null]}]}],position:{start:1888,end:1930}},position:{start:1888,end:1930}},{type:"raw",value:`\r
                <td class="ps-0">\r
                  <div class="demo-brand-swatch p-3 rounded-2`,position:{start:1930,end:2028}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"base"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2028,end:2065},output:[{type:"raw",value:" bg-",position:{start:2065,end:2069}},{type:"output",position:{start:2069,end:2084},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:2069,end:2084}},{type:"Twig.expression.type.key.period",position:{start:2069,end:2084},key:"slug"}]}]},position:{open:{start:2028,end:2065},close:{start:2084,end:2095}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2095,end:2134},output:[{type:"raw",value:" border",position:{start:2134,end:2141}}]},position:{open:{start:2095,end:2134},close:{start:2141,end:2152}}},{type:"raw",value:`" \r
                    `,position:{start:2152,end:2176}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"subtle"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2176,end:2215},output:[{type:"raw",value:'style="background-color: var(',position:{start:2215,end:2244}},{type:"output",position:{start:2244,end:2267},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2244,end:2267}},{type:"Twig.expression.type.key.period",position:{start:2244,end:2267},key:"var"}]},{type:"raw",value:')"',position:{start:2267,end:2269}}]},position:{open:{start:2176,end:2215},close:{start:2269,end:2280}}},{type:"raw",value:`\r
                    `,position:{start:2280,end:2302}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2302,end:2341},output:[{type:"raw",value:'style="border: 5px var(',position:{start:2341,end:2364}},{type:"output",position:{start:2364,end:2387},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2364,end:2387}},{type:"Twig.expression.type.key.period",position:{start:2364,end:2387},key:"var"}]},{type:"raw",value:') solid"',position:{start:2387,end:2395}}]},position:{open:{start:2302,end:2341},close:{start:2395,end:2406}}},{type:"raw",value:`\r
                    `,position:{start:2406,end:2428}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2428,end:2465},output:[{type:"raw",value:'style="color: var(',position:{start:2465,end:2483}},{type:"output",position:{start:2483,end:2506},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2483,end:2506}},{type:"Twig.expression.type.key.period",position:{start:2483,end:2506},key:"var"}]},{type:"raw",value:')"',position:{start:2506,end:2508}}]},position:{open:{start:2428,end:2465},close:{start:2508,end:2519}}},{type:"raw",value:`>\r
                    `,position:{start:2519,end:2542}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2542,end:2579},output:[{type:"raw",value:'<span class="demo-brand-text fw-bold">Text</span>',position:{start:2579,end:2628}}]},position:{open:{start:2542,end:2579},close:{start:2628,end:2638}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2628,end:2638},output:[{type:"raw",value:"&nbsp;",position:{start:2638,end:2644}}]},position:{open:{start:2628,end:2638},close:{start:2644,end:2655}}},{type:"raw",value:`\r
                  </div>\r
                </td>\r
                <td>\r
                  <p><code>`,position:{start:2655,end:2755}},{type:"output",position:{start:2755,end:2778},stack:[{type:"Twig.expression.type.variable",value:"first_variant",match:["first_variant"],position:{start:2755,end:2778}},{type:"Twig.expression.type.key.period",position:{start:2755,end:2778},key:"var"}]},{type:"raw",value:`</code></p>\r
                </td>\r
              </tr>\r
              \r
              `,position:{start:2778,end:2865}},{type:"raw",value:`\r
              `,position:{start:2898,end:2914}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"variant",expression:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"variants"},{type:"Twig.expression.type.filter",value:"slice",match:["|slice","slice"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.number",value:1,match:["1",null]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2914,end:2957},output:[{type:"raw",value:`\r
                <tr`,position:{start:2957,end:2978}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"slug"},{type:"Twig.expression.type.string",value:"secondary"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2978,end:3011},output:[{type:"raw",value:' class="demo-brand-divider"',position:{start:3011,end:3038}}]},position:{open:{start:2978,end:3011},close:{start:3038,end:3049}}},{type:"raw",value:`>\r
                  <td class="ps-0">\r
                    <div class="demo-brand-swatch p-3 rounded-2`,position:{start:3049,end:3152}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3152,end:3185},output:[{type:"raw",value:" border",position:{start:3185,end:3192}}]},position:{open:{start:3152,end:3185},close:{start:3192,end:3203}}},{type:"raw",value:`" \r
                      `,position:{start:3203,end:3229}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"subtle"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3229,end:3262},output:[{type:"raw",value:'style="background-color: var(',position:{start:3262,end:3291}},{type:"output",position:{start:3291,end:3308},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3291,end:3308}},{type:"Twig.expression.type.key.period",position:{start:3291,end:3308},key:"var"}]},{type:"raw",value:')"',position:{start:3308,end:3310}}]},position:{open:{start:3229,end:3262},close:{start:3310,end:3321}}},{type:"raw",value:`\r
                      `,position:{start:3321,end:3345}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"border"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3345,end:3378},output:[{type:"raw",value:'style="border: 5px var(',position:{start:3378,end:3401}},{type:"output",position:{start:3401,end:3418},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3401,end:3418}},{type:"Twig.expression.type.key.period",position:{start:3401,end:3418},key:"var"}]},{type:"raw",value:') solid"',position:{start:3418,end:3426}}]},position:{open:{start:3345,end:3378},close:{start:3426,end:3437}}},{type:"raw",value:`\r
                      `,position:{start:3437,end:3461}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3461,end:3492},output:[{type:"raw",value:'style="color: var(',position:{start:3492,end:3510}},{type:"output",position:{start:3510,end:3527},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3510,end:3527}},{type:"Twig.expression.type.key.period",position:{start:3510,end:3527},key:"var"}]},{type:"raw",value:')"',position:{start:3527,end:3529}}]},position:{open:{start:3461,end:3492},close:{start:3529,end:3540}}},{type:"raw",value:`>\r
                      `,position:{start:3540,end:3565}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.key.period",key:"type"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:3565,end:3596},output:[{type:"raw",value:'<span class="demo-brand-text fw-bold">Text</span>',position:{start:3596,end:3645}}]},position:{open:{start:3565,end:3596},close:{start:3645,end:3655}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:3645,end:3655},output:[{type:"raw",value:"&nbsp;",position:{start:3655,end:3661}}]},position:{open:{start:3645,end:3655},close:{start:3661,end:3672}}},{type:"raw",value:`\r
                    </div>\r
                  </td>\r
                  <td>\r
                    <p><code>`,position:{start:3672,end:3780}},{type:"output",position:{start:3780,end:3797},stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"],position:{start:3780,end:3797}},{type:"Twig.expression.type.key.period",position:{start:3780,end:3797},key:"var"}]},{type:"raw",value:`</code></p>\r
                  </td>\r
                </tr>\r
              `,position:{start:3797,end:3872}}]},position:{open:{start:2914,end:2957},close:{start:3872,end:3884}}},{type:"raw",value:`\r
            `,position:{start:3884,end:3898}}]},position:{open:{start:1475,end:1497},close:{start:3898,end:3910}}},{type:"raw",value:`\r
          </tbody>\r
        </table>\r
      </div>\r
    `,position:{start:3910,end:3968}}]},position:{open:{start:1052,end:1083},close:{start:3968,end:3980}}},{type:"raw",value:`\r
  </div>\r
`,position:{start:3980,end:3992}}]},position:{open:{start:985,end:1018},close:{start:3992,end:4004}}},{type:"raw",value:`\r
\r
`,position:{start:4004,end:4008}},{type:"raw",value:`\r
`,position:{start:4041,end:4043}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_base-story.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"header"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Brand Colors"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Complete semantic color system for UI components and design states. BNP Paribas Real Estate official brand palette with base, subtle, border, and text-emphasis variants."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Design Tokens"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"meta"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Colors"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"8"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Variants"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"32"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Source"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"source/props/brand.css"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"sections"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Theme Colors"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Semantic Color Cards"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Official BNP Paribas Real Estate semantic color palette for UI components and states."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"theme_colors_section",match:["theme_colors_section"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Color System"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"All Colors and Variants"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Complete semantic token system with base, subtle, border, and text-emphasis variants for each theme color."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"semantic_colors_section",match:["semantic_colors_section"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:4043,end:5027}},position:{start:4043,end:5027}},{type:"raw",value:`\r
\r
`,position:{start:5027,end:5027}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),i(r.render({attributes:new n(t),...e}))}catch(t){return i("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/brand/brand.twig: "+t.toString())}},y={brands:{"Semantic Colors":[{name:"Primary",slug:"primary",description:"Main theme color, used for hyperlinks, focus styles, and component and form active states. Official BNP green #00915A.",variants:[{type:"base",var:"--primary",value:"#00915a"},{type:"subtle",var:"--primary-bg-subtle",value:"#ebf7f4"},{type:"border",var:"--primary-border-subtle",value:"#c7e8df"},{type:"text",var:"--primary-text-emphasis",value:"#01563a"}]},{name:"Secondary",slug:"secondary",description:"Secondary brand color for alternative actions and accents. BNP pink #A12B66.",variants:[{type:"base",var:"--secondary",value:"#A12B66"},{type:"subtle",var:"--secondary-bg-subtle",value:"#f9ecf2"},{type:"border",var:"--secondary-border-subtle",value:"#ecc6d8"},{type:"text",var:"--secondary-text-emphasis",value:"#751d4e"}]},{name:"Success",slug:"success",description:"Theme color used for positive or successful actions and information. Distinct from Primary - uses teal #198754.",variants:[{type:"base",var:"--success",value:"#198754"},{type:"subtle",var:"--success-bg-subtle",value:"#e7f4f1"},{type:"border",var:"--success-border-subtle",value:"#a3cfbb"},{type:"text",var:"--success-text-emphasis",value:"#124a3b"}]},{name:"Danger",slug:"danger",description:"Theme color used for errors and dangerous actions. Red #EB3636.",variants:[{type:"base",var:"--danger",value:"#EB3636"},{type:"subtle",var:"--danger-bg-subtle",value:"#fef7f7"},{type:"border",var:"--danger-border-subtle",value:"#f9d1d1"},{type:"text",var:"--danger-text-emphasis",value:"#a62626"}]},{name:"Warning",slug:"warning",description:"Theme color used for non-destructive warning messages. Yellow #FFC107.",variants:[{type:"base",var:"--warning",value:"#fbbf24"},{type:"subtle",var:"--warning-bg-subtle",value:"#fffdf3"},{type:"border",var:"--warning-border-subtle",value:"#fde68a"},{type:"text",var:"--warning-text-emphasis",value:"#92400e"}]},{name:"Info",slug:"info",description:"Theme color used for neutral and informative content. Blue #2563EB.",variants:[{type:"base",var:"--info",value:"#2563eb"},{type:"subtle",var:"--info-bg-subtle",value:"#f7faff"},{type:"border",var:"--info-border-subtle",value:"#bfdbfe"},{type:"text",var:"--info-text-emphasis",value:"#1e3a8a"}]},{name:"Light",slug:"light",description:"Additional theme option for less contrasting colors. Light gray #EBEDEF.",variants:[{type:"base",var:"--light",value:"#ebedef"},{type:"subtle",var:"--light-bg-subtle",value:"#ffffff"},{type:"border",var:"--light-border-subtle",value:"#d6dbde"},{type:"text",var:"--light-text-emphasis",value:"#3a4551"}]},{name:"Dark",slug:"dark",description:"Additional theme option for higher contrasting colors. Dark gray #434F57.",variants:[{type:"base",var:"--dark",value:"#434f57"},{type:"subtle",var:"--dark-bg-subtle",value:"#f9f9fb"},{type:"border",var:"--dark-border-subtle",value:"#c4c9cf"},{type:"text",var:"--dark-text-emphasis",value:"#333333"}]},{name:"Gold",slug:"gold",description:"Accent theme option for premium or highlighted content. Gold #D1AE6E.",variants:[{type:"base",var:"--gold",value:"#d1ae6e"},{type:"subtle",var:"--gold-bg-subtle",value:"#f6eddc"},{type:"border",var:"--gold-border-subtle",value:"#e2cfa2"},{type:"text",var:"--gold-text-emphasis",value:"#715e3b"}]}]}},v={title:"Base/Brand"},g={name:"Brand",render:e=>p(e),args:{...y}},u=["Brand"];export{g as Brand,u as __namedExportsOrder,v as default};
