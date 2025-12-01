import{t as I,T as z}from"./iframe-BXCbAV1K.js";import{D as j,a as E}from"./twig-CSYqopkt.js";E(z);z.cache(!1);const u=a=>a,e=(a={})=>{const c=I.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/checkbox/checkbox.twig",data:[{type:"raw",value:"",position:{start:326,end:328}},{type:"logic",token:{type:"Twig.logic.type.set",key:"checked",expression:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:328,end:372}},position:{start:328,end:372}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:373,end:419}},position:{start:373,end:419}},{type:"logic",token:{type:"Twig.logic.type.set",key:"id",expression:[{type:"Twig.expression.type.variable",value:"id",match:["id"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.variable",value:"name",match:["name"]},{type:"Twig.expression.type.string",value:"-"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:420,end:467}},position:{start:420,end:467}},{type:"raw",value:"",position:{start:468,end:469}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:469,end:493}},position:{start:469,end:493}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-checkbox"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:494,end:546}},position:{start:494,end:546}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:547,end:566},output:[{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-checkbox--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:566,end:628}},position:{start:566,end:628}}]},position:{open:{start:547,end:566},close:{start:628,end:641}}},{type:"raw",value:'<label class="',position:{start:642,end:657}},{type:"output",position:{start:657,end:685},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:657,end:685}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:657,end:685},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:657,end:685}},{type:"Twig.expression.type.string",value:" ",position:{start:657,end:685}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:657,end:685},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:657,end:685}}]},{type:"raw",value:'" for="',position:{start:685,end:692}},{type:"output",position:{start:692,end:700},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:692,end:700}}]},{type:"raw",value:`">
  <input
    class="ps-checkbox__input"
    type="checkbox"
    id="`,position:{start:700,end:771}},{type:"output",position:{start:771,end:779},stack:[{type:"Twig.expression.type.variable",value:"id",match:["id"],position:{start:771,end:779}}]},{type:"raw",value:`"
    name="`,position:{start:779,end:791}},{type:"output",position:{start:791,end:801},stack:[{type:"Twig.expression.type.variable",value:"name",match:["name"],position:{start:791,end:801}}]},{type:"raw",value:`"
    value="`,position:{start:801,end:814}},{type:"output",position:{start:814,end:825},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:814,end:825}}]},{type:"raw",value:'"',position:{start:825,end:831}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]}],position:{start:831,end:848},output:[{type:"raw",value:"checked",position:{start:848,end:855}}]},position:{open:{start:831,end:848},close:{start:855,end:867}}},{type:"raw",value:"",position:{start:868,end:872}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:872,end:890},output:[{type:"raw",value:'disabled aria-disabled="true"',position:{start:890,end:919}}]},position:{open:{start:872,end:890},close:{start:919,end:931}}},{type:"raw",value:`/>
  <span class="ps-checkbox__box" aria-hidden="true">
  </span>`,position:{start:932,end:1002}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:1002,end:1018},output:[{type:"raw",value:'<span class="ps-checkbox__label">',position:{start:1019,end:1056}},{type:"output",position:{start:1056,end:1067},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:1056,end:1067}}]},{type:"raw",value:"</span>",position:{start:1067,end:1077}}]},position:{open:{start:1002,end:1018},close:{start:1077,end:1090}}},{type:"raw",value:"</label>",position:{start:1091,end:1091}}],precompiled:!0});c.options.allowInlineIncludes=!0;try{let s=a.defaultAttributes?a.defaultAttributes:[];return Array.isArray(s)||(s=Object.entries(s)),u(c.render({attributes:new j(s),...a}))}catch(s){return u("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/checkbox/checkbox.twig: "+s.toString())}},t={name:"option",value:"1",label:"Option label",checked:!1,disabled:!1,id:""},G={title:"Elements/Checkbox",tags:["autodocs"],parameters:{docs:{description:{component:"Native checkbox with accessible label and token-based styling. Supports checked/disabled states, auto ID binding, and focus-visible for keyboard users."}}},argTypes:{name:{control:"text",description:"Input name attribute",table:{category:"Content",type:{summary:"string",required:!0}}},value:{control:"text",description:"Input value",table:{category:"Content",type:{summary:"string",required:!0}}},label:{control:"text",description:"Label text (optional)",table:{category:"Content",type:{summary:"string"}}},id:{control:"text",description:"Input ID (auto-generated if empty)",table:{category:"Appearance",type:{summary:"string"}}},checked:{control:"boolean",description:"Checked state",table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},disabled:{control:"boolean",description:"Disabled state",table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}}}},r={render:a=>e(a),args:{...t}},i={render:()=>e({...t,label:""})},n={render:()=>e({...t,label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit."})},o={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${e({...t,checked:!1,disabled:!1,label:"Unchecked"})}
      ${e({...t,checked:!0,disabled:!1,label:"Checked"})}
      ${e({...t,checked:!1,disabled:!0,label:"Disabled"})}
      ${e({...t,checked:!0,disabled:!0,label:"Checked Disabled"})}
    </div>
  `},l={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${e({...t,label:"Option label"})}
      ${e({...t,label:""})}
      ${e({...t,label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit."})}
    </div>
  `},p={render:()=>`
    <div style="display: grid; gap: var(--size-4); grid-template-columns: repeat(4, minmax(0, 1fr));">
      <div><strong>Checked + Label</strong><br/>${e({...t,checked:!0,disabled:!1,label:"Checked"})}</div>
      <div><strong>Unchecked + Label</strong><br/>${e({...t,checked:!1,disabled:!1,label:"Unchecked"})}</div>
      <div><strong>Checked + NoLabel</strong><br/>${e({...t,checked:!0,disabled:!1,label:""})}</div>
      <div><strong>Unchecked + NoLabel</strong><br/>${e({...t,checked:!1,disabled:!1,label:""})}</div>
      <div><strong>Checked + Disabled + Label</strong><br/>${e({...t,checked:!0,disabled:!0,label:"Checked Disabled"})}</div>
      <div><strong>Unchecked + Disabled + Label</strong><br/>${e({...t,checked:!1,disabled:!0,label:"Disabled"})}</div>
      <div><strong>Checked + Disabled + NoLabel</strong><br/>${e({...t,checked:!0,disabled:!0,label:""})}</div>
      <div><strong>Unchecked + Disabled + NoLabel</strong><br/>${e({...t,checked:!1,disabled:!0,label:""})}</div>
    </div>
  `},d={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <strong>Options:</strong><br/><br/>
        ${e({name:"group1",value:"1",label:"Option label",checked:!1})}
        ${e({name:"group1",value:"2",label:"Option label",checked:!0})}
      </div>
      <div>
        <strong>Long label:</strong><br/><br/>
        ${e({name:"group2",value:"1",label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.",checked:!1})}
        ${e({name:"group2",value:"2",label:"Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.",checked:!0})}
      </div>
    </div>
  `};var b,g,y;r.parameters={...r.parameters,docs:{...(b=r.parameters)==null?void 0:b.docs,source:{originalSource:`{
  render: args => checkboxTwig(args),
  args: {
    ...data
  }
}`,...(y=(g=r.parameters)==null?void 0:g.docs)==null?void 0:y.source}}};var m,v,h;i.parameters={...i.parameters,docs:{...(m=i.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: () => checkboxTwig({
    ...data,
    label: ''
  })
}`,...(h=(v=i.parameters)==null?void 0:v.docs)==null?void 0:h.source}}};var k,w,x;n.parameters={...n.parameters,docs:{...(k=n.parameters)==null?void 0:k.docs,source:{originalSource:`{
  render: () => checkboxTwig({
    ...data,
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.'
  })
}`,...(x=(w=n.parameters)==null?void 0:w.docs)==null?void 0:x.source}}};var f,T,$;o.parameters={...o.parameters,docs:{...(f=o.parameters)==null?void 0:f.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${checkboxTwig({
    ...data,
    checked: false,
    disabled: false,
    label: 'Unchecked'
  })}
      \${checkboxTwig({
    ...data,
    checked: true,
    disabled: false,
    label: 'Checked'
  })}
      \${checkboxTwig({
    ...data,
    checked: false,
    disabled: true,
    label: 'Disabled'
  })}
      \${checkboxTwig({
    ...data,
    checked: true,
    disabled: true,
    label: 'Checked Disabled'
  })}
    </div>
  \`
}`,...($=(T=o.parameters)==null?void 0:T.docs)==null?void 0:$.source}}};var L,C,D;l.parameters={...l.parameters,docs:{...(L=l.parameters)==null?void 0:L.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${checkboxTwig({
    ...data,
    label: 'Option label'
  })}
      \${checkboxTwig({
    ...data,
    label: ''
  })}
      \${checkboxTwig({
    ...data,
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.'
  })}
    </div>
  \`
}`,...(D=(C=l.parameters)==null?void 0:C.docs)==null?void 0:D.source}}};var A,U,N;p.parameters={...p.parameters,docs:{...(A=p.parameters)==null?void 0:A.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; gap: var(--size-4); grid-template-columns: repeat(4, minmax(0, 1fr));">
      <div><strong>Checked + Label</strong><br/>\${checkboxTwig({
    ...data,
    checked: true,
    disabled: false,
    label: 'Checked'
  })}</div>
      <div><strong>Unchecked + Label</strong><br/>\${checkboxTwig({
    ...data,
    checked: false,
    disabled: false,
    label: 'Unchecked'
  })}</div>
      <div><strong>Checked + NoLabel</strong><br/>\${checkboxTwig({
    ...data,
    checked: true,
    disabled: false,
    label: ''
  })}</div>
      <div><strong>Unchecked + NoLabel</strong><br/>\${checkboxTwig({
    ...data,
    checked: false,
    disabled: false,
    label: ''
  })}</div>
      <div><strong>Checked + Disabled + Label</strong><br/>\${checkboxTwig({
    ...data,
    checked: true,
    disabled: true,
    label: 'Checked Disabled'
  })}</div>
      <div><strong>Unchecked + Disabled + Label</strong><br/>\${checkboxTwig({
    ...data,
    checked: false,
    disabled: true,
    label: 'Disabled'
  })}</div>
      <div><strong>Checked + Disabled + NoLabel</strong><br/>\${checkboxTwig({
    ...data,
    checked: true,
    disabled: true,
    label: ''
  })}</div>
      <div><strong>Unchecked + Disabled + NoLabel</strong><br/>\${checkboxTwig({
    ...data,
    checked: false,
    disabled: true,
    label: ''
  })}</div>
    </div>
  \`
}`,...(N=(U=p.parameters)==null?void 0:U.docs)==null?void 0:N.source}}};var O,S,_;d.parameters={...d.parameters,docs:{...(O=d.parameters)==null?void 0:O.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <strong>Options:</strong><br/><br/>
        \${checkboxTwig({
    name: 'group1',
    value: '1',
    label: 'Option label',
    checked: false
  })}
        \${checkboxTwig({
    name: 'group1',
    value: '2',
    label: 'Option label',
    checked: true
  })}
      </div>
      <div>
        <strong>Long label:</strong><br/><br/>
        \${checkboxTwig({
    name: 'group2',
    value: '1',
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.',
    checked: false
  })}
        \${checkboxTwig({
    name: 'group2',
    value: '2',
    label: 'Lorem ipsum dolor sit amet consectetur. Cursus posuere et egestas id metus sit.',
    checked: true
  })}
      </div>
    </div>
  \`
}`,...(_=(S=d.parameters)==null?void 0:S.docs)==null?void 0:_.source}}};const R=["Default","NoLabel","WithLongLabel","AllStates","AllLabels","AllCombinations","Group"];export{p as AllCombinations,l as AllLabels,o as AllStates,r as Default,d as Group,i as NoLabel,n as WithLongLabel,R as __namedExportsOrder,G as default};
