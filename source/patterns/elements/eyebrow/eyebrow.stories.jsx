import eyebrowTwig from './eyebrow.twig';
import data from './eyebrow.yml';

const settings = {
  title: 'Elements/Eyebrow',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          "Eyebrow: label/kicker contextuel placé au-dessus d'un titre. Supporte variants (primary, secondary, accent, neutral), tailles (small, medium), styles (uppercase, bold), et décorations (ligne, point, icône). BEM strict avec tokens uniquement.",
      },
    },
  },
  argTypes: {
    text: {
      description: 'Texte affiché',
      control: { type: 'text' },
      table: { type: { summary: 'string' }, defaultValue: { summary: '' } },
    },
    variant: {
      description: 'Couleur du texte',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'accent', 'neutral'],
      table: {
        type: { summary: 'primary | secondary | accent | neutral' },
        defaultValue: { summary: 'neutral' },
      },
    },
    size: {
      description: 'Taille du texte',
      control: { type: 'select' },
      options: ['small', 'medium'],
      table: { type: { summary: 'small | medium' }, defaultValue: { summary: 'medium' } },
    },
    uppercase: {
      description: 'Texte en majuscules',
      control: { type: 'boolean' },
      table: { type: { summary: 'boolean' }, defaultValue: { summary: true } },
    },
    bold: {
      description: 'Texte en gras',
      control: { type: 'boolean' },
      table: { type: { summary: 'boolean' }, defaultValue: { summary: false } },
    },
    withLine: {
      description: 'Ajouter une ligne décorative horizontale',
      control: { type: 'boolean' },
      table: { type: { summary: 'boolean' }, defaultValue: { summary: false } },
    },
    withDot: {
      description: 'Ajouter un point décoratif',
      control: { type: 'boolean' },
      table: { type: { summary: 'boolean' }, defaultValue: { summary: false } },
    },
    icon: {
      description: "Nom de l'icône optionnelle",
      control: { type: 'text' },
      table: { type: { summary: 'string' }, defaultValue: { summary: '' } },
    },
  },
};

export const Default = {
  render: (args) => eyebrowTwig(args),
  args: { ...data },
};

export const Primary = {
  render: () => eyebrowTwig({ text: 'Nouveauté', variant: 'primary', uppercase: true }),
};

export const Secondary = {
  render: () => eyebrowTwig({ text: 'Article', variant: 'secondary', uppercase: true }),
};

export const Accent = {
  render: () => eyebrowTwig({ text: 'Étude de cas', variant: 'accent', bold: true }),
};

export const Neutral = {
  render: () => eyebrowTwig({ text: 'Information', variant: 'neutral' }),
};

export const WithLine = {
  render: () =>
    eyebrowTwig({ text: 'Actualités', variant: 'neutral', withLine: true, size: 'small' }),
};

export const WithDot = {
  render: () => eyebrowTwig({ text: 'Blog', variant: 'secondary', withDot: true }),
};

export const SmallSize = {
  render: () =>
    eyebrowTwig({ text: 'PETIT TEXTE', variant: 'primary', size: 'small', uppercase: true }),
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 24px; padding: 20px;">
      <div>
        <h4 style="margin-bottom: 12px; font-size: 14px; color: #666;">Variants de couleur</h4>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          ${eyebrowTwig({ text: 'Nouveauté', variant: 'primary' })}
          ${eyebrowTwig({ text: 'Article', variant: 'secondary' })}
          ${eyebrowTwig({ text: 'Étude de cas', variant: 'accent' })}
          ${eyebrowTwig({ text: 'Information', variant: 'neutral' })}
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 12px; font-size: 14px; color: #666;">Tailles</h4>
        <div style="display: flex; gap: 12px; align-items: center;">
          ${eyebrowTwig({ text: 'PETIT', variant: 'primary', size: 'small' })}
          ${eyebrowTwig({ text: 'MOYEN', variant: 'primary', size: 'medium' })}
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 12px; font-size: 14px; color: #666;">Styles</h4>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          ${eyebrowTwig({ text: 'Majuscules', variant: 'primary', uppercase: true })}
          ${eyebrowTwig({ text: 'Minuscules', variant: 'primary', uppercase: false })}
          ${eyebrowTwig({ text: 'Gras', variant: 'accent', bold: true })}
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 12px; font-size: 14px; color: #666;">Décorations</h4>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          ${eyebrowTwig({ text: 'Avec ligne', variant: 'neutral', withLine: true })}
          ${eyebrowTwig({ text: 'Avec point', variant: 'secondary', withDot: true })}
        </div>
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 32px; padding: 20px;">
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Hero de page</h4>
        ${eyebrowTwig({ text: 'Nouveauté', variant: 'primary', uppercase: true })}
        <h2 style="margin-top: 8px; font-size: 32px;">Grand titre principal</h2>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Card d'actualité</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 400px;">
          ${eyebrowTwig({ text: 'Actualités', variant: 'neutral', withLine: true, size: 'small' })}
          <h3 style="margin-top: 12px; font-size: 20px;">Titre de l'article</h3>
          <p style="margin-top: 8px; color: #666;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Section Blog</h4>
        ${eyebrowTwig({ text: 'Blog', variant: 'secondary', withDot: true })}
        <h2 style="margin-top: 8px; font-size: 28px;">Derniers articles</h2>
      </div>
    </div>
  `,
};

export default settings;
