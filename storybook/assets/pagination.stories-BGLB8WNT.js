import { t as h, T as w } from './iframe-D21U4yYN.js';
import { a as b, D as x } from './twig-BPJOkNgt.js';
b(w);
w.cache(!1);
const p = (e) => e,
  i = (e = {}) => {
    const o = h.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/pagination/pagination.twig',
      data: [
        {
          type: 'raw',
          value: `
<nav class="ps-pagination" aria-label="Pagination">
  <ul class="ps-pagination__list">
    `,
          position: { start: 34, end: 126 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'has_previous',
                match: ['has_previous'],
              },
            ],
            position: { start: 126, end: 147 },
            output: [
              {
                type: 'raw',
                value: `      <li class="ps-pagination__item">
        <a href="#" class="ps-pagination__link ps-pagination__link--prev" aria-label="Previous page">
          <span class="visually-hidden">Previous</span>
          <svg class="icon icon--prev" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/>
          </svg>
        </a>
      </li>
    `,
                position: { start: 148, end: 560 },
              },
            ],
          },
          position: { open: { start: 126, end: 147 }, close: { start: 560, end: 570 } },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.else',
            match: ['else'],
            position: { start: 560, end: 570 },
            output: [
              {
                type: 'raw',
                value: `      <li class="ps-pagination__item">
        <span class="ps-pagination__link ps-pagination__link--disabled" aria-disabled="true">
          <span class="visually-hidden">Previous page unavailable</span>
          <svg class="icon icon--prev" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/>
          </svg>
        </span>
      </li>
    `,
                position: { start: 571, end: 995 },
              },
            ],
          },
          position: { open: { start: 560, end: 570 }, close: { start: 995, end: 1006 } },
        },
        {
          type: 'raw',
          value: `
    `,
          position: { start: 1007, end: 1012 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'page',
            expression: [
              {
                type: 'Twig.expression.type._function',
                fn: 'range',
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.number', value: 1, match: ['1', null] },
                  { type: 'Twig.expression.type.comma' },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'total_pages',
                    match: ['total_pages'],
                  },
                  { type: 'Twig.expression.type.number', value: 1, match: ['1', null] },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '+',
                    precidence: 6,
                    associativity: 'leftToRight',
                    operator: '+',
                  },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1012, end: 1055 },
            output: [
              {
                type: 'raw',
                value: `      <li class="ps-pagination__item">
        `,
                position: { start: 1056, end: 1103 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'page', match: ['page'] },
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'current_page',
                      match: ['current_page'],
                    },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: '==',
                      precidence: 9,
                      associativity: 'leftToRight',
                      operator: '==',
                    },
                  ],
                  position: { start: 1103, end: 1132 },
                  output: [
                    {
                      type: 'raw',
                      value:
                        '          <span class="ps-pagination__link ps-pagination__link--active" aria-current="page">',
                      position: { start: 1133, end: 1225 },
                    },
                    {
                      type: 'output',
                      position: { start: 1225, end: 1235 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'page',
                          match: ['page'],
                          position: { start: 1225, end: 1235 },
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `</span>
        `,
                      position: { start: 1235, end: 1251 },
                    },
                  ],
                },
                position: { open: { start: 1103, end: 1132 }, close: { start: 1251, end: 1261 } },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.else',
                  match: ['else'],
                  position: { start: 1251, end: 1261 },
                  output: [
                    {
                      type: 'raw',
                      value: '          <a href="#" class="ps-pagination__link">',
                      position: { start: 1262, end: 1312 },
                    },
                    {
                      type: 'output',
                      position: { start: 1312, end: 1322 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'page',
                          match: ['page'],
                          position: { start: 1312, end: 1322 },
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `</a>
        `,
                      position: { start: 1322, end: 1335 },
                    },
                  ],
                },
                position: { open: { start: 1251, end: 1261 }, close: { start: 1335, end: 1346 } },
              },
              {
                type: 'raw',
                value: `      </li>
    `,
                position: { start: 1347, end: 1363 },
              },
            ],
          },
          position: { open: { start: 1012, end: 1055 }, close: { start: 1363, end: 1375 } },
        },
        {
          type: 'raw',
          value: `
    `,
          position: { start: 1376, end: 1381 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'has_next', match: ['has_next'] },
            ],
            position: { start: 1381, end: 1398 },
            output: [
              {
                type: 'raw',
                value: `      <li class="ps-pagination__item">
        <a href="#" class="ps-pagination__link ps-pagination__link--next" aria-label="Next page">
          <span class="visually-hidden">Next</span>
          <svg class="icon icon--next" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M10 6L8.59 7.41 12.17 11 8.59 14.59 10 16l6-6z" fill="currentColor"/>
          </svg>
        </a>
      </li>
    `,
                position: { start: 1399, end: 1804 },
              },
            ],
          },
          position: { open: { start: 1381, end: 1398 }, close: { start: 1804, end: 1814 } },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.else',
            match: ['else'],
            position: { start: 1804, end: 1814 },
            output: [
              {
                type: 'raw',
                value: `      <li class="ps-pagination__item">
        <span class="ps-pagination__link ps-pagination__link--disabled" aria-disabled="true">
          <span class="visually-hidden">Next page unavailable</span>
          <svg class="icon icon--next" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M10 6L8.59 7.41 12.17 11 8.59 14.59 10 16l6-6z" fill="currentColor"/>
          </svg>
        </span>
      </li>
    `,
                position: { start: 1815, end: 2236 },
              },
            ],
          },
          position: { open: { start: 1804, end: 1814 }, close: { start: 2236, end: 2247 } },
        },
        {
          type: 'raw',
          value: `  </ul>
</nav>
`,
          position: { start: 2248, end: 2248 },
        },
      ],
      precompiled: !0,
    });
    o.options.allowInlineIncludes = !0;
    try {
      let a = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(a) || (a = Object.entries(a)), p(o.render({ attributes: new x(a), ...e }))
      );
    } catch (a) {
      return p(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/pagination/pagination.twig: ' +
          a.toString()
      );
    }
  },
  r = {
    current_page: 2,
    total_pages: 5,
    per_page: 10,
    total_items: 45,
    has_previous: !0,
    has_next: !0,
  },
  T = {
    title: 'Components/Pagination',
    tags: ['autodocs'],
    argTypes: {
      current_page: {
        control: { type: 'number', min: 1, max: 10 },
        description: 'Current active page number',
        table: { category: 'State' },
      },
      total_pages: {
        control: { type: 'number', min: 1, max: 20 },
        description: 'Total number of pages available',
        table: { category: 'Configuration' },
      },
      has_previous: {
        control: 'boolean',
        description: 'Show previous page button',
        table: { category: 'State' },
      },
      has_next: {
        control: 'boolean',
        description: 'Show next page button',
        table: { category: 'State' },
      },
    },
  },
  t = { name: 'Pagination', render: (e) => i(e), args: { ...r } },
  n = {
    name: 'First Page (No Previous)',
    render: (e) => i(e),
    args: { ...r, current_page: 1, has_previous: !1, has_next: !0 },
  },
  s = {
    name: 'Last Page (No Next)',
    render: (e) => i(e),
    args: { ...r, current_page: 5, has_previous: !0, has_next: !1 },
  };
