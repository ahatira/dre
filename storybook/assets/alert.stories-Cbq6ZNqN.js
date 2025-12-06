import { t as D, T as W } from './iframe-D21U4yYN.js';
import { a as I, D as M } from './twig-BPJOkNgt.js';
import './button-Bf_lQ4Tb.js';
I(W);
W.cache(!1);
D.twig({
  id: '@elements/button/button.twig',
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
          { type: 'Twig.expression.type.variable', value: 'iconPosition', match: ['iconPosition'] },
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
        stack: [{ type: 'Twig.expression.type.variable', value: 'loading', match: ['loading'] }],
        position: { start: 2390, end: 2407 },
        output: [{ type: 'raw', value: 'aria-busy="true"', position: { start: 2407, end: 2423 } }],
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
        stack: [{ type: 'Twig.expression.type.variable', value: 'loading', match: ['loading'] }],
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
          { type: 'Twig.expression.type.variable', value: 'iconPosition', match: ['iconPosition'] },
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
const c = (t) => t,
  e = (t = {}) => {
    const y = D.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/alert/alert.twig',
      data: [
        { type: 'raw', value: '', position: { start: 579, end: 583 } },
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
                  { type: 'Twig.expression.type.string', value: 'primary' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 583, end: 631 },
          },
          position: { start: 583, end: 631 },
        },
        { type: 'raw', value: '', position: { start: 631, end: 633 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'dismissible',
            expression: [
              {
                type: 'Twig.expression.type.variable',
                value: 'dismissible',
                match: ['dismissible'],
              },
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
            position: { start: 633, end: 685 },
          },
          position: { start: 633, end: 685 },
        },
        { type: 'raw', value: '', position: { start: 685, end: 687 } },
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
            position: { start: 687, end: 731 },
          },
          position: { start: 687, end: 731 },
        },
        { type: 'raw', value: '', position: { start: 731, end: 733 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'content',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'content', match: ['content'] },
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
            position: { start: 733, end: 774 },
          },
          position: { start: 733, end: 774 },
        },
        { type: 'raw', value: '', position: { start: 774, end: 776 } },
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
            position: { start: 776, end: 839 },
          },
          position: { start: 776, end: 839 },
        },
        { type: 'raw', value: '', position: { start: 839, end: 843 } },
        {
          type: 'output',
          position: { start: 843, end: 881 },
          stack: [
            {
              type: 'Twig.expression.type._function',
              position: { start: 843, end: 881 },
              fn: 'attach_library',
              params: [
                {
                  type: 'Twig.expression.type.parameter.start',
                  value: '(',
                  match: ['('],
                  position: { start: 843, end: 881 },
                },
                {
                  type: 'Twig.expression.type.string',
                  value: 'ps_theme/alert',
                  position: { start: 843, end: 881 },
                },
                {
                  type: 'Twig.expression.type.parameter.end',
                  value: ')',
                  match: [')'],
                  position: { start: 843, end: 881 },
                  expression: !1,
                },
              ],
            },
          ],
        },
        {
          type: 'raw',
          value: `\r
\r
`,
          position: { start: 881, end: 885 },
        },
        { type: 'raw', value: '', position: { start: 947, end: 949 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'role',
            expression: [
              {
                type: 'Twig.expression.type.subexpression.end',
                value: ')',
                match: [')'],
                expression: !0,
                params: [
                  { type: 'Twig.expression.type.variable', value: 'variant', match: ['variant'] },
                  { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
                  { type: 'Twig.expression.type.string', value: 'danger' },
                  { type: 'Twig.expression.type.comma' },
                  { type: 'Twig.expression.type.string', value: 'warning' },
                  { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: 'in',
                    precidence: 8,
                    associativity: 'leftToRight',
                    operator: 'in',
                  },
                ],
              },
              { type: 'Twig.expression.type.string', value: 'alert' },
              { type: 'Twig.expression.type.string', value: 'status' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 949, end: 1023 },
          },
          position: { start: 949, end: 1023 },
        },
        { type: 'raw', value: '', position: { start: 1023, end: 1025 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'ariaLive',
            expression: [
              {
                type: 'Twig.expression.type.subexpression.end',
                value: ')',
                match: [')'],
                expression: !0,
                params: [
                  { type: 'Twig.expression.type.variable', value: 'variant', match: ['variant'] },
                  { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
                  { type: 'Twig.expression.type.string', value: 'danger' },
                  { type: 'Twig.expression.type.comma' },
                  { type: 'Twig.expression.type.string', value: 'warning' },
                  { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: 'in',
                    precidence: 8,
                    associativity: 'leftToRight',
                    operator: 'in',
                  },
                ],
              },
              { type: 'Twig.expression.type.string', value: 'assertive' },
              { type: 'Twig.expression.type.string', value: 'polite' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 1025, end: 1107 },
          },
          position: { start: 1025, end: 1107 },
        },
        { type: 'raw', value: '', position: { start: 1107, end: 1111 } },
        { type: 'raw', value: '', position: { start: 1183, end: 1185 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-alert' },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.string', value: 'ps-alert--' },
              { type: 'Twig.expression.type.variable', value: 'variant', match: ['variant'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.comma' },
              {
                type: 'Twig.expression.type.variable',
                value: 'dismissible',
                match: ['dismissible'],
              },
              { type: 'Twig.expression.type.string', value: 'ps-alert--dismissible' },
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
              { type: 'Twig.expression.type.string', value: 'ps-alert--rounded' },
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
            position: { start: 1185, end: 1342 },
          },
          position: { start: 1185, end: 1342 },
        },
        { type: 'raw', value: '<div ', position: { start: 1342, end: 1351 } },
        {
          type: 'output',
          position: { start: 1351, end: 1385 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 1351, end: 1385 },
            },
            {
              type: 'Twig.expression.type.key.period',
              position: { start: 1351, end: 1385 },
              key: 'addClass',
            },
            {
              type: 'Twig.expression.type.parameter.end',
              value: ')',
              match: [')'],
              position: { start: 1351, end: 1385 },
              expression: !0,
              params: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'classes',
                  match: ['classes'],
                  position: { start: 1351, end: 1385 },
                },
              ],
            },
          ],
        },
        { type: 'raw', value: ' role="', position: { start: 1385, end: 1392 } },
        {
          type: 'output',
          position: { start: 1392, end: 1402 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'role',
              match: ['role'],
              position: { start: 1392, end: 1402 },
            },
          ],
        },
        { type: 'raw', value: '" aria-live="', position: { start: 1402, end: 1415 } },
        {
          type: 'output',
          position: { start: 1415, end: 1429 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'ariaLive',
              match: ['ariaLive'],
              position: { start: 1415, end: 1429 },
            },
          ],
        },
        { type: 'raw', value: '">', position: { start: 1429, end: 1435 } },
        {
          type: 'output_whitespace_both',
          position: { start: 1435, end: 1454 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'content',
              match: ['content'],
              position: { start: 1435, end: 1454 },
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'raw',
              match: ['|raw', 'raw'],
              position: { start: 1435, end: 1454 },
            },
          ],
        },
        { type: 'raw', value: '', position: { start: 1454, end: 1460 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'dismissible',
                match: ['dismissible'],
              },
            ],
            position: { start: 1460, end: 1482 },
            output: [
              { type: 'raw', value: '', position: { start: 1482, end: 1488 } },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.include',
                  only: 4,
                  ignoreMissing: !1,
                  stack: [
                    { type: 'Twig.expression.type.string', value: '@elements/button/button.twig' },
                  ],
                  withStack: [
                    { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'baseClass',
                    },
                    { type: 'Twig.expression.type.string', value: 'ps-alert__close' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'label',
                    },
                    { type: 'Twig.expression.type.string', value: '' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'icon',
                    },
                    { type: 'Twig.expression.type.string', value: 'close' },
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
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'attributes',
                    },
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
                    { type: 'Twig.expression.type.key.period', key: 'setAttribute' },
                    {
                      type: 'Twig.expression.type.parameter.end',
                      value: ')',
                      match: [')'],
                      expression: !0,
                      params: [
                        { type: 'Twig.expression.type.string', value: 'aria-label' },
                        { type: 'Twig.expression.type.comma' },
                        { type: 'Twig.expression.type.string', value: 'Close alert' },
                      ],
                    },
                    { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                  ],
                  position: { start: 1488, end: 1730 },
                },
                position: { start: 1488, end: 1730 },
              },
              { type: 'raw', value: '', position: { start: 1730, end: 1734 } },
            ],
          },
          position: { open: { start: 1460, end: 1482 }, close: { start: 1734, end: 1747 } },
        },
        {
          type: 'raw',
          value: `</div>\r
`,
          position: { start: 1747, end: 1747 },
        },
      ],
      precompiled: !0,
    });
    y.options.allowInlineIncludes = !0;
    try {
      let a = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(a) || (a = Object.entries(a)), c(y.render({ attributes: new M(a), ...t }))
      );
    } catch (a) {
      return c(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/alert/alert.twig: ' +
          a.toString()
      );
    }
  },
  V = {
    variant: 'success',
    content:
      'Property listing successfully saved to your favorites! <a href="#" class="ps-alert-link">View your saved properties</a> to manage your watchlist.',
    dismissible: !0,
    rounded: !1,
  },
  j = {
    title: 'Components/Alert',
    tags: ['autodocs'],
    render: (t) => e(t),
    args: V,
    parameters: {
      docs: {
        description: { component: 'Semantic alert with 8 color variants and optional dismissal.' },
      },
    },
    argTypes: {
      variant: {
        description: 'Semantic variant (8 options)',
        control: { type: 'select' },
        options: ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'],
        table: {
          category: 'Appearance',
          type: {
            summary: 'primary | secondary | success | danger | warning | info | light | dark',
          },
          defaultValue: { summary: 'primary' },
        },
      },
      content: {
        description: 'Free HTML content (headings, paragraphs, links, icons optional)',
        control: { type: 'text' },
        table: {
          category: 'Content',
          type: { summary: 'string (HTML)' },
          defaultValue: { summary: '""' },
        },
      },
      dismissible: {
        description: 'Show close button with JavaScript dismiss behavior',
        control: { type: 'boolean' },
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: 'false' },
        },
      },
      rounded: {
        description: 'Apply border radius (default: no radius)',
        control: { type: 'boolean' },
        table: {
          category: 'Appearance',
          type: { summary: 'boolean' },
          defaultValue: { summary: 'false' },
        },
      },
      attributes: {
        description: 'Drupal attributes object for root element',
        table: { category: 'Layout', type: { summary: 'Drupal.Attribute' } },
      },
    },
  },
  i = { render: (t) => e(t), args: { ...V } },
  s = {
    name: 'All 8 Variants',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({ variant: 'primary', content: 'A simple <strong>primary</strong> alert—check it out!' })}
      ${e({ variant: 'secondary', content: 'A simple <strong>secondary</strong> alert—check it out!' })}
      ${e({ variant: 'success', content: 'A simple <strong>success</strong> alert—check it out!' })}
      ${e({ variant: 'danger', content: 'A simple <strong>danger</strong> alert—check it out!' })}
      ${e({ variant: 'warning', content: 'A simple <strong>warning</strong> alert—check it out!' })}
      ${e({ variant: 'info', content: 'A simple <strong>info</strong> alert—check it out!' })}
      ${e({ variant: 'light', content: 'A simple <strong>light</strong> alert—check it out!' })}
      ${e({ variant: 'dark', content: 'A simple <strong>dark</strong> alert—check it out!' })}
    </div>
  `,
  },
  r = {
    name: 'Alert Links',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({ variant: 'primary', content: 'A property listing with <a href="#" class="ps-alert-link">an example link</a>. Give it a click if you like.' })}
      ${e({ variant: 'success', content: 'Offer accepted! <a href="#" class="ps-alert-link">View contract details</a> in your dashboard.' })}
      ${e({ variant: 'warning', content: 'Your insurance expires soon. <a href="#" class="ps-alert-link">Renew now</a> to avoid gaps.' })}
      ${e({ variant: 'danger', content: 'Payment failed. <a href="#" class="ps-alert-link">Update payment method</a> immediately.' })}
    </div>
  `,
  },
  n = {
    name: 'Additional Content',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({
        variant: 'success',
        content: `
          <h4 class="ps-alert-heading">Property Inspection Complete!</h4>
          <p>Aww yeah, you successfully completed the inspection for 123 Main Street. The detailed report has been uploaded to your dashboard and is now available for review.</p>
          <hr>
          <p style="margin-bottom: 0;">Whenever you're ready, proceed with the final offer or schedule a follow-up viewing with the property agent.</p>
        `,
      })}
      ${e({
        variant: 'info',
        content: `
          <h4 class="ps-alert-heading">Market Analysis Available</h4>
          <p>A comprehensive market analysis for downtown commercial properties has been prepared by your real estate advisor.</p>
          <p style="margin-bottom: 0;"><a href="#" class="ps-alert-link">Download report</a> to review key insights and investment opportunities.</p>
        `,
      })}
    </div>
  `,
  },
  p = {
    name: 'With Icons (Optional)',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({ variant: 'primary', content: '<span data-icon="infos" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Featured property listing in your preferred area' })}
      ${e({ variant: 'success', content: '<span data-icon="check" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Viewing confirmed for tomorrow at 2 PM' })}
      ${e({ variant: 'warning', content: '<span data-icon="help" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Document expires in 30 days' })}
      ${e({ variant: 'danger', content: '<span data-icon="close" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Payment processing failed' })}
    </div>
  `,
  },
  o = {
    name: 'Rounded vs Sharp',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Sharp corners (default):</p>
        ${e({ variant: 'primary', content: 'Default alert with sharp corners (no border-radius)' })}
        ${e({ variant: 'success', content: 'Success alert with sharp corners' })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Rounded corners (rounded: true):</p>
        ${e({ variant: 'primary', content: 'Alert with rounded corners (border-radius applied)', rounded: !0 })}
        ${e({ variant: 'success', content: 'Success alert with rounded corners', rounded: !0 })}
      </div>
    </div>
  `,
  },
  l = {
    name: 'Dismissible',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      ${e({ variant: 'warning', content: '<strong>Holy guacamole!</strong> You should check in on some of those fields below.', dismissible: !0 })}
      ${e({ variant: 'success', content: 'Your property has been saved to favorites!', dismissible: !0 })}
      ${e({
        variant: 'danger',
        content: `
          <h4 class="ps-alert-heading">Urgent: Payment Overdue</h4>
          <p style="margin-bottom: 0;">Your monthly payment is now 15 days overdue. Please update your payment information immediately to avoid service interruption.</p>
        `,
        dismissible: !0,
      })}
    </div>
  `,
  };
var u, g, d;
i.parameters = {
  ...i.parameters,
  docs: {
    ...((u = i.parameters) == null ? void 0 : u.docs),
    source: {
      originalSource: `{
  render: args => alertTwig(args),
  args: {
    ...data
  }
}`,
      ...((d = (g = i.parameters) == null ? void 0 : g.docs) == null ? void 0 : d.source),
    },
  },
};
var v, w, m;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((v = s.parameters) == null ? void 0 : v.docs),
    source: {
      originalSource: `{
  name: 'All 8 Variants',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'primary',
    content: 'A simple <strong>primary</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'secondary',
    content: 'A simple <strong>secondary</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'success',
    content: 'A simple <strong>success</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'danger',
    content: 'A simple <strong>danger</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'warning',
    content: 'A simple <strong>warning</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'info',
    content: 'A simple <strong>info</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'light',
    content: 'A simple <strong>light</strong> alert—check it out!'
  })}
      \${alertTwig({
    variant: 'dark',
    content: 'A simple <strong>dark</strong> alert—check it out!'
  })}
    </div>
  \`
}`,
      ...((m = (w = s.parameters) == null ? void 0 : w.docs) == null ? void 0 : m.source),
    },
  },
};
var T, h, x;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((T = r.parameters) == null ? void 0 : T.docs),
    source: {
      originalSource: `{
  name: 'Alert Links',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'primary',
    content: 'A property listing with <a href="#" class="ps-alert-link">an example link</a>. Give it a click if you like.'
  })}
      \${alertTwig({
    variant: 'success',
    content: 'Offer accepted! <a href="#" class="ps-alert-link">View contract details</a> in your dashboard.'
  })}
      \${alertTwig({
    variant: 'warning',
    content: 'Your insurance expires soon. <a href="#" class="ps-alert-link">Renew now</a> to avoid gaps.'
  })}
      \${alertTwig({
    variant: 'danger',
    content: 'Payment failed. <a href="#" class="ps-alert-link">Update payment method</a> immediately.'
  })}
    </div>
  \`
}`,
      ...((x = (h = r.parameters) == null ? void 0 : h.docs) == null ? void 0 : x.source),
    },
  },
};
var f, b, k;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((f = n.parameters) == null ? void 0 : f.docs),
    source: {
      originalSource: `{
  name: 'Additional Content',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'success',
    content: \`
          <h4 class="ps-alert-heading">Property Inspection Complete!</h4>
          <p>Aww yeah, you successfully completed the inspection for 123 Main Street. The detailed report has been uploaded to your dashboard and is now available for review.</p>
          <hr>
          <p style="margin-bottom: 0;">Whenever you're ready, proceed with the final offer or schedule a follow-up viewing with the property agent.</p>
        \`
  })}
      \${alertTwig({
    variant: 'info',
    content: \`
          <h4 class="ps-alert-heading">Market Analysis Available</h4>
          <p>A comprehensive market analysis for downtown commercial properties has been prepared by your real estate advisor.</p>
          <p style="margin-bottom: 0;"><a href="#" class="ps-alert-link">Download report</a> to review key insights and investment opportunities.</p>
        \`
  })}
    </div>
  \`
}`,
      ...((k = (b = n.parameters) == null ? void 0 : b.docs) == null ? void 0 : k.source),
    },
  },
};
var A, $, z;
p.parameters = {
  ...p.parameters,
  docs: {
    ...((A = p.parameters) == null ? void 0 : A.docs),
    source: {
      originalSource: `{
  name: 'With Icons (Optional)',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'primary',
    content: '<span data-icon="infos" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Featured property listing in your preferred area'
  })}
      \${alertTwig({
    variant: 'success',
    content: '<span data-icon="check" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Viewing confirmed for tomorrow at 2 PM'
  })}
      \${alertTwig({
    variant: 'warning',
    content: '<span data-icon="help" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Document expires in 30 days'
  })}
      \${alertTwig({
    variant: 'danger',
    content: '<span data-icon="close" aria-hidden="true" style="margin-right: var(--size-3); font-size: var(--font-size-3);"></span> Payment processing failed'
  })}
    </div>
  \`
}`,
      ...((z = ($ = p.parameters) == null ? void 0 : $.docs) == null ? void 0 : z.source),
    },
  },
};
var C, _, R;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((C = o.parameters) == null ? void 0 : C.docs),
    source: {
      originalSource: `{
  name: 'Rounded vs Sharp',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Sharp corners (default):</p>
        \${alertTwig({
    variant: 'primary',
    content: 'Default alert with sharp corners (no border-radius)'
  })}
        \${alertTwig({
    variant: 'success',
    content: 'Success alert with sharp corners'
  })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-weight: var(--font-weight-600);">Rounded corners (rounded: true):</p>
        \${alertTwig({
    variant: 'primary',
    content: 'Alert with rounded corners (border-radius applied)',
    rounded: true
  })}
        \${alertTwig({
    variant: 'success',
    content: 'Success alert with rounded corners',
    rounded: true
  })}
      </div>
    </div>
  \`
}`,
      ...((R = (_ = o.parameters) == null ? void 0 : _.docs) == null ? void 0 : R.source),
    },
  },
};
var L, S, P;
l.parameters = {
  ...l.parameters,
  docs: {
    ...((L = l.parameters) == null ? void 0 : L.docs),
    source: {
      originalSource: `{
  name: 'Dismissible',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      \${alertTwig({
    variant: 'warning',
    content: '<strong>Holy guacamole!</strong> You should check in on some of those fields below.',
    dismissible: true
  })}
      \${alertTwig({
    variant: 'success',
    content: 'Your property has been saved to favorites!',
    dismissible: true
  })}
      \${alertTwig({
    variant: 'danger',
    content: \`
          <h4 class="ps-alert-heading">Urgent: Payment Overdue</h4>
          <p style="margin-bottom: 0;">Your monthly payment is now 15 days overdue. Please update your payment information immediately to avoid service interruption.</p>
        \`,
    dismissible: true
  })}
    </div>
  \`
}`,
      ...((P = (S = l.parameters) == null ? void 0 : S.docs) == null ? void 0 : P.source),
    },
  },
};
const U = [
  'Default',
  'AllVariants',
  'WithLinks',
  'WithHeadings',
  'WithIcons',
  'WithRoundedCorners',
  'DismissibleAlerts',
];
export {
  s as AllVariants,
  i as Default,
  l as DismissibleAlerts,
  n as WithHeadings,
  p as WithIcons,
  r as WithLinks,
  o as WithRoundedCorners,
  U as __namedExportsOrder,
  j as default,
};
