import { T as c, t as y } from './iframe-D21U4yYN.js';
import { a as b, D as m } from './twig-BPJOkNgt.js';
b(c);
c.cache(!1);
const n = (e) => e,
  g = (e = {}) => {
    const r = y.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/tag-list/tag-list.twig',
      data: [
        {
          type: 'raw',
          value: `
<div class="ps-tag-list" role="group" aria-label="Tags">
  `,
          position: { start: 35, end: 95 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'tag',
            expression: [{ type: 'Twig.expression.type.variable', value: 'tags', match: ['tags'] }],
            position: { start: 95, end: 116 },
            output: [
              {
                type: 'raw',
                value: `    <div class="ps-tag-list__item">
      <label class="ps-tag">
        <input type="checkbox" class="ps-tag__input" `,
                position: { start: 117, end: 235 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'tag', match: ['tag'] },
                    { type: 'Twig.expression.type.key.period', key: 'selected' },
                  ],
                  position: { start: 235, end: 256 },
                  output: [{ type: 'raw', value: 'checked', position: { start: 256, end: 263 } }],
                },
                position: { open: { start: 235, end: 256 }, close: { start: 263, end: 274 } },
              },
              { type: 'raw', value: ' aria-label="', position: { start: 274, end: 287 } },
              {
                type: 'output',
                position: { start: 287, end: 302 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'tag',
                    match: ['tag'],
                    position: { start: 287, end: 302 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 287, end: 302 },
                    key: 'label',
                  },
                ],
              },
              {
                type: 'raw',
                value: `" />
        <span class="ps-tag__label">`,
                position: { start: 302, end: 343 },
              },
              {
                type: 'output',
                position: { start: 343, end: 358 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'tag',
                    match: ['tag'],
                    position: { start: 343, end: 358 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 343, end: 358 },
                    key: 'label',
                  },
                ],
              },
              {
                type: 'raw',
                value: `</span>
        `,
                position: { start: 358, end: 374 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'tag', match: ['tag'] },
                    { type: 'Twig.expression.type.key.period', key: 'removable' },
                  ],
                  position: { start: 374, end: 396 },
                  output: [
                    {
                      type: 'raw',
                      value:
                        '          <button type="button" class="ps-tag__remove" aria-label="Remove ',
                      position: { start: 397, end: 471 },
                    },
                    {
                      type: 'output',
                      position: { start: 471, end: 486 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'tag',
                          match: ['tag'],
                          position: { start: 471, end: 486 },
                        },
                        {
                          type: 'Twig.expression.type.key.period',
                          position: { start: 471, end: 486 },
                          key: 'label',
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `">×</button>
        `,
                      position: { start: 486, end: 507 },
                    },
                  ],
                },
                position: { open: { start: 374, end: 396 }, close: { start: 507, end: 518 } },
              },
              {
                type: 'raw',
                value: `      </label>
    </div>
  `,
                position: { start: 519, end: 547 },
              },
            ],
          },
          position: { open: { start: 95, end: 116 }, close: { start: 547, end: 559 } },
        },
        {
          type: 'raw',
          value: `</div>
`,
          position: { start: 560, end: 560 },
        },
      ],
      precompiled: !0,
    });
    r.options.allowInlineIncludes = !0;
    try {
      let t = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(t) || (t = Object.entries(t)), n(r.render({ attributes: new m(t), ...e }))
      );
    } catch (t) {
      return n(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/tag-list/tag-list.twig: ' +
          t.toString()
      );
    }
  },
  v = {
    tags: [
      { label: 'Paris', removable: !0, selected: !0 },
      { label: 'London', removable: !0, selected: !1 },
      { label: 'Berlin', removable: !0, selected: !0 },
    ],
  },
  T = {
    title: 'Components/Tag List',
    tags: ['autodocs'],
    argTypes: {
      tags: {
        description: 'Array of tag objects with label, removable, selected',
        table: { category: 'Content' },
      },
    },
  },
  a = { name: 'With Checkboxes', render: (e) => g(e), args: { ...v } },
  s = {
    name: 'Many Tags',
    render: (e) => g(e),
    args: {
      tags: [
        { label: 'Paris', removable: !0, selected: !0 },
        { label: 'London', removable: !0, selected: !1 },
        { label: 'Berlin', removable: !0, selected: !0 },
        { label: 'Amsterdam', removable: !0, selected: !1 },
        { label: 'Brussels', removable: !0, selected: !0 },
      ],
    },
  };
var o, l, i;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((o = a.parameters) == null ? void 0 : o.docs),
    source: {
      originalSource: `{
  name: 'With Checkboxes',
  render: args => markup(args),
  args: {
    ...data
  }
}`,
      ...((i = (l = a.parameters) == null ? void 0 : l.docs) == null ? void 0 : i.source),
    },
  },
};
var p, u, d;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((p = s.parameters) == null ? void 0 : p.docs),
    source: {
      originalSource: `{
  name: 'Many Tags',
  render: args => markup(args),
  args: {
    tags: [{
      label: 'Paris',
      removable: true,
      selected: true
    }, {
      label: 'London',
      removable: true,
      selected: false
    }, {
      label: 'Berlin',
      removable: true,
      selected: true
    }, {
      label: 'Amsterdam',
      removable: true,
      selected: false
    }, {
      label: 'Brussels',
      removable: true,
      selected: true
    }]
  }
}`,
      ...((d = (u = s.parameters) == null ? void 0 : u.docs) == null ? void 0 : d.source),
    },
  },
};
const f = ['Default', 'ManyTags'];
export { a as Default, s as ManyTags, f as __namedExportsOrder, T as default };
