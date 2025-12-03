import{t as h,T as f}from"./iframe-CnHaBuCA.js";import{D as w,a as x}from"./twig-Dp8duUs-.js";x(f);f.cache(!1);const l=t=>t,e=(t={})=>{const o=h.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/radio/radio.twig",data:[{type:"raw",value:"",position:{start:512,end:513}},{type:"logic",token:{type:"Twig.logic.type.set",key:"name",expression:[{type:"Twig.expression.type.variable",value:"name",match:["name"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"radio"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:513,end:553}},position:{start:513,end:553}},{type:"logic",token:{type:"Twig.logic.type.set",key:"value",expression:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"1"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:554,end:592}},position:{start:554,end:592}},{type:"logic",token:{type:"Twig.logic.type.set",key:"label",expression:[{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"Option label"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:593,end:642}},position:{start:593,end:642}},{type:"logic",token:{type:"Twig.logic.type.set",key:"checked",expression:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:643,end:687}},position:{start:643,end:687}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:688,end:734}},position:{start:688,end:734}},{type:"logic",token:{type:"Twig.logic.type.set",key:"attributes",expression:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:735,end:798}},position:{start:735,end:798}},{type:"raw",value:"",position:{start:799,end:800}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-radio"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.string",value:"ps-radio--disabled"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:800,end:880}},position:{start:800,end:880}},{type:"raw",value:"<label ",position:{start:881,end:889}},{type:"output",position:{start:889,end:923},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:889,end:923}},{type:"Twig.expression.type.key.period",position:{start:889,end:923},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:889,end:923},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:889,end:923}}]}]},{type:"raw",value:`>
  <input
    type="radio"
    class="ps-radio__input"
    name="`,position:{start:923,end:989}},{type:"output",position:{start:989,end:999},stack:[{type:"Twig.expression.type.variable",value:"name",match:["name"],position:{start:989,end:999}}]},{type:"raw",value:`"
    value="`,position:{start:999,end:1012}},{type:"output",position:{start:1012,end:1023},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:1012,end:1023}}]},{type:"raw",value:`"
    `,position:{start:1023,end:1029}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]}],position:{start:1029,end:1045},output:[{type:"raw",value:"checked",position:{start:1045,end:1052}}]},position:{open:{start:1029,end:1045},close:{start:1052,end:1063}}},{type:"raw",value:"    ",position:{start:1064,end:1068}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:1068,end:1085},output:[{type:"raw",value:"disabled",position:{start:1085,end:1093}}]},position:{open:{start:1068,end:1085},close:{start:1093,end:1104}}},{type:"raw",value:'    aria-checked="',position:{start:1105,end:1123}},{type:"output",position:{start:1123,end:1155},stack:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"],position:{start:1123,end:1155}},{type:"Twig.expression.type.string",value:"true",position:{start:1123,end:1155}},{type:"Twig.expression.type.string",value:"false",position:{start:1123,end:1155}},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:1123,end:1155},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:`"
  />
  <span class="ps-radio__circle" aria-hidden="true"></span>
  `,position:{start:1155,end:1224}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:1224,end:1238},output:[{type:"raw",value:'    <span class="ps-radio__label">',position:{start:1239,end:1273}},{type:"output",position:{start:1273,end:1284},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:1273,end:1284}}]},{type:"raw",value:`</span>
  `,position:{start:1284,end:1294}}]},position:{open:{start:1224,end:1238},close:{start:1294,end:1305}}},{type:"raw",value:"</label>",position:{start:1306,end:1306}}],precompiled:!0});o.options.allowInlineIncludes=!0;try{let a=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),l(o.render({attributes:new w(a),...t}))}catch(a){return l("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/radio/radio.twig: "+a.toString())}},b={name:"option",value:"1",label:"Option label",checked:!1,disabled:!1},z={title:"Elements/Radio",tags:["autodocs"],parameters:{docs:{description:{component:`Semantic radio control for single selection within a group.
Supports checked/disabled states, focus-visible, and accessible labeling.`}}},argTypes:{label:{description:"Visible label text displayed next to radio button",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:"Option label"}}},value:{description:"Unique value for this radio button within the group",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"1"}}},name:{description:"Radio group name (all radios with same name allow single selection)",control:{type:"text"},table:{category:"Behavior",type:{summary:"string",required:!0},defaultValue:{summary:"option"}}},checked:{description:"Checked state (only one radio per group should be checked)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},disabled:{description:"Disabled state (50% opacity, not-allowed cursor, prevents interaction)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}}},args:{...b}},i={render:t=>e(t),args:{...b}},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Unchecked (gray border circle)</p>
        ${e({name:"demo1",value:"1",label:"Option label",checked:!1})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Checked (green filled circle with white dot)</p>
        ${e({name:"demo2",value:"2",label:"Option label",checked:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled unchecked (50% opacity)</p>
        ${e({name:"demo3",value:"3",label:"Option label",checked:!1,disabled:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled checked (50% opacity)</p>
        ${e({name:"demo4",value:"4",label:"Option label",checked:!0,disabled:!0})}
      </div>
    </div>
  `},r={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Type Selection</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${e({name:"property-type",value:"apartment",label:"Apartment",checked:!1})}
          ${e({name:"property-type",value:"house",label:"House",checked:!0})}
          ${e({name:"property-type",value:"commercial",label:"Commercial Property",checked:!1})}
          ${e({name:"property-type",value:"land",label:"Land / Plot",checked:!1})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Listing Status</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${e({name:"status",value:"sale",label:"For Sale",checked:!0})}
          ${e({name:"status",value:"rent",label:"For Rent",checked:!1})}
          ${e({name:"status",value:"sold",label:"Sold",checked:!1,disabled:!0})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Mortgage Type</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${e({name:"mortgage",value:"fixed",label:"Fixed Rate Mortgage",checked:!0})}
          ${e({name:"mortgage",value:"variable",label:"Variable Rate Mortgage",checked:!1})}
          ${e({name:"mortgage",value:"interest",label:"Interest Only",checked:!1})}
        </div>
      </div>
    </div>
  `};var p,n,d;i.parameters={...i.parameters,docs:{...(p=i.parameters)==null?void 0:p.docs,source:{originalSource:`{
  render: args => radioTwig(args),
  args: {
    ...data
  }
}`,...(d=(n=i.parameters)==null?void 0:n.docs)==null?void 0:d.source}}};var c,y,u;s.parameters={...s.parameters,docs:{...(c=s.parameters)==null?void 0:c.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Unchecked (gray border circle)</p>
        \${radioTwig({
    name: 'demo1',
    value: '1',
    label: 'Option label',
    checked: false
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Checked (green filled circle with white dot)</p>
        \${radioTwig({
    name: 'demo2',
    value: '2',
    label: 'Option label',
    checked: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled unchecked (50% opacity)</p>
        \${radioTwig({
    name: 'demo3',
    value: '3',
    label: 'Option label',
    checked: false,
    disabled: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled checked (50% opacity)</p>
        \${radioTwig({
    name: 'demo4',
    value: '4',
    label: 'Option label',
    checked: true,
    disabled: true
  })}
      </div>
    </div>
  \`
}`,...(u=(y=s.parameters)==null?void 0:y.docs)==null?void 0:u.source}}};var v,m,g;r.parameters={...r.parameters,docs:{...(v=r.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Type Selection</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${radioTwig({
    name: 'property-type',
    value: 'apartment',
    label: 'Apartment',
    checked: false
  })}
          \${radioTwig({
    name: 'property-type',
    value: 'house',
    label: 'House',
    checked: true
  })}
          \${radioTwig({
    name: 'property-type',
    value: 'commercial',
    label: 'Commercial Property',
    checked: false
  })}
          \${radioTwig({
    name: 'property-type',
    value: 'land',
    label: 'Land / Plot',
    checked: false
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Listing Status</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${radioTwig({
    name: 'status',
    value: 'sale',
    label: 'For Sale',
    checked: true
  })}
          \${radioTwig({
    name: 'status',
    value: 'rent',
    label: 'For Rent',
    checked: false
  })}
          \${radioTwig({
    name: 'status',
    value: 'sold',
    label: 'Sold',
    checked: false,
    disabled: true
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Mortgage Type</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${radioTwig({
    name: 'mortgage',
    value: 'fixed',
    label: 'Fixed Rate Mortgage',
    checked: true
  })}
          \${radioTwig({
    name: 'mortgage',
    value: 'variable',
    label: 'Variable Rate Mortgage',
    checked: false
  })}
          \${radioTwig({
    name: 'mortgage',
    value: 'interest',
    label: 'Interest Only',
    checked: false
  })}
        </div>
      </div>
    </div>
  \`
}`,...(g=(m=r.parameters)==null?void 0:m.docs)==null?void 0:g.source}}};const $=["Default","AllStates","UseCases"];export{s as AllStates,i as Default,r as UseCases,$ as __namedExportsOrder,z as default};
