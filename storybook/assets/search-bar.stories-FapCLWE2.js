import { T as g, t as y } from './iframe-D21U4yYN.js';
import { a as m, D as w } from './twig-BPJOkNgt.js';
m(g);
g.cache(!1);
const o = (e) => e,
  d = (e = {}) => {
    const r = y.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/search-bar/search-bar.twig',
      data: [
        {
          type: 'raw',
          value: `
<div class="ps-search-bar">
  <form class="ps-search-bar__form" role="search">
    <div class="ps-search-bar__input-wrapper">
      `,
          position: { start: 41, end: 174 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'show_icon', match: ['show_icon'] },
            ],
            position: { start: 174, end: 192 },
            output: [
              {
                type: 'raw',
                value: `        <svg class="ps-search-bar__icon ps-search-bar__icon--search" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
        </svg>
      `,
                position: { start: 193, end: 564 },
              },
            ],
          },
          position: { open: { start: 174, end: 192 }, close: { start: 564, end: 575 } },
        },
        {
          type: 'raw',
          value: `      <input
        type="search"
        class="ps-search-bar__input"
        placeholder="`,
          position: { start: 576, end: 669 },
        },
        {
          type: 'output',
          position: { start: 669, end: 686 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'placeholder',
              match: ['placeholder'],
              position: { start: 669, end: 686 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"
        value="`,
          position: { start: 686, end: 703 },
        },
        {
          type: 'output',
          position: { start: 703, end: 720 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'search_text',
              match: ['search_text'],
              position: { start: 703, end: 720 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"
        aria-label="Search"
      />
    </div>
    `,
          position: { start: 720, end: 774 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'has_suggestions',
                match: ['has_suggestions'],
              },
              {
                type: 'Twig.expression.type.variable',
                value: 'search_text',
                match: ['search_text'],
              },
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
            ],
            position: { start: 774, end: 814 },
            output: [
              {
                type: 'raw',
                value: `      <ul class="ps-search-bar__suggestions" role="listbox">
        `,
                position: { start: 815, end: 884 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.for',
                  keyVar: null,
                  valueVar: 'suggestion',
                  expression: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'suggestions',
                      match: ['suggestions'],
                    },
                  ],
                  position: { start: 884, end: 919 },
                  output: [
                    {
                      type: 'raw',
                      value: `          <li class="ps-search-bar__suggestion" role="option">
            <a href="#" class="ps-search-bar__suggestion-link">`,
                      position: { start: 920, end: 1046 },
                    },
                    {
                      type: 'output',
                      position: { start: 1046, end: 1062 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'suggestion',
                          match: ['suggestion'],
                          position: { start: 1046, end: 1062 },
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `</a>
          </li>
        `,
                      position: { start: 1062, end: 1091 },
                    },
                  ],
                },
                position: { open: { start: 884, end: 919 }, close: { start: 1091, end: 1103 } },
              },
              {
                type: 'raw',
                value: `      </ul>
    `,
                position: { start: 1104, end: 1120 },
              },
            ],
          },
          position: { open: { start: 774, end: 814 }, close: { start: 1120, end: 1131 } },
        },
        {
          type: 'raw',
          value: `  </form>
</div>
`,
          position: { start: 1132, end: 1132 },
        },
      ],
      precompiled: !0,
    });
    r.options.allowInlineIncludes = !0;
    try {
      let t = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(t) || (t = Object.entries(t)), o(r.render({ attributes: new w(t), ...e }))
      );
    } catch (t) {
      return o(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/search-bar/search-bar.twig: ' +
          t.toString()
      );
    }
  },
  h = {
    placeholder: 'Search properties...',
    search_text: '',
    has_suggestions: !0,
    suggestions: ['Paris Office', 'London Residences', 'Berlin Complex'],
    show_icon: !0,
  },
  b = {
    title: 'Components/Search Bar',
    tags: ['autodocs'],
    argTypes: {
      placeholder: {
        control: 'text',
        description: 'Placeholder text for input',
        table: { category: 'Content' },
      },
      search_text: {
        control: 'text',
        description: 'Current search text',
        table: { category: 'State' },
      },
      has_suggestions: {
        control: 'boolean',
        description: 'Show suggestions dropdown',
        table: { category: 'Configuration' },
      },
      show_icon: {
        control: 'boolean',
        description: 'Display search icon',
        table: { category: 'Configuration' },
      },
    },
  },
  s = { name: 'Empty', render: (e) => d(e), args: { ...h, search_text: '', has_suggestions: !1 } },
  a = {
    name: 'With Suggestions',
    render: (e) => d(e),
    args: { ...h, search_text: 'Paris', has_suggestions: !0 },
  };
var n, i, p;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((n = s.parameters) == null ? void 0 : n.docs),
    source: {
      originalSource: `{
  name: 'Empty',
  render: args => markup(args),
  args: {
    ...data,
    search_text: '',
    has_suggestions: false
  }
}`,
      ...((p = (i = s.parameters) == null ? void 0 : i.docs) == null ? void 0 : p.source),
    },
  },
};
var c, l, u;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((c = a.parameters) == null ? void 0 : c.docs),
    source: {
      originalSource: `{
  name: 'With Suggestions',
  render: args => markup(args),
  args: {
    ...data,
    search_text: 'Paris',
    has_suggestions: true
  }
}`,
      ...((u = (l = a.parameters) == null ? void 0 : l.docs) == null ? void 0 : u.source),
    },
  },
};
const f = ['Default', 'WithSuggestions'];
export { s as Default, a as WithSuggestions, f as __namedExportsOrder, b as default };
