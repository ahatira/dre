import iconRegistry from '../../documentation/icons-registry.json';
import iconTwig from './icon.twig';
import data from './icon.yml';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  args: { ...data },
  parameters: {
    docs: {
      description: {
        component:
          'Sprite-based icon atom that inherits typography and color from its context.\n\n' +
          '- **API**: `name` (required), optional `ariaLabel`, and `attributes` for composition.\n' +
          '- **Sizing**: defaults to 20px (`--ps-icon-size-md`); override via `--ps-icon-size` or contextual font-size.\n' +
          '- **Color**: follows `currentColor`; semantic modifier classes remain available for manual use.\n' +
          '- **Integration**: use `attributes.addClass()` to attach component-level selectors (badge, button, etc.).',
      },
    },
  },
  argTypes: {
    name: {
      description:
        'Icon name without "icon-" prefix. Backed by sprite generated from source/icons-source/*.svg.',
      control: { type: 'select' },
      options: iconRegistry.names,
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'search' },
      },
    },
    ariaLabel: {
      description: 'Accessibility label (informative icons)',
      control: { type: 'text' },
      table: { category: 'Accessibility', type: { summary: 'string' } },
    },
    attributes: {
      description:
        'Drupal attributes object for composition (e.g., `create_attribute().addClass(...)`).',
      control: { type: 'object' },
      table: { category: 'Structure', type: { summary: 'Drupal\Attribute' } },
    },
  },
};

export const Default = {
  render: (args) => iconTwig(args),
  args: { ...data },
  parameters: {
    docs: {
      description: {
        story:
          'Default icon with medium size. Use the controls to test different icons from the sprite.',
      },
    },
  },
};

