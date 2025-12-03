import{t as p,T as n}from"./iframe-CnHaBuCA.js";import{D as d,a as l}from"./twig-Dp8duUs-.js";l(n);n.cache(!1);const s=e=>e,m=(e={})=>{const r=p.twig({id:"C:/wamp64/www/ps_theme/source/patterns/base/aspects/aspects.twig",data:[{type:"raw",value:`<section class="demo-aspect-ratio">\r
  <div class="just-for-gap">\r
    <div class="heading">Aspect Ratios</div>\r
    <article class="aspect-demo">\r
      <div style="aspect-ratio: var(--ratio-box)">box</div>\r
      <div style="aspect-ratio: var(--ratio-photo)">photo</div>\r
      <div style="aspect-ratio: var(--ratio-portrait)">portrait</div>\r
      <div style="aspect-ratio: var(--ratio-landscape)">landscape</div>\r
      <div style="aspect-ratio: var(--ratio-widescreen)">widescreen</div>\r
      <div style="aspect-ratio: var(--ratio-cinemascope)">cinemascope</div>\r
    </article>\r
  </div>\r
</section>\r
`,position:{start:0,end:0}}],precompiled:!0});r.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),s(r.render({attributes:new d(t),...e}))}catch(t){return s("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/base/aspects/aspects.twig: "+t.toString())}},w={title:"Base/Aspect ratios",parameters:{docs:{description:{component:`
## Aspect Ratio System

Common aspect ratios for images, videos, and containers.

### Usage

\`\`\`css
/* Images */
aspect-ratio: var(--ratio-box); /* 1:1 - Square */
aspect-ratio: var(--ratio-photo); /* 4:3 - Classic photo */
aspect-ratio: var(--ratio-portrait); /* 3:4 - Vertical */

/* Video */
aspect-ratio: var(--ratio-widescreen); /* 16:9 - HD video */
aspect-ratio: var(--ratio-cinemascope); /* 21:9 - Ultra-wide */

/* Special */
aspect-ratio: var(--ratio-landscape); /* 3:2 - Photography */
aspect-ratio: var(--ratio-golden); /* 1.618:1 - Golden ratio */
\`\`\`

### Ratio Reference

- **box**: 1 / 1 - Square avatars, thumbnails
- **photo**: 4 / 3 - Classic photography, presentations
- **portrait**: 3 / 4 - Vertical images, mobile
- **landscape**: 3 / 2 - SLR cameras, print
- **widescreen**: 16 / 9 - HD video, monitors
- **cinemascope**: 21 / 9 - Ultra-wide displays
- **golden**: 1.618 / 1 - Aesthetic compositions

### Reference

- **Source**: \`source/props/aspects.css\` (7 tokens)
- **Usage**: Images, videos, containers
- **Browser support**: Modern browsers (IE11 fallback needed)
        `}}}},a={name:"Aspect ratios",render:e=>m(e)};var o,i,c;a.parameters={...a.parameters,docs:{...(o=a.parameters)==null?void 0:o.docs,source:{originalSource:`{
  name: 'Aspect ratios',
  render: args => aspects(args)
}`,...(c=(i=a.parameters)==null?void 0:i.docs)==null?void 0:c.source}}};const g=["Aspects"];export{a as Aspects,g as __namedExportsOrder,w as default};
