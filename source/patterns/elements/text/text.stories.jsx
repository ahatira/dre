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
          'Composant typographique pour paragraphes et contenus textuels. 3 tailles (body 16px, small 14px, large 18px), états muted/strong, et alignement configurable.',
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

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

// === Grouped Showcases ===

export const AllSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${component({ text: 'Large text (18px) — Lead paragraphs et introductions', variant: 'large' })}
      ${component({ text: 'Body text (16px) — Paragraphes standards et contenu principal', variant: 'body' })}
      ${component({ text: 'Small text (14px) — Captions, helper text, et footnotes', variant: 'small' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 tailles disponibles : Large (18px) pour lead paragraphs, Body (16px) par défaut pour contenu principal, Small (14px) pour texte secondaire.',
      },
    },
  },
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Normal</p>
        ${component({ text: 'Texte standard avec couleur et poids par défaut', variant: 'body' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted</p>
        ${component({ text: 'Texte muted pour informations secondaires (couleur gris-600)', variant: 'body', muted: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Strong</p>
        ${component({ text: 'Texte strong pour emphase et mise en avant (font-weight-700)', variant: 'body', strong: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--gray-600);">Muted + Strong</p>
        ${component({ text: 'Combinaison muted et strong possible', variant: 'body', muted: true, strong: true })}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'États disponibles : Normal (défaut), Muted (couleur gris-600 pour texte secondaire), Strong (font-weight-700 pour emphase). Les états peuvent être combinés.',
      },
    },
  },
};

export const AllAlignments = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      ${component({ text: 'Left aligned text (default) — Alignement standard pour lecture', variant: 'body', align: 'left' })}
      ${component({ text: 'Center aligned text — Pour titres et contenus centrés', variant: 'body', align: 'center' })}
      ${component({ text: 'Right aligned text — Pour dates, montants, ou layouts spécifiques', variant: 'body', align: 'right' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 alignements disponibles : Left (défaut), Center, Right. Utilisez left pour lecture optimale, center pour titres/callouts, right pour données numériques.',
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
          "Exemple d'utilisation réelle : Lead paragraph (large + strong) pour accroche, body paragraphs pour contenu principal, small muted pour disclaimers et footnotes.",
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
          "Toutes les combinaisons possibles de tailles (large/body/small) et états (normal/muted/strong). Chaque taille peut avoir n'importe quel état.",
      },
    },
  },
};
