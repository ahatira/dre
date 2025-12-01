import flags from '../../documentation/flags-list.json';
import flagTwig from './flag.twig';
import data from './flag.yml';

const settings = {
  title: 'Elements/Flag',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Visual indicator for country/language using flag images. Supports ISO 3166-1 alpha-2 codes (FR, GB, DE) or BCP 47 locale tags (fr-FR, en-GB).',
      },
    },
  },
  argTypes: {
    // Content
    code: {
      description: 'Country code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL)',
      control: { type: 'select' },
      options: ['FR', 'GB', 'DE', 'ES', 'IT', 'NL', 'IE', 'PL'],
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'FR' },
      },
    },
    locale: {
      description: 'BCP 47 locale tag (ex: fr-FR, en-GB). If provided, derives country code.',
      control: { type: 'select' },
      options: ['fr-FR', 'en-GB', 'de-DE', 'es-ES', 'it-IT', 'nl-NL'],
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    src: {
      description: 'Explicit flag image path (overrides automatic /flags/{code}.svg path)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Appearance
    size: {
      description: 'Flag size (xs: 12px, sm: 16px, md: 20px, lg: 24px, xl: 48px)',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl' },
        defaultValue: { summary: 'md' },
      },
    },
    shape: {
      description:
        'Flag shape (square: 4:3 ratio, rounded: 4:3 with 4px radius, circle: 1:1 ratio)',
      control: { type: 'select' },
      options: ['square', 'rounded', 'circle'],
      table: {
        category: 'Appearance',
        type: { summary: 'square | rounded | circle' },
        defaultValue: { summary: 'square' },
      },
    },
    // Behavior
    disabled: {
      description: 'Disabled state (reduced opacity and grayscale)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    label: {
      description: 'Accessible label for screen readers (ex: "France", "United Kingdom")',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    decorative: {
      description:
        'Marks flag as decorative only (adds aria-hidden, removes from accessibility tree)',
      control: { type: 'boolean' },
      table: {
        category: 'Accessibility',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => flagTwig(args),
  args: { ...data },
};

export const AllCountries = {
  render: () => {
    const sectionStyle = 'margin-bottom:var(--size-8)';
    const h3Style =
      'margin:0 0 var(--size-4) 0;font-size:var(--size-5);font-weight:var(--font-weight-600);color:var(--gray-800)';
    const gridStyle =
      'display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:var(--size-4)';
    const itemStyle = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-2)';
    const labelStyle = 'font-size:var(--size-3);color:var(--gray-700)';

    const countries = {
      Europe: [
        ['AT', 'Austria'],
        ['BE', 'Belgium'],
        ['BG', 'Bulgaria'],
        ['HR', 'Croatia'],
        ['CY', 'Cyprus'],
        ['CZ', 'Czech Republic'],
        ['DK', 'Denmark'],
        ['EE', 'Estonia'],
        ['FI', 'Finland'],
        ['FR', 'France'],
        ['DE', 'Germany'],
        ['GR', 'Greece'],
        ['HU', 'Hungary'],
        ['IE', 'Ireland'],
        ['IT', 'Italy'],
        ['LV', 'Latvia'],
        ['LT', 'Lithuania'],
        ['LU', 'Luxembourg'],
        ['MT', 'Malta'],
        ['NL', 'Netherlands'],
        ['NO', 'Norway'],
        ['PL', 'Poland'],
        ['PT', 'Portugal'],
        ['RO', 'Romania'],
        ['SK', 'Slovakia'],
        ['SI', 'Slovenia'],
        ['ES', 'Spain'],
        ['SE', 'Sweden'],
        ['CH', 'Switzerland'],
        ['GB', 'United Kingdom'],
      ],
      Americas: [
        ['AR', 'Argentina'],
        ['BR', 'Brazil'],
        ['CA', 'Canada'],
        ['CL', 'Chile'],
        ['CO', 'Colombia'],
        ['MX', 'Mexico'],
        ['PE', 'Peru'],
        ['US', 'United States'],
      ],
      Asia: [
        ['CN', 'China'],
        ['IN', 'India'],
        ['ID', 'Indonesia'],
        ['IL', 'Israel'],
        ['JP', 'Japan'],
        ['MY', 'Malaysia'],
        ['PH', 'Philippines'],
        ['SG', 'Singapore'],
        ['KR', 'South Korea'],
        ['TH', 'Thailand'],
        ['TR', 'Turkey'],
        ['AE', 'United Arab Emirates'],
        ['VN', 'Vietnam'],
      ],
      'Africa & Middle East': [
        ['EG', 'Egypt'],
        ['KE', 'Kenya'],
        ['MA', 'Morocco'],
        ['NG', 'Nigeria'],
        ['SA', 'Saudi Arabia'],
        ['ZA', 'South Africa'],
      ],
      Oceania: [
        ['AU', 'Australia'],
        ['NZ', 'New Zealand'],
      ],
    };

    const sections = Object.entries(countries)
      .map(
        ([continent, list]) => `
      <div style="${sectionStyle}">
        <h3 style="${h3Style}">${continent}</h3>
        <div style="${gridStyle}">
          ${list
            .map(
              ([code, name]) => `
            <div style="${itemStyle}">
              ${flagTwig({ code, label: name, size: 'lg' })}
              <span style="${labelStyle}">${name}</span>
            </div>
          `
            )
            .join('')}
        </div>
      </div>
    `
      )
      .join('');

    return `<div>${sections}</div>`;
  },
};

export const AllCountriesFull = {
  name: 'AllCountries (Full)',
  render: () => {
    const wrap = 'display:flex;flex-direction:column;gap:var(--size-6)';
    const toolbar = 'display:flex;gap:var(--size-3);align-items:center;flex-wrap:wrap';
    const grid =
      'display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:var(--size-4)';
    const item = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-2)';
    const label = 'font-size:var(--size-3);color:var(--gray-700)';

    const cards = (list) =>
      list
        .map(
          (code) => `
      <div style="${item}">
        ${flagTwig({ code, label: code, size: 'lg' })}
        <span style="${label}">${code}</span>
      </div>
    `
        )
        .join('');

    // Build static markup with total count
    const all = flags.filter((c) => c.length === 2 || c.includes('-'));
    return `
      <div style="${wrap}">
        <div style="${toolbar}"><strong>Total:</strong> <span>${all.length}</span></div>
        <div style="${grid}">
          ${cards(all)}
        </div>
      </div>
    `;
  },
};

export default settings;
