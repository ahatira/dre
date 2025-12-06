import{i as F}from"./icons-list-Di3fqTRs.js";import{b as e}from"./button-BRMWzZ8O.js";import"./iframe-DeCmpQ6I.js";import"./twig-Cbw8xbjJ.js";const I={label:"Button",variant:"primary",outline:!1,size:"medium",disabled:!1,loading:!1,fullWidth:!1},q={title:"Elements/Button",tags:["autodocs"],parameters:{docs:{description:{component:"Interactive action trigger with semantic variants, sizes, and styles. Supports icons, loading/disabled states, links, and full-width layout using design tokens."}}},argTypes:{label:{description:"Button text",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"Button"}}},icon:{description:"Icon name to display (optional)",control:{type:"select"},options:F.categories.generic,table:{category:"Content",type:{summary:"string"}}},iconPosition:{description:"Icon position relative to text",control:{type:"select"},options:["left","right"],table:{category:"Content",type:{summary:"left | right"},defaultValue:{summary:"right"}}},variant:{description:"Semantic variant (neutral: gray default, primary: green, secondary: pink, success/info/warning/danger)",control:{type:"select"},options:["neutral","primary","secondary","success","info","warning","danger"],table:{category:"Appearance",type:{summary:"primary | secondary | neutral | success | info | warning | danger"},defaultValue:{summary:"neutral"}}},outline:{description:"Outline version (border only, transparent background)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},size:{description:"Button size (small: 34px, medium: 36px, large: 40px)",control:{type:"select"},options:["small","medium","large"],table:{category:"Appearance",type:{summary:"small | medium | large"},defaultValue:{summary:"medium"}}},fullWidth:{description:"Full width button (width: 100%)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},disabled:{description:"Disable button (reduces opacity to 50%)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},loading:{description:"Display loading state",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},url:{description:"Destination URL (transforms button to link)",control:{type:"text"},table:{category:"Link",type:{summary:"string"}}},target:{description:"Link target attribute",control:{type:"select"},options:["_self","_blank"],table:{category:"Link",type:{summary:"_self | _blank"},defaultValue:{summary:"_self"}}},baseClass:{description:"Override BEM block class name (for custom button variants in parent components)",control:{type:"text"},table:{category:"Advanced",type:{summary:"string"},defaultValue:{summary:"ps-button"}}}}},r={render:a=>e(a),args:{...I,variant:"neutral"}},t={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${["neutral","primary","secondary","success","info","warning","danger"].map(a=>e({label:a.charAt(0).toUpperCase()+a.slice(1),variant:a})).join("")}
    </div>
  `},n={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${["neutral","primary","secondary","success","info","warning","danger"].map(a=>e({label:a.charAt(0).toUpperCase()+a.slice(1),variant:a,outline:!0})).join("")}
    </div>
  `},s={render:()=>`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${["small","medium","large"].map(a=>e({label:a.charAt(0).toUpperCase()+a.slice(1),variant:"primary",size:a})).join("")}
    </div>
  `},i={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${e({label:"Search",variant:"primary",icon:"search",iconPosition:"left"})}
      ${e({label:"Next",variant:"primary",icon:"arrow-right",iconPosition:"right"})}
      ${e({icon:"close",variant:"primary",size:"medium"})}
    </div>
  `},o={render:()=>e({label:"Full Width Button",variant:"primary",fullWidth:!0})},l={render:()=>`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${e({label:"Loading...",variant:"primary",loading:!0})}
      ${e({label:"Loading...",variant:"secondary",outline:!0,loading:!0})}
    </div>
  `},c={render:()=>`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${e({label:"Disabled",variant:"primary",disabled:!0})}
      ${e({label:"Disabled",variant:"secondary",outline:!0,disabled:!0})}
    </div>
  `},d={name:"Custom Base Class (Advanced)",render:()=>`
    <style>
      .custom-action { padding: var(--size-3) var(--size-5); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; }
      .custom-action:hover { background: var(--primary-hover); }
      .custom-action__icon { margin-left: var(--size-2); }
    </style>
    <div style="display: flex; gap: var(--size-4); flex-direction: column;">
      <p><strong>Default button:</strong></p>
      ${e({label:"Standard Button",variant:"primary",icon:"arrow-right"})}
      <p><strong>With baseClass override (custom-action):</strong></p>
      ${e({baseClass:"custom-action",label:"Custom Styled",icon:"arrow-right"})}
      <p><em>Note: baseClass is used by parent components (alert, modal, etc.) to fully control button styling via their own BEM classes.</em></p>
    </div>
  `};var u,p,m;r.parameters={...r.parameters,docs:{...(u=r.parameters)==null?void 0:u.docs,source:{originalSource:`{
  render: args => buttonTwig(args),
  args: {
    ...data,
    variant: 'neutral'
  }
}`,...(m=(p=r.parameters)==null?void 0:p.docs)==null?void 0:m.source}}};var y,g,b;t.parameters={...t.parameters,docs:{...(y=t.parameters)==null?void 0:y.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant
  })).join('')}
    </div>
  \`
}`,...(b=(g=t.parameters)==null?void 0:g.docs)==null?void 0:b.source}}};var v,f,h;n.parameters={...n.parameters,docs:{...(v=n.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${[
  // Show neutral outline first as the default
  'neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant,
    outline: true
  })).join('')}
    </div>
  \`
}`,...(h=(f=n.parameters)==null?void 0:f.docs)==null?void 0:h.source}}};var w,x,z;s.parameters={...s.parameters,docs:{...(w=s.parameters)==null?void 0:w.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${['small', 'medium', 'large'].map(size => buttonTwig({
    label: size.charAt(0).toUpperCase() + size.slice(1),
    variant: 'primary',
    size
  })).join('')}
    </div>
  \`
}`,...(z=(x=s.parameters)==null?void 0:x.docs)==null?void 0:z.source}}};var C,$,S;i.parameters={...i.parameters,docs:{...(C=i.parameters)==null?void 0:C.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${buttonTwig({
    label: 'Search',
    variant: 'primary',
    icon: 'search',
    iconPosition: 'left'
  })}
      \${buttonTwig({
    label: 'Next',
    variant: 'primary',
    icon: 'arrow-right',
    iconPosition: 'right'
  })}
      \${buttonTwig({
    icon: 'close',
    variant: 'primary',
    size: 'medium'
  })}
    </div>
  \`
}`,...(S=($=i.parameters)==null?void 0:$.docs)==null?void 0:S.source}}};var A,B,T;o.parameters={...o.parameters,docs:{...(A=o.parameters)==null?void 0:A.docs,source:{originalSource:`{
  render: () => buttonTwig({
    label: 'Full Width Button',
    variant: 'primary',
    fullWidth: true
  })
}`,...(T=(B=o.parameters)==null?void 0:B.docs)==null?void 0:T.source}}};var k,D,V;l.parameters={...l.parameters,docs:{...(k=l.parameters)==null?void 0:k.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${buttonTwig({
    label: 'Loading...',
    variant: 'primary',
    loading: true
  })}
      \${buttonTwig({
    label: 'Loading...',
    variant: 'secondary',
    outline: true,
    loading: true
  })}
    </div>
  \`
}`,...(V=(D=l.parameters)==null?void 0:D.docs)==null?void 0:V.source}}};var W,_,L;c.parameters={...c.parameters,docs:{...(W=c.parameters)==null?void 0:W.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${buttonTwig({
    label: 'Disabled',
    variant: 'primary',
    disabled: true
  })}
      \${buttonTwig({
    label: 'Disabled',
    variant: 'secondary',
    outline: true,
    disabled: true
  })}
    </div>
  \`
}`,...(L=(_=c.parameters)==null?void 0:_.docs)==null?void 0:L.source}}};var U,j,E;d.parameters={...d.parameters,docs:{...(U=d.parameters)==null?void 0:U.docs,source:{originalSource:`{
  name: 'Custom Base Class (Advanced)',
  render: () => \`
    <style>
      .custom-action { padding: var(--size-3) var(--size-5); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; }
      .custom-action:hover { background: var(--primary-hover); }
      .custom-action__icon { margin-left: var(--size-2); }
    </style>
    <div style="display: flex; gap: var(--size-4); flex-direction: column;">
      <p><strong>Default button:</strong></p>
      \${buttonTwig({
    label: 'Standard Button',
    variant: 'primary',
    icon: 'arrow-right'
  })}
      <p><strong>With baseClass override (custom-action):</strong></p>
      \${buttonTwig({
    baseClass: 'custom-action',
    label: 'Custom Styled',
    icon: 'arrow-right'
  })}
      <p><em>Note: baseClass is used by parent components (alert, modal, etc.) to fully control button styling via their own BEM classes.</em></p>
    </div>
  \`
}`,...(E=(j=d.parameters)==null?void 0:j.docs)==null?void 0:E.source}}};const R=["Default","AllVariants","AllOutlines","AllSizes","WithIcons","FullWidth","Loading","Disabled","CustomBaseClass"];export{n as AllOutlines,s as AllSizes,t as AllVariants,d as CustomBaseClass,r as Default,c as Disabled,o as FullWidth,l as Loading,i as WithIcons,R as __namedExportsOrder,q as default};
