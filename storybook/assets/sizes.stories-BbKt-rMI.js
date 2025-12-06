import{t as a,T as r}from"./iframe-DeCmpQ6I.js";import{D as n,a as o}from"./twig-Cbw8xbjJ.js";o(r);r.cache(!1);const t=e=>e,m=(e={})=>{const s=a.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/sizes/sizes.twig",data:[{type:"raw",value:`<section class="demo-sizes">\r
  <article class="size-demo">\r
    <div><h4>Size</h4></div>\r
    <div><h4>REM</h4></div>\r
    <div><h4>PX</h4></div>\r
    <div><h4>Example</h4></div>\r
\r
    `,position:{start:0,end:187}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"spacing",match:["spacing"]}],position:{start:187,end:212},output:[{type:"raw",value:`\r
      <div>`,position:{start:212,end:225}},{type:"output",position:{start:225,end:240},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:225,end:240}},{type:"Twig.expression.type.key.period",position:{start:225,end:240},key:"size"}]},{type:"raw",value:`</div>\r
      <div>`,position:{start:240,end:259}},{type:"output",position:{start:259,end:273},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:259,end:273}},{type:"Twig.expression.type.key.period",position:{start:259,end:273},key:"rem"}]},{type:"raw",value:`</div>\r
      <div>`,position:{start:273,end:292}},{type:"output",position:{start:292,end:305},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:292,end:305}},{type:"Twig.expression.type.key.period",position:{start:292,end:305},key:"px"}]},{type:"raw",value:`</div>\r
      <div>\r
        <div style="block-size: 1rem; inline-size:`,position:{start:305,end:376}},{type:"output",position:{start:376,end:390},stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"],position:{start:376,end:390}},{type:"Twig.expression.type.key.period",position:{start:376,end:390},key:"rem"}]},{type:"raw",value:`; background-color: var(--gray-300);"></div>\r
      </div>\r
    `,position:{start:390,end:454}}]},position:{open:{start:187,end:212},close:{start:454,end:466}}},{type:"raw",value:`\r
  </article>\r
</section>\r
`,position:{start:466,end:466}}],precompiled:!0});s.options.allowInlineIncludes=!0;try{let i=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(i)||(i=Object.entries(i)),t(s.render({attributes:new n(i),...e}))}catch(i){return t("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/sizes/sizes.twig: "+i.toString())}},p={spacing:[{size:"--size-px",px:"1px",rem:"0.0625rem"},{size:"--size-05",px:"2px",rem:"0.125rem"},{size:"--size-1",px:"4px",rem:"0.25rem"},{size:"--size-2",px:"8px",rem:"0.5rem"},{size:"--size-3",px:"12px",rem:"0.75rem"},{size:"--size-4",px:"16px",rem:"1rem"},{size:"--size-5",px:"20px",rem:"1.25rem"},{size:"--size-6",px:"24px",rem:"1.5rem"},{size:"--size-7",px:"28px",rem:"1.75rem"},{size:"--size-8",px:"32px",rem:"2rem"},{size:"--size-9",px:"36px",rem:"2.25rem"},{size:"--size-10",px:"40px",rem:"2.5rem"},{size:"--size-11",px:"44px",rem:"2.75rem"},{size:"--size-12",px:"48px",rem:"3rem"},{size:"--size-14",px:"56px",rem:"3.5rem"},{size:"--size-16",px:"64px",rem:"4rem"},{size:"--size-20",px:"80px",rem:"5rem"},{size:"--size-24",px:"96px",rem:"6rem"},{size:"--size-28",px:"112px",rem:"7rem"},{size:"--size-32",px:"128px",rem:"8rem"},{size:"--size-36",px:"144px",rem:"9rem"},{size:"--size-40",px:"160px",rem:"10rem"},{size:"--size-44",px:"176px",rem:"11rem"},{size:"--size-48",px:"192px",rem:"12rem"},{size:"--size-52",px:"208px",rem:"13rem"},{size:"--size-56",px:"224px",rem:"14rem"},{size:"--size-60",px:"240px",rem:"15rem"},{size:"--size-64",px:"256px",rem:"16rem"},{size:"--size-72",px:"288px",rem:"18rem"},{size:"--size-80",px:"320px",rem:"20rem"},{size:"--size-96",px:"384px",rem:"24rem"}]},d={title:"Base/Sizes",args:{...p},parameters:{docs:{description:{component:`
## Size System (Spacing Scale)

Complete spacing scale with 33 tokens from 1px to 384px.

### Usage

\`\`\`css
/* Spacing */
padding: var(--size-4); /* 16px - base unit */
margin-block-end: var(--size-6); /* 24px */
gap: var(--size-2); /* 8px - tight spacing */

/* Dimensions */
width: var(--size-48); /* 192px */
height: var(--size-32); /* 128px */
max-width: var(--size-max-content-width); /* 1376px */

/* Common patterns */
padding: var(--size-3) var(--size-6); /* 12px 24px */
margin-inline: var(--size-auto); /* Center align */
\`\`\`

### Scale Reference

- **Micro**: --size-px (1px), --size-05 (2px)
- **Tight**: --size-1 to --size-4 (4px to 16px)
- **Normal**: --size-5 to --size-12 (20px to 48px)
- **Loose**: --size-14 to --size-32 (56px to 128px)
- **Extra**: --size-36 to --size-96 (144px to 384px)

### Reference

- **Source**: \`source/props/sizes.css\` (33 tokens)
- **Base unit**: 16px (1rem)
- **Usage**: All spacing, dimensions, gaps
        `}}}},c={name:"Sizes",render:e=>m(e),args:{...p}},l=["Sizes"];export{c as Sizes,l as __namedExportsOrder,d as default};
