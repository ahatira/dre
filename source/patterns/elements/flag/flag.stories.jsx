import flagTwig from './flag.twig';
import data from './flag.yml';

export default {
  title: 'Elements/Flag',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Country/language visual indicator using flag SVG images. Supports ISO 3166-1 alpha-2 codes (FR, GB, DE) or BCP 47 locale tags (fr-FR, en-GB). Ideal for language selectors and international content.',
      },
    },
  },
  argTypes: {
    // Content
    code: {
      description: 'Country code ISO 3166-1 alpha-2 (ex: FR, GB, DE, ES, IT, NL)',
      control: { type: 'select' },
      options: ['FR', 'GB', 'DE', 'ES', 'IT', 'NL', 'IE', 'PL', 'BE', 'PT'],
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
    // Appearance
    size: {
      description: 'Flag size (small: 16px, medium: 20px, large: 24px)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
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
      description: 'Disabled state (reduced opacity and grayscale filter)',
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
    attributes: {
      description:
        'Additional HTML attributes for Drupal integration (ARIA, data-*, extra classes).',
      control: { type: 'object' },
      table: {
        category: 'Accessibility',
        type: { summary: 'object' },
      },
    },
  },
  args: data,
};

export const Default = {
  render: (args) => flagTwig(args),
  args: data,
};

export const Sizes = {
  render: () => `
    <div style="display: flex; gap: 2rem; align-items: flex-end; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'small' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">Small</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">16px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'medium' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">Medium</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">20px · Default</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'large' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">Large</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">24px</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 sizes available: Small (16px) for inline text, Medium (20px) default for UI elements, Large (24px) for prominent displays.',
      },
    },
  },
};

export const Shapes = {
  render: () => `
    <div style="display: flex; gap: 2rem; align-items: center; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'large', shape: 'square' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Square</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">4:3 ratio</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'large', shape: 'rounded' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Rounded</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">4:3 + 4px radius</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'large', shape: 'circle' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Circle</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">1:1 ratio</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          '3 shape variants: Square (default 4:3 standard flag ratio), Rounded (4:3 with soft corners), Circle (1:1 for avatars or icons).',
      },
    },
  },
};

export const EuropeanCountries = {
  render: () => `
    <div style="padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <p style="margin: 0 0 1.5rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">European Union Countries (selection)</p>
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 1.5rem;">
        ${[
          ['FR', 'France'],
          ['DE', 'Germany'],
          ['IT', 'Italy'],
          ['ES', 'Spain'],
          ['NL', 'Netherlands'],
          ['BE', 'Belgium'],
          ['PL', 'Poland'],
          ['PT', 'Portugal'],
          ['IE', 'Ireland'],
          ['AT', 'Austria'],
          ['SE', 'Sweden'],
          ['DK', 'Denmark'],
        ]
          .map(
            ([code, name]) => `
          <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
            ${flagTwig({ code, label: name, size: 'large' })}
            <span style="font-size: 12px; color: var(--gray-700); text-align: center;">${name}</span>
          </div>
        `
          )
          .join('')}
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Example of European countries flags. Useful for language selectors, international property search, or regional filters.',
      },
    },
  },
};

export const LanguageSelector = {
  render: () => `
    <div style="padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <p style="margin: 0 0 1.5rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Language Selector Example</p>
      <div style="display: flex; gap: 0.75rem; padding: 1rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); width: fit-content;">
        <button style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: white; cursor: pointer; font-size: 14px; color: var(--gray-700);">
          ${flagTwig({ code: 'FR', label: 'Français', size: 'small' })}
          Français
        </button>
        <button style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: white; cursor: pointer; font-size: 14px; color: var(--gray-700);">
          ${flagTwig({ code: 'GB', label: 'English', size: 'small' })}
          English
        </button>
        <button style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: white; cursor: pointer; font-size: 14px; color: var(--gray-700);">
          ${flagTwig({ code: 'DE', label: 'Deutsch', size: 'small' })}
          Deutsch
        </button>
        <button style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border: 1px solid var(--gray-300); border-radius: var(--radius-2); background: white; cursor: pointer; font-size: 14px; color: var(--gray-700);">
          ${flagTwig({ code: 'ES', label: 'Español', size: 'small' })}
          Español
        </button>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Real-world language selector implementation using Small size flags with text labels. Ideal for header navigation.',
      },
    },
  },
};

export const DisabledState = {
  render: () => `
    <div style="display: flex; gap: 2rem; align-items: center; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France', size: 'large', disabled: false })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Normal</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${flagTwig({ code: 'FR', label: 'France (disabled)', size: 'large', disabled: true })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Disabled</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Opacity + grayscale</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Disabled state reduces opacity and applies grayscale filter. Used for unavailable languages or regions.',
      },
    },
  },
};
