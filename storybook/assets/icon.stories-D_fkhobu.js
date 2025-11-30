import{i as m}from"./icons-list-BivTTKC1.js";import{i as e}from"./icon-BnjtxcgP.js";import"./iframe-wV_yutGI.js";import"https://kit.fontawesome.com/a0eb0bad75.js";import"./twig--ZzzhHos.js";const O={name:"search",size:"medium",disabled:!1},F={title:"Elements/Icon",tags:["autodocs"],parameters:{docs:{description:{component:`Semantic icon component using BNPRE icon fonts.
Supports sizes, color tokens, and accessibility for informative vs decorative usage.`}}},argTypes:{name:{description:'Icon name without "icon-" prefix (e.g., search, check, pin-map, poi-hotel)',control:{type:"select"},options:m.all,table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"search"}}},size:{description:"Icon size (small: 16px, medium: 20px, large: 24px, xlarge: 32px)",control:{type:"select"},options:["small","medium","large","xlarge"],table:{category:"Appearance",type:{summary:"small | medium | large | xlarge"},defaultValue:{summary:"medium"}}},color:{description:"Custom color (CSS color value or design token)",control:{type:"color"},table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:"inherit"}}},disabled:{description:"Disabled state (50% opacity, no pointer events)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},ariaLabel:{description:"Accessibility label (use for informative icons, omit for decorative)",control:{type:"text"},table:{category:"Accessibility",type:{summary:"string"},defaultValue:{summary:""}}}}},i={render:s=>e(s),args:{...O}},r={render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"search",size:"small"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">small (16px)</span>
      ${e({name:"search",size:"medium"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">medium (20px)</span>
      ${e({name:"search",size:"large"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">large (24px)</span>
      ${e({name:"search",size:"xlarge"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">xlarge (32px)</span>
    </div>
  `},n={render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"check",size:"xlarge",color:"var(--bnp-green)"})}
      <span>Success (green)</span>
      ${e({name:"close",size:"xlarge",color:"var(--red-600)"})}
      <span>Danger (red)</span>
      ${e({name:"infos",size:"xlarge",color:"var(--blue-500)"})}
      <span>Info (blue)</span>
      ${e({name:"help",size:"xlarge",color:"var(--amber-500)"})}
      <span>Warning (amber)</span>
    </div>
  `},o={render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"search",size:"xlarge",disabled:!1})}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Normal</span>
      ${e({name:"search",size:"xlarge",disabled:!0})}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Disabled (50% opacity)</span>
    </div>
  `},t={name:"All Icons (Categorized)",render:()=>{const s=m.categories||{},d=new Set;Object.values(s).forEach(a=>a.forEach(c=>d.add(c)));const E=m.all.filter(a=>!d.has(a));return`
      <div style="display:flex; flex-direction:column; gap:var(--size-8);">
        ${Object.entries(s).map(([a,c])=>`
          <section>
            <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">${a.charAt(0).toUpperCase()+a.slice(1)}</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
              ${c.map(p=>`
                <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-200); border-radius: var(--radius-2);">
                  ${e({name:p,size:"large"})}
                  <span style="font-size: var(--font-size--1); text-align:center;">${p.replace("icon-","")}</span>
                </div>
              `).join("")}
            </div>
          </section>
        `).join("")}
        <section>
          <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">Others</h3>
          <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
            ${E.map(a=>`
              <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-100); border-radius: var(--radius-2);">
                ${e({name:a,size:"large"})}
                <span style="font-size: var(--font-size--1); text-align:center;">${a.replace("icon-","")}</span>
              </div>
            `).join("")}
          </div>
        </section>
      </div>
    `}},l={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Search & Navigation</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${e({name:"search",size:"medium"})}
          ${e({name:"pin-map",size:"medium"})}
          ${e({name:"arrow-left",size:"medium"})}
          ${e({name:"arrow-right",size:"medium"})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Actions</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${e({name:"check",size:"medium",color:"var(--bnp-green)"})}
          ${e({name:"close",size:"medium",color:"var(--red-600)"})}
          ${e({name:"edit",size:"medium"})}
          ${e({name:"bin",size:"medium"})}
          ${e({name:"share",size:"medium"})}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Controls</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${e({name:"checkbox",size:"medium"})}
          ${e({name:"checkbox-checked",size:"medium",color:"var(--bnp-green)"})}
          ${e({name:"radio-unselected",size:"medium"})}
          ${e({name:"radio-selected",size:"medium",color:"var(--bnp-green)"})}
        </div>
      </div>
    </div>
  `};var v,g,z;i.parameters={...i.parameters,docs:{...(v=i.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: args => icon(args),
  args: {
    ...data
  }
}`,...(z=(g=i.parameters)==null?void 0:g.docs)==null?void 0:z.source}}};var u,y,f;r.parameters={...r.parameters,docs:{...(u=r.parameters)==null?void 0:u.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'search',
    size: 'small'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">small (16px)</span>
      \${icon({
    name: 'search',
    size: 'medium'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">medium (20px)</span>
      \${icon({
    name: 'search',
    size: 'large'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">large (24px)</span>
      \${icon({
    name: 'search',
    size: 'xlarge'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">xlarge (32px)</span>
    </div>
  \`
}`,...(f=(y=r.parameters)==null?void 0:y.docs)==null?void 0:f.source}}};var x,h,$;n.parameters={...n.parameters,docs:{...(x=n.parameters)==null?void 0:x.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'check',
    size: 'xlarge',
    color: 'var(--bnp-green)'
  })}
      <span>Success (green)</span>
      \${icon({
    name: 'close',
    size: 'xlarge',
    color: 'var(--red-600)'
  })}
      <span>Danger (red)</span>
      \${icon({
    name: 'infos',
    size: 'xlarge',
    color: 'var(--blue-500)'
  })}
      <span>Info (blue)</span>
      \${icon({
    name: 'help',
    size: 'xlarge',
    color: 'var(--amber-500)'
  })}
      <span>Warning (amber)</span>
    </div>
  \`
}`,...($=(h=n.parameters)==null?void 0:h.docs)==null?void 0:$.source}}};var b,S,A;o.parameters={...o.parameters,docs:{...(b=o.parameters)==null?void 0:b.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'search',
    size: 'xlarge',
    disabled: false
  })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Normal</span>
      \${icon({
    name: 'search',
    size: 'xlarge',
    disabled: true
  })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Disabled (50% opacity)</span>
    </div>
  \`
}`,...(A=(S=o.parameters)==null?void 0:S.docs)==null?void 0:A.source}}};var k,C,j;t.parameters={...t.parameters,docs:{...(k=t.parameters)==null?void 0:k.docs,source:{originalSource:`{
  name: 'All Icons (Categorized)',
  render: () => {
    const categories = iconsList.categories || {};
    const used = new Set();
    Object.values(categories).forEach(arr => arr.forEach(i => used.add(i)));
    const others = iconsList.all.filter(i => !used.has(i));
    return \`
      <div style="display:flex; flex-direction:column; gap:var(--size-8);">
        \${Object.entries(categories).map(([key, list]) => \`
          <section>
            <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">\${key.charAt(0).toUpperCase() + key.slice(1)}</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
              \${list.map(name => \`
                <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-200); border-radius: var(--radius-2);">
                  \${icon({
      name,
      size: 'large'
    })}
                  <span style="font-size: var(--font-size--1); text-align:center;">\${name.replace('icon-', '')}</span>
                </div>
              \`).join('')}
            </div>
          </section>
        \`).join('')}
        <section>
          <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">Others</h3>
          <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
            \${others.map(name => \`
              <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-100); border-radius: var(--radius-2);">
                \${icon({
      name,
      size: 'large'
    })}
                <span style="font-size: var(--font-size--1); text-align:center;">\${name.replace('icon-', '')}</span>
              </div>
            \`).join('')}
          </div>
        </section>
      </div>
    \`;
  }
}`,...(j=(C=t.parameters)==null?void 0:C.docs)==null?void 0:j.source}}};var I,w,D;l.parameters={...l.parameters,docs:{...(I=l.parameters)==null?void 0:I.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Search & Navigation</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          \${icon({
    name: 'search',
    size: 'medium'
  })}
          \${icon({
    name: 'pin-map',
    size: 'medium'
  })}
          \${icon({
    name: 'arrow-left',
    size: 'medium'
  })}
          \${icon({
    name: 'arrow-right',
    size: 'medium'
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Actions</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          \${icon({
    name: 'check',
    size: 'medium',
    color: 'var(--bnp-green)'
  })}
          \${icon({
    name: 'close',
    size: 'medium',
    color: 'var(--red-600)'
  })}
          \${icon({
    name: 'edit',
    size: 'medium'
  })}
          \${icon({
    name: 'bin',
    size: 'medium'
  })}
          \${icon({
    name: 'share',
    size: 'medium'
  })}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Controls</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          \${icon({
    name: 'checkbox',
    size: 'medium'
  })}
          \${icon({
    name: 'checkbox-checked',
    size: 'medium',
    color: 'var(--bnp-green)'
  })}
          \${icon({
    name: 'radio-unselected',
    size: 'medium'
  })}
          \${icon({
    name: 'radio-selected',
    size: 'medium',
    color: 'var(--bnp-green)'
  })}
        </div>
      </div>
    </div>
  \`
}`,...(D=(w=l.parameters)==null?void 0:w.docs)==null?void 0:D.source}}};const W=["Default","AllSizes","AllColors","AllStates","AllIcons","UseCases"];export{n as AllColors,t as AllIcons,r as AllSizes,o as AllStates,i as Default,l as UseCases,W as __namedExportsOrder,F as default};
