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
        component:
          'Typography component for paragraphs and text content. Variants: body (16px), small (14px), large (18px). Supports muted/strong states and alignment.',
      },
    },
  },
  argTypes: {
    text: {
      control: 'text',
      description: 'Text content',
      table: {
        category: 'content',
        type: { summary: 'string', required: true },
      },
    },
    variant: {
      control: { type: 'select' },
      options: ['body', 'small', 'large'],
      description: 'Text size variant',
      table: {
        category: 'appearance',
        defaultValue: { summary: 'body' },
      },
    },
    tag: {
      control: { type: 'select' },
      options: ['p', 'span', 'div'],
      description: 'HTML tag',
      table: {
        category: 'structure',
        defaultValue: { summary: 'p' },
      },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Text alignment',
      table: {
        category: 'appearance',
        defaultValue: { summary: 'left' },
      },
    },
    muted: {
      control: 'boolean',
      description: 'Muted color (secondary text)',
      table: {
        category: 'appearance',
        defaultValue: { summary: false },
      },
    },
    strong: {
      control: 'boolean',
      description: 'Bold weight (emphasis)',
      table: {
        category: 'appearance',
        defaultValue: { summary: false },
      },
    },
  },
};

// Variant sizes
export const Body = {
  args: {
    text: 'Body text (16px) - Default paragraph style for main content.',
    variant: 'body',
  },
};

export const Small = {
  args: {
    text: 'Small text (14px) - For captions, helper text, and secondary information.',
    variant: 'small',
  },
};

export const Large = {
  args: {
    text: 'Large text (18px) - For lead paragraphs and emphasized content.',
    variant: 'large',
  },
};

// State modifiers
export const Muted = {
  args: {
    text: 'Muted text with secondary color for less important information.',
    variant: 'body',
    muted: true,
  },
};

export const Strong = {
  args: {
    text: 'Strong text with bold weight for emphasis within content.',
    variant: 'body',
    strong: true,
  },
};

export const MutedSmall = {
  args: {
    text: 'Small muted text - Common for disclaimers and footnotes.',
    variant: 'small',
    muted: true,
  },
};

// Alignment variations
export const AlignmentVariants = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-6);">
      ${component({ text: 'Left aligned text (default)', variant: 'body', align: 'left' })}
      ${component({ text: 'Center aligned text', variant: 'body', align: 'center' })}
      ${component({ text: 'Right aligned text', variant: 'body', align: 'right' })}
    </div>
  `,
};

// Size comparison
export const SizeComparison = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Large text (18px) - Lead paragraphs', variant: 'large' })}
      ${component({ text: 'Body text (16px) - Standard paragraphs', variant: 'body' })}
      ${component({ text: 'Small text (14px) - Captions and helper text', variant: 'small' })}
    </div>
  `,
};

// Real-world examples
export const RealWorldExamples = {
  render: () => `
    <div style="max-width: 600px;">
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
      
      ${component({
        text: '* Informations non contractuelles. Prix indicatifs sous réserve de disponibilité.',
        variant: 'small',
        muted: true,
      })}
    </div>
  `,
};

// All combinations showcase
export const AllCombinations = {
  render: () => `
    <div style="display:grid; gap: var(--size-6);">
      <div>
        <h3 style="font-size: 14px; text-transform: uppercase; color: var(--gray-600); margin-bottom: var(--size-2);">Body Variants</h3>
        ${component({ text: 'Body text - normal', variant: 'body' })}
        ${component({ text: 'Body text - muted', variant: 'body', muted: true })}
        ${component({ text: 'Body text - strong', variant: 'body', strong: true })}
      </div>
      
      <div>
        <h3 style="font-size: 14px; text-transform: uppercase; color: var(--gray-600); margin-bottom: var(--size-2);">Small Variants</h3>
        ${component({ text: 'Small text - normal', variant: 'small' })}
        ${component({ text: 'Small text - muted', variant: 'small', muted: true })}
        ${component({ text: 'Small text - strong', variant: 'small', strong: true })}
      </div>
      
      <div>
        <h3 style="font-size: 14px; text-transform: uppercase; color: var(--gray-600); margin-bottom: var(--size-2);">Large Variants</h3>
        ${component({ text: 'Large text - normal', variant: 'large' })}
        ${component({ text: 'Large text - muted', variant: 'large', muted: true })}
        ${component({ text: 'Large text - strong', variant: 'large', strong: true })}
      </div>
    </div>
  `,
};
