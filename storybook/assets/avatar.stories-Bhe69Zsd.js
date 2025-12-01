import{t as P,T as N}from"./iframe-BXCbAV1K.js";import{D as Q,a as X}from"./twig-CSYqopkt.js";X(N);N.cache(!1);const h=e=>e,t=(e={})=>{const i=P.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/avatar/avatar.twig",data:[{type:"raw",value:"",position:{start:570,end:572}},{type:"logic",token:{type:"Twig.logic.type.set",key:"size",expression:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:572,end:609}},position:{start:572,end:609}},{type:"logic",token:{type:"Twig.logic.type.set",key:"shape",expression:[{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"circle"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:610,end:653}},position:{start:610,end:653}},{type:"logic",token:{type:"Twig.logic.type.set",key:"bordered",expression:[{type:"Twig.expression.type.variable",value:"bordered",match:["bordered"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:654,end:700}},position:{start:654,end:700}},{type:"logic",token:{type:"Twig.logic.type.set",key:"clickable",expression:[{type:"Twig.expression.type.variable",value:"clickable",match:["clickable"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:701,end:749}},position:{start:701,end:749}},{type:"raw",value:"",position:{start:750,end:751}},{type:"logic",token:{type:"Twig.logic.type.set",key:"has_image",expression:[{type:"Twig.expression.type.variable",value:"src",match:["src"]},{type:"Twig.expression.type.variable",value:"src",match:["src"]},{type:"Twig.expression.type.test",filter:"empty",modifier:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:751,end:799}},position:{start:751,end:799}},{type:"logic",token:{type:"Twig.logic.type.set",key:"has_initials",expression:[{type:"Twig.expression.type.variable",value:"initials",match:["initials"]},{type:"Twig.expression.type.variable",value:"initials",match:["initials"]},{type:"Twig.expression.type.test",filter:"empty",modifier:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"},{type:"Twig.expression.type.variable",value:"has_image",match:["has_image"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:800,end:879}},position:{start:800,end:879}},{type:"logic",token:{type:"Twig.logic.type.set",key:"has_icon",expression:[{type:"Twig.expression.type.variable",value:"has_image",match:["has_image"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.variable",value:"has_initials",match:["has_initials"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:880,end:937}},position:{start:880,end:937}},{type:"logic",token:{type:"Twig.logic.type.set",key:"gender",expression:[{type:"Twig.expression.type.variable",value:"gender",match:["gender"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"male"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:938,end:981}},position:{start:938,end:981}},{type:"raw",value:"",position:{start:982,end:983}},{type:"logic",token:{type:"Twig.logic.type.set",key:"wrapper_classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar-wrapper"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:983,end:1034}},position:{start:983,end:1034}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="}],position:{start:1035,end:1058},output:[{type:"raw",value:"",position:{start:1059,end:1061}},{type:"logic",token:{type:"Twig.logic.type.set",key:"wrapper_classes",expression:[{type:"Twig.expression.type.variable",value:"wrapper_classes",match:["wrapper_classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar-wrapper--"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1061,end:1144}},position:{start:1061,end:1144}}]},position:{open:{start:1035,end:1058},close:{start:1145,end:1158}}},{type:"raw",value:"",position:{start:1159,end:1160}},{type:"logic",token:{type:"Twig.logic.type.set",key:"avatar_classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1160,end:1202}},position:{start:1160,end:1202}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.string",value:"circle"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="}],position:{start:1203,end:1231},output:[{type:"raw",value:"",position:{start:1232,end:1234}},{type:"logic",token:{type:"Twig.logic.type.set",key:"avatar_classes",expression:[{type:"Twig.expression.type.variable",value:"avatar_classes",match:["avatar_classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar--"},{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1234,end:1308}},position:{start:1234,end:1308}}]},position:{open:{start:1203,end:1231},close:{start:1309,end:1322}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"has_initials",match:["has_initials"]}],position:{start:1323,end:1346},output:[{type:"raw",value:"",position:{start:1347,end:1349}},{type:"logic",token:{type:"Twig.logic.type.set",key:"avatar_classes",expression:[{type:"Twig.expression.type.variable",value:"avatar_classes",match:["avatar_classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar--initials"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1349,end:1423}},position:{start:1349,end:1423}}]},position:{open:{start:1323,end:1346},close:{start:1424,end:1437}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"has_icon",match:["has_icon"]}],position:{start:1438,end:1457},output:[{type:"raw",value:"",position:{start:1458,end:1460}},{type:"logic",token:{type:"Twig.logic.type.set",key:"avatar_classes",expression:[{type:"Twig.expression.type.variable",value:"avatar_classes",match:["avatar_classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar--icon"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1460,end:1530}},position:{start:1460,end:1530}}]},position:{open:{start:1438,end:1457},close:{start:1531,end:1544}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"bordered",match:["bordered"]}],position:{start:1545,end:1564},output:[{type:"raw",value:"",position:{start:1565,end:1567}},{type:"logic",token:{type:"Twig.logic.type.set",key:"avatar_classes",expression:[{type:"Twig.expression.type.variable",value:"avatar_classes",match:["avatar_classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar--bordered"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1567,end:1641}},position:{start:1567,end:1641}}]},position:{open:{start:1545,end:1564},close:{start:1642,end:1655}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"clickable",match:["clickable"]}],position:{start:1656,end:1676},output:[{type:"raw",value:"",position:{start:1677,end:1679}},{type:"logic",token:{type:"Twig.logic.type.set",key:"avatar_classes",expression:[{type:"Twig.expression.type.variable",value:"avatar_classes",match:["avatar_classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-avatar--clickable"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1679,end:1754}},position:{start:1679,end:1754}}]},position:{open:{start:1656,end:1676},close:{start:1755,end:1768}}},{type:"raw",value:"",position:{start:1769,end:1770}},{type:"logic",token:{type:"Twig.logic.type.set",key:"tag",expression:[{type:"Twig.expression.type.variable",value:"href",match:["href"]},{type:"Twig.expression.type.string",value:"a"},{type:"Twig.expression.type.string",value:"div"},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:1770,end:1806}},position:{start:1770,end:1806}},{type:"raw",value:'<div class="',position:{start:1807,end:1820}},{type:"output",position:{start:1820,end:1856},stack:[{type:"Twig.expression.type.variable",value:"wrapper_classes",match:["wrapper_classes"],position:{start:1820,end:1856}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:1820,end:1856},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:1820,end:1856}},{type:"Twig.expression.type.string",value:" ",position:{start:1820,end:1856}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:1820,end:1856},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:1820,end:1856}}]},{type:"raw",value:'"',position:{start:1856,end:1857}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:1857,end:1877},output:[{type:"raw",value:" ",position:{start:1877,end:1878}},{type:"output",position:{start:1878,end:1894},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:1878,end:1894}}]}]},position:{open:{start:1857,end:1877},close:{start:1894,end:1906}}},{type:"raw",value:`>
  <`,position:{start:1906,end:1911}},{type:"output",position:{start:1911,end:1920},stack:[{type:"Twig.expression.type.variable",value:"tag",match:["tag"],position:{start:1911,end:1920}}]},{type:"raw",value:' class="',position:{start:1920,end:1928}},{type:"output",position:{start:1928,end:1963},stack:[{type:"Twig.expression.type.variable",value:"avatar_classes",match:["avatar_classes"],position:{start:1928,end:1963}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:1928,end:1963},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:1928,end:1963}},{type:"Twig.expression.type.string",value:" ",position:{start:1928,end:1963}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:1928,end:1963},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:1928,end:1963}}]},{type:"raw",value:'"',position:{start:1963,end:1969}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"href",match:["href"]}],position:{start:1969,end:1983},output:[{type:"raw",value:'href="',position:{start:1983,end:1989}},{type:"output",position:{start:1989,end:1999},stack:[{type:"Twig.expression.type.variable",value:"href",match:["href"],position:{start:1989,end:1999}}]},{type:"raw",value:'"',position:{start:1999,end:2e3}}]},position:{open:{start:1969,end:1983},close:{start:2e3,end:2012}}},{type:"raw",value:">",position:{start:2013,end:2021}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"has_image",match:["has_image"]}],position:{start:2021,end:2041},output:[{type:"raw",value:'<img class="ps-avatar__image" src="',position:{start:2042,end:2083}},{type:"output",position:{start:2083,end:2092},stack:[{type:"Twig.expression.type.variable",value:"src",match:["src"],position:{start:2083,end:2092}}]},{type:"raw",value:'" alt="',position:{start:2092,end:2099}},{type:"output",position:{start:2099,end:2120},stack:[{type:"Twig.expression.type.variable",value:"alt",match:["alt"],position:{start:2099,end:2120}},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],position:{start:2099,end:2120},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2099,end:2120}},{type:"Twig.expression.type.string",value:"",position:{start:2099,end:2120}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2099,end:2120},expression:!1}]}]},{type:"raw",value:'" loading="lazy" />',position:{start:2120,end:2144}}]},position:{open:{start:2021,end:2041},close:{start:2144,end:2171}}},{type:"logic",token:{type:"Twig.logic.type.elseif",stack:[{type:"Twig.expression.type.variable",value:"has_initials",match:["has_initials"]}],position:{start:2144,end:2171},output:[{type:"raw",value:'<span class="ps-avatar__text">',position:{start:2172,end:2208}},{type:"output",position:{start:2208,end:2222},stack:[{type:"Twig.expression.type.variable",value:"initials",match:["initials"],position:{start:2208,end:2222}}]},{type:"raw",value:"</span>",position:{start:2222,end:2234}}]},position:{open:{start:2144,end:2171},close:{start:2234,end:2246}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2234,end:2246},output:[{type:"raw",value:'<span class="ps-avatar__icon" data-agent="',position:{start:2247,end:2295}},{type:"output",position:{start:2295,end:2339},stack:[{type:"Twig.expression.type.variable",value:"gender",match:["gender"],position:{start:2295,end:2339}},{type:"Twig.expression.type.string",value:"female",position:{start:2295,end:2339}},{type:"Twig.expression.type.operator.binary",value:"==",position:{start:2295,end:2339},precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.string",value:"female",position:{start:2295,end:2339}},{type:"Twig.expression.type.string",value:"male",position:{start:2295,end:2339}},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:2295,end:2339},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:'" aria-hidden="true"></span>',position:{start:2339,end:2372}}]},position:{open:{start:2234,end:2246},close:{start:2372,end:2385}}},{type:"raw",value:"</",position:{start:2386,end:2390}},{type:"output",position:{start:2390,end:2399},stack:[{type:"Twig.expression.type.variable",value:"tag",match:["tag"],position:{start:2390,end:2399}}]},{type:"raw",value:">",position:{start:2399,end:2406}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"status",match:["status"]}],position:{start:2406,end:2423},output:[{type:"raw",value:'<span class="ps-avatar__status" data-status="',position:{start:2424,end:2473}},{type:"output",position:{start:2473,end:2485},stack:[{type:"Twig.expression.type.variable",value:"status",match:["status"],position:{start:2473,end:2485}}]},{type:"raw",value:'" aria-label="',position:{start:2485,end:2499}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"status",match:["status"]},{type:"Twig.expression.type.string",value:"online"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2499,end:2526},output:[{type:"raw",value:"Online",position:{start:2526,end:2532}}]},position:{open:{start:2499,end:2526},close:{start:2532,end:2561}}},{type:"logic",token:{type:"Twig.logic.type.elseif",stack:[{type:"Twig.expression.type.variable",value:"status",match:["status"]},{type:"Twig.expression.type.string",value:"busy"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:2532,end:2561},output:[{type:"raw",value:"Busy",position:{start:2561,end:2565}}]},position:{open:{start:2532,end:2561},close:{start:2565,end:2575}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:2565,end:2575},output:[{type:"raw",value:"Offline",position:{start:2575,end:2582}}]},position:{open:{start:2565,end:2575},close:{start:2582,end:2593}}},{type:"raw",value:'"></span>',position:{start:2593,end:2605}}]},position:{open:{start:2406,end:2423},close:{start:2605,end:2618}}},{type:"raw",value:"</div>",position:{start:2619,end:2619}}],precompiled:!0});i.options.allowInlineIncludes=!0;try{let s=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(s)||(s=Object.entries(s)),h(i.render({attributes:new Q(s),...e}))}catch(s){return h("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/avatar/avatar.twig: "+s.toString())}},T={src:"https://i.pravatar.cc/150?img=12",alt:"John Doe",initials:"",size:"md",shape:"circle",status:"",bordered:!1,clickable:!1,href:"",gender:"male"},ee={title:"Elements/Avatar",tags:["autodocs"],render:e=>t(e),args:T,parameters:{docs:{description:{component:"User or entity visual representation with automatic fallback hierarchy (image → initials → icon)."}}},argTypes:{src:{control:"text",description:"Avatar image URL",table:{category:"Content",type:{summary:"string"}}},initials:{control:"text",description:"Initials text (2 letters, fallback if no image)",table:{category:"Content",type:{summary:"string"}}},size:{control:"select",options:["xs","sm","md","lg","xl"],description:"Avatar size",table:{category:"Appearance",type:{summary:"xs | sm | md | lg | xl"},defaultValue:{summary:"md"}}},shape:{control:"select",options:["circle","square","rounded"],description:"Border radius style",table:{category:"Appearance",type:{summary:"circle | square | rounded"},defaultValue:{summary:"circle"}}},status:{control:"select",options:["online","offline","busy"],description:"Status badge indicator",table:{category:"Appearance",type:{summary:"online | offline | busy"}}},bordered:{control:"boolean",description:"White border",table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},gender:{control:"select",options:["male","female"],description:"Icon gender variant",table:{category:"Appearance",type:{summary:"male | female"},defaultValue:{summary:"male"}}},clickable:{control:"boolean",description:"Interactive states",table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},href:{control:"text",description:"Link URL",table:{category:"Link",type:{summary:"string"}}},alt:{control:"text",description:"Image alt text",table:{category:"Accessibility",type:{summary:"string"}}}}},p={args:{...T}},n={render:()=>{const e=["xs","sm","md","lg","xl"],i=["circle","square","rounded"],s=e.length+1,v=['<div class="cell cell--label"></div>',...e.map(a=>`<div class="cell cell--label">${a.toUpperCase()}</div>`)].join(""),m=i.map(a=>{const w=`<div class="cell cell--label">${a}</div>`,x=e.map(r=>`<div class="cell">${t({src:"",initials:"JD",size:r,shape:a,alt:`Initials ${a} ${r}`})}</div>`).join("");return w+x}).join("");return`
      <div style="display:grid;grid-template-columns:repeat(${s}, auto);gap:12px;align-items:center;">
        <style>
          .cell--label{font:600 var(--font-size-0)/1 var(--font-sans);color:var(--gray-600);text-transform:capitalize}
          .cell{display:flex;align-items:center;justify-content:center}
        </style>
        ${v}
        ${m}
      </div>
    `}},o={args:{...T,src:"",initials:"",size:"xs",gender:"female"}},l={render:()=>`
    <div style="display:flex;gap:1rem;align-items:center;">
      ${t({src:"https://i.pravatar.cc/150?img=12",status:"online",size:"lg",alt:"Online"})}
      ${t({src:"https://i.pravatar.cc/150?img=12",status:"offline",size:"lg",alt:"Offline"})}
      ${t({src:"https://i.pravatar.cc/150?img=12",status:"busy",size:"lg",alt:"Busy"})}
    </div>
  `},c={render:()=>`
    <div style="display:flex;gap:1rem;align-items:center;">
      ${["xs","sm","md","lg","xl"].map(e=>t({src:"https://i.pravatar.cc/150?img=12",size:e,alt:e.toUpperCase()})).join("")}
    </div>
  `},y={render:()=>`
    <div style="display:flex;gap:1rem;align-items:center;">
      ${["circle","square","rounded"].map(e=>t({src:"https://i.pravatar.cc/150?img=47",shape:e,size:"lg",alt:e})).join("")}
    </div>
  `},g={render:()=>`
    <div style="display:flex;gap:1rem;align-items:center;">
      ${t({src:"https://i.pravatar.cc/150?img=47",size:"lg",alt:"Image"})}
      ${t({initials:"JD",src:"",size:"lg",alt:"Initials"})}
      ${t({src:"",initials:"",size:"lg",alt:"Icon Fallback"})}
    </div>
  `},d={render:()=>`
    <div style="display:flex;gap:1rem;align-items:center;">
      ${["xs","sm","md","lg","xl"].map(e=>t({src:"https://i.pravatar.cc/150?img=18",shape:"rounded",size:e,alt:`Rounded ${e}`})).join("")}
    </div>
  `},u={render:()=>{const e=["online","offline","busy"],i=["xs","sm","md","lg","xl"],s=i.length+1,v=['<div class="cell cell--label"></div>',...i.map(a=>`<div class="cell cell--label">${a.toUpperCase()}</div>`)].join(""),m=e.map(a=>{const w=`<div class="cell cell--label">${a}</div>`,x=i.map(r=>`<div class="cell">${t({src:"https://i.pravatar.cc/150?img=12",status:a,size:r,alt:`${a} ${r}`})}</div>`).join("");return w+x}).join("");return`
      <div style="display:grid;grid-template-columns:repeat(${s}, auto);gap:12px;align-items:center;">
        <style>
          .cell--label{font:600 var(--font-size-0)/1 var(--font-sans);color:var(--gray-600);text-transform:capitalize}
          .cell{display:flex;align-items:center;justify-content:center}
        </style>
        ${v}
        ${m}
      </div>
    `}};var f,b,k;p.parameters={...p.parameters,docs:{...(f=p.parameters)==null?void 0:f.docs,source:{originalSource:`{
  args: {
    ...data
  }
}`,...(k=(b=p.parameters)==null?void 0:b.docs)==null?void 0:k.source}}};var z,_,$;n.parameters={...n.parameters,docs:{...(z=n.parameters)==null?void 0:z.docs,source:{originalSource:`{
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const shapes = ['circle', 'square', 'rounded'];
    const gridCols = sizes.length + 1;
    const headRow = ['<div class="cell cell--label"></div>', ...sizes.map(s => \`<div class="cell cell--label">\${s.toUpperCase()}</div>\`)].join('');
    const rows = shapes.map(shape => {
      const label = \`<div class=\\"cell cell--label\\">\${shape}</div>\`;
      const cells = sizes.map(s => \`<div class=\\"cell\\">\${avatarTwig({
        src: '',
        initials: 'JD',
        size: s,
        shape,
        alt: \`Initials \${shape} \${s}\`
      })}</div>\`).join('');
      return label + cells;
    }).join('');
    return \`
      <div style="display:grid;grid-template-columns:repeat(\${gridCols}, auto);gap:12px;align-items:center;">
        <style>
          .cell--label{font:600 var(--font-size-0)/1 var(--font-sans);color:var(--gray-600);text-transform:capitalize}
          .cell{display:flex;align-items:center;justify-content:center}
        </style>
        \${headRow}
        \${rows}
      </div>
    \`;
  }
}`,...($=(_=n.parameters)==null?void 0:_.docs)==null?void 0:$.source}}};var j,R,S;o.parameters={...o.parameters,docs:{...(j=o.parameters)==null?void 0:j.docs,source:{originalSource:`{
  args: {
    ...data,
    src: '',
    initials: '',
    size: 'xs',
    gender: 'female'
  }
}`,...(S=(R=o.parameters)==null?void 0:R.docs)==null?void 0:S.source}}};var A,I,C;l.parameters={...l.parameters,docs:{...(A=l.parameters)==null?void 0:A.docs,source:{originalSource:`{
  render: () => \`
    <div style="display:flex;gap:1rem;align-items:center;">
      \${avatarTwig({
    src: 'https://i.pravatar.cc/150?img=12',
    status: 'online',
    size: 'lg',
    alt: 'Online'
  })}
      \${avatarTwig({
    src: 'https://i.pravatar.cc/150?img=12',
    status: 'offline',
    size: 'lg',
    alt: 'Offline'
  })}
      \${avatarTwig({
    src: 'https://i.pravatar.cc/150?img=12',
    status: 'busy',
    size: 'lg',
    alt: 'Busy'
  })}
    </div>
  \`
}`,...(C=(I=l.parameters)==null?void 0:I.docs)==null?void 0:C.source}}};var D,L,U;c.parameters={...c.parameters,docs:{...(D=c.parameters)==null?void 0:D.docs,source:{originalSource:`{
  render: () => \`
    <div style="display:flex;gap:1rem;align-items:center;">
      \${['xs', 'sm', 'md', 'lg', 'xl'].map(s => avatarTwig({
    src: 'https://i.pravatar.cc/150?img=12',
    size: s,
    alt: s.toUpperCase()
  })).join('')}
    </div>
  \`
}`,...(U=(L=c.parameters)==null?void 0:L.docs)==null?void 0:U.source}}};var O,V,q;y.parameters={...y.parameters,docs:{...(O=y.parameters)==null?void 0:O.docs,source:{originalSource:`{
  render: () => \`
    <div style="display:flex;gap:1rem;align-items:center;">
      \${['circle', 'square', 'rounded'].map(shape => avatarTwig({
    src: 'https://i.pravatar.cc/150?img=47',
    shape,
    size: 'lg',
    alt: shape
  })).join('')}
    </div>
  \`
}`,...(q=(V=y.parameters)==null?void 0:V.docs)==null?void 0:q.source}}};var B,J,E;g.parameters={...g.parameters,docs:{...(B=g.parameters)==null?void 0:B.docs,source:{originalSource:`{
  render: () => \`
    <div style="display:flex;gap:1rem;align-items:center;">
      \${avatarTwig({
    src: 'https://i.pravatar.cc/150?img=47',
    size: 'lg',
    alt: 'Image'
  })}
      \${avatarTwig({
    initials: 'JD',
    src: '',
    size: 'lg',
    alt: 'Initials'
  })}
      \${avatarTwig({
    src: '',
    initials: '',
    size: 'lg',
    alt: 'Icon Fallback'
  })}
    </div>
  \`
}`,...(E=(J=g.parameters)==null?void 0:J.docs)==null?void 0:E.source}}};var F,M,W;d.parameters={...d.parameters,docs:{...(F=d.parameters)==null?void 0:F.docs,source:{originalSource:`{
  render: () => \`
    <div style="display:flex;gap:1rem;align-items:center;">
      \${['xs', 'sm', 'md', 'lg', 'xl'].map(s => avatarTwig({
    src: 'https://i.pravatar.cc/150?img=18',
    shape: 'rounded',
    size: s,
    alt: \`Rounded \${s}\`
  })).join('')}
    </div>
  \`
}`,...(W=(M=d.parameters)==null?void 0:M.docs)==null?void 0:W.source}}};var G,H,K;u.parameters={...u.parameters,docs:{...(G=u.parameters)==null?void 0:G.docs,source:{originalSource:`{
  render: () => {
    const statuses = ['online', 'offline', 'busy'];
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
    const gridCols = sizes.length + 1;
    const headRow = ['<div class="cell cell--label"></div>', ...sizes.map(s => \`<div class="cell cell--label">\${s.toUpperCase()}</div>\`)].join('');
    const rows = statuses.map(status => {
      const label = \`<div class=\\"cell cell--label\\">\${status}</div>\`;
      const cells = sizes.map(s => \`<div class=\\"cell\\">\${avatarTwig({
        src: 'https://i.pravatar.cc/150?img=12',
        status,
        size: s,
        alt: \`\${status} \${s}\`
      })}</div>\`).join('');
      return label + cells;
    }).join('');
    return \`
      <div style="display:grid;grid-template-columns:repeat(\${gridCols}, auto);gap:12px;align-items:center;">
        <style>
          .cell--label{font:600 var(--font-size-0)/1 var(--font-sans);color:var(--gray-600);text-transform:capitalize}
          .cell{display:flex;align-items:center;justify-content:center}
        </style>
        \${headRow}
        \${rows}
      </div>
    \`;
  }
}`,...(K=(H=u.parameters)==null?void 0:H.docs)==null?void 0:K.source}}};const te=["Default","Initials","FallbackIcon","StatusVariants","AllSizes","AllShapes","Modes","RoundedScaling","StatusMatrix"];export{y as AllShapes,c as AllSizes,p as Default,o as FallbackIcon,n as Initials,g as Modes,d as RoundedScaling,u as StatusMatrix,l as StatusVariants,te as __namedExportsOrder,ee as default};
