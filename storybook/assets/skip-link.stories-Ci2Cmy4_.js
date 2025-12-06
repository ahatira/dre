import { t as m, T as v } from './iframe-D21U4yYN.js';
import { D as c, a as k } from './twig-BPJOkNgt.js';
k(v);
v.cache(!1);
const n = (e) => e,
  i = (e = {}) => {
    const s = m.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/elements/skip-link/skip-link.twig',
      data: [
        { type: 'raw', value: '', position: { start: 361, end: 363 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'targetId',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'targetId', match: ['targetId'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'main-content' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 363, end: 418 },
          },
          position: { start: 363, end: 418 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'label',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'Skip to main content' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 419, end: 476 },
          },
          position: { start: 419, end: 476 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'href',
            expression: [
              { type: 'Twig.expression.type.string', value: '#' },
              { type: 'Twig.expression.type.variable', value: 'targetId', match: ['targetId'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
            ],
            position: { start: 477, end: 510 },
          },
          position: { start: 477, end: 510 },
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
            position: { start: 511, end: 574 },
          },
          position: { start: 511, end: 574 },
        },
        { type: 'raw', value: '', position: { start: 575, end: 576 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-skip-link' },
              { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
            ],
            position: { start: 576, end: 614 },
          },
          position: { start: 576, end: 614 },
        },
        { type: 'raw', value: '<a ', position: { start: 615, end: 619 } },
        {
          type: 'output',
          position: { start: 619, end: 653 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 619, end: 653 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 619, end: 653 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 619, end: 653 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 619, end: 653 },
                },
              ],
            },
          ],
        },
        { type: 'raw', value: ' href="', position: { start: 653, end: 660 } },
        {
          type: 'output',
          position: { start: 660, end: 670 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'href',
              match: ['href'],
              position: { start: 660, end: 670 },
            },
          ],
        },
        { type: 'raw', value: '">', position: { start: 670, end: 672 } },
        {
          type: 'output',
          position: { start: 672, end: 683 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'label',
              match: ['label'],
              position: { start: 672, end: 683 },
            },
          ],
        },
        { type: 'raw', value: '</a>', position: { start: 683, end: 683 } },
      ],
      precompiled: !0,
    });
    s.options.allowInlineIncludes = !0;
    try {
      let t = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(t) || (t = Object.entries(t)), n(s.render({ attributes: new c(t), ...e }))
      );
    } catch (t) {
      return n(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/skip-link/skip-link.twig: ' +
          t.toString()
      );
    }
  },
  u = { targetId: 'main-content', label: 'Skip to main content' },
  f = {
    title: 'Elements/Skip Link',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component: `WCAG “Skip to content” link for keyboard navigation.
Hidden by default, appears on focus; targets an in-page anchor by id.`,
        },
      },
    },
    argTypes: {
      label: {
        description:
          'Link text displayed to user (e.g., "Skip to main content", "Skip to navigation")',
        control: { type: 'text' },
        table: {
          category: 'Content',
          type: { summary: 'string', required: !0 },
          defaultValue: { summary: 'Skip to main content' },
        },
      },
      targetId: {
        description:
          'ID of the target anchor element (must exist in page, e.g., main-content, navigation, search)',
        control: { type: 'text' },
        table: {
          category: 'Link',
          type: { summary: 'string', required: !0 },
          defaultValue: { summary: 'main-content' },
        },
      },
    },
    args: { ...u },
  },
  a = { render: (e) => i(e), args: { ...u } },
  r = {
    render: () => `
    <div style="position: relative; padding: 4rem 1rem 1rem; border: 2px dashed var(--gray-300); min-height: 200px;">
      <p style="position: absolute; top: var(--size-4); left: var(--size-4); margin: 0; font-size: var(--font-size-0); color: var(--gray-600);">
        👆 Press Tab to see the skip link appear at top-left
      </p>
      ${i({ targetId: 'main-content', label: 'Skip to main content' })}
      <div style="margin-top: var(--size-8);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Multiple Skip Links Example</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-2);">
          ${i({ targetId: 'main-content', label: 'Skip to main content' })}
          ${i({ targetId: 'navigation', label: 'Skip to navigation' })}
          ${i({ targetId: 'search', label: 'Skip to search' })}
        </div>
      </div>
      <div style="margin-top: var(--size-6);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Implementation Guidelines</h3>
        <ul style="margin: 0; padding-left: var(--size-6); font-size: var(--font-size-1);">
          <li>Must be the first focusable element on the page</li>
          <li>Allows keyboard users to bypass repetitive navigation</li>
          <li>Required for WCAG 2.2 criterion 2.4.1 (Level A)</li>
          <li>Visible only on keyboard focus, hidden otherwise</li>
          <li>Target element must have matching id attribute</li>
        </ul>
      </div>
      <div id="main-content" style="margin-top: var(--size-8); padding: var(--size-4); background: var(--gray-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Main Content Area</strong> - Skip link points here (id="main-content")</p>
      </div>
      <div id="navigation" style="margin-top: var(--size-4); padding: var(--size-4); background: var(--blue-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Navigation Area</strong> - Alternative target (id="navigation")</p>
      </div>
      <div id="search" style="margin-top: var(--size-4); padding: var(--size-4); background: var(--green-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Search Area</strong> - Alternative target (id="search")</p>
      </div>
    </div>
  `,
  };
