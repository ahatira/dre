import { t as m, T as u } from './iframe-D21U4yYN.js';
import { a as v, D as w } from './twig-BPJOkNgt.js';
v(u);
u.cache(!1);
const n = (e) => e,
  d = (e = {}) => {
    const r = m.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/skeleton/skeleton.twig',
      data: [
        {
          type: 'raw',
          value: `
<div class="ps-skeleton ps-skeleton--`,
          position: { start: 56, end: 94 },
        },
        {
          type: 'output',
          position: { start: 94, end: 104 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'type',
              match: ['type'],
              position: { start: 94, end: 104 },
            },
          ],
        },
        {
          type: 'raw',
          value: `">
  `,
          position: { start: 104, end: 109 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'show_avatar',
                match: ['show_avatar'],
              },
            ],
            position: { start: 109, end: 129 },
            output: [
              {
                type: 'raw',
                value: `    <div class="ps-skeleton__avatar"></div>
  `,
                position: { start: 130, end: 176 },
              },
            ],
          },
          position: { open: { start: 109, end: 129 }, close: { start: 176, end: 187 } },
        },
        {
          type: 'raw',
          value: `  <div class="ps-skeleton__content">
    `,
          position: { start: 188, end: 229 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'i',
            expression: [
              {
                type: 'Twig.expression.type._function',
                fn: 'range',
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.number', value: 1, match: ['1', null] },
                  { type: 'Twig.expression.type.comma' },
                  { type: 'Twig.expression.type.variable', value: 'lines', match: ['lines'] },
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
            position: { start: 229, end: 263 },
            output: [
              {
                type: 'raw',
                value: '      <div class="ps-skeleton__line ps-skeleton__line--',
                position: { start: 264, end: 319 },
              },
              {
                type: 'output',
                position: { start: 319, end: 331 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'height',
                    match: ['height'],
                    position: { start: 319, end: 331 },
                  },
                ],
              },
              { type: 'raw', value: '" style="width: ', position: { start: 331, end: 347 } },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'i', match: ['i'] },
                    { type: 'Twig.expression.type.variable', value: 'lines', match: ['lines'] },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: '==',
                      precidence: 9,
                      associativity: 'leftToRight',
                      operator: '==',
                    },
                  ],
                  position: { start: 347, end: 366 },
                  output: [{ type: 'raw', value: '75%', position: { start: 366, end: 369 } }],
                },
                position: { open: { start: 347, end: 366 }, close: { start: 369, end: 379 } },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.else',
                  match: ['else'],
                  position: { start: 369, end: 379 },
                  output: [{ type: 'raw', value: '100%', position: { start: 379, end: 383 } }],
                },
                position: { open: { start: 369, end: 379 }, close: { start: 383, end: 394 } },
              },
              {
                type: 'raw',
                value: `"></div>
    `,
                position: { start: 394, end: 407 },
              },
            ],
          },
          position: { open: { start: 229, end: 263 }, close: { start: 407, end: 419 } },
        },
        {
          type: 'raw',
          value: `  </div>
</div>
`,
          position: { start: 420, end: 420 },
        },
      ],
      precompiled: !0,
    });
    r.options.allowInlineIncludes = !0;
    try {
      let t = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(t) || (t = Object.entries(t)), n(r.render({ attributes: new w(t), ...e }))
      );
    } catch (t) {
      return n(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/skeleton/skeleton.twig: ' +
          t.toString()
      );
    }
  },
  g = { type: 'text', lines: 3, height: 'medium', show_avatar: !1 },
  T = {
    title: 'Components/Skeleton',
    tags: ['autodocs'],
    argTypes: {
      type: {
        control: { type: 'select' },
        options: ['text', 'card', 'table'],
        description: 'Skeleton type/shape',
        table: { category: 'Display' },
      },
      lines: {
        control: { type: 'number', min: 1, max: 5 },
        description: 'Number of placeholder lines',
        table: { category: 'Content' },
      },
      height: {
        control: { type: 'select' },
        options: ['small', 'medium', 'large'],
        description: 'Height of each line',
        table: { category: 'Display' },
      },
      show_avatar: {
        control: 'boolean',
        description: 'Show avatar placeholder',
        table: { category: 'Content' },
      },
    },
  },
  a = {
    name: 'Text',
    render: (e) => d(e),
    args: { ...g, type: 'text', lines: 3, show_avatar: !1 },
  },
  s = {
    name: 'With Avatar',
    render: (e) => d(e),
    args: { ...g, type: 'text', lines: 3, show_avatar: !0 },
  };
var o, i, p;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((o = a.parameters) == null ? void 0 : o.docs),
    source: {
      originalSource: `{
  name: 'Text',
  render: args => markup(args),
  args: {
    ...data,
    type: 'text',
    lines: 3,
    show_avatar: false
  }
}`,
      ...((p = (i = a.parameters) == null ? void 0 : i.docs) == null ? void 0 : p.source),
    },
  },
};
var l, c, y;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((l = s.parameters) == null ? void 0 : l.docs),
    source: {
      originalSource: `{
  name: 'With Avatar',
  render: args => markup(args),
  args: {
    ...data,
    type: 'text',
    lines: 3,
    show_avatar: true
  }
}`,
      ...((y = (c = s.parameters) == null ? void 0 : c.docs) == null ? void 0 : y.source),
    },
  },
};
const f = ['Default', 'WithAvatar'];
export { a as Default, s as WithAvatar, f as __namedExportsOrder, T as default };
