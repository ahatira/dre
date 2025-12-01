import{t as q,T as H}from"./iframe-BXCbAV1K.js";import{D as J,a as G}from"./twig-CSYqopkt.js";G(H);H.cache(!1);const d=t=>t,e=(t={})=>{const u=q.twig({id:"C:/wamp64/www/ps_theme/source/patterns/components/alert/alert.twig",data:[{type:"raw",value:"",position:{start:721,end:725}},{type:"logic",token:{type:"Twig.logic.type.set",key:"variant",expression:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:"info"},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:725,end:770}},position:{start:725,end:770}},{type:"raw",value:"",position:{start:770,end:772}},{type:"logic",token:{type:"Twig.logic.type.set",key:"icon",expression:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.test",filter:"defined"},{type:"Twig.expression.type.variable",value:"icon",match:["icon"]},{type:"Twig.expression.type.bool",value:!0},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:772,end:820}},position:{start:772,end:820}},{type:"raw",value:"",position:{start:820,end:822}},{type:"logic",token:{type:"Twig.logic.type.set",key:"dismissible",expression:[{type:"Twig.expression.type.variable",value:"dismissible",match:["dismissible"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:822,end:874}},position:{start:822,end:874}},{type:"raw",value:"",position:{start:874,end:876}},{type:"logic",token:{type:"Twig.logic.type.set",key:"compact",expression:[{type:"Twig.expression.type.variable",value:"compact",match:["compact"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.bool",value:!1},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:876,end:920}},position:{start:876,end:920}},{type:"raw",value:"",position:{start:920,end:922}},{type:"logic",token:{type:"Twig.logic.type.set",key:"title",expression:[{type:"Twig.expression.type.variable",value:"title",match:["title"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:922,end:959}},position:{start:922,end:959}},{type:"raw",value:"",position:{start:959,end:961}},{type:"logic",token:{type:"Twig.logic.type.set",key:"message",expression:[{type:"Twig.expression.type.variable",value:"message",match:["message"]},{type:"Twig.expression.type.filter",value:"default",match:["|default","default"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.string",value:""},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:961,end:1002}},position:{start:961,end:1002}},{type:"raw",value:"",position:{start:1002,end:1006}},{type:"output",position:{start:1006,end:1044},stack:[{type:"Twig.expression.type._function",position:{start:1006,end:1044},fn:"attach_library",params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:1006,end:1044}},{type:"Twig.expression.type.string",value:"ps_theme/alert",position:{start:1006,end:1044}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:1006,end:1044},expression:!1}]}]},{type:"raw",value:`\r
\r
`,position:{start:1044,end:1048}},{type:"raw",value:"",position:{start:1078,end:1080}},{type:"logic",token:{type:"Twig.logic.type.set",key:"iconMap",expression:[{type:"Twig.expression.type.object.start",value:"{",match:["{"]},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"info"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"success"},{type:"Twig.expression.type.string",value:"check"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"warning"},{type:"Twig.expression.type.string",value:"help"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"error"},{type:"Twig.expression.type.string",value:"close"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"info-subtle"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"success-subtle"},{type:"Twig.expression.type.string",value:"check"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"warning-subtle"},{type:"Twig.expression.type.string",value:"help"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"error-subtle"},{type:"Twig.expression.type.string",value:"close"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"default"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"primary"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"secondary"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"light"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.comma"},{type:"Twig.expression.type.operator.binary",value:":",precidence:16,associativity:"rightToLeft",operator:":",key:"dark"},{type:"Twig.expression.type.string",value:"infos"},{type:"Twig.expression.type.object.end",value:"}",match:["}"]}],position:{start:1080,end:1417}},position:{start:1080,end:1417}},{type:"raw",value:"",position:{start:1417,end:1419}},{type:"logic",token:{type:"Twig.logic.type.set",key:"iconName",expression:[{type:"Twig.expression.type.variable",value:"iconMap",match:["iconMap"]},{type:"Twig.expression.type.key.brackets",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]}]}],position:{start:1419,end:1458}},position:{start:1419,end:1458}},{type:"raw",value:"",position:{start:1458,end:1462}},{type:"raw",value:"",position:{start:1493,end:1495}},{type:"logic",token:{type:"Twig.logic.type.set",key:"role",expression:[{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"error"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"error-subtle"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"or",precidence:14,associativity:"leftToRight",operator:"or"}]},{type:"Twig.expression.type.string",value:"alert"},{type:"Twig.expression.type.string",value:"status"},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:1495,end:1584}},position:{start:1495,end:1584}},{type:"raw",value:"",position:{start:1584,end:1586}},{type:"logic",token:{type:"Twig.logic.type.set",key:"ariaLive",expression:[{type:"Twig.expression.type.subexpression.end",value:")",match:[")"],expression:!0,params:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"error"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"error-subtle"},{type:"Twig.expression.type.operator.binary",value:"==",precidence:9,associativity:"leftToRight",operator:"=="},{type:"Twig.expression.type.operator.binary",value:"or",precidence:14,associativity:"leftToRight",operator:"or"}]},{type:"Twig.expression.type.string",value:"assertive"},{type:"Twig.expression.type.string",value:"polite"},{type:"Twig.expression.type.operator.binary",value:"?",precidence:16,associativity:"rightToLeft",operator:"?"}],position:{start:1586,end:1683}},position:{start:1586,end:1683}},{type:"raw",value:"",position:{start:1683,end:1687}},{type:"raw",value:"",position:{start:1748,end:1750}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-alert"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]}],position:{start:1750,end:1784}},position:{start:1750,end:1784}},{type:"raw",value:"",position:{start:1784,end:1786}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.string",value:"info"},{type:"Twig.expression.type.operator.binary",value:"!=",precidence:9,associativity:"leftToRight",operator:"!="}],position:{start:1786,end:1814},output:[{type:"raw",value:"",position:{start:1814,end:1818}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-alert--"},{type:"Twig.expression.type.variable",value:"variant",match:["variant"]},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1818,end:1879}},position:{start:1818,end:1879}},{type:"raw",value:"",position:{start:1879,end:1881}}]},position:{open:{start:1786,end:1814},close:{start:1881,end:1894}}},{type:"raw",value:"",position:{start:1894,end:1896}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"dismissible",match:["dismissible"]}],position:{start:1896,end:1918},output:[{type:"raw",value:"",position:{start:1918,end:1922}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-alert--dismissible"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:1922,end:1984}},position:{start:1922,end:1984}},{type:"raw",value:"",position:{start:1984,end:1986}}]},position:{open:{start:1896,end:1918},close:{start:1986,end:1999}}},{type:"raw",value:"",position:{start:1999,end:2001}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"compact",match:["compact"]}],position:{start:2001,end:2019},output:[{type:"raw",value:"",position:{start:2019,end:2023}},{type:"logic",token:{type:"Twig.logic.type.set",key:"classes",expression:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"]},{type:"Twig.expression.type.filter",value:"merge",match:["|merge","merge"],params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("]},{type:"Twig.expression.type.array.start",value:"[",match:["["]},{type:"Twig.expression.type.string",value:"ps-alert--compact"},{type:"Twig.expression.type.array.end",value:"]",match:["]"]},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],expression:!1}]}],position:{start:2023,end:2081}},position:{start:2023,end:2081}},{type:"raw",value:"",position:{start:2081,end:2083}}]},position:{open:{start:2001,end:2019},close:{start:2083,end:2096}}},{type:"raw",value:`<div\r
  class="`,position:{start:2096,end:2115}},{type:"output",position:{start:2115,end:2143},stack:[{type:"Twig.expression.type.variable",value:"classes",match:["classes"],position:{start:2115,end:2143}},{type:"Twig.expression.type.filter",value:"join",match:["|join","join"],position:{start:2115,end:2143},params:[{type:"Twig.expression.type.parameter.start",value:"(",match:["("],position:{start:2115,end:2143}},{type:"Twig.expression.type.string",value:" ",position:{start:2115,end:2143}},{type:"Twig.expression.type.parameter.end",value:")",match:[")"],position:{start:2115,end:2143},expression:!1}]},{type:"Twig.expression.type.filter",value:"trim",match:["|trim","trim"],position:{start:2115,end:2143}}]},{type:"raw",value:`"\r
  role="`,position:{start:2143,end:2154}},{type:"output",position:{start:2154,end:2164},stack:[{type:"Twig.expression.type.variable",value:"role",match:["role"],position:{start:2154,end:2164}}]},{type:"raw",value:`"\r
  aria-live="`,position:{start:2164,end:2180}},{type:"output",position:{start:2180,end:2194},stack:[{type:"Twig.expression.type.variable",value:"ariaLive",match:["ariaLive"],position:{start:2180,end:2194}}]},{type:"raw",value:`"\r
  `,position:{start:2194,end:2199}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"]}],position:{start:2199,end:2218},output:[{type:"output",position:{start:2218,end:2234},stack:[{type:"Twig.expression.type.variable",value:"attributes",match:["attributes"],position:{start:2218,end:2234}}]}]},position:{open:{start:2199,end:2218},close:{start:2234,end:2245}}},{type:"raw",value:`\r
>`,position:{start:2245,end:2252}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"icon",match:["icon"]}],position:{start:2252,end:2267},output:[{type:"raw",value:'<span class="ps-alert__icon" data-icon="',position:{start:2267,end:2313}},{type:"output",position:{start:2313,end:2327},stack:[{type:"Twig.expression.type.variable",value:"iconName",match:["iconName"],position:{start:2313,end:2327}}]},{type:"raw",value:'" aria-hidden="true"></span>',position:{start:2327,end:2359}}]},position:{open:{start:2252,end:2267},close:{start:2359,end:2372}}},{type:"raw",value:'<div class="ps-alert__content">',position:{start:2372,end:2415}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"]}],position:{start:2415,end:2431},output:[{type:"raw",value:'<h3 class="ps-alert__title">',position:{start:2431,end:2467}},{type:"output",position:{start:2467,end:2478},stack:[{type:"Twig.expression.type.variable",value:"title",match:["title"],position:{start:2467,end:2478}}]},{type:"raw",value:"</h3>",position:{start:2478,end:2489}}]},position:{open:{start:2415,end:2431},close:{start:2489,end:2502}}},{type:"raw",value:'<div class="ps-alert__message">',position:{start:2502,end:2539}},{type:"output",position:{start:2539,end:2556},stack:[{type:"Twig.expression.type.variable",value:"message",match:["message"],position:{start:2539,end:2556}},{type:"Twig.expression.type.filter",value:"raw",match:["|raw","raw"],position:{start:2539,end:2556}}]},{type:"raw",value:`</div>\r
  </div>`,position:{start:2556,end:2578}},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"dismissible",match:["dismissible"]}],position:{start:2578,end:2600},output:[{type:"raw",value:`<button\r
      class="ps-alert__close"\r
      type="button"\r
      aria-label="Close alert"\r
    >\r
      <span aria-hidden="true">&times;</span>\r
    </button>`,position:{start:2600,end:2770}}]},position:{open:{start:2578,end:2600},close:{start:2770,end:2783}}},{type:"raw",value:`</div>\r
`,position:{start:2783,end:2783}}],precompiled:!0});u.options.allowInlineIncludes=!0;try{let a=t.defaultAttributes?t.defaultAttributes:[];return Array.isArray(a)||(a=Object.entries(a)),d(u.render({attributes:new J(a),...t}))}catch(a){return d("An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/alert/alert.twig: "+a.toString())}},F={variant:"info",title:"Information",message:"This is an informational alert message.",icon:!0,dismissible:!1,compact:!1},X={title:"Components/Alert",tags:["autodocs"],render:t=>e(t),args:F,parameters:{docs:{description:{component:"Semantic alert component for status messages with accessible roles and tokenized design."}}},argTypes:{variant:{description:"Semantic variant of the alert",control:{type:"select"},options:["info","success","warning","error","info-subtle","success-subtle","warning-subtle","error-subtle","default","primary","secondary","light","dark"],table:{category:"Appearance",type:{summary:"info | success | warning | error | info-subtle | success-subtle | warning-subtle | error-subtle | default | primary | secondary | light | dark"},defaultValue:{summary:"info"}}},title:{description:"Optional title text",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:'""'}}},message:{description:"Message content (supports HTML)",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:'""'}}},icon:{description:"Show icon (icons: info=infos, success=check, warning=help, error=close)",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:"true"}}},dismissible:{description:"Show close button with JavaScript dismiss behavior",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},compact:{description:"Reduced padding for dense layouts",control:{type:"boolean"},table:{category:"Appearance",type:{summary:"boolean"},defaultValue:{summary:"false"}}}}},i={render:t=>e(t),args:{...F}},s={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"info",title:"Information",message:"This is an informational alert message."})}
      ${e({variant:"success",title:"Success",message:"Operation completed successfully."})}
      ${e({variant:"warning",title:"Warning",message:"Please review your input before continuing."})}
      ${e({variant:"error",title:"Error",message:"An error occurred. Please try again."})}
      ${e({variant:"default",title:"Default",message:"Neutral alert for general messages."})}
      ${e({variant:"primary",title:"Primary",message:"Brand primary green alert."})}
      ${e({variant:"secondary",title:"Secondary",message:"Brand secondary pink alert."})}
      ${e({variant:"light",title:"Light",message:"Light background for subtle alerts."})}
      ${e({variant:"dark",title:"Dark",message:"Dark background with light text."})}
    </div>
  `},r={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"info-subtle",title:"Information",message:"Subtle info alert with soft background and colored text."})}
      ${e({variant:"success-subtle",title:"Success",message:"Subtle success alert for gentle confirmation."})}
      ${e({variant:"warning-subtle",title:"Warning",message:"Subtle warning with light background and accent border."})}
      ${e({variant:"error-subtle",title:"Error",message:"You can't compare more than 4 ads at the same time. Delete one to add this one."})}
    </div>
  `},n={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"info",message:"Simple info message without title."})}
      ${e({variant:"success",message:"Operation successful!"})}
      ${e({variant:"warning",message:"Please verify your email address."})}
      ${e({variant:"error",message:"Connection lost. Reconnecting..."})}
    </div>
  `},o={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"info",title:"Information",message:"Alert without icon.",icon:!1})}
      ${e({variant:"success",message:"Text-only success message.",icon:!1})}
      ${e({variant:"warning",message:"Warning without visual icon.",icon:!1})}
      ${e({variant:"error",title:"Error",message:"Error message, no icon.",icon:!1})}
    </div>
  `},p={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"primary",title:"Primary Brand",message:"BNP Paribas RealEstate primary green."})}
      ${e({variant:"secondary",title:"Secondary Brand",message:"BNP Paribas RealEstate accent pink."})}
      ${e({variant:"light",message:"Light subtle variant for minimal alerts."})}
      ${e({variant:"dark",message:"Dark variant with light text for contrasting sections."})}
    </div>
  `},l={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"info",title:"Tip",message:"You can dismiss this message.",dismissible:!0})}
      ${e({variant:"success",message:"Changes saved successfully.",dismissible:!0})}
      ${e({variant:"warning",message:"Low disk space available.",dismissible:!0})}
    </div>
  `},c={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-3); max-width: 320px;">
      ${e({variant:"info",message:"Compact info alert for sidebars.",compact:!0})}
      ${e({variant:"success",message:"Saved!",compact:!0,dismissible:!0})}
      ${e({variant:"warning",message:"Low battery.",compact:!0})}
      ${e({variant:"error",message:"Error!",compact:!0})}
    </div>
  `},y={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({variant:"info",title:"Update Available",message:'<p>A new version is available. <a href="#">Download now</a> to get the latest features.</p>'})}
      ${e({variant:"success",message:"<p><strong>Payment received!</strong></p><p>Your order will be processed within 24 hours.</p>"})}
      ${e({variant:"warning",title:"Action Required",message:'<p>Your subscription expires in 3 days.</p><p><a href="#">Renew now</a> to avoid interruption.</p>',dismissible:!0})}
    </div>
  `},g={render:()=>`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Submission Success</h3>
        ${e({variant:"success",title:"Form Submitted",message:"Thank you for your submission. We will review your application and contact you within 5 business days.",dismissible:!0})}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Session Expiration Warning</h3>
        ${e({variant:"warning",title:"Session Expiring Soon",message:"Your session will expire in 2 minutes due to inactivity. Click anywhere to stay logged in."})}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">System Error</h3>
        ${e({variant:"error",title:"Connection Error",message:"Unable to connect to the server. Please check your internet connection and try again."})}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Inline Help (Compact)</h3>
        <div style="max-width: 400px;">
          ${e({variant:"info",message:"Pro tip: Use keyboard shortcuts to navigate faster.",compact:!0,dismissible:!0})}
        </div>
      </div>
    </div>
  `};var m,v,w;i.parameters={...i.parameters,docs:{...(m=i.parameters)==null?void 0:m.docs,source:{originalSource:`{
  render: args => alertTwig(args),
  args: {
    ...data
  }
}`,...(w=(v=i.parameters)==null?void 0:v.docs)==null?void 0:w.source}}};var T,f,x;s.parameters={...s.parameters,docs:{...(T=s.parameters)==null?void 0:T.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'info',
    title: 'Information',
    message: 'This is an informational alert message.'
  })}
      \${alertTwig({
    variant: 'success',
    title: 'Success',
    message: 'Operation completed successfully.'
  })}
      \${alertTwig({
    variant: 'warning',
    title: 'Warning',
    message: 'Please review your input before continuing.'
  })}
      \${alertTwig({
    variant: 'error',
    title: 'Error',
    message: 'An error occurred. Please try again.'
  })}
      \${alertTwig({
    variant: 'default',
    title: 'Default',
    message: 'Neutral alert for general messages.'
  })}
      \${alertTwig({
    variant: 'primary',
    title: 'Primary',
    message: 'Brand primary green alert.'
  })}
      \${alertTwig({
    variant: 'secondary',
    title: 'Secondary',
    message: 'Brand secondary pink alert.'
  })}
      \${alertTwig({
    variant: 'light',
    title: 'Light',
    message: 'Light background for subtle alerts.'
  })}
      \${alertTwig({
    variant: 'dark',
    title: 'Dark',
    message: 'Dark background with light text.'
  })}
    </div>
  \`
}`,...(x=(f=s.parameters)==null?void 0:f.docs)==null?void 0:x.source}}};var h,b,k;r.parameters={...r.parameters,docs:{...(h=r.parameters)==null?void 0:h.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'info-subtle',
    title: 'Information',
    message: 'Subtle info alert with soft background and colored text.'
  })}
      \${alertTwig({
    variant: 'success-subtle',
    title: 'Success',
    message: 'Subtle success alert for gentle confirmation.'
  })}
      \${alertTwig({
    variant: 'warning-subtle',
    title: 'Warning',
    message: 'Subtle warning with light background and accent border.'
  })}
      \${alertTwig({
    variant: 'error-subtle',
    title: 'Error',
    message: "You can't compare more than 4 ads at the same time. Delete one to add this one."
  })}
    </div>
  \`
}`,...(k=(b=r.parameters)==null?void 0:b.docs)==null?void 0:k.source}}};var $,S,z;n.parameters={...n.parameters,docs:{...($=n.parameters)==null?void 0:$.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'info',
    message: 'Simple info message without title.'
  })}
      \${alertTwig({
    variant: 'success',
    message: 'Operation successful!'
  })}
      \${alertTwig({
    variant: 'warning',
    message: 'Please verify your email address.'
  })}
      \${alertTwig({
    variant: 'error',
    message: 'Connection lost. Reconnecting...'
  })}
    </div>
  \`
}`,...(z=(S=n.parameters)==null?void 0:S.docs)==null?void 0:z.source}}};var L,E,C;o.parameters={...o.parameters,docs:{...(L=o.parameters)==null?void 0:L.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'info',
    title: 'Information',
    message: 'Alert without icon.',
    icon: false
  })}
      \${alertTwig({
    variant: 'success',
    message: 'Text-only success message.',
    icon: false
  })}
      \${alertTwig({
    variant: 'warning',
    message: 'Warning without visual icon.',
    icon: false
  })}
      \${alertTwig({
    variant: 'error',
    title: 'Error',
    message: 'Error message, no icon.',
    icon: false
  })}
    </div>
  \`
}`,...(C=(E=o.parameters)==null?void 0:E.docs)==null?void 0:C.source}}};var P,A,D;p.parameters={...p.parameters,docs:{...(P=p.parameters)==null?void 0:P.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'primary',
    title: 'Primary Brand',
    message: 'BNP Paribas RealEstate primary green.'
  })}
      \${alertTwig({
    variant: 'secondary',
    title: 'Secondary Brand',
    message: 'BNP Paribas RealEstate accent pink.'
  })}
      \${alertTwig({
    variant: 'light',
    message: 'Light subtle variant for minimal alerts.'
  })}
      \${alertTwig({
    variant: 'dark',
    message: 'Dark variant with light text for contrasting sections.'
  })}
    </div>
  \`
}`,...(D=(A=p.parameters)==null?void 0:A.docs)==null?void 0:D.source}}};var R,_,W;l.parameters={...l.parameters,docs:{...(R=l.parameters)==null?void 0:R.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'info',
    title: 'Tip',
    message: 'You can dismiss this message.',
    dismissible: true
  })}
      \${alertTwig({
    variant: 'success',
    message: 'Changes saved successfully.',
    dismissible: true
  })}
      \${alertTwig({
    variant: 'warning',
    message: 'Low disk space available.',
    dismissible: true
  })}
    </div>
  \`
}`,...(W=(_=l.parameters)==null?void 0:_.docs)==null?void 0:W.source}}};var B,I,V;c.parameters={...c.parameters,docs:{...(B=c.parameters)==null?void 0:B.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-3); max-width: 320px;">
      \${alertTwig({
    variant: 'info',
    message: 'Compact info alert for sidebars.',
    compact: true
  })}
      \${alertTwig({
    variant: 'success',
    message: 'Saved!',
    compact: true,
    dismissible: true
  })}
      \${alertTwig({
    variant: 'warning',
    message: 'Low battery.',
    compact: true
  })}
      \${alertTwig({
    variant: 'error',
    message: 'Error!',
    compact: true
  })}
    </div>
  \`
}`,...(V=(I=c.parameters)==null?void 0:I.docs)==null?void 0:V.source}}};var Y,N,U;y.parameters={...y.parameters,docs:{...(Y=y.parameters)==null?void 0:Y.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'info',
    title: 'Update Available',
    message: '<p>A new version is available. <a href="#">Download now</a> to get the latest features.</p>'
  })}
      \${alertTwig({
    variant: 'success',
    message: '<p><strong>Payment received!</strong></p><p>Your order will be processed within 24 hours.</p>'
  })}
      \${alertTwig({
    variant: 'warning',
    title: 'Action Required',
    message: '<p>Your subscription expires in 3 days.</p><p><a href="#">Renew now</a> to avoid interruption.</p>',
    dismissible: true
  })}
    </div>
  \`
}`,...(U=(N=y.parameters)==null?void 0:N.docs)==null?void 0:U.source}}};var M,O,j;g.parameters={...g.parameters,docs:{...(M=g.parameters)==null?void 0:M.docs,source:{originalSource:`{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Form Submission Success</h3>
        \${alertTwig({
    variant: 'success',
    title: 'Form Submitted',
    message: 'Thank you for your submission. We will review your application and contact you within 5 business days.',
    dismissible: true
  })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Session Expiration Warning</h3>
        \${alertTwig({
    variant: 'warning',
    title: 'Session Expiring Soon',
    message: 'Your session will expire in 2 minutes due to inactivity. Click anywhere to stay logged in.'
  })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">System Error</h3>
        \${alertTwig({
    variant: 'error',
    title: 'Connection Error',
    message: 'Unable to connect to the server. Please check your internet connection and try again.'
  })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Inline Help (Compact)</h3>
        <div style="max-width: 400px;">
          \${alertTwig({
    variant: 'info',
    message: 'Pro tip: Use keyboard shortcuts to navigate faster.',
    compact: true,
    dismissible: true
  })}
        </div>
      </div>
    </div>
  \`
}`,...(j=(O=g.parameters)==null?void 0:O.docs)==null?void 0:j.source}}};const Z=["Default","AllVariants","SubtleVariants","WithoutTitle","WithoutIcon","BrandColors","Dismissible","Compact","WithHTML","UseCases"];export{s as AllVariants,p as BrandColors,c as Compact,i as Default,l as Dismissible,r as SubtleVariants,g as UseCases,y as WithHTML,o as WithoutIcon,n as WithoutTitle,Z as __namedExportsOrder,X as default};
