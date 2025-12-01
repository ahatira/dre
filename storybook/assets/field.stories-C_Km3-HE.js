import{i as z}from"./icons-list-BivTTKC1.js";import{f as e}from"./field-BpukmSeQ.js";import"./iframe-BXCbAV1K.js";import"./twig-CSYqopkt.js";const $={type:"text",value:"",placeholder:"Enter text...",disabled:!1,error:"",done:!1,icon:"",iconPosition:"right"},E={title:"Elements/Field",tags:["autodocs"],parameters:{docs:{description:{component:"Base field component (input/select/textarea) with token-based styling and accessible states. Supports icons, error/disabled/done states, and multiple input types."}}},argTypes:{value:{description:"Current value of the field",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:""}}},placeholder:{description:"Placeholder text shown when field is empty",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:""}}},type:{description:"Type of field input",control:{type:"select"},options:["text","number","email","search","select","textarea"],table:{category:"Appearance",type:{summary:"text | number | email | search | select | textarea"},defaultValue:{summary:"text"}}},icon:{description:"Icon to display (optional)",control:{type:"select"},options:["",...z.all],table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:""}}},iconPosition:{description:"Position of the icon",control:{type:"select"},options:["left","right"],table:{category:"Appearance",type:{summary:"left | right"},defaultValue:{summary:"right"}}},disabled:{description:"Disabled state of the field",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},done:{description:"Success/validated state of the field",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},error:{description:"Error message to display below the field (sets aria-invalid and aria-describedby)",control:{type:"text"},table:{category:"Accessibility",type:{summary:"string"},defaultValue:{summary:""}}}}},t={render:x=>e(x),args:{...$}},i={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Text</label>
        ${e({type:"text",placeholder:"Enter text..."})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Number</label>
        ${e({type:"number",placeholder:"Enter number..."})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Email</label>
        ${e({type:"email",placeholder:"your.email@example.com"})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Search</label>
        ${e({type:"search",placeholder:"Search...",icon:"search",iconPosition:"right"})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Select</label>
        ${e({type:"select",value:"Select an option",icon:"arrow-down",iconPosition:"right"})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Textarea</label>
        ${e({type:"textarea",placeholder:"Enter your message..."})}
      </div>
    </div>
  `},a={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Default (empty)</label>
        ${e({type:"text",placeholder:"Enter text..."})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Filled</label>
        ${e({type:"text",value:"John Doe",placeholder:"Enter text..."})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Done (Success)</label>
        ${e({type:"text",value:"Valid input",done:!0,icon:"check",iconPosition:"left"})}
      </div>
      <div style="margin-bottom: var(--size-4);">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Error</label>
        ${e({type:"email",value:"invalid-email",error:"Please enter a valid email address"})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Disabled</label>
        ${e({type:"text",value:"Disabled field",disabled:!0})}
      </div>
    </div>
  `},l={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Icon Left</label>
        ${e({type:"search",placeholder:"Search...",icon:"search",iconPosition:"left"})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Icon Right</label>
        ${e({type:"email",placeholder:"your.email@example.com",icon:"check",iconPosition:"right"})}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">No Icon</label>
        ${e({type:"text",placeholder:"Enter text..."})}
      </div>
    </div>
  `},o={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 500px;">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Contact Form</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${e({type:"text",placeholder:"Full Name"})}
          ${e({type:"email",placeholder:"Email Address"})}
          ${e({type:"textarea",placeholder:"Your Message"})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Property Search</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${e({type:"search",placeholder:"Search location...",icon:"search",iconPosition:"right"})}
          ${e({type:"select",value:"Property Type",icon:"arrow-down",iconPosition:"right"})}
          ${e({type:"number",placeholder:"Max Price"})}
        </div>
      </div>
      <div style="margin-bottom: var(--size-6);">
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Validation States</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-6);">
          ${e({type:"email",value:"user@example.com",icon:"check",iconPosition:"right"})}
          ${e({type:"email",value:"invalid-email",error:"Please enter a valid email address"})}
        </div>
      </div>
    </div>
  `};var r,s,d;t.parameters={...t.parameters,docs:{...(r=t.parameters)==null?void 0:r.docs,source:{originalSource:`{
  render: args => fieldTwig(args),
  args: {
    ...data
  }
}`,...(d=(s=t.parameters)==null?void 0:s.docs)==null?void 0:d.source}}};var n,c,p;i.parameters={...i.parameters,docs:{...(n=i.parameters)==null?void 0:n.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Text</label>
        \${fieldTwig({
    type: 'text',
    placeholder: 'Enter text...'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Number</label>
        \${fieldTwig({
    type: 'number',
    placeholder: 'Enter number...'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Email</label>
        \${fieldTwig({
    type: 'email',
    placeholder: 'your.email@example.com'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Search</label>
        \${fieldTwig({
    type: 'search',
    placeholder: 'Search...',
    icon: 'search',
    iconPosition: 'right'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Select</label>
        \${fieldTwig({
    type: 'select',
    value: 'Select an option',
    icon: 'arrow-down',
    iconPosition: 'right'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Textarea</label>
        \${fieldTwig({
    type: 'textarea',
    placeholder: 'Enter your message...'
  })}
      </div>
    </div>
  \`
}`,...(p=(c=i.parameters)==null?void 0:c.docs)==null?void 0:p.source}}};var m,v,y;a.parameters={...a.parameters,docs:{...(m=a.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Default (empty)</label>
        \${fieldTwig({
    type: 'text',
    placeholder: 'Enter text...'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Filled</label>
        \${fieldTwig({
    type: 'text',
    value: 'John Doe',
    placeholder: 'Enter text...'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Done (Success)</label>
        \${fieldTwig({
    type: 'text',
    value: 'Valid input',
    done: true,
    icon: 'check',
    iconPosition: 'left'
  })}
      </div>
      <div style="margin-bottom: var(--size-4);">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Error</label>
        \${fieldTwig({
    type: 'email',
    value: 'invalid-email',
    error: 'Please enter a valid email address'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Disabled</label>
        \${fieldTwig({
    type: 'text',
    value: 'Disabled field',
    disabled: true
  })}
      </div>
    </div>
  \`
}`,...(y=(v=a.parameters)==null?void 0:v.docs)==null?void 0:y.source}}};var g,h,f;l.parameters={...l.parameters,docs:{...(g=l.parameters)==null?void 0:g.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Icon Left</label>
        \${fieldTwig({
    type: 'search',
    placeholder: 'Search...',
    icon: 'search',
    iconPosition: 'left'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Icon Right</label>
        \${fieldTwig({
    type: 'email',
    placeholder: 'your.email@example.com',
    icon: 'check',
    iconPosition: 'right'
  })}
      </div>
      <div>
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">No Icon</label>
        \${fieldTwig({
    type: 'text',
    placeholder: 'Enter text...'
  })}
      </div>
    </div>
  \`
}`,...(f=(h=l.parameters)==null?void 0:h.docs)==null?void 0:f.source}}};var b,u,w;o.parameters={...o.parameters,docs:{...(b=o.parameters)==null?void 0:b.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 500px;">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Contact Form</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          \${fieldTwig({
    type: 'text',
    placeholder: 'Full Name'
  })}
          \${fieldTwig({
    type: 'email',
    placeholder: 'Email Address'
  })}
          \${fieldTwig({
    type: 'textarea',
    placeholder: 'Your Message'
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Property Search</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          \${fieldTwig({
    type: 'search',
    placeholder: 'Search location...',
    icon: 'search',
    iconPosition: 'right'
  })}
          \${fieldTwig({
    type: 'select',
    value: 'Property Type',
    icon: 'arrow-down',
    iconPosition: 'right'
  })}
          \${fieldTwig({
    type: 'number',
    placeholder: 'Max Price'
  })}
        </div>
      </div>
      <div style="margin-bottom: var(--size-6);">
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-5);">Validation States</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-6);">
          \${fieldTwig({
    type: 'email',
    value: 'user@example.com',
    icon: 'check',
    iconPosition: 'right'
  })}
          \${fieldTwig({
    type: 'email',
    value: 'invalid-email',
    error: 'Please enter a valid email address'
  })}
        </div>
      </div>
    </div>
  \`
}`,...(w=(u=o.parameters)==null?void 0:u.docs)==null?void 0:w.source}}};const V=["Default","AllTypes","AllStates","IconVariations","UseCases"];export{a as AllStates,i as AllTypes,t as Default,l as IconVariations,o as UseCases,V as __namedExportsOrder,E as default};
