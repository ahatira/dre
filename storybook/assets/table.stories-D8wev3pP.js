import { t as m, T as u } from './iframe-D21U4yYN.js';
import { a as b, D as g } from './twig-BPJOkNgt.js';
b(u);
u.cache(!1);
const o = (e) => e,
  y = (e = {}) => {
    const s = m.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/table/table.twig',
      data: [
        {
          type: 'raw',
          value: `
<div class="ps-table-wrapper">
  <table class="ps-table">
    <thead class="ps-table__head">
      <tr class="ps-table__row">
        `,
          position: { start: 34, end: 169 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'header',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'headers', match: ['headers'] },
            ],
            position: { start: 169, end: 196 },
            output: [
              {
                type: 'raw',
                value: '          <th class="ps-table__header">',
                position: { start: 197, end: 236 },
              },
              {
                type: 'output',
                position: { start: 236, end: 248 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'header',
                    match: ['header'],
                    position: { start: 236, end: 248 },
                  },
                ],
              },
              {
                type: 'raw',
                value: `</th>
        `,
                position: { start: 248, end: 262 },
              },
            ],
          },
          position: { open: { start: 169, end: 196 }, close: { start: 262, end: 274 } },
        },
        {
          type: 'raw',
          value: `      </tr>
    </thead>
    <tbody class="ps-table__body">
      `,
          position: { start: 275, end: 341 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'row',
            expression: [{ type: 'Twig.expression.type.variable', value: 'rows', match: ['rows'] }],
            position: { start: 341, end: 362 },
            output: [
              {
                type: 'raw',
                value: `        <tr class="ps-table__row">
          `,
                position: { start: 363, end: 408 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.for',
                  keyVar: null,
                  valueVar: 'cell',
                  expression: [
                    { type: 'Twig.expression.type.variable', value: 'row', match: ['row'] },
                  ],
                  position: { start: 408, end: 429 },
                  output: [
                    {
                      type: 'raw',
                      value: '            <td class="ps-table__cell">',
                      position: { start: 430, end: 469 },
                    },
                    {
                      type: 'output',
                      position: { start: 469, end: 479 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'cell',
                          match: ['cell'],
                          position: { start: 469, end: 479 },
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `</td>
          `,
                      position: { start: 479, end: 495 },
                    },
                  ],
                },
                position: { open: { start: 408, end: 429 }, close: { start: 495, end: 507 } },
              },
              {
                type: 'raw',
                value: `        </tr>
      `,
                position: { start: 508, end: 528 },
              },
            ],
          },
          position: { open: { start: 341, end: 362 }, close: { start: 528, end: 540 } },
        },
        {
          type: 'raw',
          value: `    </tbody>
  </table>
</div>
`,
          position: { start: 541, end: 541 },
        },
      ],
      precompiled: !0,
    });
    s.options.allowInlineIncludes = !0;
    try {
      let t = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(t) || (t = Object.entries(t)), o(s.render({ attributes: new g(t), ...e }))
      );
    } catch (t) {
      return o(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/table/table.twig: ' +
          t.toString()
      );
    }
  },
  w = {
    headers: ['Property', 'Location', 'Type', 'Price'],
    rows: [
      ['Central Office', 'Paris', 'Office', '€2.5M'],
      ['Residential Complex', 'London', 'Residential', '€4.2M'],
      ['Retail Space', 'Berlin', 'Retail', '€1.8M'],
    ],
  },
  f = {
    title: 'Components/Table',
    tags: ['autodocs'],
    argTypes: {
      headers: { description: 'Array of column header labels', table: { category: 'Content' } },
      rows: { description: 'Array of row arrays (cells)', table: { category: 'Content' } },
    },
  },
  a = { name: 'Data Table', render: (e) => y(e), args: { ...w } },
  r = { name: 'Empty State', render: (e) => y(e), args: { ...w, rows: [] } };
var n, p, i;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((n = a.parameters) == null ? void 0 : n.docs),
    source: {
      originalSource: `{
  name: 'Data Table',
  render: args => markup(args),
  args: {
    ...data
  }
}`,
      ...((i = (p = a.parameters) == null ? void 0 : p.docs) == null ? void 0 : i.source),
    },
  },
};
var l, d, c;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((l = r.parameters) == null ? void 0 : l.docs),
    source: {
      originalSource: `{
  name: 'Empty State',
  render: args => markup(args),
  args: {
    ...data,
    rows: []
  }
}`,
      ...((c = (d = r.parameters) == null ? void 0 : d.docs) == null ? void 0 : c.source),
    },
  },
};
const T = ['Default', 'Empty'];
export { a as Default, r as Empty, T as __namedExportsOrder, f as default };
