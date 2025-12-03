import{l as e}from"./label-BUNf4--_.js";import"./iframe-CnHaBuCA.js";import"./twig-Dp8duUs-.js";const v={props:{text:"Property name",forId:"field-id",required:!1,disabled:!1,attributes:{}}},g={title:"Elements/Label",tags:["autodocs"],parameters:{docs:{description:{component:"Form field label with semantic <label> binding, required indicator (asterisk + SR text), and disabled state. Essential building block for form components."}}},args:{...v.props},argTypes:{text:{description:"Label text content",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"Label"}}},forId:{description:"ID of the associated form field for proper label-input binding",control:{type:"text"},table:{category:"Behavior",type:{summary:"string"},defaultValue:{summary:""}}},required:{description:'Adds visual asterisk (*) and accessible "required" text for screen readers',control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},disabled:{description:"Disabled state with reduced opacity (70%) and muted text color",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},attributes:{description:"Additional HTML attributes object for custom styling or data attributes",control:{type:"object"},table:{category:"Structure",type:{summary:"object"},defaultValue:{summary:"{}"}}},baseClass:{description:"Override root class when composing inside other components. Modifiers map to baseClass variants; elements render as baseClass__*.",control:{type:"text"},table:{category:"Layout",type:{summary:"string"},defaultValue:{summary:"ps-label"}}}}},r={render:f=>e(f),args:{...v.props}},t={render:()=>`
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
  `},i={render:()=>`
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
  `};var a,s,d;r.parameters={...r.parameters,docs:{...(a=r.parameters)==null?void 0:a.docs,source:{originalSource:`{
  render: args => labelTwig(args),
  args: {
    ...data.props
  }
}`,...(d=(s=r.parameters)==null?void 0:s.docs)==null?void 0:d.source}}};var o,l,n;t.parameters={...t.parameters,docs:{...(o=t.parameters)==null?void 0:o.docs,source:{originalSource:`{
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
}`,...(n=(l=t.parameters)==null?void 0:l.docs)==null?void 0:n.source}}};var p,u,c;i.parameters={...i.parameters,docs:{...(p=i.parameters)==null?void 0:p.docs,source:{originalSource:`{
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
}`,...(c=(u=i.parameters)==null?void 0:u.docs)==null?void 0:c.source}}};const x=["Default","AllStates","UseCases"];export{t as AllStates,r as Default,i as UseCases,x as __namedExportsOrder,g as default};
