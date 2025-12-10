import component from './text.twig';
import data from './text.yml';
import './text.css';

const semanticColors = [
  'default',
  'primary',
  'secondary',
  'gold',
  'info',
  'warning',
  'success',
  'danger',
  'dark',
  'light',
];

export default {
  title: 'Elements/Text',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic text atom for paragraphs and inline content with six sizes, semantic colors (including gold), alignment, and emphasis states. Uses component-scoped CSS variables (layered) for easy overrides.',
      },
    },
  },
  argTypes: {
    text: {
      control: { type: 'text' },
      description: 'Text content rendered directly (no HTML parsing).',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: data.text },
      },
    },
    size: {
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      description: 'Size variants from compact helper text (xs) to hero copy (xxl).',
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'md' },
      },
    },
    color: {
      control: { type: 'select' },
      options: semanticColors,
      description:
        'Semantic color: default text plus brand/status options (primary, secondary, gold, info, warning, success, danger, dark, light).',
      table: {
        category: 'Appearance',
        type: { summary: semanticColors.join(' | ') },
        defaultValue: { summary: 'default' },
      },
    },
    tag: {
      control: { type: 'select' },
      options: ['p', 'span', 'div'],
      description: 'HTML tag used for rendering (paragraph by default).',
      table: {
        category: 'Content',
        type: { summary: 'p | span | div' },
        defaultValue: { summary: 'p' },
      },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Horizontal text alignment.',
      table: {
        category: 'Layout',
        type: { summary: 'left | center | right' },
        defaultValue: { summary: 'left' },
      },
    },
    muted: {
      control: { type: 'boolean' },
      description: 'Secondary tone using `--text-secondary` (can combine with strong).',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    strong: {
      control: { type: 'boolean' },
      description: 'Bold emphasis via `--font-weight-700` (can combine with muted).',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

export const Sizes = {
  render: () => {
    const sizes = ['xxl', 'xl', 'lg', 'md', 'sm', 'xs'];

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-3); padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
        ${sizes
          .map((size) =>
            component({
              ...data,
              size,
              text:
                size === 'xxl'
                  ? 'XXL — Hero intro for premium property highlights'
                  : size === 'xl'
                    ? 'XL — Lead paragraph for brochure openings'
                    : size === 'lg'
                      ? 'LG — Lead paragraph for landing pages'
                      : size === 'md'
                        ? 'MD — Standard body text (default)'
                        : size === 'sm'
                          ? 'SM — Captions, helper text, legal mentions'
                          : 'XS — Microcopy and footnotes',
              strong: size === 'xxl',
              muted: size === 'xs',
            })
          )
          .join('\n')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Six sizes mapped to the typography scale. Larger sizes support hero/lead copy; smaller sizes support helper text and microcopy.',
      },
    },
  },
};

export const Colors = {
  render: () => {
    const items = semanticColors.map((color) => {
      if (color === 'light') {
        return `
          <div style="padding: var(--size-4); background: var(--gray-800); border-radius: var(--radius-2);">
            ${component({ ...data, text: 'Light — Use on dark backgrounds', color })}
          </div>
        `;
      }

      return component({
        ...data,
        color,
        text: `${color.charAt(0).toUpperCase()}${color.slice(1)} semantic color`,
      });
    });

    return `
      <div style="display: grid; gap: var(--size-3); padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
        ${items.join('\n')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Semantic colors aligned with design tokens, including the gold accent. The light variant is demonstrated on a dark tile to preserve contrast.',
      },
    },
  },
};

export const Emphasis = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3); padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      ${component({ ...data, text: 'Normal tone — default weight and color' })}
      ${component({ ...data, text: 'Muted tone — secondary information', muted: true })}
      ${component({ ...data, text: 'Strong emphasis — bold highlight', strong: true })}
      ${component({ ...data, text: 'Muted + strong — secondary yet highlighted', muted: true, strong: true })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Combine muted and strong to balance hierarchy for helper text, disclaimers, and emphasis.',
      },
    },
  },
};

export const Alignments = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3); padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      ${component({ ...data, text: 'Left aligned — best readability for paragraphs', align: 'left' })}
      ${component({ ...data, text: 'Center aligned — promotional callouts and short copy', align: 'center' })}
      ${component({ ...data, text: 'Right aligned — metadata, numbers, or prices', align: 'right' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Alignment options adapt the text to layouts such as cards, callouts, and metadata blocks.',
      },
    },
  },
};

export const RealEstateUseCase = {
  render: () => `
    <div style="max-width: 720px; padding: var(--size-6); background: var(--white); border-radius: var(--radius-2); border: 1px solid var(--border-default); display: grid; gap: var(--size-3);">
      ${component({
        text: 'Programme neuf – bureaux modulables à La Défense, livrables au T3',
        size: 'lg',
        strong: true,
      })}
      ${component({
        text: 'Plateaux lumineux de 450 à 900 m² avec terrasses végétalisées, à deux minutes du RER et de la future ligne 15. Certification HQE Excellent et espaces collaboratifs modulables.',
        size: 'md',
      })}
      ${component({
        text: 'Accompagnement complet : recherche de financement, space-planning, et pilotage des travaux d’aménagement.',
        size: 'md',
      })}
      <div style="padding-top: var(--size-3); border-top: 1px solid var(--border-default);">
        ${component({
          text: '* Informations non contractuelles. Disponibilités et loyers indicatifs, sous réserve de signature du bail.',
          size: 'sm',
          muted: true,
        })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Real-estate narrative mixing lead paragraph, body copy, and a muted disclaimer to showcase sizing and emphasis in context.',
      },
    },
  },
};
