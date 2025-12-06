import{t as l,T as p}from"./iframe-DeCmpQ6I.js";import{D as g,a as u}from"./twig-Cbw8xbjJ.js";u(p);p.cache(!1);const n=e=>e,c=(e={})=>{const r=l.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/language-selector/language-selector.twig",data:[{type:"raw",value:`\r
<div class="ps-language-selector" data-language-selector>\r
  <button class="ps-language-selector__trigger" aria-haspopup="listbox" aria-label="Language selection">\r
    `,position:{start:49,end:220}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"current",match:["current"]}],position:{start:220,end:236},output:[{type:"raw",value:`\r
      <span class="ps-language-selector__current">\r
        `,position:{start:236,end:298}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"lang",expression:[{type:"Twig.expression.type.variable",value:"languages",match:["languages"]}],position:{start:298,end:325},output:[{type:"raw",value:`\r
          `,position:{start:325,end:337}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"lang",match:["lang"]},{type:"Twig.expression.type.key.period",key:"code"},{type:"Twig.expression.type.variable",value:"current",match:["current"]},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:337,end:366},output:[{type:"output",position:{start:366,end:382},stack:[{type:"Twig.expression.type.variable",value:"lang",match:["lang"],position:{start:366,end:382}},{type:"Twig.expression.type.key.period",position:{start:366,end:382},key:"label"}]}]},position:{open:{start:337,end:366},close:{start:382,end:393}}},{type:"raw",value:`\r
        `,position:{start:393,end:403}}]},position:{open:{start:298,end:325},close:{start:403,end:415}}},{type:"raw",value:`\r
      </span>\r
    `,position:{start:415,end:436}}]},position:{open:{start:220,end:236},close:{start:436,end:447}}},{type:"raw",value:`\r
    <svg class="ps-language-selector__icon" aria-hidden="true" width="16" height="16">\r
      <use href="#icon-chevron-down"></use>\r
    </svg>\r
  </button>\r
  <ul class="ps-language-selector__menu" role="listbox" hidden>\r
    `,position:{start:447,end:676}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"lang",expression:[{type:"Twig.expression.type.variable",value:"languages",match:["languages"]}],position:{start:676,end:703},output:[{type:"raw",value:`\r
      <li>\r
        <a href="#" class="ps-language-selector__option`,position:{start:703,end:772}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"lang",match:["lang"]},{type:"Twig.expression.type.key.period",key:"code"},{type:"Twig.expression.type.variable",value:"current",match:["current"]},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:772,end:801},output:[{type:"raw",value:" ps-language-selector__option--active",position:{start:801,end:838}}]},position:{open:{start:772,end:801},close:{start:838,end:849}}},{type:"raw",value:'" data-lang="',position:{start:849,end:862}},{type:"output",position:{start:862,end:877},stack:[{type:"Twig.expression.type.variable",value:"lang",match:["lang"],position:{start:862,end:877}},{type:"Twig.expression.type.key.period",position:{start:862,end:877},key:"code"}]},{type:"raw",value:`">\r
          `,position:{start:877,end:891}},{type:"output",position:{start:891,end:907},stack:[{type:"Twig.expression.type.variable",value:"lang",match:["lang"],position:{start:891,end:907}},{type:"Twig.expression.type.key.period",position:{start:891,end:907},key:"label"}]},{type:"raw",value:`\r
        </a>\r
      </li>\r
    `,position:{start:907,end:940}}]},position:{open:{start:676,end:703},close:{start:940,end:952}}},{type:"raw",value:`\r
  </ul>\r
</div>\r
`,position:{start:952,end:952}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),n(r.render({attributes:new g(t),...e}))}catch(t){return n("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/language-selector/language-selector.twig: "+t.toString())}},y={languages:[{code:"fr",label:"Français",active:!0},{code:"en",label:"English"},{code:"de",label:"Deutsch"}],current:"fr"},v={title:"Components/Language Selector",tags:["autodocs"],argTypes:{languages:{control:{type:"object"},description:"Array of language options"},current:{control:{type:"text"},description:"Current language code"}}},a={name:"Language Selector",render:e=>c(e),args:{...y}};var s,o,i;a.parameters={...a.parameters,docs:{...(s=a.parameters)==null?void 0:s.docs,source:{originalSource:`{
  name: 'Language Selector',
  render: args => markup(args),
  args: {
    ...data
  }
}`,...(i=(o=a.parameters)==null?void 0:o.docs)==null?void 0:i.source}}};const m=["Default"];export{a as Default,m as __namedExportsOrder,v as default};
