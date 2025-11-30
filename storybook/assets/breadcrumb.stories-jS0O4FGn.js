import{t as V,T as N}from"./iframe-wV_yutGI.js";import{D,a as j}from"./twig--ZzzhHos.js";import"./icon-BnjtxcgP.js";import"https://kit.fontawesome.com/a0eb0bad75.js";j(N);N.cache(!1);V.twig({id:"@elements/icon/icon.twig",data:[{type:"raw",value:`\r
\r
`,position:{start:512,end:516}},{type:"logic",token:{type:"Twig.logic.type.set",key:"name",expression:[{type:"Twig.expression.type.variable",value:"name",match:["name"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"search"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:516,end:555}},position:{start:516,end:555}},{type:"raw",value:`\r
`,position:{start:555,end:557}},{type:"logic",token:{type:"Twig.logic.type.set",key:"size",expression:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"medium"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:557,end:596}},position:{start:557,end:596}},{type:"raw",value:`\r
`,position:{start:596,end:598}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:598,end:642}},position:{start:598,end:642}},{type:"raw",value:`\r
\r
`,position:{start:642,end:646}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:646,end:668}},position:{start:646,end:668}},{type:"raw",value:`\r
`,position:{start:668,end:670}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"icon-"},{type:"Twig.expression.type.variable",value:"name",match:["name"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:670,end:721}},position:{start:670,end:721}},{type:"raw",value:`\r
`,position:{start:721,end:723}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"size",match:["size"]}],position:{start:723,end:736},output:[{type:"raw",value:`\r
  `,position:{start:736,end:740}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-icon--"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:740,end:795}},position:{start:740,end:795}},{type:"raw",value:`\r
`,position:{start:795,end:797}}]},position:{open:{start:723,end:736},close:{start:797,end:808}}},{type:"raw",value:`\r
`,position:{start:808,end:810}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:810,end:827},output:[{type:"raw",value:`\r
  `,position:{start:827,end:831}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-icon--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:831,end:887}},position:{start:831,end:887}},{type:"raw",value:`\r
`,position:{start:887,end:889}}]},position:{open:{start:810,end:827},close:{start:889,end:900}}},{type:"raw",value:`\r
\r
<i\r
  class="`,position:{start:900,end:917}},{type:"output",position:{start:917,end:945},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:917,end:945}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:917,end:945},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:917,end:945}},{type:"Twig.expression.type.string",value:" ",position:{start:917,end:945}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:917,end:945},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:917,end:945}}]},{type:"raw",value:`"\r
  `,position:{start:945,end:950}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:950,end:969},output:[{type:"output",position:{start:969,end:985},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:969,end:985}}]}]},position:{open:{start:950,end:969},close:{start:985,end:996}}},{type:"raw",value:`\r
  `,position:{start:996,end:1e3}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"color",match:["color"]}],position:{start:1e3,end:1014},output:[{type:"raw",value:'style="color: ',position:{start:1014,end:1028}},{type:"output",position:{start:1028,end:1039},stack:[{type:"Twig.expression.type.variable",value:"color",match:["color"],position:{start:1028,end:1039}}]},{type:"raw",value:';"',position:{start:1039,end:1041}}]},position:{open:{start:1e3,end:1014},close:{start:1041,end:1052}}},{type:"raw",value:`\r
  `,position:{start:1052,end:1056}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"ariaLabel",match:["ariaLabel"]}],position:{start:1056,end:1074},output:[{type:"raw",value:'aria-label="',position:{start:1074,end:1086}},{type:"output",position:{start:1086,end:1101},stack:[{type:"Twig.expression.type.variable",value:"ariaLabel",match:["ariaLabel"],position:{start:1086,end:1101}}]},{type:"raw",value:'" role="img"',position:{start:1101,end:1113}}]},position:{open:{start:1056,end:1074},close:{start:1113,end:1123}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:1113,end:1123},output:[{type:"raw",value:'aria-hidden="true"',position:{start:1123,end:1141}}]},position:{open:{start:1113,end:1123},close:{start:1141,end:1152}}},{type:"raw",value:`\r
></i>\r
`,position:{start:1152,end:1152}}],precompiled:!0});const c=t=>t,e=(t={})=>{const y=V.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/breadcrumb/breadcrumb.twig",data:[{type:"raw",value:`\r
\r
`,position:{start:447,end:451}},{type:"logic",token:{type:"Twig.logic.type.set",key:"compact",expression:[{type:"Twig.expression.type.variable",value:"compact",match:["compact"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:451,end:493}},position:{start:451,end:493}},{type:"raw",value:`\r
`,position:{start:493,end:495}},{type:"logic",token:{type:"Twig.logic.type.set",key:"truncate",expression:[{type:"Twig.expression.type.variable",value:"truncate",match:["truncate"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:495,end:539}},position:{start:495,end:539}},{type:"raw",value:`\r
\r
`,position:{start:539,end:543}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-breadcrumb"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:543,end:580}},position:{start:543,end:580}},{type:"raw",value:`\r
`,position:{start:580,end:582}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"compact",match:["compact"]}],position:{start:582,end:598},output:[{type:"raw",value:`\r
  `,position:{start:598,end:602}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-breadcrumb--compact"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:602,end:663}},position:{start:602,end:663}},{type:"raw",value:`\r
`,position:{start:663,end:665}}]},position:{open:{start:582,end:598},close:{start:665,end:676}}},{type:"raw",value:`\r
`,position:{start:676,end:678}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"truncate",match:["truncate"]}],position:{start:678,end:695},output:[{type:"raw",value:`\r
  `,position:{start:695,end:699}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-breadcrumb--truncate"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:699,end:761}},position:{start:699,end:761}},{type:"raw",value:`\r
`,position:{start:761,end:763}}]},position:{open:{start:678,end:695},close:{start:763,end:774}}},{type:"raw",value:`\r
\r
<nav `,position:{start:774,end:783}},{type:"output",position:{start:783,end:869},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:783,end:869}},{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:783,end:869}},{type:"Twig.expression.type.key.period",position:{start:783,end:869},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:783,end:869},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:783,end:869}}]},{type:"Twig.expression.type._function",position:{start:783,end:869},fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:783,end:869}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:783,end:869},expression:!1}]},{type:"Twig.expression.type.key.period",position:{start:783,end:869},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:783,end:869},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:783,end:869}}]},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:783,end:869},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:` aria-label="Breadcrumb">\r
  <ol class="ps-breadcrumb__list">\r
    `,position:{start:869,end:936}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],position:{start:936,end:959},output:[{type:"raw",value:`\r
      `,position:{start:959,end:967}},{type:"logic",token:{type:"Twig.logic.type.set",key:"is_last",expression:[{type:"Twig.expression.type.variable",value:"loop",match:["loop"]},{type:"Twig.expression.type.key.period",key:"last"}],position:{start:967,end:996}},position:{start:967,end:996}},{type:"raw",value:`\r
      \r
      `,position:{start:996,end:1012}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"is_last",match:["is_last"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"}],position:{start:1012,end:1032},output:[{type:"raw",value:`\r
        <li class="ps-breadcrumb__item">\r
          `,position:{start:1032,end:1086}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"url"}],position:{start:1086,end:1103},output:[{type:"raw",value:`\r
            <a class="ps-breadcrumb__link" href="`,position:{start:1103,end:1154}},{type:"output",position:{start:1154,end:1168},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1154,end:1168}},{type:"Twig.expression.type.key.period",position:{start:1154,end:1168},key:"url"}]},{type:"raw",value:`">\r
              `,position:{start:1168,end:1186}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"icon"}],position:{start:1186,end:1204},output:[{type:"raw",value:`\r
                `,position:{start:1204,end:1222}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/icon/icon.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"name"},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"icon"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"size"},{type:"Twig.expression.type.string",value:"small"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:1222,end:1362}},position:{start:1222,end:1362}},{type:"raw",value:`\r
              `,position:{start:1362,end:1378}}]},position:{open:{start:1186,end:1204},close:{start:1378,end:1389}}},{type:"raw",value:`\r
              `,position:{start:1389,end:1405}},{type:"output",position:{start:1405,end:1421},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1405,end:1421}},{type:"Twig.expression.type.key.period",position:{start:1405,end:1421},key:"label"}]},{type:"raw",value:`\r
            </a>\r
          `,position:{start:1421,end:1451}}]},position:{open:{start:1086,end:1103},close:{start:1451,end:1461}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:1451,end:1461},output:[{type:"raw",value:`\r
            <span class="ps-breadcrumb__link">\r
              `,position:{start:1461,end:1525}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"icon"}],position:{start:1525,end:1543},output:[{type:"raw",value:`\r
                `,position:{start:1543,end:1561}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/icon/icon.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"name"},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"icon"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"size"},{type:"Twig.expression.type.string",value:"small"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:1561,end:1701}},position:{start:1561,end:1701}},{type:"raw",value:`\r
              `,position:{start:1701,end:1717}}]},position:{open:{start:1525,end:1543},close:{start:1717,end:1728}}},{type:"raw",value:`\r
              `,position:{start:1728,end:1744}},{type:"output",position:{start:1744,end:1760},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1744,end:1760}},{type:"Twig.expression.type.key.period",position:{start:1744,end:1760},key:"label"}]},{type:"raw",value:`\r
            </span>\r
          `,position:{start:1760,end:1793}}]},position:{open:{start:1451,end:1461},close:{start:1793,end:1804}}},{type:"raw",value:`\r
        </li>\r
        <li class="ps-breadcrumb__separator" aria-hidden="true">›</li>\r
      `,position:{start:1804,end:1899}}]},position:{open:{start:1012,end:1032},close:{start:1899,end:1909}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:1899,end:1909},output:[{type:"raw",value:`\r
        <li class="ps-breadcrumb__item ps-breadcrumb__item--current" aria-current="page">\r
          <span class="ps-breadcrumb__current">`,position:{start:1909,end:2049}},{type:"output",position:{start:2049,end:2065},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:2049,end:2065}},{type:"Twig.expression.type.key.period",position:{start:2049,end:2065},key:"label"}]},{type:"raw",value:`</span>\r
        </li>\r
      `,position:{start:2065,end:2095}}]},position:{open:{start:1899,end:1909},close:{start:2095,end:2106}}},{type:"raw",value:`\r
    `,position:{start:2106,end:2112}}]},position:{open:{start:936,end:959},close:{start:2112,end:2124}}},{type:"raw",value:`\r
  </ol>\r
</nav>\r
`,position:{start:2124,end:2124}}],precompiled:!0});y.options.allowInlineIncludes=!0;try{let a=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),c(y.render({attributes:new D(a),...t}))}catch(a){return c("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/breadcrumb/breadcrumb.twig: "+a.toString())}},I={items:[{label:"Home",url:"/"},{label:"Locations",url:"/locations"},{label:"Paris 15e",url:"/locations/paris-15"},{label:"Family Apartment"}],compact:!1,truncate:!1},R={title:"Components/Breadcrumb",tags:["autodocs"],parameters:{docs:{description:{component:"Navigation trail showing page hierarchy in site structure. Enhances SEO and UX with semantic markup, accessible states, and keyboard navigation support."}}},argTypes:{items:{description:"Array of breadcrumb items with label, optional url, and optional icon",control:{type:"object"},table:{category:"Content",type:{summary:"array<{label: string, url?: string, icon?: string}>"}}},compact:{description:"Enable compact spacing (reduced font size and gaps)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},truncate:{description:"Enable CSS text truncation for long labels",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}}}},r={render:t=>e(t),args:{...I}},s={render:()=>e({items:[{label:"Home",url:"/",icon:"home"},{label:"Locations",url:"/locations",icon:"map"},{label:"Paris 15e",url:"/locations/paris-15",icon:"building"},{label:"Family Apartment"}]})},i={render:()=>e({items:[{label:"Home",url:"/"},{label:"Products",url:"/products"},{label:"Electronics",url:"/products/electronics"},{label:"Smartphones"}],compact:!0})},o={render:()=>e({items:[{label:"Home",url:"/"},{label:"Very Long Category Name That Should Be Truncated",url:"/category"},{label:"Another Extremely Long Subcategory Name",url:"/category/subcategory"},{label:"Final Item with Very Long Name"}],truncate:!0})},n={render:()=>e({items:[{label:"Home",url:"/"},{label:"Current Page"}]})},p={render:()=>e({items:[{label:"Home",url:"/"},{label:"Real Estate",url:"/real-estate"},{label:"Commercial",url:"/real-estate/commercial"},{label:"Offices",url:"/real-estate/commercial/offices"},{label:"Paris",url:"/real-estate/commercial/offices/paris"},{label:"8th District",url:"/real-estate/commercial/offices/paris/8th"},{label:"Champs-Élysées Building"}]})},l={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Standard</h3>
        ${e({items:[{label:"Home",url:"/"},{label:"Locations",url:"/locations"},{label:"Paris 15e"}]})}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Compact</h3>
        ${e({items:[{label:"Home",url:"/"},{label:"Locations",url:"/locations"},{label:"Paris 15e"}],compact:!0})}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">With Icons</h3>
        ${e({items:[{label:"Home",url:"/",icon:"home"},{label:"Products",url:"/products",icon:"grid"},{label:"Laptop"}]})}
      </div>
      <div style="max-width: 400px; border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Truncated (narrow container)</h3>
        ${e({items:[{label:"Home",url:"/"},{label:"Long Category Name",url:"/category"},{label:"Very Long Subcategory Name"}],truncate:!0})}
      </div>
    </div>
  `};var u,m,d;r.parameters={...r.parameters,docs:{...(u=r.parameters)==null?void 0:u.docs,source:{originalSource:`{
  render: args => breadcrumbTwig(args),
  args: {
    ...breadcrumbData
  }
}`,...(d=(m=r.parameters)==null?void 0:m.docs)==null?void 0:d.source}}};var g,v,w;s.parameters={...s.parameters,docs:{...(g=s.parameters)==null?void 0:g.docs,source:{originalSource:`{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/',
      icon: 'home'
    }, {
      label: 'Locations',
      url: '/locations',
      icon: 'map'
    }, {
      label: 'Paris 15e',
      url: '/locations/paris-15',
      icon: 'building'
    }, {
      label: 'Family Apartment'
    }]
  })
}`,...(w=(v=s.parameters)==null?void 0:v.docs)==null?void 0:w.source}}};var b,T,x;i.parameters={...i.parameters,docs:{...(b=i.parameters)==null?void 0:b.docs,source:{originalSource:`{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Products',
      url: '/products'
    }, {
      label: 'Electronics',
      url: '/products/electronics'
    }, {
      label: 'Smartphones'
    }],
    compact: true
  })
}`,...(x=(T=i.parameters)==null?void 0:T.docs)==null?void 0:x.source}}};var h,f,k;o.parameters={...o.parameters,docs:{...(h=o.parameters)==null?void 0:h.docs,source:{originalSource:`{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Very Long Category Name That Should Be Truncated',
      url: '/category'
    }, {
      label: 'Another Extremely Long Subcategory Name',
      url: '/category/subcategory'
    }, {
      label: 'Final Item with Very Long Name'
    }],
    truncate: true
  })
}`,...(k=(f=o.parameters)==null?void 0:f.docs)==null?void 0:k.source}}};var z,L,S;n.parameters={...n.parameters,docs:{...(z=n.parameters)==null?void 0:z.docs,source:{originalSource:`{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Current Page'
    }]
  })
}`,...(S=(L=n.parameters)==null?void 0:L.docs)==null?void 0:S.source}}};var _,C,H;p.parameters={...p.parameters,docs:{...(_=p.parameters)==null?void 0:_.docs,source:{originalSource:`{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Real Estate',
      url: '/real-estate'
    }, {
      label: 'Commercial',
      url: '/real-estate/commercial'
    }, {
      label: 'Offices',
      url: '/real-estate/commercial/offices'
    }, {
      label: 'Paris',
      url: '/real-estate/commercial/offices/paris'
    }, {
      label: '8th District',
      url: '/real-estate/commercial/offices/paris/8th'
    }, {
      label: 'Champs-Élysées Building'
    }]
  })
}`,...(H=(C=p.parameters)==null?void 0:C.docs)==null?void 0:H.source}}};var A,P,E;l.parameters={...l.parameters,docs:{...(A=l.parameters)==null?void 0:A.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Standard</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Locations',
      url: '/locations'
    }, {
      label: 'Paris 15e'
    }]
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Compact</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Locations',
      url: '/locations'
    }, {
      label: 'Paris 15e'
    }],
    compact: true
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">With Icons</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/',
      icon: 'home'
    }, {
      label: 'Products',
      url: '/products',
      icon: 'grid'
    }, {
      label: 'Laptop'
    }]
  })}
      </div>
      <div style="max-width: 400px; border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Truncated (narrow container)</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Long Category Name',
      url: '/category'
    }, {
      label: 'Very Long Subcategory Name'
    }],
    truncate: true
  })}
      </div>
    </div>
  \`
}`,...(E=(P=l.parameters)==null?void 0:P.docs)==null?void 0:E.source}}};const W=["Default","WithIcons","Compact","Truncated","Simple","Deep","ShowcaseVariants"];export{i as Compact,p as Deep,r as Default,l as ShowcaseVariants,n as Simple,o as Truncated,s as WithIcons,W as __namedExportsOrder,R as default};
