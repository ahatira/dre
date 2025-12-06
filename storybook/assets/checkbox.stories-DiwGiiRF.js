import { t as h, T as v } from './iframe-D21U4yYN.js';
import { a as f, D as x } from './twig-BPJOkNgt.js';
f(v);
v.cache(!1);
const o = (t) => t,
  e = (t = {}) => {
    const n = h.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/elements/checkbox/checkbox.twig',
      data: [
        { type: 'raw', value: '', position: { start: 326, end: 328 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'checked',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'checked', match: ['checked'] },
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
            position: { start: 328, end: 372 },
          },
          position: { start: 328, end: 372 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'disabled',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
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
            position: { start: 373, end: 419 },
          },
          position: { start: 373, end: 419 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'id',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.variable', value: 'name', match: ['name'] },
                  { type: 'Twig.expression.type.string', value: '-' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '~',
                    precidence: 6,
                    associativity: 'leftToRight',
                    operator: '~',
                  },
                  { type: 'Twig.expression.type.variable', value: 'value', match: ['value'] },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '~',
                    precidence: 6,
                    associativity: 'leftToRight',
                    operator: '~',
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
            position: { start: 420, end: 467 },
          },
          position: { start: 420, end: 467 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'attributes',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'attributes', match: ['attributes'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  {
                    type: 'Twig.expression.type._function',
                    fn: 'create_attribute',
                    params: [
                      { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                      {
                        type: 'Twig.expression.type.parameter.end',
                        value: ')',
                        match: [')'],
                        expression: !1,
                      },
                    ],
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
            position: { start: 468, end: 531 },
          },
          position: { start: 468, end: 531 },
        },
        { type: 'raw', value: '', position: { start: 532, end: 533 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-checkbox' },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              { type: 'Twig.expression.type.string', value: 'ps-checkbox--disabled' },
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
            position: { start: 533, end: 619 },
          },
          position: { start: 533, end: 619 },
        },
        { type: 'raw', value: '<label ', position: { start: 620, end: 628 } },
        {
          type: 'output',
          position: { start: 628, end: 662 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 628, end: 662 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 628, end: 662 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 628, end: 662 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 628, end: 662 },
                },
              ],
            },
          ],
        },
        { type: 'raw', value: ' for="', position: { start: 662, end: 668 } },
        {
          type: 'output',
          position: { start: 668, end: 676 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'id',
              match: ['id'],
              position: { start: 668, end: 676 },
            },
          ],
        },
        {
          type: 'raw',
          value: `">
  <input
    class="ps-checkbox__input"
    type="checkbox"
    id="`,
          position: { start: 676, end: 747 },
        },
        {
          type: 'output',
          position: { start: 747, end: 755 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'id',
              match: ['id'],
              position: { start: 747, end: 755 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"
    name="`,
          position: { start: 755, end: 767 },
        },
        {
          type: 'output',
          position: { start: 767, end: 777 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'name',
              match: ['name'],
              position: { start: 767, end: 777 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"
    value="`,
          position: { start: 777, end: 790 },
        },
        {
          type: 'output',
          position: { start: 790, end: 801 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'value',
              match: ['value'],
              position: { start: 790, end: 801 },
            },
          ],
        },
        { type: 'raw', value: '"', position: { start: 801, end: 807 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'checked', match: ['checked'] },
            ],
            position: { start: 807, end: 824 },
            output: [{ type: 'raw', value: ' checked', position: { start: 824, end: 832 } }],
          },
          position: { open: { start: 807, end: 824 }, close: { start: 832, end: 844 } },
        },
        { type: 'raw', value: '', position: { start: 845, end: 849 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
            ],
            position: { start: 849, end: 867 },
            output: [{ type: 'raw', value: ' disabled', position: { start: 867, end: 876 } }],
          },
          position: { open: { start: 849, end: 867 }, close: { start: 876, end: 888 } },
        },
        { type: 'raw', value: 'aria-checked="', position: { start: 889, end: 907 } },
        {
          type: 'output',
          position: { start: 907, end: 939 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'checked',
              match: ['checked'],
              position: { start: 907, end: 939 },
            },
            {
              type: 'Twig.expression.type.string',
              value: 'true',
              position: { start: 907, end: 939 },
            },
            {
              type: 'Twig.expression.type.string',
              value: 'false',
              position: { start: 907, end: 939 },
            },
            {
              type: 'Twig.expression.type.operator.binary',
              value: '?',
              position: { start: 907, end: 939 },
              precidence: 16,
              associativity: 'rightToLeft',
              operator: '?',
            },
          ],
        },
        {
          type: 'raw',
          value: `"
  />
  <span class="ps-checkbox__box" aria-hidden="true"></span>`,
          position: { start: 939, end: 1008 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'label', match: ['label'] }],
            position: { start: 1008, end: 1024 },
            output: [
              {
                type: 'raw',
                value: '<span class="ps-checkbox__label">',
                position: { start: 1025, end: 1062 },
              },
              {
                type: 'output',
                position: { start: 1062, end: 1073 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'label',
                    match: ['label'],
                    position: { start: 1062, end: 1073 },
                  },
                ],
              },
              { type: 'raw', value: '</span>', position: { start: 1073, end: 1083 } },
            ],
          },
          position: { open: { start: 1008, end: 1024 }, close: { start: 1083, end: 1096 } },
        },
        { type: 'raw', value: '</label>', position: { start: 1097, end: 1097 } },
      ],
      precompiled: !0,
    });
    n.options.allowInlineIncludes = !0;
    try {
      let a = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(a) || (a = Object.entries(a)), o(n.render({ attributes: new x(a), ...t }))
      );
    } catch (a) {
      return o(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/checkbox/checkbox.twig: ' +
          a.toString()
      );
    }
  },
  w = { name: 'option', value: '1', label: 'Option label', checked: !1, disabled: !1, id: '' },
  $ = {
    title: 'Elements/Checkbox',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component:
            'Native checkbox with accessible label and component-scoped CSS variables. Supports checked/disabled states with semantic color tokens.',
        },
      },
    },
    argTypes: {
      name: {
        control: 'text',
        description: 'Input `name` attribute (required)',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      value: {
        control: 'text',
        description: 'Input `value` attribute (required)',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      label: {
        control: 'text',
        description: 'Label text displayed next to checkbox',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      checked: {
        control: 'boolean',
        description: 'Whether checkbox is checked',
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: 'false' },
        },
      },
      disabled: {
        control: 'boolean',
        description: 'Whether checkbox is disabled',
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: 'false' },
        },
      },
      id: {
        control: 'text',
        description: 'Unique input ID (auto-generated from name+value if omitted)',
        table: { category: 'Accessibility', type: { summary: 'string' } },
      },
    },
  },
  s = { render: (t) => e(t), args: { ...w } },
  i = {
    render: () => `
    <div style="display: flex; gap: var(--size-6); flex-wrap: wrap; align-items: flex-start;">
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>Enabled</strong>
        ${e({ name: 'enabled', value: '1', label: 'Unchecked', checked: !1, disabled: !1 })}
        ${e({ name: 'enabled', value: '2', label: 'Checked', checked: !0, disabled: !1 })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>Disabled</strong>
        ${e({ name: 'disabled', value: '1', label: 'Unchecked', checked: !1, disabled: !0 })}
        ${e({ name: 'disabled', value: '2', label: 'Checked', checked: !0, disabled: !0 })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>No Label</strong>
        ${e({ name: 'nolabel', value: '1', label: '', checked: !1, disabled: !1 })}
        ${e({ name: 'nolabel', value: '2', label: '', checked: !0, disabled: !1 })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3); max-width: 300px;">
        <strong>Long Label</strong>
        ${e({ name: 'long', value: '1', label: 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Cursus posuere et egestas id metus sit amet magna.', checked: !1 })}
      </div>
    </div>
  `,
  },
  r = {
    render: () => `
    <fieldset style="border: 0; padding: 0; margin: 0;">
      <legend style="font-weight: var(--font-weight-600); margin-bottom: var(--size-3);">Select your preferences</legend>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        ${e({ name: 'preferences[]', value: 'newsletter', label: 'Subscribe to newsletter', checked: !0 })}
        ${e({ name: 'preferences[]', value: 'updates', label: 'Receive product updates', checked: !1 })}
        ${e({ name: 'preferences[]', value: 'offers', label: 'Get special offers and promotions', checked: !1 })}
        ${e({ name: 'preferences[]', value: 'terms', label: 'I accept the terms and conditions', checked: !1 })}
      </div>
    </fieldset>
  `,
  };
