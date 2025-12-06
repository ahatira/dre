import { t as p, T as s } from './iframe-D21U4yYN.js';
import { D as o, a as r } from './twig-BPJOkNgt.js';
r(s);
s.cache(!1);
const a = (t) => t,
  y = (t = {}) => {
    const i = p.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/elements/button/button.twig',
      data: [
        { type: 'raw', value: '', position: { start: 906, end: 910 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'variant',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'variant', match: ['variant'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.string', value: 'neutral' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 910, end: 958 },
          },
          position: { start: 910, end: 958 },
        },
        { type: 'raw', value: '', position: { start: 958, end: 960 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'outline',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'outline', match: ['outline'] },
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
            position: { start: 960, end: 1004 },
          },
          position: { start: 960, end: 1004 },
        },
        { type: 'raw', value: '', position: { start: 1004, end: 1006 } },
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
            position: { start: 1006, end: 1047 },
          },
          position: { start: 1006, end: 1047 },
        },
        { type: 'raw', value: '', position: { start: 1047, end: 1049 } },
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
            position: { start: 1049, end: 1105 },
          },
          position: { start: 1049, end: 1105 },
        },
        { type: 'raw', value: '', position: { start: 1105, end: 1107 } },
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
            position: { start: 1107, end: 1151 },
          },
          position: { start: 1107, end: 1151 },
        },
        { type: 'raw', value: '', position: { start: 1151, end: 1153 } },
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
            position: { start: 1153, end: 1199 },
          },
          position: { start: 1153, end: 1199 },
        },
        { type: 'raw', value: '', position: { start: 1199, end: 1201 } },
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
            position: { start: 1201, end: 1245 },
          },
          position: { start: 1201, end: 1245 },
        },
        { type: 'raw', value: '', position: { start: 1245, end: 1247 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'fullWidth',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'fullWidth', match: ['fullWidth'] },
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
            position: { start: 1247, end: 1295 },
          },
          position: { start: 1247, end: 1295 },
        },
        { type: 'raw', value: '', position: { start: 1295, end: 1297 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'toggle',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'toggle', match: ['toggle'] },
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
            position: { start: 1297, end: 1339 },
          },
          position: { start: 1297, end: 1339 },
        },
        { type: 'raw', value: '', position: { start: 1339, end: 1341 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'active',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'active', match: ['active'] },
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
            position: { start: 1341, end: 1383 },
          },
          position: { start: 1341, end: 1383 },
        },
        { type: 'raw', value: '', position: { start: 1383, end: 1385 } },
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
            position: { start: 1385, end: 1448 },
          },
          position: { start: 1385, end: 1448 },
        },
        { type: 'raw', value: '', position: { start: 1448, end: 1450 } },
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
                  { type: 'Twig.expression.type.string', value: 'ps-button' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1450, end: 1504 },
          },
          position: { start: 1450, end: 1504 },
        },
        { type: 'raw', value: '', position: { start: 1504, end: 1506 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'el_icon',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '__icon' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
            ],
            position: { start: 1506, end: 1548 },
          },
          position: { start: 1506, end: 1548 },
        },
        { type: 'raw', value: '', position: { start: 1548, end: 1550 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'el_label',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '__label' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
            ],
            position: { start: 1550, end: 1594 },
          },
          position: { start: 1550, end: 1594 },
        },
        { type: 'raw', value: '', position: { start: 1594, end: 1596 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'el_spinner',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '__spinner' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
            ],
            position: { start: 1596, end: 1644 },
          },
          position: { start: 1596, end: 1644 },
        },
        { type: 'raw', value: '', position: { start: 1644, end: 1648 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'variant', match: ['variant'] },
              { type: 'Twig.expression.type.string', value: 'neutral' },
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
              { type: 'Twig.expression.type.variable', value: 'variant', match: ['variant'] },
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
              { type: 'Twig.expression.type.variable', value: 'outline', match: ['outline'] },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--outline' },
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
                type: 'Twig.expression.type.subexpression.end',
                value: ')',
                match: [')'],
                expression: !0,
                params: [
                  { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
                  {
                    type: 'Twig.expression.type.operator.unary',
                    value: 'not',
                    precidence: 3,
                    associativity: 'rightToLeft',
                    operator: 'not',
                  },
                ],
              },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--icon-only' },
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
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'loading', match: ['loading'] },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--loading' },
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
              { type: 'Twig.expression.type.variable', value: 'fullWidth', match: ['fullWidth'] },
              { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
              { type: 'Twig.expression.type.string', value: '--full-width' },
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
                type: 'Twig.expression.type.subexpression.end',
                value: ')',
                match: [')'],
                expression: !0,
                params: [
                  { type: 'Twig.expression.type.variable', value: 'toggle', match: ['toggle'] },
                  { type: 'Twig.expression.type.variable', value: 'active', match: ['active'] },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: 'and',
                    precidence: 13,
                    associativity: 'leftToRight',
                    operator: 'and',
                  },
                ],
              },
              { type: 'Twig.expression.type.string', value: 'active' },
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
            position: { start: 1648, end: 2082 },
          },
          position: { start: 1648, end: 2082 },
        },
        { type: 'raw', value: '', position: { start: 2082, end: 2086 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'tag',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'url', match: ['url'] },
              { type: 'Twig.expression.type.string', value: 'a' },
              { type: 'Twig.expression.type.string', value: 'button' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 2086, end: 2124 },
          },
          position: { start: 2086, end: 2124 },
        },
        { type: 'raw', value: '<', position: { start: 2124, end: 2129 } },
        {
          type: 'output',
          position: { start: 2129, end: 2138 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'tag',
              match: ['tag'],
              position: { start: 2129, end: 2138 },
            },
          ],
        },
        { type: 'raw', value: ' ', position: { start: 2138, end: 2139 } },
        {
          type: 'output',
          position: { start: 2139, end: 2173 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 2139, end: 2173 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 2139, end: 2173 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 2139, end: 2173 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 2139, end: 2173 },
                },
              ],
            },
          ],
        },
        { type: 'raw', value: '', position: { start: 2173, end: 2177 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'url', match: ['url'] }],
            position: { start: 2177, end: 2190 },
            output: [
              { type: 'raw', value: 'href="', position: { start: 2190, end: 2196 } },
              {
                type: 'output',
                position: { start: 2196, end: 2205 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'url',
                    match: ['url'],
                    position: { start: 2196, end: 2205 },
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 2205, end: 2206 } },
            ],
          },
          position: { open: { start: 2177, end: 2190 }, close: { start: 2206, end: 2218 } },
        },
        { type: 'raw', value: '', position: { start: 2218, end: 2222 } },
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
            ],
            position: { start: 2222, end: 2250 },
            output: [
              {
                type: 'raw',
                value: 'target="_blank" rel="noopener noreferrer"',
                position: { start: 2250, end: 2291 },
              },
            ],
          },
          position: { open: { start: 2222, end: 2250 }, close: { start: 2291, end: 2303 } },
        },
        { type: 'raw', value: '', position: { start: 2303, end: 2307 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              { type: 'Twig.expression.type.variable', value: 'tag', match: ['tag'] },
              { type: 'Twig.expression.type.string', value: 'button' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '==',
                precidence: 9,
                associativity: 'leftToRight',
                operator: '==',
              },
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
            ],
            position: { start: 2307, end: 2345 },
            output: [
              {
                type: 'raw',
                value: 'disabled aria-disabled="true"',
                position: { start: 2345, end: 2374 },
              },
            ],
          },
          position: { open: { start: 2307, end: 2345 }, close: { start: 2374, end: 2386 } },
        },
        { type: 'raw', value: '', position: { start: 2386, end: 2390 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'loading', match: ['loading'] },
            ],
            position: { start: 2390, end: 2407 },
            output: [
              { type: 'raw', value: 'aria-busy="true"', position: { start: 2407, end: 2423 } },
            ],
          },
          position: { open: { start: 2390, end: 2407 }, close: { start: 2423, end: 2435 } },
        },
        { type: 'raw', value: '', position: { start: 2435, end: 2439 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'toggle', match: ['toggle'] }],
            position: { start: 2439, end: 2455 },
            output: [
              {
                type: 'raw',
                value: 'data-ps-toggle="button" aria-pressed="',
                position: { start: 2455, end: 2493 },
              },
              {
                type: 'output',
                position: { start: 2493, end: 2524 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'active',
                    match: ['active'],
                    position: { start: 2493, end: 2524 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: 'true',
                    position: { start: 2493, end: 2524 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: 'false',
                    position: { start: 2493, end: 2524 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '?',
                    position: { start: 2493, end: 2524 },
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: '?',
                  },
                ],
              },
              { type: 'raw', value: '"', position: { start: 2524, end: 2525 } },
            ],
          },
          position: { open: { start: 2439, end: 2455 }, close: { start: 2525, end: 2537 } },
        },
        { type: 'raw', value: '>', position: { start: 2537, end: 2544 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'loading', match: ['loading'] },
            ],
            position: { start: 2544, end: 2562 },
            output: [
              { type: 'raw', value: '<span class="', position: { start: 2562, end: 2581 } },
              {
                type: 'output',
                position: { start: 2581, end: 2597 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'el_spinner',
                    match: ['el_spinner'],
                    position: { start: 2581, end: 2597 },
                  },
                ],
              },
              {
                type: 'raw',
                value: '" aria-hidden="true"></span>',
                position: { start: 2597, end: 2629 },
              },
            ],
          },
          position: { open: { start: 2544, end: 2562 }, close: { start: 2629, end: 2642 } },
        },
        { type: 'raw', value: '', position: { start: 2642, end: 2648 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] },
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
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
              { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
            ],
            position: { start: 2648, end: 2700 },
            output: [
              { type: 'raw', value: '<span class="', position: { start: 2700, end: 2719 } },
              {
                type: 'output',
                position: { start: 2719, end: 2732 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'el_icon',
                    match: ['el_icon'],
                    position: { start: 2719, end: 2732 },
                  },
                ],
              },
              { type: 'raw', value: '" data-icon="', position: { start: 2732, end: 2745 } },
              {
                type: 'output',
                position: { start: 2745, end: 2755 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'icon',
                    match: ['icon'],
                    position: { start: 2745, end: 2755 },
                  },
                ],
              },
              {
                type: 'raw',
                value: '" aria-hidden="true"></span>',
                position: { start: 2755, end: 2787 },
              },
            ],
          },
          position: { open: { start: 2648, end: 2700 }, close: { start: 2787, end: 2800 } },
        },
        { type: 'raw', value: '', position: { start: 2800, end: 2806 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'label', match: ['label'] }],
            position: { start: 2806, end: 2822 },
            output: [
              { type: 'raw', value: '<span class="', position: { start: 2822, end: 2841 } },
              {
                type: 'output',
                position: { start: 2841, end: 2855 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'el_label',
                    match: ['el_label'],
                    position: { start: 2841, end: 2855 },
                  },
                ],
              },
              { type: 'raw', value: '">', position: { start: 2855, end: 2857 } },
              {
                type: 'output_whitespace_both',
                position: { start: 2857, end: 2870 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'label',
                    match: ['label'],
                    position: { start: 2857, end: 2870 },
                  },
                ],
              },
              { type: 'raw', value: '</span>', position: { start: 2870, end: 2881 } },
            ],
          },
          position: { open: { start: 2806, end: 2822 }, close: { start: 2881, end: 2894 } },
        },
        { type: 'raw', value: '', position: { start: 2894, end: 2900 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] },
              {
                type: 'Twig.expression.type.subexpression.end',
                value: ')',
                match: [')'],
                expression: !0,
                params: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'iconPosition',
                    match: ['iconPosition'],
                  },
                  { type: 'Twig.expression.type.string', value: 'right' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '==',
                    precidence: 9,
                    associativity: 'leftToRight',
                    operator: '==',
                  },
                  { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
                  {
                    type: 'Twig.expression.type.operator.unary',
                    value: 'not',
                    precidence: 3,
                    associativity: 'rightToLeft',
                    operator: 'not',
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: 'or',
                    precidence: 14,
                    associativity: 'leftToRight',
                    operator: 'or',
                  },
                ],
              },
              {
                type: 'Twig.expression.type.operator.binary',
                value: 'and',
                precidence: 13,
                associativity: 'leftToRight',
                operator: 'and',
              },
            ],
            position: { start: 2900, end: 2958 },
            output: [
              { type: 'raw', value: '<span class="', position: { start: 2958, end: 2977 } },
              {
                type: 'output',
                position: { start: 2977, end: 2990 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'el_icon',
                    match: ['el_icon'],
                    position: { start: 2977, end: 2990 },
                  },
                ],
              },
              { type: 'raw', value: '" data-icon="', position: { start: 2990, end: 3003 } },
              {
                type: 'output',
                position: { start: 3003, end: 3013 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'icon',
                    match: ['icon'],
                    position: { start: 3003, end: 3013 },
                  },
                ],
              },
              {
                type: 'raw',
                value: '" aria-hidden="true"></span>',
                position: { start: 3013, end: 3045 },
              },
            ],
          },
          position: { open: { start: 2900, end: 2958 }, close: { start: 3045, end: 3058 } },
        },
        { type: 'raw', value: '</', position: { start: 3058, end: 3062 } },
        {
          type: 'output',
          position: { start: 3062, end: 3071 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'tag',
              match: ['tag'],
              position: { start: 3062, end: 3071 },
            },
          ],
        },
        {
          type: 'raw',
          value: `>\r
`,
          position: { start: 3071, end: 3071 },
        },
      ],
      precompiled: !0,
    });
    i.options.allowInlineIncludes = !0;
    try {
      let e = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(e) || (e = Object.entries(e)), a(i.render({ attributes: new o(e), ...t }))
      );
    } catch (e) {
      return a(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/elements/button/button.twig: ' +
          e.toString()
      );
    }
  };
export { y as b };
