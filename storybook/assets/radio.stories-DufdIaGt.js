import{t as h,T as f}from"./iframe-BXCbAV1K.js";import{D as w,a as k}from"./twig-CSYqopkt.js";k(f);f.cache(!1);const o=a=>a,e=(a={})=>{const l=h.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/radio/radio.twig",data:[{type:"raw",value:"",position:{start:512,end:513}},{type:"logic",token:{type:"Twig.logic.type.set",key:"name",expression:[{type:"Twig.expression.type.variable",value:"name",match:["name"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"radio"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:513,end:553}},position:{start:513,end:553}},{type:"logic",token:{type:"Twig.logic.type.set",key:"value",expression:[{type:"Twig.expression.type.variable",value:"value",match:["value"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"1"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:554,end:592}},position:{start:554,end:592}},{type:"logic",token:{type:"Twig.logic.type.set",key:"label",expression:[{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"Option label"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:593,end:642}},position:{start:593,end:642}},{type:"logic",token:{type:"Twig.logic.type.set",key:"checked",expression:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:643,end:687}},position:{start:643,end:687}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:688,end:734}},position:{start:688,end:734}},{type:"raw",value:'<label class="ps-radio',position:{start:735,end:758}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:758,end:775},output:[{type:"raw",value:" ps-radio--disabled",position:{start:775,end:794}}]},position:{open:{start:758,end:775},close:{start:794,end:805}}},{type:"raw",value:`">
  <input
    type="radio"
    class="ps-radio__input"
    name="`,position:{start:805,end:872}},{type:"output",position:{start:872,end:882},stack:[{type:"Twig.expression.type.variable",value:"name",match:["name"],position:{start:872,end:882}}]},{type:"raw",value:`"
    value="`,position:{start:882,end:895}},{type:"output",position:{start:895,end:906},stack:[{type:"Twig.expression.type.variable",value:"value",match:["value"],position:{start:895,end:906}}]},{type:"raw",value:`"
    `,position:{start:906,end:912}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"checked",match:["checked"]}],position:{start:912,end:928},output:[{type:"raw",value:"checked",position:{start:928,end:935}}]},position:{open:{start:912,end:928},close:{start:935,end:946}}},{type:"raw",value:"    ",position:{start:947,end:951}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:951,end:968},output:[{type:"raw",value:"disabled",position:{start:968,end:976}}]},position:{open:{start:951,end:968},close:{start:976,end:987}}},{type:"raw",value:`  />
  <span class="ps-radio__circle" aria-hidden="true"></span>
  `,position:{start:988,end:1055}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"]}],position:{start:1055,end:1069},output:[{type:"raw",value:'    <span class="ps-radio__label">',position:{start:1070,end:1104}},{type:"output",position:{start:1104,end:1115},stack:[{type:"Twig.expression.type.variable",value:"label",match:["label"],position:{start:1104,end:1115}}]},{type:"raw",value:`</span>
  `,position:{start:1115,end:1125}}]},position:{open:{start:1055,end:1069},close:{start:1125,end:1136}}},{type:"raw",value:"</label>",position:{start:1137,end:1137}}],precompiled:!0});l.options.allowInlineIncludes=!0;try{let t=a.defaultAttributes?a.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),o(l.render({attributes:new w(t),...a}))}catch(t){return o("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/radio/radio.twig: "+t.toString())}},b={name:"option",value:"1",label:"Option label",checked:!1,disabled:!1},z={title:"Elements/Radio",tags:["autodocs"],parameters:{docs:{description:{component:`Semantic radio control for single selection within a group.
Supports checked/disabled states, focus-visible, and accessible labeling.`}}},argTypes:{label:{description:"Visible label text displayed next to radio button",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:"Option label"}}},value:{description:"Unique value for this radio button within the group",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"1"}}},name:{description:"Radio group name (all radios with same name allow single selection)",control:{type:"text"},table:{category:"Behavior",type:{summary:"string",required:!0},defaultValue:{summary:"option"}}},checked:{description:"Checked state (only one radio per group should be checked)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},disabled:{description:"Disabled state (50% opacity, not-allowed cursor, prevents interaction)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}}},args:{...b}},i={render:a=>e(a),args:{...b}},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Unchecked (default icon)</p>
        ${e({name:"demo1",value:"1",label:"Option 1",checked:!1})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Checked (filled green icon)</p>
        ${e({name:"demo2",value:"2",label:"Option 2",checked:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled unchecked (50% opacity)</p>
        ${e({name:"demo3",value:"3",label:"Option 3",checked:!1,disabled:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled checked (50% opacity)</p>
        ${e({name:"demo4",value:"4",label:"Option 4",checked:!0,disabled:!0})}
      </div>
    </div>
  `},r={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Single Choice Selection</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${e({name:"plan",value:"basic",label:"Basic Plan - Free",checked:!1})}
          ${e({name:"plan",value:"premium",label:"Premium Plan - $9.99/month",checked:!0})}
          ${e({name:"plan",value:"enterprise",label:"Enterprise Plan - Contact us",checked:!1})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Account Type</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${e({name:"type",value:"individual",label:"Individual",checked:!0})}
          ${e({name:"type",value:"business",label:"Business",checked:!1})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Payment Method</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${e({name:"payment",value:"card",label:"Credit Card",checked:!0})}
          ${e({name:"payment",value:"paypal",label:"PayPal",checked:!1})}
          ${e({name:"payment",value:"bank",label:"Bank Transfer",checked:!1,disabled:!0})}
        </div>
      </div>
    </div>
  `};var n,p,d;i.parameters={...i.parameters,docs:{...(n=i.parameters)==null?void 0:n.docs,source:{originalSource:`{
  render: args => radioTwig(args),
  args: {
    ...data
  }
}`,...(d=(p=i.parameters)==null?void 0:p.docs)==null?void 0:d.source}}};var c,u,y;s.parameters={...s.parameters,docs:{...(c=s.parameters)==null?void 0:c.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Unchecked (default icon)</p>
        \${radioTwig({
    name: 'demo1',
    value: '1',
    label: 'Option 1',
    checked: false
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Checked (filled green icon)</p>
        \${radioTwig({
    name: 'demo2',
    value: '2',
    label: 'Option 2',
    checked: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled unchecked (50% opacity)</p>
        \${radioTwig({
    name: 'demo3',
    value: '3',
    label: 'Option 3',
    checked: false,
    disabled: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled checked (50% opacity)</p>
        \${radioTwig({
    name: 'demo4',
    value: '4',
    label: 'Option 4',
    checked: true,
    disabled: true
  })}
      </div>
    </div>
  \`
}`,...(y=(u=s.parameters)==null?void 0:u.docs)==null?void 0:y.source}}};var v,m,g;r.parameters={...r.parameters,docs:{...(v=r.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Single Choice Selection</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${radioTwig({
    name: 'plan',
    value: 'basic',
    label: 'Basic Plan - Free',
    checked: false
  })}
          \${radioTwig({
    name: 'plan',
    value: 'premium',
    label: 'Premium Plan - $9.99/month',
    checked: true
  })}
          \${radioTwig({
    name: 'plan',
    value: 'enterprise',
    label: 'Enterprise Plan - Contact us',
    checked: false
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Account Type</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${radioTwig({
    name: 'type',
    value: 'individual',
    label: 'Individual',
    checked: true
  })}
          \${radioTwig({
    name: 'type',
    value: 'business',
    label: 'Business',
    checked: false
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Payment Method</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${radioTwig({
    name: 'payment',
    value: 'card',
    label: 'Credit Card',
    checked: true
  })}
          \${radioTwig({
    name: 'payment',
    value: 'paypal',
    label: 'PayPal',
    checked: false
  })}
          \${radioTwig({
    name: 'payment',
    value: 'bank',
    label: 'Bank Transfer',
    checked: false,
    disabled: true
  })}
        </div>
      </div>
    </div>
  \`
}`,...(g=(m=r.parameters)==null?void 0:m.docs)==null?void 0:g.source}}};const $=["Default","AllStates","UseCases"];export{s as AllStates,i as Default,r as UseCases,$ as __namedExportsOrder,z as default};
