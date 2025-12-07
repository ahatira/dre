import{t as C,T as D}from"./iframe-C-ciPShf.js";import{D as A,a as _}from"./twig-B9SdSbF4.js";_(D);D.cache(!1);const v=a=>a,e=(a={})=>{const p=C.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/progress-bar/progress-bar.twig",data:[{type:"raw",value:`
`,position:{start:871,end:872}},{type:"logic",token:{type:"Twig.logic.type.set",key:"variant",expression:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"linear"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:872,end:917}},position:{start:872,end:917}},{type:"logic",token:{type:"Twig.logic.type.set",key:"color",expression:[{type:"Twig.expression.type.variable",value:"color",match:["color"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"default"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:918,end:960}},position:{start:918,end:960}},{type:"logic",token:{type:"Twig.logic.type.set",key:"size",expression:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:961,end:996}},position:{start:961,end:996}},{type:"logic",token:{type:"Twig.logic.type.set",key:"indeterminate",expression:[{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:997,end:1051}},position:{start:997,end:1051}},{type:"logic",token:{type:"Twig.logic.type.set",key:"striped",expression:[{type:"Twig.expression.type.variable",value:"striped",match:["striped"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1052,end:1094}},position:{start:1052,end:1094}},{type:"logic",token:{type:"Twig.logic.type.set",key:"showLabel",expression:[{type:"Twig.expression.type.variable",value:"showLabel",match:["showLabel"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1095,end:1141}},position:{start:1095,end:1141}},{type:"logic",token:{type:"Twig.logic.type.set",key:"value",expression:[{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.number",value:0,match:["0",null]},{type:"Twig.expression.type.operator.binary",value:"??",precidence:15,associativity:"rightToLeft",operator:"??"}]},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:1142,end:1195}},position:{start:1142,end:1195}},{type:"logic",token:{type:"Twig.logic.type.set",key:"min",expression:[{type:"Twig.expression.type.variable",value:"min",match:["min"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.number",value:0,match:["0",null]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1196,end:1226}},position:{start:1196,end:1226}},{type:"logic",token:{type:"Twig.logic.type.set",key:"max",expression:[{type:"Twig.expression.type.variable",value:"max",match:["max"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.number",value:100,match:["100",null]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1227,end:1259}},position:{start:1227,end:1259}},{type:"logic",token:{type:"Twig.logic.type.set",key:"percent",expression:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.test",filter:"null",modifier:"not"},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.variable",value:"min",match:["min"]},{type:"Twig.expression.type.operator.binary",value:"-",precidence:6,associativity:"leftToRight",operator:"-"}]},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"max",match:["max"]},{type:"Twig.expression.type.variable",value:"min",match:["min"]},{type:"Twig.expression.type.operator.binary",value:"-",precidence:6,associativity:"leftToRight",operator:"-"}]},{type:"Twig.expression.type.operator.binary",value:"/",precidence:5,associativity:"leftToRight",operator:"/"},{type:"Twig.expression.type.number",value:100,match:["100",null]},{type:"Twig.expression.type.operator.binary",value:"*",precidence:5,associativity:"leftToRight",operator:"*"}]},{type:"Twig.expression.type.filter",value:"round",match:["|round","round"]},{type:"Twig.expression.type.number",value:0,match:["0",null]},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:1260,end:1345}},position:{start:1260,end:1345}},{type:"logic",token:{type:"Twig.logic.type.set",key:"attributes",expression:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1346,end:1407}},position:{start:1346,end:1407}},{type:"raw",value:`
`,position:{start:1503,end:1504}},{type:"logic",token:{type:"Twig.logic.type.set",key:"root_classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-progress"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"linear"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.string",value:"ps-progress--"},{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"color",match:["color"]},{type:"Twig.expression.type.string",value:"default"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.string",value:"ps-progress--"},{type:"Twig.expression.type.variable",value:"color",match:["color"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.string",value:"ps-progress--"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.string",value:"ps-progress--indeterminate"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"striped",match:["striped"]},{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"linear"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"},{type:"Twig.expression.type.string",value:"ps-progress--striped"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"showLabel",match:["showLabel"]},{type:"Twig.expression.type.string",value:"ps-progress--with-label"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1504,end:1879}},position:{start:1504,end:1879}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"linear"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:1880,end:1908},output:[{type:"raw",value:"  <div ",position:{start:1909,end:1916}},{type:"output",position:{start:1916,end:1955},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:1916,end:1955}},{type:"Twig.expression.type.key.period",position:{start:1916,end:1955},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:1916,end:1955},expression:!0,params:[{type:"Twig.expression.type.variable",value:"root_classes",match:["root_classes"],position:{start:1916,end:1955}}]}]},{type:"raw",value:' role="progressbar" ',position:{start:1955,end:1975}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"}],position:{start:1975,end:2001},output:[{type:"raw",value:'aria-valuenow="',position:{start:2001,end:2016}},{type:"output",position:{start:2016,end:2027},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2016,end:2027}}]},{type:"raw",value:'" aria-valuemin="',position:{start:2027,end:2044}},{type:"output",position:{start:2044,end:2053},stack:[{type:"Twig.expression.type.variable",value:"min",match:["min"],position:{start:2044,end:2053}}]},{type:"raw",value:'" aria-valuemax="',position:{start:2053,end:2070}},{type:"output",position:{start:2070,end:2079},stack:[{type:"Twig.expression.type.variable",value:"max",match:["max"],position:{start:2070,end:2079}}]},{type:"raw",value:'"',position:{start:2079,end:2080}}]},position:{open:{start:1975,end:2001},close:{start:2080,end:2091}}},{type:"raw",value:" ",position:{start:2091,end:2092}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:2092,end:2106},output:[{type:"raw",value:'aria-label="',position:{start:2106,end:2118}},{type:"output",position:{start:2118,end:2129},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:2118,end:2129}}]},{type:"raw",value:'"',position:{start:2129,end:2130}}]},position:{open:{start:2092,end:2106},close:{start:2130,end:2141}}},{type:"raw",value:`>
    <div class="ps-progress__track">
      <div class="ps-progress__fill" `,position:{start:2141,end:2217}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"}],position:{start:2217,end:2243},output:[{type:"raw",value:'style="width: ',position:{start:2243,end:2257}},{type:"output",position:{start:2257,end:2270},stack:[{type:"Twig.expression.type.variable",value:"percent",match:["percent"],position:{start:2257,end:2270}}]},{type:"raw",value:'%;"',position:{start:2270,end:2273}}]},position:{open:{start:2217,end:2243},close:{start:2273,end:2284}}},{type:"raw",value:`></div>
    </div>
    `,position:{start:2284,end:2307}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"showLabel",match:["showLabel"]},{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:2307,end:2347},output:[{type:"raw",value:'      <span class="ps-progress__label">',position:{start:2348,end:2387}},{type:"output",position:{start:2387,end:2400},stack:[{type:"Twig.expression.type.variable",value:"percent",match:["percent"],position:{start:2387,end:2400}}]},{type:"raw",value:`%</span>
    `,position:{start:2400,end:2413}}]},position:{open:{start:2307,end:2347},close:{start:2413,end:2424}}},{type:"raw",value:`  </div>
`,position:{start:2425,end:2434}}]},position:{open:{start:1880,end:1908},close:{start:2434,end:2444}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2434,end:2444},output:[{type:"raw",value:"  ",position:{start:2445,end:2447}},{type:"logic",token:{type:"Twig.logic.type.set",key:"circumference",expression:[{type:"Twig.expression.type.number",value:2,match:["2",null]},{type:"Twig.expression.type.number",value:3.14159,match:["3.14159",".14159"]},{type:"Twig.expression.type.operator.binary",value:"*",precidence:5,associativity:"leftToRight",operator:"*"},{type:"Twig.expression.type.number",value:45,match:["45",null]},{type:"Twig.expression.type.operator.binary",value:"*",precidence:5,associativity:"leftToRight",operator:"*"}],position:{start:2447,end:2489}},position:{start:2447,end:2489}},{type:"raw",value:"  ",position:{start:2490,end:2492}},{type:"logic",token:{type:"Twig.logic.type.set",key:"offset",expression:[{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.number",value:0,match:["0",null]},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"circumference",match:["circumference"]},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.number",value:1,match:["1",null]},{type:"Twig.expression.type.variable",value:"percent",match:["percent"]},{type:"Twig.expression.type.number",value:100,match:["100",null]},{type:"Twig.expression.type.operator.binary",value:"/",precidence:5,associativity:"leftToRight",operator:"/"},{type:"Twig.expression.type.operator.binary",value:"-",precidence:6,associativity:"leftToRight",operator:"-"}]},{type:"Twig.expression.type.operator.binary",value:"*",precidence:5,associativity:"leftToRight",operator:"*"}]},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:2492,end:2568}},position:{start:2492,end:2568}},{type:"raw",value:"  <div ",position:{start:2569,end:2576}},{type:"output",position:{start:2576,end:2615},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:2576,end:2615}},{type:"Twig.expression.type.key.period",position:{start:2576,end:2615},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2576,end:2615},expression:!0,params:[{type:"Twig.expression.type.variable",value:"root_classes",match:["root_classes"],position:{start:2576,end:2615}}]}]},{type:"raw",value:' role="progressbar" ',position:{start:2615,end:2635}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"}],position:{start:2635,end:2661},output:[{type:"raw",value:'aria-valuenow="',position:{start:2661,end:2676}},{type:"output",position:{start:2676,end:2687},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:2676,end:2687}}]},{type:"raw",value:'" aria-valuemin="',position:{start:2687,end:2704}},{type:"output",position:{start:2704,end:2713},stack:[{type:"Twig.expression.type.variable",value:"min",match:["min"],position:{start:2704,end:2713}}]},{type:"raw",value:'" aria-valuemax="',position:{start:2713,end:2730}},{type:"output",position:{start:2730,end:2739},stack:[{type:"Twig.expression.type.variable",value:"max",match:["max"],position:{start:2730,end:2739}}]},{type:"raw",value:'"',position:{start:2739,end:2740}}]},position:{open:{start:2635,end:2661},close:{start:2740,end:2751}}},{type:"raw",value:" ",position:{start:2751,end:2752}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:2752,end:2766},output:[{type:"raw",value:'aria-label="',position:{start:2766,end:2778}},{type:"output",position:{start:2778,end:2789},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:2778,end:2789}}]},{type:"raw",value:'"',position:{start:2789,end:2790}}]},position:{open:{start:2752,end:2766},close:{start:2790,end:2801}}},{type:"raw",value:`>
    <svg class="ps-progress__svg" viewBox="0 0 100 100">
      <circle class="ps-progress__track-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8"></circle>
      <circle class="ps-progress__fill-circle" cx="50" cy="50" r="45" fill="none" stroke-width="8" stroke-dasharray="`,position:{start:2801,end:3087}},{type:"output",position:{start:3087,end:3106},stack:[{type:"Twig.expression.type.variable",value:"circumference",match:["circumference"],position:{start:3087,end:3106}}]},{type:"raw",value:'" stroke-dashoffset="',position:{start:3106,end:3127}},{type:"output",position:{start:3127,end:3139},stack:[{type:"Twig.expression.type.variable",value:"offset",match:["offset"],position:{start:3127,end:3139}}]},{type:"raw",value:`"></circle>
    </svg>
    `,position:{start:3139,end:3166}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"showLabel",match:["showLabel"]},{type:"Twig.expression.type.variable",value:"indeterminate",match:["indeterminate"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:3166,end:3206},output:[{type:"raw",value:'      <span class="ps-progress__label">',position:{start:3207,end:3246}},{type:"output",position:{start:3246,end:3259},stack:[{type:"Twig.expression.type.variable",value:"percent",match:["percent"],position:{start:3246,end:3259}}]},{type:"raw",value:`%</span>
    `,position:{start:3259,end:3272}}]},position:{open:{start:3166,end:3206},close:{start:3272,end:3283}}},{type:"raw",value:`  </div>
`,position:{start:3284,end:3293}}]},position:{open:{start:2434,end:2444},close:{start:3293,end:3304}}}],precompiled:!0});p.options.allowInlineIncludes=!0;try{let r=a.defaultAttributes?a.defaultAttributes:[];return Array.isArray(r)||(r=Object.entries(r)),v(p.render({attributes:new A(r),...a}))}catch(r){return v("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/progress-bar/progress-bar.twig: "+r.toString())}},P={value:60,min:0,max:100,variant:"linear",color:"default",size:"md",indeterminate:!1,striped:!1,showLabel:!0,label:"Property upload in progress"},I={title:"Elements/Progress Bar",tags:["autodocs"],parameters:{docs:{description:{component:`Semantic progress indicator (linear or circular) for task status.
Supports sizes, semantic colors, indeterminate/striped states, and accessible labels.`}}},argTypes:{value:{description:"Current progress value (0-100, percentage of completion)",control:{type:"number",min:0,max:100,step:1},table:{category:"Content",type:{summary:"number"},defaultValue:{summary:"0"}}},min:{description:"Minimum value for progress range",control:{type:"number"},table:{category:"Content",type:{summary:"number"},defaultValue:{summary:"0"}}},max:{description:"Maximum value for progress range",control:{type:"number"},table:{category:"Content",type:{summary:"number"},defaultValue:{summary:"100"}}},variant:{description:"Progress bar type (linear: horizontal bar, circular: ring)",control:{type:"select"},options:["linear","circular"],table:{category:"Appearance",type:{summary:"linear | circular"},defaultValue:{summary:"linear"}}},color:{description:"Semantic color variant (default: neutral gray, others use component variables)",control:{type:"select"},options:["default","primary","secondary","info","warning","success","danger","dark","light"],table:{category:"Appearance",type:{summary:"default | primary | secondary | info | warning | success | danger | dark | light"},defaultValue:{summary:"default"}}},size:{description:"Size variant (xs: 2px/24px, sm: 4px/32px, md: 8px/40px, lg: 12px/48px, xl: 16px/64px, xxl: 24px/80px - linear height / circular diameter)",control:{type:"select"},options:["xs","sm","md","lg","xl","xxl"],table:{category:"Appearance",type:{summary:"xs | sm | md | lg | xl | xxl"},defaultValue:{summary:"md"}}},showLabel:{description:"Display percentage label next to (linear) or inside (circular) progress bar",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:"false"}}},indeterminate:{description:"Indeterminate state with infinite animation (for tasks with unknown duration)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},striped:{description:"Animated diagonal stripes (linear variant only, adds visual feedback)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},label:{description:'Accessibility label for screen readers (e.g., "Upload in progress", "Processing data")',control:{type:"text"},table:{category:"Accessibility",type:{summary:"string"},defaultValue:{summary:""}}}},args:{...P}},t={render:a=>e(a),args:{...P,value:60}},i={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (neutral gray)</p>
        ${e({variant:"linear",color:"default",value:60,showLabel:!0,label:"Property listing progress"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green)</p>
        ${e({variant:"linear",color:"primary",value:60,showLabel:!0,label:"Document upload"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple)</p>
        ${e({variant:"linear",color:"secondary",value:60,showLabel:!0,label:"Virtual tour loading"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (blue) - 85%</p>
        ${e({variant:"linear",color:"info",value:85,showLabel:!0,label:"Property data sync"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (yellow) - 45%</p>
        ${e({variant:"linear",color:"warning",value:45,showLabel:!0,label:"Profile completion"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (green) - 100%</p>
        ${e({variant:"linear",color:"success",value:100,showLabel:!0,label:"Lease agreement signed"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (red) - 15%</p>
        ${e({variant:"linear",color:"danger",value:15,showLabel:!0,label:"Critical: Low storage"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (near black)</p>
        ${e({variant:"linear",color:"dark",value:70,showLabel:!0,label:"Report generation"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Light (near white)</p>
        <div style="background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
          ${e({variant:"linear",color:"light",value:50,showLabel:!0,label:"Image optimization"})}
        </div>
      </div>
    </div>
  `},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (neutral gray) - Striped</p>
        ${e({variant:"linear",color:"default",value:60,showLabel:!0,striped:!0,label:"Property listing progress"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green) - Striped</p>
        ${e({variant:"linear",color:"primary",value:60,showLabel:!0,striped:!0,label:"Document upload"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple) - Striped</p>
        ${e({variant:"linear",color:"secondary",value:60,showLabel:!0,striped:!0,label:"Virtual tour loading"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (blue) - Striped 85%</p>
        ${e({variant:"linear",color:"info",value:85,showLabel:!0,striped:!0,label:"Property data sync"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (yellow) - Striped 45%</p>
        ${e({variant:"linear",color:"warning",value:45,showLabel:!0,striped:!0,label:"Profile completion"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (green) - Striped 100%</p>
        ${e({variant:"linear",color:"success",value:100,showLabel:!0,striped:!0,label:"Lease agreement signed"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (red) - Striped 15%</p>
        ${e({variant:"linear",color:"danger",value:15,showLabel:!0,striped:!0,label:"Critical: Low storage"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (near black) - Striped</p>
        ${e({variant:"linear",color:"dark",value:70,showLabel:!0,striped:!0,label:"Report generation"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Light (near white) - Striped</p>
        <div style="background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
          ${e({variant:"linear",color:"light",value:50,showLabel:!0,striped:!0,label:"Image optimization"})}
        </div>
      </div>
    </div>
  `},o={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Linear Sizes</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XS (2px height)</p>
            ${e({variant:"linear",size:"xs",value:60,showLabel:!0,color:"primary",label:"Compact view"})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">SM (4px height)</p>
            ${e({variant:"linear",size:"sm",value:60,showLabel:!0,color:"primary",label:"Small progress"})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">MD (8px height, default)</p>
            ${e({variant:"linear",size:"md",value:60,showLabel:!0,color:"primary",label:"Standard size"})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">LG (12px height)</p>
            ${e({variant:"linear",size:"lg",value:60,showLabel:!0,color:"primary",label:"Large display"})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XL (16px height)</p>
            ${e({variant:"linear",size:"xl",value:60,showLabel:!0,color:"primary",label:"Extra large"})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XXL (24px height)</p>
            ${e({variant:"linear",size:"xxl",value:60,showLabel:!0,color:"primary",label:"Hero display"})}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular Sizes</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center; flex-wrap: wrap;">
          <div style="text-align: center;">
            ${e({variant:"circular",size:"xs",value:60,showLabel:!0,color:"primary",label:"XS 24px"})}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XS (24px)</span>
          </div>
          <div style="text-align: center;">
            ${e({variant:"circular",size:"sm",value:60,showLabel:!0,color:"primary",label:"SM 32px"})}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">SM (32px)</span>
          </div>
          <div style="text-align: center;">
            ${e({variant:"circular",size:"md",value:60,showLabel:!0,color:"primary",label:"MD 40px"})}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">MD (40px)</span>
          </div>
          <div style="text-align: center;">
            ${e({variant:"circular",size:"lg",value:60,showLabel:!0,color:"primary",label:"LG 48px"})}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">LG (48px)</span>
          </div>
          <div style="text-align: center;">
            ${e({variant:"circular",size:"xl",value:60,showLabel:!0,color:"primary",label:"XL 64px"})}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XL (64px)</span>
          </div>
          <div style="text-align: center;">
            ${e({variant:"circular",size:"xxl",value:60,showLabel:!0,color:"primary",label:"XXL 80px"})}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XXL (80px)</span>
          </div>
        </div>
      </div>
    </div>
  `},n={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Linear States</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Determinate (60%)</p>
            ${e({variant:"linear",value:60,color:"primary",showLabel:!0})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Striped (animated diagonal stripes)</p>
            ${e({variant:"linear",striped:!0,animated:!0,color:"info",value:60,showLabel:!0})}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate (infinite animation)</p>
            ${e({variant:"linear",indeterminate:!0,color:"primary",label:"Processing"})}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular States</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Determinate</p>
            ${e({variant:"circular",value:75,color:"success",size:"lg",showLabel:!0})}
          </div>
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate</p>
            ${e({variant:"circular",indeterminate:!0,color:"primary",size:"lg"})}
          </div>
        </div>
      </div>
    </div>
  `},l={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Document Upload</h3>
        ${e({variant:"linear",value:45,color:"info",showLabel:!0,label:"Uploading floor plans and contracts"})}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Processing Lease Application (Indeterminate)</h3>
        ${e({variant:"linear",indeterminate:!0,color:"primary",label:"Processing tenant application data"})}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Profile Completion Status</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${e({variant:"circular",value:33,color:"warning",size:"lg",showLabel:!0,label:"Agent profile 33% complete"})}
          <span>Complete your agent profile</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Tour Video Loading</h3>
        ${e({variant:"linear",striped:!0,animated:!0,value:70,color:"primary",showLabel:!0,label:"Loading 3D virtual tour"})}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Multi-Step Lease Process</h3>
        <div style="display: flex; gap: var(--size-4); align-items: center;">
          ${e({variant:"circular",value:100,color:"success",size:"md",showLabel:!0,label:"Step 1: Identity verified"})}
          <span style="font-size: var(--font-size-1);">Identity</span>
          ${e({variant:"circular",value:66,color:"info",size:"md",showLabel:!0,label:"Step 2: Documents in progress"})}
          <span style="font-size: var(--font-size-1);">Documents</span>
          ${e({variant:"circular",value:0,color:"default",size:"md",showLabel:!1,label:"Step 3: Payment pending"})}
          <span style="font-size: var(--font-size-1);">Payment</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Critical: Storage Quota Low</h3>
        ${e({variant:"linear",value:92,color:"danger",size:"lg",showLabel:!0,label:"Storage usage: 92% - upgrade needed"})}
      </div>
    </div>
  `};var y,c,d;t.parameters={...t.parameters,docs:{...(y=t.parameters)==null?void 0:y.docs,source:{originalSource:`{
  render: args => progressBarTwig(args),
  args: {
    ...data,
    value: 60
  }
}`,...(d=(c=t.parameters)==null?void 0:c.docs)==null?void 0:d.source}}};var g,u,m;i.parameters={...i.parameters,docs:{...(g=i.parameters)==null?void 0:g.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (neutral gray)</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'default',
    value: 60,
    showLabel: true,
    label: 'Property listing progress'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green)</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'primary',
    value: 60,
    showLabel: true,
    label: 'Document upload'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple)</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'secondary',
    value: 60,
    showLabel: true,
    label: 'Virtual tour loading'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (blue) - 85%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'info',
    value: 85,
    showLabel: true,
    label: 'Property data sync'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (yellow) - 45%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'warning',
    value: 45,
    showLabel: true,
    label: 'Profile completion'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (green) - 100%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'success',
    value: 100,
    showLabel: true,
    label: 'Lease agreement signed'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (red) - 15%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'danger',
    value: 15,
    showLabel: true,
    label: 'Critical: Low storage'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (near black)</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'dark',
    value: 70,
    showLabel: true,
    label: 'Report generation'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Light (near white)</p>
        <div style="background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
          \${progressBarTwig({
    variant: 'linear',
    color: 'light',
    value: 50,
    showLabel: true,
    label: 'Image optimization'
  })}
        </div>
      </div>
    </div>
  \`
}`,...(m=(u=i.parameters)==null?void 0:u.docs)==null?void 0:m.source}}};var f,w,b;s.parameters={...s.parameters,docs:{...(f=s.parameters)==null?void 0:f.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (neutral gray) - Striped</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'default',
    value: 60,
    showLabel: true,
    striped: true,
    label: 'Property listing progress'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green) - Striped</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'primary',
    value: 60,
    showLabel: true,
    striped: true,
    label: 'Document upload'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple) - Striped</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'secondary',
    value: 60,
    showLabel: true,
    striped: true,
    label: 'Virtual tour loading'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (blue) - Striped 85%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'info',
    value: 85,
    showLabel: true,
    striped: true,
    label: 'Property data sync'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (yellow) - Striped 45%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'warning',
    value: 45,
    showLabel: true,
    striped: true,
    label: 'Profile completion'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (green) - Striped 100%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'success',
    value: 100,
    showLabel: true,
    striped: true,
    label: 'Lease agreement signed'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (red) - Striped 15%</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'danger',
    value: 15,
    showLabel: true,
    striped: true,
    label: 'Critical: Low storage'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (near black) - Striped</p>
        \${progressBarTwig({
    variant: 'linear',
    color: 'dark',
    value: 70,
    showLabel: true,
    striped: true,
    label: 'Report generation'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Light (near white) - Striped</p>
        <div style="background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
          \${progressBarTwig({
    variant: 'linear',
    color: 'light',
    value: 50,
    showLabel: true,
    striped: true,
    label: 'Image optimization'
  })}
        </div>
      </div>
    </div>
  \`
}`,...(b=(w=s.parameters)==null?void 0:w.docs)==null?void 0:b.source}}};var z,x,h;o.parameters={...o.parameters,docs:{...(z=o.parameters)==null?void 0:z.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Linear Sizes</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XS (2px height)</p>
            \${progressBarTwig({
    variant: 'linear',
    size: 'xs',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'Compact view'
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">SM (4px height)</p>
            \${progressBarTwig({
    variant: 'linear',
    size: 'sm',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'Small progress'
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">MD (8px height, default)</p>
            \${progressBarTwig({
    variant: 'linear',
    size: 'md',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'Standard size'
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">LG (12px height)</p>
            \${progressBarTwig({
    variant: 'linear',
    size: 'lg',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'Large display'
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XL (16px height)</p>
            \${progressBarTwig({
    variant: 'linear',
    size: 'xl',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'Extra large'
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XXL (24px height)</p>
            \${progressBarTwig({
    variant: 'linear',
    size: 'xxl',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'Hero display'
  })}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular Sizes</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center; flex-wrap: wrap;">
          <div style="text-align: center;">
            \${progressBarTwig({
    variant: 'circular',
    size: 'xs',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'XS 24px'
  })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XS (24px)</span>
          </div>
          <div style="text-align: center;">
            \${progressBarTwig({
    variant: 'circular',
    size: 'sm',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'SM 32px'
  })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">SM (32px)</span>
          </div>
          <div style="text-align: center;">
            \${progressBarTwig({
    variant: 'circular',
    size: 'md',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'MD 40px'
  })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">MD (40px)</span>
          </div>
          <div style="text-align: center;">
            \${progressBarTwig({
    variant: 'circular',
    size: 'lg',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'LG 48px'
  })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">LG (48px)</span>
          </div>
          <div style="text-align: center;">
            \${progressBarTwig({
    variant: 'circular',
    size: 'xl',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'XL 64px'
  })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XL (64px)</span>
          </div>
          <div style="text-align: center;">
            \${progressBarTwig({
    variant: 'circular',
    size: 'xxl',
    value: 60,
    showLabel: true,
    color: 'primary',
    label: 'XXL 80px'
  })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XXL (80px)</span>
          </div>
        </div>
      </div>
    </div>
  \`
}`,...(h=(x=o.parameters)==null?void 0:x.docs)==null?void 0:h.source}}};var T,L,k;n.parameters={...n.parameters,docs:{...(T=n.parameters)==null?void 0:T.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Linear States</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Determinate (60%)</p>
            \${progressBarTwig({
    variant: 'linear',
    value: 60,
    color: 'primary',
    showLabel: true
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Striped (animated diagonal stripes)</p>
            \${progressBarTwig({
    variant: 'linear',
    striped: true,
    animated: true,
    color: 'info',
    value: 60,
    showLabel: true
  })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate (infinite animation)</p>
            \${progressBarTwig({
    variant: 'linear',
    indeterminate: true,
    color: 'primary',
    label: 'Processing'
  })}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular States</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Determinate</p>
            \${progressBarTwig({
    variant: 'circular',
    value: 75,
    color: 'success',
    size: 'lg',
    showLabel: true
  })}
          </div>
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate</p>
            \${progressBarTwig({
    variant: 'circular',
    indeterminate: true,
    color: 'primary',
    size: 'lg'
  })}
          </div>
        </div>
      </div>
    </div>
  \`
}`,...(k=(L=n.parameters)==null?void 0:L.docs)==null?void 0:k.source}}};var $,S,B;l.parameters={...l.parameters,docs:{...($=l.parameters)==null?void 0:$.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Document Upload</h3>
        \${progressBarTwig({
    variant: 'linear',
    value: 45,
    color: 'info',
    showLabel: true,
    label: 'Uploading floor plans and contracts'
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Processing Lease Application (Indeterminate)</h3>
        \${progressBarTwig({
    variant: 'linear',
    indeterminate: true,
    color: 'primary',
    label: 'Processing tenant application data'
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Profile Completion Status</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          \${progressBarTwig({
    variant: 'circular',
    value: 33,
    color: 'warning',
    size: 'lg',
    showLabel: true,
    label: 'Agent profile 33% complete'
  })}
          <span>Complete your agent profile</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Tour Video Loading</h3>
        \${progressBarTwig({
    variant: 'linear',
    striped: true,
    animated: true,
    value: 70,
    color: 'primary',
    showLabel: true,
    label: 'Loading 3D virtual tour'
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Multi-Step Lease Process</h3>
        <div style="display: flex; gap: var(--size-4); align-items: center;">
          \${progressBarTwig({
    variant: 'circular',
    value: 100,
    color: 'success',
    size: 'md',
    showLabel: true,
    label: 'Step 1: Identity verified'
  })}
          <span style="font-size: var(--font-size-1);">Identity</span>
          \${progressBarTwig({
    variant: 'circular',
    value: 66,
    color: 'info',
    size: 'md',
    showLabel: true,
    label: 'Step 2: Documents in progress'
  })}
          <span style="font-size: var(--font-size-1);">Documents</span>
          \${progressBarTwig({
    variant: 'circular',
    value: 0,
    color: 'default',
    size: 'md',
    showLabel: false,
    label: 'Step 3: Payment pending'
  })}
          <span style="font-size: var(--font-size-1);">Payment</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Critical: Storage Quota Low</h3>
        \${progressBarTwig({
    variant: 'linear',
    value: 92,
    color: 'danger',
    size: 'lg',
    showLabel: true,
    label: 'Storage usage: 92% - upgrade needed'
  })}
      </div>
    </div>
  \`
}`,...(B=(S=l.parameters)==null?void 0:S.docs)==null?void 0:B.source}}};const M=["Default","AllColors","AllStriped","AllSizes","AllStates","UseCases"];export{i as AllColors,o as AllSizes,n as AllStates,s as AllStriped,t as Default,l as UseCases,M as __namedExportsOrder,I as default};
