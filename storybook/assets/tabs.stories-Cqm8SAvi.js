import{t as b,T as y}from"./iframe-DeCmpQ6I.js";import{D as v,a as w}from"./twig-Cbw8xbjJ.js";w(y);y.cache(!1);const i=t=>t,u=(t={})=>{const n=b.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/tabs/tabs.twig",data:[{type:"raw",value:`
<div class="ps-tabs">
  <div class="ps-tabs__list" role="tablist">
    `,position:{start:37,end:109}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"tab",expression:[{type:"Twig.expression.type.variable",value:"tabs",match:["tabs"]}],position:{start:109,end:130},output:[{type:"raw",value:'      <button class="ps-tabs__tab ',position:{start:131,end:165}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"]},{type:"Twig.expression.type.key.period",key:"active"}],position:{start:165,end:184},output:[{type:"raw",value:"ps-tabs__tab--active",position:{start:184,end:204}}]},position:{open:{start:165,end:184},close:{start:204,end:215}}},{type:"raw",value:'" role="tab" id="',position:{start:215,end:232}},{type:"output",position:{start:232,end:244},stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"],position:{start:232,end:244}},{type:"Twig.expression.type.key.period",position:{start:232,end:244},key:"id"}]},{type:"raw",value:'" aria-selected="',position:{start:244,end:261}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"]},{type:"Twig.expression.type.key.period",key:"active"}],position:{start:261,end:280},output:[{type:"raw",value:"true",position:{start:280,end:284}}]},position:{open:{start:261,end:280},close:{start:284,end:294}}},{type:"logic",token:{type:"Twig.logic.type.else",match:["else"],position:{start:284,end:294},output:[{type:"raw",value:"false",position:{start:294,end:299}}]},position:{open:{start:284,end:294},close:{start:299,end:310}}},{type:"raw",value:'" aria-controls="',position:{start:310,end:327}},{type:"output",position:{start:327,end:339},stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"],position:{start:327,end:339}},{type:"Twig.expression.type.key.period",position:{start:327,end:339},key:"id"}]},{type:"raw",value:`-panel">
        `,position:{start:339,end:356}},{type:"output",position:{start:356,end:371},stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"],position:{start:356,end:371}},{type:"Twig.expression.type.key.period",position:{start:356,end:371},key:"label"}]},{type:"raw",value:`
      </button>
    `,position:{start:371,end:392}}]},position:{open:{start:109,end:130},close:{start:392,end:404}}},{type:"raw",value:`  </div>
  `,position:{start:405,end:416}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"tab",expression:[{type:"Twig.expression.type.variable",value:"tabs",match:["tabs"]}],position:{start:416,end:437},output:[{type:"raw",value:'    <div class="ps-tabs__panel ',position:{start:438,end:469}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"]},{type:"Twig.expression.type.key.period",key:"active"}],position:{start:469,end:488},output:[{type:"raw",value:"ps-tabs__panel--active",position:{start:488,end:510}}]},position:{open:{start:469,end:488},close:{start:510,end:521}}},{type:"raw",value:'" role="tabpanel" id="',position:{start:521,end:543}},{type:"output",position:{start:543,end:555},stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"],position:{start:543,end:555}},{type:"Twig.expression.type.key.period",position:{start:543,end:555},key:"id"}]},{type:"raw",value:'-panel" aria-labelledby="',position:{start:555,end:580}},{type:"output",position:{start:580,end:592},stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"],position:{start:580,end:592}},{type:"Twig.expression.type.key.period",position:{start:580,end:592},key:"id"}]},{type:"raw",value:`">
      `,position:{start:592,end:601}},{type:"output",position:{start:601,end:618},stack:[{type:"Twig.expression.type.variable",value:"tab",match:["tab"],position:{start:601,end:618}},{type:"Twig.expression.type.key.period",position:{start:601,end:618},key:"content"}]},{type:"raw",value:`
    </div>
  `,position:{start:618,end:632}}]},position:{open:{start:416,end:437},close:{start:632,end:644}}},{type:"raw",value:`</div>
`,position:{start:645,end:645}}],precompiled:!0});n.options.allowInlineIncludes=!0;try{let e=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(e)||(e=Object.entries(e)),i(n.render({attributes:new v(e),...t}))}catch(e){return i("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/tabs/tabs.twig: "+e.toString())}},g={tabs:[{id:"tab1",label:"Overview",content:"Overview content",active:!0},{id:"tab2",label:"Details",content:"Details content",active:!1},{id:"tab3",label:"Reviews",content:"Reviews content",active:!1}]},T={title:"Components/Tabs",tags:["autodocs"],argTypes:{tabs:{description:"Array of tab objects with id, label, content, active",table:{category:"Content"}}}},a={name:"Default",render:t=>u(t),args:{...g}},s={name:"Four Tabs",render:t=>u(t),args:{tabs:[{id:"tab1",label:"Properties",content:"Properties panel",active:!0},{id:"tab2",label:"Details",content:"Details panel",active:!1},{id:"tab3",label:"History",content:"History panel",active:!1},{id:"tab4",label:"Settings",content:"Settings panel",active:!1}]}};var o,r,p;a.parameters={...a.parameters,docs:{...(o=a.parameters)==null?void 0:o.docs,source:{originalSource:`{
  name: 'Default',
  render: args => markup(args),
  args: {
    ...data
  }
}`,...(p=(r=a.parameters)==null?void 0:r.docs)==null?void 0:p.source}}};var l,d,c;s.parameters={...s.parameters,docs:{...(l=s.parameters)==null?void 0:l.docs,source:{originalSource:`{
  name: 'Four Tabs',
  render: args => markup(args),
  args: {
    tabs: [{
      id: 'tab1',
      label: 'Properties',
      content: 'Properties panel',
      active: true
    }, {
      id: 'tab2',
      label: 'Details',
      content: 'Details panel',
      active: false
    }, {
      id: 'tab3',
      label: 'History',
      content: 'History panel',
      active: false
    }, {
      id: 'tab4',
      label: 'Settings',
      content: 'Settings panel',
      active: false
    }]
  }
}`,...(c=(d=s.parameters)==null?void 0:d.docs)==null?void 0:c.source}}};const f=["Default","FourTabs"];export{a as Default,s as FourTabs,f as __namedExportsOrder,T as default};
