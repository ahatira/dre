import{c as a}from"./card-DXXn6fEP.js";import"./iframe-BXCbAV1K.js";import"./twig-CSYqopkt.js";const N={title:"Components/Card (Generic)",tags:["autodocs"],render:e=>{const c=e.showImage?`<img src="${e.imageUrl}" alt="Card image" />`:"",A=e.contentHTML||"<h3>Card Title</h3><p>Card description goes here...</p>";return a(e).replace('<div class="ps-card__content">',`${c?`<div class="ps-card__image">${c}</div>`:""}<div class="ps-card__content">${A}`).replace("</div></div>","</div></div></div>")},argTypes:{variant:{control:"select",options:["default","outlined","flat","elevated"],description:"Visual variant",table:{category:"Appearance"}},layout:{control:"select",options:["vertical","horizontal"],description:"Layout orientation",table:{category:"Layout"}},size:{control:"select",options:["small","medium","large"],description:"Padding size",table:{category:"Layout"}},radius:{control:"select",options:["none","sm","md","lg"],description:"Border radius",table:{category:"Appearance"}},imagePosition:{control:"select",options:["top","bottom","left","right"],description:"Image position (vertical: top/bottom, horizontal: left/right)",table:{category:"Layout"}},url:{control:"text",description:"Optional card link URL (wraps entire card)",table:{category:"Behavior"}},showImage:{control:!1,description:"[Storybook only] Show image block",table:{category:"Demo",disable:!0}},imageUrl:{control:!1,description:"[Storybook only] Image URL",table:{category:"Demo",disable:!0}},contentHTML:{control:!1,description:"[Storybook only] Content HTML",table:{category:"Demo",disable:!0}}}},t={args:{variant:"default",layout:"vertical",size:"medium",radius:"none",imagePosition:"top",showImage:!0,imageUrl:"https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop&q=80",contentHTML:'<h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">Card Title</h3><p style="margin: 12px 0 0; color: #666;">This is a generic card container. Content is composed using Twig blocks for maximum flexibility.</p>'}},i={render:()=>`
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
      ${["default","outlined","flat","elevated"].map(e=>a({variant:e,layout:"vertical",size:"medium"}).replace('<div class="ps-card__content">',`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #333;">${e.charAt(0).toUpperCase()+e.slice(1)} Card</h3><p style="margin: 8px 0 0; color: #666;">Visual variant: ${e}</p>`).replace("</div></div>","</div></div></div>")).join("")}
    </div>
  `},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: 24px;">
      <div>
        <h4 style="margin-bottom: 12px;">Vertical Layout</h4>
        ${a({layout:"vertical"}).replace('<div class="ps-card__content">','<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=300&fit=crop&q=80" alt="Vertical" /></div><div class="ps-card__content"><h3 style="margin: 0;">Vertical Card</h3><p style="margin: 8px 0 0;">Image on top</p>').replace("</div></div>","</div></div></div>")}
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout</h4>
        ${a({layout:"horizontal"}).replace('<div class="ps-card__content">','<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=242&h=212&fit=crop&q=80" alt="Horizontal" /></div><div class="ps-card__content"><h3 style="margin: 0;">Horizontal Card</h3><p style="margin: 8px 0 0;">Image on left (242px width)</p>').replace("</div></div>","</div></div></div>")}
      </div>
    </div>
  `},o={render:()=>`
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
      ${["small","medium","large"].map(e=>a({size:e}).replace('<div class="ps-card__content">',`<div class="ps-card__content"><h3 style="margin: 0; font-size: ${e==="small"?"16px":e==="large"?"24px":"20px"};">${e.charAt(0).toUpperCase()+e.slice(1)}</h3><p style="margin: 8px 0 0;">Padding: ${e==="small"?"16px":e==="large"?"32px":"30px 24px"}</p>`)).join("")}
    </div>
  `},r={render:()=>`
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
      ${["none","sm","md","lg"].map(e=>a({radius:e}).replace('<div class="ps-card__content">',`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px;">${e==="none"?"No Radius":`${e.toUpperCase()} Radius`}</h3><p style="margin: 8px 0 0;">Border radius: ${e}</p>`).replace("</div></div>","</div></div></div>")).join("")}
    </div>
  `},d={render:()=>`
    <div style="display: flex; flex-direction: column; gap: 32px;">
      <div>
        <h4 style="margin-bottom: 12px;">Vertical Layout - Image Positions</h4>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
          ${["top","bottom"].map(e=>a({layout:"vertical",imagePosition:e}).replace('<div class="ps-card__content">',`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0;">Image ${e==="top"?"Top":"Bottom"}</h3><p style="margin: 8px 0 0;">Position: ${e}</p>`).replace("</div></div>","</div></div></div>")).join("")}
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout - Image Positions</h4>
        <div style="display: flex; flex-direction: column; gap: 16px;">
          ${["left","right"].map(e=>a({layout:"horizontal",imagePosition:e}).replace('<div class="ps-card__content">',`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=242&h=212&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0;">Image ${e==="left"?"Left":"Right"}</h3><p style="margin: 8px 0 0;">Position: ${e} (242px width)</p>`).replace("</div></div>","</div></div></div>")).join("")}
        </div>
      </div>
    </div>
  `},p={args:{variant:"default",layout:"vertical",size:"medium",url:"#card-link",showImage:!0,imageUrl:"https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop&q=80",contentHTML:'<h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">Clickable Card</h3><p style="margin: 12px 0 0; color: #666;">This entire card is clickable. Hover to see the shadow effect.</p>'}},l={render:()=>`
    <div style="max-width: 400px;">
      <h4 style="margin-bottom: 12px;">Example: News Article Card</h4>
      ${a({variant:"elevated"}).replace('<div class="ps-card__content">',`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=400&h=300&fit=crop&q=80" alt="News" /></div><div class="ps-card__content">
          <div class="ps-card__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="display: inline-block; padding: 4px 8px; background: #00915A; color: white; font-size: 12px; font-weight: 600; border-radius: 4px; text-transform: uppercase;">News</span>
            <span style="font-size: 14px; color: #777;">Nov 30, 2025</span>
          </div>
          <div class="ps-card__body">
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 700; color: #333;">Card Composition Pattern</h3>
            <p style="margin: 0; color: #666;">Use Twig blocks to compose custom card content. This example shows a news article with header (badge + date), body (title + excerpt), and footer (read more link).</p>
          </div>
          <div class="ps-card__footer" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee;">
            <a href="#" style="color: #00915A; text-decoration: none; font-weight: 600;">Read more →</a>
          </div>`).replace("</div></div>","</div></div></div>")}
    </div>
  `};var n,m,g;t.parameters={...t.parameters,docs:{...(n=t.parameters)==null?void 0:n.docs,source:{originalSource:`{
  args: {
    variant: 'default',
    layout: 'vertical',
    size: 'medium',
    radius: 'none',
    imagePosition: 'top',
    showImage: true,
    imageUrl: 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop&q=80',
    contentHTML: '<h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">Card Title</h3><p style="margin: 12px 0 0; color: #666;">This is a generic card container. Content is composed using Twig blocks for maximum flexibility.</p>'
  }
}`,...(g=(m=t.parameters)==null?void 0:m.docs)==null?void 0:g.source}}};var v,h,u;i.parameters={...i.parameters,docs:{...(v=i.parameters)==null?void 0:v.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
      \${['default', 'outlined', 'flat', 'elevated'].map(variant => cardTwig({
    variant,
    layout: 'vertical',
    size: 'medium'
  }).replace('<div class="ps-card__content">', \`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #333;">\${variant.charAt(0).toUpperCase() + variant.slice(1)} Card</h3><p style="margin: 8px 0 0; color: #666;">Visual variant: \${variant}</p>\`).replace('</div></div>', '</div></div></div>')).join('')}
    </div>
  \`
}`,...(u=(h=i.parameters)==null?void 0:h.docs)==null?void 0:u.source}}};var y,f,x;s.parameters={...s.parameters,docs:{...(y=s.parameters)==null?void 0:y.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: 24px;">
      <div>
        <h4 style="margin-bottom: 12px;">Vertical Layout</h4>
        \${cardTwig({
    layout: 'vertical'
  }).replace('<div class="ps-card__content">', '<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=300&fit=crop&q=80" alt="Vertical" /></div><div class="ps-card__content"><h3 style="margin: 0;">Vertical Card</h3><p style="margin: 8px 0 0;">Image on top</p>').replace('</div></div>', '</div></div></div>')}
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout</h4>
        \${cardTwig({
    layout: 'horizontal'
  }).replace('<div class="ps-card__content">', '<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=242&h=212&fit=crop&q=80" alt="Horizontal" /></div><div class="ps-card__content"><h3 style="margin: 0;">Horizontal Card</h3><p style="margin: 8px 0 0;">Image on left (242px width)</p>').replace('</div></div>', '</div></div></div>')}
      </div>
    </div>
  \`
}`,...(x=(f=s.parameters)==null?void 0:f.docs)==null?void 0:x.source}}};var _,w,b;o.parameters={...o.parameters,docs:{...(_=o.parameters)==null?void 0:_.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
      \${['small', 'medium', 'large'].map(size => cardTwig({
    size
  }).replace('<div class="ps-card__content">', \`<div class="ps-card__content"><h3 style="margin: 0; font-size: \${size === 'small' ? '16px' : size === 'large' ? '24px' : '20px'};">\${size.charAt(0).toUpperCase() + size.slice(1)}</h3><p style="margin: 8px 0 0;">Padding: \${size === 'small' ? '16px' : size === 'large' ? '32px' : '30px 24px'}</p>\`)).join('')}
    </div>
  \`
}`,...(b=(w=o.parameters)==null?void 0:w.docs)==null?void 0:b.source}}};var $,z,C;r.parameters={...r.parameters,docs:{...($=r.parameters)==null?void 0:$.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
      \${['none', 'sm', 'md', 'lg'].map(radius => cardTwig({
    radius
  }).replace('<div class="ps-card__content">', \`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px;">\${radius === 'none' ? 'No Radius' : \`\${radius.toUpperCase()} Radius\`}</h3><p style="margin: 8px 0 0;">Border radius: \${radius}</p>\`).replace('</div></div>', '</div></div></div>')).join('')}
    </div>
  \`
}`,...(C=(z=r.parameters)==null?void 0:z.docs)==null?void 0:C.source}}};var T,L,k;d.parameters={...d.parameters,docs:{...(T=d.parameters)==null?void 0:T.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: 32px;">
      <div>
        <h4 style="margin-bottom: 12px;">Vertical Layout - Image Positions</h4>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
          \${['top', 'bottom'].map(pos => cardTwig({
    layout: 'vertical',
    imagePosition: pos
  }).replace('<div class="ps-card__content">', \`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0;">Image \${pos === 'top' ? 'Top' : 'Bottom'}</h3><p style="margin: 8px 0 0;">Position: \${pos}</p>\`).replace('</div></div>', '</div></div></div>')).join('')}
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout - Image Positions</h4>
        <div style="display: flex; flex-direction: column; gap: 16px;">
          \${['left', 'right'].map(pos => cardTwig({
    layout: 'horizontal',
    imagePosition: pos
  }).replace('<div class="ps-card__content">', \`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=242&h=212&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0;">Image \${pos === 'left' ? 'Left' : 'Right'}</h3><p style="margin: 8px 0 0;">Position: \${pos} (242px width)</p>\`).replace('</div></div>', '</div></div></div>')).join('')}
        </div>
      </div>
    </div>
  \`
}`,...(k=(L=d.parameters)==null?void 0:L.docs)==null?void 0:k.source}}};var I,P,q;p.parameters={...p.parameters,docs:{...(I=p.parameters)==null?void 0:I.docs,source:{originalSource:`{
  args: {
    variant: 'default',
    layout: 'vertical',
    size: 'medium',
    url: '#card-link',
    showImage: true,
    imageUrl: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop&q=80',
    contentHTML: '<h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">Clickable Card</h3><p style="margin: 12px 0 0; color: #666;">This entire card is clickable. Hover to see the shadow effect.</p>'
  }
}`,...(q=(P=p.parameters)==null?void 0:P.docs)==null?void 0:q.source}}};var H,U,V;l.parameters={...l.parameters,docs:{...(H=l.parameters)==null?void 0:H.docs,source:{originalSource:`{
  render: () => \`
    <div style="max-width: 400px;">
      <h4 style="margin-bottom: 12px;">Example: News Article Card</h4>
      \${cardTwig({
    variant: 'elevated'
  }).replace('<div class="ps-card__content">', \`<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=400&h=300&fit=crop&q=80" alt="News" /></div><div class="ps-card__content">
          <div class="ps-card__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="display: inline-block; padding: 4px 8px; background: #00915A; color: white; font-size: 12px; font-weight: 600; border-radius: 4px; text-transform: uppercase;">News</span>
            <span style="font-size: 14px; color: #777;">Nov 30, 2025</span>
          </div>
          <div class="ps-card__body">
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 700; color: #333;">Card Composition Pattern</h3>
            <p style="margin: 0; color: #666;">Use Twig blocks to compose custom card content. This example shows a news article with header (badge + date), body (title + excerpt), and footer (read more link).</p>
          </div>
          <div class="ps-card__footer" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee;">
            <a href="#" style="color: #00915A; text-decoration: none; font-weight: 600;">Read more →</a>
          </div>\`).replace('</div></div>', '</div></div></div>')}
    </div>
  \`
}`,...(V=(U=l.parameters)==null?void 0:U.docs)==null?void 0:V.source}}};const B=["Default","VisualVariants","Layouts","Sizes","RadiusOptions","ImagePositions","AsLink","CompositionExample"];export{p as AsLink,l as CompositionExample,t as Default,d as ImagePositions,s as Layouts,r as RadiusOptions,o as Sizes,i as VisualVariants,B as __namedExportsOrder,N as default};
