import{t as v,T as w}from"./iframe-C-ciPShf.js";import{D as f,a as T}from"./twig-B9SdSbF4.js";T(w);w.cache(!1);const n=e=>e,p=(e={})=>{const t=v.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/search-bar/search-bar.twig",data:[{type:"raw",value:"",position:{start:515,end:517}},{type:"logic",token:{type:"Twig.logic.type.set",key:"placeholder",expression:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"Search..."},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:517,end:575}},position:{start:517,end:575}},{type:"logic",token:{type:"Twig.logic.type.set",key:"search_text",expression:[{type:"Twig.expression.type.variable",value:"search_text",match:["search_text"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:576,end:625}},position:{start:576,end:625}},{type:"logic",token:{type:"Twig.logic.type.set",key:"show_icon",expression:[{type:"Twig.expression.type.variable",value:"show_icon",match:["show_icon"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!0},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:626,end:673}},position:{start:626,end:673}},{type:"logic",token:{type:"Twig.logic.type.set",key:"has_suggestions",expression:[{type:"Twig.expression.type.variable",value:"has_suggestions",match:["has_suggestions"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:674,end:734}},position:{start:674,end:734}},{type:"logic",token:{type:"Twig.logic.type.set",key:"suggestions",expression:[{type:"Twig.expression.type.variable",value:"suggestions",match:["suggestions"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:735,end:784}},position:{start:735,end:784}},{type:"logic",token:{type:"Twig.logic.type.set",key:"size",expression:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:785,end:822}},position:{start:785,end:822}},{type:"logic",token:{type:"Twig.logic.type.set",key:"attributes",expression:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type._function",fn:"create_attribute",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:823,end:886}},position:{start:823,end:886}},{type:"raw",value:"",position:{start:887,end:888}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-search-bar"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.string",value:"ps-search-bar--"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.null",value:null},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:888,end:981}},position:{start:888,end:981}},{type:"raw",value:"<div ",position:{start:982,end:988}},{type:"output",position:{start:988,end:1022},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:988,end:1022}},{type:"Twig.expression.type.key.period",position:{start:988,end:1022},key:"addClass"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:988,end:1022},expression:!0,params:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:988,end:1022}}]}]},{type:"raw",value:`>
  <form class="ps-search-bar__form" role="search">
    <div class="ps-search-bar__input-wrapper">
      `,position:{start:1022,end:1128}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"show_icon",match:["show_icon"]}],position:{start:1128,end:1146},output:[{type:"raw",value:`        <svg class="ps-search-bar__icon" aria-hidden="true" focusable="false">
          <use xlink:href="#icon-search" />
        </svg>
      `,position:{start:1147,end:1291}}]},position:{open:{start:1128,end:1146},close:{start:1291,end:1302}}},{type:"raw",value:`      <input
        type="search"
        class="ps-search-bar__input"
        placeholder="`,position:{start:1303,end:1396}},{type:"output",position:{start:1396,end:1413},stack:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"],position:{start:1396,end:1413}}]},{type:"raw",value:`"
        value="`,position:{start:1413,end:1430}},{type:"output",position:{start:1430,end:1447},stack:[{type:"Twig.expression.type.variable",value:"search_text",match:["search_text"],position:{start:1430,end:1447}}]},{type:"raw",value:`"
        aria-label="Search"
      />
    </div>
    `,position:{start:1447,end:1501}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"has_suggestions",match:["has_suggestions"]},{type:"Twig.expression.type.variable",value:"search_text",match:["search_text"]},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:1501,end:1541},output:[{type:"raw",value:`      <ul class="ps-search-bar__suggestions" role="listbox">
        `,position:{start:1542,end:1611}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"suggestion",expression:[{type:"Twig.expression.type.variable",value:"suggestions",match:["suggestions"]}],position:{start:1611,end:1646},output:[{type:"raw",value:`          <li class="ps-search-bar__suggestion" role="option">
            <a href="#" class="ps-search-bar__suggestion-link">`,position:{start:1647,end:1773}},{type:"output",position:{start:1773,end:1789},stack:[{type:"Twig.expression.type.variable",value:"suggestion",match:["suggestion"],position:{start:1773,end:1789}}]},{type:"raw",value:`</a>
          </li>
        `,position:{start:1789,end:1818}}]},position:{open:{start:1611,end:1646},close:{start:1818,end:1830}}},{type:"raw",value:`      </ul>
    `,position:{start:1831,end:1847}}]},position:{open:{start:1501,end:1541},close:{start:1847,end:1858}}},{type:"raw",value:`  </form>
</div>
`,position:{start:1859,end:1859}}],precompiled:!0});t.options.allowInlineIncludes=!0;try{let s=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(s)||(s=Object.entries(s)),n(t.render({attributes:new f(s),...e}))}catch(s){return n("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/search-bar/search-bar.twig: "+s.toString())}},o={placeholder:"Search properties...",search_text:"",has_suggestions:!0,suggestions:["Paris Office","London Residences","Berlin Complex"],show_icon:!0,size:"md"},k={title:"Components/Search Bar",tags:["autodocs"],argTypes:{placeholder:{control:"text",description:"Placeholder text for input",table:{category:"Content"}},search_text:{control:"text",description:"Current search text",table:{category:"State"}},has_suggestions:{control:"boolean",description:"Show suggestions dropdown",table:{category:"Configuration"}},show_icon:{control:"boolean",description:"Display search icon",table:{category:"Configuration"}},size:{control:"select",options:["xs","sm","md","lg","xl","xxl"],description:"Size variant",table:{category:"Appearance"}}}},a={name:"Empty",render:e=>p(e),args:{...o,search_text:"",has_suggestions:!1}},r={name:"With Suggestions",render:e=>p(e),args:{...o,search_text:"Paris",has_suggestions:!0}},i={render:()=>`
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 600px;">
        ${["xs","sm","md","lg","xl","xxl"].map(t=>`
          <div>
            <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600); text-transform: uppercase;">${t}</label>
            ${p({...o,size:t,placeholder:`Search (${t.toUpperCase()})...`,has_suggestions:!1})}
          </div>
        `).join("")}
      </div>
    `};var l,c,y;a.parameters={...a.parameters,docs:{...(l=a.parameters)==null?void 0:l.docs,source:{originalSource:`{
  name: 'Empty',
  render: args => markup(args),
  args: {
    ...data,
    search_text: '',
    has_suggestions: false
  }
}`,...(y=(c=a.parameters)==null?void 0:c.docs)==null?void 0:y.source}}};var u,g,d;r.parameters={...r.parameters,docs:{...(u=r.parameters)==null?void 0:u.docs,source:{originalSource:`{
  name: 'With Suggestions',
  render: args => markup(args),
  args: {
    ...data,
    search_text: 'Paris',
    has_suggestions: true
  }
}`,...(d=(g=r.parameters)==null?void 0:g.docs)==null?void 0:d.source}}};var h,m,x;i.parameters={...i.parameters,docs:{...(h=i.parameters)==null?void 0:h.docs,source:{originalSource:`{
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
    return \`
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 600px;">
        \${sizes.map(size => \`
          <div>
            <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600); text-transform: uppercase;">\${size}</label>
            \${markup({
      ...data,
      size,
      placeholder: \`Search (\${size.toUpperCase()})...\`,
      has_suggestions: false
    })}
          </div>
        \`).join('')}
      </div>
    \`;
  }
}`,...(x=(m=i.parameters)==null?void 0:m.docs)==null?void 0:x.source}}};const z=["Default","WithSuggestions","Sizes"];export{a as Default,i as Sizes,r as WithSuggestions,z as __namedExportsOrder,k as default};
