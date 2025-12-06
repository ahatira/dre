import{t as n,T as L}from"./iframe-DeCmpQ6I.js";import{D as R,a as j}from"./twig-Cbw8xbjJ.js";import"./field-siyBWPbV.js";import"./label-CdGegDd8.js";j(L);L.cache(!1);n.twig({id:"@elements/label/label.twig",data:[{type:"raw",value:"",position:{start:444,end:446}},{type:"logic",token:{type:"Twig.logic.type.set",key:"baseClass",expression:[{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"ps-label"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:446,end:499}},position:{start:446,end:499}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"required",match:["required"]},{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.string",value:"--required"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"]},{type:"Twig.expression.type.string",value:"--disabled"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:500,end:629}},position:{start:500,end:629}},{type:"raw",value:"<label",position:{start:630,end:637}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:637,end:656},output:[{type:"raw",value:" ",position:{start:656,end:657}},{type:"output",position:{start:657,end:691},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:657,end:691}},{type:"Twig.expression.type.key.period",position:{start:657,end:691},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:657,end:691},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:657,end:691}}]}]}]},position:{open:{start:637,end:656},close:{start:691,end:701}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:691,end:701},output:[{type:"raw",value:' class="',position:{start:701,end:709}},{type:"output",position:{start:709,end:737},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:709,end:737}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:709,end:737},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:709,end:737}},{type:"Twig.expression.type.string",value:" ",position:{start:709,end:737}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:709,end:737},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:709,end:737}}]},{type:"raw",value:'"',position:{start:737,end:738}}]},position:{open:{start:691,end:701},close:{start:738,end:749}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"forId",match:["forId"]}],position:{start:749,end:763},output:[{type:"raw",value:' for="',position:{start:763,end:769}},{type:"output",position:{start:769,end:780},stack:[{type:"Twig.expression.type.variable",value:"forId",match:["forId"],position:{start:769,end:780}}]},{type:"raw",value:'"',position:{start:780,end:781}}]},position:{open:{start:749,end:763},close:{start:781,end:792}}},{type:"raw",value:`>
  <span class="`,position:{start:792,end:809}},{type:"output",position:{start:809,end:824},stack:[{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"],position:{start:809,end:824}}]},{type:"raw",value:'__text">',position:{start:824,end:832}},{type:"output",position:{start:832,end:842},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:832,end:842}}]},{type:"raw",value:`</span>
  `,position:{start:842,end:852}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"required",match:["required"]}],position:{start:852,end:869},output:[{type:"raw",value:'    <span class="',position:{start:870,end:887}},{type:"output",position:{start:887,end:902},stack:[{type:"Twig.expression.type.variable",value:"baseClass",match:["baseClass"],position:{start:887,end:902}}]},{type:"raw",value:`__required" aria-hidden="true">*</span>
    <span class="visually-hidden">(required field)</span>
  `,position:{start:902,end:1002}}]},position:{open:{start:852,end:869},close:{start:1002,end:1013}}},{type:"raw",value:"</label>",position:{start:1014,end:1014}}],precompiled:!0});n.twig({id:"@elements/field/field.twig",data:[{type:"raw",value:"",position:{start:902,end:904}},{type:"logic",token:{type:"Twig.logic.type.set",key:"type",expression:[{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:904,end:943}},position:{start:904,end:943}},{type:"logic",token:{type:"Twig.logic.type.set",key:"value",expression:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:944,end:981}},position:{start:944,end:981}},{type:"logic",token:{type:"Twig.logic.type.set",key:"placeholder",expression:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:982,end:1031}},position:{start:982,end:1031}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1032,end:1078}},position:{start:1032,end:1078}},{type:"logic",token:{type:"Twig.logic.type.set",key:"error",expression:[{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1079,end:1116}},position:{start:1079,end:1116}},{type:"logic",token:{type:"Twig.logic.type.set",key:"hideError",expression:[{type:"Twig.expression.type.variable",value:"hideError",match:["hideError"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1117,end:1165}},position:{start:1117,end:1165}},{type:"logic",token:{type:"Twig.logic.type.set",key:"done",expression:[{type:"Twig.expression.type.variable",value:"done",match:["done"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1166,end:1204}},position:{start:1166,end:1204}},{type:"logic",token:{type:"Twig.logic.type.set",key:"icon",expression:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1205,end:1240}},position:{start:1205,end:1240}},{type:"logic",token:{type:"Twig.logic.type.set",key:"iconPosition",expression:[{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"right"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1241,end:1297}},position:{start:1241,end:1297}},{type:"logic",token:{type:"Twig.logic.type.set",key:"attributes",expression:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1298,end:1361}},position:{start:1298,end:1361}},{type:"raw",value:"",position:{start:1362,end:1363}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-field"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.string",value:"text"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.string",value:"ps-field--"},{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.string",value:"ps-field--error"},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"done",match:["done"]},{type:"Twig.expression.type.string",value:"ps-field--done"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.string",value:"ps-field--disabled"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.string",value:"ps-field--filled"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.string",value:"ps-field--icon-"},{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1363,end:1641}},position:{start:1363,end:1641}},{type:"raw",value:"<div ",position:{start:1642,end:1648}},{type:"output",position:{start:1648,end:1682},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:1648,end:1682}},{type:"Twig.expression.type.key.period",position:{start:1648,end:1682},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:1648,end:1682},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:1648,end:1682}}]}]},{type:"raw",value:">",position:{start:1682,end:1686}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.string",value:"left"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:1686,end:1728},output:[{type:"raw",value:'<span class="ps-field__icon ps-field__icon--left" data-icon="',position:{start:1729,end:1794}},{type:"output",position:{start:1794,end:1804},stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"],position:{start:1794,end:1804}}]},{type:"raw",value:'" aria-hidden="true"></span>',position:{start:1804,end:1835}}]},position:{open:{start:1686,end:1728},close:{start:1835,end:1848}}},{type:"raw",value:"",position:{start:1849,end:1852}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.string",value:"textarea"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1852,end:1881},output:[{type:"raw",value:`<textarea
      class="ps-field__input"
      placeholder="`,position:{start:1882,end:1945}},{type:"output",position:{start:1945,end:1962},stack:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:1945,end:1962}}]},{type:"raw",value:`"
      `,position:{start:1962,end:1970}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1970,end:1987},output:[{type:"raw",value:'disabled aria-disabled="true"',position:{start:1987,end:2016}}]},position:{open:{start:1970,end:1987},close:{start:2016,end:2027}}},{type:"raw",value:"      ",position:{start:2028,end:2034}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2034,end:2048},output:[{type:"raw",value:'aria-invalid="true" aria-describedby="field-error"',position:{start:2048,end:2098}}]},position:{open:{start:2034,end:2048},close:{start:2098,end:2109}}},{type:"raw",value:"    >",position:{start:2110,end:2115}},{type:"output",position:{start:2115,end:2126},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2115,end:2126}}]},{type:"raw",value:"</textarea>",position:{start:2126,end:2140}}]},position:{open:{start:1852,end:1881},close:{start:2140,end:2171}}},{type:"logic",token:{type:"Twig.logic.type.elseif",stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"]},{type:"Twig.expression.type.string",value:"select"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2140,end:2171},output:[{type:"raw",value:`<div
      class="ps-field__input"
      role="combobox"
      aria-expanded="false"
      aria-haspopup="listbox"
      `,position:{start:2172,end:2297}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:2297,end:2314},output:[{type:"raw",value:'aria-disabled="true"',position:{start:2314,end:2334}}]},position:{open:{start:2297,end:2314},close:{start:2334,end:2345}}},{type:"raw",value:"      ",position:{start:2346,end:2352}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2352,end:2366},output:[{type:"raw",value:'aria-invalid="true" aria-describedby="field-error"',position:{start:2366,end:2416}}]},position:{open:{start:2352,end:2366},close:{start:2416,end:2427}}},{type:"raw",value:"    >",position:{start:2428,end:2433}},{type:"output",position:{start:2433,end:2465},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2433,end:2465}},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],position:{start:2433,end:2465},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2433,end:2465}},{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:2433,end:2465}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2433,end:2465},expression:!1}]}]},{type:"raw",value:"</div>",position:{start:2465,end:2474}}]},position:{open:{start:2140,end:2171},close:{start:2474,end:2486}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2474,end:2486},output:[{type:"raw",value:`<input
      class="ps-field__input"
      type="`,position:{start:2487,end:2540}},{type:"output",position:{start:2540,end:2550},stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"],position:{start:2540,end:2550}}]},{type:"raw",value:`"
      value="`,position:{start:2550,end:2565}},{type:"output",position:{start:2565,end:2576},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2565,end:2576}}]},{type:"raw",value:`"
      placeholder="`,position:{start:2576,end:2597}},{type:"output",position:{start:2597,end:2614},stack:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:2597,end:2614}}]},{type:"raw",value:`"
      `,position:{start:2614,end:2622}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:2622,end:2639},output:[{type:"raw",value:'disabled aria-disabled="true"',position:{start:2639,end:2668}}]},position:{open:{start:2622,end:2639},close:{start:2668,end:2679}}},{type:"raw",value:"      ",position:{start:2680,end:2686}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:2686,end:2700},output:[{type:"raw",value:'aria-invalid="true" aria-describedby="field-error"',position:{start:2700,end:2750}}]},position:{open:{start:2686,end:2700},close:{start:2750,end:2761}}},{type:"raw",value:"    />",position:{start:2762,end:2771}}]},position:{open:{start:2474,end:2486},close:{start:2771,end:2784}}},{type:"raw",value:"",position:{start:2785,end:2788}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.variable",value:"iconPosition",match:["iconPosition"]},{type:"Twig.expression.type.string",value:"right"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:2788,end:2831},output:[{type:"raw",value:'<span class="ps-field__icon ps-field__icon--right" data-icon="',position:{start:2832,end:2898}},{type:"output",position:{start:2898,end:2908},stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"],position:{start:2898,end:2908}}]},{type:"raw",value:'" aria-hidden="true"></span>',position:{start:2908,end:2939}}]},position:{open:{start:2788,end:2831},close:{start:2939,end:2952}}},{type:"raw",value:"",position:{start:2953,end:2956}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.variable",value:"hideError",match:["hideError"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:2956,end:2990},output:[{type:"raw",value:'<div class="ps-field__error" id="field-error" role="alert">',position:{start:2991,end:3054}},{type:"output",position:{start:3054,end:3065},stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"],position:{start:3054,end:3065}}]},{type:"raw",value:"</div>",position:{start:3065,end:3074}}]},position:{open:{start:2956,end:2990},close:{start:3074,end:3087}}},{type:"raw",value:"</div>",position:{start:3088,end:3088}}],precompiled:!0});const y=t=>t,e=(t={})=>{const l=n.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/form-field/form-field.twig",data:[{type:"raw",value:"",position:{start:1060,end:1064}},{type:"logic",token:{type:"Twig.logic.type.set",key:"label",expression:[{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1064,end:1101}},position:{start:1064,end:1101}},{type:"raw",value:"",position:{start:1101,end:1103}},{type:"logic",token:{type:"Twig.logic.type.set",key:"id",expression:[{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"field-"},{type:"Twig.expression.type._function",fn:"random",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1103,end:1151}},position:{start:1103,end:1151}},{type:"raw",value:"",position:{start:1151,end:1153}},{type:"logic",token:{type:"Twig.logic.type.set",key:"field",expression:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1153,end:1190}},position:{start:1153,end:1190}},{type:"raw",value:"",position:{start:1190,end:1192}},{type:"logic",token:{type:"Twig.logic.type.set",key:"helperText",expression:[{type:"Twig.expression.type.variable",value:"helperText",match:["helperText"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1192,end:1239}},position:{start:1192,end:1239}},{type:"raw",value:"",position:{start:1239,end:1241}},{type:"logic",token:{type:"Twig.logic.type.set",key:"error",expression:[{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1241,end:1278}},position:{start:1241,end:1278}},{type:"raw",value:"",position:{start:1278,end:1280}},{type:"logic",token:{type:"Twig.logic.type.set",key:"required",expression:[{type:"Twig.expression.type.variable",value:"required",match:["required"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1280,end:1326}},position:{start:1280,end:1326}},{type:"raw",value:"",position:{start:1326,end:1328}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1328,end:1374}},position:{start:1328,end:1374}},{type:"raw",value:"",position:{start:1374,end:1378}},{type:"raw",value:"",position:{start:1429,end:1431}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1431,end:1450},output:[{type:"raw",value:"",position:{start:1450,end:1454}},{type:"logic",token:{type:"Twig.logic.type.set",key:"field",expression:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"disabled"},{type:"Twig.expression.type.bool",value:!0},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1454,end:1505}},position:{start:1454,end:1505}},{type:"raw",value:"",position:{start:1505,end:1507}}]},position:{open:{start:1431,end:1450},close:{start:1507,end:1520}}},{type:"raw",value:"",position:{start:1520,end:1524}},{type:"raw",value:`\r
`,position:{start:1592,end:1594}},{type:"raw",value:`\r
\r
`,position:{start:1659,end:1663}},{type:"raw",value:"",position:{start:1743,end:1745}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1745,end:1784}},position:{start:1745,end:1784}},{type:"raw",value:"",position:{start:1784,end:1786}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"required",match:["required"]}],position:{start:1786,end:1805},output:[{type:"raw",value:"",position:{start:1805,end:1809}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field--required"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1809,end:1873}},position:{start:1809,end:1873}},{type:"raw",value:"",position:{start:1873,end:1875}}]},position:{open:{start:1786,end:1805},close:{start:1875,end:1888}}},{type:"raw",value:"",position:{start:1888,end:1890}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:1890,end:1906},output:[{type:"raw",value:"",position:{start:1906,end:1910}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field--error"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1910,end:1971}},position:{start:1910,end:1971}},{type:"raw",value:"",position:{start:1971,end:1973}}]},position:{open:{start:1890,end:1906},close:{start:1973,end:1986}}},{type:"raw",value:"",position:{start:1986,end:1988}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1988,end:2007},output:[{type:"raw",value:"",position:{start:2007,end:2011}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-form-field--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2011,end:2075}},position:{start:2011,end:2075}},{type:"raw",value:"",position:{start:2075,end:2077}}]},position:{open:{start:1988,end:2007},close:{start:2077,end:2090}}},{type:"raw",value:`<div\r
  class="`,position:{start:2090,end:2109}},{type:"output",position:{start:2109,end:2137},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:2109,end:2137}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:2109,end:2137},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2109,end:2137}},{type:"Twig.expression.type.string",value:" ",position:{start:2109,end:2137}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2109,end:2137},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:2109,end:2137}}]},{type:"raw",value:`"\r
  `,position:{start:2137,end:2142}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:2142,end:2161},output:[{type:"output",position:{start:2161,end:2177},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:2161,end:2177}}]}]},position:{open:{start:2142,end:2161},close:{start:2177,end:2188}}},{type:"raw",value:`\r
>`,position:{start:2188,end:2195}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:2195,end:2211},output:[{type:"raw",value:"",position:{start:2211,end:2217}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/label/label.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"text"},{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"forId"},{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"required"},{type:"Twig.expression.type.variable",value:"required",match:["required"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"disabled"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"attributes"},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.key.period",key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.string",value:"ps-form-field__label"}]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:2217,end:2442}},position:{start:2217,end:2442}},{type:"raw",value:"",position:{start:2442,end:2446}}]},position:{open:{start:2195,end:2211},close:{start:2446,end:2459}}},{type:"raw",value:`<div class="ps-form-field__input-wrapper">\r
    `,position:{start:2459,end:2513}},{type:"raw",value:`\r
    `,position:{start:2590,end:2596}},{type:"raw",value:`\r
    `,position:{start:2685,end:2691}},{type:"logic",token:{type:"Twig.logic.type.set",key:"fieldProps",expression:[{type:"Twig.expression.type.variable",value:"field",match:["field"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"attributes"},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"id"},{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"hideError"},{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.bool",value:!0},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2691,end:2826}},position:{start:2691,end:2826}},{type:"raw",value:`\r
    `,position:{start:2826,end:2832}},{type:"logic",token:{type:"Twig.logic.type.include",only:!1,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/field/field.twig"}],withStack:[{type:"Twig.expression.type.variable",value:"fieldProps",match:["fieldProps"]}],position:{start:2832,end:2890}},position:{start:2832,end:2890}},{type:"raw",value:`\r
  </div>`,position:{start:2890,end:2906}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"helperText",match:["helperText"]},{type:"Twig.expression.type.variable",value:"error",match:["error"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:2906,end:2941},output:[{type:"raw",value:'<div class="ps-form-field__helper" id="',position:{start:2941,end:2986}},{type:"output",position:{start:2986,end:2994},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:2986,end:2994}}]},{type:"raw",value:'-helper">',position:{start:2994,end:3003}},{type:"output",position:{start:3003,end:3019},stack:[{type:"Twig.expression.type.variable",value:"helperText",match:["helperText"],position:{start:3003,end:3019}}]},{type:"raw",value:"</div>",position:{start:3019,end:3029}}]},position:{open:{start:2906,end:2941},close:{start:3029,end:3042}}},{type:"raw",value:"",position:{start:3042,end:3048}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"]}],position:{start:3048,end:3064},output:[{type:"raw",value:'<div class="ps-form-field__error" id="',position:{start:3064,end:3108}},{type:"output",position:{start:3108,end:3116},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:3108,end:3116}}]},{type:"raw",value:`-error" role="alert" aria-live="polite">\r
      <svg class="ps-form-field__error-icon" viewBox="0 0 24 24" aria-hidden="true" width="16" height="16">\r
        <circle cx="12" cy="12" r="11" fill="none" stroke="currentColor" stroke-width="2"/>\r
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>\r
      </svg>\r
      <span>`,position:{start:3116,end:3487}},{type:"output",position:{start:3487,end:3498},stack:[{type:"Twig.expression.type.variable",value:"error",match:["error"],position:{start:3487,end:3498}}]},{type:"raw",value:`</span>\r
    </div>`,position:{start:3498,end:3521}}]},position:{open:{start:3048,end:3064},close:{start:3521,end:3534}}},{type:"raw",value:`</div>\r
`,position:{start:3534,end:3534}}],precompiled:!0});l.options.allowInlineIncludes=!0;try{let i=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(i)||(i=Object.entries(i)),y(l.render({attributes:new R(i),...t}))}catch(i){return y("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/form-field/form-field.twig: "+i.toString())}},N={label:"Email Address",id:"email-field",field:{type:"email",value:"",placeholder:"Enter your email address",disabled:!1,icon:"",iconPosition:"right"},helperText:"We will never share your email with anyone.",error:"",required:!1,disabled:!1},O={title:"Components/FormField",tags:["autodocs"],parameters:{docs:{description:{component:`Complete form field with label, input, helper text, and error message. Wraps ps-field atom with form semantics.

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
Demonstrates default, filled, error, and disabled states`,...(h=(f=r.parameters)==null?void 0:f.docs)==null?void 0:h.description}}};var T,b,k,z,E;s.parameters={...s.parameters,docs:{...(T=s.parameters)==null?void 0:T.docs,source:{originalSource:`{
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
}`,...(k=(b=s.parameters)==null?void 0:b.docs)==null?void 0:k.source},description:{story:"With Icon - Shows fields with left and right positioned icons",...(E=(z=s.parameters)==null?void 0:z.docs)==null?void 0:E.description}}};var q,F,_,P,C;p.parameters={...p.parameters,docs:{...(q=p.parameters)==null?void 0:q.docs,source:{originalSource:`{
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
}`,...(_=(F=p.parameters)==null?void 0:F.docs)==null?void 0:_.source},description:{story:"Different Field Types - Text, Email, Number, Textarea",...(C=(P=p.parameters)==null?void 0:P.docs)==null?void 0:C.description}}};var I,A,$,D,S;o.parameters={...o.parameters,docs:{...(I=o.parameters)==null?void 0:I.docs,source:{originalSource:`{
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
