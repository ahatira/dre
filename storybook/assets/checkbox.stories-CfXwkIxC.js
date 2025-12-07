import{d as Le,c as a}from"./checkbox-DrRk-V4t.js";import"./iframe-GGfdoSBx.js";import"./twig-Dqrk-56N.js";const Ee={title:"Elements/Checkbox",render:n=>a(n),tags:["autodocs"],parameters:{docs:{description:{component:"Native checkbox input with custom SVG icon styling. Supports 8 semantic color variants (primary, secondary, success, warning, danger, info, dark, light) and 6 sizes (xs, sm, md, lg, xl, xxl). Includes checked/unchecked/indeterminate/disabled states with smooth animations. Fully accessible with keyboard navigation and WCAG 2.2 AA compliance."}}},argTypes:{name:{description:"Input name attribute (required)",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"checkbox-name"}}},value:{description:"Input value attribute (required)",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"checkbox-value"}}},label:{description:"Checkbox label text (optional)",control:{type:"text"},table:{category:"Content",type:{summary:"string"}}},color:{description:"Color variant",control:{type:"select"},options:["primary","secondary","success","warning","danger","info","dark","light"],table:{category:"Appearance",type:{summary:"primary | secondary | success | warning | danger | info | dark | light"},defaultValue:{summary:"primary"}}},size:{description:"Size variant",control:{type:"select"},options:["xs","sm","md","lg","xl","xxl"],table:{category:"Appearance",type:{summary:"xs | sm | md | lg | xl | xxl"},defaultValue:{summary:"md"}}},checked:{description:"Checked state",control:{type:"boolean"},table:{category:"State",type:{summary:"boolean"},defaultValue:{summary:"false"}}},indeterminate:{description:"Indeterminate state (neither checked nor unchecked)",control:{type:"boolean"},table:{category:"State",type:{summary:"boolean"},defaultValue:{summary:"false"}}},disabled:{description:"Disabled state",control:{type:"boolean"},table:{category:"State",type:{summary:"boolean"},defaultValue:{summary:"false"}}},id:{description:"Unique ID for input (auto-generated if not provided)",control:{type:"text"},table:{category:"Advanced",type:{summary:"string"}}}},args:{...Le}},i={args:{name:"property-type",value:"apartment",label:"Apartment",checked:!1,disabled:!1}},s={args:{name:"property-features",value:"parking",label:"Parking available",checked:!0,disabled:!1}},l={args:{name:"property-features",value:"garden",label:"Garden (not available)",checked:!1,disabled:!0}},o={args:{name:"property-features",value:"balcony",label:"Balcony (included)",checked:!0,disabled:!0}},c={args:{name:"agreement",value:"accepted",label:null,checked:!1,disabled:!1}},d={args:{name:"terms",value:"accepted",label:"I agree to the terms and conditions of the real estate transaction, including the payment schedule and property inspection requirements.",checked:!1,disabled:!1}},m={render:()=>{const n=[{name:"property-type",value:"apartment",label:"Apartment",checked:!0},{name:"property-type",value:"house",label:"House",checked:!1},{name:"property-type",value:"commercial",label:"Commercial Space",checked:!1},{name:"property-type",value:"land",label:"Land",checked:!1}],e=[{name:"features",value:"parking",label:"Parking",checked:!0},{name:"features",value:"elevator",label:"Elevator",checked:!0},{name:"features",value:"balcony",label:"Balcony",checked:!1},{name:"features",value:"garden",label:"Garden",checked:!1},{name:"features",value:"pool",label:"Swimming Pool",checked:!1}];return`
      <div style="max-width: 600px;">
        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Property Type</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
          ${n.map(r=>a(r)).join("")}
        </div>

        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Required Features</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
          ${e.map(r=>a(r)).join("")}
        </div>
      </div>
    `}},p={render:()=>`
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; max-width: 600px;">
        ${[{name:"amenities",value:"gym",label:"Fitness Center"},{name:"amenities",value:"concierge",label:"24/7 Concierge"},{name:"amenities",value:"security",label:"Security System"},{name:"amenities",value:"storage",label:"Storage Space"},{name:"amenities",value:"bike",label:"Bike Storage"},{name:"amenities",value:"terrace",label:"Rooftop Terrace"}].map(e=>a(e)).join("")}
      </div>
    `},u={render:()=>`
      <div style="display: flex; flex-direction: column; gap: 2rem;">
        ${["primary","secondary","success","warning","danger","info","dark","light"].map(e=>`
          <div>
            <h4 style="margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">${e}</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; padding: 1.5rem; background-color: ${e==="light"?"var(--gray-800)":"var(--gray-50)"}; border-radius: 8px;">
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Unchecked</span>
                ${a({name:`color-${e}-unchecked`,value:"unchecked",label:"Default state",color:e,checked:!1})}
              </div>
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Checked</span>
                ${a({name:`color-${e}-checked`,value:"checked",label:"Selected state",color:e,checked:!0})}
              </div>
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Indeterminate</span>
                ${a({name:`color-${e}-indeterminate`,value:"indeterminate",label:"Partial selection",color:e,indeterminate:!0})}
              </div>
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Disabled</span>
                ${a({name:`color-${e}-disabled`,value:"disabled-unchecked",label:"Disabled",color:e,disabled:!0})}
                ${a({name:`color-${e}-disabled-checked`,value:"disabled-checked",label:"Disabled checked",color:e,checked:!0,disabled:!0})}
              </div>
            </div>
          </div>
        `).join("")}
      </div>
    `,parameters:{docs:{description:{story:"Complete showcase of all 8 color variants with all states. Note: unchecked icons stay gray, only checked state displays the color variant. Light variant shown on dark background for visibility."}}}},g={render:()=>`
      <div style="display: flex; flex-direction: column; gap: 2rem;">
        ${[{value:"xs",label:"Extra Small",pixels:"12px"},{value:"sm",label:"Small",pixels:"16px"},{value:"md",label:"Medium (Default)",pixels:"24px"},{value:"lg",label:"Large",pixels:"28px"},{value:"xl",label:"Extra Large",pixels:"32px"},{value:"xxl",label:"Extra Extra Large",pixels:"40px"}].map(e=>`
          <div>
            <h4 style="margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">
              ${e.value} — ${e.label} <span style="font-weight: 400; color: var(--gray-500);">(${e.pixels})</span>
            </h4>
            <div style="display: flex; flex-wrap: wrap; gap: 2rem; padding: 1.5rem; background-color: var(--gray-50); border-radius: 8px; align-items: center;">
              ${a({name:`size-${e.value}-unchecked`,value:"unchecked",label:"Unchecked",size:e.value,checked:!1})}
              ${a({name:`size-${e.value}-checked`,value:"checked",label:"Checked",size:e.value,checked:!0})}
              ${a({name:`size-${e.value}-indeterminate`,value:"indeterminate",label:"Indeterminate",size:e.value,indeterminate:!0})}
              ${a({name:`size-${e.value}-disabled`,value:"disabled",label:"Disabled",size:e.value,disabled:!0})}
            </div>
          </div>
        `).join("")}
      </div>
    `,parameters:{docs:{description:{story:"Complete showcase of all 6 size variants with pixel dimensions. Font sizes scale proportionally with checkbox size for optimal readability."}}}},b={render:()=>`
      <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 600px;">
        ${[{title:"Interactive States",description:"Hover over these checkboxes to see animated transitions",states:[{label:"Unchecked",checked:!1},{label:"Checked",checked:!0},{label:"Indeterminate",checked:!1,indeterminate:!0}]},{title:"Disabled States",description:"Non-interactive states with reduced opacity",states:[{label:"Disabled (unchecked)",checked:!1,disabled:!0},{label:"Disabled (checked)",checked:!0,disabled:!0},{label:"Disabled (indeterminate)",checked:!1,disabled:!0,indeterminate:!0}]}].map(e=>`
          <div>
            <h4 style="margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">${e.title}</h4>
            <p style="font-size: 0.75rem; color: var(--gray-500); margin-bottom: 1rem;">${e.description}</p>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; padding: 1.5rem; background-color: var(--gray-50); border-radius: 8px;">
              ${e.states.map(r=>`
                ${a({name:`state-${e.title.toLowerCase().replace(/\s+/g,"-")}`,value:r.label.toLowerCase().replace(/\s+/g,"-"),label:r.label,...r})}
              `).join("")}
            </div>
          </div>
        `).join("")}
      </div>
    `,parameters:{docs:{description:{story:"Complete demonstration of all checkbox states organized by category: interactive states (unchecked, checked, indeterminate) and disabled states (all variations). Hover over interactive checkboxes to see smooth animated transitions."}}}},y={render:()=>`
      <div style="max-width: 700px;">
        <div style="margin-bottom: 2rem; padding: 1rem; background-color: var(--color-info-light); border-left: 4px solid var(--color-info); border-radius: 4px;">
          <p style="font-size: 0.875rem; color: var(--gray-700); margin: 0;">
            <strong>Indeterminate state</strong> indicates a parent checkbox controlling multiple children where <strong>some (but not all) are selected</strong>. Common in hierarchical filters and multi-level selection interfaces.
          </p>
        </div>

        <div style="padding: 2rem; background-color: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
          <div style="margin-bottom: 2rem;">
            <h4 style="margin-bottom: 1.25rem; font-size: 1rem; font-weight: 600; color: var(--gray-800);">Property Amenities Filter</h4>
            ${a({name:"amenities-all",value:"all",label:"All amenities (2 of 4 selected — indeterminate)",indeterminate:!0})}
          </div>

          <div style="margin-left: 2rem; padding-left: 1.5rem; border-left: 2px solid var(--gray-300);">
            <h5 style="margin-bottom: 1rem; font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; color: var(--gray-600); letter-spacing: 0.05em;">Available amenities:</h5>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
              ${a({name:"amenities",value:"gym",label:"Fitness Center & Gym",checked:!0,color:"success"})}
              ${a({name:"amenities",value:"pool",label:"Swimming Pool & Jacuzzi",checked:!0,color:"success"})}
              ${a({name:"amenities",value:"spa",label:"Spa & Wellness Center",checked:!1})}
              ${a({name:"amenities",value:"concierge",label:"24/7 Concierge Service",checked:!1})}
            </div>
          </div>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background-color: var(--gray-100); border-radius: 4px;">
          <p style="font-size: 0.8125rem; color: var(--gray-600); margin: 0;">
            <strong>UX Pattern:</strong> Clicking the parent checkbox should select/deselect all children. In a real implementation, JavaScript would handle the state synchronization between parent and child checkboxes.
          </p>
        </div>
      </div>
    `,parameters:{docs:{description:{story:"Demonstrates the indeterminate state in a realistic hierarchical selection scenario. The parent checkbox displays an indeterminate icon when some (but not all) child options are selected. In real implementations, JavaScript manages state synchronization between parent and children."}}}},v={render:()=>{const n=[{title:"Compact Filters",description:"Small checkboxes for dense filter interfaces",combinations:[{color:"primary",size:"xs",label:"Type: Office",useCase:"Property type filter"},{color:"secondary",size:"sm",label:"Available now",useCase:"Availability filter"}]},{title:"Standard Forms",description:"Medium-sized checkboxes for typical forms",combinations:[{color:"success",size:"md",label:"Verified listing",useCase:"Quality indicator"},{color:"info",size:"md",label:"Featured property",useCase:"Status flag"}]},{title:"Prominent Actions",description:"Large checkboxes for key selections",combinations:[{color:"warning",size:"lg",label:"Priority viewing",useCase:"Important option"},{color:"danger",size:"xl",label:"Delete property",useCase:"Destructive action"}]}],e=r=>({xs:"1.75rem",sm:"2rem",md:"2.5rem",lg:"2.75rem",xl:"3rem",xxl:"3.5rem"})[r]||"2.5rem";return`
      <div style="display: flex; flex-direction: column; gap: 2.5rem;">
        ${n.map(r=>`
          <div>
            <h4 style="margin-bottom: 0.5rem; font-size: 0.9375rem; font-weight: 600; color: var(--gray-800);">${r.title}</h4>
            <p style="font-size: 0.75rem; color: var(--gray-500); margin-bottom: 1.25rem;">${r.description}</p>
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
              ${r.combinations.map(t=>`
                <div style="padding: 1.5rem; background-color: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                  <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0.5rem;">
                    ${a({name:`combo-${t.color}-${t.size}`,value:"checked",label:t.label,color:t.color,size:t.size,checked:!0})}
                  </div>
                  <div style="margin-left: ${e(t.size)}; font-size: 0.75rem; color: var(--gray-500);">
                    <strong>Color:</strong> ${t.color} &nbsp;|&nbsp; <strong>Size:</strong> ${t.size} &nbsp;|&nbsp; <strong>Use case:</strong> ${t.useCase}
                  </div>
                </div>
              `).join("")}
            </div>
          </div>
        `).join("")}
      </div>
    `},parameters:{docs:{description:{story:"Practical examples combining color and size variants in realistic Real Estate scenarios: compact filters for search interfaces, standard forms for data entry, and prominent actions for important decisions. Demonstrates how different combinations serve specific UX purposes."}}}};var h,f,x,k,w;i.parameters={...i.parameters,docs:{...(h=i.parameters)==null?void 0:h.docs,source:{originalSource:`{
  args: {
    name: 'property-type',
    value: 'apartment',
    label: 'Apartment',
    checked: false,
    disabled: false
  }
}`,...(x=(f=i.parameters)==null?void 0:f.docs)==null?void 0:x.source},description:{story:"Default: Standard checkbox with label",...(w=(k=i.parameters)==null?void 0:k.docs)==null?void 0:w.description}}};var z,$,C,S,D;s.parameters={...s.parameters,docs:{...(z=s.parameters)==null?void 0:z.docs,source:{originalSource:`{
  args: {
    name: 'property-features',
    value: 'parking',
    label: 'Parking available',
    checked: true,
    disabled: false
  }
}`,...(C=($=s.parameters)==null?void 0:$.docs)==null?void 0:C.source},description:{story:"Checked: Pre-selected checkbox",...(D=(S=s.parameters)==null?void 0:S.docs)==null?void 0:D.description}}};var A,L,T,I,P;l.parameters={...l.parameters,docs:{...(A=l.parameters)==null?void 0:A.docs,source:{originalSource:`{
  args: {
    name: 'property-features',
    value: 'garden',
    label: 'Garden (not available)',
    checked: false,
    disabled: true
  }
}`,...(T=(L=l.parameters)==null?void 0:L.docs)==null?void 0:T.source},description:{story:"Disabled: Non-interactive state",...(P=(I=l.parameters)==null?void 0:I.docs)==null?void 0:P.description}}};var E,F,j,G,U;o.parameters={...o.parameters,docs:{...(E=o.parameters)==null?void 0:E.docs,source:{originalSource:`{
  args: {
    name: 'property-features',
    value: 'balcony',
    label: 'Balcony (included)',
    checked: true,
    disabled: true
  }
}`,...(j=(F=o.parameters)==null?void 0:F.docs)==null?void 0:j.source},description:{story:"Disabled Checked: Pre-selected and non-interactive",...(U=(G=o.parameters)==null?void 0:G.docs)==null?void 0:U.description}}};var V,q,M,N,R;c.parameters={...c.parameters,docs:{...(V=c.parameters)==null?void 0:V.docs,source:{originalSource:`{
  args: {
    name: 'agreement',
    value: 'accepted',
    label: null,
    checked: false,
    disabled: false
  }
}`,...(M=(q=c.parameters)==null?void 0:q.docs)==null?void 0:M.source},description:{story:"No Label: Checkbox without label text",...(R=(N=c.parameters)==null?void 0:N.docs)==null?void 0:R.description}}};var H,B,J,X,O;d.parameters={...d.parameters,docs:{...(H=d.parameters)==null?void 0:H.docs,source:{originalSource:`{
  args: {
    name: 'terms',
    value: 'accepted',
    label: 'I agree to the terms and conditions of the real estate transaction, including the payment schedule and property inspection requirements.',
    checked: false,
    disabled: false
  }
}`,...(J=(B=d.parameters)==null?void 0:B.docs)==null?void 0:J.source},description:{story:"Long Label: Checkbox with multiline text",...(O=(X=d.parameters)==null?void 0:X.docs)==null?void 0:O.description}}};var W,_,Q,K,Y;m.parameters={...m.parameters,docs:{...(W=m.parameters)==null?void 0:W.docs,source:{originalSource:`{
  render: () => {
    const properties = [{
      name: 'property-type',
      value: 'apartment',
      label: 'Apartment',
      checked: true
    }, {
      name: 'property-type',
      value: 'house',
      label: 'House',
      checked: false
    }, {
      name: 'property-type',
      value: 'commercial',
      label: 'Commercial Space',
      checked: false
    }, {
      name: 'property-type',
      value: 'land',
      label: 'Land',
      checked: false
    }];
    const features = [{
      name: 'features',
      value: 'parking',
      label: 'Parking',
      checked: true
    }, {
      name: 'features',
      value: 'elevator',
      label: 'Elevator',
      checked: true
    }, {
      name: 'features',
      value: 'balcony',
      label: 'Balcony',
      checked: false
    }, {
      name: 'features',
      value: 'garden',
      label: 'Garden',
      checked: false
    }, {
      name: 'features',
      value: 'pool',
      label: 'Swimming Pool',
      checked: false
    }];
    return \`
      <div style="max-width: 600px;">
        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Property Type</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
          \${properties.map(prop => checkboxTwig(prop)).join('')}
        </div>

        <h3 style="margin-bottom: 1rem; font-size: 1.125rem; font-weight: 600;">Required Features</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
          \${features.map(feat => checkboxTwig(feat)).join('')}
        </div>
      </div>
    \`;
  }
}`,...(Q=(_=m.parameters)==null?void 0:_.docs)==null?void 0:Q.source},description:{story:"Real Estate Form: Multiple checkboxes for property search",...(Y=(K=m.parameters)==null?void 0:K.docs)==null?void 0:Y.description}}};var Z,ee,ae,re,te;p.parameters={...p.parameters,docs:{...(Z=p.parameters)==null?void 0:Z.docs,source:{originalSource:`{
  render: () => {
    const amenities = [{
      name: 'amenities',
      value: 'gym',
      label: 'Fitness Center'
    }, {
      name: 'amenities',
      value: 'concierge',
      label: '24/7 Concierge'
    }, {
      name: 'amenities',
      value: 'security',
      label: 'Security System'
    }, {
      name: 'amenities',
      value: 'storage',
      label: 'Storage Space'
    }, {
      name: 'amenities',
      value: 'bike',
      label: 'Bike Storage'
    }, {
      name: 'amenities',
      value: 'terrace',
      label: 'Rooftop Terrace'
    }];
    return \`
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; max-width: 600px;">
        \${amenities.map(item => checkboxTwig(item)).join('')}
      </div>
    \`;
  }
}`,...(ae=(ee=p.parameters)==null?void 0:ee.docs)==null?void 0:ae.source},description:{story:"Grid Layout: Checkboxes in two columns",...(te=(re=p.parameters)==null?void 0:re.docs)==null?void 0:te.description}}};var ne,ie,se,le,oe;u.parameters={...u.parameters,docs:{...(ne=u.parameters)==null?void 0:ne.docs,source:{originalSource:`{
  render: () => {
    const colors = ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'dark', 'light'];
    return \`
      <div style="display: flex; flex-direction: column; gap: 2rem;">
        \${colors.map(color => \`
          <div>
            <h4 style="margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">\${color}</h4>
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; padding: 1.5rem; background-color: \${color === 'light' ? 'var(--gray-800)' : 'var(--gray-50)'}; border-radius: 8px;">
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Unchecked</span>
                \${checkboxTwig({
      name: \`color-\${color}-unchecked\`,
      value: 'unchecked',
      label: 'Default state',
      color,
      checked: false
    })}
              </div>
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Checked</span>
                \${checkboxTwig({
      name: \`color-\${color}-checked\`,
      value: 'checked',
      label: 'Selected state',
      color,
      checked: true
    })}
              </div>
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Indeterminate</span>
                \${checkboxTwig({
      name: \`color-\${color}-indeterminate\`,
      value: 'indeterminate',
      label: 'Partial selection',
      color,
      indeterminate: true
    })}
              </div>
              <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <span style="font-size: 0.75rem; color: var(--gray-600); font-weight: 500; margin-bottom: 0.25rem;">Disabled</span>
                \${checkboxTwig({
      name: \`color-\${color}-disabled\`,
      value: 'disabled-unchecked',
      label: 'Disabled',
      color,
      disabled: true
    })}
                \${checkboxTwig({
      name: \`color-\${color}-disabled-checked\`,
      value: 'disabled-checked',
      label: 'Disabled checked',
      color,
      checked: true,
      disabled: true
    })}
              </div>
            </div>
          </div>
        \`).join('')}
      </div>
    \`;
  },
  parameters: {
    docs: {
      description: {
        story: 'Complete showcase of all 8 color variants with all states. Note: unchecked icons stay gray, only checked state displays the color variant. Light variant shown on dark background for visibility.'
      }
    }
  }
}`,...(se=(ie=u.parameters)==null?void 0:ie.docs)==null?void 0:se.source},description:{story:"All Colors: Complete color variant showcase",...(oe=(le=u.parameters)==null?void 0:le.docs)==null?void 0:oe.description}}};var ce,de,me,pe,ue;g.parameters={...g.parameters,docs:{...(ce=g.parameters)==null?void 0:ce.docs,source:{originalSource:`{
  render: () => {
    const sizes = [{
      value: 'xs',
      label: 'Extra Small',
      pixels: '12px'
    }, {
      value: 'sm',
      label: 'Small',
      pixels: '16px'
    }, {
      value: 'md',
      label: 'Medium (Default)',
      pixels: '24px'
    }, {
      value: 'lg',
      label: 'Large',
      pixels: '28px'
    }, {
      value: 'xl',
      label: 'Extra Large',
      pixels: '32px'
    }, {
      value: 'xxl',
      label: 'Extra Extra Large',
      pixels: '40px'
    }];
    return \`
      <div style="display: flex; flex-direction: column; gap: 2rem;">
        \${sizes.map(size => \`
          <div>
            <h4 style="margin-bottom: 0.75rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">
              \${size.value} — \${size.label} <span style="font-weight: 400; color: var(--gray-500);">(\${size.pixels})</span>
            </h4>
            <div style="display: flex; flex-wrap: wrap; gap: 2rem; padding: 1.5rem; background-color: var(--gray-50); border-radius: 8px; align-items: center;">
              \${checkboxTwig({
      name: \`size-\${size.value}-unchecked\`,
      value: 'unchecked',
      label: 'Unchecked',
      size: size.value,
      checked: false
    })}
              \${checkboxTwig({
      name: \`size-\${size.value}-checked\`,
      value: 'checked',
      label: 'Checked',
      size: size.value,
      checked: true
    })}
              \${checkboxTwig({
      name: \`size-\${size.value}-indeterminate\`,
      value: 'indeterminate',
      label: 'Indeterminate',
      size: size.value,
      indeterminate: true
    })}
              \${checkboxTwig({
      name: \`size-\${size.value}-disabled\`,
      value: 'disabled',
      label: 'Disabled',
      size: size.value,
      disabled: true
    })}
            </div>
          </div>
        \`).join('')}
      </div>
    \`;
  },
  parameters: {
    docs: {
      description: {
        story: 'Complete showcase of all 6 size variants with pixel dimensions. Font sizes scale proportionally with checkbox size for optimal readability.'
      }
    }
  }
}`,...(me=(de=g.parameters)==null?void 0:de.docs)==null?void 0:me.source},description:{story:"All Sizes: Complete size variant showcase",...(ue=(pe=g.parameters)==null?void 0:pe.docs)==null?void 0:ue.description}}};var ge,be,ye,ve,he;b.parameters={...b.parameters,docs:{...(ge=b.parameters)==null?void 0:ge.docs,source:{originalSource:`{
  render: () => {
    const stateGroups = [{
      title: 'Interactive States',
      description: 'Hover over these checkboxes to see animated transitions',
      states: [{
        label: 'Unchecked',
        checked: false
      }, {
        label: 'Checked',
        checked: true
      }, {
        label: 'Indeterminate',
        checked: false,
        indeterminate: true
      }]
    }, {
      title: 'Disabled States',
      description: 'Non-interactive states with reduced opacity',
      states: [{
        label: 'Disabled (unchecked)',
        checked: false,
        disabled: true
      }, {
        label: 'Disabled (checked)',
        checked: true,
        disabled: true
      }, {
        label: 'Disabled (indeterminate)',
        checked: false,
        disabled: true,
        indeterminate: true
      }]
    }];
    return \`
      <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 600px;">
        \${stateGroups.map(group => \`
          <div>
            <h4 style="margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: var(--gray-700); letter-spacing: 0.05em;">\${group.title}</h4>
            <p style="font-size: 0.75rem; color: var(--gray-500); margin-bottom: 1rem;">\${group.description}</p>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; padding: 1.5rem; background-color: var(--gray-50); border-radius: 8px;">
              \${group.states.map(state => \`
                \${checkboxTwig({
      name: \`state-\${group.title.toLowerCase().replace(/\\s+/g, '-')}\`,
      value: state.label.toLowerCase().replace(/\\s+/g, '-'),
      label: state.label,
      ...state
    })}
              \`).join('')}
            </div>
          </div>
        \`).join('')}
      </div>
    \`;
  },
  parameters: {
    docs: {
      description: {
        story: 'Complete demonstration of all checkbox states organized by category: interactive states (unchecked, checked, indeterminate) and disabled states (all variations). Hover over interactive checkboxes to see smooth animated transitions.'
      }
    }
  }
}`,...(ye=(be=b.parameters)==null?void 0:be.docs)==null?void 0:ye.source},description:{story:"All States: Interactive state combinations",...(he=(ve=b.parameters)==null?void 0:ve.docs)==null?void 0:he.description}}};var fe,xe,ke,we,ze;y.parameters={...y.parameters,docs:{...(fe=y.parameters)==null?void 0:fe.docs,source:{originalSource:`{
  render: () => {
    return \`
      <div style="max-width: 700px;">
        <div style="margin-bottom: 2rem; padding: 1rem; background-color: var(--color-info-light); border-left: 4px solid var(--color-info); border-radius: 4px;">
          <p style="font-size: 0.875rem; color: var(--gray-700); margin: 0;">
            <strong>Indeterminate state</strong> indicates a parent checkbox controlling multiple children where <strong>some (but not all) are selected</strong>. Common in hierarchical filters and multi-level selection interfaces.
          </p>
        </div>

        <div style="padding: 2rem; background-color: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
          <div style="margin-bottom: 2rem;">
            <h4 style="margin-bottom: 1.25rem; font-size: 1rem; font-weight: 600; color: var(--gray-800);">Property Amenities Filter</h4>
            \${checkboxTwig({
      name: 'amenities-all',
      value: 'all',
      label: 'All amenities (2 of 4 selected — indeterminate)',
      indeterminate: true
    })}
          </div>

          <div style="margin-left: 2rem; padding-left: 1.5rem; border-left: 2px solid var(--gray-300);">
            <h5 style="margin-bottom: 1rem; font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; color: var(--gray-600); letter-spacing: 0.05em;">Available amenities:</h5>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
              \${checkboxTwig({
      name: 'amenities',
      value: 'gym',
      label: 'Fitness Center & Gym',
      checked: true,
      color: 'success'
    })}
              \${checkboxTwig({
      name: 'amenities',
      value: 'pool',
      label: 'Swimming Pool & Jacuzzi',
      checked: true,
      color: 'success'
    })}
              \${checkboxTwig({
      name: 'amenities',
      value: 'spa',
      label: 'Spa & Wellness Center',
      checked: false
    })}
              \${checkboxTwig({
      name: 'amenities',
      value: 'concierge',
      label: '24/7 Concierge Service',
      checked: false
    })}
            </div>
          </div>
        </div>

        <div style="margin-top: 2rem; padding: 1rem; background-color: var(--gray-100); border-radius: 4px;">
          <p style="font-size: 0.8125rem; color: var(--gray-600); margin: 0;">
            <strong>UX Pattern:</strong> Clicking the parent checkbox should select/deselect all children. In a real implementation, JavaScript would handle the state synchronization between parent and child checkboxes.
          </p>
        </div>
      </div>
    \`;
  },
  parameters: {
    docs: {
      description: {
        story: 'Demonstrates the indeterminate state in a realistic hierarchical selection scenario. The parent checkbox displays an indeterminate icon when some (but not all) child options are selected. In real implementations, JavaScript manages state synchronization between parent and children.'
      }
    }
  }
}`,...(ke=(xe=y.parameters)==null?void 0:xe.docs)==null?void 0:ke.source},description:{story:"Indeterminate State: Partial selection indicator",...(ze=(we=y.parameters)==null?void 0:we.docs)==null?void 0:ze.description}}};var $e,Ce,Se,De,Ae;v.parameters={...v.parameters,docs:{...($e=v.parameters)==null?void 0:$e.docs,source:{originalSource:`{
  render: () => {
    const practicalExamples = [{
      title: 'Compact Filters',
      description: 'Small checkboxes for dense filter interfaces',
      combinations: [{
        color: 'primary',
        size: 'xs',
        label: 'Type: Office',
        useCase: 'Property type filter'
      }, {
        color: 'secondary',
        size: 'sm',
        label: 'Available now',
        useCase: 'Availability filter'
      }]
    }, {
      title: 'Standard Forms',
      description: 'Medium-sized checkboxes for typical forms',
      combinations: [{
        color: 'success',
        size: 'md',
        label: 'Verified listing',
        useCase: 'Quality indicator'
      }, {
        color: 'info',
        size: 'md',
        label: 'Featured property',
        useCase: 'Status flag'
      }]
    }, {
      title: 'Prominent Actions',
      description: 'Large checkboxes for key selections',
      combinations: [{
        color: 'warning',
        size: 'lg',
        label: 'Priority viewing',
        useCase: 'Important option'
      }, {
        color: 'danger',
        size: 'xl',
        label: 'Delete property',
        useCase: 'Destructive action'
      }]
    }];

    // Helper function to calculate left margin based on size
    const getMarginLeft = size => {
      const margins = {
        xs: '1.75rem',
        sm: '2rem',
        md: '2.5rem',
        lg: '2.75rem',
        xl: '3rem',
        xxl: '3.5rem'
      };
      return margins[size] || '2.5rem';
    };
    return \`
      <div style="display: flex; flex-direction: column; gap: 2.5rem;">
        \${practicalExamples.map(example => \`
          <div>
            <h4 style="margin-bottom: 0.5rem; font-size: 0.9375rem; font-weight: 600; color: var(--gray-800);">\${example.title}</h4>
            <p style="font-size: 0.75rem; color: var(--gray-500); margin-bottom: 1.25rem;">\${example.description}</p>
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
              \${example.combinations.map(combo => \`
                <div style="padding: 1.5rem; background-color: var(--gray-50); border-radius: 8px; border: 1px solid var(--gray-200);">
                  <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0.5rem;">
                    \${checkboxTwig({
      name: \`combo-\${combo.color}-\${combo.size}\`,
      value: 'checked',
      label: combo.label,
      color: combo.color,
      size: combo.size,
      checked: true
    })}
                  </div>
                  <div style="margin-left: \${getMarginLeft(combo.size)}; font-size: 0.75rem; color: var(--gray-500);">
                    <strong>Color:</strong> \${combo.color} &nbsp;|&nbsp; <strong>Size:</strong> \${combo.size} &nbsp;|&nbsp; <strong>Use case:</strong> \${combo.useCase}
                  </div>
                </div>
              \`).join('')}
            </div>
          </div>
        \`).join('')}
      </div>
    \`;
  },
  parameters: {
    docs: {
      description: {
        story: 'Practical examples combining color and size variants in realistic Real Estate scenarios: compact filters for search interfaces, standard forms for data entry, and prominent actions for important decisions. Demonstrates how different combinations serve specific UX purposes.'
      }
    }
  }
}`,...(Se=(Ce=v.parameters)==null?void 0:Ce.docs)==null?void 0:Se.source},description:{story:"Color + Size Combined: Mixed variations",...(Ae=(De=v.parameters)==null?void 0:De.docs)==null?void 0:Ae.description}}};const Fe=["Default","Checked","Disabled","DisabledChecked","NoLabel","LongLabel","RealEstateForm","GridLayout","AllColors","AllSizes","AllStates","IndeterminateState","ColorAndSizeCombined"];export{u as AllColors,g as AllSizes,b as AllStates,s as Checked,v as ColorAndSizeCombined,i as Default,l as Disabled,o as DisabledChecked,p as GridLayout,y as IndeterminateState,d as LongLabel,c as NoLabel,m as RealEstateForm,Fe as __namedExportsOrder,Ee as default};
