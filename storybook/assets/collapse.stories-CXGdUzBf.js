import{c as e}from"./collapse-BcX1zpbp.js";import"./iframe-DeCmpQ6I.js";import"./twig-Cbw8xbjJ.js";const v={id:"collapse-1",title:"Property Details",content:"This modern apartment features hardwood floors, stainless steel appliances, and floor-to-ceiling windows with stunning city views. Located in a prime downtown location with easy access to public transportation.",expanded:!1},T={title:"Elements/Collapse",tags:["autodocs"],parameters:{docs:{description:{component:"Collapsible disclosure element with trigger and expandable panel. Single-item behavior for show/hide content."}}},argTypes:{id:{name:"ID",description:"Unique identifier for panel/trigger linkage (required for ARIA)",control:"text",table:{category:"Content"}},title:{name:"Title",description:"Trigger button text",control:"text",table:{category:"Content"}},content:{name:"Content (raw)",description:"Raw HTML content for panel",control:"text",table:{category:"Content"}},variant:{name:"Variant",description:"Visual style variant",control:"select",options:["primary","secondary","success","warning","danger","info","dark","light"],table:{category:"Appearance"}},expanded:{name:"Expanded",description:"Initial expanded state",control:"boolean",table:{category:"Behavior"}},trigger_tag:{name:"Trigger Tag",description:"HTML tag for trigger element (default: button)",control:"select",options:["button","h3","h4"],table:{category:"Accessibility"}}}},a={args:v,render:n=>e(n)},t={args:{...v,expanded:!0},render:n=>e(n)},i={args:{id:"collapse-basic",title:"Real Estate Listing Features",content:"This luxury property offers 3 bedrooms, 2 bathrooms, modern kitchen with granite countertops, hardwood flooring throughout, and a private balcony overlooking the city skyline."},render:n=>e(n)},r={name:"Variants",render:()=>`
    <div style="display: flex; flex-direction: column; gap: 0;">
      ${e({id:"variant-default",title:"[Default] Property Financing Options",content:"We offer flexible financing solutions including conventional mortgages, FHA loans, and investment property financing. Our team will guide you through the pre-approval process.",variant:"default",expanded:!1})}
      ${e({id:"variant-primary",title:"[Primary] Featured Property Details",content:"This premium commercial space features modern amenities, high-speed fiber internet, 24/7 security, and panoramic city views. Perfect for executive offices or tech startups.",variant:"primary",expanded:!0})}
      ${e({id:"variant-secondary",title:"[Secondary] Additional Property Information",content:"Built in 2022, this LEED-certified building offers energy-efficient systems, ample parking, and convenient access to public transportation and major highways.",variant:"secondary",expanded:!1})}
      ${e({id:"variant-success",title:"[Success] Property Available Now",content:"Immediate occupancy available! This move-in ready office space has been freshly renovated and is ready for your business. Schedule a viewing today.",variant:"success",expanded:!1})}
      ${e({id:"variant-warning",title:"[Warning] Limited Availability",content:"Only 3 units remaining in this high-demand building. Act fast to secure your preferred space before they are gone. Contact our leasing team immediately.",variant:"warning",expanded:!1})}
      ${e({id:"variant-danger",title:"[Danger] Property Inspection Required",content:"This property requires mandatory structural inspection before closing. Foundation issues have been identified and must be addressed by qualified contractors.",variant:"danger",expanded:!1})}
      ${e({id:"variant-info",title:"[Info] Market Insights & Trends",content:"The commercial real estate market in this district has seen 12% year-over-year growth. Average lease rates are €45/sqm, with strong demand for Class A office space.",variant:"info",expanded:!1})}
      ${e({id:"variant-dark",title:"[Dark] Exclusive Listing",content:"Private sale opportunity for discerning investors. This off-market property offers exceptional ROI potential with established tenants and premium location.",variant:"dark",expanded:!1})}
      ${e({id:"variant-light",title:"[Light] General Property FAQ",content:"Have questions about our properties? Our FAQ covers common inquiries about lease terms, maintenance responsibilities, parking policies, and move-in procedures.",variant:"light",expanded:!1})}
    </div>
  `};var o,s,l;a.parameters={...a.parameters,docs:{...(o=a.parameters)==null?void 0:o.docs,source:{originalSource:`{
  args: collapseData,
  render: args => collapseTwig(args)
}`,...(l=(s=a.parameters)==null?void 0:s.docs)==null?void 0:l.source}}};var c,d,p;t.parameters={...t.parameters,docs:{...(c=t.parameters)==null?void 0:c.docs,source:{originalSource:`{
  args: {
    ...collapseData,
    expanded: true
  },
  render: args => collapseTwig(args)
}`,...(p=(d=t.parameters)==null?void 0:d.docs)==null?void 0:p.source}}};var g,u,m;i.parameters={...i.parameters,docs:{...(g=i.parameters)==null?void 0:g.docs,source:{originalSource:`{
  args: {
    id: 'collapse-basic',
    title: 'Real Estate Listing Features',
    content: 'This luxury property offers 3 bedrooms, 2 bathrooms, modern kitchen with granite countertops, hardwood flooring throughout, and a private balcony overlooking the city skyline.'
  },
  render: args => collapseTwig(args)
}`,...(m=(u=i.parameters)==null?void 0:u.docs)==null?void 0:m.source}}};var f,y,h;r.parameters={...r.parameters,docs:{...(f=r.parameters)==null?void 0:f.docs,source:{originalSource:`{
  name: 'Variants',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: 0;">
      \${collapseTwig({
    id: 'variant-default',
    title: '[Default] Property Financing Options',
    content: 'We offer flexible financing solutions including conventional mortgages, FHA loans, and investment property financing. Our team will guide you through the pre-approval process.',
    variant: 'default',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-primary',
    title: '[Primary] Featured Property Details',
    content: 'This premium commercial space features modern amenities, high-speed fiber internet, 24/7 security, and panoramic city views. Perfect for executive offices or tech startups.',
    variant: 'primary',
    expanded: true
  })}
      \${collapseTwig({
    id: 'variant-secondary',
    title: '[Secondary] Additional Property Information',
    content: 'Built in 2022, this LEED-certified building offers energy-efficient systems, ample parking, and convenient access to public transportation and major highways.',
    variant: 'secondary',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-success',
    title: '[Success] Property Available Now',
    content: 'Immediate occupancy available! This move-in ready office space has been freshly renovated and is ready for your business. Schedule a viewing today.',
    variant: 'success',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-warning',
    title: '[Warning] Limited Availability',
    content: 'Only 3 units remaining in this high-demand building. Act fast to secure your preferred space before they are gone. Contact our leasing team immediately.',
    variant: 'warning',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-danger',
    title: '[Danger] Property Inspection Required',
    content: 'This property requires mandatory structural inspection before closing. Foundation issues have been identified and must be addressed by qualified contractors.',
    variant: 'danger',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-info',
    title: '[Info] Market Insights & Trends',
    content: 'The commercial real estate market in this district has seen 12% year-over-year growth. Average lease rates are €45/sqm, with strong demand for Class A office space.',
    variant: 'info',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-dark',
    title: '[Dark] Exclusive Listing',
    content: 'Private sale opportunity for discerning investors. This off-market property offers exceptional ROI potential with established tenants and premium location.',
    variant: 'dark',
    expanded: false
  })}
      \${collapseTwig({
    id: 'variant-light',
    title: '[Light] General Property FAQ',
    content: 'Have questions about our properties? Our FAQ covers common inquiries about lease terms, maintenance responsibilities, parking policies, and move-in procedures.',
    variant: 'light',
    expanded: false
  })}
    </div>
  \`
}`,...(h=(y=r.parameters)==null?void 0:y.docs)==null?void 0:h.source}}};const k=["Default","Expanded","BasicContent","Variants"];export{i as BasicContent,a as Default,t as Expanded,r as Variants,k as __namedExportsOrder,T as default};
