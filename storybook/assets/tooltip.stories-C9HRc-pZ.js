import{t as h,T as b}from"./iframe-DeCmpQ6I.js";import{D as v,a as f}from"./twig-Cbw8xbjJ.js";f(b);b.cache(!1);const p=t=>t,i=(t={})=>{const a=h.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/tooltip/tooltip.twig",data:[{type:"raw",value:`
<div class="ps-tooltip">
  <button type="button" class="ps-tooltip__trigger" aria-describedby="tooltip-content">
    `,position:{start:37,end:155}},{type:"output",position:{start:155,end:173},stack:[{type:"Twig.expression.type.variable",value:"trigger_text",match:["trigger_text"],position:{start:155,end:173}}]},{type:"raw",value:`
    <svg class="ps-tooltip__icon" viewBox="0 0 24 24" aria-hidden="true">
      <circle cx="12" cy="12" r="1" fill="currentColor"/>
      <path d="M12 3a9 9 0 0 0-9 9 9 9 0 0 0 9 9 9 9 0 0 0 9-9 9 9 0 0 0-9-9zm0 16a7 7 0 0 1-7-7 7 7 0 0 1 7-7 7 7 0 0 1 7 7 7 7 0 0 1-7 7z" fill="currentColor"/>
    </svg>
  </button>
  <div class="ps-tooltip__content ps-tooltip__content--`,position:{start:173,end:547}},{type:"output",position:{start:547,end:561},stack:[{type:"Twig.expression.type.variable",value:"position",match:["position"],position:{start:547,end:561}}]},{type:"raw",value:`" id="tooltip-content" role="tooltip">
    `,position:{start:561,end:604}},{type:"output",position:{start:604,end:614},stack:[{type:"Twig.expression.type.variable",value:"text",match:["text"],position:{start:604,end:614}}]},{type:"raw",value:`
  </div>
</div>
`,position:{start:614,end:614}}],precompiled:!0});a.options.allowInlineIncludes=!0;try{let o=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(o)||(o=Object.entries(o)),p(a.render({attributes:new v(o),...t}))}catch(o){return p("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/tooltip/tooltip.twig: "+o.toString())}},s={text:"Additional information",position:"top",trigger_text:"Hover me"},C={title:"Components/Tooltip",tags:["autodocs"],argTypes:{text:{control:"text",description:"Tooltip content text",table:{category:"Content"}},position:{control:{type:"select"},options:["top","bottom","left","right"],description:"Tooltip position relative to trigger",table:{category:"Display"}},trigger_text:{control:"text",description:"Trigger button text",table:{category:"Content"}}}},e={name:"Top",render:t=>i(t),args:{...s,position:"top",text:"Contact the agent for more info"}},r={name:"Bottom",render:t=>i(t),args:{...s,position:"bottom",text:"Swipe to see more photos"}},n={name:"Right",render:t=>i(t),args:{...s,position:"right",text:"Click to expand details"}};var c,l,d;e.parameters={...e.parameters,docs:{...(c=e.parameters)==null?void 0:c.docs,source:{originalSource:`{
  name: 'Top',
  render: args => markup(args),
  args: {
    ...data,
    position: 'top',
    text: 'Contact the agent for more info'
  }
}`,...(d=(l=e.parameters)==null?void 0:l.docs)==null?void 0:d.source}}};var g,m,u;r.parameters={...r.parameters,docs:{...(g=r.parameters)==null?void 0:g.docs,source:{originalSource:`{
  name: 'Bottom',
  render: args => markup(args),
  args: {
    ...data,
    position: 'bottom',
    text: 'Swipe to see more photos'
  }
}`,...(u=(m=r.parameters)==null?void 0:m.docs)==null?void 0:u.source}}};var w,x,y;n.parameters={...n.parameters,docs:{...(w=n.parameters)==null?void 0:w.docs,source:{originalSource:`{
  name: 'Right',
  render: args => markup(args),
  args: {
    ...data,
    position: 'right',
    text: 'Click to expand details'
  }
}`,...(y=(x=n.parameters)==null?void 0:x.docs)==null?void 0:y.source}}};const k=["TopPosition","BottomPosition","RightPosition"];export{r as BottomPosition,n as RightPosition,e as TopPosition,k as __namedExportsOrder,C as default};
