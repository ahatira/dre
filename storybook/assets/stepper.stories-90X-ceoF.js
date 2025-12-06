import { t as g, T as u } from './iframe-D21U4yYN.js';
import { a as v, D as w } from './twig-BPJOkNgt.js';
v(u);
u.cache(!1);
const o = (e) => e,
  y = (e = {}) => {
    const p = g.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/stepper/stepper.twig',
      data: [
        {
          type: 'raw',
          value: `
<ol class="ps-stepper ps-stepper--`,
          position: { start: 39, end: 74 },
        },
        {
          type: 'output',
          position: { start: 74, end: 91 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'orientation',
              match: ['orientation'],
              position: { start: 74, end: 91 },
            },
          ],
        },
        {
          type: 'raw',
          value: `" aria-label="Progress">
  `,
          position: { start: 91, end: 118 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'step',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'steps', match: ['steps'] },
            ],
            position: { start: 118, end: 141 },
            output: [
              {
                type: 'raw',
                value: '    <li class="ps-stepper__item ',
                position: { start: 142, end: 174 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'step', match: ['step'] },
                    { type: 'Twig.expression.type.key.period', key: 'active' },
                  ],
                  position: { start: 174, end: 194 },
                  output: [
                    {
                      type: 'raw',
                      value: 'ps-stepper__item--active',
                      position: { start: 194, end: 218 },
                    },
                  ],
                },
                position: { open: { start: 174, end: 194 }, close: { start: 218, end: 229 } },
              },
              { type: 'raw', value: ' ', position: { start: 229, end: 230 } },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'step', match: ['step'] },
                    { type: 'Twig.expression.type.key.period', key: 'completed' },
                  ],
                  position: { start: 230, end: 253 },
                  output: [
                    {
                      type: 'raw',
                      value: 'ps-stepper__item--completed',
                      position: { start: 253, end: 280 },
                    },
                  ],
                },
                position: { open: { start: 230, end: 253 }, close: { start: 280, end: 291 } },
              },
              {
                type: 'raw',
                value: `">
      <div class="ps-stepper__step-marker">
        `,
                position: { start: 291, end: 346 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'step', match: ['step'] },
                    { type: 'Twig.expression.type.key.period', key: 'completed' },
                  ],
                  position: { start: 346, end: 369 },
                  output: [
                    {
                      type: 'raw',
                      value: `          <svg class="ps-stepper__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" fill="currentColor"/>
          </svg>
        `,
                      position: { start: 370, end: 577 },
                    },
                  ],
                },
                position: { open: { start: 346, end: 369 }, close: { start: 577, end: 587 } },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.else',
                  match: ['else'],
                  position: { start: 577, end: 587 },
                  output: [
                    {
                      type: 'raw',
                      value: '          <span class="ps-stepper__number">',
                      position: { start: 588, end: 631 },
                    },
                    {
                      type: 'output',
                      position: { start: 631, end: 647 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'loop',
                          match: ['loop'],
                          position: { start: 631, end: 647 },
                        },
                        {
                          type: 'Twig.expression.type.key.period',
                          position: { start: 631, end: 647 },
                          key: 'index',
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `</span>
        `,
                      position: { start: 647, end: 663 },
                    },
                  ],
                },
                position: { open: { start: 577, end: 587 }, close: { start: 663, end: 674 } },
              },
              {
                type: 'raw',
                value: `      </div>
      <span class="ps-stepper__label">`,
                position: { start: 675, end: 726 },
              },
              {
                type: 'output',
                position: { start: 726, end: 742 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'step',
                    match: ['step'],
                    position: { start: 726, end: 742 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 726, end: 742 },
                    key: 'label',
                  },
                ],
              },
              {
                type: 'raw',
                value: `</span>
    </li>
  `,
                position: { start: 742, end: 762 },
              },
            ],
          },
          position: { open: { start: 118, end: 141 }, close: { start: 762, end: 774 } },
        },
        {
          type: 'raw',
          value: `</ol>
`,
          position: { start: 775, end: 775 },
        },
      ],
      precompiled: !0,
    });
    p.options.allowInlineIncludes = !0;
    try {
      let t = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(t) || (t = Object.entries(t)), o(p.render({ attributes: new w(t), ...e }))
      );
    } catch (t) {
      return o(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/stepper/stepper.twig: ' +
          t.toString()
      );
    }
  },
  m = {
    steps: [
      { label: 'Step 1', completed: !0, active: !1 },
      { label: 'Step 2', completed: !1, active: !0 },
      { label: 'Step 3', completed: !1, active: !1 },
    ],
    orientation: 'horizontal',
  },
  k = {
    title: 'Components/Stepper',
    tags: ['autodocs'],
    argTypes: {
      steps: {
        description: 'Array of step objects with label, completed, active',
        table: { category: 'Content' },
      },
      orientation: {
        control: { type: 'select' },
        options: ['horizontal', 'vertical'],
        description: 'Step indicator orientation',
        table: { category: 'Display' },
      },
    },
  },
  s = { name: 'Horizontal', render: (e) => y(e), args: { ...m, orientation: 'horizontal' } },
  a = {
    name: 'Vertical',
    render: (e) => y(e),
    args: {
      ...m,
      orientation: 'vertical',
      steps: [
        { label: 'Review', completed: !0, active: !1 },
        { label: 'Confirm', completed: !1, active: !0 },
        { label: 'Submit', completed: !1, active: !1 },
      ],
    },
  };
var r, i, n;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((r = s.parameters) == null ? void 0 : r.docs),
    source: {
      originalSource: `{
  name: 'Horizontal',
  render: args => markup(args),
  args: {
    ...data,
    orientation: 'horizontal'
  }
}`,
      ...((n = (i = s.parameters) == null ? void 0 : i.docs) == null ? void 0 : n.source),
    },
  },
};
var l, c, d;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((l = a.parameters) == null ? void 0 : l.docs),
    source: {
      originalSource: `{
  name: 'Vertical',
  render: args => markup(args),
  args: {
    ...data,
    orientation: 'vertical',
    steps: [{
      label: 'Review',
      completed: true,
      active: false
    }, {
      label: 'Confirm',
      completed: false,
      active: true
    }, {
      label: 'Submit',
      completed: false,
      active: false
    }]
  }
}`,
      ...((d = (c = a.parameters) == null ? void 0 : c.docs) == null ? void 0 : d.source),
    },
  },
};
const T = ['Default', 'Vertical'];
export { s as Default, a as Vertical, T as __namedExportsOrder, k as default };
