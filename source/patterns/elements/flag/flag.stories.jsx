import flagTwig from './flag.twig';
import data from './flag.yml';
import flags from './flags-full.json';

const settings = {
  title: 'Elements/Flag',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Visual indicator for country/language as a flag. Supports ISO 3166-1 alpha-2 country codes or BCP 47 locale tags. Always provide a label for accessibility when not purely decorative. Sizes: xs (12px), sm (16px), md (20px), lg (24px), xl (48px). Shapes: square, rounded, circle.',
      },
    },
  },
  argTypes: {
    code: {
      description: 'Country code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL)',
      control: { type: 'select' },
      options: ['FR', 'GB', 'DE', 'ES', 'IT', 'NL', 'IE', 'PL'],
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'FR' },
      },
    },
    locale: {
      description: 'BCP 47 locale tag (ex: fr-FR, en-GB). If provided, derives country code.',
      control: { type: 'select' },
      options: ['fr-FR', 'en-GB', 'de-DE', 'es-ES', 'it-IT', 'nl-NL'],
      table: {
        type: { summary: 'string' },
      },
    },
    label: {
      description: 'Accessible label (ex: "France", "United Kingdom")',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
      },
    },
    src: {
      description: 'Explicit flag image path (optional)',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
      },
    },
    size: {
      description: 'Flag size',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      table: {
        type: { summary: 'xs | sm | md | lg | xl' },
        defaultValue: { summary: 'md' },
      },
    },
    shape: {
      description: 'Flag shape',
      control: { type: 'select' },
      options: ['square', 'rounded', 'circle'],
      table: {
        type: { summary: 'square | rounded | circle' },
        defaultValue: { summary: 'square' },
      },
    },
    disabled: {
      description: 'Disabled state (grayed out)',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    decorative: {
      description: 'Decorative only (hides from screen readers)',
      control: { type: 'boolean' },
      table: {
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

export const Sizes = {
  render: () => {
    const wrapStyle = 'display:flex;gap:var(--size-8);align-items:flex-end;flex-wrap:wrap';
    const itemStyle = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-2)';
    const labelStyle =
      'font-size:var(--size-3);color:var(--gray-600);font-weight:var(--font-weight-500)';

    return `
      <div style="${wrapStyle}">
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', size: 'xs' })}
          <span style="${labelStyle}">xs (12px)</span>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', size: 'sm' })}
          <span style="${labelStyle}">sm (16px)</span>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', size: 'md' })}
          <span style="${labelStyle}">md (20px)</span>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', size: 'lg' })}
          <span style="${labelStyle}">lg (24px)</span>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', size: 'xl' })}
          <span style="${labelStyle}">xl (48px)</span>
        </div>
      </div>
    `;
  },
};

export const Shapes = {
  render: () => {
    const wrapStyle = 'display:flex;gap:var(--size-8);align-items:flex-end;flex-wrap:wrap';
    const itemStyle = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-3)';
    const titleStyle =
      'font-size:var(--size-305);color:var(--gray-800);font-weight:var(--font-weight-600);margin-bottom:var(--size-1)';
    const descStyle = 'font-size:var(--size-3);color:var(--gray-600)';

    return `
      <div style="${wrapStyle}">
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', shape: 'square', size: 'xl' })}
          <div style="text-align:center">
            <div style="${titleStyle}">Square</div>
            <div style="${descStyle}">Default (no radius)</div>
          </div>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', shape: 'rounded', size: 'xl' })}
          <div style="text-align:center">
            <div style="${titleStyle}">Rounded</div>
            <div style="${descStyle}">4px radius</div>
          </div>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', shape: 'circle', size: 'xl' })}
          <div style="text-align:center">
            <div style="${titleStyle}">Circle</div>
            <div style="${descStyle}">Perfect circle (1:1)</div>
          </div>
        </div>
      </div>
    `;
  },
};

export const DisabledState = {
  render: () => {
    const wrapStyle = 'display:flex;gap:var(--size-10);align-items:flex-end;flex-wrap:wrap';
    const itemStyle = 'display:flex;flex-direction:column;align-items:center;gap:var(--size-3)';
    const titleStyle =
      'font-size:var(--size-305);color:var(--gray-800);font-weight:var(--font-weight-600);margin-bottom:var(--size-1)';
    const descStyle = 'font-size:var(--size-3);color:var(--gray-600)';

    return `
      <div style="${wrapStyle}">
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', disabled: false, size: 'xl' })}
          <div style="text-align:center">
            <div style="${titleStyle}">Normal</div>
            <div style="${descStyle}">Full color</div>
          </div>
        </div>
        <div style="${itemStyle}">
          ${flagTwig({ code: 'FR', label: 'France', disabled: true, size: 'xl' })}
          <div style="text-align:center">
            <div style="${titleStyle}">Disabled</div>
            <div style="${descStyle}">Reduced opacity</div>
          </div>
        </div>
      </div>
    `;
  },
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
