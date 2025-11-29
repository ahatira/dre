import{t as W,T as G}from"./iframe-itFLYljW.js";import{D as M,a as z}from"./twig-DayQgIu7.js";import"https://kit.fontawesome.com/a0eb0bad75.js";z(G);G.cache(!1);const y=t=>t,s=(t={})=>{const u=W.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/checkbox/checkbox.twig",data:[{type:"raw",value:"",position:{start:326,end:328}},{type:"logic",token:{type:"Twig.logic.type.set",key:"checked",expression:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:328,end:372}},position:{start:328,end:372}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:373,end:419}},position:{start:373,end:419}},{type:"logic",token:{type:"Twig.logic.type.set",key:"id",expression:[{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.variable",value:"name",match:["name"]},{type:"Twig.expression.type.string",value:"-"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:420,end:467}},position:{start:420,end:467}},{type:"raw",value:"",position:{start:468,end:469}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:469,end:493}},position:{start:469,end:493}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-checkbox"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:494,end:546}},position:{start:494,end:546}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:547,end:566},output:[{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-checkbox--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:566,end:628}},position:{start:566,end:628}}]},position:{open:{start:547,end:566},close:{start:628,end:641}}},{type:"raw",value:'<label class="',position:{start:642,end:657}},{type:"output",position:{start:657,end:685},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:657,end:685}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:657,end:685},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:657,end:685}},{type:"Twig.expression.type.string",value:" ",position:{start:657,end:685}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:657,end:685},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:657,end:685}}]},{type:"raw",value:'" for="',position:{start:685,end:692}},{type:"output",position:{start:692,end:700},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:692,end:700}}]},{type:"raw",value:`">
  <input
    class="ps-checkbox__input"
    type="checkbox"
    id="`,position:{start:700,end:771}},{type:"output",position:{start:771,end:779},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:771,end:779}}]},{type:"raw",value:`"
    name="`,position:{start:779,end:791}},{type:"output",position:{start:791,end:801},stack:[{type:"Twig.expression.type.variable",value:"name",match:["name"],position:{start:791,end:801}}]},{type:"raw",value:`"
    value="`,position:{start:801,end:814}},{type:"output",position:{start:814,end:825},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:814,end:825}}]},{type:"raw",value:'"',position:{start:825,end:831}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]}],position:{start:831,end:848},output:[{type:"raw",value:"checked",position:{start:848,end:855}}]},position:{open:{start:831,end:848},close:{start:855,end:867}}},{type:"raw",value:"",position:{start:868,end:872}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:872,end:890},output:[{type:"raw",value:'disabled aria-disabled="true"',position:{start:890,end:919}}]},position:{open:{start:872,end:890},close:{start:919,end:931}}},{type:"raw",value:`/>
  <span class="ps-checkbox__box" aria-hidden="true">
  </span>`,position:{start:932,end:1002}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:1002,end:1018},output:[{type:"raw",value:'<span class="ps-checkbox__label">',position:{start:1019,end:1056}},{type:"output",position:{start:1056,end:1067},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:1056,end:1067}}]},{type:"raw",value:"</span>",position:{start:1067,end:1077}}]},position:{open:{start:1002,end:1018},close:{start:1077,end:1090}}},{type:"raw",value:"</label>",position:{start:1091,end:1091}}],precompiled:!0});u.options.allowInlineIncludes=!0;try{let a=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),y(u.render({attributes:new M(a),...t}))}catch(a){return y("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/checkbox/checkbox.twig: "+a.toString())}},e={name:"option",value:"1",label:"Option label",checked:!1,disabled:!1,id:""},J={title:"Elements/Checkbox",tags:["autodocs"],render:t=>s(t),args:e,parameters:{docs:{description:{component:"Case à cocher accessible conforme au Design System.\n\n- Contenu: input natif + label optionnel lié par `id`/`for`.\n- États: `checked`, `disabled` — styles et curseur adaptés.\n- Icône: rendu via pseudo-éléments (police d'icônes), sans balise supplémentaire.\n- Accessibilité: cible clavier, focus visible; annonce ARIA native; label recommandé pour la compréhension.\n- Tokens: couleurs, espacements, bordures et typos uniquement via tokens.\n- Marquage minimal: classe de base applique les styles par défaut; modificateurs ajoutés uniquement si nécessaires."}}},argTypes:{name:{control:"text",description:"Input name attribute"},value:{control:"text",description:"Input value"},label:{control:"text",description:"Label text (optional)"},checked:{control:"boolean",description:"Checked state"},disabled:{control:"boolean",description:"Disabled state"},id:{control:"text",description:"Input ID (auto-generated if empty)"}}},r={args:{...e}},i={args:{...e,label:""}},o={args:{...e,checked:!0}},n={args:{...e,label:"",checked:!0}},p={args:{...e,label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit."}},l={args:{...e,disabled:!0}},c={args:{...e,checked:!0,disabled:!0}},d={render:()=>`
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <strong>No label:</strong><br/><br/>
        ${s({name:"group1",value:"1",label:"Option label",checked:!1})}
      </div>
      <div>
        <strong>Label:</strong><br/><br/>
        ${s({name:"group2",value:"1",label:"Option label",checked:!1})}<br/>
        ${s({name:"group2",value:"2",label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.",checked:!1})}
      </div>
      <div>
        <strong>Selected:</strong><br/><br/>
        ${s({name:"group3",value:"1",label:"Option label",checked:!0})}<br/>
        ${s({name:"group3",value:"2",label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.",checked:!0})}
      </div>
    </div>
  `};var m,g,b;r.parameters={...r.parameters,docs:{...(m=r.parameters)==null?void 0:m.docs,source:{originalSource:`{
  args: {
    ...data
  }
}`,...(b=(g=r.parameters)==null?void 0:g.docs)==null?void 0:b.source}}};var v,h,w;i.parameters={...i.parameters,docs:{...(v=i.parameters)==null?void 0:v.docs,source:{originalSource:`{
  args: {
    ...data,
    label: ''
  }
}`,...(w=(h=i.parameters)==null?void 0:h.docs)==null?void 0:w.source}}};var x,k,T;o.parameters={...o.parameters,docs:{...(x=o.parameters)==null?void 0:x.docs,source:{originalSource:`{
  args: {
    ...data,
    checked: true
  }
}`,...(T=(k=o.parameters)==null?void 0:k.docs)==null?void 0:T.source}}};var f,C,L;n.parameters={...n.parameters,docs:{...(f=n.parameters)==null?void 0:f.docs,source:{originalSource:`{
  args: {
    ...data,
    label: '',
    checked: true
  }
}`,...(L=(C=n.parameters)==null?void 0:C.docs)==null?void 0:L.source}}};var D,S,_;p.parameters={...p.parameters,docs:{...(D=p.parameters)==null?void 0:D.docs,source:{originalSource:`{
  args: {
    ...data,
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.'
  }
}`,...(_=(S=p.parameters)==null?void 0:S.docs)==null?void 0:_.source}}};var A,$,O;l.parameters={...l.parameters,docs:{...(A=l.parameters)==null?void 0:A.docs,source:{originalSource:`{
  args: {
    ...data,
    disabled: true
  }
}`,...(O=($=l.parameters)==null?void 0:$.docs)==null?void 0:O.source}}};var I,N,j;c.parameters={...c.parameters,docs:{...(I=c.parameters)==null?void 0:I.docs,source:{originalSource:`{
  args: {
    ...data,
    checked: true,
    disabled: true
  }
}`,...(j=(N=c.parameters)==null?void 0:N.docs)==null?void 0:j.source}}};var q,E,R;d.parameters={...d.parameters,docs:{...(q=d.parameters)==null?void 0:q.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <strong>No label:</strong><br/><br/>
        \${checkbox({
    name: 'group1',
    value: '1',
    label: 'Option label',
    checked: false
  })}
      </div>
      <div>
        <strong>Label:</strong><br/><br/>
        \${checkbox({
    name: 'group2',
    value: '1',
    label: 'Option label',
    checked: false
  })}<br/>
        \${checkbox({
    name: 'group2',
    value: '2',
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.',
    checked: false
  })}
      </div>
      <div>
        <strong>Selected:</strong><br/><br/>
        \${checkbox({
    name: 'group3',
    value: '1',
    label: 'Option label',
    checked: true
  })}<br/>
        \${checkbox({
    name: 'group3',
    value: '2',
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.',
    checked: true
  })}
      </div>
    </div>
  \`
}`,...(R=(E=d.parameters)==null?void 0:E.docs)==null?void 0:R.source}}};const K=["Default","NoLabel","Checked","CheckedNoLabel","WithLongLabel","Disabled","DisabledChecked","Group"];export{o as Checked,n as CheckedNoLabel,r as Default,l as Disabled,c as DisabledChecked,d as Group,i as NoLabel,p as WithLongLabel,K as __namedExportsOrder,J as default};
