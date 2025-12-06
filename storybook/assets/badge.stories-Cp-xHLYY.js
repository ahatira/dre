import { i as B } from './icons-list-Di3fqTRs.js';
import { t as E, T as V } from './iframe-D21U4yYN.js';
import { D as I, a as j } from './twig-BPJOkNgt.js';
j(V);
V.cache(!1);
const d = (t) => t,
  e = (t = {}) => {
    const y = E.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/elements/badge/badge.twig',
      data: [
        { type: 'raw', value: '', position: { start: 683, end: 687 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'color',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'default' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 687, end: 731 },
          },
          position: { start: 687, end: 731 },
        },
        { type: 'raw', value: '', position: { start: 731, end: 733 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'size',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'medium' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 733, end: 774 },
          },
          position: { start: 733, end: 774 },
        },
        { type: 'raw', value: '', position: { start: 774, end: 776 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'pill',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'pill', match: ['pill'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.bool', value: !1 },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 776, end: 814 },
          },
          position: { start: 776, end: 814 },
        },
        { type: 'raw', value: '', position: { start: 814, end: 816 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'tag',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'url', match: ['url'] },
              { type: 'Twig.expression.type.string', value: 'a' },
              { type: 'Twig.expression.type.string', value: 'span' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 816, end: 852 },
          },
          position: { start: 816, end: 852 },
        },
        { type: 'raw', value: '', position: { start: 852, end: 854 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'has_text',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'text', match: ['text'] },
              { type: 'Twig.expression.type.test', filter: 'defined' },
              { type: 'Twig.expression.type.variable', value: 'text', match: ['text'] },
              { type: 'Twig.expression.type.test', filter: 'empty', modifier: 'not' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
            ],
            position: { start: 854, end: 914 },
          },
          position: { start: 854, end: 914 },
        },
        { type: 'raw', value: '', position: { start: 914, end: 918 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'badge_classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-badge' },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
              { type: 'Twig.expression.type.string', value: 'default' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '!=',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '!=',
              },
              { type: 'Twig.expression.type.string', value: 'ps-badge--' },
              { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
              { type: 'Twig.expression.type.string', value: 'medium' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '!=',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '!=',
              },
              { type: 'Twig.expression.type.string', value: 'ps-badge--' },
              { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'pill', match: ['pill'] },
              { type: 'Twig.expression.type.string', value: 'ps-badge--pill' },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
              { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
            ],
            position: { start: 918, end: 1102 },
          },
          position: { start: 918, end: 1102 },
        },
        { type: 'raw', value: '<', position: { start: 1102, end: 1107 } },
        {
          type: 'output',
          position: { start: 1107, end: 1116 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'tag',
              match: ['tag'],
              position: { start: 1107, end: 1116 },
            },
          ],
        },
        {
          type: 'output',
          position: { start: 1116, end: 1219 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 1116, end: 1219 },
            },
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 1116, end: 1219 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 1116, end: 1219 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 1116, end: 1219 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'badge_classes',
                  match: ['badge_classes'],
                  position: { start: 1116, end: 1219 },
                },
              ],
            },
            {
              type: 'Twig.expression.type.string',
              value: ' class="',
              position: { start: 1116, end: 1219 },
            },
            {
              type: 'Twig.expression.type.variable',
              value: 'badge_classes',
              match: ['badge_classes'],
              position: { start: 1116, end: 1219 },
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'join',
              match: ['|join', 'join'],
              position: { start: 1116, end: 1219 },
              params: [
                {
                  type: 'Twig.expression.type.parameter.start',
                  value: '(',
                  match: ['('],
                  position: { start: 1116, end: 1219 },
                },
                {
                  type: 'Twig.expression.type.string',
                  value: ' ',
                  position: { start: 1116, end: 1219 },
                },
                {
                  type: 'Twig.expression.type.parameter.end',
                  value: ')',
                  match: [')'],
                  position: { start: 1116, end: 1219 },
                  expression: !1,
                },
              ],
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'trim',
              match: ['|trim', 'trim'],
              position: { start: 1116, end: 1219 },
            },
            {
              type: 'Twig.expression.type.operator.binary',
              value: '~',
              position: { start: 1116, end: 1219 },
              precidence: 6,
              associativity: 'leftToRight',
              operator: '~',
            },
            {
              type: 'Twig.expression.type.string',
              value: '"',
              position: { start: 1116, end: 1219 },
            },
            {
              type: 'Twig.expression.type.operator.binary',
              value: '~',
              position: { start: 1116, end: 1219 },
              precidence: 6,
              associativity: 'leftToRight',
              operator: '~',
            },
            {
              type: 'Twig.expression.type.operator.binary',
              value: '?',
              position: { start: 1116, end: 1219 },
              precidence: 16,
              associativity: 'rightToLeft',
              operator: '?',
            },
          ],
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'url', match: ['url'] }],
            position: { start: 1219, end: 1231 },
            output: [
              { type: 'raw', value: ' href="', position: { start: 1231, end: 1238 } },
              {
                type: 'output',
                position: { start: 1238, end: 1247 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'url',
                    match: ['url'],
                    position: { start: 1238, end: 1247 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1247, end: 1248 } },
            ],
          },
          position: { open: { start: 1219, end: 1231 }, close: { start: 1248, end: 1259 } },
        },
        { type: 'raw', value: '>', position: { start: 1259, end: 1264 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] }],
            position: { start: 1264, end: 1279 },
            output: [
              {
                type: 'raw',
                value: '<span class="ps-badge__icon" data-icon="',
                position: { start: 1279, end: 1325 },
              },
              {
                type: 'output',
                position: { start: 1325, end: 1335 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'icon',
                    match: ['icon'],
                    position: { start: 1325, end: 1335 },
                  },
                ],
              },
              { type: 'raw', value: '"></span>', position: { start: 1335, end: 1348 } },
            ],
          },
          position: { open: { start: 1264, end: 1279 }, close: { start: 1348, end: 1361 } },
        },
        { type: 'raw', value: '', position: { start: 1361, end: 1365 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'has_text', match: ['has_text'] },
            ],
            position: { start: 1365, end: 1384 },
            output: [
              {
                type: 'raw',
                value: '<span class="ps-badge__text">',
                position: { start: 1384, end: 1419 },
              },
              {
                type: 'output_whitespace_both',
                position: { start: 1419, end: 1431 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'text',
                    match: ['text'],
                    position: { start: 1419, end: 1431 },
                  },
                ],
              },
              { type: 'raw', value: '</span>', position: { start: 1431, end: 1442 } },
            ],
          },
          position: { open: { start: 1365, end: 1384 }, close: { start: 1442, end: 1455 } },
        },
        { type: 'raw', value: '</', position: { start: 1455, end: 1459 } },
        {
          type: 'output',
          position: { start: 1459, end: 1468 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'tag',
              match: ['tag'],
              position: { start: 1459, end: 1468 },
            },
          ],
        },
        {
          type: 'raw',
          value: `>\r
`,
          position: { start: 1468, end: 1468 },
        },
      ],
      precompiled: !0,
    });
    y.options.allowInlineIncludes = !0;
    try {
      let r = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(r) || (r = Object.entries(r)), d(y.render({ attributes: new I(r), ...t }))
      );
    } catch (r) {
      return d(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/badge/badge.twig: ' +
          r.toString()
      );
    }
  },
  c = { text: 'Badge', color: 'default', pill: !1, size: 'medium', icon: '', url: '' },
  G = {
    title: 'Elements/Badge',
    render: (t) => e(t),
    args: c,
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component:
            'Compact badge for statuses and metadata with semantic colors, sizes, and optional pill or icon. Fully token-based for spacing, typography, colors, and radius.',
        },
      },
    },
    argTypes: {
      text: {
        control: 'text',
        description: 'Badge text (short: 1–2 words).',
        table: {
          category: 'Content',
          type: { summary: 'string', required: !0 },
          defaultValue: { summary: c.text || '""' },
        },
      },
      icon: {
        control: 'select',
        options: ['', ...B.categories.generic],
        description: 'Optional icon name (without "icon-" prefix).',
        table: {
          category: 'Content',
          type: { summary: 'string' },
          defaultValue: { summary: '""' },
        },
      },
      color: {
        control: { type: 'select' },
        options: [
          'default',
          'primary',
          'secondary',
          'gold',
          'info',
          'success',
          'warning',
          'danger',
        ],
        description: 'Semantic color variant (supports legacy gold accent).',
        table: {
          category: 'Appearance',
          type: {
            summary: 'default | primary | secondary | gold | info | success | warning | danger',
          },
          defaultValue: { summary: 'default' },
        },
      },
      size: {
        control: { type: 'inline-radio' },
        options: ['small', 'medium', 'large'],
        description: 'Badge size driven by typography & padding tokens.',
        table: {
          category: 'Appearance',
          type: { summary: 'small | medium | large' },
          defaultValue: { summary: 'medium' },
        },
      },
      pill: {
        control: 'boolean',
        description: 'Apply fully rounded pill shape.',
        table: {
          category: 'Appearance',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      url: {
        control: 'text',
        description: 'Optional link URL (renders <a> with focus outline).',
        table: { category: 'Link', type: { summary: 'string' }, defaultValue: { summary: '""' } },
      },
    },
  },
  a = { render: (t) => e(t), args: { ...c } },
  i = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${e({ color: 'default', text: 'Default' })}
      ${e({ color: 'primary', text: 'Primary' })}
      ${e({ color: 'secondary', text: 'Secondary' })}
      ${e({ color: 'gold', text: 'Gold' })}
      ${e({ color: 'info', text: 'Info' })}
      ${e({ color: 'success', text: 'Success' })}
      ${e({ color: 'warning', text: 'Warning' })}
      ${e({ color: 'danger', text: 'Danger' })}
    </div>
  `,
  },
  o = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${e({ size: 'small', text: 'Small', color: 'primary' })}
      ${e({ size: 'medium', text: 'Medium', color: 'primary' })}
      ${e({ size: 'large', text: 'Large', color: 'primary' })}
    </div>
  `,
  },
  s = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      ${e({ text: 'Rounded', color: 'primary', pill: !1 })}
      ${e({ text: 'Pill', color: 'primary', pill: !0 })}
    </div>
  `,
  },
  n = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${e({ color: 'info', text: 'Location', icon: 'pin-map' })}
      ${e({ color: 'success', text: 'Calendar', icon: 'calendar', pill: !0 })}
      ${e({ color: 'gold', text: 'Exclusive', icon: 'medal', pill: !0 })}
      ${e({ color: 'primary', text: 'Verified', icon: 'check' })}
    </div>
  `,
  },
  p = {
    render: () => `
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      ${e({ color: 'primary', text: 'Link badge', url: '#' })}
      ${e({ color: 'info', text: 'Learn more', icon: 'infos', url: '#', pill: !0 })}
      ${e({ color: 'secondary', text: 'Discover', url: '#' })}
    </div>
  `,
  },
  l = {
    render: () => `
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Status badges</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${e({ color: 'success', text: 'Available', icon: 'check' })}
          ${e({ color: 'warning', text: 'Pending', icon: 'help' })}
          ${e({ color: 'danger', text: 'Sold', icon: 'close' })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Property features</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${e({ color: 'gold', text: 'Exclusive', icon: 'medal', pill: !0 })}
          ${e({ color: 'info', text: 'New', pill: !0 })}
          ${e({ color: 'secondary', text: 'Premium', pill: !0 })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Interactive labels</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          ${e({ color: 'primary', text: 'View details', url: '#' })}
          ${e({ color: 'info', text: 'Learn more', icon: 'infos', url: '#', pill: !0 })}
        </div>
      </div>
    </div>
  `,
  };
