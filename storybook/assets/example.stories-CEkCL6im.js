import{T as e,t as i}from"./iframe-B-yX16js.js";import{a,D as c}from"./twig-CgICq6Dc.js";import"./_base-story-Cp7prtAx.js";a(e);e.cache(!1);a(e);e.cache(!1);a(e);e.cache(!1);a(e);e.cache(!1);a(e);e.cache(!1);a(e);e.cache(!1);i.twig({id:"../_templates/swatch-grid.twig",data:[{type:"raw",value:`\r
\r
<div class="ps-swatch-grid" style="--columns: `,position:{start:389,end:439}},{type:"output",position:{start:439,end:463},stack:[{type:"Twig.expression.type.variable",value:"columns",match:["columns"],position:{start:439,end:463}},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],position:{start:439,end:463},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:439,end:463}},{type:"Twig.expression.type.number",value:4,match:["4",null],position:{start:439,end:463}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:439,end:463},expression:!1}]}]},{type:"raw",value:`">\r
  `,position:{start:463,end:469}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],position:{start:469,end:492},output:[{type:"raw",value:`\r
    <div class="ps-swatch-item">\r
      `,position:{start:492,end:534}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"preview"},{type:"Twig.expression.type.string",value:"color"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:534,end:566},output:[{type:"raw",value:`\r
        <div class="ps-swatch-preview ps-swatch-preview--color" style="background-color: var(`,position:{start:566,end:661}},{type:"output",position:{start:661,end:675},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:661,end:675}},{type:"Twig.expression.type.key.period",position:{start:661,end:675},key:"var"}]},{type:"raw",value:`)"></div>\r
      `,position:{start:675,end:692}}]},position:{open:{start:534,end:566},close:{start:692,end:703}}},{type:"raw",value:`\r
      \r
      `,position:{start:703,end:719}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"preview"},{type:"Twig.expression.type.string",value:"box"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:719,end:749},output:[{type:"raw",value:`\r
        <div class="ps-swatch-preview ps-swatch-preview--box" style="box-shadow: var(`,position:{start:749,end:836}},{type:"output",position:{start:836,end:850},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:836,end:850}},{type:"Twig.expression.type.key.period",position:{start:836,end:850},key:"var"}]},{type:"raw",value:`)"></div>\r
      `,position:{start:850,end:867}}]},position:{open:{start:719,end:749},close:{start:867,end:878}}},{type:"raw",value:`\r
      \r
      `,position:{start:878,end:894}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"preview"},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:894,end:925},output:[{type:"raw",value:`\r
        <div class="ps-swatch-preview ps-swatch-preview--text" style="font-size: var(`,position:{start:925,end:1012}},{type:"output",position:{start:1012,end:1026},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1012,end:1026}},{type:"Twig.expression.type.key.period",position:{start:1012,end:1026},key:"var"}]},{type:"raw",value:`)">Aa</div>\r
      `,position:{start:1026,end:1045}}]},position:{open:{start:894,end:925},close:{start:1045,end:1056}}},{type:"raw",value:`\r
      \r
      <div class="ps-swatch-info">\r
        <strong class="ps-swatch-name">`,position:{start:1056,end:1141}},{type:"output",position:{start:1141,end:1156},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1141,end:1156}},{type:"Twig.expression.type.key.period",position:{start:1141,end:1156},key:"name"}]},{type:"raw",value:`</strong>\r
        <code class="ps-swatch-token">`,position:{start:1156,end:1205}},{type:"output",position:{start:1205,end:1219},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1205,end:1219}},{type:"Twig.expression.type.key.period",position:{start:1205,end:1219},key:"var"}]},{type:"raw",value:`</code>\r
        `,position:{start:1219,end:1236}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"value"}],position:{start:1236,end:1255},output:[{type:"raw",value:`\r
          <span class="ps-swatch-value">`,position:{start:1255,end:1297}},{type:"output",position:{start:1297,end:1313},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1297,end:1313}},{type:"Twig.expression.type.key.period",position:{start:1297,end:1313},key:"value"}]},{type:"raw",value:`</span>\r
        `,position:{start:1313,end:1330}}]},position:{open:{start:1236,end:1255},close:{start:1330,end:1341}}},{type:"raw",value:`\r
      </div>\r
    </div>\r
  `,position:{start:1341,end:1371}}]},position:{open:{start:469,end:492},close:{start:1371,end:1383}}},{type:"raw",value:`\r
</div>\r
`,position:{start:1383,end:1383}}],precompiled:!0});i.twig({id:"../_templates/type-sample.twig",data:[{type:"raw",value:`\r
\r
<div class="ps-type-sample">\r
  <div class="ps-type-label">\r
    <code>`,position:{start:294,end:369}},{type:"output",position:{start:369,end:380},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:369,end:380}}]},{type:"raw",value:`</code>\r
  </div>\r
  \r
  <div class="ps-type-display">\r
    `,position:{start:380,end:440}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"h1"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:440,end:464},output:[{type:"raw",value:"<h1>",position:{start:464,end:468}},{type:"output",position:{start:468,end:478},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:468,end:478}}]},{type:"raw",value:"</h1>",position:{start:478,end:483}}]},position:{open:{start:440,end:464},close:{start:483,end:494}}},{type:"raw",value:`\r
    `,position:{start:494,end:500}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"h2"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:500,end:524},output:[{type:"raw",value:"<h2>",position:{start:524,end:528}},{type:"output",position:{start:528,end:538},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:528,end:538}}]},{type:"raw",value:"</h2>",position:{start:538,end:543}}]},position:{open:{start:500,end:524},close:{start:543,end:554}}},{type:"raw",value:`\r
    `,position:{start:554,end:560}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"h3"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:560,end:584},output:[{type:"raw",value:"<h3>",position:{start:584,end:588}},{type:"output",position:{start:588,end:598},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:588,end:598}}]},{type:"raw",value:"</h3>",position:{start:598,end:603}}]},position:{open:{start:560,end:584},close:{start:603,end:614}}},{type:"raw",value:`\r
    `,position:{start:614,end:620}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"h4"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:620,end:644},output:[{type:"raw",value:"<h4>",position:{start:644,end:648}},{type:"output",position:{start:648,end:658},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:648,end:658}}]},{type:"raw",value:"</h4>",position:{start:658,end:663}}]},position:{open:{start:620,end:644},close:{start:663,end:674}}},{type:"raw",value:`\r
    `,position:{start:674,end:680}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"h5"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:680,end:704},output:[{type:"raw",value:"<h5>",position:{start:704,end:708}},{type:"output",position:{start:708,end:718},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:708,end:718}}]},{type:"raw",value:"</h5>",position:{start:718,end:723}}]},position:{open:{start:680,end:704},close:{start:723,end:734}}},{type:"raw",value:`\r
    `,position:{start:734,end:740}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"h6"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:740,end:764},output:[{type:"raw",value:"<h6>",position:{start:764,end:768}},{type:"output",position:{start:768,end:778},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:768,end:778}}]},{type:"raw",value:"</h6>",position:{start:778,end:783}}]},position:{open:{start:740,end:764},close:{start:783,end:794}}},{type:"raw",value:`\r
    `,position:{start:794,end:800}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"p"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:800,end:823},output:[{type:"raw",value:"<p>",position:{start:823,end:826}},{type:"output",position:{start:826,end:836},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:826,end:836}}]},{type:"raw",value:"</p>",position:{start:836,end:840}}]},position:{open:{start:800,end:823},close:{start:840,end:851}}},{type:"raw",value:`\r
    `,position:{start:851,end:857}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"element",match:["element"]},{type:"Twig.expression.type.string",value:"div"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:857,end:882},output:[{type:"raw",value:'<div class="',position:{start:882,end:894}},{type:"output",position:{start:894,end:905},stack:[{type:"Twig.expression.type.variable",value:"class",match:["class"],position:{start:894,end:905}}]},{type:"raw",value:'">',position:{start:905,end:907}},{type:"output",position:{start:907,end:917},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:907,end:917}}]},{type:"raw",value:"</div>",position:{start:917,end:923}}]},position:{open:{start:857,end:882},close:{start:923,end:934}}},{type:"raw",value:`\r
  </div>\r
  \r
  `,position:{start:934,end:952}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"specs",match:["specs"]}],position:{start:952,end:966},output:[{type:"raw",value:`\r
    <small class="ps-type-specs">`,position:{start:966,end:1001}},{type:"output",position:{start:1001,end:1012},stack:[{type:"Twig.expression.type.variable",value:"specs",match:["specs"],position:{start:1001,end:1012}}]},{type:"raw",value:`</small>\r
  `,position:{start:1012,end:1024}}]},position:{open:{start:952,end:966},close:{start:1024,end:1035}}},{type:"raw",value:`\r
</div>\r
`,position:{start:1035,end:1035}}],precompiled:!0});i.twig({id:"../_templates/token-table.twig",data:[{type:"raw",value:`\r
\r
<div class="ps-token-table-wrapper">\r
  <table class="ps-token-table">\r
    <thead>\r
      <tr>\r
        `,position:{start:460,end:569}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"header",expression:[{type:"Twig.expression.type.variable",value:"headers",match:["headers"]}],position:{start:569,end:596},output:[{type:"raw",value:`\r
          <th `,position:{start:596,end:612}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.key.period",key:"width"}],position:{start:612,end:633},output:[{type:"raw",value:'style="width: ',position:{start:633,end:647}},{type:"output",position:{start:647,end:665},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:647,end:665}},{type:"Twig.expression.type.key.period",position:{start:647,end:665},key:"width"}]},{type:"raw",value:'"',position:{start:665,end:666}}]},position:{open:{start:612,end:633},close:{start:666,end:677}}},{type:"raw",value:`>\r
            `,position:{start:677,end:692}},{type:"output",position:{start:692,end:710},stack:[{type:"Twig.expression.type.variable",value:"header",match:["header"],position:{start:692,end:710}},{type:"Twig.expression.type.key.period",position:{start:692,end:710},key:"label"}]},{type:"raw",value:`\r
          </th>\r
        `,position:{start:710,end:737}}]},position:{open:{start:569,end:596},close:{start:737,end:749}}},{type:"raw",value:`\r
      </tr>\r
    </thead>\r
    <tbody>\r
      `,position:{start:749,end:797}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"row",expression:[{type:"Twig.expression.type.variable",value:"rows",match:["rows"]}],position:{start:797,end:818},output:[{type:"raw",value:`\r
        <tr>\r
          `,position:{start:818,end:844}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"]},{type:"Twig.expression.type.key.period",key:"token"}],position:{start:844,end:862},output:[{type:"raw",value:`\r
            <td><code class="ps-token-name">`,position:{start:862,end:908}},{type:"output",position:{start:908,end:923},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:908,end:923}},{type:"Twig.expression.type.key.period",position:{start:908,end:923},key:"token"}]},{type:"raw",value:`</code></td>\r
          `,position:{start:923,end:947}}]},position:{open:{start:844,end:862},close:{start:947,end:958}}},{type:"raw",value:`\r
          `,position:{start:958,end:970}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"]},{type:"Twig.expression.type.key.period",key:"value"}],position:{start:970,end:988},output:[{type:"raw",value:`\r
            <td><code class="ps-token-value">`,position:{start:988,end:1035}},{type:"output",position:{start:1035,end:1050},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:1035,end:1050}},{type:"Twig.expression.type.key.period",position:{start:1035,end:1050},key:"value"}]},{type:"raw",value:`</code></td>\r
          `,position:{start:1050,end:1074}}]},position:{open:{start:970,end:988},close:{start:1074,end:1085}}},{type:"raw",value:`\r
          `,position:{start:1085,end:1097}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"]},{type:"Twig.expression.type.key.period",key:"usage"}],position:{start:1097,end:1115},output:[{type:"raw",value:`\r
            <td class="ps-token-usage">`,position:{start:1115,end:1156}},{type:"output",position:{start:1156,end:1171},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:1156,end:1171}},{type:"Twig.expression.type.key.period",position:{start:1156,end:1171},key:"usage"}]},{type:"raw",value:`</td>\r
          `,position:{start:1171,end:1188}}]},position:{open:{start:1097,end:1115},close:{start:1188,end:1199}}},{type:"raw",value:`\r
          `,position:{start:1199,end:1211}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"]},{type:"Twig.expression.type.key.period",key:"swatch"}],position:{start:1211,end:1230},output:[{type:"raw",value:`\r
            <td>\r
              <div class="ps-token-swatch" style="background-color: var(`,position:{start:1230,end:1322}},{type:"output",position:{start:1322,end:1337},stack:[{type:"Twig.expression.type.variable",value:"row",match:["row"],position:{start:1322,end:1337}},{type:"Twig.expression.type.key.period",position:{start:1322,end:1337},key:"token"}]},{type:"raw",value:`)"></div>\r
            </td>\r
          `,position:{start:1337,end:1377}}]},position:{open:{start:1211,end:1230},close:{start:1377,end:1388}}},{type:"raw",value:`\r
        </tr>\r
      `,position:{start:1388,end:1411}}]},position:{open:{start:797,end:818},close:{start:1411,end:1423}}},{type:"raw",value:`\r
    </tbody>\r
  </table>\r
</div>\r
`,position:{start:1423,end:1423}}],precompiled:!0});i.twig({id:"../_templates/color-column.twig",data:[{type:"raw",value:`\r
\r
<div class="ps-color-column">\r
  `,position:{start:405,end:442}},{type:"raw",value:`\r
  <div class="ps-color-main" style="background-color: var(`,position:{start:465,end:525}},{type:"output",position:{start:525,end:539},stack:[{type:"Twig.expression.type.variable",value:"base_var",match:["base_var"],position:{start:525,end:539}}]},{type:"raw",value:`)">\r
    <strong class="ps-color-title">`,position:{start:539,end:579}},{type:"output",position:{start:579,end:590},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:579,end:590}}]},{type:"raw",value:`</strong>\r
    <code class="ps-color-token">`,position:{start:590,end:634}},{type:"output",position:{start:634,end:648},stack:[{type:"Twig.expression.type.variable",value:"base_var",match:["base_var"],position:{start:634,end:648}}]},{type:"raw",value:`</code>\r
    <span class="ps-color-value">`,position:{start:648,end:690}},{type:"output",position:{start:690,end:706},stack:[{type:"Twig.expression.type.variable",value:"base_value",match:["base_value"],position:{start:690,end:706}}]},{type:"raw",value:`</span>\r
  </div>\r
  \r
  `,position:{start:706,end:731}},{type:"raw",value:`\r
  <div class="ps-color-shades">\r
    `,position:{start:751,end:790}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"shade",expression:[{type:"Twig.expression.type.variable",value:"shades",match:["shades"]}],position:{start:790,end:815},output:[{type:"raw",value:`\r
      `,position:{start:815,end:823}},{type:"logic",token:{type:"Twig.logic.type.set",key:"is_light",expression:[{type:"Twig.expression.type.variable",value:"shade",match:["shade"]},{type:"Twig.expression.type.key.period",key:"index"},{type:"Twig.expression.type.number",value:3,match:["3",null]},{type:"Twig.expression.type.operator.binary",value:"<=",precidence:8,associativity:"leftToRight",operator:"<="}],position:{start:823,end:860}},position:{start:823,end:860}},{type:"raw",value:`\r
      <div class="ps-color-shade `,position:{start:860,end:895}},{type:"output",position:{start:895,end:934},stack:[{type:"Twig.expression.type.variable",value:"is_light",match:["is_light"],position:{start:895,end:934}},{type:"Twig.expression.type.string",value:"is-light",position:{start:895,end:934}},{type:"Twig.expression.type.string",value:"is-dark",position:{start:895,end:934}},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:895,end:934},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:'" style="background-color: var(',position:{start:934,end:965}},{type:"output",position:{start:965,end:980},stack:[{type:"Twig.expression.type.variable",value:"shade",match:["shade"],position:{start:965,end:980}},{type:"Twig.expression.type.key.period",position:{start:965,end:980},key:"var"}]},{type:"raw",value:`)">\r
        <code class="ps-shade-token">`,position:{start:980,end:1022}},{type:"output",position:{start:1022,end:1037},stack:[{type:"Twig.expression.type.variable",value:"shade",match:["shade"],position:{start:1022,end:1037}},{type:"Twig.expression.type.key.period",position:{start:1022,end:1037},key:"var"}]},{type:"raw",value:`</code>\r
      </div>\r
    `,position:{start:1037,end:1064}}]},position:{open:{start:790,end:815},close:{start:1064,end:1076}}},{type:"raw",value:`\r
  </div>\r
</div>\r
`,position:{start:1076,end:1076}}],precompiled:!0});i.twig({id:"../_templates/color-cards.twig",data:[{type:"raw",value:`\r
\r
<div class="ps-color-cards">\r
  `,position:{start:338,end:374}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"color",expression:[{type:"Twig.expression.type.variable",value:"colors",match:["colors"]}],position:{start:374,end:399},output:[{type:"raw",value:`\r
    <div class="ps-color-card ps-color-card--`,position:{start:399,end:446}},{type:"output",position:{start:446,end:462},stack:[{type:"Twig.expression.type.variable",value:"color",match:["color"],position:{start:446,end:462}},{type:"Twig.expression.type.key.period",position:{start:446,end:462},key:"slug"}]},{type:"raw",value:`">\r
      <span class="ps-card-name">`,position:{start:462,end:499}},{type:"output",position:{start:499,end:515},stack:[{type:"Twig.expression.type.variable",value:"color",match:["color"],position:{start:499,end:515}},{type:"Twig.expression.type.key.period",position:{start:499,end:515},key:"name"}]},{type:"raw",value:`</span>\r
      `,position:{start:515,end:530}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"color",match:["color"]},{type:"Twig.expression.type.key.period",key:"var"}],position:{start:530,end:548},output:[{type:"raw",value:`\r
        <code class="ps-card-token">`,position:{start:548,end:586}},{type:"output",position:{start:586,end:601},stack:[{type:"Twig.expression.type.variable",value:"color",match:["color"],position:{start:586,end:601}},{type:"Twig.expression.type.key.period",position:{start:586,end:601},key:"var"}]},{type:"raw",value:`</code>\r
      `,position:{start:601,end:616}}]},position:{open:{start:530,end:548},close:{start:616,end:627}}},{type:"raw",value:`\r
    </div>\r
  `,position:{start:627,end:643}}]},position:{open:{start:374,end:399},close:{start:643,end:655}}},{type:"raw",value:`\r
</div>\r
`,position:{start:655,end:655}}],precompiled:!0});i.twig({id:"../_base-story.twig",data:[{type:"raw",value:`\r
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
`,position:{start:2872,end:2872}}],precompiled:!0});const p=t=>t,d=(t={})=>{const o=i.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/example/example.twig",data:[{type:"raw",value:`\r
\r
`,position:{start:383,end:387}},{type:"raw",value:`\r
`,position:{start:437,end:439}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_base-story.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"header"},{type:"Twig.expression.type.variable",value:"header",match:["header"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"sections"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Component 1"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Color Cards"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Large semantic color cards showing theme colors. Each card displays a semantic color used throughout the system."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"color_cards_content",match:["color_cards_content"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Component 2"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Color Palettes"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Vertical color scales showing tint/shade progression from 50 to 900. Click to inspect color values."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"color_palettes_content",match:["color_palettes_content"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Component 3"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Token Reference Table"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Complete reference table of all design tokens with values and usage descriptions."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"token_table_content",match:["token_table_content"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Component 4"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Typography Specimens"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Font samples showing heading and body text styles with responsive sizing information."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"typography_content",match:["typography_content"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"badge"},{type:"Twig.expression.type.string",value:"Component 5"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.string",value:"Spacing Scale"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"description"},{type:"Twig.expression.type.string",value:"Visual demonstration of spacing tokens (size scale) used for margins, paddings, and gaps."},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"content"},{type:"Twig.expression.type.variable",value:"spacing_content",match:["spacing_content"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:439,end:1680}},position:{start:439,end:1680}},{type:"raw",value:`\r
\r
`,position:{start:1680,end:1684}},{type:"raw",value:`\r
`,position:{start:1820,end:1822}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"color_cards_content",position:{start:1822,end:1851},output:[{type:"raw",value:`\r
  `,position:{start:1851,end:1855}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_templates/color-cards.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"colors"},{type:"Twig.expression.type.variable",value:"semantic_colors",match:["semantic_colors"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:1855,end:1947}},position:{start:1855,end:1947}},{type:"raw",value:`\r
`,position:{start:1947,end:1949}}]},position:{open:{start:1822,end:1851},close:{start:1949,end:1961}}},{type:"raw",value:`\r
\r
`,position:{start:1961,end:1965}},{type:"raw",value:`\r
`,position:{start:2120,end:2122}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"color_palettes_content",position:{start:2122,end:2154},output:[{type:"raw",value:`\r
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--size-5)">\r
    `,position:{start:2154,end:2272}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:"key",valueVar:"palette",expression:[{type:"Twig.expression.type.variable",value:"color_palettes",match:["color_palettes"]}],position:{start:2272,end:2312},output:[{type:"raw",value:`\r
      `,position:{start:2312,end:2320}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_templates/color-column.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"title"},{type:"Twig.expression.type.variable",value:"palette",match:["palette"]},{type:"Twig.expression.type.key.period",key:"title"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"base_var"},{type:"Twig.expression.type.variable",value:"palette",match:["palette"]},{type:"Twig.expression.type.key.period",key:"base_var"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"base_value"},{type:"Twig.expression.type.variable",value:"palette",match:["palette"]},{type:"Twig.expression.type.key.period",key:"base_value"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"shades"},{type:"Twig.expression.type.variable",value:"palette",match:["palette"]},{type:"Twig.expression.type.key.period",key:"shades"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:2320,end:2529}},position:{start:2320,end:2529}},{type:"raw",value:`\r
    `,position:{start:2529,end:2535}}]},position:{open:{start:2272,end:2312},close:{start:2535,end:2547}}},{type:"raw",value:`\r
  </div>\r
`,position:{start:2547,end:2559}}]},position:{open:{start:2122,end:2154},close:{start:2559,end:2571}}},{type:"raw",value:`\r
\r
`,position:{start:2571,end:2575}},{type:"raw",value:`\r
`,position:{start:2711,end:2713}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"token_table_content",position:{start:2713,end:2742},output:[{type:"raw",value:`\r
  `,position:{start:2742,end:2746}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_templates/token-table.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"headers"},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Token"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"width"},{type:"Twig.expression.type.string",value:"25%"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Value"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"width"},{type:"Twig.expression.type.string",value:"20%"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.string",value:"Usage"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"width"},{type:"Twig.expression.type.string",value:"55%"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"rows"},{type:"Twig.expression.type.variable",value:"tokens",match:["tokens"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:2746,end:2973}},position:{start:2746,end:2973}},{type:"raw",value:`\r
`,position:{start:2973,end:2975}}]},position:{open:{start:2713,end:2742},close:{start:2975,end:2987}}},{type:"raw",value:`\r
\r
`,position:{start:2987,end:2991}},{type:"raw",value:`\r
`,position:{start:3134,end:3136}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"typography_content",position:{start:3136,end:3164},output:[{type:"raw",value:`\r
  <div style="display: grid; gap: var(--size-6)">\r
    `,position:{start:3164,end:3221}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"sample",expression:[{type:"Twig.expression.type.variable",value:"typography",match:["typography"]}],position:{start:3221,end:3251},output:[{type:"raw",value:`\r
      `,position:{start:3251,end:3259}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_templates/type-sample.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"label"},{type:"Twig.expression.type.variable",value:"sample",match:["sample"]},{type:"Twig.expression.type.key.period",key:"label"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"element"},{type:"Twig.expression.type.variable",value:"sample",match:["sample"]},{type:"Twig.expression.type.key.period",key:"element"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"text"},{type:"Twig.expression.type.variable",value:"sample",match:["sample"]},{type:"Twig.expression.type.key.period",key:"text"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"specs"},{type:"Twig.expression.type.variable",value:"sample",match:["sample"]},{type:"Twig.expression.type.key.period",key:"specs"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:3259,end:3447}},position:{start:3259,end:3447}},{type:"raw",value:`\r
    `,position:{start:3447,end:3453}}]},position:{open:{start:3221,end:3251},close:{start:3453,end:3465}}},{type:"raw",value:`\r
  </div>\r
`,position:{start:3465,end:3477}}]},position:{open:{start:3136,end:3164},close:{start:3477,end:3489}}},{type:"raw",value:`\r
\r
`,position:{start:3489,end:3493}},{type:"raw",value:`\r
`,position:{start:3639,end:3641}},{type:"logic",token:{type:"Twig.logic.type.setcapture",key:"spacing_content",position:{start:3641,end:3666},output:[{type:"raw",value:`\r
  `,position:{start:3666,end:3670}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"../_templates/swatch-grid.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"items"},{type:"Twig.expression.type.variable",value:"spacing_tokens",match:["spacing_tokens"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"columns"},{type:"Twig.expression.type.number",value:4,match:["4",null]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:3670,end:3777}},position:{start:3670,end:3777}},{type:"raw",value:`\r
`,position:{start:3777,end:3779}}]},position:{open:{start:3641,end:3666},close:{start:3779,end:3791}}},{type:"raw",value:`\r
\r
`,position:{start:3791,end:3795}},{type:"raw",value:`\r
<link rel="stylesheet" href="../_templates/components.css">\r
`,position:{start:3930,end:3930}}],precompiled:!0});o.options.allowInlineIncludes=!0;try{let r=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(r)||(r=Object.entries(r)),p(o.render({attributes:new c(r),...t}))}catch(r){return p("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/example/example.twig: "+r.toString())}},g={header:{title:"Design System Tokens",description:"Comprehensive reference for BNP Paribas Real Estate design tokens. This system ensures consistency across all digital properties through carefully curated color palettes, typography scales, and spacing systems.",badge:"Design System",meta:[{label:"Total Tokens",value:"97+"},{label:"Color Palette",value:"9 semantic + neutrals"},{label:"Last Updated",value:"Dec 7, 2025"}]},color_palettes:{primary:{title:"Primary Green",base_var:"--primary-600",base_value:"#00915A",shades:[{var:"--primary-50",index:1},{var:"--primary-100",index:2},{var:"--primary-200",index:3},{var:"--primary-300",index:4},{var:"--primary-400",index:5},{var:"--primary-500",index:6},{var:"--primary-700",index:7},{var:"--primary-800",index:8},{var:"--primary-900",index:9}]},secondary:{title:"Secondary Pink",base_var:"--secondary-600",base_value:"#E63888",shades:[{var:"--secondary-50",index:1},{var:"--secondary-100",index:2},{var:"--secondary-200",index:3},{var:"--secondary-300",index:4},{var:"--secondary-400",index:5},{var:"--secondary-500",index:6},{var:"--secondary-700",index:7},{var:"--secondary-800",index:8},{var:"--secondary-900",index:9}]},gold:{title:"Premium Gold",base_var:"--gold-600",base_value:"#D1AE6E",shades:[{var:"--gold-50",index:1},{var:"--gold-100",index:2},{var:"--gold-200",index:3},{var:"--gold-300",index:4},{var:"--gold-400",index:5},{var:"--gold-500",index:6},{var:"--gold-700",index:7},{var:"--gold-800",index:8},{var:"--gold-900",index:9}]}},semantic_colors:[{name:"Primary",slug:"primary",var:"--primary",description:"Brand identity & main actions"},{name:"Secondary",slug:"secondary",var:"--secondary",description:"Accent colors & highlights"},{name:"Success",slug:"success",var:"--success",description:"Positive feedback & confirmations"},{name:"Danger",slug:"danger",var:"--danger",description:"Errors & destructive actions"},{name:"Warning",slug:"warning",var:"--warning",description:"Caution & important alerts"},{name:"Info",slug:"info",var:"--info",description:"Informational messages"},{name:"Gold",slug:"gold",var:"--gold",description:"Premium & VIP features"},{name:"Light",slug:"light",var:"--light",description:"Subtle & muted backgrounds"},{name:"Dark",slug:"dark",var:"--dark",description:"High contrast & emphasis"}],tokens:[{token:"--primary",value:"#00915A",usage:"Primary actions, links, brand identity"},{token:"--secondary",value:"#E63888",usage:"Secondary actions, accents, highlights"},{token:"--success",value:"#00ADB5",usage:"Success states, positive confirmations"},{token:"--danger",value:"#DC3545",usage:"Error states, destructive actions"},{token:"--warning",value:"#FFC107",usage:"Warning alerts, cautions"},{token:"--info",value:"#17A2B8",usage:"Information messages, tooltips"},{token:"--gold",value:"#D1AE6E",usage:"Premium features, VIP content"},{token:"--light",value:"#F8F9FA",usage:"Subtle backgrounds, disabled states"},{token:"--dark",value:"#212529",usage:"High contrast text, emphasis"}],typography:[{label:"h1 — Page Title",element:"h1",text:"Welcome to BNP Paribas Real Estate",specs:"48px / 60px • Bold 700 • Desktop only"},{label:"h2 — Section Heading",element:"h2",text:"Discover Our Premium Properties",specs:"44px / 44px • Bold 700 • Responsive"},{label:"h3 — Subsection",element:"h3",text:"Featured Listings in Paris",specs:"32px / 40px • Bold 700 • Mobile: 28px"},{label:"p — Body Text",element:"p",text:"Explore a curated selection of luxury real estate properties across Europe. Our team of experts ensures every property meets the highest standards of quality and location.",specs:"16px / 24px • Regular 400 • Optimized for readability"}],spacing_tokens:[{name:"XS",var:"--size-1",preview:"text",value:"4px"},{name:"S",var:"--size-2",preview:"text",value:"8px"},{name:"SM",var:"--size-3",preview:"text",value:"12px"},{name:"MD",var:"--size-4",preview:"text",value:"16px"},{name:"L",var:"--size-5",preview:"text",value:"20px"},{name:"XL",var:"--size-6",preview:"text",value:"24px"},{name:"2XL",var:"--size-8",preview:"text",value:"32px"},{name:"3XL",var:"--size-10",preview:"text",value:"40px"}]},m={title:"Base/Example"},s={render:t=>d(t),args:g};var n,y,l;s.parameters={...s.parameters,docs:{...(n=s.parameters)==null?void 0:n.docs,source:{originalSource:`{
  render: args => exampleTwig(args),
  args: data
}`,...(l=(y=s.parameters)==null?void 0:y.docs)==null?void 0:l.source}}};const x=["Example"];export{s as Example,x as __namedExportsOrder,m as default};
