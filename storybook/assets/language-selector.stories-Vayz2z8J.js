import { T as L, t as z } from './iframe-D21U4yYN.js';
import { D as R, a as S } from './twig-BPJOkNgt.js';
import './flag-CNh5c8TV.js';
S(L);
L.cache(!1);
z.twig({
  id: '@elements/flag/flag.twig',
  data: [
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
        position: { start: 733, end: 770 },
      },
      position: { start: 733, end: 770 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'shape',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'default',
            match: ['|default', 'default'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.string', value: 'square' },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
        ],
        position: { start: 771, end: 814 },
      },
      position: { start: 771, end: 814 },
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
        position: { start: 815, end: 861 },
      },
      position: { start: 815, end: 861 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'decorative',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'decorative', match: ['decorative'] },
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
        position: { start: 862, end: 912 },
      },
      position: { start: 862, end: 912 },
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
        position: { start: 913, end: 976 },
      },
      position: { start: 913, end: 976 },
    },
    { type: 'raw', value: '', position: { start: 977, end: 978 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'classes',
        expression: [
          { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
          { type: 'Twig.expression.type.string', value: 'ps-flag' },
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
          { type: 'Twig.expression.type.string', value: 'ps-flag--' },
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
          { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
          { type: 'Twig.expression.type.string', value: 'square' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '!=',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '!=',
          },
          { type: 'Twig.expression.type.string', value: 'ps-flag--' },
          { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
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
          { type: 'Twig.expression.type.string', value: 'ps-flag--disabled' },
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
        position: { start: 978, end: 1150 },
      },
      position: { start: 978, end: 1150 },
    },
    { type: 'raw', value: '', position: { start: 1151, end: 1152 } },
    { type: 'raw', value: '', position: { start: 1221, end: 1222 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'locale_norm',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'locale', match: ['locale'] },
          { type: 'Twig.expression.type.test', filter: 'defined' },
          { type: 'Twig.expression.type.variable', value: 'locale', match: ['locale'] },
          {
            type: 'Twig.expression.type.operator.binary',
            value: 'and',
            precidence: 13,
            associativity: 'leftToRight',
            operator: 'and',
          },
          { type: 'Twig.expression.type.variable', value: 'locale', match: ['locale'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'replace',
            match: ['|replace', 'replace'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: ':',
                precidence: 16,
                associativity: 'rightToLeft',
                operator: ':',
                key: '_',
              },
              { type: 'Twig.expression.type.string', value: '-' },
              { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
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
        position: { start: 1222, end: 1311 },
      },
      position: { start: 1222, end: 1311 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'parts',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'locale_norm', match: ['locale_norm'] },
          { type: 'Twig.expression.type.variable', value: 'locale_norm', match: ['locale_norm'] },
          {
            type: 'Twig.expression.type.filter',
            value: 'split',
            match: ['|split', 'split'],
            params: [
              { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
              { type: 'Twig.expression.type.string', value: '-' },
              {
                type: 'Twig.expression.type.parameter.end',
                value: ')',
                match: [')'],
                expression: !1,
              },
            ],
          },
          { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
          { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
        ],
        position: { start: 1312, end: 1373 },
      },
      position: { start: 1312, end: 1373 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'region',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'parts', match: ['parts'] },
          { type: 'Twig.expression.type.filter', value: 'length', match: ['|length', 'length'] },
          { type: 'Twig.expression.type.number', value: 1, match: ['1', null] },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '>',
            precidence: 8,
            associativity: 'leftToRight',
            operator: '>',
          },
          { type: 'Twig.expression.type.variable', value: 'parts', match: ['parts'] },
          {
            type: 'Twig.expression.type.key.brackets',
            stack: [{ type: 'Twig.expression.type.number', value: 1, match: ['1', null] }],
          },
          { type: 'Twig.expression.type.filter', value: 'length', match: ['|length', 'length'] },
          { type: 'Twig.expression.type.number', value: 2, match: ['2', null] },
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
          { type: 'Twig.expression.type.variable', value: 'parts', match: ['parts'] },
          {
            type: 'Twig.expression.type.key.brackets',
            stack: [{ type: 'Twig.expression.type.number', value: 1, match: ['1', null] }],
          },
          { type: 'Twig.expression.type.filter', value: 'upper', match: ['|upper', 'upper'] },
          { type: 'Twig.expression.type.null', value: null },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
        ],
        position: { start: 1374, end: 1460 },
      },
      position: { start: 1374, end: 1460 },
    },
    { type: 'raw', value: '', position: { start: 1461, end: 1462 } },
    { type: 'raw', value: '', position: { start: 1529, end: 1530 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'country_code',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'code', match: ['code'] },
          { type: 'Twig.expression.type.test', filter: 'defined' },
          { type: 'Twig.expression.type.variable', value: 'code', match: ['code'] },
          {
            type: 'Twig.expression.type.operator.binary',
            value: 'and',
            precidence: 13,
            associativity: 'leftToRight',
            operator: 'and',
          },
          { type: 'Twig.expression.type.variable', value: 'code', match: ['code'] },
          { type: 'Twig.expression.type.filter', value: 'upper', match: ['|upper', 'upper'] },
          {
            type: 'Twig.expression.type.subexpression.end',
            value: ')',
            match: [')'],
            expression: !0,
            params: [
              { type: 'Twig.expression.type.variable', value: 'region', match: ['region'] },
              { type: 'Twig.expression.type.null', value: null },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '??',
                precidence: 15,
                associativity: 'rightToLeft',
                operator: '??',
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
        ],
        position: { start: 1530, end: 1613 },
      },
      position: { start: 1530, end: 1613 },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'asset_code',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'country_code', match: ['country_code'] },
          { type: 'Twig.expression.type.variable', value: 'country_code', match: ['country_code'] },
          { type: 'Twig.expression.type.filter', value: 'lower', match: ['|lower', 'lower'] },
          { type: 'Twig.expression.type.string', value: 'xx' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '?',
            precidence: 16,
            associativity: 'rightToLeft',
            operator: '?',
          },
        ],
        position: { start: 1614, end: 1679 },
      },
      position: { start: 1614, end: 1679 },
    },
    { type: 'raw', value: '', position: { start: 1680, end: 1681 } },
    { type: 'raw', value: '', position: { start: 1705, end: 1706 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'img_src',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'src', match: ['src'] },
          {
            type: 'Twig.expression.type.subexpression.end',
            value: ')',
            match: [')'],
            expression: !0,
            params: [
              { type: 'Twig.expression.type.string', value: '/flags/' },
              { type: 'Twig.expression.type.variable', value: 'asset_code', match: ['asset_code'] },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
              },
              { type: 'Twig.expression.type.string', value: '.svg' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '~',
                precidence: 6,
                associativity: 'leftToRight',
                operator: '~',
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
        position: { start: 1706, end: 1768 },
      },
      position: { start: 1706, end: 1768 },
    },
    { type: 'raw', value: '', position: { start: 1769, end: 1770 } },
    { type: 'raw', value: '', position: { start: 1790, end: 1791 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'alt_text',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'decorative', match: ['decorative'] },
          { type: 'Twig.expression.type.string', value: '' },
          {
            type: 'Twig.expression.type.subexpression.end',
            value: ')',
            match: [')'],
            expression: !0,
            params: [
              { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
              {
                type: 'Twig.expression.type.variable',
                value: 'country_code',
                match: ['country_code'],
              },
              { type: 'Twig.expression.type.string', value: 'Flag' },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '??',
                precidence: 15,
                associativity: 'rightToLeft',
                operator: '??',
              },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '??',
                precidence: 15,
                associativity: 'rightToLeft',
                operator: '??',
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
        ],
        position: { start: 1791, end: 1865 },
      },
      position: { start: 1791, end: 1865 },
    },
    { type: 'raw', value: '', position: { start: 1866, end: 1867 } },
    { type: 'raw', value: '', position: { start: 1894, end: 1895 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.set',
        key: 'title_text',
        expression: [
          { type: 'Twig.expression.type.variable', value: 'decorative', match: ['decorative'] },
          { type: 'Twig.expression.type.string', value: '' },
          {
            type: 'Twig.expression.type.subexpression.end',
            value: ')',
            match: [')'],
            expression: !0,
            params: [
              { type: 'Twig.expression.type.variable', value: 'label', match: ['label'] },
              {
                type: 'Twig.expression.type.variable',
                value: 'country_code',
                match: ['country_code'],
              },
              {
                type: 'Twig.expression.type.operator.binary',
                value: '??',
                precidence: 15,
                associativity: 'rightToLeft',
                operator: '??',
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
        ],
        position: { start: 1895, end: 1961 },
      },
      position: { start: 1895, end: 1961 },
    },
    { type: 'raw', value: '', position: { start: 1962, end: 1963 } },
    { type: 'raw', value: '', position: { start: 2044, end: 2045 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
          { type: 'Twig.expression.type.string', value: 'xs' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '==',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '==',
          },
        ],
        position: { start: 2045, end: 2068 },
        output: [
          { type: 'raw', value: '', position: { start: 2069, end: 2071 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_height',
              expression: [{ type: 'Twig.expression.type.string', value: '12' }],
              position: { start: 2071, end: 2100 },
            },
            position: { start: 2071, end: 2100 },
          },
          { type: 'raw', value: '', position: { start: 2101, end: 2103 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_width',
              expression: [
                { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
                { type: 'Twig.expression.type.string', value: 'circle' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '==',
                  precidence: 9,
                  associativity: 'leftToRight',
                  operator: '==',
                },
                { type: 'Twig.expression.type.string', value: '12' },
                { type: 'Twig.expression.type.string', value: '16' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '?',
                  precidence: 16,
                  associativity: 'rightToLeft',
                  operator: '?',
                },
              ],
              position: { start: 2103, end: 2158 },
            },
            position: { start: 2103, end: 2158 },
          },
        ],
      },
      position: { open: { start: 2045, end: 2068 }, close: { start: 2159, end: 2186 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.elseif',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
          { type: 'Twig.expression.type.string', value: 'sm' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '==',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '==',
          },
        ],
        position: { start: 2159, end: 2186 },
        output: [
          { type: 'raw', value: '', position: { start: 2187, end: 2189 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_height',
              expression: [{ type: 'Twig.expression.type.string', value: '16' }],
              position: { start: 2189, end: 2218 },
            },
            position: { start: 2189, end: 2218 },
          },
          { type: 'raw', value: '', position: { start: 2219, end: 2221 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_width',
              expression: [
                { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
                { type: 'Twig.expression.type.string', value: 'circle' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '==',
                  precidence: 9,
                  associativity: 'leftToRight',
                  operator: '==',
                },
                { type: 'Twig.expression.type.string', value: '16' },
                { type: 'Twig.expression.type.string', value: '21' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '?',
                  precidence: 16,
                  associativity: 'rightToLeft',
                  operator: '?',
                },
              ],
              position: { start: 2221, end: 2276 },
            },
            position: { start: 2221, end: 2276 },
          },
        ],
      },
      position: { open: { start: 2159, end: 2186 }, close: { start: 2277, end: 2304 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.elseif',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
          { type: 'Twig.expression.type.string', value: 'lg' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '==',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '==',
          },
        ],
        position: { start: 2277, end: 2304 },
        output: [
          { type: 'raw', value: '', position: { start: 2305, end: 2307 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_height',
              expression: [{ type: 'Twig.expression.type.string', value: '24' }],
              position: { start: 2307, end: 2336 },
            },
            position: { start: 2307, end: 2336 },
          },
          { type: 'raw', value: '', position: { start: 2337, end: 2339 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_width',
              expression: [
                { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
                { type: 'Twig.expression.type.string', value: 'circle' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '==',
                  precidence: 9,
                  associativity: 'leftToRight',
                  operator: '==',
                },
                { type: 'Twig.expression.type.string', value: '24' },
                { type: 'Twig.expression.type.string', value: '32' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '?',
                  precidence: 16,
                  associativity: 'rightToLeft',
                  operator: '?',
                },
              ],
              position: { start: 2339, end: 2394 },
            },
            position: { start: 2339, end: 2394 },
          },
        ],
      },
      position: { open: { start: 2277, end: 2304 }, close: { start: 2395, end: 2422 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.elseif',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'size', match: ['size'] },
          { type: 'Twig.expression.type.string', value: 'xl' },
          {
            type: 'Twig.expression.type.operator.binary',
            value: '==',
            precidence: 9,
            associativity: 'leftToRight',
            operator: '==',
          },
        ],
        position: { start: 2395, end: 2422 },
        output: [
          { type: 'raw', value: '', position: { start: 2423, end: 2425 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_height',
              expression: [{ type: 'Twig.expression.type.string', value: '48' }],
              position: { start: 2425, end: 2454 },
            },
            position: { start: 2425, end: 2454 },
          },
          { type: 'raw', value: '', position: { start: 2455, end: 2457 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_width',
              expression: [
                { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
                { type: 'Twig.expression.type.string', value: 'circle' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '==',
                  precidence: 9,
                  associativity: 'leftToRight',
                  operator: '==',
                },
                { type: 'Twig.expression.type.string', value: '48' },
                { type: 'Twig.expression.type.string', value: '64' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '?',
                  precidence: 16,
                  associativity: 'rightToLeft',
                  operator: '?',
                },
              ],
              position: { start: 2457, end: 2512 },
            },
            position: { start: 2457, end: 2512 },
          },
        ],
      },
      position: { open: { start: 2395, end: 2422 }, close: { start: 2513, end: 2525 } },
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.else',
        match: ['else'],
        position: { start: 2513, end: 2525 },
        output: [
          { type: 'raw', value: '', position: { start: 2526, end: 2528 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_height',
              expression: [{ type: 'Twig.expression.type.string', value: '20' }],
              position: { start: 2528, end: 2557 },
            },
            position: { start: 2528, end: 2557 },
          },
          { type: 'raw', value: '', position: { start: 2558, end: 2560 } },
          {
            type: 'logic',
            token: {
              type: 'Twig.logic.type.set',
              key: 'img_width',
              expression: [
                { type: 'Twig.expression.type.variable', value: 'shape', match: ['shape'] },
                { type: 'Twig.expression.type.string', value: 'circle' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '==',
                  precidence: 9,
                  associativity: 'leftToRight',
                  operator: '==',
                },
                { type: 'Twig.expression.type.string', value: '20' },
                { type: 'Twig.expression.type.string', value: '27' },
                {
                  type: 'Twig.expression.type.operator.binary',
                  value: '?',
                  precidence: 16,
                  associativity: 'rightToLeft',
                  operator: '?',
                },
              ],
              position: { start: 2560, end: 2615 },
            },
            position: { start: 2560, end: 2615 },
          },
        ],
      },
      position: { open: { start: 2513, end: 2525 }, close: { start: 2616, end: 2629 } },
    },
    { type: 'raw', value: '<span ', position: { start: 2630, end: 2637 } },
    {
      type: 'output',
      position: { start: 2637, end: 2671 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'attributes',
          match: ['attributes'],
          position: { start: 2637, end: 2671 },
        },
        {
          type: 'Twig.expression.type.key.period',
          position: { start: 2637, end: 2671 },
          key: 'addClass',
        },
        {
          type: 'Twig.expression.type.parameter.end',
          value: ')',
          match: [')'],
          position: { start: 2637, end: 2671 },
          expression: !0,
          params: [
            {
              type: 'Twig.expression.type.variable',
              value: 'classes',
              match: ['classes'],
              position: { start: 2637, end: 2671 },
            },
          ],
        },
      ],
    },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'title_text', match: ['title_text'] },
        ],
        position: { start: 2671, end: 2690 },
        output: [
          { type: 'raw', value: ' title="', position: { start: 2690, end: 2698 } },
          {
            type: 'output',
            position: { start: 2698, end: 2714 },
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'title_text',
                match: ['title_text'],
                position: { start: 2698, end: 2714 },
              },
            ],
          },
          { type: 'raw', value: '"', position: { start: 2714, end: 2715 } },
        ],
      },
      position: { open: { start: 2671, end: 2690 }, close: { start: 2715, end: 2726 } },
    },
    {
      type: 'raw',
      value: `>
  <img class="ps-flag__img" src="`,
      position: { start: 2726, end: 2761 },
    },
    {
      type: 'output',
      position: { start: 2761, end: 2774 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'img_src',
          match: ['img_src'],
          position: { start: 2761, end: 2774 },
        },
      ],
    },
    { type: 'raw', value: '" alt="', position: { start: 2774, end: 2781 } },
    {
      type: 'output',
      position: { start: 2781, end: 2795 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'alt_text',
          match: ['alt_text'],
          position: { start: 2781, end: 2795 },
        },
      ],
    },
    { type: 'raw', value: '" width="', position: { start: 2795, end: 2804 } },
    {
      type: 'output',
      position: { start: 2804, end: 2819 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'img_width',
          match: ['img_width'],
          position: { start: 2804, end: 2819 },
        },
      ],
    },
    { type: 'raw', value: '" height="', position: { start: 2819, end: 2829 } },
    {
      type: 'output',
      position: { start: 2829, end: 2845 },
      stack: [
        {
          type: 'Twig.expression.type.variable',
          value: 'img_height',
          match: ['img_height'],
          position: { start: 2829, end: 2845 },
        },
      ],
    },
    { type: 'raw', value: '"', position: { start: 2845, end: 2846 } },
    {
      type: 'logic',
      token: {
        type: 'Twig.logic.type.if',
        stack: [
          { type: 'Twig.expression.type.variable', value: 'decorative', match: ['decorative'] },
        ],
        position: { start: 2846, end: 2865 },
        output: [
          { type: 'raw', value: ' aria-hidden="true"', position: { start: 2865, end: 2884 } },
        ],
      },
      position: { open: { start: 2846, end: 2865 }, close: { start: 2884, end: 2895 } },
    },
    {
      type: 'raw',
      value: ` />
</span>`,
      position: { start: 2895, end: 2895 },
    },
  ],
  precompiled: !0,
});
const l = (t) => t,
  e = (t = {}) => {
    const n = z.twig({
      id: 'C:/wamp64/www/ps_theme/source/patterns/components/language-selector/language-selector.twig',
      data: [
        { type: 'raw', value: '', position: { start: 700, end: 702 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'languages',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'languages', match: ['languages'] },
              {
                type: 'Twig.expression.type.filter',
                value: 'default',
                match: ['|default', 'default'],
                params: [
                  { type: 'Twig.expression.type.parameter.start', value: '(', match: ['('] },
                  { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
                  { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 702, end: 747 },
          },
          position: { start: 702, end: 747 },
        },
        { type: 'raw', value: '', position: { start: 747, end: 749 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'current',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'current', match: ['current'] },
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
            position: { start: 749, end: 792 },
          },
          position: { start: 749, end: 792 },
        },
        { type: 'raw', value: '', position: { start: 792, end: 794 } },
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
                  { type: 'Twig.expression.type.string', value: 'chevron-down' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 794, end: 841 },
          },
          position: { start: 794, end: 841 },
        },
        { type: 'raw', value: '', position: { start: 841, end: 843 } },
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
                  { type: 'Twig.expression.type.string', value: 'Language selection' },
                  {
                    type: 'Twig.expression.type.parameter.end',
                    value: ')',
                    match: [')'],
                    expression: !1,
                  },
                ],
              },
            ],
            position: { start: 843, end: 898 },
          },
          position: { start: 843, end: 898 },
        },
        { type: 'raw', value: '', position: { start: 898, end: 900 } },
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
            position: { start: 900, end: 937 },
          },
          position: { start: 900, end: 937 },
        },
        { type: 'raw', value: '', position: { start: 937, end: 939 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'modifier_class',
            expression: [
              {
                type: 'Twig.expression.type.variable',
                value: 'modifier_class',
                match: ['modifier_class'],
              },
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
            position: { start: 939, end: 996 },
          },
          position: { start: 939, end: 996 },
        },
        { type: 'raw', value: '', position: { start: 996, end: 1e3 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'classes',
            expression: [
              { type: 'Twig.expression.type.array.start', value: '[', match: ['['] },
              { type: 'Twig.expression.type.string', value: 'ps-language-selector' },
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
              { type: 'Twig.expression.type.string', value: 'ps-language-selector--' },
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
              {
                type: 'Twig.expression.type.variable',
                value: 'modifier_class',
                match: ['modifier_class'],
              },
              { type: 'Twig.expression.type.array.end', value: ']', match: [']'] },
            ],
            position: { start: 1e3, end: 1129 },
          },
          position: { start: 1e3, end: 1129 },
        },
        { type: 'raw', value: '', position: { start: 1129, end: 1133 } },
        { type: 'raw', value: '', position: { start: 1177, end: 1179 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.set',
            key: 'current_lang',
            expression: [{ type: 'Twig.expression.type.null', value: null }],
            position: { start: 1179, end: 1210 },
          },
          position: { start: 1179, end: 1210 },
        },
        { type: 'raw', value: '', position: { start: 1210, end: 1212 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'lang',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'languages', match: ['languages'] },
            ],
            position: { start: 1212, end: 1239 },
            output: [
              {
                type: 'raw',
                value: `\r
  `,
                position: { start: 1239, end: 1243 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.if',
                  stack: [
                    { type: 'Twig.expression.type.variable', value: 'lang', match: ['lang'] },
                    { type: 'Twig.expression.type.key.period', key: 'code' },
                    { type: 'Twig.expression.type.variable', value: 'current', match: ['current'] },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: '==',
                      precidence: 9,
                      associativity: 'leftToRight',
                      operator: '==',
                    },
                  ],
                  position: { start: 1243, end: 1272 },
                  output: [
                    { type: 'raw', value: '', position: { start: 1272, end: 1278 } },
                    {
                      type: 'logic',
                      token: {
                        type: 'Twig.logic.type.set',
                        key: 'current_lang',
                        expression: [
                          { type: 'Twig.expression.type.variable', value: 'lang', match: ['lang'] },
                        ],
                        position: { start: 1278, end: 1309 },
                      },
                      position: { start: 1278, end: 1309 },
                    },
                    { type: 'raw', value: '', position: { start: 1309, end: 1313 } },
                  ],
                },
                position: { open: { start: 1243, end: 1272 }, close: { start: 1313, end: 1324 } },
              },
              {
                type: 'raw',
                value: `\r
`,
                position: { start: 1324, end: 1326 },
              },
            ],
          },
          position: { open: { start: 1212, end: 1239 }, close: { start: 1326, end: 1338 } },
        },
        {
          type: 'raw',
          value: `\r
\r
<nav class="`,
          position: { start: 1338, end: 1354 },
        },
        {
          type: 'output',
          position: { start: 1354, end: 1382 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'classes',
              match: ['classes'],
              position: { start: 1354, end: 1382 },
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'join',
              match: ['|join', 'join'],
              position: { start: 1354, end: 1382 },
              params: [
                {
                  type: 'Twig.expression.type.parameter.start',
                  value: '(',
                  match: ['('],
                  position: { start: 1354, end: 1382 },
                },
                {
                  type: 'Twig.expression.type.string',
                  value: ' ',
                  position: { start: 1354, end: 1382 },
                },
                {
                  type: 'Twig.expression.type.parameter.end',
                  value: ')',
                  match: [')'],
                  position: { start: 1354, end: 1382 },
                  expression: !1,
                },
              ],
            },
            {
              type: 'Twig.expression.type.filter',
              value: 'trim',
              match: ['|trim', 'trim'],
              position: { start: 1354, end: 1382 },
            },
          ],
        },
        { type: 'raw', value: '" aria-label="', position: { start: 1382, end: 1396 } },
        {
          type: 'output',
          position: { start: 1396, end: 1407 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'label',
              match: ['label'],
              position: { start: 1396, end: 1407 },
            },
          ],
        },
        { type: 'raw', value: '" data-language-selector', position: { start: 1407, end: 1435 } },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              { type: 'Twig.expression.type.variable', value: 'attributes', match: ['attributes'] },
            ],
            position: { start: 1435, end: 1455 },
            output: [
              { type: 'raw', value: ' ', position: { start: 1455, end: 1456 } },
              {
                type: 'output',
                position: { start: 1456, end: 1472 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'attributes',
                    match: ['attributes'],
                    position: { start: 1456, end: 1472 },
                  },
                ],
              },
            ],
          },
          position: { open: { start: 1435, end: 1455 }, close: { start: 1472, end: 1484 } },
        },
        {
          type: 'raw',
          value: `>\r
  <button class="ps-language-selector__trigger"\r
    type="button"\r
    aria-haspopup="listbox"\r
    aria-expanded="false"\r
    aria-label="`,
          position: { start: 1484, end: 1629 },
        },
        {
          type: 'output',
          position: { start: 1629, end: 1640 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'label',
              match: ['label'],
              position: { start: 1629, end: 1640 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"\r
  >\r
    `,
          position: { start: 1640, end: 1652 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.if',
            stack: [
              {
                type: 'Twig.expression.type.variable',
                value: 'current_lang',
                match: ['current_lang'],
              },
            ],
            position: { start: 1652, end: 1673 },
            output: [
              {
                type: 'raw',
                value: `\r
      <span class="ps-language-selector__current">\r
        `,
                position: { start: 1673, end: 1735 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.include',
                  only: 4,
                  ignoreMissing: !1,
                  stack: [
                    { type: 'Twig.expression.type.string', value: '@elements/flag/flag.twig' },
                  ],
                  withStack: [
                    { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'code',
                    },
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'current_lang',
                      match: ['current_lang'],
                    },
                    { type: 'Twig.expression.type.key.period', key: 'country_code' },
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'current_lang',
                      match: ['current_lang'],
                    },
                    { type: 'Twig.expression.type.key.period', key: 'code' },
                    {
                      type: 'Twig.expression.type.filter',
                      value: 'upper',
                      match: ['|upper', 'upper'],
                    },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: '??',
                      precidence: 15,
                      associativity: 'rightToLeft',
                      operator: '??',
                    },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'label',
                    },
                    {
                      type: 'Twig.expression.type.variable',
                      value: 'current_lang',
                      match: ['current_lang'],
                    },
                    { type: 'Twig.expression.type.key.period', key: 'label' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'size',
                    },
                    { type: 'Twig.expression.type.string', value: 'sm' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'shape',
                    },
                    { type: 'Twig.expression.type.string', value: 'rounded' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'decorative',
                    },
                    { type: 'Twig.expression.type.bool', value: !0 },
                    { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                  ],
                  position: { start: 1735, end: 1987 },
                },
                position: { start: 1735, end: 1987 },
              },
              {
                type: 'raw',
                value: `\r
        <span class="ps-language-selector__label">`,
                position: { start: 1987, end: 2039 },
              },
              {
                type: 'output',
                position: { start: 2039, end: 2063 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'current_lang',
                    match: ['current_lang'],
                    position: { start: 2039, end: 2063 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 2039, end: 2063 },
                    key: 'label',
                  },
                ],
              },
              {
                type: 'raw',
                value: `</span>\r
      </span>\r
    `,
                position: { start: 2063, end: 2091 },
              },
            ],
          },
          position: { open: { start: 1652, end: 1673 }, close: { start: 2091, end: 2102 } },
        },
        {
          type: 'raw',
          value: `\r
    <svg class="ps-language-selector__icon" aria-hidden="true" width="16" height="16">\r
      <use href="#icon-`,
          position: { start: 2102, end: 2215 },
        },
        {
          type: 'output',
          position: { start: 2215, end: 2225 },
          stack: [
            {
              type: 'Twig.expression.type.variable',
              value: 'icon',
              match: ['icon'],
              position: { start: 2215, end: 2225 },
            },
          ],
        },
        {
          type: 'raw',
          value: `"></use>\r
    </svg>\r
  </button>\r
  <ul class="ps-language-selector__menu" role="listbox" hidden>\r
    `,
          position: { start: 2225, end: 2329 },
        },
        {
          type: 'logic',
          token: {
            type: 'Twig.logic.type.for',
            keyVar: null,
            valueVar: 'lang',
            expression: [
              { type: 'Twig.expression.type.variable', value: 'languages', match: ['languages'] },
            ],
            position: { start: 2329, end: 2356 },
            output: [
              {
                type: 'raw',
                value: `\r
      <li role="presentation">\r
        <a href="`,
                position: { start: 2356, end: 2407 },
              },
              {
                type: 'output',
                position: { start: 2407, end: 2428 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'lang',
                    match: ['lang'],
                    position: { start: 2407, end: 2428 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 2407, end: 2428 },
                    key: 'url',
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: '#',
                    position: { start: 2407, end: 2428 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '??',
                    position: { start: 2407, end: 2428 },
                    precidence: 15,
                    associativity: 'rightToLeft',
                    operator: '??',
                  },
                ],
              },
              {
                type: 'raw',
                value: `"\r
          class="ps-language-selector__option`,
                position: { start: 2428, end: 2476 },
              },
              {
                type: 'output',
                position: { start: 2476, end: 2549 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'lang',
                    match: ['lang'],
                    position: { start: 2476, end: 2549 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 2476, end: 2549 },
                    key: 'code',
                  },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'current',
                    match: ['current'],
                    position: { start: 2476, end: 2549 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '==',
                    position: { start: 2476, end: 2549 },
                    precidence: 9,
                    associativity: 'leftToRight',
                    operator: '==',
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: ' ps-language-selector__option--active',
                    position: { start: 2476, end: 2549 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: '',
                    position: { start: 2476, end: 2549 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '?',
                    position: { start: 2476, end: 2549 },
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: '?',
                  },
                ],
              },
              {
                type: 'raw',
                value: `"\r
          role="option"\r
          aria-selected="`,
                position: { start: 2549, end: 2602 },
              },
              {
                type: 'output',
                position: { start: 2602, end: 2647 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'lang',
                    match: ['lang'],
                    position: { start: 2602, end: 2647 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 2602, end: 2647 },
                    key: 'code',
                  },
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'current',
                    match: ['current'],
                    position: { start: 2602, end: 2647 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '==',
                    position: { start: 2602, end: 2647 },
                    precidence: 9,
                    associativity: 'leftToRight',
                    operator: '==',
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: 'true',
                    position: { start: 2602, end: 2647 },
                  },
                  {
                    type: 'Twig.expression.type.string',
                    value: 'false',
                    position: { start: 2602, end: 2647 },
                  },
                  {
                    type: 'Twig.expression.type.operator.binary',
                    value: '?',
                    position: { start: 2602, end: 2647 },
                    precidence: 16,
                    associativity: 'rightToLeft',
                    operator: '?',
                  },
                ],
              },
              {
                type: 'raw',
                value: `"\r
          data-lang="`,
                position: { start: 2647, end: 2671 },
              },
              {
                type: 'output',
                position: { start: 2671, end: 2686 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'lang',
                    match: ['lang'],
                    position: { start: 2671, end: 2686 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 2671, end: 2686 },
                    key: 'code',
                  },
                ],
              },
              {
                type: 'raw',
                value: `"\r
          tabindex="-1"\r
        >\r
          `,
                position: { start: 2686, end: 2735 },
              },
              {
                type: 'logic',
                token: {
                  type: 'Twig.logic.type.include',
                  only: 4,
                  ignoreMissing: !1,
                  stack: [
                    { type: 'Twig.expression.type.string', value: '@elements/flag/flag.twig' },
                  ],
                  withStack: [
                    { type: 'Twig.expression.type.object.start', value: '{', match: ['{'] },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'code',
                    },
                    { type: 'Twig.expression.type.variable', value: 'lang', match: ['lang'] },
                    { type: 'Twig.expression.type.key.period', key: 'country_code' },
                    { type: 'Twig.expression.type.variable', value: 'lang', match: ['lang'] },
                    { type: 'Twig.expression.type.key.period', key: 'code' },
                    {
                      type: 'Twig.expression.type.filter',
                      value: 'upper',
                      match: ['|upper', 'upper'],
                    },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: '??',
                      precidence: 15,
                      associativity: 'rightToLeft',
                      operator: '??',
                    },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'label',
                    },
                    { type: 'Twig.expression.type.variable', value: 'lang', match: ['lang'] },
                    { type: 'Twig.expression.type.key.period', key: 'label' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'size',
                    },
                    { type: 'Twig.expression.type.string', value: 'sm' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'shape',
                    },
                    { type: 'Twig.expression.type.string', value: 'rounded' },
                    { type: 'Twig.expression.type.comma' },
                    {
                      type: 'Twig.expression.type.operator.binary',
                      value: ':',
                      precidence: 16,
                      associativity: 'rightToLeft',
                      operator: ':',
                      key: 'decorative',
                    },
                    { type: 'Twig.expression.type.bool', value: !0 },
                    { type: 'Twig.expression.type.object.end', value: '}', match: ['}'] },
                  ],
                  position: { start: 2735, end: 2975 },
                },
                position: { start: 2735, end: 2975 },
              },
              {
                type: 'raw',
                value: `\r
          <span class="ps-language-selector__label">`,
                position: { start: 2975, end: 3029 },
              },
              {
                type: 'output',
                position: { start: 3029, end: 3045 },
                stack: [
                  {
                    type: 'Twig.expression.type.variable',
                    value: 'lang',
                    match: ['lang'],
                    position: { start: 3029, end: 3045 },
                  },
                  {
                    type: 'Twig.expression.type.key.period',
                    position: { start: 3029, end: 3045 },
                    key: 'label',
                  },
                ],
              },
              {
                type: 'raw',
                value: `</span>\r
        </a>\r
      </li>\r
    `,
                position: { start: 3045, end: 3085 },
              },
            ],
          },
          position: { open: { start: 2329, end: 2356 }, close: { start: 3085, end: 3097 } },
        },
        {
          type: 'raw',
          value: `\r
  </ul>\r
</nav>\r
`,
          position: { start: 3097, end: 3097 },
        },
      ],
      precompiled: !0,
    });
    n.options.allowInlineIncludes = !0;
    try {
      let i = t.defaultAttributes ? t.defaultAttributes : [];
      return (
        Array.isArray(i) || (i = Object.entries(i)), l(n.render({ attributes: new R(i), ...t }))
      );
    } catch (i) {
      return l(
        'An error occurred whilst rendering C:/wamp64/www/ps_theme/source/patterns/components/language-selector/language-selector.twig: ' +
          i.toString()
      );
    }
  },
  E = {
    languages: [
      { code: 'fr', label: 'Français', country_code: 'fr', url: '/fr' },
      { code: 'en', label: 'English', country_code: 'gb', url: '/en' },
      { code: 'de', label: 'Deutsch', country_code: 'de', url: '/de' },
      { code: 'es', label: 'Español', country_code: 'es', url: '/es' },
      { code: 'it', label: 'Italiano', country_code: 'it', url: '/it' },
      { code: 'nl', label: 'Nederlands', country_code: 'nl', url: '/nl' },
    ],
    current: 'fr',
    icon: 'chevron-down',
    label: 'Language selection',
    size: 'md',
  },
  j = {
    title: 'Components/Language Selector',
    tags: ['autodocs'],
    parameters: {
      docs: {
        description: {
          component:
            'Multi-language dropdown with flag icons for locale switching across BNP Paribas Real Estate markets. Integrates Flag (atom) component for visual country representation.',
        },
      },
    },
    render: (t) => e(t),
    args: E,
    argTypes: {
      languages: {
        control: { type: 'object' },
        description:
          'Array of language objects with `code` (language), `label` (display name), and `country_code` (for flag)',
        table: { category: 'Content' },
      },
      current: {
        control: { type: 'text' },
        description: 'Current active language code (ISO 639-1)',
        table: { category: 'Content' },
      },
      size: {
        control: { type: 'select' },
        options: ['sm', 'md', 'lg'],
        description: 'Size variant',
        table: { category: 'Appearance', defaultValue: { summary: 'md' } },
      },
      icon: {
        control: { type: 'text' },
        description: 'Icon name for dropdown indicator (no "icon-" prefix)',
        table: { category: 'Appearance', defaultValue: { summary: 'chevron-down' } },
      },
      label: {
        control: { type: 'text' },
        description: 'Accessible label for trigger button (screen readers)',
        table: { category: 'Accessibility', defaultValue: { summary: 'Language selection' } },
      },
      modifier_class: {
        control: { type: 'text' },
        description: 'Additional CSS classes',
        table: { category: 'Layout' },
      },
      attributes: {
        control: { type: 'object' },
        description: 'Additional HTML attributes',
        table: { category: 'Layout' },
      },
    },
  },
  a = { name: 'Default (European Markets with Flags)', args: E },
  r = {
    name: 'All European Markets',
    render: () =>
      e({
        languages: [
          { code: 'fr', label: 'Français', country_code: 'fr' },
          { code: 'en', label: 'English', country_code: 'gb' },
          { code: 'de', label: 'Deutsch', country_code: 'de' },
          { code: 'es', label: 'Español', country_code: 'es' },
          { code: 'it', label: 'Italiano', country_code: 'it' },
          { code: 'nl', label: 'Nederlands', country_code: 'nl' },
          { code: 'pt', label: 'Português', country_code: 'pt' },
          { code: 'pl', label: 'Polski', country_code: 'pl' },
        ],
        current: 'en',
      }),
  },
  s = {
    name: 'Minimal Setup (FR/EN)',
    render: () =>
      e({
        languages: [
          { code: 'fr', label: 'Français', country_code: 'fr' },
          { code: 'en', label: 'English', country_code: 'gb' },
        ],
        current: 'fr',
      }),
  },
  p = {
    name: 'Size Variants',
    render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); align-items: flex-start;">
      <div>
        <p style="margin-bottom: var(--size-2); font-size: var(--font-size--1); color: var(--gray-600);">Small</p>
        ${e({
          languages: [
            { code: 'fr', label: 'FR', country_code: 'fr' },
            { code: 'en', label: 'EN', country_code: 'gb' },
          ],
          current: 'fr',
          size: 'sm',
        })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-size: var(--font-size--1); color: var(--gray-600);">Medium (default)</p>
        ${e({
          languages: [
            { code: 'fr', label: 'Français', country_code: 'fr' },
            { code: 'en', label: 'English', country_code: 'gb' },
          ],
          current: 'fr',
          size: 'md',
        })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-size: var(--font-size--1); color: var(--gray-600);">Large</p>
        ${e({
          languages: [
            { code: 'fr', label: 'Français', country_code: 'fr' },
            { code: 'en', label: 'English', country_code: 'gb' },
          ],
          current: 'fr',
          size: 'lg',
        })}
      </div>
    </div>
  `,
  },
  o = {
    name: 'Multiple Instances',
    render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center; flex-wrap: wrap;">
      ${e({
        languages: [
          { code: 'fr', label: 'Français', country_code: 'fr' },
          { code: 'en', label: 'English', country_code: 'gb' },
        ],
        current: 'fr',
      })}
      ${e({
        languages: [
          { code: 'de', label: 'Deutsch', country_code: 'de' },
          { code: 'en', label: 'English', country_code: 'gb' },
        ],
        current: 'de',
      })}
      ${e({
        languages: [
          { code: 'es', label: 'Español', country_code: 'es' },
          { code: 'en', label: 'English', country_code: 'gb' },
        ],
        current: 'es',
      })}
    </div>
  `,
  };
var y, c, g;
a.parameters = {
  ...a.parameters,
  docs: {
    ...((y = a.parameters) == null ? void 0 : y.docs),
    source: {
      originalSource: `{
  name: 'Default (European Markets with Flags)',
  args: data
}`,
      ...((g = (c = a.parameters) == null ? void 0 : c.docs) == null ? void 0 : g.source),
    },
  },
};
var u, d, v;
r.parameters = {
  ...r.parameters,
  docs: {
    ...((u = r.parameters) == null ? void 0 : u.docs),
    source: {
      originalSource: `{
  name: 'All European Markets',
  render: () => markup({
    languages: [{
      code: 'fr',
      label: 'Français',
      country_code: 'fr'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }, {
      code: 'de',
      label: 'Deutsch',
      country_code: 'de'
    }, {
      code: 'es',
      label: 'Español',
      country_code: 'es'
    }, {
      code: 'it',
      label: 'Italiano',
      country_code: 'it'
    }, {
      code: 'nl',
      label: 'Nederlands',
      country_code: 'nl'
    }, {
      code: 'pt',
      label: 'Português',
      country_code: 'pt'
    }, {
      code: 'pl',
      label: 'Polski',
      country_code: 'pl'
    }],
    current: 'en'
  })
}`,
      ...((v = (d = r.parameters) == null ? void 0 : d.docs) == null ? void 0 : v.source),
    },
  },
};
var w, T, x;
s.parameters = {
  ...s.parameters,
  docs: {
    ...((w = s.parameters) == null ? void 0 : w.docs),
    source: {
      originalSource: `{
  name: 'Minimal Setup (FR/EN)',
  render: () => markup({
    languages: [{
      code: 'fr',
      label: 'Français',
      country_code: 'fr'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }],
    current: 'fr'
  })
}`,
      ...((x = (T = s.parameters) == null ? void 0 : T.docs) == null ? void 0 : x.source),
    },
  },
};
var m, h, b;
p.parameters = {
  ...p.parameters,
  docs: {
    ...((m = p.parameters) == null ? void 0 : m.docs),
    source: {
      originalSource: `{
  name: 'Size Variants',
  render: () => \`
    <div style="display: flex; flex-direction: column; gap: var(--size-4); align-items: flex-start;">
      <div>
        <p style="margin-bottom: var(--size-2); font-size: var(--font-size--1); color: var(--gray-600);">Small</p>
        \${markup({
    languages: [{
      code: 'fr',
      label: 'FR',
      country_code: 'fr'
    }, {
      code: 'en',
      label: 'EN',
      country_code: 'gb'
    }],
    current: 'fr',
    size: 'sm'
  })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-size: var(--font-size--1); color: var(--gray-600);">Medium (default)</p>
        \${markup({
    languages: [{
      code: 'fr',
      label: 'Français',
      country_code: 'fr'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }],
    current: 'fr',
    size: 'md'
  })}
      </div>
      <div>
        <p style="margin-bottom: var(--size-2); font-size: var(--font-size--1); color: var(--gray-600);">Large</p>
        \${markup({
    languages: [{
      code: 'fr',
      label: 'Français',
      country_code: 'fr'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }],
    current: 'fr',
    size: 'lg'
  })}
      </div>
    </div>
  \`
}`,
      ...((b = (h = p.parameters) == null ? void 0 : h.docs) == null ? void 0 : b.source),
    },
  },
};
var f, k, _;
o.parameters = {
  ...o.parameters,
  docs: {
    ...((f = o.parameters) == null ? void 0 : f.docs),
    source: {
      originalSource: `{
  name: 'Multiple Instances',
  render: () => \`
    <div style="display: flex; gap: var(--size-6); align-items: center; flex-wrap: wrap;">
      \${markup({
    languages: [{
      code: 'fr',
      label: 'Français',
      country_code: 'fr'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }],
    current: 'fr'
  })}
      \${markup({
    languages: [{
      code: 'de',
      label: 'Deutsch',
      country_code: 'de'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }],
    current: 'de'
  })}
      \${markup({
    languages: [{
      code: 'es',
      label: 'Español',
      country_code: 'es'
    }, {
      code: 'en',
      label: 'English',
      country_code: 'gb'
    }],
    current: 'es'
  })}
    </div>
  \`
}`,
      ...((_ = (k = o.parameters) == null ? void 0 : k.docs) == null ? void 0 : _.source),
    },
  },
};
const D = ['Default', 'AllMarkets', 'Minimal', 'SizeVariants', 'MultipleInstances'];
export {
  r as AllMarkets,
  a as Default,
  s as Minimal,
  o as MultipleInstances,
  p as SizeVariants,
  D as __namedExportsOrder,
  j as default,
};