var l, p, c;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((l = s.parameters) == null ? void 0 : l.docs),
    source: {
      originalSource: `{
  render: args => checkboxTwig(args),
  args: {
    ...data
  }
}`,
      ...((c = (p = s.parameters) == null ? void 0 : p.docs) == null ? void 0 : c.source),
    },
  },
};
var d, u, y;
i.parameters = {
  ...i.parameters,
  docs: {
    ...((d = i.parameters) == null ? void 0 : d.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; gap: var(--size-6); flex-wrap: wrap; align-items: flex-start;">
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>Enabled</strong>
        \${checkboxTwig({
    name: 'enabled',
    value: '1',
    label: 'Unchecked',
    checked: false,
    disabled: false
  })}
        \${checkboxTwig({
    name: 'enabled',
    value: '2',
    label: 'Checked',
    checked: true,
    disabled: false
  })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>Disabled</strong>
        \${checkboxTwig({
    name: 'disabled',
    value: '1',
    label: 'Unchecked',
    checked: false,
    disabled: true
  })}
        \${checkboxTwig({
    name: 'disabled',
    value: '2',
    label: 'Checked',
    checked: true,
    disabled: true
  })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        <strong>No Label</strong>
        \${checkboxTwig({
    name: 'nolabel',
    value: '1',
    label: '',
    checked: false,
    disabled: false
  })}
        \${checkboxTwig({
    name: 'nolabel',
    value: '2',
    label: '',
    checked: true,
    disabled: false
  })}
      </div>
      <div style="display: flex; flex-direction: column; gap: var(--size-3); max-width: 300px;">
        <strong>Long Label</strong>
        \${checkboxTwig({
    name: 'long',
    value: '1',
    label: 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Cursus posuere et egestas id metus sit amet magna.',
    checked: false
  })}
      </div>
    </div>
  \`
}`,
      ...((y = (u = i.parameters) == null ? void 0 : u.docs) == null ? void 0 : y.source),
    },
  },
};
var g, m, b;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((g = r.parameters) == null ? void 0 : g.docs),
    source: {
      originalSource: `{
  render: () => \`
    <fieldset style="border: 0; padding: 0; margin: 0;">
      <legend style="font-weight: var(--font-weight-600); margin-bottom: var(--size-3);">Select your preferences</legend>
      <div style="display: flex; flex-direction: column; gap: var(--size-3);">
        \${checkboxTwig({
    name: 'preferences[]',
    value: 'newsletter',
    label: 'Subscribe to newsletter',
    checked: true
  })}
        \${checkboxTwig({
    name: 'preferences[]',
    value: 'updates',
    label: 'Receive product updates',
    checked: false
  })}
        \${checkboxTwig({
    name: 'preferences[]',
    value: 'offers',
    label: 'Get special offers and promotions',
    checked: false
  })}
        \${checkboxTwig({
    name: 'preferences[]',
    value: 'terms',
    label: 'I accept the terms and conditions',
    checked: false
  })}
      </div>
    </fieldset>
  \`
}`,
      ...((b = (m = r.parameters) == null ? void 0 : m.docs) == null ? void 0 : b.source),
    },
  },
};
const z = ['Default', 'AllStates', 'Group'];
export { i as AllStates, s as Default, r as Group, z as __namedExportsOrder, $ as default };
