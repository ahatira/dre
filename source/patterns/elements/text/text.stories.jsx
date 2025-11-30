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
    variant: {
      control: { type: 'select' },
      options: ['body', 'small', 'large'],
      description: 'Size variant: body (16px), small (14px), large (18px).',
      table: {
        category: 'Appearance',
        type: { summary: 'body | small | large' },
        defaultValue: { summary: 'body' },
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
      ${component({ text: 'Large text (18px) — Lead paragraphs and introductions', variant: 'large' })}
      ${component({ text: 'Body text (16px) — Standard paragraphs and primary content', variant: 'body' })}
      ${component({ text: 'Small text (14px) — Captions, helper text, and footnotes', variant: 'small' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 size variants: Large (18px) for lead paragraphs, Body (16px) default for main content, Small (14px) for secondary/supporting text.',
      },
    },
  },
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Normal</p>
        ${component({ text: 'Standard text with default color and weight', variant: 'body' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted</p>
        ${component({ text: 'Muted text for secondary information (reduced prominence)', variant: 'body', muted: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Strong</p>
        ${component({ text: 'Strong text for emphasis and highlighted importance (bold weight)', variant: 'body', strong: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted + Strong</p>
        ${component({ text: 'Combination of muted and strong is possible', variant: 'body', muted: true, strong: true })}
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
      ${component({ text: 'Left aligned text (default) — Optimal for continuous reading', variant: 'body', align: 'left' })}
      ${component({ text: 'Center aligned text — Use for callouts and headings', variant: 'body', align: 'center' })}
      ${component({ text: 'Right aligned text — Use for numeric values or metadata blocks', variant: 'body', align: 'right' })}
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
        variant: 'large',
        strong: true,
      })}
      
      ${component({
        text: 'BNP Paribas Real Estate vous accompagne dans tous vos projets immobiliers professionnels. Fort de notre expertise et de notre réseau international, nous vous proposons des solutions adaptées à vos besoins.',
        variant: 'body',
      })}
      
      ${component({
        text: "Notre équipe d'experts analyse le marché en temps réel pour vous offrir les meilleures opportunités d'investissement.",
        variant: 'body',
      })}
      
      <div style="margin-top: var(--size-6); padding-top: var(--size-4); border-top: 1px solid var(--gray-200);">
        ${component({
          text: '* Informations non contractuelles. Prix indicatifs sous réserve de disponibilité.',
          variant: 'small',
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
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Large Variants (18px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${component({ text: 'Large text — Normal', variant: 'large' })}
          ${component({ text: 'Large text — Muted', variant: 'large', muted: true })}
          ${component({ text: 'Large text — Strong', variant: 'large', strong: true })}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Body Variants (16px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${component({ text: 'Body text — Normal', variant: 'body' })}
          ${component({ text: 'Body text — Muted', variant: 'body', muted: true })}
          ${component({ text: 'Body text — Strong', variant: 'body', strong: true })}
        </div>
      </div>
      
      <div>
        <h3 style="font-size: 13px; font-weight: 600; text-transform: uppercase; color: var(--gray-700); margin: 0 0 var(--size-3); letter-spacing: 0.5px;">Small Variants (14px)</h3>
        <div style="background: white; padding: var(--size-4); border-radius: var(--radius-2); display: flex; flex-direction: column; gap: var(--size-2);">
          ${component({ text: 'Small text — Normal', variant: 'small' })}
          ${component({ text: 'Small text — Muted', variant: 'small', muted: true })}
          ${component({ text: 'Small text — Strong', variant: 'small', strong: true })}
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
