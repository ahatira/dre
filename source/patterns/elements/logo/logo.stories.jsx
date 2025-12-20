import logoTwig from './logo.twig';
import data from './logo.yml';

export default {
  title: 'Elements/Logo',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Brand logo for BNP Paribas Real Estate. Supports two variants (horizontal wordmark and square 3D icon) and three sizes.
Typically placed in header and footer with link to homepage.`,
      },
    },
  },
  argTypes: {
    // Appearance
    variant: {
      description:
        'Logo variant (default: horizontal wordmark, square: 3D icon for compact spaces)',
      control: { type: 'select' },
      options: ['default', 'square'],
      table: {
        category: 'Appearance',
        type: { summary: 'default | square' },
        defaultValue: { summary: 'default' },
      },
    },
    size: {
      description: 'Logo size (small: 24-32px, medium: 32-48px, large: 48-64px)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
      attributes: {
        control: false,
        description:
          'Additional HTML attributes and utility classes on the wrapper (<a> or <div>). Example: `attributes.addClass("block")`.',
        table: {
          category: 'Attributes',
          type: { summary: 'Attribute' },
        },
      },
    },
    // Link
    href: {
      description: 'Link URL (wraps logo in <a> tag, typically "/" for homepage)',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: '/' },
      },
    },
    // Accessibility
    alt: {
      description: 'Accessible alt text for screen readers',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'BNP Paribas Real Estate' },
      },
    },
  },
  args: data,
};

export const Default = {
  render: (args) => logoTwig(args),
  args: data,
};

export const Variants = {
  render: () => {
    const styles = {
      container:
        'display: flex; gap: 3rem; align-items: center; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);',
      logoBox:
        'margin-bottom: 1rem; padding: 1rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);',
      title: 'margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);',
      caption: 'margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);',
    };
    return `
      <div style="${styles.container}">
        <div style="text-align: center;">
          <div style="${styles.logoBox}">
            ${logoTwig({ variant: 'default', size: 'medium', href: null })}
          </div>
          <p style="${styles.title}">Default</p>
          <p style="${styles.caption}">Horizontal wordmark</p>
        </div>
        <div style="text-align: center;">
          <div style="${styles.logoBox}">
            ${logoTwig({ variant: 'square', size: 'medium', href: null })}
          </div>
          <p style="${styles.title}">Square</p>
          <p style="${styles.caption}">3D icon (compact)</p>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Two logo variants: Default (horizontal wordmark) for primary branding, Square (3D icon) for compact spaces like mobile headers or favicons.',
      },
    },
  },
};

