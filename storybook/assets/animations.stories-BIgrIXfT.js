import{t,T as s}from"./iframe-B-yX16js.js";import{D as r,a as o}from"./twig-CgICq6Dc.js";import"./_base-story-Cp7prtAx.js";o(s);s.cache(!1);t.twig({id:"../_base-story.twig",data:[{type:"raw",value:`\r
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
`,position:{start:2872,end:2872}}],precompiled:!0});const n=e=>e,d=(e={})=>{const i=t.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/animations/animations.twig",data:[{type:"raw",value:`

`,position:{start:220,end:222}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_base-story.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"header"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Design Tokens"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Animations"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Effets de mouvement réutilisables avec durées standardisées et fonctions de timing pour des interactions UI cohérentes. Cliquez sur les cartes pour voir les animations en action."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"meta"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Durations"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"6"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Keyframes"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"11"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Presets"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"value"},{type:"Twig.expression.type.string",value:"20"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"sections"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Animations"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Animations Disponibles"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Cliquez sur une carte pour déclencher l'animation correspondante."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.string",value:`
        <div class="demo-animations-grid">
          <!-- Fade Animations -->
          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">fade-in</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="fade-in"></div>
            </div>
            <div class="anim-card__footer">
              
              <div class="anim-card__detail">
                <span class="anim-card__detail-label">Token</span>
                <code class="anim-card__code">--animation-fade-in</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">fade-out</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="fade-out"></div>
            </div>
            <div class="anim-card__footer">
              
              <div class="anim-card__detail">
                <span class="anim-card__detail-label">Token</span>
                <code class="anim-card__code">--animation-fade-out</code>
              </div>
            </div>
          </div>

          <!-- Scale Animations -->
          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">scale-up</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="scale-up"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-scale-up</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">scale-down</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="scale-down"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-scale-down</code>
              </div>
            </div>
          </div>

          <!-- Slide Out Animations -->
          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-out-up</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-out-up"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-out-up</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-out-down</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-out-down"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-out-down</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-out-left</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-out-left"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-out-left</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-out-right</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-out-right"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-out-right</code>
              </div>
            </div>
          </div>

          <!-- Slide In Animations -->
          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-in-up</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-in-up"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-in-up</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-in-down</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-in-down"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-in-down</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-in-left</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-in-left"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-in-left</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">slide-in-right</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="slide-in-right"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-slide-in-right</code>
              </div>
            </div>
          </div>

          <!-- Special Effects -->
          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">shake-x</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="shake-x"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-shake-x</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">shake-y</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="shake-y"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-shake-y</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">spin</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="spin"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-spin</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">bounce</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="bounce"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-bounce</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">pulse</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="pulse"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-pulse</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">float</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="float"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-float</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">ping</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="ping"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-ping</code>
              </div>
            </div>
          </div>

          <div class="anim-card">
            <div class="anim-card__header">
              <h3 class="anim-card__title">blink</h3>
            </div>
            <div class="anim-card__area">
              <div class="anim-card__box" data-animation="blink"></div>
            </div>
            <div class="anim-card__footer">
              <div class="anim-card__info">
                <div class="anim-card__label">
                  <span class="anim-card__label-key">Token:</span>
                </div>
                <code class="anim-card__code">--animation-blink</code>
              </div>
            </div>
          </div>
        </div>
      `},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Durations"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Durées de Transition"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Valeurs standardisées de timing pour les transitions UI."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.string",value:`
        <div class="demo-animations-grid">
          <div class="anim-card">
            <div class="anim-card__header"><h3 class="anim-card__title">instant</h3></div>
            <div class="anim-card__area"><div class="duration-circle duration-circle--instant"></div></div>
            <div class="anim-card__footer"><div class="anim-card__detail"><span class="anim-card__detail-label">0.1s</span><code class="anim-card__code">--duration-instant</code></div></div>
          </div>
          <div class="anim-card">
            <div class="anim-card__header"><h3 class="anim-card__title">fast</h3></div>
            <div class="anim-card__area"><div class="duration-circle duration-circle--fast"></div></div>
            <div class="anim-card__footer"><div class="anim-card__detail"><span class="anim-card__detail-label">0.15s</span><code class="anim-card__code">--duration-fast</code></div></div>
          </div>
          <div class="anim-card">
            <div class="anim-card__header"><h3 class="anim-card__title">normal</h3></div>
            <div class="anim-card__area"><div class="duration-circle duration-circle--normal"></div></div>
            <div class="anim-card__footer"><div class="anim-card__detail"><span class="anim-card__detail-label">0.3s</span><code class="anim-card__code">--duration-normal</code></div></div>
          </div>
          <div class="anim-card">
            <div class="anim-card__header"><h3 class="anim-card__title">slow</h3></div>
            <div class="anim-card__area"><div class="duration-circle duration-circle--slow"></div></div>
            <div class="anim-card__footer"><div class="anim-card__detail"><span class="anim-card__detail-label">0.5s</span><code class="anim-card__code">--duration-slow</code></div></div>
          </div>
          <div class="anim-card">
            <div class="anim-card__header"><h3 class="anim-card__title">slower</h3></div>
            <div class="anim-card__area"><div class="duration-circle duration-circle--slower"></div></div>
            <div class="anim-card__footer"><div class="anim-card__detail"><span class="anim-card__detail-label">0.75s</span><code class="anim-card__code">--duration-slower</code></div></div>
          </div>
          <div class="anim-card">
            <div class="anim-card__header"><h3 class="anim-card__title">slowest</h3></div>
            <div class="anim-card__area"><div class="duration-circle duration-circle--slowest"></div></div>
            <div class="anim-card__footer"><div class="anim-card__detail"><span class="anim-card__detail-label">1s</span><code class="anim-card__code">--duration-slowest</code></div></div>
          </div>
        </div>
      `},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Keyframes"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Keyframes Disponibles"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Séquences d'animation prédéfinies qui peuvent être combinées avec des durées et des fonctions de timing."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.string",value:`
        <div style="padding: var(--size-8); background: white;">
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--size-6);">
            <div style="padding: var(--size-4); background: var(--gray-100); border-radius: var(--radius-2);">
              <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600;">Opacity</h4>
              <ul style="margin: 0; padding-left: var(--size-4); list-style: disc;">
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">fade-in</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">fade-out</code></li>
              </ul>
            </div>
            <div style="padding: var(--size-4); background: var(--gray-100); border-radius: var(--radius-2);">
              <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600;">Scale</h4>
              <ul style="margin: 0; padding-left: var(--size-4); list-style: disc;">
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">scale-up</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">scale-down</code></li>
              </ul>
            </div>
            <div style="padding: var(--size-4); background: var(--gray-100); border-radius: var(--radius-2);">
              <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600;">Slide Out</h4>
              <ul style="margin: 0; padding-left: var(--size-4); list-style: disc;">
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-out-up</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-out-down</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-out-left</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-out-right</code></li>
              </ul>
            </div>
            <div style="padding: var(--size-4); background: var(--gray-100); border-radius: var(--radius-2);">
              <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600;">Slide In</h4>
              <ul style="margin: 0; padding-left: var(--size-4); list-style: disc;">
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-in-up</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-in-down</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-in-left</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">slide-in-right</code></li>
              </ul>
            </div>
            <div style="padding: var(--size-4); background: var(--gray-100); border-radius: var(--radius-2);">
              <h4 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-3); font-weight: 600;">Special Effects</h4>
              <ul style="margin: 0; padding-left: var(--size-4); list-style: disc;">
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">shake-x</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">shake-y</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">spin</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">bounce</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">pulse</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">float</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">ping</code></li>
                <li><code style="font-family: var(--font-mono); font-size: var(--font-size-0);">blink</code></li>
              </ul>
            </div>
          </div>
        </div>
      `},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:222,end:21473}},position:{start:222,end:21473}}],precompiled:!0});i.options.allowInlineIncludes=!0;try{let a=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),n(i.render({attributes:new r(a),...e}))}catch(a){return n("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/animations/animations.twig: "+a.toString())}},c={durations:{instant:"0.1s",fast:"0.15s",normal:"0.3s",slow:"0.5s",slower:"0.75s",slowest:"1s"},keyframes:[{name:"fade-in",description:"Opacity transition from 0% to 100%"},{name:"fade-out",description:"Opacity transition from 100% to 0%"},{name:"scale-up",description:"Scale transformation to 125%"},{name:"scale-down",description:"Scale transformation to 75%"},{name:"slide-out-up",description:"Translate Y to -100%"},{name:"slide-out-down",description:"Translate Y to 100%"},{name:"slide-out-left",description:"Translate X to -100%"},{name:"slide-out-right",description:"Translate X to 100%"},{name:"slide-in-up",description:"From Translate Y 100% to 0"},{name:"slide-in-down",description:"From Translate Y -100% to 0"},{name:"slide-in-left",description:"From Translate X -100% to 0"},{name:"slide-in-right",description:"From Translate X 100% to 0"},{name:"shake-x",description:"Horizontal shake effect"},{name:"shake-y",description:"Vertical shake effect"},{name:"spin",description:"Continuous rotation"},{name:"ping",description:"Expanding fade effect"},{name:"blink",description:"Opacity pulsing at 50%"},{name:"float",description:"Vertical floating motion"},{name:"bounce",description:"Bouncing motion"},{name:"pulse",description:"Scale pulse effect"}]},y={title:"Base/Animations"},m={name:"Animations",render:e=>d(e),args:{...c}},_=["Animations"];export{m as Animations,_ as __namedExportsOrder,y as default};
