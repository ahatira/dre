import{r as b,d as r}from"./radio-xdkkunKp.js";import"./iframe-GGfdoSBx.js";import"./twig-Dqrk-56N.js";const y={title:"Elements/Radio",tags:["autodocs"],parameters:{docs:{description:{component:"Individual radio with label. Use within Radios group or standalone."}}},argTypes:{name:{description:"Input name attribute (group identifier)",control:"text",table:{category:"Content"}},value:{description:"Input value attribute",control:"text",table:{category:"Content"}},id:{description:"Input ID for label association",control:"text",table:{category:"Content"}},label:{description:"Label text",control:"text",table:{category:"Content"}},checked:{description:"Whether radio is checked",control:"boolean",table:{category:"Behavior"}},disabled:{description:"Whether radio is disabled",control:"boolean",table:{category:"Behavior"}}},render:m=>b(m)},e={args:r},t={args:{...r,checked:!0}},a={args:{...r,disabled:!0}};var o,s,n;e.parameters={...e.parameters,docs:{...(o=e.parameters)==null?void 0:o.docs,source:{originalSource:`{
  args: data
}`,...(n=(s=e.parameters)==null?void 0:s.docs)==null?void 0:n.source}}};var c,i,d;t.parameters={...t.parameters,docs:{...(c=t.parameters)==null?void 0:c.docs,source:{originalSource:`{
  args: {
    ...data,
    checked: true
  }
}`,...(d=(i=t.parameters)==null?void 0:i.docs)==null?void 0:d.source}}};var l,p,u;a.parameters={...a.parameters,docs:{...(l=a.parameters)==null?void 0:l.docs,source:{originalSource:`{
  args: {
    ...data,
    disabled: true
  }
}`,...(u=(p=a.parameters)==null?void 0:p.docs)==null?void 0:u.source}}};const f=["Default","Checked","Disabled"];export{t as Checked,e as Default,a as Disabled,f as __namedExportsOrder,y as default};
