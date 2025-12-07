import{t as m,T as p}from"./iframe-GGfdoSBx.js";import{D as c,a as l}from"./twig-Dqrk-56N.js";l(p);p.cache(!1);const a=t=>t,d=(t={})=>{const o=m.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/form/form.twig",data:[{type:"raw",value:`\r
<form`,position:{start:179,end:186}},{type:"output",position:{start:186,end:202},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:186,end:202}}]},{type:"raw",value:`>\r
  `,position:{start:202,end:207}},{type:"output",position:{start:207,end:221},stack:[{type:"Twig.expression.type.variable",value:"children",match:["children"],position:{start:207,end:221}}]},{type:"raw",value:`\r
</form>`,position:{start:221,end:221}}],precompiled:!0});o.options.allowInlineIncludes=!0;try{let e=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(e)||(e=Object.entries(e)),a(o.render({attributes:new c(e),...t}))}catch(e){return a("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/form/form.twig: "+e.toString())}},u={class:"ps-form",method:"POST"},b={title:"Components/Form",tags:["autodocs"],parameters:{docs:{description:{component:"Drupal form wrapper. Use with Drupal form render arrays."}}},argTypes:{class:{description:"CSS class for form",control:"text",table:{category:"Appearance"}},method:{description:"Form method (POST, GET)",control:"select",options:["POST","GET"],table:{category:"Behavior"}}},render:t=>d({...t,attributes:{class:t.class,method:t.method},children:`
        <div class="form-item">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" class="form-control" />
        </div>
        <div class="form-item">
          <button type="submit" class="ps-button">Submit</button>
        </div>
      `})},r={args:u};var s,n,i;r.parameters={...r.parameters,docs:{...(s=r.parameters)==null?void 0:s.docs,source:{originalSource:`{
  args: data
}`,...(i=(n=r.parameters)==null?void 0:n.docs)==null?void 0:i.source}}};const y=["Default"];export{r as Default,y as __namedExportsOrder,b as default};
