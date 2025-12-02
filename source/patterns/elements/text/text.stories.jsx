import component from './text.twig';
import data from './text.yml';
import './text.css';

export default {
  title: 'Elements/Text',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Semantic text component for paragraphs and inline content.
Supports size variants, emphasis (muted/strong), alignment, and semantic tags.`,
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
        defaultValue: { summary: 'Body text example' },
      },
    },
    size: {
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      description: 'Text size: xs (12px), sm (14px), md (16px), lg (18px), xl (20px), xxl (24px).',
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'md' },
      },
    },
    color: {
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'dark', 'light'],
      description: 'Semantic color: default (text), primary, secondary, success, info, warning, danger, dark, light.',
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | success | info | warning | danger | dark | light' },
        defaultValue: { summary: 'default' },
      },
    },
    tag: {
      control: { type: 'select' },
      options: ['p', 'span', 'div'],
      description: 'Semantic HTML tag used for rendering.',
      table: {
        category: 'Structure',
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
      description: 'Apply secondary tone (muted) for de‑emphasized information.',
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    strong: {
      control: { type: 'boolean' },
      description: 'Apply bold weight for emphasis (can combine with muted).',
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

// === Grouped Showcases ===

export const AllSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${component({ text: 'XXL text (24px) — Hero intro and featured statements', size: 'xxl', strong: true })}
      ${component({ text: 'XL text (20px) — Lead paragraphs and introductions', size: 'xl' })}
      ${component({ text: 'LG text (18px) — Lead paragraphs', size: 'lg' })}
      ${component({ text: 'MD text (16px) — Standard body content (default)', size: 'md' })}
      ${component({ text: 'SM text (14px) — Captions, helper text', size: 'sm' })}
      ${component({ text: 'XS text (12px) — Footnotes, microcopy', size: 'xs', muted: true })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '6 sizes: XXL (24px), XL (20px), LG (18px), MD (16px default), SM (14px), XS (12px). Use larger sizes for introductions and smaller sizes for captions/microcopy.',
      },
    },
  },
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Normal</p>
        ${component({ text: 'Standard text with default color and weight', size: 'md' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted</p>
        ${component({ text: 'Muted text for secondary information (reduced prominence)', size: 'md', muted: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Strong</p>
        ${component({ text: 'Strong text for emphasis and highlighted importance (bold weight)', size: 'md', strong: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted + Strong</p>
        ${component({ text: 'Combination of muted and strong is possible', size: 'md', muted: true, strong: true })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'States: Normal (default), Muted (secondary tone), Strong (bold emphasis). States can be combined for nuanced emphasis.',
      },
    },
  },
};

export const AllAlignments = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${component({ text: 'Left aligned text (default) — Optimal for continuous reading', size: 'md', align: 'left' })}
      ${component({ text: 'Center aligned text — Use for callouts and headings', size: 'md', align: 'center' })}
      ${component({ text: 'Right aligned text — Use for numeric values or metadata blocks', size: 'md', align: 'right' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 alignments: Left (default, best readability), Center (headings/callouts), Right (numeric / metadata alignment).',
      },
    },
  },
};

export const UseCases = {
  render: () => `
    <div style="max-width: 650px; padding: 2rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
      ${component({
        text: "Découvrez notre sélection exclusive de biens immobiliers d'exception à Paris et en Île-de-France.",
        size: 'lg',
        strong: true,
      })}
      
      ${component({
        text: 'BNP Paribas Real Estate vous accompagne dans tous vos projets immobiliers professionnels. Fort de notre expertise et de notre réseau international, nous vous proposons des solutions adaptées à vos besoins.',
        size: 'md',
      })}
      
      ${component({
        text: "Notre équipe d'experts analyse le marché en temps réel pour vous offrir les meilleures opportunités d'investissement.",
        size: 'md',
      })}
      
      <div style="margin-top: var(--size-6); padding-top: var(--size-4); border-top: 1px solid var(--gray-200);">
        ${component({
          text: '* Informations non contractuelles. Prix indicatifs sous réserve de disponibilité.',
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
          'Real usage example: lead paragraph (large + strong) for introduction, body paragraphs for primary content, small muted for disclaimers / footnotes.',
      },
    },
  },
};

export const AllCombinations = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Large Variants (18–24px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${component({ text: 'LG text — Normal', size: 'lg' })}
          ${component({ text: 'XL text — Muted', size: 'xl', muted: true })}
          ${component({ text: 'XXL text — Strong', size: 'xxl', strong: true })}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Body Variants (16px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${component({ text: 'Body text — Normal', size: 'md' })}
          ${component({ text: 'Body text — Muted', size: 'md', muted: true })}
          ${component({ text: 'Body text — Strong', size: 'md', strong: true })}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Small Variants (12–14px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${component({ text: 'Small text — Normal', size: 'sm' })}
          ${component({ text: 'Small text — Muted', size: 'sm', muted: true })}
          ${component({ text: 'Small text — Strong', size: 'sm', strong: true })}
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'All possible combinations of size (large/body/small) and state (normal/muted/strong). Any size can pair with any emphasis state.',
      },
    },
  },
};
