import { T as B, t as l } from './iframe-D21U4yYN.js';
import { a as J, D as V } from './twig-BPJOkNgt.js';
import './field-BdV63eLX.js';
import './label-BkLBn4m8.js';
J(B);
B.cache(!1);
l.twig({
  id: '@elements/label/label.twig',
  data: [
    { type: 'raw', value: '', position: { start: 444, end: 446 } },
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
              { type: 'Twig.expression.type.string', value: 'ps-label' },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
        ],
        position: { start: 446, end: 499 },
      },
      position: { start: 446, end: 499 },
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
          { type: 'Twig.expression.type.variable', value: 'required', match: ['required'] },
          { type: 'Twig.expression.type.variable', value: 'baseClass', match: ['baseClass'] },
          { type: 'Twig.expression.type.string', value: '--required' },
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
        position: { start: 500, end: 629 },
      },
      position: { start: 500, end: 629 },
    },
    { type: 'raw', value: '<label', position: { start: 630, end: 637 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'attributes', match: ['attributes'] },
        ],
        position: { start: 637, end: 656 },
        output: [
          { type: 'raw', value: ' ', position: { start: 656, end: 657 } },
          {
            type: 'output',
            position: { start: 657, end: 691 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'attributes',
                match: ['attributes'],
                position: { start: 657, end: 691 },
              },
              {
                type: 'Twig.expression.type.key.period',
                position: { start: 657, end: 691 },
                key: 'addClass',
              },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                position: { start: 657, end: 691 },
                expression: !0,
                params: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'classes',
                    match: ['classes'],
                    position: { start: 657, end: 691 },
                  },
                ],
              },
            ],
          },
        ],
      },
      position: { open: { start: 637, end: 656 }, close: { start: 691, end: 701 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.else',
        match: ['else'],
        position: { start: 691, end: 701 },
        output: [
          { type: 'raw', value: ' class="', position: { start: 701, end: 709 } },
          {
            type: 'output',
            position: { start: 709, end: 737 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'classes',
                match: ['classes'],
                position: { start: 709, end: 737 },
              },
              {
                type: 'Twig.expression.type.filter',
                value: 'join',
                match: ['|join', 'join'],
                position: { start: 709, end: 737 },
                params: [
                  {
                    type: 'Twig.expression.type.parameter.start',
                    value: '(',
                    match: ['('],
                    position: { start: 709, end: 737 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: ' ',
                    position: { start: 709, end: 737 },
                  },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    position: { start: 709, end: 737 },
                    expression: !1,
                  },
                ],
              },
              {
                type: 'Twig.expression.type.filter',
                value: 'trim',
                match: ['|trim', 'trim'],
                position: { start: 709, end: 737 },
              },
            ],
          },
          { type: 'raw', value: '"', position: { start: 737, end: 738 } },
        ],
      },
      position: { open: { start: 691, end: 701 }, close: { start: 738, end: 749 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [{ type: 'Twig.expression.type.variable', value: 'forId', match: ['forId'] }],
        position: { start: 749, end: 763 },
        output: [
          { type: 'raw', value: ' for="', position: { start: 763, end: 769 } },
          {
            type: 'output',
            position: { start: 769, end: 780 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'forId',
                match: ['forId'],
                position: { start: 769, end: 780 },
              },
            ],
          },
          { type: 'raw', value: '"', position: { start: 780, end: 781 } },
        ],
      },
      position: { open: { start: 749, end: 763 }, close: { start: 781, end: 792 } },
    },
    {
      type: 'raw',
      value: `>
  <span class="`,
      position: { start: 792, end: 809 },
    },
    {
      type: 'output',
      position: { start: 809, end: 824 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'baseClass',
          match: ['baseClass'],
          position: { start: 809, end: 824 },
        },
      ],
    },
    { type: 'raw', value: '__text">', position: { start: 824, end: 832 } },
    {
      type: 'output',
      position: { start: 832, end: 842 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'text',
          match: ['text'],
          position: { start: 832, end: 842 },
        },
      ],
    },
    {
      type: 'raw',
      value: `</span>
  `,
      position: { start: 842, end: 852 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [{ type: 'Twig.expression.type.variable', value: 'required', match: ['required'] }],
        position: { start: 852, end: 869 },
        output: [
          { type: 'raw', value: '    <span class="', position: { start: 870, end: 887 } },
          {
            type: 'output',
            position: { start: 887, end: 902 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'baseClass',
                match: ['baseClass'],
                position: { start: 887, end: 902 },
              },
            ],
          },
          {
            type: 'raw',
            value: `__required" aria-hidden="true">*</span>
    <span class="visually-hidden">(required field)</span>
  `,
            position: { start: 902, end: 1002 },
          },
        ],
      },
      position: { open: { start: 852, end: 869 }, close: { start: 1002, end: 1013 } },
    },
    { type: 'raw', value: '</label>', position: { start: 1014, end: 1014 } },
  ],
  precompiled: !0,
});
l.twig({
  id: '@elements/field/field.twig',
  data: [
    { type: 'raw', value: '', position: { start: 1217, end: 1219 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'type',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'type', match: ['type'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'default',
            match: ['|default', 'default'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.string', value: 'text' },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
        ],
        position: { start: 1219, end: 1258 },
      },
      position: { start: 1219, end: 1258 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'value',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'value', match: ['value'] },
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
        position: { start: 1259, end: 1296 },
      },
      position: { start: 1259, end: 1296 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'placeholder',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'placeholder', match: ['placeholder'] },
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
        position: { start: 1297, end: 1346 },
      },
      position: { start: 1297, end: 1346 },
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
        position: { start: 1347, end: 1393 },
      },
      position: { start: 1347, end: 1393 },
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
        position: { start: 1394, end: 1425 },
      },
      position: { start: 1394, end: 1425 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'describedBy',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'describedBy', match: ['describedBy'] },
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
        position: { start: 1426, end: 1477 },
      },
      position: { start: 1426, end: 1477 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'helperId',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'helperId', match: ['helperId'] },
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
        position: { start: 1478, end: 1523 },
      },
      position: { start: 1478, end: 1523 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'error',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
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
        position: { start: 1524, end: 1561 },
      },
      position: { start: 1524, end: 1561 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'errorId',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'errorId', match: ['errorId'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'default',
            match: ['|default', 'default'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
              { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
              { type: 'Twig.expression.type.string', value: '-error' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.string', value: 'field-error' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
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
        position: { start: 1562, end: 1635 },
      },
      position: { start: 1562, end: 1635 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'hideError',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'hideError', match: ['hideError'] },
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
        position: { start: 1636, end: 1684 },
      },
      position: { start: 1636, end: 1684 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'done',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'done', match: ['done'] },
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
        position: { start: 1685, end: 1723 },
      },
      position: { start: 1685, end: 1723 },
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
        position: { start: 1724, end: 1759 },
      },
      position: { start: 1724, end: 1759 },
    },
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
        position: { start: 1760, end: 1816 },
      },
      position: { start: 1760, end: 1816 },
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
        position: { start: 1817, end: 1880 },
      },
      position: { start: 1817, end: 1880 },
    },
    { type: 'raw', value: '', position: { start: 1881, end: 1882 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'ariaDescribedby',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'describedBy', match: ['describedBy'] },
          {
            type: 'Twig.expression.type.subexpression.end',
            value: ')',
            match: [')'],
            expression: !0,
            params: [
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
              { type: 'Twig.expression.type.variable', value: 'errorId', match: ['errorId'] },
              { type: 'Twig.expression.type.variable', value: 'helperId', match: ['helperId'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
          },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '??',
            precidence: 15,
            associativity: 'rightToLeft',
            operator: '??',
          },
        ],
        position: { start: 1882, end: 1955 },
      },
      position: { start: 1882, end: 1955 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'ariaErrormessage',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
          { type: 'Twig.expression.type.variable', value: 'errorId', match: ['errorId'] },
          {
            type: 'Twig.expression.type.operator.binary',
            value: 'and',
            precidence: 13,
            associativity: 'leftToRight',
            operator: 'and',
          },
          { type: 'Twig.expression.type.variable', value: 'errorId', match: ['errorId'] },
          { type: 'Twig.expression.type.null', value: null },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
        ],
        position: { start: 1956, end: 2021 },
      },
      position: { start: 1956, end: 2021 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'resolvedErrorId',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'errorId', match: ['errorId'] },
          { type: 'Twig.expression.type.string', value: 'field-error' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '??',
            precidence: 15,
            associativity: 'rightToLeft',
            operator: '??',
          },
        ],
        position: { start: 2022, end: 2076 },
      },
      position: { start: 2022, end: 2076 },
    },
    { type: 'raw', value: '', position: { start: 2077, end: 2078 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'classes',
        expression: [
          { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
          { type: 'Twig.expression.type.string', value: 'ps-field' },
          { type: 'Twig.expression.type.comma' },
          { type: 'Twig.expression.type.variable', value: 'type', match: ['type'] },
          { type: 'Twig.expression.type.string', value: 'text' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '!=',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '!=',
          },
          { type: 'Twig.expression.type.string', value: 'ps-field--' },
          { type: 'Twig.expression.type.variable', value: 'type', match: ['type'] },
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
          { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
          { type: 'Twig.expression.type.string', value: 'ps-field--error' },
          {
            type: 'Twig.expression.type.subexpression.end',
            value: ')',
            match: [')'],
            expression: !0,
            params: [
              { type: 'Twig.expression.type.variable', value: 'done', match: ['done'] },
              { type: 'Twig.expression.type.string', value: 'ps-field--done' },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
          },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
          { type: 'Twig.expression.type.comma' },
          { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
          { type: 'Twig.expression.type.string', value: 'ps-field--disabled' },
          { type: 'Twig.expression.type.null', value: null },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
          { type: 'Twig.expression.type.comma' },
          { type: 'Twig.expression.type.variable', value: 'value', match: ['value'] },
          { type: 'Twig.expression.type.string', value: 'ps-field--filled' },
          { type: 'Twig.expression.type.null', value: null },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
          { type: 'Twig.expression.type.comma' },
          { type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] },
          { type: 'Twig.expression.type.string', value: 'ps-field--icon-' },
          { type: 'Twig.expression.type.variable', value: 'iconPosition', match: ['iconPosition'] },
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
        position: { start: 2078, end: 2356 },
      },
      position: { start: 2078, end: 2356 },
    },
    { type: 'raw', value: '<div ', position: { start: 2357, end: 2363 } },
    {
      type: 'output',
      position: { start: 2363, end: 2397 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'attributes',
          match: ['attributes'],
          position: { start: 2363, end: 2397 },
        },
        {
          type: 'Twig.expression.type.key.period',
          position: { start: 2363, end: 2397 },
          key: 'addClass',
        },
        {
          type: 'Twig.expression.type.parameter.end',
          value: ')',
          match: [')'],
          position: { start: 2363, end: 2397 },
          expression: !0,
          params: [
            {
              type: 'Twig.expression.type.variable',
              value: 'classes',
              match: ['classes'],
              position: { start: 2363, end: 2397 },
            },
          ],
        },
      ],
    },
    { type: 'raw', value: '>', position: { start: 2397, end: 2401 } },
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
        ],
        position: { start: 2401, end: 2443 },
        output: [
          {
            type: 'raw',
            value: '<span class="ps-field__icon ps-field__icon--left" data-icon="',
            position: { start: 2444, end: 2509 },
          },
          {
            type: 'output',
            position: { start: 2509, end: 2519 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'icon',
                match: ['icon'],
                position: { start: 2509, end: 2519 },
              },
            ],
          },
          {
            type: 'raw',
            value: '" aria-hidden="true"></span>',
            position: { start: 2519, end: 2550 },
          },
        ],
      },
      position: { open: { start: 2401, end: 2443 }, close: { start: 2550, end: 2563 } },
    },
    { type: 'raw', value: '', position: { start: 2564, end: 2567 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'type', match: ['type'] },
          { type: 'Twig.expression.type.string', value: 'textarea' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '==',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '==',
          },
        ],
        position: { start: 2567, end: 2596 },
        output: [
          {
            type: 'raw',
            value: `<textarea
      class="ps-field__input"
      `,
            position: { start: 2597, end: 2647 },
          },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [{ type: 'Twig.expression.type.variable', value: 'id', match: ['id'] }],
              position: { start: 2647, end: 2658 },
              output: [
                { type: 'raw', value: 'id="', position: { start: 2658, end: 2662 } },
                {
                  type: 'output',
                  position: { start: 2662, end: 2670 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'id',
                      match: ['id'],
                      position: { start: 2662, end: 2670 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 2670, end: 2671 } },
              ],
            },
            position: { open: { start: 2647, end: 2658 }, close: { start: 2671, end: 2682 } },
          },
          { type: 'raw', value: '      placeholder="', position: { start: 2683, end: 2702 } },
          {
            type: 'output',
            position: { start: 2702, end: 2719 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'placeholder',
                match: ['placeholder'],
                position: { start: 2702, end: 2719 },
              },
            ],
          },
          {
            type: 'raw',
            value: `"
      `,
            position: { start: 2719, end: 2727 },
          },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              ],
              position: { start: 2727, end: 2744 },
              output: [
                {
                  type: 'raw',
                  value: 'disabled aria-disabled="true"',
                  position: { start: 2744, end: 2773 },
                },
              ],
            },
            position: { open: { start: 2727, end: 2744 }, close: { start: 2773, end: 2784 } },
          },
          { type: 'raw', value: '      ', position: { start: 2785, end: 2791 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'ariaDescribedby',
                  match: ['ariaDescribedby'],
                },
              ],
              position: { start: 2791, end: 2815 },
              output: [
                { type: 'raw', value: 'aria-describedby="', position: { start: 2815, end: 2833 } },
                {
                  type: 'output',
                  position: { start: 2833, end: 2854 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'ariaDescribedby',
                      match: ['ariaDescribedby'],
                      position: { start: 2833, end: 2854 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 2854, end: 2855 } },
              ],
            },
            position: { open: { start: 2791, end: 2815 }, close: { start: 2855, end: 2866 } },
          },
          { type: 'raw', value: '      ', position: { start: 2867, end: 2873 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'ariaErrormessage',
                  match: ['ariaErrormessage'],
                },
              ],
              position: { start: 2873, end: 2898 },
              output: [
                { type: 'raw', value: 'aria-errormessage="', position: { start: 2898, end: 2917 } },
                {
                  type: 'output',
                  position: { start: 2917, end: 2939 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'ariaErrormessage',
                      match: ['ariaErrormessage'],
                      position: { start: 2917, end: 2939 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 2939, end: 2940 } },
              ],
            },
            position: { open: { start: 2873, end: 2898 }, close: { start: 2940, end: 2951 } },
          },
          { type: 'raw', value: '      ', position: { start: 2952, end: 2958 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [{ type: 'Twig.expression.type.variable', value: 'error', match: ['error'] }],
              position: { start: 2958, end: 2972 },
              output: [
                { type: 'raw', value: 'aria-invalid="true"', position: { start: 2972, end: 2991 } },
              ],
            },
            position: { open: { start: 2958, end: 2972 }, close: { start: 2991, end: 3002 } },
          },
          { type: 'raw', value: '    >', position: { start: 3003, end: 3008 } },
          {
            type: 'output',
            position: { start: 3008, end: 3019 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'value',
                match: ['value'],
                position: { start: 3008, end: 3019 },
              },
            ],
          },
          { type: 'raw', value: '</textarea>', position: { start: 3019, end: 3033 } },
        ],
      },
      position: { open: { start: 2567, end: 2596 }, close: { start: 3033, end: 3064 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.elseif',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'type', match: ['type'] },
          { type: 'Twig.expression.type.string', value: 'select' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '==',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '==',
          },
        ],
        position: { start: 3033, end: 3064 },
        output: [
          {
            type: 'raw',
            value: `<div
      class="ps-field__input"
      role="combobox"
      aria-expanded="false"
      aria-haspopup="listbox"
      `,
            position: { start: 3065, end: 3190 },
          },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [{ type: 'Twig.expression.type.variable', value: 'id', match: ['id'] }],
              position: { start: 3190, end: 3201 },
              output: [
                { type: 'raw', value: 'id="', position: { start: 3201, end: 3205 } },
                {
                  type: 'output',
                  position: { start: 3205, end: 3213 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'id',
                      match: ['id'],
                      position: { start: 3205, end: 3213 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 3213, end: 3214 } },
              ],
            },
            position: { open: { start: 3190, end: 3201 }, close: { start: 3214, end: 3225 } },
          },
          { type: 'raw', value: '      ', position: { start: 3226, end: 3232 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'ariaDescribedby',
                  match: ['ariaDescribedby'],
                },
              ],
              position: { start: 3232, end: 3256 },
              output: [
                { type: 'raw', value: 'aria-describedby="', position: { start: 3256, end: 3274 } },
                {
                  type: 'output',
                  position: { start: 3274, end: 3295 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'ariaDescribedby',
                      match: ['ariaDescribedby'],
                      position: { start: 3274, end: 3295 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 3295, end: 3296 } },
              ],
            },
            position: { open: { start: 3232, end: 3256 }, close: { start: 3296, end: 3307 } },
          },
          { type: 'raw', value: '      ', position: { start: 3308, end: 3314 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'ariaErrormessage',
                  match: ['ariaErrormessage'],
                },
              ],
              position: { start: 3314, end: 3339 },
              output: [
                { type: 'raw', value: 'aria-errormessage="', position: { start: 3339, end: 3358 } },
                {
                  type: 'output',
                  position: { start: 3358, end: 3380 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'ariaErrormessage',
                      match: ['ariaErrormessage'],
                      position: { start: 3358, end: 3380 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 3380, end: 3381 } },
              ],
            },
            position: { open: { start: 3314, end: 3339 }, close: { start: 3381, end: 3392 } },
          },
          { type: 'raw', value: '      ', position: { start: 3393, end: 3399 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              ],
              position: { start: 3399, end: 3416 },
              output: [
                {
                  type: 'raw',
                  value: 'aria-disabled="true"',
                  position: { start: 3416, end: 3436 },
                },
              ],
            },
            position: { open: { start: 3399, end: 3416 }, close: { start: 3436, end: 3447 } },
          },
          { type: 'raw', value: '      ', position: { start: 3448, end: 3454 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [{ type: 'Twig.expression.type.variable', value: 'error', match: ['error'] }],
              position: { start: 3454, end: 3468 },
              output: [
                { type: 'raw', value: 'aria-invalid="true"', position: { start: 3468, end: 3487 } },
              ],
            },
            position: { open: { start: 3454, end: 3468 }, close: { start: 3487, end: 3498 } },
          },
          { type: 'raw', value: '    >', position: { start: 3499, end: 3504 } },
          {
            type: 'output',
            position: { start: 3504, end: 3536 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'value',
                match: ['value'],
                position: { start: 3504, end: 3536 },
              },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                position: { start: 3504, end: 3536 },
                params: [
                  {
                    type: 'Twig.expression.type.parameter.start',
                    value: '(',
                    match: ['('],
                    position: { start: 3504, end: 3536 },
                  },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'placeholder',
                    match: ['placeholder'],
                    position: { start: 3504, end: 3536 },
                  },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    position: { start: 3504, end: 3536 },
                    expression: !1,
                  },
                ],
              },
            ],
          },
          { type: 'raw', value: '</div>', position: { start: 3536, end: 3545 } },
        ],
      },
      position: { open: { start: 3033, end: 3064 }, close: { start: 3545, end: 3557 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.else',
        match: ['else'],
        position: { start: 3545, end: 3557 },
        output: [
          {
            type: 'raw',
            value: `<input
      class="ps-field__input"
      `,
            position: { start: 3558, end: 3605 },
          },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [{ type: 'Twig.expression.type.variable', value: 'id', match: ['id'] }],
              position: { start: 3605, end: 3616 },
              output: [
                { type: 'raw', value: 'id="', position: { start: 3616, end: 3620 } },
                {
                  type: 'output',
                  position: { start: 3620, end: 3628 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'id',
                      match: ['id'],
                      position: { start: 3620, end: 3628 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 3628, end: 3629 } },
              ],
            },
            position: { open: { start: 3605, end: 3616 }, close: { start: 3629, end: 3640 } },
          },
          { type: 'raw', value: '      type="', position: { start: 3641, end: 3653 } },
          {
            type: 'output',
            position: { start: 3653, end: 3663 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'type',
                match: ['type'],
                position: { start: 3653, end: 3663 },
              },
            ],
          },
          {
            type: 'raw',
            value: `"
      value="`,
            position: { start: 3663, end: 3678 },
          },
          {
            type: 'output',
            position: { start: 3678, end: 3689 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'value',
                match: ['value'],
                position: { start: 3678, end: 3689 },
              },
            ],
          },
          {
            type: 'raw',
            value: `"
      placeholder="`,
            position: { start: 3689, end: 3710 },
          },
          {
            type: 'output',
            position: { start: 3710, end: 3727 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'placeholder',
                match: ['placeholder'],
                position: { start: 3710, end: 3727 },
              },
            ],
          },
          {
            type: 'raw',
            value: `"
      `,
            position: { start: 3727, end: 3735 },
          },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
              ],
              position: { start: 3735, end: 3752 },
              output: [
                {
                  type: 'raw',
                  value: 'disabled aria-disabled="true"',
                  position: { start: 3752, end: 3781 },
                },
              ],
            },
            position: { open: { start: 3735, end: 3752 }, close: { start: 3781, end: 3792 } },
          },
          { type: 'raw', value: '      ', position: { start: 3793, end: 3799 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'ariaDescribedby',
                  match: ['ariaDescribedby'],
                },
              ],
              position: { start: 3799, end: 3823 },
              output: [
                { type: 'raw', value: 'aria-describedby="', position: { start: 3823, end: 3841 } },
                {
                  type: 'output',
                  position: { start: 3841, end: 3862 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'ariaDescribedby',
                      match: ['ariaDescribedby'],
                      position: { start: 3841, end: 3862 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 3862, end: 3863 } },
              ],
            },
            position: { open: { start: 3799, end: 3823 }, close: { start: 3863, end: 3874 } },
          },
          { type: 'raw', value: '      ', position: { start: 3875, end: 3881 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [
                {
                  type: 'Twig.expression.type.variable',
                  value: 'ariaErrormessage',
                  match: ['ariaErrormessage'],
                },
              ],
              position: { start: 3881, end: 3906 },
              output: [
                { type: 'raw', value: 'aria-errormessage="', position: { start: 3906, end: 3925 } },
                {
                  type: 'output',
                  position: { start: 3925, end: 3947 },
                  stack: [
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'ariaErrormessage',
                      match: ['ariaErrormessage'],
                      position: { start: 3925, end: 3947 },
                    },
                  ],
                },
                { type: 'raw', value: '"', position: { start: 3947, end: 3948 } },
              ],
            },
            position: { open: { start: 3881, end: 3906 }, close: { start: 3948, end: 3959 } },
          },
          { type: 'raw', value: '      ', position: { start: 3960, end: 3966 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.if',
              stack: [{ type: 'Twig.expression.type.variable', value: 'error', match: ['error'] }],
              position: { start: 3966, end: 3980 },
              output: [
                { type: 'raw', value: 'aria-invalid="true"', position: { start: 3980, end: 3999 } },
              ],
            },
            position: { open: { start: 3966, end: 3980 }, close: { start: 3999, end: 4010 } },
          },
          { type: 'raw', value: '    />', position: { start: 4011, end: 4020 } },
        ],
      },
      position: { open: { start: 3545, end: 3557 }, close: { start: 4020, end: 4033 } },
    },
    { type: 'raw', value: '', position: { start: 4034, end: 4037 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'icon', match: ['icon'] },
          { type: 'Twig.expression.type.variable', value: 'iconPosition', match: ['iconPosition'] },
          { type: 'Twig.expression.type.string', value: 'right' },
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
        position: { start: 4037, end: 4080 },
        output: [
          {
            type: 'raw',
            value: '<span class="ps-field__icon ps-field__icon--right" data-icon="',
            position: { start: 4081, end: 4147 },
          },
          {
            type: 'output',
            position: { start: 4147, end: 4157 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'icon',
                match: ['icon'],
                position: { start: 4147, end: 4157 },
              },
            ],
          },
          {
            type: 'raw',
            value: '" aria-hidden="true"></span>',
            position: { start: 4157, end: 4188 },
          },
        ],
      },
      position: { open: { start: 4037, end: 4080 }, close: { start: 4188, end: 4201 } },
    },
    { type: 'raw', value: '', position: { start: 4202, end: 4205 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
          { type: 'Twig.expression.type.variable', value: 'hideError', match: ['hideError'] },
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
        position: { start: 4205, end: 4239 },
        output: [
          {
            type: 'raw',
            value: '<div class="ps-field__error" id="',
            position: { start: 4240, end: 4277 },
          },
          {
            type: 'output',
            position: { start: 4277, end: 4298 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'resolvedErrorId',
                match: ['resolvedErrorId'],
                position: { start: 4277, end: 4298 },
              },
            ],
          },
          { type: 'raw', value: '" role="alert">', position: { start: 4298, end: 4313 } },
          {
            type: 'output',
            position: { start: 4313, end: 4324 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'error',
                match: ['error'],
                position: { start: 4313, end: 4324 },
              },
            ],
          },
          { type: 'raw', value: '</div>', position: { start: 4324, end: 4333 } },
        ],
      },
      position: { open: { start: 4205, end: 4239 }, close: { start: 4333, end: 4346 } },
    },
    { type: 'raw', value: '</div>', position: { start: 4347, end: 4347 } },
  ],
  precompiled: !0,
});
const d = (t) => t,
  e = (t = {}) => {
    const y = l.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/form-field/form-field.twig',
      data: [
        { type: 'raw', value: '', position: { start: 1060, end: 1064 } },
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
            position: { start: 1064, end: 1101 },
          },
          position: { start: 1064, end: 1101 },
        },
        { type: 'raw', value: '', position: { start: 1101, end: 1103 } },
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
                  { type: 'Twig.expression.type.string', value: 'field-' },
                  {
                    type: 'Twig.expression.type._function',
                    fn: 'random',
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
            position: { start: 1103, end: 1151 },
          },
          position: { start: 1103, end: 1151 },
        },
        { type: 'raw', value: '', position: { start: 1151, end: 1153 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'field',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'field', match: ['field'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                  { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 1153, end: 1190 },
          },
          position: { start: 1153, end: 1190 },
        },
        { type: 'raw', value: '', position: { start: 1190, end: 1192 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'helperText',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'helperText', match: ['helperText'] },
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
            position: { start: 1192, end: 1239 },
          },
          position: { start: 1192, end: 1239 },
        },
        { type: 'raw', value: '', position: { start: 1239, end: 1241 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'error',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
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
            position: { start: 1241, end: 1278 },
          },
          position: { start: 1241, end: 1278 },
        },
        { type: 'raw', value: '', position: { start: 1278, end: 1280 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'required',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'required', match: ['required'] },
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
            position: { start: 1280, end: 1326 },
          },
          position: { start: 1280, end: 1326 },
        },
        { type: 'raw', value: '', position: { start: 1326, end: 1328 } },
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
            position: { start: 1328, end: 1374 },
          },
          position: { start: 1328, end: 1374 },
        },
        { type: 'raw', value: '', position: { start: 1374, end: 1376 } },
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
            position: { start: 1376, end: 1439 },
          },
          position: { start: 1376, end: 1439 },
        },
        { type: 'raw', value: '', position: { start: 1439, end: 1443 } },
        { type: 'raw', value: '', position: { start: 1494, end: 1496 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'disabled', match: ['disabled'] },
            ],
            position: { start: 1496, end: 1515 },
            output: [
              { type: 'raw', value: '', position: { start: 1515, end: 1519 } },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.set',
                  key: 'field',
                  expression: [
                    { type: 'Twig.expression.type.variable', value: 'field', match: ['field'] },
                    {
                      type: 'Twig.expression.type.filter',
                      value: 'merge',
                      match: ['|merge', 'merge'],
                      params: [
                        { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                        { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                        {
                          type: 'Twig.expression.type.operator.binary',
                          value: ':',
                          precidence: 16,
                          associativity: 'rightToLeft',
                          operator: ':',
                          key: 'disabled',
                        },
                        { type: 'Twig.expression.type.bool', value: !0 },
                        { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                        {
                          type: 'Twig.expression.type.parameter.end',
                          value: ')',
                          match: [')'],
                          expression: !1,
                        },
                      ],
                    },
                  ],
                  position: { start: 1519, end: 1570 },
                },
                position: { start: 1519, end: 1570 },
              },
              { type: 'raw', value: '', position: { start: 1570, end: 1572 } },
            ],
          },
          position: { open: { start: 1496, end: 1515 }, close: { start: 1572, end: 1585 } },
        },
        { type: 'raw', value: '', position: { start: 1585, end: 1589 } },
        {
          type: 'raw',
          value: `\r
`,
          position: { start: 1657, end: 1659 },
        },
        {
          type: 'raw',
          value: `\r
\r
`,
          position: { start: 1724, end: 1728 },
        },
        { type: 'raw', value: '', position: { start: 1808, end: 1810 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-form-field' },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'required', match: ['required'] },
              { type: 'Twig.expression.type.string', value: 'ps-form-field--required' },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
              { type: 'Twig.expression.type.comma' },
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
              { type: 'Twig.expression.type.string', value: 'ps-form-field--error' },
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
              { type: 'Twig.expression.type.string', value: 'ps-form-field--disabled' },
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
            ],
            position: { start: 1810, end: 1994 },
          },
          position: { start: 1810, end: 1994 },
        },
        {
          type: 'raw',
          value: `<div\r
  class="`,
          position: { start: 1994, end: 2013 },
        },
        {
          type: 'output',
          position: { start: 2013, end: 2041 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'classes',
              match: ['classes'],
              position: { start: 2013, end: 2041 },
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'join',
              match: ['|join', 'join'],
              position: { start: 2013, end: 2041 },
              params: [
                {
                  type: 'Twig.expression.type.parameter.start',
                  value: '(',
                  match: ['('],
                  position: { start: 2013, end: 2041 },
                },
                {
                  type: 'Twig.expression.type.string',
                  value: ' ',
                  position: { start: 2013, end: 2041 },
                },
                {
                  type: 'Twig.expression.type.parameter.end',
                  value: ')',
                  match: [')'],
                  position: { start: 2013, end: 2041 },
                  expression: !1,
                },
              ],
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'trim',
              match: ['|trim', 'trim'],
              position: { start: 2013, end: 2041 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"\r
  `,
          position: { start: 2041, end: 2046 },
        },
        {
          type: 'output',
          position: { start: 2046, end: 2062 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'attributes',
              match: ['attributes'],
              position: { start: 2046, end: 2062 },
            },
          ],
        },
        {
          type: 'raw',
          value: `\r
>`,
          position: { start: 2062, end: 2069 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'label', match: ['label'] }],
            position: { start: 2069, end: 2085 },
            output: [
              { type: 'raw', value: '', position: { start: 2085, end: 2091 } },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.include',
                  only: 4,
                  ignoreMissing: !1,
                  stack: [
                    { type: 'Twig.expression.type.string', value: '@elements/label/label.twig' },
                  ],
                  withStack: [
                    { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'text',
                    },
                    { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'forId',
                    },
                    { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'required',
                    },
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'required',
                      match: ['required'],
                    },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'disabled',
                    },
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'disabled',
                      match: ['disabled'],
                    },
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
                    { type: 'Twig.expression.type.key.period', key: 'addClass' },
                    {
                      type: 'Twig.expression.type.parameter.end',
                      value: ')',
                      match: [')'],
                      expression: !0,
                      params: [
                        { type: 'Twig.expression.type.string', value: 'ps-form-field__label' },
                      ],
                    },
                    { type: 'Twig.expression.type.comma' },
                    { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                  ],
                  position: { start: 2091, end: 2316 },
                },
                position: { start: 2091, end: 2316 },
              },
              { type: 'raw', value: '', position: { start: 2316, end: 2320 } },
            ],
          },
          position: { open: { start: 2069, end: 2085 }, close: { start: 2320, end: 2333 } },
        },
        {
          type: 'raw',
          value: `<div class="ps-form-field__input-wrapper">\r
    `,
          position: { start: 2333, end: 2387 },
        },
        { type: 'raw', value: '', position: { start: 2459, end: 2465 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'helper_id',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'helperText', match: ['helperText'] },
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
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
              { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
              { type: 'Twig.expression.type.string', value: '-helper' },
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
            ],
            position: { start: 2465, end: 2537 },
          },
          position: { start: 2465, end: 2537 },
        },
        { type: 'raw', value: '', position: { start: 2537, end: 2543 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'error_id',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
              { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
              { type: 'Twig.expression.type.string', value: '-error' },
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
            ],
            position: { start: 2543, end: 2594 },
          },
          position: { start: 2543, end: 2594 },
        },
        { type: 'raw', value: '', position: { start: 2594, end: 2600 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'described_by',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
              { type: 'Twig.expression.type.variable', value: 'error_id', match: ['error_id'] },
              { type: 'Twig.expression.type.variable', value: 'helper_id', match: ['helper_id'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '?',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: '?',
              },
            ],
            position: { start: 2600, end: 2655 },
          },
          position: { start: 2600, end: 2655 },
        },
        { type: 'raw', value: '', position: { start: 2655, end: 2663 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'fieldProps',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'field', match: ['field'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'merge',
                match: ['|merge', 'merge'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: ':',
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: ':',
                    key: 'id',
                  },
                  { type: 'Twig.expression.type.variable', value: 'id', match: ['id'] },
                  { type: 'Twig.expression.type.comma' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: ':',
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: ':',
                    key: 'error',
                  },
                  { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
                  { type: 'Twig.expression.type.comma' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: ':',
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: ':',
                    key: 'hideError',
                  },
                  { type: 'Twig.expression.type.bool', value: !0 },
                  { type: 'Twig.expression.type.comma' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: ':',
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: ':',
                    key: 'describedBy',
                  },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'described_by',
                    match: ['described_by'],
                  },
                  { type: 'Twig.expression.type.comma' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: ':',
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: ':',
                    key: 'errorId',
                  },
                  { type: 'Twig.expression.type.variable', value: 'error_id', match: ['error_id'] },
                  { type: 'Twig.expression.type.comma' },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: ':',
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: ':',
                    key: 'helperId',
                  },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'helper_id',
                    match: ['helper_id'],
                  },
                  { type: 'Twig.expression.type.comma' },
                  { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 2663, end: 2855 },
          },
          position: { start: 2663, end: 2855 },
        },
        {
          type: 'raw',
          value: `\r
\r
    `,
          position: { start: 2855, end: 2863 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.include',
            only: !1,
            ignoreMissing: !1,
            stack: [{ type: 'Twig.expression.type.string', value: '@elements/field/field.twig' }],
            withStack: [
              { type: 'Twig.expression.type.variable', value: 'fieldProps', match: ['fieldProps'] },
            ],
            position: { start: 2863, end: 2921 },
          },
          position: { start: 2863, end: 2921 },
        },
        {
          type: 'raw',
          value: `\r
  </div>`,
          position: { start: 2921, end: 2937 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'helperText', match: ['helperText'] },
              { type: 'Twig.expression.type.variable', value: 'error', match: ['error'] },
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
            position: { start: 2937, end: 2972 },
            output: [
              {
                type: 'raw',
                value: '<div class="ps-form-field__helper" id="',
                position: { start: 2972, end: 3017 },
              },
              {
                type: 'output',
                position: { start: 3017, end: 3025 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'id',
                    match: ['id'],
                    position: { start: 3017, end: 3025 },
                  },
                ],
              },
              { type: 'raw', value: '-helper">', position: { start: 3025, end: 3034 } },
              {
                type: 'output',
                position: { start: 3034, end: 3050 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'helperText',
                    match: ['helperText'],
                    position: { start: 3034, end: 3050 },
                  },
                ],
              },
              { type: 'raw', value: '</div>', position: { start: 3050, end: 3060 } },
            ],
          },
          position: { open: { start: 2937, end: 2972 }, close: { start: 3060, end: 3073 } },
        },
        { type: 'raw', value: '', position: { start: 3073, end: 3079 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [{ type: 'Twig.expression.type.variable', value: 'error', match: ['error'] }],
            position: { start: 3079, end: 3095 },
            output: [
              {
                type: 'raw',
                value: '<div class="ps-form-field__error" id="',
                position: { start: 3095, end: 3139 },
              },
              {
                type: 'output',
                position: { start: 3139, end: 3147 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'id',
                    match: ['id'],
                    position: { start: 3139, end: 3147 },
                  },
                ],
              },
              {
                type: 'raw',
                value: `-error" role="alert" aria-live="polite">\r
      <svg class="ps-form-field__error-icon" viewBox="0 0 24 24" aria-hidden="true" width="16" height="16">\r
        <circle cx="12" cy="12" r="11" fill="none" stroke="currentColor" stroke-width="2"/>\r
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>\r
      </svg>\r
      <span>`,
                position: { start: 3147, end: 3518 },
              },
              {
                type: 'output',
                position: { start: 3518, end: 3529 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'error',
                    match: ['error'],
                    position: { start: 3518, end: 3529 },
                  },
                ],
              },
              {
                type: 'raw',
                value: `</span>\r
    </div>`,
                position: { start: 3529, end: 3552 },
              },
            ],
          },
          position: { open: { start: 3079, end: 3095 }, close: { start: 3552, end: 3565 } },
        },
        {
          type: 'raw',
          value: `</div>\r
`,
          position: { start: 3565, end: 3565 },
        },
      ],
      precompiled: !0,
    });
    y.options.allowInlineIncludes = !0;
    try {
      let i = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(i) || (i = Object.entries(i)), d(y.render({ attributes: new V(i), ...t }))
      );
    } catch (i) {
      return d(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/form-field/form-field.twig: ' +
          i.toString()
      );
    }
  },
  O = {
    label: 'Email Address',
    id: 'email-field',
    field: {
      type: 'email',
      value: '',
      placeholder: 'Enter your email address',
      disabled: !1,
      icon: '',
      iconPosition: 'right',
    },
    helperText: 'We will never share your email with anyone.',
    error: '',
    required: !1,
    disabled: !1,
  },
  Q = {
    title: 'Components/FormField',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component: `Complete form field with label, input, helper text, and error message. Wraps ps-field atom with form semantics.

See Props, Showcases, and README for details on states, accessibility, and integration.`,
        },
      },
    },
    argTypes: {
      label: {
        name: 'label',
        description: 'Label text for the field',
        control: 'text',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      helperText: {
        name: 'helperText',
        description: 'Optional helper text below field (hidden when error is present)',
        control: 'text',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      error: {
        name: 'error',
        description: 'Error message to display (replaces helper text, sets error state)',
        control: 'text',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      'field.placeholder': {
        name: 'field.placeholder',
        description: 'Placeholder text for the input field',
        control: 'text',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      'field.value': {
        name: 'field.value',
        description: 'Current value of the field',
        control: 'text',
        table: { category: 'Content', type: { summary: 'string' } },
      },
      'field.type': {
        name: 'field.type',
        description: 'Input field type',
        control: 'select',
        options: ['text', 'email', 'number', 'search', 'textarea', 'select'],
        table: {
          category: 'Appearance',
          type: { summary: 'string' },
          defaultValue: { summary: 'text' },
        },
      },
      'field.icon': {
        name: 'field.icon',
        description: 'Icon name (without "icon-" prefix)',
        control: 'text',
        table: { category: 'Appearance', type: { summary: 'string' } },
      },
      'field.iconPosition': {
        name: 'field.iconPosition',
        description: 'Icon position',
        control: 'select',
        options: ['left', 'right'],
        table: {
          category: 'Appearance',
          type: { summary: 'string' },
          defaultValue: { summary: 'right' },
        },
      },
      required: {
        name: 'required',
        description: 'Mark field as required (shows asterisk)',
        control: 'boolean',
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      disabled: {
        name: 'disabled',
        description: 'Disable entire field group',
        control: 'boolean',
        table: {
          category: 'Behavior',
          type: { summary: 'boolean' },
          defaultValue: { summary: !1 },
        },
      },
      id: {
        name: 'id',
        description: 'Unique ID for label/field association (auto-generated if omitted)',
        control: 'text',
        table: { category: 'Accessibility', type: { summary: 'string' } },
      },
    },
  },
  a = { render: (t) => e(t), args: { ...O } },
  r = {
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Default (empty) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Default (empty)</h3>
        ${e({ label: 'Email Address', field: { type: 'email', placeholder: 'Enter your email' }, helperText: 'We will never share your email with anyone.' })}
      </div>

      <!-- Filled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Filled</h3>
        ${e({ label: 'Full Name', field: { type: 'text', value: 'Jean Dupont', placeholder: 'Enter your name' }, helperText: 'Please enter your full legal name.' })}
      </div>

      <!-- Required -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Required</h3>
        ${e({ label: 'Phone Number', field: { type: 'text', placeholder: '+33 1 23 45 67 89' }, required: !0, helperText: 'Required for account verification.' })}
      </div>

      <!-- Error -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error</h3>
        ${e({ label: 'Email Address', field: { type: 'email', value: 'invalid-email', placeholder: 'Enter your email' }, error: 'Please enter a valid email address.', required: !0 })}
      </div>

      <!-- Disabled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Disabled</h3>
        ${e({ label: 'Account Type', field: { type: 'text', value: 'Premium Member', placeholder: 'Account type' }, disabled: !0, helperText: 'This field cannot be modified.' })}
      </div>
    </div>
  `,
  },
  s = {
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Icon Right (default) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Right</h3>
        ${e({ label: 'Search', field: { type: 'search', placeholder: 'Search properties...', icon: 'search', iconPosition: 'right' }, helperText: 'Enter keywords to search our database.' })}
      </div>

      <!-- Icon Left -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Left</h3>
        ${e({ label: 'Email', field: { type: 'email', placeholder: 'example@domain.com', icon: 'mail', iconPosition: 'left' }, helperText: 'We will send a confirmation email.' })}
      </div>
    </div>
  `,
  },
  p = {
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Helper linked to input</h3>
        ${e({ id: 'field-helper-demo', label: 'Username', helperText: 'Use 6-20 characters, no spaces.', field: { type: 'text', placeholder: 'your-name' } })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error linked to input</h3>
        ${e({ id: 'field-error-demo', label: 'Email', field: { type: 'email', value: 'invalid-email', placeholder: 'example@domain.com' }, error: 'Veuillez saisir une adresse email valide.', required: !0 })}
      </div>
    </div>
  `,
  },
  o = {
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Text -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Text Input</h3>
        ${e({ label: 'Full Name', field: { type: 'text', placeholder: 'John Doe' }, helperText: 'Enter your first and last name.' })}
      </div>

      <!-- Email -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Email Input</h3>
        ${e({ label: 'Email Address', field: { type: 'email', placeholder: 'example@domain.com' }, required: !0, helperText: 'Valid email format required.' })}
      </div>

      <!-- Number -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Number Input</h3>
        ${e({ label: 'Property Size (m²)', field: { type: 'number', placeholder: '75' }, helperText: 'Enter the total area in square meters.' })}
      </div>

      <!-- Textarea -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Textarea</h3>
        ${e({ label: 'Description', field: { type: 'textarea', placeholder: 'Enter a detailed description...' }, helperText: 'Provide additional details (optional).' })}
      </div>
    </div>
  `,
  },
  n = {
    render: () => `
    <form style="display: flex; flex-direction: column; gap: var(--size-5); max-width: 480px; padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      <h2 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-700); color: var(--gray-900);">Contact Information</h2>
      
      ${e({ label: 'Full Name', id: 'contact-name', field: { type: 'text', placeholder: 'Jean Dupont' }, required: !0 })}

      ${e({ label: 'Email Address', id: 'contact-email', field: { type: 'email', placeholder: 'jean.dupont@example.com' }, required: !0, helperText: 'We will send a confirmation to this address.' })}

      ${e({ label: 'Phone Number', id: 'contact-phone', field: { type: 'text', placeholder: '+33 1 23 45 67 89' }, helperText: 'Optional - for SMS notifications.' })}

      ${e({ label: 'Message', id: 'contact-message', field: { type: 'textarea', placeholder: 'How can we help you?' }, required: !0, helperText: 'Please provide details about your inquiry.' })}

      <button 
        type="submit" 
        style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: var(--font-weight-600); cursor: pointer;"
        onmouseover="this.style.background='var(--primary-hover)'"
        onmouseout="this.style.background='var(--primary)'"
      >
        Submit Form
      </button>
    </form>
  `,
  };
var c, u, v, g, m;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((c = a.parameters) == null ? void 0 : c.docs),
    source: {
      originalSource: `{
  render: args => formFieldTwig(args),
  args: {
    ...formFieldData
  }
}`,
      ...((v = (u = a.parameters) == null ? void 0 : u.docs) == null ? void 0 : v.source),
    },
    description: {
      story: 'Default FormField - Email input with helper text',
      ...((m = (g = a.parameters) == null ? void 0 : g.docs) == null ? void 0 : m.description),
    },
  },
};
var w, x, T, h, f;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((w = r.parameters) == null ? void 0 : w.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Default (empty) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Default (empty)</h3>
        \${formFieldTwig({
    label: 'Email Address',
    field: {
      type: 'email',
      placeholder: 'Enter your email'
    },
    helperText: 'We will never share your email with anyone.'
  })}
      </div>

      <!-- Filled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Filled</h3>
        \${formFieldTwig({
    label: 'Full Name',
    field: {
      type: 'text',
      value: 'Jean Dupont',
      placeholder: 'Enter your name'
    },
    helperText: 'Please enter your full legal name.'
  })}
      </div>

      <!-- Required -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Required</h3>
        \${formFieldTwig({
    label: 'Phone Number',
    field: {
      type: 'text',
      placeholder: '+33 1 23 45 67 89'
    },
    required: true,
    helperText: 'Required for account verification.'
  })}
      </div>

      <!-- Error -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error</h3>
        \${formFieldTwig({
    label: 'Email Address',
    field: {
      type: 'email',
      value: 'invalid-email',
      placeholder: 'Enter your email'
    },
    error: 'Please enter a valid email address.',
    required: true
  })}
      </div>

      <!-- Disabled -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Disabled</h3>
        \${formFieldTwig({
    label: 'Account Type',
    field: {
      type: 'text',
      value: 'Premium Member',
      placeholder: 'Account type'
    },
    disabled: true,
    helperText: 'This field cannot be modified.'
  })}
      </div>
    </div>
  \`
}`,
      ...((T = (x = r.parameters) == null ? void 0 : x.docs) == null ? void 0 : T.source),
    },
    description: {
      story: `All Field States Showcase\r
Demonstrates default, filled, error, and disabled states`,
      ...((f = (h = r.parameters) == null ? void 0 : h.docs) == null ? void 0 : f.description),
    },
  },
};
var b, k, z, E, I;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((b = s.parameters) == null ? void 0 : b.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Icon Right (default) -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Right</h3>
        \${formFieldTwig({
    label: 'Search',
    field: {
      type: 'search',
      placeholder: 'Search properties...',
      icon: 'search',
      iconPosition: 'right'
    },
    helperText: 'Enter keywords to search our database.'
  })}
      </div>

      <!-- Icon Left -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Icon Left</h3>
        \${formFieldTwig({
    label: 'Email',
    field: {
      type: 'email',
      placeholder: 'example@domain.com',
      icon: 'mail',
      iconPosition: 'left'
    },
    helperText: 'We will send a confirmation email.'
  })}
      </div>
    </div>
  \`
}`,
      ...((z = (k = s.parameters) == null ? void 0 : k.docs) == null ? void 0 : z.source),
    },
    description: {
      story: 'With Icon - Shows fields with left and right positioned icons',
      ...((I = (E = s.parameters) == null ? void 0 : E.docs) == null ? void 0 : I.description),
    },
  },
};
var _, q, F, D, L;
p.parameters = {
  ...p.parameters,
  docs: {
    ...((_ = p.parameters) == null ? void 0 : _.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Helper linked to input</h3>
        \${formFieldTwig({
    id: 'field-helper-demo',
    label: 'Username',
    helperText: 'Use 6-20 characters, no spaces.',
    field: {
      type: 'text',
      placeholder: 'your-name'
    }
  })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Error linked to input</h3>
        \${formFieldTwig({
    id: 'field-error-demo',
    label: 'Email',
    field: {
      type: 'email',
      value: 'invalid-email',
      placeholder: 'example@domain.com'
    },
    error: 'Veuillez saisir une adresse email valide.',
    required: true
  })}
      </div>
    </div>
  \`
}`,
      ...((F = (q = p.parameters) == null ? void 0 : q.docs) == null ? void 0 : F.source),
    },
    description: {
      story: 'Accessibility: helper vs. error linkage (aria-describedby / aria-errormessage)',
      ...((L = (D = p.parameters) == null ? void 0 : D.docs) == null ? void 0 : L.description),
    },
  },
};
var P, $, A, C, R;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((P = o.parameters) == null ? void 0 : P.docs),
    source: {
      originalSource: `{
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <!-- Text -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Text Input</h3>
        \${formFieldTwig({
    label: 'Full Name',
    field: {
      type: 'text',
      placeholder: 'John Doe'
    },
    helperText: 'Enter your first and last name.'
  })}
      </div>

      <!-- Email -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Email Input</h3>
        \${formFieldTwig({
    label: 'Email Address',
    field: {
      type: 'email',
      placeholder: 'example@domain.com'
    },
    required: true,
    helperText: 'Valid email format required.'
  })}
      </div>

      <!-- Number -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Number Input</h3>
        \${formFieldTwig({
    label: 'Property Size (m²)',
    field: {
      type: 'number',
      placeholder: '75'
    },
    helperText: 'Enter the total area in square meters.'
  })}
      </div>

      <!-- Textarea -->
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-1); color: var(--gray-700);">Textarea</h3>
        \${formFieldTwig({
    label: 'Description',
    field: {
      type: 'textarea',
      placeholder: 'Enter a detailed description...'
    },
    helperText: 'Provide additional details (optional).'
  })}
      </div>
    </div>
  \`
}`,
      ...((A = ($ = o.parameters) == null ? void 0 : $.docs) == null ? void 0 : A.source),
    },
    description: {
      story: 'Different Field Types - Text, Email, Number, Textarea',
      ...((R = (C = o.parameters) == null ? void 0 : C.docs) == null ? void 0 : R.description),
    },
  },
};
var S, j, N, M, W;
n.parameters = {
  ...n.parameters,
  docs: {
    ...((S = n.parameters) == null ? void 0 : S.docs),
    source: {
      originalSource: `{
  render: () => \`
    <form style="display: flex; flex-direction: column; gap: var(--size-5); max-width: 480px; padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      <h2 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-3); font-weight: var(--font-weight-700); color: var(--gray-900);">Contact Information</h2>
      
      \${formFieldTwig({
    label: 'Full Name',
    id: 'contact-name',
    field: {
      type: 'text',
      placeholder: 'Jean Dupont'
    },
    required: true
  })}

      \${formFieldTwig({
    label: 'Email Address',
    id: 'contact-email',
    field: {
      type: 'email',
      placeholder: 'jean.dupont@example.com'
    },
    required: true,
    helperText: 'We will send a confirmation to this address.'
  })}

      \${formFieldTwig({
    label: 'Phone Number',
    id: 'contact-phone',
    field: {
      type: 'text',
      placeholder: '+33 1 23 45 67 89'
    },
    helperText: 'Optional - for SMS notifications.'
  })}

      \${formFieldTwig({
    label: 'Message',
    id: 'contact-message',
    field: {
      type: 'textarea',
      placeholder: 'How can we help you?'
    },
    required: true,
    helperText: 'Please provide details about your inquiry.'
  })}

      <button 
        type="submit" 
        style="padding: var(--size-3) var(--size-6); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-1); font-size: var(--font-size-1); font-weight: var(--font-weight-600); cursor: pointer;"
        onmouseover="this.style.background='var(--primary-hover)'"
        onmouseout="this.style.background='var(--primary)'"
      >
        Submit Form
      </button>
    </form>
  \`
}`,
      ...((N = (j = n.parameters) == null ? void 0 : j.docs) == null ? void 0 : N.source),
    },
    description: {
      story: 'In Form Context - Multiple fields in a realistic form layout',
      ...((W = (M = n.parameters) == null ? void 0 : M.docs) == null ? void 0 : W.description),
    },
  },
};
const X = [
  'Default',
  'AllStates',
  'WithIcon',
  'AccessibilityIds',
  'AllFieldTypes',
  'InFormContext',
];
export {
  p as AccessibilityIds,
  o as AllFieldTypes,
  r as AllStates,
  a as Default,
  n as InFormContext,
  s as WithIcon,
  X as __namedExportsOrder,
  Q as default,
};
