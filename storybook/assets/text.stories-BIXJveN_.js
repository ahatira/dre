import{c as e}from"./text-CNMd8xiQ.js";import"./iframe-GGfdoSBx.js";import"./twig-Dqrk-56N.js";const C={text:"This is body text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.",size:"md",color:"default",tag:"p",align:"left",muted:!1,strong:!1},B={title:"Elements/Text",render:d=>e(d),args:C,tags:["autodocs"],parameters:{docs:{description:{component:`Semantic text component for paragraphs and inline content.
Supports size variants, emphasis (muted/strong), alignment, and semantic tags.`}}},argTypes:{baseClass:{control:{type:"text"},description:"Override root BEM class for composition. When provided, Text emits only this class and `--strong` if applicable. Fallback emits `ps-text` classes.",table:{category:"Structure",type:{summary:"string"},defaultValue:{summary:null}}},text:{control:{type:"text"},description:"Text content rendered directly (no HTML parsing).",table:{category:"Content",type:{summary:"string",required:!0},defaultValue:{summary:"Body text example"}}},size:{control:{type:"select"},options:["xs","sm","md","lg","xl","xxl"],description:"Text size: xs (12px), sm (14px), md (16px), lg (18px), xl (20px), xxl (24px).",table:{category:"Appearance",type:{summary:"xs | sm | md | lg | xl | xxl"},defaultValue:{summary:"md"}}},color:{control:{type:"select"},options:["default","primary","secondary","success","info","warning","danger","dark","light"],description:"Semantic color: default (text), primary, secondary, success, info, warning, danger, dark, light.",table:{category:"Appearance",type:{summary:"default | primary | secondary | success | info | warning | danger | dark | light"},defaultValue:{summary:"default"}}},tag:{control:{type:"select"},options:["p","span","div"],description:"Semantic HTML tag used for rendering.",table:{category:"Structure",type:{summary:"p | span | div"},defaultValue:{summary:"p"}}},align:{control:{type:"inline-radio"},options:["left","center","right"],description:"Horizontal text alignment.",table:{category:"Layout",type:{summary:"left | center | right"},defaultValue:{summary:"left"}}},muted:{control:{type:"boolean"},description:"Apply secondary tone (muted) for de‑emphasized information.",table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}},strong:{control:{type:"boolean"},description:"Apply bold weight for emphasis (can combine with muted).",table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:!1}}}}},t={render:d=>e(d),args:{...C}},r={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${e({text:"XXL text (24px) — Hero intro and featured statements",size:"xxl",strong:!0})}
      ${e({text:"XL text (20px) — Lead paragraphs and introductions",size:"xl"})}
      ${e({text:"LG text (18px) — Lead paragraphs",size:"lg"})}
      ${e({text:"MD text (16px) — Standard body content (default)",size:"md"})}
      ${e({text:"SM text (14px) — Captions, helper text",size:"sm"})}
      ${e({text:"XS text (12px) — Footnotes, microcopy",size:"xs",muted:!0})}
    </div>
  `,parameters:{docs:{description:{story:"6 sizes: XXL (24px), XL (20px), LG (18px), MD (16px default), SM (14px), XS (12px). Use larger sizes for introductions and smaller sizes for captions/microcopy."}}}},a={render:()=>`
    <div style="display: grid; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${e({text:"Default color — Standard body text",size:"md",color:"default"})}
      ${e({text:"Primary color — Highlighted callouts",size:"md",color:"primary"})}
      ${e({text:"Secondary color — Complementary information",size:"md",color:"secondary"})}
      ${e({text:"Success color — Positive status",size:"md",color:"success"})}
      ${e({text:"Info color — Informational notes",size:"md",color:"info"})}
      ${e({text:"Warning color — Caution messages",size:"md",color:"warning"})}
      ${e({text:"Danger color — Error messages",size:"md",color:"danger"})}
      ${e({text:"Dark color — High contrast on light backgrounds",size:"md",color:"dark"})}
      <div style="padding: var(--size-4); background: var(--gray-800); border-radius: var(--radius-2);">
        ${e({text:"Light color — Inverted contexts on dark surfaces",size:"md",color:"light"})}
      </div>
    </div>
  `,parameters:{docs:{description:{story:"9 semantic colors: default, primary, secondary, success, info, warning, danger, dark, light. The `light` variant is showcased on a dark tile for proper contrast."}}}},o={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Normal</p>
        ${e({text:"Standard text with default color and weight",size:"md"})}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted</p>
        ${e({text:"Muted text for secondary information (reduced prominence)",size:"md",muted:!0})}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Strong</p>
        ${e({text:"Strong text for emphasis and highlighted importance (bold weight)",size:"md",strong:!0})}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted + Strong</p>
        ${e({text:"Combination of muted and strong is possible",size:"md",muted:!0,strong:!0})}
      </div>
    </div>
  `,parameters:{docs:{description:{story:"States: Normal (default), Muted (secondary tone), Strong (bold emphasis). States can be combined for nuanced emphasis."}}}},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${e({text:"Left aligned text (default) — Optimal for continuous reading",size:"md",align:"left"})}
      ${e({text:"Center aligned text — Use for callouts and headings",size:"md",align:"center"})}
      ${e({text:"Right aligned text — Use for numeric values or metadata blocks",size:"md",align:"right"})}
    </div>
  `,parameters:{docs:{description:{story:"3 alignments: Left (default, best readability), Center (headings/callouts), Right (numeric / metadata alignment)."}}}},n={render:()=>`
    <div style="max-width: 650px; padding: 2rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
      ${e({text:"Découvrez notre sélection exclusive de biens immobiliers d'exception à Paris et en Île-de-France.",size:"lg",strong:!0})}
      
      ${e({text:"BNP Paribas Real Estate vous accompagne dans tous vos projets immobiliers professionnels. Fort de notre expertise et de notre réseau international, nous vous proposons des solutions adaptées à vos besoins.",size:"md"})}
      
      ${e({text:"Notre équipe d'experts analyse le marché en temps réel pour vous offrir les meilleures opportunités d'investissement.",size:"md"})}
      
      <div style="margin-top: var(--size-6); padding-top: var(--size-4); border-top: 1px solid var(--gray-200);">
        ${e({text:"* Informations non contractuelles. Prix indicatifs sous réserve de disponibilité.",size:"sm",muted:!0})}
      </div>
    </div>
  `,parameters:{docs:{description:{story:"Real usage example: lead paragraph (large + strong) for introduction, body paragraphs for primary content, small muted for disclaimers / footnotes."}}}},i={render:()=>`
    <div style="display: grid; gap: var(--size-6); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Large Variants (18–24px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${e({text:"LG text — Normal",size:"lg"})}
          ${e({text:"XL text — Muted",size:"xl",muted:!0})}
          ${e({text:"XXL text — Strong",size:"xxl",strong:!0})}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Body Variants (16px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${e({text:"Body text — Normal",size:"md"})}
          ${e({text:"Body text — Muted",size:"md",muted:!0})}
          ${e({text:"Body text — Strong",size:"md",strong:!0})}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Small Variants (12–14px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${e({text:"Small text — Normal",size:"sm"})}
          ${e({text:"Small text — Muted",size:"sm",muted:!0})}
          ${e({text:"Small text — Strong",size:"sm",strong:!0})}
        </div>
      </div>
    </div>
  `,parameters:{docs:{description:{story:"All possible combinations of size (large/body/small) and state (normal/muted/strong). Any size can pair with any emphasis state."}}}};var l,p,c;t.parameters={...t.parameters,docs:{...(l=t.parameters)==null?void 0:l.docs,source:{originalSource:`{
  render: args => component(args),
  args: {
    ...data
  }
}`,...(c=(p=t.parameters)==null?void 0:p.docs)==null?void 0:c.source}}};var m,u,g;r.parameters={...r.parameters,docs:{...(m=r.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      \${component({
    text: 'XXL text (24px) — Hero intro and featured statements',
    size: 'xxl',
    strong: true
  })}
      \${component({
    text: 'XL text (20px) — Lead paragraphs and introductions',
    size: 'xl'
  })}
      \${component({
    text: 'LG text (18px) — Lead paragraphs',
    size: 'lg'
  })}
      \${component({
    text: 'MD text (16px) — Standard body content (default)',
    size: 'md'
  })}
      \${component({
    text: 'SM text (14px) — Captions, helper text',
    size: 'sm'
  })}
      \${component({
    text: 'XS text (12px) — Footnotes, microcopy',
    size: 'xs',
    muted: true
  })}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: '6 sizes: XXL (24px), XL (20px), LG (18px), MD (16px default), SM (14px), XS (12px). Use larger sizes for introductions and smaller sizes for captions/microcopy.'
      }
    }
  }
}`,...(g=(u=r.parameters)==null?void 0:u.docs)==null?void 0:g.source}}};var x,y,v;a.parameters={...a.parameters,docs:{...(x=a.parameters)==null?void 0:x.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      \${component({
    text: 'Default color — Standard body text',
    size: 'md',
    color: 'default'
  })}
      \${component({
    text: 'Primary color — Highlighted callouts',
    size: 'md',
    color: 'primary'
  })}
      \${component({
    text: 'Secondary color — Complementary information',
    size: 'md',
    color: 'secondary'
  })}
      \${component({
    text: 'Success color — Positive status',
    size: 'md',
    color: 'success'
  })}
      \${component({
    text: 'Info color — Informational notes',
    size: 'md',
    color: 'info'
  })}
      \${component({
    text: 'Warning color — Caution messages',
    size: 'md',
    color: 'warning'
  })}
      \${component({
    text: 'Danger color — Error messages',
    size: 'md',
    color: 'danger'
  })}
      \${component({
    text: 'Dark color — High contrast on light backgrounds',
    size: 'md',
    color: 'dark'
  })}
      <div style="padding: var(--size-4); background: var(--gray-800); border-radius: var(--radius-2);">
        \${component({
    text: 'Light color — Inverted contexts on dark surfaces',
    size: 'md',
    color: 'light'
  })}
      </div>
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: '9 semantic colors: default, primary, secondary, success, info, warning, danger, dark, light. The \`light\` variant is showcased on a dark tile for proper contrast.'
      }
    }
  }
}`,...(v=(y=a.parameters)==null?void 0:y.docs)==null?void 0:v.source}}};var f,z,h;o.parameters={...o.parameters,docs:{...(f=o.parameters)==null?void 0:f.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Normal</p>
        \${component({
    text: 'Standard text with default color and weight',
    size: 'md'
  })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted</p>
        \${component({
    text: 'Muted text for secondary information (reduced prominence)',
    size: 'md',
    muted: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Strong</p>
        \${component({
    text: 'Strong text for emphasis and highlighted importance (bold weight)',
    size: 'md',
    strong: true
  })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted + Strong</p>
        \${component({
    text: 'Combination of muted and strong is possible',
    size: 'md',
    muted: true,
    strong: true
  })}
      </div>
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'States: Normal (default), Muted (secondary tone), Strong (bold emphasis). States can be combined for nuanced emphasis.'
      }
    }
  }
}`,...(h=(z=o.parameters)==null?void 0:z.docs)==null?void 0:h.source}}};var b,$,S;s.parameters={...s.parameters,docs:{...(b=s.parameters)==null?void 0:b.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      \${component({
    text: 'Left aligned text (default) — Optimal for continuous reading',
    size: 'md',
    align: 'left'
  })}
      \${component({
    text: 'Center aligned text — Use for callouts and headings',
    size: 'md',
    align: 'center'
  })}
      \${component({
    text: 'Right aligned text — Use for numeric values or metadata blocks',
    size: 'md',
    align: 'right'
  })}
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: '3 alignments: Left (default, best readability), Center (headings/callouts), Right (numeric / metadata alignment).'
      }
    }
  }
}`,...(S=($=s.parameters)==null?void 0:$.docs)==null?void 0:S.source}}};var w,k,L;n.parameters={...n.parameters,docs:{...(w=n.parameters)==null?void 0:w.docs,source:{originalSource:`{
  render: () => \`
    <div style="max-width: 650px; padding: 2rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
      \${component({
    text: "Découvrez notre sélection exclusive de biens immobiliers d'exception à Paris et en Île-de-France.",
    size: 'lg',
    strong: true
  })}
      
      \${component({
    text: 'BNP Paribas Real Estate vous accompagne dans tous vos projets immobiliers professionnels. Fort de notre expertise et de notre réseau international, nous vous proposons des solutions adaptées à vos besoins.',
    size: 'md'
  })}
      
      \${component({
    text: "Notre équipe d'experts analyse le marché en temps réel pour vous offrir les meilleures opportunités d'investissement.",
    size: 'md'
  })}
      
      <div style="margin-top: var(--size-6); padding-top: var(--size-4); border-top: 1px solid var(--gray-200);">
        \${component({
    text: '* Informations non contractuelles. Prix indicatifs sous réserve de disponibilité.',
    size: 'sm',
    muted: true
  })}
      </div>
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'Real usage example: lead paragraph (large + strong) for introduction, body paragraphs for primary content, small muted for disclaimers / footnotes.'
      }
    }
  }
}`,...(L=(k=n.parameters)==null?void 0:k.docs)==null?void 0:L.source}}};var M,A,X;i.parameters={...i.parameters,docs:{...(M=i.parameters)==null?void 0:M.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; gap: var(--size-6); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Large Variants (18–24px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          \${component({
    text: 'LG text — Normal',
    size: 'lg'
  })}
          \${component({
    text: 'XL text — Muted',
    size: 'xl',
    muted: true
  })}
          \${component({
    text: 'XXL text — Strong',
    size: 'xxl',
    strong: true
  })}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Body Variants (16px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          \${component({
    text: 'Body text — Normal',
    size: 'md'
  })}
          \${component({
    text: 'Body text — Muted',
    size: 'md',
    muted: true
  })}
          \${component({
    text: 'Body text — Strong',
    size: 'md',
    strong: true
  })}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Small Variants (12–14px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          \${component({
    text: 'Small text — Normal',
    size: 'sm'
  })}
          \${component({
    text: 'Small text — Muted',
    size: 'sm',
    muted: true
  })}
          \${component({
    text: 'Small text — Strong',
    size: 'sm',
    strong: true
  })}
        </div>
      </div>
    </div>
  \`,
  parameters: {
    docs: {
      description: {
        story: 'All possible combinations of size (large/body/small) and state (normal/muted/strong). Any size can pair with any emphasis state.'
      }
    }
  }
}`,...(X=(A=i.parameters)==null?void 0:A.docs)==null?void 0:X.source}}};const P=["Default","AllSizes","AllColors","AllStates","AllAlignments","UseCases","AllCombinations"];export{s as AllAlignments,a as AllColors,i as AllCombinations,r as AllSizes,o as AllStates,t as Default,n as UseCases,P as __namedExportsOrder,B as default};
