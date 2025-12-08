import{t as h,T as b}from"./iframe-B-yX16js.js";import{D as T,a as f}from"./twig-CgICq6Dc.js";f(b);b.cache(!1);const p=e=>e,i=(e={})=>{const o=h.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/menu-item/menu-item.twig",data:[{type:"raw",value:`\r
<li class="ps-menu-item`,position:{start:39,end:64}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"active",match:["active"]}],position:{start:64,end:79},output:[{type:"raw",value:" ps-menu-item--active",position:{start:79,end:100}}]},position:{open:{start:64,end:79},close:{start:100,end:111}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:111,end:128},output:[{type:"raw",value:" ps-menu-item--disabled",position:{start:128,end:151}}]},position:{open:{start:111,end:128},close:{start:151,end:162}}},{type:"raw",value:`">\r
  <a href="`,position:{start:162,end:177}},{type:"output",position:{start:177,end:187},stack:[{type:"Twig.expression.type.variable",value:"href",match:["href"],position:{start:177,end:187}}]},{type:"raw",value:'" class="ps-menu-item__link" ',position:{start:187,end:216}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:216,end:233},output:[{type:"raw",value:'aria-disabled="true"',position:{start:233,end:253}}]},position:{open:{start:216,end:233},close:{start:253,end:264}}},{type:"raw",value:`>\r
    `,position:{start:264,end:271}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]}],position:{start:271,end:284},output:[{type:"raw",value:`\r
      <svg class="ps-menu-item__icon" aria-hidden="true" width="20" height="20">\r
        <use href="#icon-`,position:{start:284,end:393}},{type:"output",position:{start:393,end:403},stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"],position:{start:393,end:403}}]},{type:"raw",value:`"></use>\r
      </svg>\r
    `,position:{start:403,end:431}}]},position:{open:{start:271,end:284},close:{start:431,end:442}}},{type:"raw",value:`\r
    <span class="ps-menu-item__label">`,position:{start:442,end:482}},{type:"output",position:{start:482,end:493},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:482,end:493}}]},{type:"raw",value:`</span>\r
    `,position:{start:493,end:506}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"badge",match:["badge"]}],position:{start:506,end:520},output:[{type:"raw",value:`\r
      <span class="ps-menu-item__badge">`,position:{start:520,end:562}},{type:"output",position:{start:562,end:573},stack:[{type:"Twig.expression.type.variable",value:"badge",match:["badge"],position:{start:562,end:573}}]},{type:"raw",value:`</span>\r
    `,position:{start:573,end:586}}]},position:{open:{start:506,end:520},close:{start:586,end:597}}},{type:"raw",value:`\r
  </a>\r
  `,position:{start:597,end:609}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"submenu",match:["submenu"]},{type:"Twig.expression.type.variable",value:"submenu",match:["submenu"]},{type:"Twig.expression.type.filter",value:"length",match:["|length","length"]},{type:"Twig.expression.type.number",value:0,match:["0",null]},{type:"Twig.expression.type.operator.binary",value:">",precidence:8,associativity:"leftToRight",operator:">"},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:609,end:648},output:[{type:"raw",value:`\r
    <ul class="ps-menu-item__submenu">\r
      `,position:{start:648,end:696}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"submenu",match:["submenu"]}],position:{start:696,end:721},output:[{type:"raw",value:`\r
        <li><a href="`,position:{start:721,end:744}},{type:"output",position:{start:744,end:759},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:744,end:759}},{type:"Twig.expression.type.key.period",position:{start:744,end:759},key:"href"}]},{type:"raw",value:'">',position:{start:759,end:761}},{type:"output",position:{start:761,end:777},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:761,end:777}},{type:"Twig.expression.type.key.period",position:{start:761,end:777},key:"label"}]},{type:"raw",value:`</a></li>\r
      `,position:{start:777,end:794}}]},position:{open:{start:696,end:721},close:{start:794,end:806}}},{type:"raw",value:`\r
    </ul>\r
  `,position:{start:806,end:821}}]},position:{open:{start:609,end:648},close:{start:821,end:832}}},{type:"raw",value:`\r
</li>\r
`,position:{start:832,end:832}}],precompiled:!0});o.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),p(o.render({attributes:new T(t),...e}))}catch(t){return p("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/menu-item/menu-item.twig: "+t.toString())}},r={label:"Menu Item",href:"#",active:!1,disabled:!1,icon:null,badge:null,submenu:[]},_={title:"Components/Menu Item",tags:["autodocs"]},a={name:"Menu Item",render:e=>`<ul>${i(e)}</ul>`,args:{...r}},s={name:"Active State",render:e=>`<ul>${i({...e,active:!0})}</ul>`,args:{...r}},n={name:"With Icon",render:e=>`<ul>${i({...e,icon:"home"})}</ul>`,args:{...r}};var u,l,c;a.parameters={...a.parameters,docs:{...(u=a.parameters)==null?void 0:u.docs,source:{originalSource:`{
  name: 'Menu Item',
  render: args => \`<ul>\${markup(args)}</ul>\`,
  args: {
    ...data
  }
}`,...(c=(l=a.parameters)==null?void 0:l.docs)==null?void 0:c.source}}};var d,y,m;s.parameters={...s.parameters,docs:{...(d=s.parameters)==null?void 0:d.docs,source:{originalSource:`{
  name: 'Active State',
  render: args => \`<ul>\${markup({
    ...args,
    active: true
  })}</ul>\`,
  args: {
    ...data
  }
}`,...(m=(y=s.parameters)==null?void 0:y.docs)==null?void 0:m.source}}};var g,v,w;n.parameters={...n.parameters,docs:{...(g=n.parameters)==null?void 0:g.docs,source:{originalSource:`{
  name: 'With Icon',
  render: args => \`<ul>\${markup({
    ...args,
    icon: 'home'
  })}</ul>\`,
  args: {
    ...data
  }
}`,...(w=(v=n.parameters)==null?void 0:v.docs)==null?void 0:w.source}}};const A=["Default","Active","WithIcon"];export{s as Active,a as Default,n as WithIcon,A as __namedExportsOrder,_ as default};
