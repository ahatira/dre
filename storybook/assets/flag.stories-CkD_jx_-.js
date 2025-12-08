import{f as p}from"./flag-CHmIoRiM.js";import"./iframe-B-yX16js.js";import"./twig-CgICq6Dc.js";const M=["AD","AE","AF","AG","AI","AL","AM","AO","AQ","AR","ARAB","AS","ASEAN","AT","AU","AW","AX","AZ","BA","BB","BD","BE","BF","BG","BH","BI","BJ","BL","BM","BN","BO","BQ","BR","BS","BT","BV","BW","BY","BZ","CA","CC","CD","CEFTA","CF","CG","CH","CI","CK","CL","CM","CN","CO","CP","CR","CU","CV","CW","CX","CY","CZ","DE","DG","DJ","DK","DM","DO","DZ","EAC","EC","EE","EG","EH","ER","ES","ES-CT","ES-GA","ES-PV","ET","EU","FI","FJ","FK","FM","FO","FR","GA","GB","GB-ENG","GB-NIR","GB-SCT","GB-WLS","GD","GE","GF","GG","GH","GI","GL","GM","GN","GP","GQ","GR","GS","GT","GU","GW","GY","HK","HM","HN","HR","HT","HU","IC","ID","IE","IL","IM","IN","IO","IQ","IR","IS","IT","JE","JM","JO","JP","KE","KG","KH","KI","KM","KN","KP","KR","KW","KY","KZ","LA","LB","LC","LI","LK","LR","LS","LT","LU","LV","LY","MA","MC","MD","ME","MF","MG","MH","MK","ML","MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV","MW","MX","MY","MZ","NA","NC","NE","NF","NG","NI","NL","NO","NP","NR","NU","NZ","OM","PA","PC","PE","PF","PG","PH","PK","PL","PM","PN","PR","PS","PT","PW","PY","QA","RE","RO","RS","RU","RW","SA","SB","SC","SD","SE","SG","SH","SH-AC","SH-HL","SH-TA","SI","SJ","SK","SL","SM","SN","SO","SR","SS","ST","SV","SX","SY","SZ","TC","TD","TF","TG","TH","TJ","TK","TL","TM","TN","TO","TR","TT","TV","TW","TZ","UA","UG","UM","UN","US","UY","UZ","VA","VC","VE","VG","VI","VN","VU","WF","WS","XK","XX","YE","YT","ZA","ZM","ZW"],B={code:"FR",label:"France",size:"md",shape:"square",disabled:!1,decorative:!1},b={title:"Elements/Flag",tags:["autodocs"],parameters:{docs:{description:{component:"Visual indicator for country/language using flag images. Supports ISO 3166-1 alpha-2 codes (FR, GB, DE) or BCP 47 locale tags (fr-FR, en-GB)."}}},argTypes:{code:{description:"Country code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL)",control:{type:"select"},options:["FR","GB","DE","ES","IT","NL","IE","PL"],table:{category:"Content",type:{summary:"string"},defaultValue:{summary:"FR"}}},locale:{description:"BCP 47 locale tag (ex: fr-FR, en-GB). If provided, derives country code.",control:{type:"select"},options:["fr-FR","en-GB","de-DE","es-ES","it-IT","nl-NL"],table:{category:"Content",type:{summary:"string"},defaultValue:{summary:""}}},src:{description:"Explicit flag image path (overrides automatic /flags/{code}.svg path)",control:{type:"text"},table:{category:"Content",type:{summary:"string"},defaultValue:{summary:""}}},size:{description:"Flag size (xs: 12px, sm: 16px, md: 20px, lg: 24px, xl: 48px)",control:{type:"select"},options:["xs","sm","md","lg","xl"],table:{category:"Appearance",type:{summary:"xs | sm | md | lg | xl"},defaultValue:{summary:"md"}}},shape:{description:"Flag shape (square: 4:3 ratio, rounded: 4:3 with 4px radius, circle: 1:1 ratio)",control:{type:"select"},options:["square","rounded","circle"],table:{category:"Appearance",type:{summary:"square | rounded | circle"},defaultValue:{summary:"square"}}},disabled:{description:"Disabled state (reduced opacity and grayscale)",control:{type:"boolean"},table:{category:"Behavior",type:{summary:"boolean"},defaultValue:{summary:"false"}}},label:{description:'Accessible label for screen readers (ex: "France", "United Kingdom")',control:{type:"text"},table:{category:"Accessibility",type:{summary:"string"},defaultValue:{summary:""}}},decorative:{description:"Marks flag as decorative only (adds aria-hidden, removes from accessibility tree)",control:{type:"boolean"},table:{category:"Accessibility",type:{summary:"boolean"},defaultValue:{summary:"false"}}}}},n={render:a=>p(a),args:{...B}},i={render:()=>{const a="margin-bottom:var(--size-8)",s="margin:0 0 var(--size-4) 0;font-size:var(--size-5);font-weight:var(--font-weight-600);color:var(--gray-800)",l="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:var(--size-4)",o="display:flex;flex-direction:column;align-items:center;gap:var(--size-2)",c="font-size:var(--size-3);color:var(--gray-700)";return`<div>${Object.entries({Europe:[["AT","Austria"],["BE","Belgium"],["BG","Bulgaria"],["HR","Croatia"],["CY","Cyprus"],["CZ","Czech Republic"],["DK","Denmark"],["EE","Estonia"],["FI","Finland"],["FR","France"],["DE","Germany"],["GR","Greece"],["HU","Hungary"],["IE","Ireland"],["IT","Italy"],["LV","Latvia"],["LT","Lithuania"],["LU","Luxembourg"],["MT","Malta"],["NL","Netherlands"],["NO","Norway"],["PL","Poland"],["PT","Portugal"],["RO","Romania"],["SK","Slovakia"],["SI","Slovenia"],["ES","Spain"],["SE","Sweden"],["CH","Switzerland"],["GB","United Kingdom"]],Americas:[["AR","Argentina"],["BR","Brazil"],["CA","Canada"],["CL","Chile"],["CO","Colombia"],["MX","Mexico"],["PE","Peru"],["US","United States"]],Asia:[["CN","China"],["IN","India"],["ID","Indonesia"],["IL","Israel"],["JP","Japan"],["MY","Malaysia"],["PH","Philippines"],["SG","Singapore"],["KR","South Korea"],["TH","Thailand"],["TR","Turkey"],["AE","United Arab Emirates"],["VN","Vietnam"]],"Africa & Middle East":[["EG","Egypt"],["KE","Kenya"],["MA","Morocco"],["NG","Nigeria"],["SA","Saudi Arabia"],["ZA","South Africa"]],Oceania:[["AU","Australia"],["NZ","New Zealand"]]}).map(([e,t])=>`
      <div style="${a}">
        <h3 style="${s}">${e}</h3>
        <div style="${l}">
          ${t.map(([T,u])=>`
            <div style="${o}">
              ${p({code:T,label:u,size:"lg"})}
              <span style="${c}">${u}</span>
            </div>
          `).join("")}
        </div>
      </div>
    `).join("")}</div>`}},r={name:"AllCountries (Full)",render:()=>{const a="display:flex;flex-direction:column;gap:var(--size-6)",s="display:flex;gap:var(--size-3);align-items:center;flex-wrap:wrap",l="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:var(--size-4)",o="display:flex;flex-direction:column;align-items:center;gap:var(--size-2)",c="font-size:var(--size-3);color:var(--gray-700)",m=e=>e.map(t=>`
      <div style="${o}">
        ${p({code:t,label:t,size:"lg"})}
        <span style="${c}">${t}</span>
      </div>
    `).join(""),d=M.filter(e=>e.length===2||e.includes("-"));return`
      <div style="${a}">
        <div style="${s}"><strong>Total:</strong> <span>${d.length}</span></div>
        <div style="${l}">
          ${m(d)}
        </div>
      </div>
    `}};var g,y,S;n.parameters={...n.parameters,docs:{...(g=n.parameters)==null?void 0:g.docs,source:{originalSource:`{
  render: args => flagTwig(args),
  args: {
    ...data
  }
}`,...(S=(y=n.parameters)==null?void 0:y.docs)==null?void 0:S.source}}};var A,v,C;i.parameters={...i.parameters,docs:{...(A=i.parameters)==null?void 0:A.docs,source:{originalSource:`{
  render: () => {
    const sectionStyle = 'margin-bottom:var(--size-8)';
    const h3Style = 'margin:0 0 var(--size-4) 0;font-size:var(--size-5);font-weight:var(--font-weight-600);color:var(--gray-800)';
    const gridStyle = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:var(--size-4)';
    const itemStyle = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-2)';
    const labelStyle = 'font-size:var(--size-3);color:var(--gray-700)';
    const countries = {
      Europe: [['AT', 'Austria'], ['BE', 'Belgium'], ['BG', 'Bulgaria'], ['HR', 'Croatia'], ['CY', 'Cyprus'], ['CZ', 'Czech Republic'], ['DK', 'Denmark'], ['EE', 'Estonia'], ['FI', 'Finland'], ['FR', 'France'], ['DE', 'Germany'], ['GR', 'Greece'], ['HU', 'Hungary'], ['IE', 'Ireland'], ['IT', 'Italy'], ['LV', 'Latvia'], ['LT', 'Lithuania'], ['LU', 'Luxembourg'], ['MT', 'Malta'], ['NL', 'Netherlands'], ['NO', 'Norway'], ['PL', 'Poland'], ['PT', 'Portugal'], ['RO', 'Romania'], ['SK', 'Slovakia'], ['SI', 'Slovenia'], ['ES', 'Spain'], ['SE', 'Sweden'], ['CH', 'Switzerland'], ['GB', 'United Kingdom']],
      Americas: [['AR', 'Argentina'], ['BR', 'Brazil'], ['CA', 'Canada'], ['CL', 'Chile'], ['CO', 'Colombia'], ['MX', 'Mexico'], ['PE', 'Peru'], ['US', 'United States']],
      Asia: [['CN', 'China'], ['IN', 'India'], ['ID', 'Indonesia'], ['IL', 'Israel'], ['JP', 'Japan'], ['MY', 'Malaysia'], ['PH', 'Philippines'], ['SG', 'Singapore'], ['KR', 'South Korea'], ['TH', 'Thailand'], ['TR', 'Turkey'], ['AE', 'United Arab Emirates'], ['VN', 'Vietnam']],
      'Africa & Middle East': [['EG', 'Egypt'], ['KE', 'Kenya'], ['MA', 'Morocco'], ['NG', 'Nigeria'], ['SA', 'Saudi Arabia'], ['ZA', 'South Africa']],
      Oceania: [['AU', 'Australia'], ['NZ', 'New Zealand']]
    };
    const sections = Object.entries(countries).map(([continent, list]) => \`
      <div style="\${sectionStyle}">
        <h3 style="\${h3Style}">\${continent}</h3>
        <div style="\${gridStyle}">
          \${list.map(([code, name]) => \`
            <div style="\${itemStyle}">
              \${flagTwig({
      code,
      label: name,
      size: 'lg'
    })}
              <span style="\${labelStyle}">\${name}</span>
            </div>
          \`).join('')}
        </div>
      </div>
    \`).join('');
    return \`<div>\${sections}</div>\`;
  }
}`,...(C=(v=i.parameters)==null?void 0:v.docs)==null?void 0:C.source}}};var f,E,G;r.parameters={...r.parameters,docs:{...(f=r.parameters)==null?void 0:f.docs,source:{originalSource:`{
  name: 'AllCountries (Full)',
  render: () => {
    const wrap = 'display:flex;flex-direction:column;gap:var(--size-6)';
    const toolbar = 'display:flex;gap:var(--size-3);align-items:center;flex-wrap:wrap';
    const grid = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:var(--size-4)';
    const item = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-2)';
    const label = 'font-size:var(--size-3);color:var(--gray-700)';
    const cards = list => list.map(code => \`
      <div style="\${item}">
        \${flagTwig({
      code,
      label: code,
      size: 'lg'
    })}
        <span style="\${label}">\${code}</span>
      </div>
    \`).join('');

    // Build static markup with total count
    const all = flags.filter(c => c.length === 2 || c.includes('-'));
    return \`
      <div style="\${wrap}">
        <div style="\${toolbar}"><strong>Total:</strong> <span>\${all.length}</span></div>
        <div style="\${grid}">
          \${cards(all)}
        </div>
      </div>
    \`;
  }
}`,...(G=(E=r.parameters)==null?void 0:E.docs)==null?void 0:G.source}}};const x=["Default","AllCountries","AllCountriesFull"];export{i as AllCountries,r as AllCountriesFull,n as Default,x as __namedExportsOrder,b as default};
