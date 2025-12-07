import{c as u,d as t}from"./checkbox-CvSM45oN.js";import"./iframe-C-ciPShf.js";import"./twig-B9SdSbF4.js";const x={title:"Elements/Checkbox",tags:["autodocs"],args:t,render:i=>u(i),argTypes:{label:{control:"text",table:{category:"Content"}},checked:{control:"boolean",table:{category:"State"}},disabled:{control:"boolean",table:{category:"State"}}}},e={args:{...t}},a={args:{...t,checked:!0}},r={args:{...t,disabled:!0}};var o,s,c;e.parameters={...e.parameters,docs:{...(o=e.parameters)==null?void 0:o.docs,source:{originalSource:`{
  args: {
    ...checkboxData
  }
}`,...(c=(s=e.parameters)==null?void 0:s.docs)==null?void 0:c.source}}};var n,d,l;a.parameters={...a.parameters,docs:{...(n=a.parameters)==null?void 0:n.docs,source:{originalSource:`{
  args: {
    ...checkboxData,
    checked: true
  }
}`,...(l=(d=a.parameters)==null?void 0:d.docs)==null?void 0:l.source}}};var m,p,b;r.parameters={...r.parameters,docs:{...(m=r.parameters)==null?void 0:m.docs,source:{originalSource:`{
  args: {
    ...checkboxData,
    disabled: true
  }
}`,...(b=(p=r.parameters)==null?void 0:p.docs)==null?void 0:b.source}}};const D=["Default","Checked","Disabled"];export{a as Checked,e as Default,r as Disabled,D as __namedExportsOrder,x as default};
