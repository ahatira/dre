import{i as N}from"./icons-list-Ce2D1ZE4.js";import{b as a}from"./button-B0Fv6BJC.js";import"./iframe-B-yX16js.js";import"./twig-CgICq6Dc.js";import"./icon-D0_QLvvF.js";const R={label:"Button",variant:"neutral",outline:!1,size:"md",disabled:!1,loading:!1,fullWidth:!1,toggle:!1,active:!1},K={title:"Elements/Button",tags:["autodocs"],parameters:{docs:{description:{component:"Interactive action trigger with semantic variants, sizes, and styles. Supports icons, loading/disabled states, links, and full-width layout using design tokens."}}},argTypes:{label:{description:"Button text",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"Button"}}},icon:{description:"Icon name to display (optional)",control:{type:"select"},options:N.all,table:{category:"Content",type:{summary:"string"}}},iconPosition:{description:"Icon position relative to text",control:{type:"select"},options:["left","right"],table:{category:"Content",type:{summary:"left | right"},defaultValue:{summary:"right"}}},variant:{description:"Semantic variant (neutral: gray default, primary: green, secondary: pink, success/info/warning/danger)",control:{type:"select"},options:["neutral","primary","secondary","success","info","warning","danger"],table:{category:"Appearance",type:{summary:"primary | secondary | neutral | success | info | warning | danger"},defaultValue:{summary:"neutral"}}},outline:{description:"Outline version (border only, transparent background)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},size:{description:"Button size scale (xs: 28px, sm: 32px, md: 36px, lg: 40px, xl: 44px, xxl: 48px)",control:{type:"select"},options:["xs","sm","md","lg","xl","xxl"],table:{category:"Appearance",type:{summary:"xs | sm | md | lg | xl | xxl"},defaultValue:{summary:"md"}}},fullWidth:{description:"Full width button (width: 100%)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},disabled:{description:"Disable button (reduces opacity to 50%)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},loading:{description:"Display loading state",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},url:{description:"Destination URL (transforms button to link)",control:{type:"text"},table:{category:"Link",type:{summary:"string"}}},target:{description:"Link target attribute",control:{type:"select"},options:["_self","_blank"],table:{category:"Link",type:{summary:"_self | _blank"},defaultValue:{summary:"_self"}}},baseClass:{description:"Override BEM block class name (for custom button variants in parent components)",control:{type:"text"},table:{category:"Advanced",type:{summary:"string"},defaultValue:{summary:"ps-button"}}},toggle:{description:'Enable toggle functionality via data-ps-toggle="button". Toggles .active class and aria-pressed attribute on click.',control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},active:{description:'Pre-toggled state (only applies when toggle=true). Renders .active class and aria-pressed="true".',control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}}}},r={render:e=>a(e),args:{...R,variant:"neutral"}},t={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${["neutral","primary","secondary","success","info","warning","danger"].map(e=>a({label:e.charAt(0).toUpperCase()+e.slice(1),variant:e})).join("")}
    </div>
  `},n={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${["neutral","primary","secondary","success","info","warning","danger"].map(e=>a({label:e.charAt(0).toUpperCase()+e.slice(1),variant:e,outline:!0})).join("")}
    </div>
  `},i={render:()=>`
    <div style="display: flex; gap: var(--size-4); align-items: flex-end;">
      ${["xs","sm","md","lg","xl","xxl"].map(e=>a({label:e.toUpperCase(),variant:"primary",size:e})).join("")}
    </div>
  `},s={render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      ${a({label:"Search",variant:"primary",icon:"search",iconPosition:"left"})}
      ${a({label:"Next",variant:"primary",icon:"arrow-right",iconPosition:"right"})}
      ${a({icon:"close",variant:"primary",size:"md"})}
    </div>
  `},o={render:()=>a({label:"Full Width Button",variant:"primary",fullWidth:!0})},l={render:()=>`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${a({label:"Loading...",variant:"primary",loading:!0})}
      ${a({label:"Loading...",variant:"secondary",outline:!0,loading:!0})}
    </div>
  `},c={render:()=>`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${a({label:"Disabled",variant:"primary",disabled:!0})}
      ${a({label:"Disabled",variant:"secondary",outline:!0,disabled:!0})}
    </div>
  `},d={name:"Toggle",render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-305); font-weight: var(--font-weight-bold); color: var(--gray-900);">Inactive State</h3>
        <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
          ${["neutral","primary","secondary","success","info","warning","danger"].map(e=>a({label:e.charAt(0).toUpperCase()+e.slice(1),variant:e,icon:"heart",toggle:!0})).join("")}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-305); font-weight: var(--font-weight-bold); color: var(--gray-900);">Active State (Pre-Toggled)</h3>
        <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
          ${["neutral","primary","secondary","success","info","warning","danger"].map(e=>a({label:e.charAt(0).toUpperCase()+e.slice(1),variant:e,icon:"heart",toggle:!0,active:!0})).join("")}
        </div>
      </div>
    </div>
  `,parameters:{docs:{description:{story:'Toggle button functionality with all color variants. Click to toggle .active class and aria-pressed attribute. Includes both inactive and active states with all variants. Uses data-ps-toggle="button" behavior.'}}}},p={name:"Toggle Icons Only",render:()=>`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${["neutral","primary","secondary","success","info","warning","danger"].map(e=>`
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
          <span style="font-size: var(--size-305); color: var(--gray-700);">${e}</span>
          ${a({icon:"heart",variant:e,toggle:!0,label:""})}
        </div>
      `).join("")}
    </div>
  `,parameters:{docs:{description:{story:"Toggle icon-only buttons with all color variants. Inactive: gray (#333333), Active: variant color. Perfect for favorite/like/bookmark actions."}}}};var g,u,y;r.parameters={...r.parameters,docs:{...(g=r.parameters)==null?void 0:g.docs,source:{originalSource:`{
  render: args => buttonTwig(args),
  args: {
    ...data,
    variant: 'neutral'
  }
}`,...(y=(u=r.parameters)==null?void 0:u.docs)==null?void 0:y.source}}};var m,v,f;t.parameters={...t.parameters,docs:{...(m=t.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant
  })).join('')}
    </div>
  \`
}`,...(f=(v=t.parameters)==null?void 0:v.docs)==null?void 0:f.source}}};var b,w,x;n.parameters={...n.parameters,docs:{...(b=n.parameters)==null?void 0:b.docs,source:{originalSource:`{
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
}`,...(x=(w=n.parameters)==null?void 0:w.docs)==null?void 0:x.source}}};var h,z,T;i.parameters={...i.parameters,docs:{...(h=i.parameters)==null?void 0:h.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: flex-end;">
      \${['xs', 'sm', 'md', 'lg', 'xl', 'xxl'].map(size => buttonTwig({
    label: size.toUpperCase(),
    variant: 'primary',
    size
  })).join('')}
    </div>
  \`
}`,...(T=(z=i.parameters)==null?void 0:z.docs)==null?void 0:T.source}}};var $,S,k;s.parameters={...s.parameters,docs:{...($=s.parameters)==null?void 0:$.docs,source:{originalSource:`{
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
    size: 'md'
  })}
    </div>
  \`
}`,...(k=(S=s.parameters)==null?void 0:S.docs)==null?void 0:k.source}}};var A,C,I;o.parameters={...o.parameters,docs:{...(A=o.parameters)==null?void 0:A.docs,source:{originalSource:`{
  render: () => buttonTwig({
    label: 'Full Width Button',
    variant: 'primary',
    fullWidth: true
  })
}`,...(I=(C=o.parameters)==null?void 0:C.docs)==null?void 0:I.source}}};var V,U,j;l.parameters={...l.parameters,docs:{...(V=l.parameters)==null?void 0:V.docs,source:{originalSource:`{
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
}`,...(j=(U=l.parameters)==null?void 0:U.docs)==null?void 0:j.source}}};var B,D,L;c.parameters={...c.parameters,docs:{...(B=c.parameters)==null?void 0:B.docs,source:{originalSource:`{
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
}`,...(L=(D=c.parameters)==null?void 0:D.docs)==null?void 0:L.source}}};var P,W,O;d.parameters={...d.parameters,docs:{...(P=d.parameters)==null?void 0:P.docs,source:{originalSource:`{
  name: 'Toggle',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-305); font-weight: var(--font-weight-bold); color: var(--gray-900);">Inactive State</h3>
        <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
          \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant,
    icon: 'heart',
    toggle: true
  })).join('')}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-305); font-weight: var(--font-weight-bold); color: var(--gray-900);">Active State (Pre-Toggled)</h3>
        <div style="display: flex; gap: var(--size-4); flex-wrap: wrap;">
          \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => buttonTwig({
    label: variant.charAt(0).toUpperCase() + variant.slice(1),
    variant,
    icon: 'heart',
    toggle: true,
    active: true
  })).join('')}
        </div>
      </div>
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Toggle button functionality with all color variants. Click to toggle .active class and aria-pressed attribute. Includes both inactive and active states with all variants. Uses data-ps-toggle="button" behavior.'
      }
    }
  }
}`,...(O=(W=d.parameters)==null?void 0:W.docs)==null?void 0:O.source}}};var _,F,E;p.parameters={...p.parameters,docs:{...(_=p.parameters)==null?void 0:_.docs,source:{originalSource:`{
  name: 'Toggle Icons Only',
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      \${['neutral', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'].map(variant => \`
        <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
          <span style="font-size: var(--size-305); color: var(--gray-700);">\${variant}</span>
          \${buttonTwig({
    icon: 'heart',
    variant,
    toggle: true,
    label: ''
  })}
        </div>
      \`).join('')}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Toggle icon-only buttons with all color variants. Inactive: gray (#333333), Active: variant color. Perfect for favorite/like/bookmark actions.'
      }
    }
  }
}`,...(E=(F=p.parameters)==null?void 0:F.docs)==null?void 0:E.source}}};const Q=["Default","Variants","Outlines","Sizes","WithIcons","FullWidth","Loading","Disabled","Toggle","ToggleIconsOnly"];export{r as Default,c as Disabled,o as FullWidth,l as Loading,n as Outlines,i as Sizes,d as Toggle,p as ToggleIconsOnly,t as Variants,s as WithIcons,Q as __namedExportsOrder,K as default};