var l, c, u;
t.parameters = {
  ...t.parameters,
  docs: {
    ...((l = t.parameters) == null ? void 0 : l.docs),
    source: {
      originalSource: `{
  name: 'Pagination',
  render: args => markup(args),
  args: {
    ...data
  }
}`,
      ...((u = (c = t.parameters) == null ? void 0 : c.docs) == null ? void 0 : u.source),
    },
  },
};
var g, d, y;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((g = n.parameters) == null ? void 0 : g.docs),
    source: {
      originalSource: `{
  name: 'First Page (No Previous)',
  render: args => markup(args),
  args: {
    ...data,
    current_page: 1,
    has_previous: false,
    has_next: true
  }
}`,
      ...((y = (d = n.parameters) == null ? void 0 : d.docs) == null ? void 0 : y.source),
    },
  },
};
var v, m, _;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((v = s.parameters) == null ? void 0 : v.docs),
    source: {
      originalSource: `{
  name: 'Last Page (No Next)',
  render: args => markup(args),
  args: {
    ...data,
    current_page: 5,
    has_previous: true,
    has_next: false
  }
}`,
      ...((_ = (m = s.parameters) == null ? void 0 : m.docs) == null ? void 0 : _.source),
    },
  },
};
const P = ['Default', 'FirstPage', 'LastPage'];
export { t as Default, n as FirstPage, s as LastPage, P as __namedExportsOrder, T as default };