export const Sizes = {
  render: () => {
    const styles = {
      section:
        'display: flex; flex-direction: column; gap: 2rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);',
      title: 'margin: 0 0 1rem; font-weight: 600; font-size: 14px; color: var(--gray-800);',
      row: 'display: flex; gap: 2rem; align-items: flex-end; padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);',
      logo: 'margin-bottom: 0.75rem;',
      label: 'margin: 0; font-size: 12px; color: var(--gray-700);',
    };
    return `
      <div style="${styles.section}">
        <div>
          <p style="${styles.title}">Default Variant</p>
          <div style="${styles.row}">
            <div style="text-align: center;">
              <div style="${styles.logo}">${logoTwig({ variant: 'default', size: 'small', href: null })}</div>
              <p style="${styles.label}">Small (24px)</p>
            </div>
            <div style="text-align: center;">
              <div style="${styles.logo}">${logoTwig({ variant: 'default', size: 'medium', href: null })}</div>
              <p style="${styles.label}">Medium (32px)</p>
            </div>
            <div style="text-align: center;">
              <div style="${styles.logo}">${logoTwig({ variant: 'default', size: 'large', href: null })}</div>
              <p style="${styles.label}">Large (48px)</p>
            </div>
          </div>
        </div>
        <div>
          <p style="${styles.title}">Square Variant</p>
          <div style="${styles.row}">
            <div style="text-align: center;">
              <div style="${styles.logo}">${logoTwig({ variant: 'square', size: 'small', href: null })}</div>
              <p style="${styles.label}">Small (32px)</p>
            </div>
            <div style="text-align: center;">
              <div style="${styles.logo}">${logoTwig({ variant: 'square', size: 'medium', href: null })}</div>
              <p style="${styles.label}">Medium (48px)</p>
            </div>
            <div style="text-align: center;">
              <div style="${styles.logo}">${logoTwig({ variant: 'square', size: 'large', href: null })}</div>
              <p style="${styles.label}">Large (64px)</p>
            </div>
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '3 sizes available: Small (24-32px) for mobile headers, Medium (32-48px) default for desktop headers, Large (48-64px) for hero sections or footers.',
      },
    },
  },
};

export const HeaderUsage = {
  render: () => {
    const styles = {
      container: 'padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);',
      title: 'margin: 0 0 1.5rem; font-weight: 600; font-size: 15px; color: var(--gray-800);',
      desktop:
        'padding: 1rem 2rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between;',
      mobile:
        'padding: 0.75rem 1rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: space-between; max-width: 375px;',
      nav: 'display: flex; gap: 1.5rem;',
      navLink: 'color: var(--gray-700); text-decoration: none; font-size: 14px;',
      menuButton: 'padding: 0.5rem; background: none; border: none; cursor: pointer;',
    };
    return `
      <div style="${styles.container}">
        <p style="${styles.title}">Header Logo (Desktop)</p>
        <div style="${styles.desktop}">
          ${logoTwig({ variant: 'default', size: 'medium', href: '/' })}
          <div style="${styles.nav}">
            <a href="#" style="${styles.navLink}">Acheter</a>
            <a href="#" style="${styles.navLink}">Louer</a>
            <a href="#" style="${styles.navLink}">Estimer</a>
            <a href="#" style="${styles.navLink}">Contact</a>
          </div>
        </div>
        <p style="${styles.title}">Header Logo (Mobile)</p>
        <div style="${styles.mobile}">
          ${logoTwig({ variant: 'square', size: 'small', href: '/' })}
          <button style="${styles.menuButton}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <line x1="3" y1="12" x2="21" y2="12"></line>
              <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Real-world header usage: Desktop uses default variant (medium size), mobile uses square variant (small size) for space efficiency. Logo always links to homepage.',
      },
    },
  },
};

export const FooterUsage = {
  render: () => {
    const styles = {
      outer: 'padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);',
      footer:
        'padding: 3rem 2rem; background: var(--gray-900); border-radius: var(--radius-2); color: white;',
      grid: 'display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 2rem; max-width: 1200px; margin: 0 auto;',
      subtitle: 'margin-top: 1rem; font-size: 13px; color: var(--gray-400); line-height: 1.5;',
      heading: 'margin: 0 0 1rem; font-size: 14px; font-weight: 600;',
      list: 'list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.5rem;',
      link: 'color: var(--gray-400); text-decoration: none; font-size: 13px;',
    };
    return `
      <div style="${styles.outer}">
        <div style="${styles.footer}">
          <div style="${styles.grid}">
            <div>
              ${logoTwig({ variant: 'default', size: 'medium', href: '/', alt: "BNP Paribas Real Estate - Retour à l'accueil" })}
              <p style="${styles.subtitle}">Votre partenaire immobilier de confiance depuis 1873.</p>
            </div>
            <div>
              <h4 style="${styles.heading}">Acheter</h4>
              <ul style="${styles.list}">
                <li><a href="#" style="${styles.link}">Appartements</a></li>
                <li><a href="#" style="${styles.link}">Maisons</a></li>
                <li><a href="#" style="${styles.link}">Terrains</a></li>
              </ul>
            </div>
            <div>
              <h4 style="${styles.heading}">Louer</h4>
              <ul style="${styles.list}">
                <li><a href="#" style="${styles.link}">Appartements</a></li>
                <li><a href="#" style="${styles.link}">Maisons</a></li>
                <li><a href="#" style="${styles.link}">Bureaux</a></li>
              </ul>
            </div>
            <div>
              <h4 style="${styles.heading}">Informations</h4>
              <ul style="${styles.list}">
                <li><a href="#" style="${styles.link}">À propos</a></li>
                <li><a href="#" style="${styles.link}">Contact</a></li>
                <li><a href="#" style="${styles.link}">Mentions légales</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Footer usage example with default variant (medium size) on dark background. Logo links to homepage with descriptive alt text.',
      },
    },
  },
};