export const HowToUse = {
  render: () => `
    <div style="display: grid; gap: var(--size-8); padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
      <section style="display: grid; gap: var(--size-4);">
        <h3 style="margin: 0; font-size: var(--font-size-4); color: var(--gray-900);">3 Ways to Use Icons</h3>
        
        <div style="display: grid; gap: var(--size-4);">
          <div style="padding: var(--size-4); background: var(--white); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
            <h4 style="margin: 0 0 var(--size-2) 0; color: var(--primary); font-size: var(--font-size-2);">1. Twig Component (Recommended)</h4>
            <code style="display: block; padding: var(--size-3); background: var(--gray-100); border-radius: var(--radius-1); font-family: monospace; font-size: var(--font-size-0); overflow-x: auto;">{% include '@elements/icon/icon.twig' with { name: 'search' } only %}</code>
            <p style="margin: var(--size-2) 0 0 0; font-size: var(--font-size-0); color: var(--gray-600);">Full component with accessibility support and composition via attributes.</p>
          </div>

          <div style="padding: var(--size-4); background: var(--white); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
            <h4 style="margin: 0 0 var(--size-2) 0; color: var(--secondary); font-size: var(--font-size-2);">2. Data Attribute (CSS-driven)</h4>
            <code style="display: block; padding: var(--size-3); background: var(--gray-100); border-radius: var(--radius-1); font-family: monospace; font-size: var(--font-size-0); overflow-x: auto;">&lt;span data-icon="search" aria-hidden="true"&gt;&lt;/span&gt;</code>
            <p style="margin: var(--size-2) 0 0 0; font-size: var(--font-size-0); color: var(--gray-600);">Lightweight approach using CSS background-image (see source/props/icons-generated.css).</p>
          </div>

          <div style="padding: var(--size-4); background: var(--white); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
            <h4 style="margin: 0 0 var(--size-2) 0; color: var(--info); font-size: var(--font-size-2);">3. SVG Use (Direct sprite)</h4>
            <code style="display: block; padding: var(--size-3); background: var(--gray-100); border-radius: var(--radius-1); font-family: monospace; font-size: var(--font-size-0); overflow-x: auto;">&lt;svg class="ps-icon__svg"&gt;&lt;use href="/icons/icons-sprite.svg#icon-search"&gt;&lt;/use&gt;&lt;/svg&gt;</code>
            <p style="margin: var(--size-2) 0 0 0; font-size: var(--font-size-0); color: var(--gray-600);">Direct SVG reference for custom implementations.</p>
          </div>
        </div>
      </section>

      <section style="display: grid; gap: var(--size-4);">
        <h3 style="margin: 0; font-size: var(--font-size-4); color: var(--gray-900);">Adding New Icons</h3>
        <div style="padding: var(--size-4); background: var(--white); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
          <ol style="margin: 0; padding-left: var(--size-5); font-size: var(--font-size-1); color: var(--gray-700); display: grid; gap: var(--size-2);">
            <li>Place SVG in appropriate category folder: <code style="background: var(--gray-100); padding: 2px 6px; border-radius: var(--radius-1);">source/icons-source/{category}/icon-name.svg</code></li>
            <li>Run: <code style="background: var(--gray-100); padding: 2px 6px; border-radius: var(--radius-1);">npm run icons:build</code></li>
            <li>Icon automatically added to sprite, CSS, and registry</li>
            <li>Use immediately: <code style="background: var(--gray-100); padding: 2px 6px; border-radius: var(--radius-1);">{% include '@elements/icon/icon.twig' with { name: 'icon-name' } %}</code></li>
          </ol>
          <p style="margin: var(--size-3) 0 0 0; font-size: var(--font-size-0); color: var(--gray-600);"><strong>Categories:</strong> ad, website, generic, search, metropole, social-media, mobile-only, tutoffice, univers, tools, blog, other, country</p>
        </div>
      </section>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Complete guide for using icons in templates, sizing, color inheritance, and adding new icons to the sprite.',
      },
    },
  },
};

export const Gallery = {
  render: () => {
    const categoryPalette = {
      ad: 'var(--primary)',
      website: 'var(--secondary)',
      generic: 'var(--text-secondary)',
      search: 'var(--info)',
      metropole: 'var(--gold)',
      'social-media': 'var(--secondary)',
      'mobile-only': 'var(--success)',
      tutoffice: 'var(--danger)',
      univers: 'var(--warning)',
      tools: 'var(--info)',
      blog: 'var(--primary)',
      other: 'var(--text-secondary)',
      country: 'var(--success)',
    };

    const orderedCategories = (iconRegistry.order || Object.keys(iconRegistry.categories)).map(
      (slug) => {
        const entry = iconRegistry.categories[slug] || { label: slug, icons: [] };
        return {
          slug,
          label: entry.label || slug,
          icons: entry.icons || [],
        };
      }
    );

    const populatedCategories = orderedCategories.filter((category) => category.icons.length > 0);

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-6); padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-2);">
        <header style="display: grid; gap: var(--size-2);">
          <h2 style="margin: 0; font-size: var(--font-size-6); color: var(--gray-900);">All ${iconRegistry.total} icons by category</h2>
          <p style="margin: 0; color: var(--gray-600); font-size: var(--font-size-1);">Dataset generated from source/patterns/documentation/icons-registry.json during the icon build step.</p>
          <nav aria-label="Icon categories" style="display: flex; flex-wrap: wrap; gap: var(--size-2);">
            ${populatedCategories
              .map(({ slug, label }) => {
                const accent = categoryPalette[slug] || 'var(--text-secondary)';
                return `
                  <a href="#icon-category-${slug}"
                     style="display: inline-flex; align-items: center; gap: var(--size-1); padding: var(--size-1) var(--size-3); border: 1px solid var(--border-default); border-radius: var(--radius-2); color: var(--gray-700); text-decoration: none; background: var(--white);">
                    <span style="inline-size: var(--size-4); block-size: var(--size-4); border-radius: var(--radius-round); background: color-mix(in srgb, ${accent} 25%, transparent);"></span>
                    <span style="font-size: var(--font-size--1);">${label}</span>
                  </a>`;
              })
              .join('')}
          </nav>
        </header>

        ${populatedCategories
          .map(({ slug, label, icons }) => {
            const accent = categoryPalette[slug] || 'var(--text-secondary)';

            return `
                <section id="icon-category-${slug}" style="display: flex; flex-direction: column; gap: var(--size-4);">
                  <div style="display: flex; align-items: baseline; justify-content: space-between; gap: var(--size-3);">
                    <h3 style="margin: 0; font-size: var(--font-size-4); color: ${accent};">${label}</h3>
                    <span style="font-size: var(--font-size--1); color: var(--gray-600);">${icons.length} icons</span>
                  </div>
                  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: var(--size-4);">
                    ${icons
                      .map(
                        (iconName) => `
                          <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-2); padding: var(--size-3); border: 1px solid var(--border-default); border-radius: var(--radius-2); background: var(--white); color: ${accent}; --ps-icon-size: var(--size-8);">
                            <div style="display: flex; align-items: center; justify-content: center; inline-size: var(--size-12); block-size: var(--size-12);">
                              ${iconTwig({ name: iconName })}
                            </div>
                            <span style="font-size: var(--font-size--1); color: var(--gray-700); text-align: center; line-height: 1.2;">${iconName}</span>
                          </div>
                        `
                      )
                      .join('')}
                  </div>
                </section>
              `;
          })
          .join('')}

        ${
          iconRegistry.unmapped && iconRegistry.unmapped.length
            ? `<p style="margin: 0; font-size: var(--font-size--1); color: var(--danger);">Unmapped icons detected in registry: ${iconRegistry.unmapped.join(', ')}</p>`
            : ''
        }
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Full taxonomy-aligned gallery mirroring the design mockup (Ad, Website, Generic, Search, Metropole, Social media, Mobile only, TutOffice, Univers, Tools, Blog, Other, Country).',
      },
    },
  },
};
