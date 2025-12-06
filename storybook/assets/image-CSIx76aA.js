import { t as p, T as s } from './iframe-D21U4yYN.js';
import { a as o, D as r } from './twig-BPJOkNgt.js';
o(s);
s.cache(!1);
const a = (t) => t,
  l = (t = {}) => {
    const i = p.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/elements/image/image.twig',
      data: [
        {
          type: 'raw',
          value: `\r
\r
`,
          position: { start: 1253, end: 1257 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'fit',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'fit', match: ['fit'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'none' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1257, end: 1292 },
          },
          position: { start: 1257, end: 1292 },
        },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 1292, end: 1294 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'rounded',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'rounded', match: ['rounded'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'none' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1294, end: 1337 },
          },
          position: { start: 1294, end: 1337 },
        },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 1337, end: 1339 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'loading',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'loading', match: ['loading'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'lazy' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1339, end: 1382 },
          },
          position: { start: 1339, end: 1382 },
        },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 1382, end: 1384 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'decoding',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'decoding', match: ['decoding'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'auto' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1384, end: 1429 },
          },
          position: { start: 1384, end: 1429 },
        },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 1429, end: 1431 },
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
            position: { start: 1431, end: 1476 },
          },
          position: { start: 1431, end: 1476 },
        },
        {
          type: 'raw',
          value: `\r
\r
`,
          position: { start: 1476, end: 1480 },
        },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 1558, end: 1560 },
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
              { type: 'Twig.expression.type.variable', value: 'fit', match: ['fit'] },
              { type: 'Twig.expression.type.string', value: 'none' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '!=',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '!=',
              },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--fit-' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.variable', value: 'fit', match: ['fit'] },
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
              { type: 'Twig.expression.type.variable', value: 'rounded', match: ['rounded'] },
              { type: 'Twig.expression.type.string', value: 'none' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '!=',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '!=',
              },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--rounded-' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.variable', value: 'rounded', match: ['rounded'] },
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
              { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-image' },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'fit', match: ['fit'] },
              { type: 'Twig.expression.type.string', value: 'none' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '!=',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '!=',
              },
              { type: 'Twig.expression.type.string', value: 'ps-image--fit-' },
              { type: 'Twig.expression.type.variable', value: 'fit', match: ['fit'] },
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
              { type: 'Twig.expression.type.variable', value: 'rounded', match: ['rounded'] },
              { type: 'Twig.expression.type.string', value: 'none' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '!=',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '!=',
              },
              { type: 'Twig.expression.type.string', value: 'ps-image--rounded-' },
              { type: 'Twig.expression.type.variable', value: 'rounded', match: ['rounded'] },
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
              { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 1560, end: 1864 },
          },
          position: { start: 1560, end: 1864 },
        },
        {
          type: 'raw',
          value: `\r
\r
<img`,
          position: { start: 1864, end: 1876 },
        },
        {
          type: 'output_whitespace_both',
          position: { start: 1876, end: 1912 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 1876, end: 1912 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 1876, end: 1912 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 1876, end: 1912 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 1876, end: 1912 },
                },
              ],
            },
          ],
        },
        { type: 'raw', value: 'src="', position: { start: 1912, end: 1921 } },
        {
          type: 'output',
          position: { start: 1921, end: 1930 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'src',
              match: ['src'],
              position: { start: 1921, end: 1930 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"\r
  alt="`,
          position: { start: 1930, end: 1940 },
        },
        {
          type: 'output',
          position: { start: 1940, end: 1949 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'alt',
              match: ['alt'],
              position: { start: 1940, end: 1949 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"\r
  `,
          position: { start: 1949, end: 1954 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'width', match: ['width'] }],
            position: { start: 1954, end: 1968 },
            output: [
              { type: 'raw', value: 'width="', position: { start: 1968, end: 1975 } },
              {
                type: 'output',
                position: { start: 1975, end: 1986 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'width',
                    match: ['width'],
                    position: { start: 1975, end: 1986 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1986, end: 1987 } },
            ],
          },
          position: { open: { start: 1954, end: 1968 }, close: { start: 1987, end: 1998 } },
        },
        {
          type: 'raw',
          value: `\r
  `,
          position: { start: 1998, end: 2002 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'height', match: ['height'] }],
            position: { start: 2002, end: 2017 },
            output: [
              { type: 'raw', value: 'height="', position: { start: 2017, end: 2025 } },
              {
                type: 'output',
                position: { start: 2025, end: 2037 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'height',
                    match: ['height'],
                    position: { start: 2025, end: 2037 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 2037, end: 2038 } },
            ],
          },
          position: { open: { start: 2002, end: 2017 }, close: { start: 2038, end: 2049 } },
        },
        {
          type: 'raw',
          value: `\r
  `,
          position: { start: 2049, end: 2053 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'srcset', match: ['srcset'] }],
            position: { start: 2053, end: 2068 },
            output: [
              { type: 'raw', value: 'srcset="', position: { start: 2068, end: 2076 } },
              {
                type: 'output',
                position: { start: 2076, end: 2099 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'srcset',
                    match: ['srcset'],
                    position: { start: 2076, end: 2099 },
                  },
                  {
                    type: 'Twig.expression.type.filter',
                    value: 'join',
                    match: ['|join', 'join'],
                    position: { start: 2076, end: 2099 },
                    params: [
                      {
                        type: 'Twig.expression.type.parameter.start',
                        value: '(',
                        match: ['('],
                        position: { start: 2076, end: 2099 },
                      },
                      {
                        type: 'Twig.expression.type.string',
                        value: ', ',
                        position: { start: 2076, end: 2099 },
                      },
                      {
                        type: 'Twig.expression.type.parameter.end',
                        value: ')',
                        match: [')'],
                        position: { start: 2076, end: 2099 },
                        expression: !1,
                      },
                    ],
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 2099, end: 2100 } },
            ],
          },
          position: { open: { start: 2053, end: 2068 }, close: { start: 2100, end: 2111 } },
        },
        {
          type: 'raw',
          value: `\r
  `,
          position: { start: 2111, end: 2115 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'sizes', match: ['sizes'] }],
            position: { start: 2115, end: 2129 },
            output: [
              { type: 'raw', value: 'sizes="', position: { start: 2129, end: 2136 } },
              {
                type: 'output',
                position: { start: 2136, end: 2147 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'sizes',
                    match: ['sizes'],
                    position: { start: 2136, end: 2147 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 2147, end: 2148 } },
            ],
          },
          position: { open: { start: 2115, end: 2129 }, close: { start: 2148, end: 2159 } },
        },
        {
          type: 'raw',
          value: `\r
  loading="`,
          position: { start: 2159, end: 2172 },
        },
        {
          type: 'output',
          position: { start: 2172, end: 2185 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'loading',
              match: ['loading'],
              position: { start: 2172, end: 2185 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"\r
  decoding="`,
          position: { start: 2185, end: 2200 },
        },
        {
          type: 'output',
          position: { start: 2200, end: 2214 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'decoding',
              match: ['decoding'],
              position: { start: 2200, end: 2214 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"\r
/>\r
`,
          position: { start: 2214, end: 2214 },
        },
      ],
      precompiled: !0,
    });
    i.options.allowInlineIncludes = !0;
    try {
      let e = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(e) || (e = Object.entries(e)), a(i.render({ attributes: new r(e), ...t }))
      );
    } catch (e) {
      return a(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/image/image.twig: ' +
          e.toString()
      );
    }
  };
export { l as i };
