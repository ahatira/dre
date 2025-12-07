import{t as S,T as k}from"./iframe-GGfdoSBx.js";import{D as A,a as x}from"./twig-Dqrk-56N.js";x(k);k.cache(!1);const c=e=>e,n=(e={})=>{const p=S.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/toast/toast.twig",data:[{type:"raw",value:`
`,position:{start:32,end:33}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"show",match:["show"]}],position:{start:33,end:46},output:[{type:"raw",value:'  <div class="ps-toast ps-toast--',position:{start:47,end:80}},{type:"output",position:{start:80,end:90},stack:[{type:"Twig.expression.type.variable",value:"type",match:["type"],position:{start:80,end:90}}]},{type:"raw",value:`" role="status" aria-live="polite" aria-atomic="true">
    <div class="ps-toast__content">
      `,position:{start:90,end:187}},{type:"output",position:{start:187,end:200},stack:[{type:"Twig.expression.type.variable",value:"message",match:["message"],position:{start:187,end:200}}]},{type:"raw",value:`
    </div>
    `,position:{start:200,end:216}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"dismissible",match:["dismissible"]}],position:{start:216,end:236},output:[{type:"raw",value:`      <button type="button" class="ps-toast__close" aria-label="Dismiss notification">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" fill="currentColor"/>
        </svg>
      </button>
    `,position:{start:237,end:564}}]},position:{open:{start:216,end:236},close:{start:564,end:575}}},{type:"raw",value:`  </div>
`,position:{start:576,end:585}}]},position:{open:{start:33,end:46},close:{start:585,end:596}}}],precompiled:!0});p.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),c(p.render({attributes:new A(t),...e}))}catch(t){return c("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/toast/toast.twig: "+t.toString())}},i={message:"Operation completed successfully!",type:"success",duration:4e3,dismissible:!0,show:!0},C={title:"Components/Toast",tags:["autodocs"],argTypes:{message:{control:"text",description:"Notification message",table:{category:"Content"}},type:{control:{type:"select"},options:["success","error","warning","info"],description:"Toast type/variant",table:{category:"State"}},dismissible:{control:"boolean",description:"Show close button",table:{category:"Configuration"}},show:{control:"boolean",description:"Display toast",table:{category:"State"}}}},s={name:"Success",render:e=>n(e),args:{...i,type:"success",message:"Property listed successfully!"}},a={name:"Error",render:e=>n(e),args:{...i,type:"error",message:"An error occurred. Please try again."}},r={name:"Warning",render:e=>n(e),args:{...i,type:"warning",message:"This action cannot be undone."}},o={name:"Info",render:e=>n(e),args:{...i,type:"info",message:"New properties available in your area."}};var u,l,d;s.parameters={...s.parameters,docs:{...(u=s.parameters)==null?void 0:u.docs,source:{originalSource:`{
  name: 'Success',
  render: args => markup(args),
  args: {
    ...data,
    type: 'success',
    message: 'Property listed successfully!'
  }
}`,...(d=(l=s.parameters)==null?void 0:l.docs)==null?void 0:d.source}}};var g,m,y;a.parameters={...a.parameters,docs:{...(g=a.parameters)==null?void 0:g.docs,source:{originalSource:`{
  name: 'Error',
  render: args => markup(args),
  args: {
    ...data,
    type: 'error',
    message: 'An error occurred. Please try again.'
  }
}`,...(y=(m=a.parameters)==null?void 0:m.docs)==null?void 0:y.source}}};var w,b,f;r.parameters={...r.parameters,docs:{...(w=r.parameters)==null?void 0:w.docs,source:{originalSource:`{
  name: 'Warning',
  render: args => markup(args),
  args: {
    ...data,
    type: 'warning',
    message: 'This action cannot be undone.'
  }
}`,...(f=(b=r.parameters)==null?void 0:b.docs)==null?void 0:f.source}}};var v,h,T;o.parameters={...o.parameters,docs:{...(v=o.parameters)==null?void 0:v.docs,source:{originalSource:`{
  name: 'Info',
  render: args => markup(args),
  args: {
    ...data,
    type: 'info',
    message: 'New properties available in your area.'
  }
}`,...(T=(h=o.parameters)==null?void 0:h.docs)==null?void 0:T.source}}};const I=["Success","ErrorToast","Warning","Info"];export{a as ErrorToast,o as Info,s as Success,r as Warning,I as __namedExportsOrder,C as default};
