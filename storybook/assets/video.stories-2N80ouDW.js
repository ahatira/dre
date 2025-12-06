import{t as T,T as b}from"./iframe-DeCmpQ6I.js";import{D as f,a as _}from"./twig-Cbw8xbjJ.js";_(b);b.cache(!1);const p=e=>e,o=(e={})=>{const n=T.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/video/video.twig",data:[{type:"raw",value:`
<div class="ps-video ps-video--`,position:{start:36,end:68}},{type:"output",position:{start:68,end:106},stack:[{type:"Twig.expression.type.variable",value:"aspect_ratio",match:["aspect_ratio"],position:{start:68,end:106}},{type:"Twig.expression.type.filter",value:"replace",match:["| replace","replace"],position:{start:68,end:106},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:68,end:106}},{type:"Twig.expression.type.string",value:":",position:{start:68,end:106}},{type:"Twig.expression.type.comma",position:{start:68,end:106}},{type:"Twig.expression.type.string",value:"-",position:{start:68,end:106}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:68,end:106},expression:!1}]}]},{type:"raw",value:`">
  `,position:{start:106,end:111}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"provider",match:["provider"]},{type:"Twig.expression.type.string",value:"youtube"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="}],position:{start:111,end:141},output:[{type:"raw",value:`    <iframe
      class="ps-video__player"
      src="https://www.youtube.com/embed/`,position:{start:142,end:226}},{type:"output",position:{start:226,end:240},stack:[{type:"Twig.expression.type.variable",value:"video_id",match:["video_id"],position:{start:226,end:240}}]},{type:"raw",value:`"
      title="`,position:{start:240,end:255}},{type:"output",position:{start:255,end:266},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:255,end:266}}]},{type:"raw",value:`"
      frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      allowfullscreen
      aria-label="`,position:{start:266,end:433}},{type:"output",position:{start:433,end:444},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:433,end:444}}]},{type:"raw",value:`"
    ></iframe>
  `,position:{start:444,end:463}}]},position:{open:{start:111,end:141},close:{start:463,end:474}}},{type:"raw",value:`</div>
`,position:{start:475,end:475}}],precompiled:!0});n.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),p(n.render({attributes:new f(t),...e}))}catch(t){return p("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/video/video.twig: "+t.toString())}},i={video_id:"dQw4w9WgXcQ",provider:"youtube",aspect_ratio:"16:9",title:"Real Estate Tour"},k={title:"Components/Video",tags:["autodocs"],argTypes:{video_id:{control:"text",description:"YouTube video ID",table:{category:"Content"}},provider:{control:{type:"select"},options:["youtube","vimeo"],description:"Video provider platform",table:{category:"Configuration"}},aspect_ratio:{control:{type:"select"},options:["16:9","4:3","1:1"],description:"Video aspect ratio",table:{category:"Display"}},title:{control:"text",description:"Video title/label",table:{category:"Content"}}}},a={name:"16:9 YouTube",render:e=>o(e),args:{...i,aspect_ratio:"16:9"}},r={name:"1:1 Square",render:e=>o(e),args:{...i,aspect_ratio:"1:1"}},s={name:"4:3 Classic",render:e=>o(e),args:{...i,aspect_ratio:"4:3"}};var c,d,l;a.parameters={...a.parameters,docs:{...(c=a.parameters)==null?void 0:c.docs,source:{originalSource:`{
  name: '16:9 YouTube',
  render: args => markup(args),
  args: {
    ...data,
    aspect_ratio: '16:9'
  }
}`,...(l=(d=a.parameters)==null?void 0:d.docs)==null?void 0:l.source}}};var u,y,m;r.parameters={...r.parameters,docs:{...(u=r.parameters)==null?void 0:u.docs,source:{originalSource:`{
  name: '1:1 Square',
  render: args => markup(args),
  args: {
    ...data,
    aspect_ratio: '1:1'
  }
}`,...(m=(y=r.parameters)==null?void 0:y.docs)==null?void 0:m.source}}};var g,w,v;s.parameters={...s.parameters,docs:{...(g=s.parameters)==null?void 0:g.docs,source:{originalSource:`{
  name: '4:3 Classic',
  render: args => markup(args),
  args: {
    ...data,
    aspect_ratio: '4:3'
  }
}`,...(v=(w=s.parameters)==null?void 0:w.docs)==null?void 0:v.source}}};const C=["Default","Square","Classic"];export{s as Classic,a as Default,r as Square,C as __namedExportsOrder,k as default};