var u, g, m;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((u = a.parameters) == null ? void 0 : u.docs),
    source: {
      originalSource: `{
  render: args => component(args),
  args: {
    ...data
  }
}`,
      ...((m = (g = a.parameters) == null ? void 0 : g.docs) == null ? void 0 : m.source),
    },
  },
};
var v, x, w;
i.parameters = {
  ...i.parameters,
  docs: {
    ...((v = i.parameters) == null ? void 0 : v.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      \${component({
    color: 'default',
    text: 'Default'
  })}
      \${component({
    color: 'primary',
    text: 'Primary'
  })}
      \${component({
    color: 'secondary',
    text: 'Secondary'
  })}
      \${component({
    color: 'gold',
    text: 'Gold'
  })}
      \${component({
    color: 'info',
    text: 'Info'
  })}
      \${component({
    color: 'success',
    text: 'Success'
  })}
      \${component({
    color: 'warning',
    text: 'Warning'
  })}
      \${component({
    color: 'danger',
    text: 'Danger'
  })}
    </div>
  \`
}`,
      ...((w = (x = i.parameters) == null ? void 0 : x.docs) == null ? void 0 : w.source),
    },
  },
};
var f, T, h;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((f = o.parameters) == null ? void 0 : f.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${component({
    size: 'small',
    text: 'Small',
    color: 'primary'
  })}
      \${component({
    size: 'medium',
    text: 'Medium',
    color: 'primary'
  })}
      \${component({
    size: 'large',
    text: 'Large',
    color: 'primary'
  })}
    </div>
  \`
}`,
      ...((h = (T = o.parameters) == null ? void 0 : T.docs) == null ? void 0 : h.source),
    },
  },
};
var b, $, z;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((b = s.parameters) == null ? void 0 : b.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); align-items: center;">
      \${component({
    text: 'Rounded',
    color: 'primary',
    pill: false
  })}
      \${component({
    text: 'Pill',
    color: 'primary',
    pill: true
  })}
    </div>
  \`
}`,
      ...((z = ($ = s.parameters) == null ? void 0 : $.docs) == null ? void 0 : z.source),
    },
  },
};
var k, S, A;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((k = n.parameters) == null ? void 0 : k.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      \${component({
    color: 'info',
    text: 'Location',
    icon: 'pin-map'
  })}
      \${component({
    color: 'success',
    text: 'Calendar',
    icon: 'calendar',
    pill: true
  })}
      \${component({
    color: 'gold',
    text: 'Exclusive',
    icon: 'medal',
    pill: true
  })}
      \${component({
    color: 'primary',
    text: 'Verified',
    icon: 'check'
  })}
    </div>
  \`
}`,
      ...((A = (S = n.parameters) == null ? void 0 : S.docs) == null ? void 0 : A.source),
    },
  },
};
var L, _, C;
p.parameters = {
  ...p.parameters,
  docs: {
    ...((L = p.parameters) == null ? void 0 : L.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-4); flex-wrap: wrap; align-items: center;">
      \${component({
    color: 'primary',
    text: 'Link badge',
    url: '#'
  })}
      \${component({
    color: 'info',
    text: 'Learn more',
    icon: 'infos',
    url: '#',
    pill: true
  })}
      \${component({
    color: 'secondary',
    text: 'Discover',
    url: '#'
  })}
    </div>
  \`
}`,
      ...((C = (_ = p.parameters) == null ? void 0 : _.docs) == null ? void 0 : C.source),
    },
  },
};
var D, P, R;
l.parameters = {
  ...l.parameters,
  docs: {
    ...((D = l.parameters) == null ? void 0 : D.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: grid; gap: var(--size-6); grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Status badges</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          \${component({
    color: 'success',
    text: 'Available',
    icon: 'check'
  })}
          \${component({
    color: 'warning',
    text: 'Pending',
    icon: 'help'
  })}
          \${component({
    color: 'danger',
    text: 'Sold',
    icon: 'close'
  })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Property features</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          \${component({
    color: 'gold',
    text: 'Exclusive',
    icon: 'medal',
    pill: true
  })}
          \${component({
    color: 'info',
    text: 'New',
    pill: true
  })}
          \${component({
    color: 'secondary',
    text: 'Premium',
    pill: true
  })}
        </div>
      </div>
      <div>
        <h4 style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); font-weight: 500;">Interactive labels</h4>
        <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
          \${component({
    color: 'primary',
    text: 'View details',
    url: '#'
  })}
          \${component({
    color: 'info',
    text: 'Learn more',
    icon: 'infos',
    url: '#',
    pill: true
  })}
        </div>
      </div>
    </div>
  \`
}`,
      ...((R = (P = l.parameters) == null ? void 0 : P.docs) == null ? void 0 : R.source),
    },
  },
};
const M = ['Default', 'AllColors', 'AllSizes', 'AllShapes', 'WithIcons', 'AsLinks', 'UseCases'];
export {
  i as AllColors,
  s as AllShapes,
  o as AllSizes,
  p as AsLinks,
  a as Default,
  l as UseCases,
  n as WithIcons,
  M as __namedExportsOrder,
  G as default,
};
