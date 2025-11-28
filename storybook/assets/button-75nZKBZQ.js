import{j as n,M as l,C as i}from"./blocks-9a3CiBqj.js";import{u as r}from"./index-Dkzzmtkf.js";import{B as o,D as d,P as a,a as c,S as h,b as u,A as x,W as j,c as p,F as b,L as m,d as f}from"./button.stories-C2x3p2W1.js";import"./iframe-WhJwdty6.js";import"https://kit.fontawesome.com/a0eb0bad75.js";import"./twig-C4FvotTq.js";function t(s){const e={code:"code",h1:"h1",h2:"h2",h3:"h3",li:"li",p:"p",pre:"pre",strong:"strong",ul:"ul",...r(),...s.components};return n.jsxs(n.Fragment,{children:[n.jsx(l,{of:o}),`
`,n.jsx(e.h1,{id:"button",children:"Button"}),`
`,n.jsx(e.p,{children:"The button component is widely used and provides several variations following BNP Paribas brand guidelines."}),`
`,n.jsx(e.h2,{id:"button-technical-specs",children:"Button technical specs"}),`
`,n.jsxs(e.ul,{children:[`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"label"})," is the text displayed in the button."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"variant"})," determines the button style: 'primary' (filled) or 'secondary' (outlined)."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"color"})," defines the color scheme: 'green', 'purple', or 'white'."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"size"})," controls the button size: 'small' (34px), 'medium' (36px), or 'large' (40px)."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"icon"})," is an optional icon name from our icon library."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"iconPosition"})," determines icon placement: 'left' or 'right'."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"url"})," is optional - when provided, renders an ",n.jsx(e.code,{children:"<a>"})," tag instead of ",n.jsx(e.code,{children:"<button>"}),"."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"disabled"})," disables user interaction."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"loading"})," shows a loading state."]}),`
`,n.jsxs(e.li,{children:[n.jsx(e.code,{children:"fullWidth"})," makes the button take full container width."]}),`
`]}),`
`,n.jsx(e.h3,{id:"default-button",children:"Default button"}),`
`,n.jsx(e.p,{children:"The default button with medium size and primary green variant."}),`
`,n.jsx(i,{of:d}),`
`,n.jsx(e.h3,{id:"primary-buttons",children:"Primary buttons"}),`
`,n.jsxs(e.p,{children:["Primary buttons are used for main actions like ",n.jsx(e.strong,{children:"Submit"}),", ",n.jsx(e.strong,{children:"Add"}),", ",n.jsx(e.strong,{children:"Save"}),"."]}),`
`,n.jsx(i,{of:a}),`
`,n.jsx(i,{of:c}),`
`,n.jsx(e.h3,{id:"secondary-buttons",children:"Secondary buttons"}),`
`,n.jsx(e.p,{children:"Secondary buttons are used for less prominent actions."}),`
`,n.jsx(i,{of:h}),`
`,n.jsx(i,{of:u}),`
`,n.jsx(e.h3,{id:"all-variants",children:"All variants"}),`
`,n.jsx(e.p,{children:"Comparison of all available button variants."}),`
`,n.jsx(i,{of:x}),`
`,n.jsx(e.h3,{id:"button-with-icons",children:"Button with icons"}),`
`,n.jsx(e.p,{children:"Buttons can include icons positioned left, right, or icon-only."}),`
`,n.jsx(i,{of:j}),`
`,n.jsx(e.h3,{id:"button-sizes",children:"Button sizes"}),`
`,n.jsx(e.p,{children:"Buttons are available in three sizes: small (34px), medium (36px), and large (40px)."}),`
`,n.jsx(i,{of:p}),`
`,n.jsx(e.h3,{id:"full-width-button",children:"Full width button"}),`
`,n.jsx(e.p,{children:"Button that spans the full width of its container."}),`
`,n.jsx(i,{of:b}),`
`,n.jsx(e.h3,{id:"loading-state",children:"Loading state"}),`
`,n.jsx(e.p,{children:"Buttons can show a loading indicator during async operations."}),`
`,n.jsx(i,{of:m}),`
`,n.jsx(e.h3,{id:"disabled-state",children:"Disabled state"}),`
`,n.jsx(e.p,{children:"Disabled buttons prevent user interaction."}),`
`,n.jsx(i,{of:f}),`
`,n.jsx(e.h2,{id:"accessibility",children:"Accessibility"}),`
`,n.jsxs(e.ul,{children:[`
`,n.jsx(e.li,{children:"All buttons have proper focus states with visible outlines."}),`
`,n.jsx(e.li,{children:"Keyboard navigation works with Tab, Enter, and Space keys."}),`
`,n.jsxs(e.li,{children:["Icon-only buttons include ",n.jsx(e.code,{children:"aria-label"})," for screen readers."]}),`
`,n.jsxs(e.li,{children:["Disabled buttons are marked with ",n.jsx(e.code,{children:"disabled"})," attribute and ",n.jsx(e.code,{children:"aria-disabled"}),"."]}),`
`,n.jsx(e.li,{children:"Color contrast meets WCAG 2.2 AA standards (4.5:1 minimum)."}),`
`]}),`
`,n.jsx(e.h2,{id:"usage-in-drupal",children:"Usage in Drupal"}),`
`,n.jsx(e.pre,{children:n.jsx(e.code,{className:"language-twig",children:`{% include '@ps_theme/elements/button/button.twig' with {
  'label': 'Rechercher',
  'variant': 'primary',
  'color': 'green',
  'url': '/search',
} %}
`})})]})}function S(s={}){const{wrapper:e}={...r(),...s.components};return e?n.jsx(e,{...s,children:n.jsx(t,{...s})}):t(s)}export{S as default};
