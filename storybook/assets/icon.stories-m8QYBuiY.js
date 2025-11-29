import{i as e}from"./icon-VIKZ_rVJ.js";import"./iframe-itFLYljW.js";import"https://kit.fontawesome.com/a0eb0bad75.js";import"./twig-DayQgIu7.js";const H={generic:["icon-search","icon-check","icon-close","icon-edit","icon-download","icon-share","icon-send","icon-checkbox","icon-checkbox-checked","icon-radio-selected","icon-radio-unselected","icon-plus-big","icon-plus-small","icon-moins-big","icon-moins-small","icon-settings","icon-help","icon-infos","icon-fav-filled","icon-fav-stroke"],mobile:["icon-menu","icon-touch"],tutoffice:["icon-next","icon-pause","icon-play","icon-previous"],social:["icon-facebook","icon-linkedin","icon-twitter","icon-x-twitter","icon-youtube","icon-youtube-1"],tools:["icon-open-space","icon-common-areas"],univers:["icon-offices","icon-shops","icon-coworking","icon-logistic-warehouses","icon-business-premises","icon-surface","icon-residentiel","icon-hospitality"],ad:["icon-accessibility-1","icon-elevator","icon-air-conditioning","icon-bus","icon-car","icon-train","icon-tram","icon-rer","icon-metro","icon-parking","icon-partitioned-offices","icon-meeting-rooms-number","icon-waiting-room","icon-welcome-room","icon-vestiaires","icon-sport-room","icon-sanitary","icon-kitchen","icon-host","icon-hotel","icon-price","icon-gas-emission","icon-energy-cons","icon-the-mosts","icon-medal"]},J=["icon-elevator","icon-bike","icon-equipement","icon-equipement-1","icon-bus","icon-energy-cons","icon-air-conditioning","icon-car","icon-business-lounge","icon-false-ceiling","icon-fav-filled","icon-fav-stroke","icon-floors","icon-gas-emission","icon-host","icon-hotel","icon-kitchen","icon-meeting-rooms-number","icon-metro","icon-not-available","icon-parking","icon-partitioned-offices","icon-comparator-empty","icon-phone","icon-price","icon-restaurant","icon-share","icon-sanitary","icon-sport-room","icon-surface","icon-train","icon-the-mosts","icon-tram","icon-transport","icon-accessibility","icon-vestiaires","icon-virtual-tour","icon-waiting-room","icon-walking","icon-welcome-room","icon-bus-1","icon-energy-cons-1","icon-air-conditioning-1","icon-testimony","icon-trends","icon-account","icon-advise","icon-buy-rent","icon-capital-market","icon-entrusting-a-property","icon-business-premises","icon-coworking","icon-logistic-warehouses","icon-offices","icon-shops","icon-next","icon-pause","icon-play","icon-previous","icon-common-areas","icon-open-space","icon-facebook","icon-linkedin","icon-mail-outline","icon-mail","icon-twitter","icon-youtube","icon-comparateur","icon-create-alert","icon-drag-and-drop","icon-list","icon-select-area-map","icon-menu","icon-touch","icon-district","icon-medal","icon-around-me","icon-arrow-down","icon-arrow-left","icon-arrow-top","icon-big-arrow-down","icon-arrow-right","icon-big-arrow-left","icon-big-arrow-right","icon-big-arrow-top","icon-bin","icon-calendar","icon-check","icon-checkbox-checked","icon-checkbox","icon-close","icon-download","icon-edit","icon-euro","icon-help","icon-infos","icon-last-articles","icon-map","icon-moins-big","icon-moins-small","icon-picture","icon-pin-map","icon-plus-big","icon-plus-small","icon-pwd-hide","icon-pwd-show","icon-quote","icon-rer","icon-search","icon-send","icon-settings","icon-video","icon-x-twitter","icon-youtube-1","icon-hospitality","icon-residentiel","icon-radio-selected","icon-radio-unselected","icon-accessibility-1","icon-favourite3","icon-poi-sport","icon-poi-transport","icon-poi-autre","icon-poi-commerce","icon-poi-education","icon-poi-hotel","icon-poi-loisir","icon-poi-parc","icon-poi-sante","icon-poi-service","icon-poi-bus-clear","icon-poi-metro-clear","icon-poi-rer-clear","icon-poi-tram-clear"],g={categories:H,all:J},K={name:"icon-search",size:"medium",disabled:!1},X={title:"Elements/Icon",tags:["autodocs"],parameters:{docs:{description:{component:"Système d'icônes (bnpre-icons + bnpre-icons-poi). \nGroupement visuel par catégorie selon la maquette: Generic, Mobile only, Tutoffice, Social media, Tools, Univers, Ad, Autres.\nToutes les classes proviennent de `source/props/icons.css` (générées depuis le SVG)."}}},argTypes:{name:{description:"Nom de classe icone",control:{type:"select"},options:g.all},size:{description:"Taille",control:{type:"select"},options:["small","medium","large","xlarge"]},color:{description:"Couleur (token ou valeur CSS)",control:{type:"color"}},disabled:{description:"État disabled",control:{type:"boolean"}},ariaLabel:{description:"Label accessibilité",control:{type:"text"}}}},o={render:n=>e(n),args:{...K}},a={render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"icon-search",size:"small"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">small (16px)</span>
      ${e({name:"icon-search",size:"medium"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">medium (20px)</span>
      ${e({name:"icon-search",size:"large"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">large (24px)</span>
      ${e({name:"icon-search",size:"xlarge"})}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">xlarge (32px)</span>
    </div>
  `},r={render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"icon-check",size:"xlarge",color:"var(--bnp-green)"})}
      ${e({name:"icon-close",size:"xlarge",color:"var(--red-600)"})}
      ${e({name:"icon-infos",size:"xlarge",color:"var(--blue-500)"})}
      ${e({name:"icon-help",size:"xlarge",color:"var(--amber-500)"})}
    </div>
  `},s={render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"icon-search",size:"xlarge",disabled:!1})}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Normal</span>
      ${e({name:"icon-search",size:"xlarge",disabled:!0})}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Disabled (50% opacity)</span>
    </div>
  `},c={name:"Galerie catégorisée",render:()=>{const n=g.categories||{},u=new Set;Object.values(n).forEach(i=>i.forEach(p=>u.add(p)));const F=g.all.filter(i=>!u.has(i));return`
      <div style="display:flex; flex-direction:column; gap:var(--size-8);">
        ${Object.entries(n).map(([i,p])=>`
          <section>
            <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">${i.charAt(0).toUpperCase()+i.slice(1)}</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
              ${p.map(v=>`
                <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-200); border-radius: var(--radius-2);">
                  ${e({name:v,size:"large"})}
                  <span style="font-size: var(--font-size--1); text-align:center;">${v.replace("icon-","")}</span>
                </div>
              `).join("")}
            </div>
          </section>
        `).join("")}
        <section>
          <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">Autres</h3>
          <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
            ${F.map(i=>`
              <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-100); border-radius: var(--radius-2);">
                ${e({name:i,size:"large"})}
                <span style="font-size: var(--font-size--1); text-align:center;">${i.replace("icon-","")}</span>
              </div>
            `).join("")}
          </div>
        </section>
      </div>
    `}},t={name:"Example: Search Icons",render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"icon-search",size:"medium"})}
      ${e({name:"icon-pin-map",size:"medium"})}
      ${e({name:"icon-map",size:"medium"})}
      ${e({name:"icon-around-me",size:"medium"})}
    </div>
  `},l={name:"Example: Navigation Icons",render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"icon-arrow-left",size:"medium"})}
      ${e({name:"icon-arrow-right",size:"medium"})}
      ${e({name:"icon-arrow-top",size:"medium"})}
      ${e({name:"icon-arrow-down",size:"medium"})}
      ${e({name:"icon-big-arrow-left",size:"medium"})}
      ${e({name:"icon-big-arrow-right",size:"medium"})}
    </div>
  `},m={name:"Example: Action Icons",render:()=>`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${e({name:"icon-check",size:"medium",color:"var(--bnp-green)"})}
      ${e({name:"icon-close",size:"medium",color:"var(--red-600)"})}
      ${e({name:"icon-edit",size:"medium"})}
      ${e({name:"icon-bin",size:"medium"})}
      ${e({name:"icon-share",size:"medium"})}
      ${e({name:"icon-send",size:"medium"})}
    </div>
  `},d={name:"Exemple: Checkbox",render:()=>`
    <div style="display:flex; gap:var(--size-6); align-items:center;">
      ${e({name:"icon-checkbox",size:"medium"})}
      ${e({name:"icon-checkbox-checked",size:"medium",color:"var(--bnp-green)"})}
      ${e({name:"icon-radio-unselected",size:"medium"})}
      ${e({name:"icon-radio-selected",size:"medium",color:"var(--bnp-green)"})}
    </div>
  `};var z,f,y;o.parameters={...o.parameters,docs:{...(z=o.parameters)==null?void 0:z.docs,source:{originalSource:`{
  render: args => icon(args),
  args: {
    ...data
  }
}`,...(y=(f=o.parameters)==null?void 0:f.docs)==null?void 0:y.source}}};var x,h,b;a.parameters={...a.parameters,docs:{...(x=a.parameters)==null?void 0:x.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'icon-search',
    size: 'small'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">small (16px)</span>
      \${icon({
    name: 'icon-search',
    size: 'medium'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">medium (20px)</span>
      \${icon({
    name: 'icon-search',
    size: 'large'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">large (24px)</span>
      \${icon({
    name: 'icon-search',
    size: 'xlarge'
  })}
      <span style="font-size: var(--font-size-0); color: var(--gray-600);">xlarge (32px)</span>
    </div>
  \`
}`,...(b=(h=a.parameters)==null?void 0:h.docs)==null?void 0:b.source}}};var $,w,k;r.parameters={...r.parameters,docs:{...($=r.parameters)==null?void 0:$.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'icon-check',
    size: 'xlarge',
    color: 'var(--bnp-green)'
  })}
      \${icon({
    name: 'icon-close',
    size: 'xlarge',
    color: 'var(--red-600)'
  })}
      \${icon({
    name: 'icon-infos',
    size: 'xlarge',
    color: 'var(--blue-500)'
  })}
      \${icon({
    name: 'icon-help',
    size: 'xlarge',
    color: 'var(--amber-500)'
  })}
    </div>
  \`
}`,...(k=(w=r.parameters)==null?void 0:w.docs)==null?void 0:k.source}}};var S,E,C;s.parameters={...s.parameters,docs:{...(S=s.parameters)==null?void 0:S.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'icon-search',
    size: 'xlarge',
    disabled: false
  })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Normal</span>
      \${icon({
    name: 'icon-search',
    size: 'xlarge',
    disabled: true
  })}
      <span style="font-size: var(--font-size-1); color: var(--gray-600);">Disabled (50% opacity)</span>
    </div>
  \`
}`,...(C=(E=s.parameters)==null?void 0:E.docs)==null?void 0:C.source}}};var A,j,I;c.parameters={...c.parameters,docs:{...(A=c.parameters)==null?void 0:A.docs,source:{originalSource:`{
  name: 'Galerie catégorisée',
  render: () => {
    const categories = iconsList.categories || {};
    const used = new Set();
    Object.values(categories).forEach(arr => arr.forEach(i => used.add(i)));
    const others = iconsList.all.filter(i => !used.has(i));
    return \`
      <div style="display:flex; flex-direction:column; gap:var(--size-8);">
        \${Object.entries(categories).map(([key, list]) => \`
          <section>
            <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">\${key.charAt(0).toUpperCase() + key.slice(1)}</h3>
            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
              \${list.map(name => \`
                <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-200); border-radius: var(--radius-2);">
                  \${icon({
      name,
      size: 'large'
    })}
                  <span style="font-size: var(--font-size--1); text-align:center;">\${name.replace('icon-', '')}</span>
                </div>
              \`).join('')}
            </div>
          </section>
        \`).join('')}
        <section>
          <h3 style="margin:0 0 var(--size-4); font-size: var(--font-size-3);">Autres</h3>
          <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); gap:var(--size-4);">
            \${others.map(name => \`
              <div style="display:flex; flex-direction:column; align-items:center; gap:var(--size-2); padding:var(--size-3); border:1px solid var(--gray-100); border-radius: var(--radius-2);">
                \${icon({
      name,
      size: 'large'
    })}
                <span style="font-size: var(--font-size--1); text-align:center;">\${name.replace('icon-', '')}</span>
              </div>
            \`).join('')}
          </div>
        </section>
      </div>
    \`;
  }
}`,...(I=(j=c.parameters)==null?void 0:j.docs)==null?void 0:I.source}}};var N,D,G;t.parameters={...t.parameters,docs:{...(N=t.parameters)==null?void 0:N.docs,source:{originalSource:`{
  name: 'Example: Search Icons',
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'icon-search',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-pin-map',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-map',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-around-me',
    size: 'medium'
  })}
    </div>
  \`
}`,...(G=(D=t.parameters)==null?void 0:D.docs)==null?void 0:G.source}}};var L,O,T;l.parameters={...l.parameters,docs:{...(L=l.parameters)==null?void 0:L.docs,source:{originalSource:`{
  name: 'Example: Navigation Icons',
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'icon-arrow-left',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-arrow-right',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-arrow-top',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-arrow-down',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-big-arrow-left',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-big-arrow-right',
    size: 'medium'
  })}
    </div>
  \`
}`,...(T=(O=l.parameters)==null?void 0:O.docs)==null?void 0:T.source}}};var q,U,_;m.parameters={...m.parameters,docs:{...(q=m.parameters)==null?void 0:q.docs,source:{originalSource:`{
  name: 'Example: Action Icons',
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      \${icon({
    name: 'icon-check',
    size: 'medium',
    color: 'var(--bnp-green)'
  })}
      \${icon({
    name: 'icon-close',
    size: 'medium',
    color: 'var(--red-600)'
  })}
      \${icon({
    name: 'icon-edit',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-bin',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-share',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-send',
    size: 'medium'
  })}
    </div>
  \`
}`,...(_=(U=m.parameters)==null?void 0:U.docs)==null?void 0:_.source}}};var M,V,B;d.parameters={...d.parameters,docs:{...(M=d.parameters)==null?void 0:M.docs,source:{originalSource:`{
  name: 'Exemple: Checkbox',
  render: () => \`
    <div style="display:flex; gap:var(--size-6); align-items:center;">
      \${icon({
    name: 'icon-checkbox',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-checkbox-checked',
    size: 'medium',
    color: 'var(--bnp-green)'
  })}
      \${icon({
    name: 'icon-radio-unselected',
    size: 'medium'
  })}
      \${icon({
    name: 'icon-radio-selected',
    size: 'medium',
    color: 'var(--bnp-green)'
  })}
    </div>
  \`
}`,...(B=(V=d.parameters)==null?void 0:V.docs)==null?void 0:B.source}}};const Y=["Default","AllSizes","CustomColors","Disabled","Categories","SearchExample","NavigationExample","ActionsExample","CheckboxIcons"];export{m as ActionsExample,a as AllSizes,c as Categories,d as CheckboxIcons,r as CustomColors,o as Default,s as Disabled,l as NavigationExample,t as SearchExample,Y as __namedExportsOrder,X as default};
