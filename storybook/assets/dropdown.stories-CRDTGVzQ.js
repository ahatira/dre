import{t as O,T as R}from"./iframe-B-yX16js.js";import{D as B,a as G}from"./twig-CgICq6Dc.js";G(R);R.cache(!1);const v=e=>e,r=(e={})=>{const o=O.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/dropdown/dropdown.twig",data:[{type:"raw",value:"",position:{start:1128,end:1132}},{type:"logic",token:{type:"Twig.logic.type.set",key:"size",expression:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1132,end:1169}},position:{start:1132,end:1169}},{type:"raw",value:"",position:{start:1169,end:1171}},{type:"logic",token:{type:"Twig.logic.type.set",key:"shape",expression:[{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"rounded"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1171,end:1215}},position:{start:1171,end:1215}},{type:"raw",value:"",position:{start:1215,end:1217}},{type:"logic",token:{type:"Twig.logic.type.set",key:"disabled",expression:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1217,end:1263}},position:{start:1217,end:1263}},{type:"raw",value:"",position:{start:1263,end:1265}},{type:"logic",token:{type:"Twig.logic.type.set",key:"color",expression:[{type:"Twig.expression.type.variable",value:"color",match:["color"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"default"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1265,end:1309}},position:{start:1265,end:1309}},{type:"raw",value:"",position:{start:1309,end:1311}},{type:"logic",token:{type:"Twig.logic.type.set",key:"options",expression:[{type:"Twig.expression.type.variable",value:"options",match:["options"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1311,end:1352}},position:{start:1311,end:1352}},{type:"raw",value:"",position:{start:1352,end:1354}},{type:"raw",value:"",position:{start:1427,end:1429}},{type:"logic",token:{type:"Twig.logic.type.set",key:"attributes",expression:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1429,end:1476}},position:{start:1429,end:1476}},{type:"raw",value:"",position:{start:1476,end:1480}},{type:"logic",token:{type:"Twig.logic.type.set",key:"selectedOption",expression:[{type:"Twig.expression.type.null",value:null}],position:{start:1480,end:1513}},position:{start:1480,end:1513}},{type:"raw",value:"",position:{start:1513,end:1515}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"option",expression:[{type:"Twig.expression.type.variable",value:"options",match:["options"]}],position:{start:1515,end:1544},output:[{type:"raw",value:"",position:{start:1544,end:1548}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"]},{type:"Twig.expression.type.key.period",key:"selected"}],position:{start:1548,end:1574},output:[{type:"raw",value:"",position:{start:1574,end:1580}},{type:"logic",token:{type:"Twig.logic.type.set",key:"selectedOption",expression:[{type:"Twig.expression.type.variable",value:"option",match:["option"]}],position:{start:1580,end:1615}},position:{start:1580,end:1615}},{type:"raw",value:"",position:{start:1615,end:1619}}]},position:{open:{start:1548,end:1574},close:{start:1619,end:1632}}},{type:"raw",value:"",position:{start:1632,end:1634}}]},position:{open:{start:1515,end:1544},close:{start:1634,end:1648}}},{type:"raw",value:"",position:{start:1648,end:1652}},{type:"logic",token:{type:"Twig.logic.type.set",key:"displayLabel",expression:[{type:"Twig.expression.type.variable",value:"label",match:["label"]},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"selectedOption",match:["selectedOption"]},{type:"Twig.expression.type.variable",value:"selectedOption",match:["selectedOption"]},{type:"Twig.expression.type.key.period",key:"label"},{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"placeholder",match:["placeholder"]},{type:"Twig.expression.type.string",value:"Select an option"},{type:"Twig.expression.type.operator.binary",value:"??",precidence:15,associativity:"rightToLeft",operator:"??"}]},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"Twig.expression.type.operator.binary",value:"??",precidence:15,associativity:"rightToLeft",operator:"??"}],position:{start:1652,end:1765}},position:{start:1652,end:1765}},{type:"raw",value:"",position:{start:1765,end:1769}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1769,end:1793}},position:{start:1769,end:1793}},{type:"raw",value:"",position:{start:1793,end:1795}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-dropdown"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1795,end:1847}},position:{start:1795,end:1847}},{type:"raw",value:"",position:{start:1847,end:1849}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.string",value:"md"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:1849,end:1881},output:[{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-dropdown--"},{type:"Twig.expression.type.variable",value:"size",match:["size"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1881,end:1942}},position:{start:1881,end:1942}}]},position:{open:{start:1849,end:1881},close:{start:1942,end:1955}}},{type:"raw",value:"",position:{start:1955,end:1957}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.string",value:"rounded"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="},{type:"Twig.expression.type.operator.binary",value:"and",precidence:13,associativity:"leftToRight",operator:"and"}],position:{start:1957,end:1996},output:[{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-dropdown--"},{type:"Twig.expression.type.variable",value:"shape",match:["shape"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1996,end:2058}},position:{start:1996,end:2058}}]},position:{open:{start:1957,end:1996},close:{start:2058,end:2071}}},{type:"raw",value:"",position:{start:2071,end:2073}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:2073,end:2092},output:[{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-dropdown--disabled"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2092,end:2154}},position:{start:2092,end:2154}}]},position:{open:{start:2073,end:2092},close:{start:2154,end:2167}}},{type:"raw",value:'<div class="',position:{start:2167,end:2183}},{type:"output",position:{start:2183,end:2211},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:2183,end:2211}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:2183,end:2211},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2183,end:2211}},{type:"Twig.expression.type.string",value:" ",position:{start:2183,end:2211}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2183,end:2211},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:2183,end:2211}}]},{type:"raw",value:'"',position:{start:2211,end:2213}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:2213,end:2233},output:[{type:"raw",value:" ",position:{start:2233,end:2234}},{type:"output",position:{start:2234,end:2250},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:2234,end:2250}}]}]},position:{open:{start:2213,end:2233},close:{start:2250,end:2262}}},{type:"raw",value:`data-dropdown>\r
  <button\r
    class="ps-dropdown__button"\r
    type="button"\r
    aria-haspopup="listbox"\r
    aria-expanded="false"\r
    data-dropdown-button`,position:{start:2262,end:2428}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:2428,end:2446},output:[{type:"raw",value:' disabled aria-disabled="true"',position:{start:2446,end:2476}}]},position:{open:{start:2428,end:2446},close:{start:2476,end:2488}}},{type:"raw",value:`>\r
    <span class="ps-dropdown__label">`,position:{start:2488,end:2532}},{type:"output",position:{start:2532,end:2550},stack:[{type:"Twig.expression.type.variable",value:"displayLabel",match:["displayLabel"],position:{start:2532,end:2550}}]},{type:"raw",value:`</span>\r
    <svg class="ps-dropdown__icon" aria-hidden="true" focusable="false">\r
      <use xlink:href="#icon-chevron-down" />\r
    </svg>\r
  </button>\r
\r
  <ul\r
    class="ps-dropdown__list"\r
    role="listbox"\r
    tabindex="-1"\r
    hidden\r
    data-dropdown-list\r
  >`,position:{start:2550,end:2829}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"option",expression:[{type:"Twig.expression.type.variable",value:"options",match:["options"]}],position:{start:2829,end:2858},output:[{type:"raw",value:`<li\r
        class="ps-dropdown__option`,position:{start:2858,end:2905}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"]},{type:"Twig.expression.type.key.period",key:"disabled"}],position:{start:2905,end:2929},output:[{type:"raw",value:" ps-dropdown__option--disabled",position:{start:2929,end:2959}}]},position:{open:{start:2905,end:2929},close:{start:2959,end:2970}}},{type:"raw",value:`"\r
        role="option"\r
        aria-selected="`,position:{start:2970,end:3019}},{type:"output",position:{start:3019,end:3059},stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"],position:{start:3019,end:3059}},{type:"Twig.expression.type.key.period",position:{start:3019,end:3059},key:"selected"},{type:"Twig.expression.type.string",value:"true",position:{start:3019,end:3059}},{type:"Twig.expression.type.string",value:"false",position:{start:3019,end:3059}},{type:"Twig.expression.type.operator.binary",value:"?",position:{start:3019,end:3059},precidence:16,associativity:"rightToLeft",operator:"?"}]},{type:"raw",value:`"\r
        data-value="`,position:{start:3059,end:3082}},{type:"output",position:{start:3082,end:3100},stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"],position:{start:3082,end:3100}},{type:"Twig.expression.type.key.period",position:{start:3082,end:3100},key:"value"}]},{type:"raw",value:'"',position:{start:3100,end:3111}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"]},{type:"Twig.expression.type.key.period",key:"disabled"}],position:{start:3111,end:3136},output:[{type:"raw",value:' aria-disabled="true"',position:{start:3136,end:3157}}]},position:{open:{start:3111,end:3136},close:{start:3157,end:3169}}},{type:"raw",value:`>\r
        `,position:{start:3169,end:3188}},{type:"output",position:{start:3188,end:3206},stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"],position:{start:3188,end:3206}},{type:"Twig.expression.type.key.period",position:{start:3188,end:3206},key:"label"}]},{type:"raw",value:`\r
      </li>`,position:{start:3206,end:3225}}]},position:{open:{start:2829,end:2858},close:{start:3225,end:3239}}},{type:"raw",value:`</ul>\r
\r
  <select\r
    class="ps-dropdown__native"\r
    name="`,position:{start:3239,end:3306}},{type:"output",position:{start:3306,end:3316},stack:[{type:"Twig.expression.type.variable",value:"name",match:["name"],position:{start:3306,end:3316}}]},{type:"raw",value:'"',position:{start:3316,end:3323}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"disabled",match:["disabled"]}],position:{start:3323,end:3341},output:[{type:"raw",value:' disabled aria-disabled="true"',position:{start:3341,end:3371}}]},position:{open:{start:3323,end:3341},close:{start:3371,end:3383}}},{type:"raw",value:">",position:{start:3383,end:3394}},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"option",expression:[{type:"Twig.expression.type.variable",value:"options",match:["options"]}],position:{start:3394,end:3423},output:[{type:"raw",value:`<option\r
        value="`,position:{start:3423,end:3455}},{type:"output",position:{start:3455,end:3473},stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"],position:{start:3455,end:3473}},{type:"Twig.expression.type.key.period",position:{start:3455,end:3473},key:"value"}]},{type:"raw",value:'"',position:{start:3473,end:3484}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"]},{type:"Twig.expression.type.key.period",key:"selected"}],position:{start:3484,end:3509},output:[{type:"raw",value:" selected",position:{start:3509,end:3518}}]},position:{open:{start:3484,end:3509},close:{start:3518,end:3530}}},{type:"raw",value:"",position:{start:3530,end:3540}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"]},{type:"Twig.expression.type.key.period",key:"disabled"}],position:{start:3540,end:3565},output:[{type:"raw",value:" disabled",position:{start:3565,end:3574}}]},position:{open:{start:3540,end:3565},close:{start:3574,end:3586}}},{type:"raw",value:`>\r
        `,position:{start:3586,end:3605}},{type:"output",position:{start:3605,end:3623},stack:[{type:"Twig.expression.type.variable",value:"option",match:["option"],position:{start:3605,end:3623}},{type:"Twig.expression.type.key.period",position:{start:3605,end:3623},key:"label"}]},{type:"raw",value:`\r
      </option>`,position:{start:3623,end:3646}}]},position:{open:{start:3394,end:3423},close:{start:3646,end:3660}}},{type:"raw",value:`</select>\r
</div>\r
`,position:{start:3660,end:3660}}],precompiled:!0});o.options.allowInlineIncludes=!0;try{let t=e.defaultAttributes?e.defaultAttributes:[];return Array.isArray(t)||(t=Object.entries(t)),v(o.render({attributes:new B(t),...e}))}catch(t){return v("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/dropdown/dropdown.twig: "+t.toString())}},H={name:"property_type",label:"Property type",size:"md",shape:"rounded",disabled:!1,color:"default",options:[{label:"Apartment",value:"apartment",selected:!0,disabled:!1},{label:"House",value:"house",selected:!1,disabled:!1},{label:"Loft",value:"loft",selected:!1,disabled:!1},{label:"Villa",value:"villa",selected:!1,disabled:!1},{label:"Commercial",value:"commercial",selected:!1,disabled:!1}]},U={title:"Components/Dropdown",tags:["autodocs"],parameters:{docs:{description:{component:"Accessible select dropdown with custom styling, keyboard navigation, and native `<select>` fallback.\n\nSee Props, Showcases (AllSizes, AllShapes), and README for complete details on variants, accessibility, and design tokens."}}},argTypes:{name:{description:"Form field name attribute (required for form submission)",control:{type:"text"},table:{category:"Content",type:{summary:"string",required:!0}}},label:{description:"Visible button label (uses selected option if not provided)",control:{type:"text"},table:{category:"Content",type:{summary:"string"}}},placeholder:{description:"Placeholder text when no option selected",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:"Select an option"}}},options:{description:"Array of option objects with label, value, selected, and disabled properties",control:{type:"object"},table:{category:"Content",type:{summary:"array",required:!0}}},size:{description:"Size variant: xs (extra small), sm (small), md (default), lg (large), xl (extra large), xxl (extra extra large)",control:{type:"select"},options:["xs","sm","md","lg","xl","xxl"],table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:"md"}}},shape:{description:"Border radius: none (sharp), rounded (default 4px), or pill (fully rounded)",control:{type:"select"},options:["none","rounded","pill"],table:{category:"Appearance",type:{summary:"string"},defaultValue:{summary:"rounded"}}},disabled:{description:"Disable the dropdown (non-interactive)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:!1}}},attributes:{description:"Additional HTML attributes object",control:{type:"object"},table:{category:"Accessibility",type:{summary:"object"}}}}},s={render:e=>r(e),args:{...H}},i={render:()=>{const e={name:"shape_demo",options:[{label:"Apartment",value:"apartment",selected:!0},{label:"House",value:"house"},{label:"Loft",value:"loft"}]},o=r({...e,name:"none",label:"No radius (sharp)",shape:"none"}),t=r({...e,name:"rounded",label:"Rounded (default)",shape:"rounded"}),a=r({...e,name:"pill",label:"Pill (fully rounded)",shape:"pill"});return`
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">None (Sharp corners)</label>
          ${o}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Rounded (Default)</label>
          ${t}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Pill (Fully rounded)</label>
          ${a}
        </div>
      </div>
    `}},n={render:()=>{const e=["xs","sm","md","lg","xl","xxl"],o={options:[{label:"Apartment",value:"apartment",selected:!0},{label:"House",value:"house"},{label:"Loft",value:"loft"}]};return`
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 600px;">
        ${e.map(t=>`
          <div>
            <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600); text-transform: uppercase;">${t}</label>
            ${r({...o,name:`dropdown-${t}`,label:`Property type (${t.toUpperCase()})`,size:t})}
          </div>
        `).join("")}
      </div>
    `}},l={render:()=>`
      <div style="max-width: 400px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">
          Property type (some options disabled)
        </label>
        ${r({name:"property_disabled",label:"Property type",options:[{label:"Apartment",value:"apartment",selected:!0},{label:"House",value:"house"},{label:"Loft",value:"loft",disabled:!0},{label:"Villa",value:"villa",disabled:!0},{label:"Commercial",value:"commercial"}]})}
        <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          Try opening the dropdown - Loft and Villa options are disabled
        </p>
      </div>
    `},p={render:e=>r(e),args:{...H,label:"Disabled dropdown",disabled:!0}},d={render:()=>{const o=["France","Germany","Spain","Italy","United Kingdom","Belgium","Netherlands","Portugal","Switzerland","Austria","Sweden","Norway","Denmark","Finland","Poland","Czech Republic","Hungary","Greece","Ireland","Luxembourg"].map((a,N)=>({label:a,value:a.toLowerCase().replace(/\s+/g,"_"),selected:N===0}));return`
      <div style="max-width: 400px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">
          Country (scrollable list)
        </label>
        ${r({name:"country",label:"Select country",options:o})}
        <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          List has max-height and scroll for many options
        </p>
      </div>
    `}},u={render:()=>`
      <div style="max-width: 420px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Label</label>
        
        <div class="ps-dropdown ps-dropdown--grouped-prototype" style="position: relative; display: inline-block; width: 100%;">
          <button
            class="ps-dropdown__button"
            type="button"
            aria-haspopup="listbox"
            aria-expanded="false"
            data-grouped-dropdown-button
            style="display: inline-flex; align-items: center; justify-content: space-between; gap: var(--size-2); width: 100%; min-width: var(--ps-dropdown-min-width-medium); padding: var(--size-2) var(--size-3); border: var(--border-size-1) solid var(--gray-300); border-radius: var(--radius-2); background: var(--white); color: var(--ps-color-text); font-family: var(--ps-font-family-primary); font-size: var(--font-size-1); line-height: var(--leading-normal); cursor: pointer;"
          >
            <span style="flex: 1; text-align: left;">Placeholder</span>
            <span data-icon="chevron-down" aria-hidden="true" style="flex-shrink: 0; width: var(--ps-icon-size-20); height: var(--ps-icon-size-20); font-size: var(--ps-icon-size-20); line-height: 1;"></span>
          </button>

          <div
            style="display: none; position: absolute; z-index: var(--layer-40); top: calc(100% + var(--size-1)); left: 0; min-width: 100%; max-height: var(--size-80); overflow-y: auto; background: var(--white); border: var(--border-size-1) solid var(--gray-300); border-radius: var(--radius-2); box-shadow: var(--shadow-4); padding: var(--size-2) 0; margin: 0; list-style: none;"
          >
            <!-- Section 1 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 1
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 4</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 2 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 2
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 3 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 3
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 4 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 4
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Footer Button -->
            <div style="padding: var(--size-3) var(--size-3) var(--size-2); border-top: var(--border-size-1) solid var(--color-border-default); margin-top: var(--size-2);">
              <button type="button" style="width: 100%; padding: var(--size-3) var(--size-4); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; font-size: var(--font-size-1); font-weight: var(--font-weight-600); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">
                Apply Selection
              </button>
            </div>
          </div>
        </div>

        <p style="margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          <strong>Design mockup preview</strong>: Grouped sections with checkboxes and footer button.<br>
          This is a visual prototype of the "Search" variant shown in mockups.<br>
          <em>Future implementation will require: multiselect prop, grouped options data structure, and footer slot.</em>
        </p>
      </div>

      <script>
        // Manual toggle for this prototype story (since it doesn't use standard options structure)
        (function() {
          const button = document.querySelector('[data-grouped-dropdown-button]');
          const list = button ? button.nextElementSibling : null;
          
          if (button && list) {
            // Toggle dropdown
            button.addEventListener('click', function(e) {
              e.stopPropagation();
              const isExpanded = button.getAttribute('aria-expanded') === 'true';
              
              if (isExpanded) {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
              } else {
                list.style.display = 'block';
                button.setAttribute('aria-expanded', 'true');
              }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
              if (!button.contains(e.target) && !list.contains(e.target)) {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
              }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
              if (e.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
                button.focus();
              }
            });
          }
        })();
      <\/script>
    `},c={render:()=>{const e=r({name:"property_type",label:"Property type",options:[{label:"Apartment",value:"apartment",selected:!0},{label:"House",value:"house"},{label:"Loft",value:"loft"}]}),o=r({name:"rooms",label:"Bedrooms",options:[{label:"1 bedroom",value:"1"},{label:"2 bedrooms",value:"2",selected:!0},{label:"3 bedrooms",value:"3"},{label:"4+ bedrooms",value:"4plus"}]});return`
      <form style="max-width: 400px; display: flex; flex-direction: column; gap: var(--size-4);">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Property type</label>
          ${e}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Number of bedrooms</label>
          ${o}
        </div>
        <button type="submit" style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; transition: background-color var(--ps-transition-duration-fast) var(--ease-3);" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">
          Search properties
        </button>
      </form>
    `}};var y,g,b;s.parameters={...s.parameters,docs:{...(y=s.parameters)==null?void 0:y.docs,source:{originalSource:`{
  render: args => dropdownTwig(args),
  args: {
    ...dropdownData
  }
}`,...(b=(g=s.parameters)==null?void 0:g.docs)==null?void 0:b.source}}};var m,h,w;i.parameters={...i.parameters,docs:{...(m=i.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: () => {
    const baseData = {
      name: 'shape_demo',
      options: [{
        label: 'Apartment',
        value: 'apartment',
        selected: true
      }, {
        label: 'House',
        value: 'house'
      }, {
        label: 'Loft',
        value: 'loft'
      }]
    };
    const none = dropdownTwig({
      ...baseData,
      name: 'none',
      label: 'No radius (sharp)',
      shape: 'none'
    });
    const rounded = dropdownTwig({
      ...baseData,
      name: 'rounded',
      label: 'Rounded (default)',
      shape: 'rounded'
    });
    const pill = dropdownTwig({
      ...baseData,
      name: 'pill',
      label: 'Pill (fully rounded)',
      shape: 'pill'
    });
    return \`
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 400px;">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">None (Sharp corners)</label>
          \${none}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Rounded (Default)</label>
          \${rounded}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Pill (Fully rounded)</label>
          \${pill}
        </div>
      </div>
    \`;
  }
}`,...(w=(h=i.parameters)==null?void 0:h.docs)==null?void 0:w.source}}};var f,x,z;n.parameters={...n.parameters,docs:{...(f=n.parameters)==null?void 0:f.docs,source:{originalSource:`{
  render: () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];
    const baseData = {
      options: [{
        label: 'Apartment',
        value: 'apartment',
        selected: true
      }, {
        label: 'House',
        value: 'house'
      }, {
        label: 'Loft',
        value: 'loft'
      }]
    };
    return \`
      <div style="display: flex; flex-direction: column; gap: var(--size-6); max-width: 600px;">
        \${sizes.map(size => \`
          <div>
            <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600); text-transform: uppercase;">\${size}</label>
            \${dropdownTwig({
      ...baseData,
      name: \`dropdown-\${size}\`,
      label: \`Property type (\${size.toUpperCase()})\`,
      size
    })}
          </div>
        \`).join('')}
      </div>
    \`;
  }
}`,...(z=(x=n.parameters)==null?void 0:x.docs)==null?void 0:z.source}}};var k,T,C;l.parameters={...l.parameters,docs:{...(k=l.parameters)==null?void 0:k.docs,source:{originalSource:`{
  render: () => {
    const dropdown = dropdownTwig({
      name: 'property_disabled',
      label: 'Property type',
      options: [{
        label: 'Apartment',
        value: 'apartment',
        selected: true
      }, {
        label: 'House',
        value: 'house'
      }, {
        label: 'Loft',
        value: 'loft',
        disabled: true
      }, {
        label: 'Villa',
        value: 'villa',
        disabled: true
      }, {
        label: 'Commercial',
        value: 'commercial'
      }]
    });
    return \`
      <div style="max-width: 400px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">
          Property type (some options disabled)
        </label>
        \${dropdown}
        <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          Try opening the dropdown - Loft and Villa options are disabled
        </p>
      </div>
    \`;
  }
}`,...(C=(T=l.parameters)==null?void 0:T.docs)==null?void 0:C.source}}};var S,A,D;p.parameters={...p.parameters,docs:{...(S=p.parameters)==null?void 0:S.docs,source:{originalSource:`{
  render: args => dropdownTwig(args),
  args: {
    ...dropdownData,
    label: 'Disabled dropdown',
    disabled: true
  }
}`,...(D=(A=p.parameters)==null?void 0:A.docs)==null?void 0:D.source}}};var L,I,_;d.parameters={...d.parameters,docs:{...(L=d.parameters)==null?void 0:L.docs,source:{originalSource:`{
  render: () => {
    const countries = ['France', 'Germany', 'Spain', 'Italy', 'United Kingdom', 'Belgium', 'Netherlands', 'Portugal', 'Switzerland', 'Austria', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Poland', 'Czech Republic', 'Hungary', 'Greece', 'Ireland', 'Luxembourg'];
    const options = countries.map((country, index) => ({
      label: country,
      value: country.toLowerCase().replace(/\\s+/g, '_'),
      selected: index === 0
    }));
    const dropdown = dropdownTwig({
      name: 'country',
      label: 'Select country',
      options
    });
    return \`
      <div style="max-width: 400px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">
          Country (scrollable list)
        </label>
        \${dropdown}
        <p style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          List has max-height and scroll for many options
        </p>
      </div>
    \`;
  }
}`,...(_=(I=d.parameters)==null?void 0:I.docs)==null?void 0:_.source}}};var P,$,E;u.parameters={...u.parameters,docs:{...(P=u.parameters)==null?void 0:P.docs,source:{originalSource:`{
  render: () => {
    return \`
      <div style="max-width: 420px;">
        <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Label</label>
        
        <div class="ps-dropdown ps-dropdown--grouped-prototype" style="position: relative; display: inline-block; width: 100%;">
          <button
            class="ps-dropdown__button"
            type="button"
            aria-haspopup="listbox"
            aria-expanded="false"
            data-grouped-dropdown-button
            style="display: inline-flex; align-items: center; justify-content: space-between; gap: var(--size-2); width: 100%; min-width: var(--ps-dropdown-min-width-medium); padding: var(--size-2) var(--size-3); border: var(--border-size-1) solid var(--gray-300); border-radius: var(--radius-2); background: var(--white); color: var(--ps-color-text); font-family: var(--ps-font-family-primary); font-size: var(--font-size-1); line-height: var(--leading-normal); cursor: pointer;"
          >
            <span style="flex: 1; text-align: left;">Placeholder</span>
            <span data-icon="chevron-down" aria-hidden="true" style="flex-shrink: 0; width: var(--ps-icon-size-20); height: var(--ps-icon-size-20); font-size: var(--ps-icon-size-20); line-height: 1;"></span>
          </button>

          <div
            style="display: none; position: absolute; z-index: var(--layer-40); top: calc(100% + var(--size-1)); left: 0; min-width: 100%; max-height: var(--size-80); overflow-y: auto; background: var(--white); border: var(--border-size-1) solid var(--gray-300); border-radius: var(--radius-2); box-shadow: var(--shadow-4); padding: var(--size-2) 0; margin: 0; list-style: none;"
          >
            <!-- Section 1 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 1
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 4</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 2 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 2
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 3 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 3
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Divider -->
            <div style="height: var(--border-size-1); background: var(--color-border-default); margin: var(--size-2) 0;"></div>

            <!-- Section 4 -->
            <div style="padding: var(--size-2) var(--size-3); font-weight: var(--font-weight-600); font-size: var(--font-size-0); color: var(--ps-color-text-muted); text-transform: uppercase;">
              Section 4
            </div>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 1</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 2</span>
            </label>
            <label style="display: flex; align-items: center; gap: var(--size-2); padding: var(--size-2) var(--size-3) var(--size-2) var(--size-6); cursor: pointer; color: var(--ps-color-text); font-size: var(--font-size-1); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--color-bg-subtle)'" onmouseout="this.style.backgroundColor='transparent'">
              <input type="checkbox" style="width: var(--size-4); height: var(--size-4); cursor: pointer; flex-shrink: 0;">
              <span>Item 3</span>
            </label>

            <!-- Footer Button -->
            <div style="padding: var(--size-3) var(--size-3) var(--size-2); border-top: var(--border-size-1) solid var(--color-border-default); margin-top: var(--size-2);">
              <button type="button" style="width: 100%; padding: var(--size-3) var(--size-4); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; font-size: var(--font-size-1); font-weight: var(--font-weight-600); transition: background-color var(--duration-200) var(--ease-in-out);" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">
                Apply Selection
              </button>
            </div>
          </div>
        </div>

        <p style="margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--ps-color-text-muted);">
          <strong>Design mockup preview</strong>: Grouped sections with checkboxes and footer button.<br>
          This is a visual prototype of the "Search" variant shown in mockups.<br>
          <em>Future implementation will require: multiselect prop, grouped options data structure, and footer slot.</em>
        </p>
      </div>

      <script>
        // Manual toggle for this prototype story (since it doesn't use standard options structure)
        (function() {
          const button = document.querySelector('[data-grouped-dropdown-button]');
          const list = button ? button.nextElementSibling : null;
          
          if (button && list) {
            // Toggle dropdown
            button.addEventListener('click', function(e) {
              e.stopPropagation();
              const isExpanded = button.getAttribute('aria-expanded') === 'true';
              
              if (isExpanded) {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
              } else {
                list.style.display = 'block';
                button.setAttribute('aria-expanded', 'true');
              }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
              if (!button.contains(e.target) && !list.contains(e.target)) {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
              }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
              if (e.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                list.style.display = 'none';
                button.setAttribute('aria-expanded', 'false');
                button.focus();
              }
            });
          }
        })();
      <\/script>
    \`;
  }
}`,...(E=($=u.parameters)==null?void 0:$.docs)==null?void 0:E.source}}};var V,j,F;c.parameters={...c.parameters,docs:{...(V=c.parameters)==null?void 0:V.docs,source:{originalSource:`{
  render: () => {
    const typeDropdown = dropdownTwig({
      name: 'property_type',
      label: 'Property type',
      options: [{
        label: 'Apartment',
        value: 'apartment',
        selected: true
      }, {
        label: 'House',
        value: 'house'
      }, {
        label: 'Loft',
        value: 'loft'
      }]
    });
    const roomsDropdown = dropdownTwig({
      name: 'rooms',
      label: 'Bedrooms',
      options: [{
        label: '1 bedroom',
        value: '1'
      }, {
        label: '2 bedrooms',
        value: '2',
        selected: true
      }, {
        label: '3 bedrooms',
        value: '3'
      }, {
        label: '4+ bedrooms',
        value: '4plus'
      }]
    });
    return \`
      <form style="max-width: 400px; display: flex; flex-direction: column; gap: var(--size-4);">
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Property type</label>
          \${typeDropdown}
        </div>
        <div>
          <label style="display: block; margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Number of bedrooms</label>
          \${roomsDropdown}
        </div>
        <button type="submit" style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; transition: background-color var(--ps-transition-duration-fast) var(--ease-3);" onmouseover="this.style.backgroundColor='var(--primary-hover)'" onmouseout="this.style.backgroundColor='var(--primary)'">
          Search properties
        </button>
      </form>
    \`;
  }
}`,...(F=(j=c.parameters)==null?void 0:j.docs)==null?void 0:F.source}}};const W=["Default","AllShapes","AllSizes","WithDisabledOptions","DisabledDropdown","LongList","GroupedWithCheckboxes","InForm"];export{i as AllShapes,n as AllSizes,s as Default,p as DisabledDropdown,u as GroupedWithCheckboxes,c as InForm,d as LongList,l as WithDisabledOptions,W as __namedExportsOrder,U as default};
