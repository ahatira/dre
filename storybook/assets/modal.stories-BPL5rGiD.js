import{t as h,T as b}from"./iframe-C-ciPShf.js";import{D as _,a as f}from"./twig-B9SdSbF4.js";f(b);b.cache(!1);const p=t=>t,o=(t={})=>{const i=h.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/modal/modal.twig",data:[{type:"raw",value:`\r
<div class="ps-modal`,position:{start:38,end:60}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"show",match:["show"]}],position:{start:60,end:73},output:[{type:"raw",value:" ps-modal--visible",position:{start:73,end:91}}]},position:{open:{start:60,end:73},close:{start:91,end:102}}},{type:"raw",value:`" role="dialog" aria-modal="true" aria-labelledby="modal-title">\r
  `,position:{start:102,end:170}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"backdrop",match:["backdrop"]}],position:{start:170,end:187},output:[{type:"raw",value:'<div class="ps-modal__backdrop"></div>',position:{start:187,end:225}}]},position:{open:{start:170,end:187},close:{start:225,end:236}}},{type:"raw",value:`\r
  <div class="ps-modal__content ps-modal__content--`,position:{start:236,end:289}},{type:"output",position:{start:289,end:299},stack:[{type:"Twig.expression.type.variable",value:"size",match:["size"],position:{start:289,end:299}}]},{type:"raw",value:`">\r
    <div class="ps-modal__header">\r
      <h2 class="ps-modal__title" id="modal-title">`,position:{start:299,end:390}},{type:"output",position:{start:390,end:401},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:390,end:401}}]},{type:"raw",value:`</h2>\r
      <button class="ps-modal__close" aria-label="Close dialog">\r
        <svg aria-hidden="true" width="24" height="24">\r
          <use href="#icon-close"></use>\r
        </svg>\r
      </button>\r
    </div>\r
    <div class="ps-modal__body">\r
      `,position:{start:401,end:658}},{type:"output",position:{start:658,end:671},stack:[{type:"Twig.expression.type.variable",value:"content",match:["content"],position:{start:658,end:671}}]},{type:"raw",value:`\r
    </div>\r
    <div class="ps-modal__footer">\r
      <button class="ps-button ps-button--secondary">Cancel</button>\r
      <button class="ps-button ps-button--primary">Confirm</button>\r
    </div>\r
  </div>\r
</div>\r
`,position:{start:671,end:671}}],precompiled:!0});i.options.allowInlineIncludes=!0;try{let e=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(e)||(e=Object.entries(e)),p(i.render({attributes:new _(e),...t}))}catch(e){return p("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/modal/modal.twig: "+e.toString())}},n={title:"Confirm Action",content:"Are you sure you want to proceed?",show:!1,backdrop:!0,size:"md"},z={title:"Components/Modal",tags:["autodocs"]},a={name:"Modal",render:t=>o({...t,show:!0}),args:{...n}},r={name:"Small Size",render:t=>o({...t,show:!0,size:"sm"}),args:{...n}},s={name:"Large Size",render:t=>o({...t,show:!0,size:"lg"}),args:{...n}};var l,d,c;a.parameters={...a.parameters,docs:{...(l=a.parameters)==null?void 0:l.docs,source:{originalSource:`{
  name: 'Modal',
  render: args => markup({
    ...args,
    show: true
  }),
  args: {
    ...data
  }
}`,...(c=(d=a.parameters)==null?void 0:d.docs)==null?void 0:c.source}}};var u,m,g;r.parameters={...r.parameters,docs:{...(u=r.parameters)==null?void 0:u.docs,source:{originalSource:`{
  name: 'Small Size',
  render: args => markup({
    ...args,
    show: true,
    size: 'sm'
  }),
  args: {
    ...data
  }
}`,...(g=(m=r.parameters)==null?void 0:m.docs)==null?void 0:g.source}}};var w,y,v;s.parameters={...s.parameters,docs:{...(w=s.parameters)==null?void 0:w.docs,source:{originalSource:`{
  name: 'Large Size',
  render: args => markup({
    ...args,
    show: true,
    size: 'lg'
  }),
  args: {
    ...data
  }
}`,...(v=(y=s.parameters)==null?void 0:y.docs)==null?void 0:v.source}}};const T=["Default","Small","Large"];export{a as Default,s as Large,r as Small,T as __namedExportsOrder,z as default};
