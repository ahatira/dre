import { t as p, T as s } from './iframe-D21U4yYN.js';
import { a as o, D as r } from './twig-BPJOkNgt.js';
o(s);
s.cache(!1);
const a = (t) => t,
  y = (t = {}) => {
    const i = p.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/elements/link/link.twig',
      data: [
        {
          type: 'raw',
          value: `

`,
          position: { start: 913, end: 915 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'text',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'text', match: ['text'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'Link text' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 915, end: 957 },
          },
          position: { start: 915, end: 957 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'url',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'url', match: ['url'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: '#' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 958, end: 990 },
          },
          position: { start: 958, end: 990 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'underline',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'underline', match: ['underline'] },
              { type: 'Twig.expression.type.test', filter: 'null', modifier: 'not' },
              { type: 'Twig.expression.type.variable', value: 'underline', match: ['underline'] },
              { type: 'Twig.expression.type.bool', value: !0 },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 991, end: 1053 },
          },
          position: { start: 991, end: 1053 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'icon',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: '' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1054, end: 1087 },
          },
          position: { start: 1054, end: 1087 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'iconPosition',
            expression: [
              {
                type: 'Twig.expression.type.variable',
                value: 'iconPosition',
                match: ['iconPosition'],
              },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'right' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1088, end: 1142 },
          },
          position: { start: 1088, end: 1142 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'target',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'target', match: ['target'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: '_self' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1143, end: 1185 },
          },
          position: { start: 1143, end: 1185 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'rel',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'rel', match: ['rel'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: '' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1186, end: 1217 },
          },
          position: { start: 1186, end: 1217 },
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
            position: { start: 1218, end: 1262 },
          },
          position: { start: 1218, end: 1262 },
        },
        {
          type: 'raw',
          value: `
`,
          position: { start: 1263, end: 1264 },
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
                  { type: 'Twig.expression.type.string', value: 'ps-link' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1264, end: 1314 },
          },
          position: { start: 1264, end: 1314 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'color', match: ['color'] },
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
              { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
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
              { type: 'Twig.expression.type.variable', value: 'underline', match: ['underline'] },
              { type: 'Twig.expression.type.bool', value: !1 },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '==',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '==',
              },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--no-underline' },
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
              {
                type: 'Twig.expression.type.variable',
                value: 'iconPosition',
                match: ['iconPosition'],
              },
              { type: 'Twig.expression.type.string', value: 'left' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '==',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '==',
              },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--icon-left' },
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
            ],
            position: { start: 1315, end: 1601 },
          },
          position: { start: 1315, end: 1601 },
        },
        {
          type: 'raw',
          value: `
`,
          position: { start: 1602, end: 1603 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'tag',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              { type: 'Twig.expression.type.string', value: 'span' },
              { type: 'Twig.expression.type.string', value: 'a' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 1603, end: 1642 },
          },
          position: { start: 1603, end: 1642 },
        },
        {
          type: 'raw',
          value: `
<`,
          position: { start: 1643, end: 1645 },
        },
        {
          type: 'output',
          position: { start: 1645, end: 1654 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'tag',
              match: ['tag'],
              position: { start: 1645, end: 1654 },
            },
          ],
        },
        {
          type: 'raw',
          value: `
  `,
          position: { start: 1654, end: 1657 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'attributes', match: ['attributes'] },
            ],
            position: { start: 1657, end: 1676 },
            output: [
              {
                type: 'output',
                position: { start: 1676, end: 1710 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'attributes',
                    match: ['attributes'],
                    position: { start: 1676, end: 1710 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 1676, end: 1710 },
                    key: 'addClass',
                  },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    position: { start: 1676, end: 1710 },
                    expression: !0,
                    params: [
                      {
                        type: 'Twig.expression.type.variable',
                        value: 'classes',
                        match: ['classes'],
                        position: { start: 1676, end: 1710 },
                      },
                    ],
                  },
                ],
              },
            ],
          },
          position: { open: { start: 1657, end: 1676 }, close: { start: 1710, end: 1720 } },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.else',
            match: ['else'],
            position: { start: 1710, end: 1720 },
            output: [
              { type: 'raw', value: 'class="', position: { start: 1720, end: 1727 } },
              {
                type: 'output',
                position: { start: 1727, end: 1755 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'classes',
                    match: ['classes'],
                    position: { start: 1727, end: 1755 },
                  },
                  {
                    type: 'Twig.expression.type.filter',
                    value: 'join',
                    match: ['|join', 'join'],
                    position: { start: 1727, end: 1755 },
                    params: [
                      {
                        type: 'Twig.expression.type.parameter.start',
                        value: '(',
                        match: ['('],
                        position: { start: 1727, end: 1755 },
                      },
                      {
                        type: 'Twig.expression.type.string',
                        value: ' ',
                        position: { start: 1727, end: 1755 },
                      },
                      {
                        type: 'Twig.expression.type.parameter.end',
                        value: ')',
                        match: [')'],
                        position: { start: 1727, end: 1755 },
                        expression: !1,
                      },
                    ],
                  },
                  {
                    type: 'Twig.expression.type.filter',
                    value: 'trim',
                    match: ['|trim', 'trim'],
                    position: { start: 1727, end: 1755 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1755, end: 1756 } },
            ],
          },
          position: { open: { start: 1710, end: 1720 }, close: { start: 1756, end: 1767 } },
        },
        { type: 'raw', value: '  ', position: { start: 1768, end: 1770 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              {
                type: 'Twig.expression.type.operator.unary',
                value: 'not',
                precidence: 3,
                associativity: 'rightToLeft',
                operator: 'not',
              },
            ],
            position: { start: 1770, end: 1791 },
            output: [
              { type: 'raw', value: 'href="', position: { start: 1791, end: 1797 } },
              {
                type: 'output',
                position: { start: 1797, end: 1806 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'url',
                    match: ['url'],
                    position: { start: 1797, end: 1806 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1806, end: 1807 } },
            ],
          },
          position: { open: { start: 1770, end: 1791 }, close: { start: 1807, end: 1818 } },
        },
        { type: 'raw', value: '  ', position: { start: 1819, end: 1821 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'target', match: ['target'] },
              { type: 'Twig.expression.type.string', value: '_blank' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '==',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '==',
              },
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              {
                type: 'Twig.expression.type.operator.unary',
                value: 'not',
                precidence: 3,
                associativity: 'rightToLeft',
                operator: 'not',
              },
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
            ],
            position: { start: 1821, end: 1865 },
            output: [
              { type: 'raw', value: 'target="_blank" rel="', position: { start: 1865, end: 1886 } },
              {
                type: 'output',
                position: { start: 1886, end: 1925 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'rel',
                    match: ['rel'],
                    position: { start: 1886, end: 1925 },
                  },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'rel',
                    match: ['rel'],
                    position: { start: 1886, end: 1925 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: 'noopener noreferrer',
                    position: { start: 1886, end: 1925 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '?',
                    position: { start: 1886, end: 1925 },
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: '?',
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1925, end: 1926 } },
            ],
          },
          position: { open: { start: 1821, end: 1865 }, close: { start: 1926, end: 1942 } },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.elseif',
            stack: [{ type: 'Twig.expression.type.variable', value: 'rel', match: ['rel'] }],
            position: { start: 1926, end: 1942 },
            output: [
              { type: 'raw', value: 'rel="', position: { start: 1942, end: 1947 } },
              {
                type: 'output',
                position: { start: 1947, end: 1956 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'rel',
                    match: ['rel'],
                    position: { start: 1947, end: 1956 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 1956, end: 1957 } },
            ],
          },
          position: { open: { start: 1926, end: 1942 }, close: { start: 1957, end: 1968 } },
        },
        { type: 'raw', value: '  ', position: { start: 1969, end: 1971 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
            ],
            position: { start: 1971, end: 1988 },
            output: [
              { type: 'raw', value: 'aria-disabled="true"', position: { start: 1988, end: 2008 } },
            ],
          },
          position: { open: { start: 1971, end: 1988 }, close: { start: 2008, end: 2019 } },
        },
        {
          type: 'raw',
          value: `>
  <span class="`,
          position: { start: 2020, end: 2037 },
        },
        {
          type: 'output',
          position: { start: 2037, end: 2052 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'baseClass',
              match: ['baseClass'],
              position: { start: 2037, end: 2052 },
            },
          ],
        },
        { type: 'raw', value: '__text">', position: { start: 2052, end: 2060 } },
        {
          type: 'output',
          position: { start: 2060, end: 2070 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'text',
              match: ['text'],
              position: { start: 2060, end: 2070 },
            },
          ],
        },
        {
          type: 'raw',
          value: `</span>
  `,
          position: { start: 2070, end: 2080 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] }],
            position: { start: 2080, end: 2093 },
            output: [
              { type: 'raw', value: '    <span class="', position: { start: 2094, end: 2111 } },
              {
                type: 'output',
                position: { start: 2111, end: 2126 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'baseClass',
                    match: ['baseClass'],
                    position: { start: 2111, end: 2126 },
                  },
                ],
              },
              { type: 'raw', value: '__icon" data-icon="', position: { start: 2126, end: 2145 } },
              {
                type: 'output',
                position: { start: 2145, end: 2155 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'icon',
                    match: ['icon'],
                    position: { start: 2145, end: 2155 },
                  },
                ],
              },
              {
                type: 'raw',
                value: `" aria-hidden="true"></span>
  `,
                position: { start: 2155, end: 2186 },
              },
            ],
          },
          position: { open: { start: 2080, end: 2093 }, close: { start: 2186, end: 2197 } },
        },
        { type: 'raw', value: '</', position: { start: 2198, end: 2200 } },
        {
          type: 'output',
          position: { start: 2200, end: 2209 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'tag',
              match: ['tag'],
              position: { start: 2200, end: 2209 },
            },
          ],
        },
        { type: 'raw', value: '>', position: { start: 2209, end: 2209 } },
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
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/link/link.twig: ' +
          e.toString()
      );
    }
  };
export { y as l };
