import eyebrowTwig from './eyebrow.twig';
import data from './eyebrow.yml';

const settings = {
  title: 'Elements/Eyebrow',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Short contextual label placed above headings to provide category or context. Supports semantic colors (primary, secondary, accent, neutral, muted), sizes, uppercase/bold, and optional decorative elements (line, dot, icon) using design tokens.',
      },
    },
  },
  argTypes: {
    text: {
      description: 'Text content displayed in the eyebrow.',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
    variant: {
      description:
        'Semantic color variant: primary (green), secondary (magenta), accent (gold premium), neutral (gray), muted (disabled gray subtle).',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'accent', 'neutral', 'muted'],
      table: {
        category: 'Appearance',
        type: { summary: 'primary | secondary | accent | neutral | muted' },
        defaultValue: { summary: 'neutral' },
      },
    },
    size: {
      description: 'Text size: small (12px) or medium (14px, default).',
      control: { type: 'select' },
      options: ['small', 'medium'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium' },
        defaultValue: { summary: 'medium' },
      },
    },
    uppercase: {
      description: 'Transform text to uppercase.',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: true },
      },
    },
    bold: {
      description: 'Apply bold font weight.',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    withLine: {
      description: 'Add decorative horizontal line before text.',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    withDot: {
      description: 'Add decorative dot before text.',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    icon: {
      description:
        'Optional icon name (without "icon-" prefix). Examples: check, award, info, arrow-right, heart, help.',
      control: { type: 'select' },
      options: ['', 'check', 'award', 'info', 'arrow-right', 'heart', 'help'],
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '""' },
      },
    },
  },
};

