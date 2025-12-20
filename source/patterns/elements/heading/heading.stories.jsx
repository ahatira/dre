import component from './heading.twig';
import data from './heading.yml';
import './heading.css';

export default {
  title: 'Elements/Heading',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Semantic heading component (h1–h6) with **utility-first approach**. Use `.text-*` for colors, `.font-*` for weights, `.text-center` for alignment. Component provides base typography only.',
      },
    },
  },
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Heading text content',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Heading text' },
      },
    },
    // Structure
    level: {
      control: { type: 'select' },
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description:
        'Semantic heading level (h1: 48px, h2: 36px, h3: 28px, h4: 24px, h5: 20px, h6: 16px)',
      table: {
        category: 'Structure',
        type: { summary: 'h1 | h2 | h3 | h4 | h5 | h6' },
        defaultValue: { summary: 'h1' },
      },
    },
    // Accessibility
    visuallyHidden: {
      control: 'boolean',
      description: 'Hide visually but keep for screen readers (accessibility)',
      table: {
        category: 'Accessibility',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Attributes (for utility classes)
    attributes: {
      control: false,
      description:
        '**Utility-first styling**: Use `attributes.addClass()` for variations. Examples: `attributes.addClass("text-primary")` for color, `attributes.addClass("font-bold")` for weight, `attributes.addClass("text-center")` for alignment. See utilities/colors.css and utilities/typography.css.',
      table: {
        category: 'Attributes',
        type: { summary: 'Attribute' },
      },
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
  parameters: {
    docs: {
      description: {
        story:
          'Default H1 heading with base typography. Use utility classes for styling variations.',
      },
    },
  },
};

export const SemanticHierarchy = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      ${component({ text: 'Bureaux Haussmanniens Paris 8ème', level: 'h1' })}
      ${component({ text: 'Caractéristiques du bien', level: 'h2' })}
      ${component({ text: 'Surface et aménagement', level: 'h3' })}
      ${component({ text: 'Espaces de travail', level: 'h4' })}
      ${component({ text: 'Open space principal', level: 'h5' })}
      ${component({ text: 'Bureau individuel', level: 'h6' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Hiérarchie sémantique complète (h1-h6) avec contexte immobilier. Démontre l'utilisation correcte de la structure de titre pour une fiche de bien.",
      },
    },
  },
};

export const WithColor = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      <h3 class="text-primary">Bureaux à louer (.text-primary)</h3>
      <h3 class="text-success">Offre acceptée (.text-success)</h3>
      <h3 class="text-warning">Alerte documents (.text-warning)</h3>
      <h3 class="text-danger">Offre refusée (.text-danger)</h3>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Couleurs sémantiques via utilities `.text-*`. Utilisez `.text-primary` pour brand, `.text-success/.text-danger/.text-warning` pour statuts.',
      },
    },
  },
};

export const WithWeight = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-5);">
      <h2 class="font-light">Tour Horizon (.font-light)</h2>
      <h2>Bureaux premium (default bold)</h2>
      <h2 class="font-extrabold">Prestige (.font-extrabold)</h2>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Variations de poids via utilities `.font-*`. Default est bold (700), utilisez `.font-light` pour léger, `.font-extrabold` pour emphase.',
      },
    },
  },
};

export const WithAlignment = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); padding: var(--size-6); background: var(--gray-50);">
      <h2>Immobilier tertiaire (left default)</h2>
      <h2 class="text-center">Nos services (.text-center)</h2>
      <h2 class="text-right">Contact (.text-right)</h2>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Alignement via utilities `.text-center`, `.text-right`. Default est left-aligned.',
      },
    },
  },
};

export const VisuallyHidden = {
  render: () => `
    <div>
      <h2 class="visually-hidden">Navigation</h2>
      <nav style="padding: var(--size-4); background: var(--gray-100);">
        <ul style="list-style: none; padding: 0; display: flex; gap: var(--size-5); margin: 0;">
          <li><a href="#">Acheter</a></li>
          <li><a href="#">Louer</a></li>
          <li><a href="#">Vendre</a></li>
        </ul>
      </nav>
      <p style="margin-top: var(--size-4); color: var(--gray-600); font-size: var(--font-size-2);">
        ♿ Heading "Navigation" masqué visuellement mais accessible aux lecteurs d'écran
      </p>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Heading avec `.visually-hidden` pour structure sémantique accessible sans impact visuel. Conforme WCAG 2.2 AA.',
      },
    },
  },
};
