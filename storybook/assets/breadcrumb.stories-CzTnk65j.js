import{t as V,T as N}from"./iframe-BXCbAV1K.js";import{D,a as I}from"./twig-CSYqopkt.js";import"./icon-Dre3-avY.js";I(N);N.cache(!1);V.twig({id:"@elements/icon/icon.twig",data:[{type:"raw",value:`\r
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
      <li class="ps-breadcrumb__item`,position:{start:996,end:1042}},{type:"output",position:{start:1042,end:1094},stack:[{type:"Twig.expression.type.variable",value:"is_last",match:["is_last"],position:{start:1042,end:1094}},{type:"Twig.expression.type.string",value:" ps-breadcrumb__item--current",position:{start:1042,end:1094}},{type:"Twig.expression.type.string",value:"",position:{start:1042,end:1094}},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:1042,end:1094},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:'"',position:{start:1094,end:1095}},{type:"output",position:{start:1095,end:1138},stack:[{type:"Twig.expression.type.variable",value:"is_last",match:["is_last"],position:{start:1095,end:1138}},{type:"Twig.expression.type.string",value:' aria-current="page"',position:{start:1095,end:1138}},{type:"Twig.expression.type.string",value:"",position:{start:1095,end:1138}},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:1095,end:1138},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:`>\r
        `,position:{start:1138,end:1149}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"is_last",match:["is_last"]},{type:"Twig.expression.type.operator.unary",value:"not",precidence:3,associativity:"rightToLeft",operator:"not"},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"url"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:1149,end:1182},output:[{type:"raw",value:`\r
          <a class="ps-breadcrumb__link" href="`,position:{start:1182,end:1231}},{type:"output",position:{start:1231,end:1245},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1231,end:1245}},{type:"Twig.expression.type.key.period",position:{start:1231,end:1245},key:"url"}]},{type:"raw",value:`">\r
            `,position:{start:1245,end:1261}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"icon"}],position:{start:1261,end:1279},output:[{type:"raw",value:`\r
              `,position:{start:1279,end:1295}},{type:"logic",token:{type:"Twig.logic.type.include",only:4,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"@elements/icon/icon.twig"}],withStack:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"name"},{type:"Twig.expression.type.variable",value:"item",match:["item"]},{type:"Twig.expression.type.key.period",key:"icon"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"size"},{type:"Twig.expression.type.string",value:"small"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:1295,end:1429}},position:{start:1295,end:1429}},{type:"raw",value:`\r
            `,position:{start:1429,end:1443}}]},position:{open:{start:1261,end:1279},close:{start:1443,end:1454}}},{type:"raw",value:`\r
            `,position:{start:1454,end:1468}},{type:"output",position:{start:1468,end:1484},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1468,end:1484}},{type:"Twig.expression.type.key.period",position:{start:1468,end:1484},key:"label"}]},{type:"raw",value:`\r
          </a>\r
        `,position:{start:1484,end:1510}}]},position:{open:{start:1149,end:1182},close:{start:1510,end:1520}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:1510,end:1520},output:[{type:"raw",value:`\r
          `,position:{start:1520,end:1532}},{type:"output",position:{start:1532,end:1548},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:1532,end:1548}},{type:"Twig.expression.type.key.period",position:{start:1532,end:1548},key:"label"}]},{type:"raw",value:`\r
        `,position:{start:1548,end:1558}}]},position:{open:{start:1510,end:1520},close:{start:1558,end:1569}}},{type:"raw",value:`\r
      </li>\r
    `,position:{start:1569,end:1588}}]},position:{open:{start:936,end:959},close:{start:1588,end:1600}}},{type:"raw",value:`\r
  </ol>\r
</nav>\r
`,position:{start:1600,end:1600}}],precompiled:!0});y.options.allowInlineIncludes=!0;try{let a=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),c(y.render({attributes:new D(a),...t}))}catch(a){return c("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/breadcrumb/breadcrumb.twig: "+a.toString())}},$={items:[{label:"Home",url:"/"},{label:"Locations",url:"/locations"},{label:"Paris 15e",url:"/locations/paris-15"},{label:"Family Apartment"}],compact:!1,truncate:!1},O={title:"Components/Breadcrumb",tags:["autodocs"],parameters:{docs:{description:{component:"Navigation trail showing page hierarchy in site structure. Enhances SEO and UX with semantic markup, accessible states, and keyboard navigation support."}}},argTypes:{items:{description:"Array of breadcrumb items with label, optional url, and optional icon",control:{type:"object"},table:{category:"Content",type:{summary:"array<{label: string, url?: string, icon?: string}>"}}},compact:{description:"Enable compact spacing (reduced font size and gaps)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},truncate:{description:"Enable CSS text truncation for long labels",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}}}},r={render:t=>e(t),args:{...$}},s={render:()=>e({items:[{label:"Home",url:"/",icon:"home"},{label:"Locations",url:"/locations",icon:"map"},{label:"Paris 15e",url:"/locations/paris-15",icon:"building"},{label:"Family Apartment"}]})},i={render:()=>e({items:[{label:"Home",url:"/"},{label:"Products",url:"/products"},{label:"Electronics",url:"/products/electronics"},{label:"Smartphones"}],compact:!0})},o={render:()=>e({items:[{label:"Home",url:"/"},{label:"Very Long Category Name That Should Be Truncated",url:"/category"},{label:"Another Extremely Long Subcategory Name",url:"/category/subcategory"},{label:"Final Item with Very Long Name"}],truncate:!0})},n={render:()=>e({items:[{label:"Home",url:"/"},{label:"Current Page"}]})},p={render:()=>e({items:[{label:"Home",url:"/"},{label:"Real Estate",url:"/real-estate"},{label:"Commercial",url:"/real-estate/commercial"},{label:"Offices",url:"/real-estate/commercial/offices"},{label:"Paris",url:"/real-estate/commercial/offices/paris"},{label:"8th District",url:"/real-estate/commercial/offices/paris/8th"},{label:"Champs-Élysées Building"}]})},l={render:()=>`
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
}`,...(S=(L=n.parameters)==null?void 0:L.docs)==null?void 0:S.source}}};var C,_,H;p.parameters={...p.parameters,docs:{...(C=p.parameters)==null?void 0:C.docs,source:{originalSource:`{
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
}`,...(H=(_=p.parameters)==null?void 0:_.docs)==null?void 0:H.source}}};var A,P,E;l.parameters={...l.parameters,docs:{...(A=l.parameters)==null?void 0:A.docs,source:{originalSource:`{
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
}`,...(E=(P=l.parameters)==null?void 0:P.docs)==null?void 0:E.source}}};const R=["Default","WithIcons","Compact","Truncated","Simple","Deep","ShowcaseVariants"];export{i as Compact,p as Deep,r as Default,l as ShowcaseVariants,n as Simple,o as Truncated,s as WithIcons,R as __namedExportsOrder,O as default};