var o, p, l;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((o = a.parameters) == null ? void 0 : o.docs),
    source: {
      originalSource: `{
  render: args => skipLinkTwig(args),
  args: {
    ...data
  }
}`,
      ...((l = (p = a.parameters) == null ? void 0 : p.docs) == null ? void 0 : l.source),
    },
  },
};
var d, g, y;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((d = r.parameters) == null ? void 0 : d.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="position: relative; padding: 4rem 1rem 1rem; border: 2px dashed var(--gray-300); min-height: 200px;">
      <p style="position: absolute; top: var(--size-4); left: var(--size-4); margin: 0; font-size: var(--font-size-0); color: var(--gray-600);">
        👆 Press Tab to see the skip link appear at top-left
      </p>
      \${skipLinkTwig({
    targetId: 'main-content',
    label: 'Skip to main content'
  })}
      <div style="margin-top: var(--size-8);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Multiple Skip Links Example</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-2);">
          \${skipLinkTwig({
    targetId: 'main-content',
    label: 'Skip to main content'
  })}
          \${skipLinkTwig({
    targetId: 'navigation',
    label: 'Skip to navigation'
  })}
          \${skipLinkTwig({
    targetId: 'search',
    label: 'Skip to search'
  })}
        </div>
      </div>
      <div style="margin-top: var(--size-6);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Implementation Guidelines</h3>
        <ul style="margin: 0; padding-left: var(--size-6); font-size: var(--font-size-1);">
          <li>Must be the first focusable element on the page</li>
          <li>Allows keyboard users to bypass repetitive navigation</li>
          <li>Required for WCAG 2.2 criterion 2.4.1 (Level A)</li>
          <li>Visible only on keyboard focus, hidden otherwise</li>
          <li>Target element must have matching id attribute</li>
        </ul>
      </div>
      <div id="main-content" style="margin-top: var(--size-8); padding: var(--size-4); background: var(--gray-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Main Content Area</strong> - Skip link points here (id="main-content")</p>
      </div>
      <div id="navigation" style="margin-top: var(--size-4); padding: var(--size-4); background: var(--blue-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Navigation Area</strong> - Alternative target (id="navigation")</p>
      </div>
      <div id="search" style="margin-top: var(--size-4); padding: var(--size-4); background: var(--green-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Search Area</strong> - Alternative target (id="search")</p>
      </div>
    </div>
  \`
}`,
      ...((y = (g = r.parameters) == null ? void 0 : g.docs) == null ? void 0 : y.source),
    },
  },
};
const w = ['Default', 'UseCases'];
export { a as Default, r as UseCases, w as __namedExportsOrder, f as default };
