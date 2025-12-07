import{i as b}from"./icons-list-Ce2D1ZE4.js";import{l as r}from"./link-BDN26dL_.js";import"./iframe-C-ciPShf.js";import"./twig-B9SdSbF4.js";const w={text:"Property listing details",url:"/property/listing/modern-office-building",underline:!0,icon:"",iconPosition:"right",target:"_self",rel:"",disabled:!1},A={title:"Elements/Link",tags:["autodocs"],parameters:{docs:{description:{component:"Semantic text link with optional icon and variant colors. Supports underline control, external target handling, and focus-visible accessibility."}}},argTypes:{text:{description:"Link text content displayed to user",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"Link text"}}},icon:{description:'Icon name without "icon-" prefix (e.g., arrow-right, arrow-left, external-link, download)',control:{type:"select"},options:["",...b.all],table:{category:"Content",type:{summary:"string"},defaultValue:{summary:""}}},color:{description:"Link color variant: semantic colors for navigation, CTAs, and status indicators. Default (no class) uses current text color.",control:{type:"select"},options:[null,"primary","secondary","info","warning","success","danger","dark","light"],table:{category:"Appearance",type:{summary:"null | primary | secondary | info | warning | success | danger | dark | light"},defaultValue:{summary:"null (currentColor)"}}},size:{description:"Link size variant: adapt for hierarchy, accessibility, and context. Default (no class) uses md (16px).",control:{type:"select"},options:[null,"xs","sm","md","lg","xl","xxl"],table:{category:"Appearance",type:{summary:"null | xs | sm | md | lg | xl | xxl"},defaultValue:{summary:"null (md)"}}},underline:{description:"Show underline decoration (hover removes it, default: true)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:"true"}}},iconPosition:{description:"Icon position relative to text (left or right, default: right)",control:{type:"select"},options:["left","right"],table:{category:"Appearance",type:{summary:"left | right"},defaultValue:{summary:"right"}}},url:{description:"Link destination URL or anchor",control:{type:"text"},table:{category:"Link",type:{summary:"string",required:!0},defaultValue:{summary:"#"}}},target:{description:"Link target (_self for same window, _blank for new tab with security attributes)",control:{type:"select"},options:["_self","_blank"],table:{category:"Link",type:{summary:"_self | _blank"},defaultValue:{summary:"_self"}}},rel:{description:'Custom rel attribute (auto-set to "noopener noreferrer" for target="_blank")',control:{type:"text"},table:{category:"Link",type:{summary:"string"},defaultValue:{summary:""}}},disabled:{description:"Disabled state (renders as <span> with aria-disabled, pointer-events: none)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},baseClass:{description:"Override root class when composing inside other components. Modifiers map to baseClass variants; elements render as baseClass__*.",control:{type:"text"},table:{category:"Layout",type:{summary:"string"},defaultValue:{summary:"ps-link"}}}}},e={render:h=>r(h),args:{...w}},i={render:()=>`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All available color variants for links. Default uses current text color. Use semantic colors for real estate navigation, CTAs, and status indicators.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (currentColor, no class)</p>
        ${r({text:"View property details",url:"/property/details"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary variant</p>
        ${r({text:"Schedule property tour",url:"#",color:"primary"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary variant</p>
        ${r({text:"Contact real estate agent",url:"#",color:"secondary"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info variant</p>
        ${r({text:"Property information",url:"#",color:"info"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning variant</p>
        ${r({text:"Limited time offer",url:"#",color:"warning"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success variant</p>
        ${r({text:"Property available",url:"#",color:"success"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger variant</p>
        ${r({text:"Property sold",url:"#",color:"danger"})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark variant</p>
        ${r({text:"View all properties",url:"#",color:"dark"})}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--white);">Light variant (for dark backgrounds)</p>
        ${r({text:"Footer navigation link",url:"#",color:"light"})}
      </div>
    </div>
  `},t={render:()=>`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All supported link sizes. Adapt link size for hierarchy, accessibility, and context (menus, footers, property listings).</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>${r({text:"Extra small property link",url:"#",size:"xs",color:"primary"})}</div>
      <div>${r({text:"Small property link",url:"#",size:"sm",color:"primary"})}</div>
      <div>${r({text:"Medium property link",url:"#",size:"md",color:"primary"})}</div>
      <div>${r({text:"Large property link",url:"#",size:"lg",color:"primary"})}</div>
      <div>${r({text:"Extra large property link",url:"#",size:"xl",color:"primary"})}</div>
      <div>${r({text:"XXL property link",url:"#",size:"xxl",color:"primary"})}</div>
    </div>
  `},a={render:()=>`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All interactive states and icon options. Demonstrates underline, disabled, external, and icon positioning for real estate use.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With underline (default, hover removes it)</p>
        ${r({text:"Property with underline",url:"#",color:"primary",underline:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Without underline</p>
        ${r({text:"Property without underline",url:"#",color:"primary",underline:!1})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (renders as span with aria-disabled)</p>
        ${r({text:"Property unavailable",url:"#",disabled:!0})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon right</p>
        ${r({text:"Next property listing",url:"#",icon:"arrow-right",iconPosition:"right",color:"primary",underline:!1})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon left</p>
        ${r({text:"Previous property listing",url:"#",icon:"arrow-left",iconPosition:"left",color:"primary",underline:!1})}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">External (target="_blank" with security rel)</p>
        ${r({text:"External property portal",url:"https://example.com",target:"_blank",color:"primary"})}
      </div>
    </div>
  `},o={render:()=>`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">Typical real estate use cases: navigation, call-to-action, external resources, and footer links. All examples use contextual real estate content.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">Standard link in paragraph</h3>
        <p style="max-width: 600px;">
          Discover our portfolio of modern office buildings across Paris. 
          ${r({text:"Learn more about commercial properties",url:"/properties/commercial",color:"primary"})} 
          and find the perfect space for your business needs.
        </p>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">Navigation link with icon</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          ${r({text:"Next property listing",url:"#",icon:"arrow-right",iconPosition:"right",underline:!1,color:"primary"})}
          ${r({text:"Previous property listing",url:"#",icon:"arrow-left",iconPosition:"left",underline:!1,color:"primary"})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">External resource</h3>
        ${r({text:"View property on external portal",url:"https://example.com",target:"_blank",color:"primary"})}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-6); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; color: var(--white);">Link on dark background</h3>
        ${r({text:"Contact our real estate team",url:"/contact",color:"light",underline:!0})}
      </div>
    </div>
  `};var n,l,s;e.parameters={...e.parameters,docs:{...(n=e.parameters)==null?void 0:n.docs,source:{originalSource:`{
  render: args => linkTwig(args),
  args: {
    ...data
  }
}`,...(s=(l=e.parameters)==null?void 0:l.docs)==null?void 0:s.source}}};var d,c,p;i.parameters={...i.parameters,docs:{...(d=i.parameters)==null?void 0:d.docs,source:{originalSource:`{
  render: () => \`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All available color variants for links. Default uses current text color. Use semantic colors for real estate navigation, CTAs, and status indicators.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (currentColor, no class)</p>
        \${linkTwig({
    text: 'View property details',
    url: '/property/details'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary variant</p>
        \${linkTwig({
    text: 'Schedule property tour',
    url: '#',
    color: 'primary'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary variant</p>
        \${linkTwig({
    text: 'Contact real estate agent',
    url: '#',
    color: 'secondary'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info variant</p>
        \${linkTwig({
    text: 'Property information',
    url: '#',
    color: 'info'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning variant</p>
        \${linkTwig({
    text: 'Limited time offer',
    url: '#',
    color: 'warning'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success variant</p>
        \${linkTwig({
    text: 'Property available',
    url: '#',
    color: 'success'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger variant</p>
        \${linkTwig({
    text: 'Property sold',
    url: '#',
    color: 'danger'
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark variant</p>
        \${linkTwig({
    text: 'View all properties',
    url: '#',
    color: 'dark'
  })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--white);">Light variant (for dark backgrounds)</p>
        \${linkTwig({
    text: 'Footer navigation link',
    url: '#',
    color: 'light'
  })}
      </div>
    </div>
  \`
}`,...(p=(c=i.parameters)==null?void 0:c.docs)==null?void 0:p.source}}};var v,u,y;t.parameters={...t.parameters,docs:{...(v=t.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: () => \`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All supported link sizes. Adapt link size for hierarchy, accessibility, and context (menus, footers, property listings).</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>\${linkTwig({
    text: 'Extra small property link',
    url: '#',
    size: 'xs',
    color: 'primary'
  })}</div>
      <div>\${linkTwig({
    text: 'Small property link',
    url: '#',
    size: 'sm',
    color: 'primary'
  })}</div>
      <div>\${linkTwig({
    text: 'Medium property link',
    url: '#',
    size: 'md',
    color: 'primary'
  })}</div>
      <div>\${linkTwig({
    text: 'Large property link',
    url: '#',
    size: 'lg',
    color: 'primary'
  })}</div>
      <div>\${linkTwig({
    text: 'Extra large property link',
    url: '#',
    size: 'xl',
    color: 'primary'
  })}</div>
      <div>\${linkTwig({
    text: 'XXL property link',
    url: '#',
    size: 'xxl',
    color: 'primary'
  })}</div>
    </div>
  \`
}`,...(y=(u=t.parameters)==null?void 0:u.docs)==null?void 0:y.source}}};var g,m,f;a.parameters={...a.parameters,docs:{...(g=a.parameters)==null?void 0:g.docs,source:{originalSource:`{
  render: () => \`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">All interactive states and icon options. Demonstrates underline, disabled, external, and icon positioning for real estate use.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With underline (default, hover removes it)</p>
        \${linkTwig({
    text: 'Property with underline',
    url: '#',
    color: 'primary',
    underline: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Without underline</p>
        \${linkTwig({
    text: 'Property without underline',
    url: '#',
    color: 'primary',
    underline: false
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Disabled (renders as span with aria-disabled)</p>
        \${linkTwig({
    text: 'Property unavailable',
    url: '#',
    disabled: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon right</p>
        \${linkTwig({
    text: 'Next property listing',
    url: '#',
    icon: 'arrow-right',
    iconPosition: 'right',
    color: 'primary',
    underline: false
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">With icon left</p>
        \${linkTwig({
    text: 'Previous property listing',
    url: '#',
    icon: 'arrow-left',
    iconPosition: 'left',
    color: 'primary',
    underline: false
  })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">External (target="_blank" with security rel)</p>
        \${linkTwig({
    text: 'External property portal',
    url: 'https://example.com',
    target: '_blank',
    color: 'primary'
  })}
      </div>
    </div>
  \`
}`,...(f=(m=a.parameters)==null?void 0:m.docs)==null?void 0:f.source}}};var z,x,k;o.parameters={...o.parameters,docs:{...(z=o.parameters)==null?void 0:z.docs,source:{originalSource:`{
  render: () => \`
    <p style="margin-bottom: var(--size-3); color: var(--gray-700); font-size: var(--font-size-1);">Typical real estate use cases: navigation, call-to-action, external resources, and footer links. All examples use contextual real estate content.</p>
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">Standard link in paragraph</h3>
        <p style="max-width: 600px;">
          Discover our portfolio of modern office buildings across Paris. 
          \${linkTwig({
    text: 'Learn more about commercial properties',
    url: '/properties/commercial',
    color: 'primary'
  })} 
          and find the perfect space for your business needs.
        </p>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">Navigation link with icon</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          \${linkTwig({
    text: 'Next property listing',
    url: '#',
    icon: 'arrow-right',
    iconPosition: 'right',
    underline: false,
    color: 'primary'
  })}
          \${linkTwig({
    text: 'Previous property listing',
    url: '#',
    icon: 'arrow-left',
    iconPosition: 'left',
    underline: false,
    color: 'primary'
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0;">External resource</h3>
        \${linkTwig({
    text: 'View property on external portal',
    url: 'https://example.com',
    target: '_blank',
    color: 'primary'
  })}
      </div>
      <div style="background-color: var(--gray-800); padding: var(--size-6); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; color: var(--white);">Link on dark background</h3>
        \${linkTwig({
    text: 'Contact our real estate team',
    url: '/contact',
    color: 'light',
    underline: true
  })}
      </div>
    </div>
  \`
}`,...(k=(x=o.parameters)==null?void 0:x.docs)==null?void 0:k.source}}};const S=["Default","AllColors","AllSizes","AllStates","UseCases"];export{i as AllColors,t as AllSizes,a as AllStates,e as Default,o as UseCases,S as __namedExportsOrder,A as default};
