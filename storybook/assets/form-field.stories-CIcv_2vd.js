import{t as n,T as R}from"./iframe-CnHaBuCA.js";import{D as L,a as j}from"./twig-Dp8duUs-.js";import"./field-B0JZ6TJA.js";import"./label-BUNf4--_.js";j(R);R.cache(!1);n.twig({id:"@elements/label/label.twig",data:[{type:"raw",value:"",position:{start:444,end:446}},{type:"logic",token:{type:"Twig.logic.type.set",key:"baseClass",expression:[{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"ps-label"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:446,end:499}},position:{start:446,end:499}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"required",match:["required"]},{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.string",value:"--required"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.string",value:"--disabled"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:500,end:629}},position:{start:500,end:629}},{type:"raw",value:"<label",position:{start:630,end:637}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:637,end:656},output:[{type:"raw",value:" ",position:{start:656,end:657}},{type:"output",position:{start:657,end:691},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:657,end:691}},{type:"Twig.expression.type.key.period",position:{start:657,end:691},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:657,end:691},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:657,end:691}}]}]}]},position:{open:{start:637,end:656},close:{start:691,end:701}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:691,end:701},output:[{type:"raw",value:' class="',position:{start:701,end:709}},{type:"output",position:{start:709,end:737},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:709,end:737}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:709,end:737},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:709,end:737}},{type:"Twig.expression.type.string",value:" ",position:{start:709,end:737}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:709,end:737},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:709,end:737}}]},{type:"raw",value:'"',position:{start:737,end:738}}]},position:{open:{start:691,end:701},close:{start:738,end:749}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"forId",match:["forId"]}],position:{start:749,end:763},output:[{type:"raw",value:' for="',position:{start:763,end:769}},{type:"output",position:{start:769,end:780},stack:[{type:"Twig.expression.type.variable",value:"forId",match:["forId"],position:{start:769,end:780}}]},{type:"raw",value:'"',position:{start:780,end:781}}]},position:{open:{start:749,end:763},close:{start:781,end:792}}},{type:"raw",value:`>
  <span class="`,position:{start:792,end:809}},{type:"output",position:{start:809,end:824},stack:[{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"],position:{start:809,end:824}}]},{type:"raw",value:'__text">',position:{start:824,end:832}},{type:"output",position:{start:832,end:842},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:832,end:842}}]},{type:"raw",value:`</span>
  `,position:{start:842,end:852}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"required",match:["required"]}],position:{start:852,end:869},output:[{type:"raw",value:'    <span class="',position:{start:870,end:887}},{type:"output",position:{start:887,end:902},stack:[{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"],position:{start:887,end:902}}]},{type:"raw",value:`__required" aria-hidden="true">*</span>
    <span class="visually-hidden">(required field)</span>
  `,position:{start:902,end:1002}}]},position:{open:{start:852,end:869},close:{start:1002,end:1013}}},{type:"raw",value:"</label>",position:{start:1014,end:1014}}],precompiled:!0});n.twig({id:"@elements/field/field.twig",data:[{type:"raw",value:"",position:{start:763,end:765}},{type:"logic",token:{type:"Twig.logic.type.set",key:"type",expression:[{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:765,end:804}},position:{start:765,end:804}},{type:"logic",token:{type:"Twig.logic.type.set",key:"value",expression:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:805,end:842}},position:{start:805,end:842}},{type:"logic",token:{type:"Twig.logic.type.set",key:"placeholder",expression:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:843,end:892}},position:{start:843,end:892}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:893,end:939}},position:{start:893,end:939}},{type:"logic",token:{type:"Twig.logic.type.set",key:"error",expression:[{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:940,end:977}},position:{start:940,end:977}},{type:"logic",token:{type:"Twig.logic.type.set",key:"done",expression:[{type:"Twig.expression.type.variable",value:"done",match:["done"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:978,end:1016}},position:{start:978,end:1016}},{type:"logic",token:{type:"Twig.logic.type.set",key:"icon",expression:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1017,end:1052}},position:{start:1017,end:1052}},{type:"logic",token:{type:"Twig.logic.type.set",key:"iconPosition",expression:[{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"right"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1053,end:1109}},position:{start:1053,end:1109}},{type:"logic",token:{type:"Twig.logic.type.set",key:"attributes",expression:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1110,end:1173}},position:{start:1110,end:1173}},{type:"raw",value:"",position:{start:1174,end:1175}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-field"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.string",value:"ps-field--"},{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.string",value:"ps-field--error"},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"done",match:["done"]},{type:"Twig.expression.type.string",value:"ps-field--done"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.string",value:"ps-field--disabled"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.string",value:"ps-field--filled"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.string",value:"ps-field--icon-"},{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1175,end:1453}},position:{start:1175,end:1453}},{type:"raw",value:"<div ",position:{start:1454,end:1460}},{type:"output",position:{start:1460,end:1494},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:1460,end:1494}},{type:"Twig.expression.type.key.period",position:{start:1460,end:1494},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:1460,end:1494},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:1460,end:1494}}]}]},{type:"raw",value:">",position:{start:1494,end:1498}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.string",value:"left"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:1498,end:1540},output:[{type:"raw",value:'<span class="ps-field__icon ps-field__icon--left" data-icon="',position:{start:1541,end:1606}},{type:"output",position:{start:1606,end:1616},stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"],position:{start:1606,end:1616}}]},{type:"raw",value:'" aria-hidden="true"></span>',position:{start:1616,end:1647}}]},position:{open:{start:1498,end:1540},close:{start:1647,end:1660}}},{type:"raw",value:"",position:{start:1661,end:1664}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.string",value:"textarea"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1664,end:1693},output:[{type:"raw",value:`<textarea
      class="ps-field__input"
      placeholder="`,position:{start:1694,end:1757}},{type:"output",position:{start:1757,end:1774},stack:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:1757,end:1774}}]},{type:"raw",value:`"
      `,position:{start:1774,end:1782}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1782,end:1799},output:[{type:"raw",value:'disabled aria-disabled="true"',position:{start:1799,end:1828}}]},position:{open:{start:1782,end:1799},close:{start:1828,end:1839}}},{type:"raw",value:"      ",position:{start:1840,end:1846}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:1846,end:1860},output:[{type:"raw",value:'aria-invalid="true" aria-describedby="field-error"',position:{start:1860,end:1910}}]},position:{open:{start:1846,end:1860},close:{start:1910,end:1921}}},{type:"raw",value:"    >",position:{start:1922,end:1927}},{type:"output",position:{start:1927,end:1938},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:1927,end:1938}}]},{type:"raw",value:"</textarea>",position:{start:1938,end:1952}}]},position:{open:{start:1664,end:1693},close:{start:1952,end:1983}}},{type:"logic",token:{type:"Twig.logic.type.elseif",stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.string",value:"select"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1952,end:1983},output:[{type:"raw",value:`<div
      class="ps-field__input"
      role="combobox"
      aria-expanded="false"
      aria-haspopup="listbox"
      `,position:{start:1984,end:2109}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:2109,end:2126},output:[{type:"raw",value:'aria-disabled="true"',position:{start:2126,end:2146}}]},position:{open:{start:2109,end:2126},close:{start:2146,end:2157}}},{type:"raw",value:"      ",position:{start:2158,end:2164}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2164,end:2178},output:[{type:"raw",value:'aria-invalid="true" aria-describedby="field-error"',position:{start:2178,end:2228}}]},position:{open:{start:2164,end:2178},close:{start:2228,end:2239}}},{type:"raw",value:"    >",position:{start:2240,end:2245}},{type:"output",position:{start:2245,end:2277},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2245,end:2277}},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],position:{start:2245,end:2277},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2245,end:2277}},{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:2245,end:2277}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2245,end:2277},expression:!1}]}]},{type:"raw",value:"</div>",position:{start:2277,end:2286}}]},position:{open:{start:1952,end:1983},close:{start:2286,end:2298}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2286,end:2298},output:[{type:"raw",value:`<input
      class="ps-field__input"
      type="`,position:{start:2299,end:2352}},{type:"output",position:{start:2352,end:2362},stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"],position:{start:2352,end:2362}}]},{type:"raw",value:`"
      value="`,position:{start:2362,end:2377}},{type:"output",position:{start:2377,end:2388},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2377,end:2388}}]},{type:"raw",value:`"
      placeholder="`,position:{start:2388,end:2409}},{type:"output",position:{start:2409,end:2426},stack:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:2409,end:2426}}]},{type:"raw",value:`"
      `,position:{start:2426,end:2434}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:2434,end:2451},output:[{type:"raw",value:'disabled aria-disabled="true"',position:{start:2451,end:2480}}]},position:{open:{start:2434,end:2451},close:{start:2480,end:2491}}},{type:"raw",value:"      ",position:{start:2492,end:2498}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2498,end:2512},output:[{type:"raw",value:'aria-invalid="true" aria-describedby="field-error"',position:{start:2512,end:2562}}]},position:{open:{start:2498,end:2512},close:{start:2562,end:2573}}},{type:"raw",value:"    />",position:{start:2574,end:2583}}]},position:{open:{start:2286,end:2298},close:{start:2583,end:2596}}},{type:"raw",value:"",position:{start:2597,end:2600}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.string",value:"right"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:2600,end:2643},output:[{type:"raw",value:'<span class="ps-field__icon ps-field__icon--right" data-icon="',position:{start:2644,end:2710}},{type:"output",position:{start:2710,end:2720},stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"],position:{start:2710,end:2720}}]},{type:"raw",value:'" aria-hidden="true"></span>',position:{start:2720,end:2751}}]},position:{open:{start:2600,end:2643},close:{start:2751,end:2764}}},{type:"raw",value:"",position:{start:2765,end:2768}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2768,end:2784},output:[{type:"raw",value:'<div class="ps-field__error" id="field-error" role="alert">',position:{start:2785,end:2848}},{type:"output",position:{start:2848,end:2859},stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"],position:{start:2848,end:2859}}]},{type:"raw",value:"</div>",position:{start:2859,end:2868}}]},position:{open:{start:2768,end:2784},close:{start:2868,end:2881}}},{type:"raw",value:"</div>",position:{start:2882,end:2882}}],precompiled:!0});const y=t=>t,e=(t={})=>{const l=n.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/form-field/form-field.twig",data:[{type:"raw",value:"",position:{start:1060,end:1064}},{type:"logic",token:{type:"Twig.logic.type.set",key:"label",expression:[{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1064,end:1101}},position:{start:1064,end:1101}},{type:"raw",value:"",position:{start:1101,end:1103}},{type:"logic",token:{type:"Twig.logic.type.set",key:"id",expression:[{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"field-"},{type:"Twig.expression.type._function",fn:"random",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1103,end:1151}},position:{start:1103,end:1151}},{type:"raw",value:"",position:{start:1151,end:1153}},{type:"logic",token:{type:"Twig.logic.type.set",key:"field",expression:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1153,end:1190}},position:{start:1153,end:1190}},{type:"raw",value:"",position:{start:1190,end:1192}},{type:"logic",token:{type:"Twig.logic.type.set",key:"helperText",expression:[{type:"Twig.expression.type.variable",value:"helperText",match:["helperText"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1192,end:1239}},position:{start:1192,end:1239}},{type:"raw",value:"",position:{start:1239,end:1241}},{type:"logic",token:{type:"Twig.logic.type.set",key:"error",expression:[{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1241,end:1278}},position:{start:1241,end:1278}},{type:"raw",value:"",position:{start:1278,end:1280}},{type:"logic",token:{type:"Twig.logic.type.set",key:"required",expression:[{type:"Twig.expression.type.variable",value:"required",match:["required"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1280,end:1326}},position:{start:1280,end:1326}},{type:"raw",value:"",position:{start:1326,end:1328}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1328,end:1374}},position:{start:1328,end:1374}},{type:"raw",value:"",position:{start:1374,end:1378}},{type:"raw",value:"",position:{start:1429,end:1431}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1431,end:1450},output:[{type:"raw",value:"",position:{start:1450,end:1454}},{type:"logic",token:{type:"Twig.logic.type.set",key:"field",expression:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"disabled"},{type:"Twig.expression.type.bool",value:!0},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1454,end:1505}},position:{start:1454,end:1505}},{type:"raw",value:"",position:{start:1505,end:1507}}]},position:{open:{start:1431,end:1450},close:{start:1507,end:1520}}},{type:"raw",value:"",position:{start:1520,end:1524}},{type:"raw",value:"",position:{start:1572,end:1574}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:1574,end:1590},output:[{type:"raw",value:"",position:{start:1590,end:1594}},{type:"logic",token:{type:"Twig.logic.type.set",key:"field",expression:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"error"},{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1594,end:1643}},position:{start:1594,end:1643}},{type:"raw",value:"",position:{start:1643,end:1645}}]},position:{open:{start:1574,end:1590},close:{start:1645,end:1658}}},{type:"raw",value:"",position:{start:1658,end:1662}},{type:"raw",value:"",position:{start:1742,end:1744}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1744,end:1783}},position:{start:1744,end:1783}},{type:"raw",value:"",position:{start:1783,end:1785}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"required",match:["required"]}],position:{start:1785,end:1804},output:[{type:"raw",value:"",position:{start:1804,end:1808}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field--required"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1808,end:1872}},position:{start:1808,end:1872}},{type:"raw",value:"",position:{start:1872,end:1874}}]},position:{open:{start:1785,end:1804},close:{start:1874,end:1887}}},{type:"raw",value:"",position:{start:1887,end:1889}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:1889,end:1905},output:[{type:"raw",value:"",position:{start:1905,end:1909}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field--error"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1909,end:1970}},position:{start:1909,end:1970}},{type:"raw",value:"",position:{start:1970,end:1972}}]},position:{open:{start:1889,end:1905},close:{start:1972,end:1985}}},{type:"raw",value:"",position:{start:1985,end:1987}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1987,end:2006},output:[{type:"raw",value:"",position:{start:2006,end:2010}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2010,end:2074}},position:{start:2010,end:2074}},{type:"raw",value:"",position:{start:2074,end:2076}}]},position:{open:{start:1987,end:2006},close:{start:2076,end:2089}}},{type:"raw",value:`<div\r
  class="`,position:{start:2089,end:2108}},{type:"output",position:{start:2108,end:2136},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:2108,end:2136}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:2108,end:2136},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2108,end:2136}},{type:"Twig.expression.type.string",value:" ",position:{start:2108,end:2136}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2108,end:2136},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:2108,end:2136}}]},{type:"raw",value:`"\r
  `,position:{start:2136,end:2141}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:2141,end:2160},output:[{type:"output",position:{start:2160,end:2176},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:2160,end:2176}}]}]},position:{open:{start:2141,end:2160},close:{start:2176,end:2187}}},{type:"raw",value:`\r
>`,position:{start:2187,end:2194}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:2194,end:2210},output:[{type:"raw",value:"",position:{start:2210,end:2216}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/label/label.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"text"},{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"forId"},{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"required"},{type:"Twig.expression.type.variable",value:"required",match:["required"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"disabled"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"attributes"},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.key.period",key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.string",value:"ps-form-field__label"}]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:2216,end:2441}},position:{start:2216,end:2441}},{type:"raw",value:"",position:{start:2441,end:2445}}]},position:{open:{start:2194,end:2210},close:{start:2445,end:2458}}},{type:"raw",value:`<div class="ps-form-field__input-wrapper">\r
    `,position:{start:2458,end:2512}},{type:"raw",value:`\r
    `,position:{start:2589,end:2595}},{type:"logic",token:{type:"Twig.logic.type.include",only:!1,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/field/field.twig"}],withStack:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"attributes"},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"id"},{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2595,end:2702}},position:{start:2595,end:2702}},{type:"raw",value:`\r
  </div>`,position:{start:2702,end:2718}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"helperText",match:["helperText"]},{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:2718,end:2753},output:[{type:"raw",value:'<div class="ps-form-field__helper" id="',position:{start:2753,end:2798}},{type:"output",position:{start:2798,end:2806},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:2798,end:2806}}]},{type:"raw",value:'-helper">',position:{start:2806,end:2815}},{type:"output",position:{start:2815,end:2831},stack:[{type:"Twig.expression.type.variable",value:"helperText",match:["helperText"],position:{start:2815,end:2831}}]},{type:"raw",value:"</div>",position:{start:2831,end:2841}}]},position:{open:{start:2718,end:2753},close:{start:2841,end:2854}}},{type:"raw",value:"",position:{start:2854,end:2860}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2860,end:2876},output:[{type:"raw",value:'<div class="ps-form-field__error" id="',position:{start:2876,end:2920}},{type:"output",position:{start:2920,end:2928},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:2920,end:2928}}]},{type:"raw",value:`-error" role="alert" aria-live="polite">\r
      `,position:{start:2928,end:2976}},{type:"output",position:{start:2976,end:2987},stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"],position:{start:2976,end:2987}}]},{type:"raw",value:`\r
    </div>`,position:{start:2987,end:3003}}]},position:{open:{start:2860,end:2876},close:{start:3003,end:3016}}},{type:"raw",value:`</div>\r
`,position:{start:3016,end:3016}}],precompiled:!0});l.options.allowInlineIncludes=!0;try{let i=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(i)||(i=Object.entries(i)),y(l.render({attributes:new L(i),...t}))}catch(i){return y("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/form-field/form-field.twig: "+i.toString())}},N={label:"Email Address",id:"email-field",field:{type:"email",value:"",placeholder:"Enter your email address",disabled:!1,icon:"",iconPosition:"right"},helperText:"We will never share your email with anyone.",error:"",required:!1,disabled:!1},O={title:"Components/FormField",tags:["autodocs"],parameters:{docs:{description:{component:`Complete form field with label, input, helper text, and error message. Wraps ps-field atom with form semantics.

See Props, Showcases, and README for details on states, accessibility, and integration.`}}},argTypes:{label:{name:"label",description:"Label text for the field",control:"text",table:{category:"Content",type:{summary:"string"}}},helperText:{name:"helperText",description:"Optional helper text below field (hidden when error is present)",control:"text",table:{category:"Content",type:{summary:"string"}}},error:{name:"error",description:"Error message to display (replaces helper text, sets error state)",control:"text",table:{category:"Content",type:{summary:"string"}}},"field.placeholder":{name:"field.placeholder",description:"Placeholder text for the input field",control:"text",table:{category:"Content",type:{summary:"string"}}},"field.value":{name:"field.value",description:"Current value of the field",control:"text",table:{category:"Content",type:{summary:"string"}}},"field.type":{name:"field.type",description:"Input field type",control:"select",options:["text","email","number","search","textarea","select"],table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:"text"}}},"field.icon":{name:"field.icon",description:'Icon name (without "icon-" prefix)',control:"text",table:{category:"Appearance",type:{summary:"string"}}},"field.iconPosition":{name:"field.iconPosition",description:"Icon position",control:"select",options:["left","right"],table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:"right"}}},required:{name:"required",description:"Mark field as required (shows asterisk)",control:"boolean",table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},disabled:{name:"disabled",description:"Disable entire field group",control:"boolean",table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},id:{name:"id",description:"Unique ID for label/field association (auto-generated if omitted)",control:"text",table:{category:"Accessibility",type:{summary:"string"}}}}},a={render:t=>e(t),args:{...N}},r={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Default (empty) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Default (empty)</h3>
        ${e({label:"Email Address",field:{type:"email",placeholder:"Enter your email"},helperText:"We will never share your email with anyone."})}
      </div>

      <!-- Filled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Filled</h3>
        ${e({label:"Full Name",field:{type:"text",value:"Jean Dupont",placeholder:"Enter your name"},helperText:"Please enter your full legal name."})}
      </div>

      <!-- Required -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Required</h3>
        ${e({label:"Phone Number",field:{type:"text",placeholder:"+33 1 23 45 67 89"},required:!0,helperText:"Required for account verification."})}
      </div>

      <!-- Error -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error</h3>
        ${e({label:"Email Address",field:{type:"email",value:"invalid-email",placeholder:"Enter your email"},error:"Please enter a valid email address.",required:!0})}
      </div>

      <!-- Disabled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Disabled</h3>
        ${e({label:"Account Type",field:{type:"text",value:"Premium Member",placeholder:"Account type"},disabled:!0,helperText:"This field cannot be modified."})}
      </div>
    </div>
  `},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Icon Right (default) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Right</h3>
        ${e({label:"Search",field:{type:"search",placeholder:"Search properties...",icon:"search",iconPosition:"right"},helperText:"Enter keywords to search our database."})}
      </div>

      <!-- Icon Left -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Left</h3>
        ${e({label:"Email",field:{type:"email",placeholder:"example@domain.com",icon:"mail",iconPosition:"left"},helperText:"We will send a confirmation email."})}
      </div>
    </div>
  `},p={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Text -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Text Input</h3>
        ${e({label:"Full Name",field:{type:"text",placeholder:"John Doe"},helperText:"Enter your first and last name."})}
      </div>

      <!-- Email -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Email Input</h3>
        ${e({label:"Email Address",field:{type:"email",placeholder:"example@domain.com"},required:!0,helperText:"Valid email format required."})}
      </div>

      <!-- Number -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Number Input</h3>
        ${e({label:"Property Size (m²)",field:{type:"number",placeholder:"75"},helperText:"Enter the total area in square meters."})}
      </div>

      <!-- Textarea -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Textarea</h3>
        ${e({label:"Description",field:{type:"textarea",placeholder:"Enter a detailed description..."},helperText:"Provide additional details (optional)."})}
      </div>
    </div>
  `},o={render:()=>`
    <form style="display: flex; flex-direction: column; gap: var(--size-5); max-width: 480px; padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      <h2 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-700); color: var(--gray-900);">Contact Information</h2>
      
      ${e({label:"Full Name",id:"contact-name",field:{type:"text",placeholder:"Jean Dupont"},required:!0})}

      ${e({label:"Email Address",id:"contact-email",field:{type:"email",placeholder:"jean.dupont@example.com"},required:!0,helperText:"We will send a confirmation to this address."})}

      ${e({label:"Phone Number",id:"contact-phone",field:{type:"text",placeholder:"+33 1 23 45 67 89"},helperText:"Optional - for SMS notifications."})}

      ${e({label:"Message",id:"contact-message",field:{type:"textarea",placeholder:"How can we help you?"},required:!0,helperText:"Please provide details about your inquiry."})}

      <button 
        type="submit" 
        style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: var(--font-weight-600); cursor: pointer;"
        onmouseover="this.style.background='var(--primary-hover)'"
        onmouseout="this.style.background='var(--primary)'"
      >
        Submit Form
      </button>
    </form>
  `};var d,c,u,v,g;a.parameters={...a.parameters,docs:{...(d=a.parameters)==null?void 0:d.docs,source:{originalSource:`{
  render: args => formFieldTwig(args),
  args: {
    ...formFieldData
  }
}`,...(u=(c=a.parameters)==null?void 0:c.docs)==null?void 0:u.source},description:{story:"Default FormField - Email input with helper text",...(g=(v=a.parameters)==null?void 0:v.docs)==null?void 0:g.description}}};var m,w,x,f,h;r.parameters={...r.parameters,docs:{...(m=r.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Default (empty) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Default (empty)</h3>
        \${formFieldTwig({
    label: 'Email Address',
    field: {
      type: 'email',
      placeholder: 'Enter your email'
    },
    helperText: 'We will never share your email with anyone.'
  })}
      </div>

      <!-- Filled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Filled</h3>
        \${formFieldTwig({
    label: 'Full Name',
    field: {
      type: 'text',
      value: 'Jean Dupont',
      placeholder: 'Enter your name'
    },
    helperText: 'Please enter your full legal name.'
  })}
      </div>

      <!-- Required -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Required</h3>
        \${formFieldTwig({
    label: 'Phone Number',
    field: {
      type: 'text',
      placeholder: '+33 1 23 45 67 89'
    },
    required: true,
    helperText: 'Required for account verification.'
  })}
      </div>

      <!-- Error -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error</h3>
        \${formFieldTwig({
    label: 'Email Address',
    field: {
      type: 'email',
      value: 'invalid-email',
      placeholder: 'Enter your email'
    },
    error: 'Please enter a valid email address.',
    required: true
  })}
      </div>

      <!-- Disabled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Disabled</h3>
        \${formFieldTwig({
    label: 'Account Type',
    field: {
      type: 'text',
      value: 'Premium Member',
      placeholder: 'Account type'
    },
    disabled: true,
    helperText: 'This field cannot be modified.'
  })}
      </div>
    </div>
  \`
}`,...(x=(w=r.parameters)==null?void 0:w.docs)==null?void 0:x.source},description:{story:`All Field States Showcase\r
Demonstrates default, filled, error, and disabled states`,...(h=(f=r.parameters)==null?void 0:f.docs)==null?void 0:h.description}}};var T,b,k,z,q;s.parameters={...s.parameters,docs:{...(T=s.parameters)==null?void 0:T.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Icon Right (default) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Right</h3>
        \${formFieldTwig({
    label: 'Search',
    field: {
      type: 'search',
      placeholder: 'Search properties...',
      icon: 'search',
      iconPosition: 'right'
    },
    helperText: 'Enter keywords to search our database.'
  })}
      </div>

      <!-- Icon Left -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Left</h3>
        \${formFieldTwig({
    label: 'Email',
    field: {
      type: 'email',
      placeholder: 'example@domain.com',
      icon: 'mail',
      iconPosition: 'left'
    },
    helperText: 'We will send a confirmation email.'
  })}
      </div>
    </div>
  \`
}`,...(k=(b=s.parameters)==null?void 0:b.docs)==null?void 0:k.source},description:{story:"With Icon - Shows fields with left and right positioned icons",...(q=(z=s.parameters)==null?void 0:z.docs)==null?void 0:q.description}}};var E,F,_,P,I;p.parameters={...p.parameters,docs:{...(E=p.parameters)==null?void 0:E.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Text -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Text Input</h3>
        \${formFieldTwig({
    label: 'Full Name',
    field: {
      type: 'text',
      placeholder: 'John Doe'
    },
    helperText: 'Enter your first and last name.'
  })}
      </div>

      <!-- Email -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Email Input</h3>
        \${formFieldTwig({
    label: 'Email Address',
    field: {
      type: 'email',
      placeholder: 'example@domain.com'
    },
    required: true,
    helperText: 'Valid email format required.'
  })}
      </div>

      <!-- Number -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Number Input</h3>
        \${formFieldTwig({
    label: 'Property Size (m²)',
    field: {
      type: 'number',
      placeholder: '75'
    },
    helperText: 'Enter the total area in square meters.'
  })}
      </div>

      <!-- Textarea -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Textarea</h3>
        \${formFieldTwig({
    label: 'Description',
    field: {
      type: 'textarea',
      placeholder: 'Enter a detailed description...'
    },
    helperText: 'Provide additional details (optional).'
  })}
      </div>
    </div>
  \`
}`,...(_=(F=p.parameters)==null?void 0:F.docs)==null?void 0:_.source},description:{story:"Different Field Types - Text, Email, Number, Textarea",...(I=(P=p.parameters)==null?void 0:P.docs)==null?void 0:I.description}}};var C,A,$,D,S;o.parameters={...o.parameters,docs:{...(C=o.parameters)==null?void 0:C.docs,source:{originalSource:`{
  render: () => \`
    <form style="display: flex; flex-direction: column; gap: var(--size-5); max-width: 480px; padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      <h2 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-700); color: var(--gray-900);">Contact Information</h2>
      
      \${formFieldTwig({
    label: 'Full Name',
    id: 'contact-name',
    field: {
      type: 'text',
      placeholder: 'Jean Dupont'
    },
    required: true
  })}

      \${formFieldTwig({
    label: 'Email Address',
    id: 'contact-email',
    field: {
      type: 'email',
      placeholder: 'jean.dupont@example.com'
    },
    required: true,
    helperText: 'We will send a confirmation to this address.'
  })}

      \${formFieldTwig({
    label: 'Phone Number',
    id: 'contact-phone',
    field: {
      type: 'text',
      placeholder: '+33 1 23 45 67 89'
    },
    helperText: 'Optional - for SMS notifications.'
  })}

      \${formFieldTwig({
    label: 'Message',
    id: 'contact-message',
    field: {
      type: 'textarea',
      placeholder: 'How can we help you?'
    },
    required: true,
    helperText: 'Please provide details about your inquiry.'
  })}

      <button 
        type="submit" 
        style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: var(--font-weight-600); cursor: pointer;"
        onmouseover="this.style.background='var(--primary-hover)'"
        onmouseout="this.style.background='var(--primary)'"
      >
        Submit Form
      </button>
    </form>
  \`
}`,...($=(A=o.parameters)==null?void 0:A.docs)==null?void 0:$.source},description:{story:"In Form Context - Multiple fields in a realistic form layout",...(S=(D=o.parameters)==null?void 0:D.docs)==null?void 0:S.description}}};const B=["Default","AllStates","WithIcon","AllFieldTypes","InFormContext"];export{p as AllFieldTypes,r as AllStates,a as Default,o as InFormContext,s as WithIcon,B as __namedExportsOrder,O as default};
