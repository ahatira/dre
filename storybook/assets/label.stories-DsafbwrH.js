import{t as b,T as f}from"./iframe-wV_yutGI.js";import{D as w,a as h}from"./twig--ZzzhHos.js";import"https://kit.fontawesome.com/a0eb0bad75.js";h(f);f.cache(!1);const p=t=>t,e=(t={})=>{const o=b.twig({id:"C:/wamp64/www/ps_theme/source/patterns/elements/label/label.twig",data:[{type:"raw",value:"",position:{start:444,end:446}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-label"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:446,end:480}},position:{start:446,end:480}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"required",match:["required"]}],position:{start:481,end:500},output:[{type:"raw",value:"",position:{start:501,end:503}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-label--required"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:503,end:562}},position:{start:503,end:562}}]},position:{open:{start:481,end:500},close:{start:563,end:576}}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:577,end:596},output:[{type:"raw",value:"",position:{start:597,end:599}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-label--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:599,end:658}},position:{start:599,end:658}}]},position:{open:{start:577,end:596},close:{start:659,end:672}}},{type:"raw",value:"<label",position:{start:673,end:680}},{type:"output",position:{start:680,end:766},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:680,end:766}},{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:680,end:766}},{type:"Twig.expression.type.key.period",position:{start:680,end:766},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:680,end:766},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:680,end:766}}]},{type:"Twig.expression.type.string",value:' class="',position:{start:680,end:766}},{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:680,end:766}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:680,end:766},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:680,end:766}},{type:"Twig.expression.type.string",value:" ",position:{start:680,end:766}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:680,end:766},expression:!1}]},{type:"Twig.expression.type.operator.binary",value:"~",position:{start:680,end:766},precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.string",value:'"',position:{start:680,end:766}},{type:"Twig.expression.type.operator.binary",value:"~",position:{start:680,end:766},precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:680,end:766},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"forId",match:["forId"]}],position:{start:766,end:780},output:[{type:"raw",value:' for="',position:{start:780,end:786}},{type:"output",position:{start:786,end:797},stack:[{type:"Twig.expression.type.variable",value:"forId",match:["forId"],position:{start:786,end:797}}]},{type:"raw",value:'"',position:{start:797,end:798}}]},position:{open:{start:766,end:780},close:{start:798,end:809}}},{type:"raw",value:`>
  <span class="ps-label__text">`,position:{start:809,end:842}},{type:"output",position:{start:842,end:852},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:842,end:852}}]},{type:"raw",value:`</span>
  `,position:{start:852,end:862}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"required",match:["required"]}],position:{start:862,end:879},output:[{type:"raw",value:`    <span class="ps-label__required" aria-hidden="true">*</span>
    <span class="visually-hidden">(required field)</span>
  `,position:{start:880,end:1005}}]},position:{open:{start:862,end:879},close:{start:1005,end:1016}}},{type:"raw",value:"</label>",position:{start:1017,end:1017}}],precompiled:!0});o.options.allowInlineIncludes=!0;try{let r=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(r)||(r=Object.entries(r)),p(o.render({attributes:new w(r),...t}))}catch(r){return p("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/label/label.twig: "+r.toString())}},x={props:{text:"Your name",forId:"field-id",required:!1,disabled:!1,attributes:{}}},q={title:"Elements/Label",tags:["autodocs"],parameters:{docs:{description:{component:"Accessible form field label with required indicator, disabled state, and flexible attributes. Uses semantic <label> binding and supports screen reader text via tokens and structured markup."}}},args:{...x.props},argTypes:{text:{description:"Label text content",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"Label"}}},forId:{description:"ID of the associated form field for proper label-input binding",control:{type:"text"},table:{category:"Behavior",type:{summary:"string"},defaultValue:{summary:""}}},required:{description:'Adds visual asterisk (*) and accessible "required" text for screen readers',control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},disabled:{description:"Disabled state with reduced opacity (70%) and muted text color",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},attributes:{description:"Additional HTML attributes object for custom styling or data attributes",control:{type:"object"},table:{category:"Structure",type:{summary:"object"},defaultValue:{summary:"{}"}}}}},a={render:t=>e(t),args:{...x.props}},i={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default</p>
        ${e({text:"Your name",forId:"field-1"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Required (with asterisk + screen reader text)</p>
        ${e({text:"Your email",forId:"field-2",required:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (70% opacity, muted color)</p>
        ${e({text:"Disabled field",forId:"field-3",disabled:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Required + Disabled</p>
        ${e({text:"Required disabled",forId:"field-4",required:!0,disabled:!0})}
      </div>
    </div>
  `},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">With text input</h3>
        ${e({text:"Full name",forId:"name-input",required:!0})}
        <input type="text" id="name-input" style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1);" />
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">With textarea</h3>
        ${e({text:"Description",forId:"description-input"})}
        <textarea id="description-input" rows="3" style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1);"></textarea>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Disabled field</h3>
        ${e({text:"Locked field",forId:"locked-input",disabled:!0})}
        <input type="text" id="locked-input" disabled style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1); opacity: 0.7;" />
      </div>
    </div>
  `};var n,d,l;a.parameters={...a.parameters,docs:{...(n=a.parameters)==null?void 0:n.docs,source:{originalSource:`{
  render: args => labelTwig(args),
  args: {
    ...data.props
  }
}`,...(l=(d=a.parameters)==null?void 0:d.docs)==null?void 0:l.source}}};var u,y,c;i.parameters={...i.parameters,docs:{...(u=i.parameters)==null?void 0:u.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default</p>
        \${labelTwig({
    text: 'Your name',
    forId: 'field-1'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Required (with asterisk + screen reader text)</p>
        \${labelTwig({
    text: 'Your email',
    forId: 'field-2',
    required: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (70% opacity, muted color)</p>
        \${labelTwig({
    text: 'Disabled field',
    forId: 'field-3',
    disabled: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Required + Disabled</p>
        \${labelTwig({
    text: 'Required disabled',
    forId: 'field-4',
    required: true,
    disabled: true
  })}
      </div>
    </div>
  \`
}`,...(c=(y=i.parameters)==null?void 0:y.docs)==null?void 0:c.source}}};var v,g,m;s.parameters={...s.parameters,docs:{...(v=s.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">With text input</h3>
        \${labelTwig({
    text: 'Full name',
    forId: 'name-input',
    required: true
  })}
        <input type="text" id="name-input" style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1);" />
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">With textarea</h3>
        \${labelTwig({
    text: 'Description',
    forId: 'description-input'
  })}
        <textarea id="description-input" rows="3" style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1);"></textarea>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Disabled field</h3>
        \${labelTwig({
    text: 'Locked field',
    forId: 'locked-input',
    disabled: true
  })}
        <input type="text" id="locked-input" disabled style="width: 100%; max-width: 300px; padding: var(--size-2); border: 1px solid var(--gray-300); border-radius: var(--radius-1); opacity: 0.7;" />
      </div>
    </div>
  \`
}`,...(m=(g=s.parameters)==null?void 0:g.docs)==null?void 0:m.source}}};const I=["Default","AllStates","UseCases"];export{i as AllStates,a as Default,s as UseCases,I as __namedExportsOrder,q as default};
