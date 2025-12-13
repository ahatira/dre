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
          'Semantic text element for paragraphs and inline content with **utility-first approach**. Use `.text-*` for colors, `.font-*` for weights. Component provides base `<p>/<span>/<div>` styling only.',
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
    attributes: {
      control: false,
      description:
        '**Utility-first styling**: Use `attributes.addClass()` for variations. Examples: `attributes.addClass("text-primary")` for color, `attributes.addClass("font-semibold")` for weight, `attributes.addClass("text-center")` for alignment. See utilities/colors.css and utilities/typography.css.',
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
          'Default paragraph with base typography. Use utility classes for styling variations.',
      },
    },
  },
};

export const WithColor = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      <p class="text-primary">Bureaux à louer (.text-primary)</p>
      <p class="text-gray-600">Informations complémentaires (.text-gray-600)</p>
      <p class="text-success">Visite disponible (.text-success)</p>
      <p class="text-warning">Places limitées (.text-warning)</p>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Couleurs via utilities `.text-*`. Utilisez `.text-primary` pour brand, `.text-gray-600` pour texte secondaire, `.text-success/.text-warning` pour statuts.',
      },
    },
  },
};

export const WithWeight = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-3);">
      <p>Text normal (default weight 400)</p>
      <p class="font-semibold">Text important (.font-semibold)</p>
      <p class="font-bold">Text emphase forte (.font-bold)</p>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Variations de poids via utilities `.font-*`. Default est regular (400), utilisez `.font-semibold` pour emphase modérée, `.font-bold` pour emphase forte.',
      },
    },
  },
};

export const WithAlignment = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4); padding: var(--size-6); background: var(--gray-50);">
      <p>Texte aligné à gauche (default)</p>
      <p class="text-center">Texte centré (.text-center)</p>
      <p class="text-right">Prix: 2 500 € / mois (.text-right)</p>
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

export const InlineText = {
  render: () => `
    <div style="padding: var(--size-6); background: var(--gray-50);">
      <p>
        Bureaux de standing situés au cœur de 
        <span class="text-primary font-semibold">La Défense</span>, 
        disponibles à partir de 
        <span class="text-gray-900 font-bold">450 m²</span>. 
        <span class="text-gray-600">Certification HQE Excellent.</span>
      </p>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Texte inline avec `<span>` et utilities pour emphase sélective. Combinaisons: `.text-primary .font-semibold`, `.font-bold` pour chiffres, `.text-gray-600` pour info secondaire.',
      },
    },
  },
};
