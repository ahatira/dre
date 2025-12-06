import { t as F, T as O } from './iframe-D21U4yYN.js';
import { a as M, D as W } from './twig-BPJOkNgt.js';
import './icon-DB6aCijn.js';
M(O);
O.cache(!1);
F.twig({
  id: '@elements/icon/icon.twig',
  data: [
    {
      type: 'raw',
      value: `\r
`,
      position: { start: 54, end: 56 },
    },
    {
      type: 'raw',
      value: `\r
`,
      position: { start: 242, end: 244 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'name',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'name', match: ['name'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'default',
            match: ['|default', 'default'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.string', value: 'search' },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
        ],
        position: { start: 244, end: 283 },
      },
      position: { start: 244, end: 283 },
    },
    {
      type: 'raw',
      value: `\r
`,
      position: { start: 283, end: 285 },
    },
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
              { type: 'Twig.expression.type.string', value: 'md' },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
        ],
        position: { start: 285, end: 320 },
      },
      position: { start: 285, end: 320 },
    },
    {
      type: 'raw',
      value: `\r
`,
      position: { start: 320, end: 322 },
    },
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
        position: { start: 322, end: 364 },
      },
      position: { start: 322, end: 364 },
    },
    {
      type: 'raw',
      value: `\r
`,
      position: { start: 364, end: 366 },
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
        position: { start: 366, end: 410 },
      },
      position: { start: 366, end: 410 },
    },
    {
      type: 'raw',
      value: `\r
`,
      position: { start: 410, end: 412 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'baseClass',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'default',
            match: ['|default', 'default'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
        ],
        position: { start: 412, end: 457 },
      },
      position: { start: 412, end: 457 },
    },
    {
      type: 'raw',
      value: `\r
\r
`,
      position: { start: 457, end: 461 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'classes',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          { type: 'Twig.expression.type.comma' },
          { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
          { type: 'Twig.expression.type.string', value: 'md' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '!=',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '!=',
          },
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          { type: 'Twig.expression.type.string', value: '--' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '~',
            precidence: 6,
            associativity: 'leftToRight',
            operator: '~',
          },
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
          { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
          { type: 'Twig.expression.type.string', value: 'default' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '!=',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '!=',
          },
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          { type: 'Twig.expression.type.string', value: '--' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '~',
            precidence: 6,
            associativity: 'leftToRight',
            operator: '~',
          },
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
          { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          { type: 'Twig.expression.type.string', value: '--disabled' },
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
          { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
          { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
          { type: 'Twig.expression.type.string', value: 'ps-icon' },
          { type: 'Twig.expression.type.comma' },
          { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
          { type: 'Twig.expression.type.string', value: 'md' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '!=',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '!=',
          },
          { type: 'Twig.expression.type.string', value: 'ps-icon--' },
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
          { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
          { type: 'Twig.expression.type.string', value: 'default' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '!=',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '!=',
          },
          { type: 'Twig.expression.type.string', value: 'ps-icon--' },
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
          { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
          { type: 'Twig.expression.type.string', value: 'ps-icon--disabled' },
          { type: 'Twig.expression.type.null', value: null },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
          { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
        ],
        position: { start: 461, end: 823 },
      },
      position: { start: 461, end: 823 },
    },
    {
      type: 'raw',
      value: `\r
\r
<span class="`,
      position: { start: 823, end: 840 },
    },
    {
      type: 'output',
      position: { start: 840, end: 868 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'classes',
          match: ['classes'],
          position: { start: 840, end: 868 },
        },
        {
          type: 'Twig.expression.type.filter',
          value: 'join',
          match: ['|join', 'join'],
          position: { start: 840, end: 868 },
          params: [
            {
              type: 'Twig.expression.type.parameter.start',
              value: '(',
              match: ['('],
              position: { start: 840, end: 868 },
            },
            { type: 'Twig.expression.type.string', value: ' ', position: { start: 840, end: 868 } },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 840, end: 868 },
              expression: !1,
            },
          ],
        },
        {
          type: 'Twig.expression.type.filter',
          value: 'trim',
          match: ['|trim', 'trim'],
          position: { start: 840, end: 868 },
        },
      ],
    },
    { type: 'raw', value: '"', position: { start: 868, end: 869 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'ariaLabel', match: ['ariaLabel'] },
        ],
        position: { start: 869, end: 887 },
        output: [
          { type: 'raw', value: ' aria-label="', position: { start: 887, end: 900 } },
          {
            type: 'output',
            position: { start: 900, end: 915 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'ariaLabel',
                match: ['ariaLabel'],
                position: { start: 900, end: 915 },
              },
            ],
          },
          { type: 'raw', value: '" role="img"', position: { start: 915, end: 927 } },
        ],
      },
      position: { open: { start: 869, end: 887 }, close: { start: 927, end: 937 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.else',
        match: ['else'],
        position: { start: 927, end: 937 },
        output: [{ type: 'raw', value: ' aria-hidden="true"', position: { start: 937, end: 956 } }],
      },
      position: { open: { start: 927, end: 937 }, close: { start: 956, end: 967 } },
    },
    {
      type: 'raw',
      value: `>\r
  <span class="ps-icon__icon" data-icon="`,
      position: { start: 967, end: 1011 },
    },
    {
      type: 'output',
      position: { start: 1011, end: 1021 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'name',
          match: ['name'],
          position: { start: 1011, end: 1021 },
        },
      ],
    },
    {
      type: 'raw',
      value: `"></span>\r
</span>\r
`,
      position: { start: 1021, end: 1021 },
    },
  ],
  precompiled: !0,
});
const d = (e) => e,
  t = (e = {}) => {
    const u = F.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/breadcrumb/breadcrumb.twig',
      data: [
        {
          type: 'raw',
          value: `\r
\r
`,
          position: { start: 447, end: 451 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'compact',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'compact', match: ['compact'] },
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
            position: { start: 451, end: 493 },
          },
          position: { start: 451, end: 493 },
        },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 493, end: 495 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'truncate',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'truncate', match: ['truncate'] },
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
            position: { start: 495, end: 539 },
          },
          position: { start: 495, end: 539 },
        },
        {
          type: 'raw',
          value: `\r
\r
`,
          position: { start: 539, end: 543 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-breadcrumb' },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'compact', match: ['compact'] },
              { type: 'Twig.expression.type.string', value: 'ps-breadcrumb--compact' },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'truncate', match: ['truncate'] },
              { type: 'Twig.expression.type.string', value: 'ps-breadcrumb--truncate' },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
              { type: 'Twig.expression.type.string', value: 'ps-breadcrumb--' },
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
              { type: 'Twig.expression.type.string', value: 'ps-breadcrumb--' },
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
              { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
            ],
            position: { start: 543, end: 768 },
          },
          position: { start: 543, end: 768 },
        },
        {
          type: 'raw',
          value: `\r
\r
<nav `,
          position: { start: 768, end: 777 },
        },
        {
          type: 'output',
          position: { start: 777, end: 863 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 777, end: 863 },
            },
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 777, end: 863 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 777, end: 863 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 777, end: 863 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 777, end: 863 },
                },
              ],
            },
            {
              type: 'Twig.expression.type._function',
              position: { start: 777, end: 863 },
              fn: 'create_attribute',
              params: [
                {
                  type: 'Twig.expression.type.parameter.start',
                  value: '(',
                  match: ['('],
                  position: { start: 777, end: 863 },
                },
                {
                  type: 'Twig.expression.type.parameter.end',
                  value: ')',
                  match: [')'],
                  position: { start: 777, end: 863 },
                  expression: !1,
                },
              ],
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 777, end: 863 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 777, end: 863 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 777, end: 863 },
                },
              ],
            },
            {
              type: 'Twig.expression.type.operator.binary',
              value: '?',
              position: { start: 777, end: 863 },
              precidence: 16,
              associativity: 'rightToLeft',
              operator: '?',
            },
          ],
        },
        {
          type: 'raw',
          value: ` aria-label="Breadcrumb">\r
  <ol class="ps-breadcrumb__list">\r
    `,
          position: { start: 863, end: 930 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'item',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'items', match: ['items'] },
            ],
            position: { start: 930, end: 953 },
            output: [
              {
                type: 'raw',
                value: `\r
      `,
                position: { start: 953, end: 961 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.set',
                  key: 'is_last',
                  expression: [
                    { type: 'Twig.expression.type.variable', value: 'loop', match: ['loop'] },
                    { type: 'Twig.expression.type.key.period', key: 'last' },
                  ],
                  position: { start: 961, end: 990 },
                },
                position: { start: 961, end: 990 },
              },
              {
                type: 'raw',
                value: `\r
      \r
      `,
                position: { start: 990, end: 1006 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.set',
                  key: 'item_classes',
                  expression: [
                    { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
                    { type: 'Twig.expression.type.string', value: 'ps-breadcrumb__item' },
                    { type: 'Twig.expression.type.comma' },
                    { type: 'Twig.expression.type.variable', value: 'is_last', match: ['is_last'] },
                    { type: 'Twig.expression.type.string', value: 'ps-breadcrumb__item--current' },
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
                  position: { start: 1006, end: 1130 },
                },
                position: { start: 1006, end: 1130 },
              },
              {
                type: 'raw',
                value: `\r
      <li class="`,
                position: { start: 1130, end: 1149 },
              },
              {
                type: 'output',
                position: { start: 1149, end: 1177 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'item_classes',
                    match: ['item_classes'],
                    position: { start: 1149, end: 1177 },
                  },
                  {
                    type: 'Twig.expression.type.filter',
                    value: 'join',
                    match: ['|join', 'join'],
                    position: { start: 1149, end: 1177 },
                    params: [
                      {
                        type: 'Twig.expression.type.parameter.start',
                        value: '(',
                        match: ['('],
                        position: { start: 1149, end: 1177 },
                      },
                      {
                        type: 'Twig.expression.type.string',
                        value: ' ',
                        position: { start: 1149, end: 1177 },
                      },
                      {
                        type: 'Twig.expression.type.parameter.end',
                        value: ')',
                        match: [')'],
                        position: { start: 1149, end: 1177 },
                        expression: !1,
                      },
                    ],
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1177, end: 1178 } },
              {
                type: 'output',
                position: { start: 1178, end: 1221 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'is_last',
                    match: ['is_last'],
                    position: { start: 1178, end: 1221 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: ' aria-current="page"',
                    position: { start: 1178, end: 1221 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: '',
                    position: { start: 1178, end: 1221 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '?',
                    position: { start: 1178, end: 1221 },
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: '?',
                  },
                ],
              },
              {
                type: 'raw',
                value: `>\r
        `,
                position: { start: 1221, end: 1232 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'is_last', match: ['is_last'] },
                    {
                      type: 'Twig.expression.type.operator.unary',
                      value: 'not',
                      precidence: 3,
                      associativity: 'rightToLeft',
                      operator: 'not',
                    },
                    { type: 'Twig.expression.type.variable', value: 'item', match: ['item'] },
                    { type: 'Twig.expression.type.key.period', key: 'url' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: 'and',
                      precidence: 13,
                      associativity: 'leftToRight',
                      operator: 'and',
                    },
                  ],
                  position: { start: 1232, end: 1265 },
                  output: [
                    {
                      type: 'raw',
                      value: `\r
          <a class="ps-breadcrumb__link" href="`,
                      position: { start: 1265, end: 1314 },
                    },
                    {
                      type: 'output',
                      position: { start: 1314, end: 1328 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'item',
                          match: ['item'],
                          position: { start: 1314, end: 1328 },
                        },
                        {
                          type: 'Twig.expression.type.key.period',
                          position: { start: 1314, end: 1328 },
                          key: 'url',
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `">\r
            `,
                      position: { start: 1328, end: 1344 },
                    },
                    {
                      type: 'logic',
                      token: {
                        type: 'Twig.logic.type.if',
                        stack: [
                          { type: 'Twig.expression.type.variable', value: 'item', match: ['item'] },
                          { type: 'Twig.expression.type.key.period', key: 'icon' },
                        ],
                        position: { start: 1344, end: 1362 },
                        output: [
                          {
                            type: 'raw',
                            value: `\r
              `,
                            position: { start: 1362, end: 1378 },
                          },
                          {
                            type: 'logic',
                            token: {
                              type: 'Twig.logic.type.include',
                              only: 4,
                              ignoreMissing: !1,
                              stack: [
                                {
                                  type: 'Twig.expression.type.string',
                                  value: '@elements/icon/icon.twig',
                                },
                              ],
                              withStack: [
                                {
                                  type: 'Twig.expression.type.object.start',
                                  value: '{',
                                  match: ['{'],
                                },
                                {
                                  type: 'Twig.expression.type.operator.binary',
                                  value: ':',
                                  precidence: 16,
                                  associativity: 'rightToLeft',
                                  operator: ':',
                                  key: 'name',
                                },
                                {
                                  type: 'Twig.expression.type.variable',
                                  value: 'item',
                                  match: ['item'],
                                },
                                { type: 'Twig.expression.type.key.period', key: 'icon' },
                                { type: 'Twig.expression.type.comma' },
                                {
                                  type: 'Twig.expression.type.operator.binary',
                                  value: ':',
                                  precidence: 16,
                                  associativity: 'rightToLeft',
                                  operator: ':',
                                  key: 'size',
                                },
                                { type: 'Twig.expression.type.string', value: 'small' },
                                {
                                  type: 'Twig.expression.type.object.end',
                                  value: '}',
                                  match: ['}'],
                                },
                              ],
                              position: { start: 1378, end: 1512 },
                            },
                            position: { start: 1378, end: 1512 },
                          },
                          {
                            type: 'raw',
                            value: `\r
            `,
                            position: { start: 1512, end: 1526 },
                          },
                        ],
                      },
                      position: {
                        open: { start: 1344, end: 1362 },
                        close: { start: 1526, end: 1537 },
                      },
                    },
                    {
                      type: 'raw',
                      value: `\r
            `,
                      position: { start: 1537, end: 1551 },
                    },
                    {
                      type: 'output',
                      position: { start: 1551, end: 1567 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'item',
                          match: ['item'],
                          position: { start: 1551, end: 1567 },
                        },
                        {
                          type: 'Twig.expression.type.key.period',
                          position: { start: 1551, end: 1567 },
                          key: 'label',
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `\r
          </a>\r
        `,
                      position: { start: 1567, end: 1593 },
                    },
                  ],
                },
                position: { open: { start: 1232, end: 1265 }, close: { start: 1593, end: 1603 } },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.else',
                  match: ['else'],
                  position: { start: 1593, end: 1603 },
                  output: [
                    {
                      type: 'raw',
                      value: `\r
          `,
                      position: { start: 1603, end: 1615 },
                    },
                    {
                      type: 'output',
                      position: { start: 1615, end: 1631 },
                      stack: [
                        {
                          type: 'Twig.expression.type.variable',
                          value: 'item',
                          match: ['item'],
                          position: { start: 1615, end: 1631 },
                        },
                        {
                          type: 'Twig.expression.type.key.period',
                          position: { start: 1615, end: 1631 },
                          key: 'label',
                        },
                      ],
                    },
                    {
                      type: 'raw',
                      value: `\r
        `,
                      position: { start: 1631, end: 1641 },
                    },
                  ],
                },
                position: { open: { start: 1593, end: 1603 }, close: { start: 1641, end: 1652 } },
              },
              {
                type: 'raw',
                value: `\r
      </li>\r
    `,
                position: { start: 1652, end: 1671 },
              },
            ],
          },
          position: { open: { start: 930, end: 953 }, close: { start: 1671, end: 1683 } },
        },
        {
          type: 'raw',
          value: `\r
  </ol>\r
</nav>\r
`,
          position: { start: 1683, end: 1683 },
        },
      ],
      precompiled: !0,
    });
    u.options.allowInlineIncludes = !0;
    try {
      let a = e.defaultAttributes ? e.defaultAttributes : [];
      return (
        Array.isArray(a) || (a = Object.entries(a)), d(u.render({ attributes: new W(a), ...e }))
      );
    } catch (a) {
      return d(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/breadcrumb/breadcrumb.twig: ' +
          a.toString()
      );
    }
  },
  q = {
    items: [
      { label: 'Home', url: '/' },
      { label: 'Locations', url: '/locations' },
      { label: 'Paris 15e', url: '/locations/paris-15' },
      { label: 'Family Apartment' },
    ],
    compact: !1,
    truncate: !1,
  },
  Q = {
    title: 'Components/Breadcrumb',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component:
            'Breadcrumb shows the page hierarchy with semantic, accessible markup. Improves navigation and SEO with clear current-page indication.',
        },
      },
    },
    argTypes: {
      items: {
        description: 'Array of breadcrumb items with label, optional url, and optional icon',
        control: { type: 'object' },
        table: {
          category: 'Content',
          type: { summary: 'array<{label: string, url?: string, icon?: string}>' },
        },
      },
      compact: {
        description: 'Enable compact spacing (reduced font size and gaps)',
        control: { type: 'boolean' },
        table: {
          category: 'Appearance',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      truncate: {
        description: 'Enable CSS text truncation for long labels',
        control: { type: 'boolean' },
        table: {
          category: 'Appearance',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
    },
  },
  r = { render: (e) => t(e), args: { ...q } },
  i = {
    render: () =>
      t({
        items: [
          { label: 'Home', url: '/', icon: 'home' },
          { label: 'Locations', url: '/locations', icon: 'map' },
          { label: 'Paris 15e', url: '/locations/paris-15', icon: 'building' },
          { label: 'Family Apartment' },
        ],
      }),
  },
  s = {
    render: () =>
      t({
        items: [
          { label: 'Home', url: '/' },
          { label: 'Products', url: '/products' },
          { label: 'Electronics', url: '/products/electronics' },
          { label: 'Smartphones' },
        ],
        compact: !0,
      }),
  },
  o = {
    render: () =>
      t({
        items: [
          { label: 'Home', url: '/' },
          { label: 'Very Long Category Name That Should Be Truncated', url: '/category' },
          { label: 'Another Extremely Long Subcategory Name', url: '/category/subcategory' },
          { label: 'Final Item with Very Long Name' },
        ],
        truncate: !0,
      }),
  },
  p = { render: () => t({ items: [{ label: 'Home', url: '/' }, { label: 'Current Page' }] }) },
  n = {
    render: () =>
      t({
        items: [
          { label: 'Home', url: '/' },
          { label: 'Real Estate', url: '/real-estate' },
          { label: 'Commercial', url: '/real-estate/commercial' },
          { label: 'Offices', url: '/real-estate/commercial/offices' },
          { label: 'Paris', url: '/real-estate/commercial/offices/paris' },
          { label: '8th District', url: '/real-estate/commercial/offices/paris/8th' },
          { label: 'Champs-Élysées Building' },
        ],
      }),
  },
  l = {
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Standard</h3>
        ${t({ items: [{ label: 'Home', url: '/' }, { label: 'Locations', url: '/locations' }, { label: 'Paris 15e' }] })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Compact</h3>
        ${t({ items: [{ label: 'Home', url: '/' }, { label: 'Locations', url: '/locations' }, { label: 'Paris 15e' }], compact: !0 })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">With Icons</h3>
        ${t({ items: [{ label: 'Home', url: '/', icon: 'home' }, { label: 'Products', url: '/products', icon: 'grid' }, { label: 'Laptop' }] })}
      </div>
      <div style="max-width: 400px; border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Truncated (narrow container)</h3>
        ${t({ items: [{ label: 'Home', url: '/' }, { label: 'Long Category Name', url: '/category' }, { label: 'Very Long Subcategory Name' }], truncate: !0 })}
      </div>
    </div>
  `,
  },
  y = {
    render: () => `
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: var(--size-6);">
      ${['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger', 'dark', 'light']
        .map(
          (e) => `
        <div style="border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
          <h4 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-1); color: var(--gray-700);">Color: ${e}</h4>
          ${t({ items: [{ label: 'Home', url: '/' }, { label: 'Locations', url: '/locations' }, { label: 'Paris 15e' }], color: e })}
        </div>
      `
        )
        .join('')}
    </div>
  `,
  },
  c = {
    render: () => `
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: var(--size-6);">
      ${['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
        .map(
          (e) => `
        <div style="border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
          <h4 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-1); color: var(--gray-700);">Size: ${e}</h4>
          ${t({ items: [{ label: 'Home', url: '/' }, { label: 'Products', url: '/products' }, { label: 'Electronics' }], size: e })}
        </div>
      `
        )
        .join('')}
    </div>
  `,
  };
var m, g, v;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((m = r.parameters) == null ? void 0 : m.docs),
    source: {
      originalSource: `{
  render: args => breadcrumbTwig(args),
  args: {
    ...breadcrumbData
  }
}`,
      ...((v = (g = r.parameters) == null ? void 0 : g.docs) == null ? void 0 : v.source),
    },
  },
};
var b, w, T;
i.parameters = {
  ...i.parameters,
  docs: {
    ...((b = i.parameters) == null ? void 0 : b.docs),
    source: {
      originalSource: `{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/',
      icon: 'home'
    }, {
      label: 'Locations',
      url: '/locations',
      icon: 'map'
    }, {
      label: 'Paris 15e',
      url: '/locations/paris-15',
      icon: 'building'
    }, {
      label: 'Family Apartment'
    }]
  })
}`,
      ...((T = (w = i.parameters) == null ? void 0 : w.docs) == null ? void 0 : T.source),
    },
  },
};
var x, h, f;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((x = s.parameters) == null ? void 0 : x.docs),
    source: {
      originalSource: `{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Products',
      url: '/products'
    }, {
      label: 'Electronics',
      url: '/products/electronics'
    }, {
      label: 'Smartphones'
    }],
    compact: true
  })
}`,
      ...((f = (h = s.parameters) == null ? void 0 : h.docs) == null ? void 0 : f.source),
    },
  },
};
var z, k, L;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((z = o.parameters) == null ? void 0 : z.docs),
    source: {
      originalSource: `{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Very Long Category Name That Should Be Truncated',
      url: '/category'
    }, {
      label: 'Another Extremely Long Subcategory Name',
      url: '/category/subcategory'
    }, {
      label: 'Final Item with Very Long Name'
    }],
    truncate: true
  })
}`,
      ...((L = (k = o.parameters) == null ? void 0 : k.docs) == null ? void 0 : L.source),
    },
  },
};
var C, S, _;
p.parameters = {
  ...p.parameters,
  docs: {
    ...((C = p.parameters) == null ? void 0 : C.docs),
    source: {
      originalSource: `{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Current Page'
    }]
  })
}`,
      ...((_ = (S = p.parameters) == null ? void 0 : S.docs) == null ? void 0 : _.source),
    },
  },
};
var H, $, P;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((H = n.parameters) == null ? void 0 : H.docs),
    source: {
      originalSource: `{
  render: () => breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Real Estate',
      url: '/real-estate'
    }, {
      label: 'Commercial',
      url: '/real-estate/commercial'
    }, {
      label: 'Offices',
      url: '/real-estate/commercial/offices'
    }, {
      label: 'Paris',
      url: '/real-estate/commercial/offices/paris'
    }, {
      label: '8th District',
      url: '/real-estate/commercial/offices/paris/8th'
    }, {
      label: 'Champs-Élysées Building'
    }]
  })
}`,
      ...((P = ($ = n.parameters) == null ? void 0 : $.docs) == null ? void 0 : P.source),
    },
  },
};
var R, A, j;
l.parameters = {
  ...l.parameters,
  docs: {
    ...((R = l.parameters) == null ? void 0 : R.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Standard</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Locations',
      url: '/locations'
    }, {
      label: 'Paris 15e'
    }]
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Compact</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Locations',
      url: '/locations'
    }, {
      label: 'Paris 15e'
    }],
    compact: true
  })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">With Icons</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/',
      icon: 'home'
    }, {
      label: 'Products',
      url: '/products',
      icon: 'grid'
    }, {
      label: 'Laptop'
    }]
  })}
      </div>
      <div style="max-width: 400px; border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-2); color: var(--gray-700);">Truncated (narrow container)</h3>
        \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Long Category Name',
      url: '/category'
    }, {
      label: 'Very Long Subcategory Name'
    }],
    truncate: true
  })}
      </div>
    </div>
  \`
}`,
      ...((j = (A = l.parameters) == null ? void 0 : A.docs) == null ? void 0 : j.source),
    },
  },
};
var E, V, D;
y.parameters = {
  ...y.parameters,
  docs: {
    ...((E = y.parameters) == null ? void 0 : E.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: var(--size-6);">
      \${['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger', 'dark', 'light'].map(color => \`
        <div style="border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
          <h4 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-1); color: var(--gray-700);">Color: \${color}</h4>
          \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Locations',
      url: '/locations'
    }, {
      label: 'Paris 15e'
    }],
    color
  })}
        </div>
      \`).join('')}
    </div>
  \`
}`,
      ...((D = (V = y.parameters) == null ? void 0 : V.docs) == null ? void 0 : D.source),
    },
  },
};
var N, I, B;
c.parameters = {
  ...c.parameters,
  docs: {
    ...((N = c.parameters) == null ? void 0 : N.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: var(--size-6);">
      \${['xs', 'sm', 'md', 'lg', 'xl', 'xxl'].map(size => \`
        <div style="border: 1px solid var(--gray-200); padding: var(--size-4); border-radius: var(--radius-2);">
          <h4 style="margin: 0 0 var(--size-3) 0; font-family: var(--font-sans); font-size: var(--font-size-1); color: var(--gray-700);">Size: \${size}</h4>
          \${breadcrumbTwig({
    items: [{
      label: 'Home',
      url: '/'
    }, {
      label: 'Products',
      url: '/products'
    }, {
      label: 'Electronics'
    }],
    size
  })}
        </div>
      \`).join('')}
    </div>
  \`
}`,
      ...((B = (I = c.parameters) == null ? void 0 : I.docs) == null ? void 0 : B.source),
    },
  },
};
const U = [
  'Default',
  'WithIcons',
  'Compact',
  'Truncated',
  'Simple',
  'Deep',
  'ShowcaseVariants',
  'ShowcaseColors',
  'ShowcaseSizes',
];
export {
  s as Compact,
  n as Deep,
  r as Default,
  y as ShowcaseColors,
  c as ShowcaseSizes,
  l as ShowcaseVariants,
  p as Simple,
  o as Truncated,
  i as WithIcons,
  U as __namedExportsOrder,
  Q as default,
};