export const Default = {
  render: (args) => eyebrowTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Primary (Green - Brand Action)</small>
        ${eyebrowTwig({ text: 'Actualité marché', variant: 'primary' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Secondary (Magenta - Secondary Action)</small>
        ${eyebrowTwig({ text: 'Article blog', variant: 'secondary' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Accent (Gold - Premium/Featured)</small>
        ${eyebrowTwig({ text: 'Bien phare', variant: 'accent' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Neutral (Gray - Default, Context Label)</small>
        ${eyebrowTwig({ text: 'Immobilier commercial', variant: 'neutral' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Muted (Subtle Gray - Metadata like Date)</small>
        ${eyebrowTwig({ text: 'Publié le 9 décembre', variant: 'muted', size: 'small' })}
      </div>
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Small (12px)</small>
        ${eyebrowTwig({ text: 'ACTUALITÉ MARCHÉ', variant: 'primary', size: 'small' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Medium (14px, default)</small>
        ${eyebrowTwig({ text: 'ACTUALITÉ MARCHÉ', variant: 'primary', size: 'medium' })}
      </div>
    </div>
  `,
};

export const TextStyles = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Uppercase (default)</small>
        ${eyebrowTwig({ text: 'Actualité marché', variant: 'primary', uppercase: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Lowercase</small>
        ${eyebrowTwig({ text: 'Actualité marché', variant: 'primary', uppercase: false })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Bold</small>
        ${eyebrowTwig({ text: 'Bien phare', variant: 'accent', bold: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">Bold + Accent</small>
        ${eyebrowTwig({ text: 'Bien phare', variant: 'accent', bold: true, size: 'medium' })}
      </div>
    </div>
  `,
};

export const WithDecorations = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">With horizontal line (divider effect)</small>
        ${eyebrowTwig({ text: 'Nos services', variant: 'neutral', withLine: true, size: 'small' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">With decorative dot</small>
        ${eyebrowTwig({ text: 'Blog immobilier', variant: 'secondary', withDot: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">With icon (award - featured)</small>
        ${eyebrowTwig({ text: 'Sélection', variant: 'accent', icon: 'award', bold: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem; font-weight: 600;">With icon (check - success)</small>
        ${eyebrowTwig({ text: 'Confirmé', variant: 'primary', icon: 'check' })}
      </div>
    </div>
  `,
};

export const RealEstateUseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2.5rem; padding: 20px;">
      
      <!-- Hero Section -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Hero Section - Page d'accueil</h4>
        <div style="background: #f5f5f5; padding: 30px; border-radius: 8px;">
          ${eyebrowTwig({ text: 'Investisseurs', variant: 'primary', uppercase: true })}
          <h1 style="margin-top: 12px; margin-bottom: 0; font-size: 36px; line-height: 1.2;">Portefeuille immobilier premium</h1>
        </div>
      </div>

      <!-- Featured Property Card -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Featured Property Card - Bien phare</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 420px; background: white;">
          <div style="margin-bottom: 12px;">
            ${eyebrowTwig({ text: 'Bien phare', variant: 'accent', bold: true, icon: 'award' })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600;">Tour Premium - La Défense</h3>
          <p style="margin: 8px 0; color: #666; font-size: 14px;">3 500 m² - Bureau de prestige avec vue panoramique</p>
          <div style="margin-top: 8px;">
            <small style="color: #999;">Cité de la Défense • Surface: 3 500 m²</small>
          </div>
        </div>
      </div>

      <!-- Market News Card -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Market News Card - Actualité marché</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 420px; background: white;">
          <div style="margin-bottom: 8px;">
            ${eyebrowTwig({ text: 'Publié 5 décembre', variant: 'muted', size: 'small' })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Tendances du marché immobilier Q4 2025</h3>
          <p style="margin: 8px 0 12px 0; color: #666; font-size: 14px; line-height: 1.5;">Analyse approfondie des évolutions du secteur tertiaire en Île-de-France.</p>
          <div style="margin-top: 12px;">
            ${eyebrowTwig({ text: 'Actualité marché', variant: 'primary', size: 'small' })}
          </div>
        </div>
      </div>

      <!-- Blog Article Card -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Blog Article Card - Contenu expert</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 420px; background: white;">
          <div style="margin-bottom: 8px;">
            ${eyebrowTwig({ text: 'Article blog', variant: 'secondary', withDot: true })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Guide: Financer votre projet immobilier</h3>
          <p style="margin: 8px 0; color: #666; font-size: 14px;">Par Anne Dupont • 8 min de lecture</p>
        </div>
      </div>

      <!-- Report/Study Card -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Research Report - Étude de marché</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 420px; background: white;">
          <div style="margin-bottom: 12px;">
            ${eyebrowTwig({ text: 'Étude', variant: 'neutral', withLine: true, size: 'small' })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Perspectives 2026: Immobilier Tertiaire Europe</h3>
          <p style="margin: 8px 0; color: #666; font-size: 14px;">Rapport complet • 45 pages PDF</p>
        </div>
      </div>

      <!-- Section Header with Eyebrow -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Section Header - Grouper contenu</h4>
        <div style="padding: 30px; background: #f9f9f9; border-radius: 8px;">
          ${eyebrowTwig({ text: 'Notre expertise', variant: 'primary' })}
          <h2 style="margin-top: 12px; margin-bottom: 16px; font-size: 28px; line-height: 1.2;">Services immobiliers</h2>
          <p style="color: #666; margin: 0;">Financement, location, vente, conseil - des solutions adaptées à vos besoins.</p>
        </div>
      </div>

      <!-- Category/Tag usage -->
      <div>
        <h4 style="margin-bottom: 12px; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Category Classifier - Filtres/Tags</h4>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          ${eyebrowTwig({ text: 'Bureau', variant: 'neutral', size: 'small' })}
          ${eyebrowTwig({ text: 'Retail', variant: 'neutral', size: 'small' })}
          ${eyebrowTwig({ text: 'Logistique', variant: 'neutral', size: 'small' })}
          ${eyebrowTwig({ text: 'Résidentiel', variant: 'neutral', size: 'small' })}
        </div>
      </div>

    </div>
  `,
};

export default settings;
