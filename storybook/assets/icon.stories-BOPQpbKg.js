import{i as $}from"./icons-list-Ce2D1ZE4.js";import{i as s}from"./icon-D1VyfcBq.js";import"./iframe-GGfdoSBx.js";import"./twig-Dqrk-56N.js";const a={name:"search",size:"md",color:"default",disabled:!1},C={title:"Elements/Icon",tags:["autodocs"],parameters:{docs:{description:{component:"Semantic SVG icon component using a generated sprite. Supports 6 sizes (xs to xxl) and semantic colors with full accessibility support."}}},argTypes:{baseClass:{description:"Override root BEM class for composition. When provided, Icon emits only this class and mapped modifiers; otherwise emits ps-icon classes.",control:{type:"text"},table:{category:"Structure",type:{summary:"string"},defaultValue:{summary:null}}},name:{description:'Icon name without "icon-" prefix. Backed by sprite generated from source/assets/icons/*.svg.',control:{type:"select"},options:$.all,table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"search"}}},size:{description:"Size: xs (10px), sm (16px), md (20px), lg (24px), xl (32px), xxl (48px)",control:{type:"select"},options:["xs","sm","md","lg","xl","xxl"],table:{category:"Appearance",type:{summary:"xs|sm|md|lg|xl|xxl"},defaultValue:{summary:"md"}}},color:{description:"Semantic color: default (gray), primary, secondary, success, warning, danger, info",control:{type:"select"},options:["default","primary","secondary","success","warning","danger","info"],table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:"default"}}},disabled:{description:"Disabled state (50% opacity)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},ariaLabel:{description:"Accessibility label (informative icons)",control:{type:"text"},table:{category:"Accessibility",type:{summary:"string"}}}}},r={render:e=>s(e),args:{...a},parameters:{docs:{description:{story:"Default icon configuration with medium size and default gray color."}}}},i={render:e=>`
    <div style="display: flex; align-items: center; gap: var(--size-6);">
      ${s({...e,size:"xs"})}
      ${s({...e,size:"sm"})}
      ${s({...e,size:"md"})}
      ${s({...e,size:"lg"})}
      ${s({...e,size:"xl"})}
      ${s({...e,size:"xxl"})}
    </div>
  `,args:{...a},parameters:{docs:{description:{story:"All available sizes from xs (10px) to xxl (48px), scaling via design tokens."}}}},n={render:e=>`
    <div style="display: flex; align-items: center; gap: var(--size-6);">
      ${s({...e,color:"default"})}
      ${s({...e,color:"primary"})}
      ${s({...e,color:"secondary"})}
      ${s({...e,color:"success"})}
      ${s({...e,color:"warning"})}
      ${s({...e,color:"danger"})}
      ${s({...e,color:"info"})}
    </div>
  `,args:{...a,name:"check",size:"xl"},parameters:{docs:{description:{story:"All semantic colors: default (gray), primary (BNP green), secondary (magenta), success, warning, danger, and info."}}}},o={render:e=>`
    <div style="display: flex; align-items: center; gap: var(--size-8);">
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
        ${s({...e,disabled:!1})}
        <span style="font-size: var(--font-size-0); color: var(--gray-600);">Enabled</span>
      </div>
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
        ${s({...e,disabled:!0})}
        <span style="font-size: var(--font-size-0); color: var(--gray-600);">Disabled</span>
      </div>
    </div>
  `,args:{...a,name:"search",size:"xl",color:"primary"},parameters:{docs:{description:{story:"Enabled vs disabled state (50% opacity when disabled)."}}}},t={render:()=>`
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: var(--size-5); padding: var(--size-6);">
      ${$.all.map(e=>`
          <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3); padding: var(--size-4); border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: var(--white); transition: all 150ms var(--ease-3); cursor: pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.borderColor='var(--primary)'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='var(--gray-300)'">
            <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px;">
              ${s({...a,name:e,size:"xl"})}
            </div>
            <span style="font-size: var(--font-size-0); color: var(--gray-700); text-align: center; word-break: break-word; font-weight: 500; line-height: 1.3;">${e}</span>
          </div>
        `).join("")}
    </div>
  `,parameters:{docs:{description:{story:"Full gallery of all available SVG icons built from source/assets/icons/*.svg via the generated sprite. Hover over icons for a subtle highlight."}}}};var l,c,d;r.parameters={...r.parameters,docs:{...(l=r.parameters)==null?void 0:l.docs,source:{originalSource:`{
  render: args => iconTwig(args),
  args: {
    ...data
  },
  parameters: {
    docs: {
      description: {
        story: 'Default icon configuration with medium size and default gray color.'
      }
    }
  }
}`,...(d=(c=r.parameters)==null?void 0:c.docs)==null?void 0:d.source}}};var p,g,m;i.parameters={...i.parameters,docs:{...(p=i.parameters)==null?void 0:p.docs,source:{originalSource:`{
  render: args => \`
    <div style="display: flex; align-items: center; gap: var(--size-6);">
      \${iconTwig({
    ...args,
    size: 'xs'
  })}
      \${iconTwig({
    ...args,
    size: 'sm'
  })}
      \${iconTwig({
    ...args,
    size: 'md'
  })}
      \${iconTwig({
    ...args,
    size: 'lg'
  })}
      \${iconTwig({
    ...args,
    size: 'xl'
  })}
      \${iconTwig({
    ...args,
    size: 'xxl'
  })}
    </div>
  \`,
  args: {
    ...data
  },
  parameters: {
    docs: {
      description: {
        story: 'All available sizes from xs (10px) to xxl (48px), scaling via design tokens.'
      }
    }
  }
}`,...(m=(g=i.parameters)==null?void 0:g.docs)==null?void 0:m.source}}};var y,u,v;n.parameters={...n.parameters,docs:{...(y=n.parameters)==null?void 0:y.docs,source:{originalSource:`{
  render: args => \`
    <div style="display: flex; align-items: center; gap: var(--size-6);">
      \${iconTwig({
    ...args,
    color: 'default'
  })}
      \${iconTwig({
    ...args,
    color: 'primary'
  })}
      \${iconTwig({
    ...args,
    color: 'secondary'
  })}
      \${iconTwig({
    ...args,
    color: 'success'
  })}
      \${iconTwig({
    ...args,
    color: 'warning'
  })}
      \${iconTwig({
    ...args,
    color: 'danger'
  })}
      \${iconTwig({
    ...args,
    color: 'info'
  })}
    </div>
  \`,
  args: {
    ...data,
    name: 'check',
    size: 'xl'
  },
  parameters: {
    docs: {
      description: {
        story: 'All semantic colors: default (gray), primary (BNP green), secondary (magenta), success, warning, danger, and info.'
      }
    }
  }
}`,...(v=(u=n.parameters)==null?void 0:u.docs)==null?void 0:v.source}}};var x,f,b;o.parameters={...o.parameters,docs:{...(x=o.parameters)==null?void 0:x.docs,source:{originalSource:`{
  render: args => \`
    <div style="display: flex; align-items: center; gap: var(--size-8);">
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
        \${iconTwig({
    ...args,
    disabled: false
  })}
        <span style="font-size: var(--font-size-0); color: var(--gray-600);">Enabled</span>
      </div>
      <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2);">
        \${iconTwig({
    ...args,
    disabled: true
  })}
        <span style="font-size: var(--font-size-0); color: var(--gray-600);">Disabled</span>
      </div>
    </div>
  \`,
  args: {
    ...data,
    name: 'search',
    size: 'xl',
    color: 'primary'
  },
  parameters: {
    docs: {
      description: {
        story: 'Enabled vs disabled state (50% opacity when disabled).'
      }
    }
  }
}`,...(b=(f=o.parameters)==null?void 0:f.docs)==null?void 0:b.source}}};var z,h,w;t.parameters={...t.parameters,docs:{...(z=t.parameters)==null?void 0:z.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: var(--size-5); padding: var(--size-6);">
      \${iconsList.all.map(iconName => \`
          <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3); padding: var(--size-4); border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: var(--white); transition: all 150ms var(--ease-3); cursor: pointer;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.borderColor='var(--primary)'" onmouseout="this.style.boxShadow='none'; this.style.borderColor='var(--gray-300)'">
            <div style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px;">
              \${iconTwig({
    ...data,
    name: iconName,
    size: 'xl'
  })}
            </div>
            <span style="font-size: var(--font-size-0); color: var(--gray-700); text-align: center; word-break: break-word; font-weight: 500; line-height: 1.3;">\${iconName}</span>
          </div>
        \`).join('')}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Full gallery of all available SVG icons built from source/assets/icons/*.svg via the generated sprite. Hover over icons for a subtle highlight.'
      }
    }
  }
}`,...(w=(h=t.parameters)==null?void 0:h.docs)==null?void 0:w.source}}};const V=["Default","AllSizes","AllColors","AllStates","Gallery"];export{n as AllColors,i as AllSizes,o as AllStates,r as Default,t as Gallery,V as __namedExportsOrder,C as default};
