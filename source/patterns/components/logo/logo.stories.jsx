import logoTwig from './logo.twig';
import data from './logo.yml';

export default {
  title: 'Components/Logo',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Logo de marque BNP Paribas Real Estate avec support pour 3 variantes : desktop (logo seul), desktop avec slogan, et mobile (version compacte). Le logo peut être cliquable (lien vers la page d'accueil) ou statique.`,
      },
    },
  },
  argTypes: {
    // Appearance
    variant: {
      description:
        'Variante du logo (desktop: logo seul, desktop-slogan: logo + slogan, mobile: version compacte carrée)',
      control: { type: 'select' },
      options: ['desktop', 'desktop-slogan', 'mobile'],
      table: {
        category: 'Appearance',
        type: { summary: 'desktop | desktop-slogan | mobile' },
        defaultValue: { summary: 'desktop' },
      },
    },
    // Link
    href: {
      description:
        "URL du lien (encapsule le logo dans un <a>, typiquement '/' pour la page d'accueil)",
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: '/' },
      },
    },
    // Content
    slogan: {
      description: 'Texte du slogan (uniquement pour la variante desktop-slogan)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Real Estate for a Changing World' },
      },
    },
    // Accessibility
    alt: {
      description: "Texte alternatif pour les lecteurs d'écran",
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: 'BNP Paribas Real Estate' },
      },
    },
    // Attributes
    attributes: {
      control: false,
      description:
        'Attributs HTML additionnels et classes utilitaires sur l\'élément racine. Exemple : `attributes.addClass("block")`.',
      table: {
        category: 'Attributes',
        type: { summary: 'Attribute' },
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
        'display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);',
      logoBox:
        'padding: 2rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); display: flex; align-items: center; justify-content: center; min-height: 120px;',
      title:
        'margin: 1rem 0 0.25rem; font-weight: 600; font-size: 14px; color: var(--gray-700); text-align: center;',
      caption: 'margin: 0; font-size: 12px; color: var(--gray-500); text-align: center;',
    };
    return `
      <div style="${styles.container}">
        <div>
          <div style="${styles.logoBox}">
            ${logoTwig({ variant: 'desktop', href: null })}
          </div>
          <p style="${styles.title}">Desktop</p>
          <p style="${styles.caption}">Logo seul</p>
        </div>
        <div>
          <div style="${styles.logoBox}">
            ${logoTwig({ variant: 'desktop-slogan', href: null })}
          </div>
          <p style="${styles.title}">Desktop (Avec Slogan)</p>
          <p style="${styles.caption}">Logo + "Real Estate for a Changing World"</p>
        </div>
        <div>
          <div style="${styles.logoBox}">
            ${logoTwig({ variant: 'mobile', href: null })}
          </div>
          <p style="${styles.title}">Mobile</p>
          <p style="${styles.caption}">Version compacte carrée (48x48px)</p>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Les 3 variantes disponibles : Desktop (logo seul), Desktop avec slogan, et Mobile (version compacte)',
      },
    },
  },
};

export const WithLink = {
  render: () => {
    return `
      <div style="display: flex; gap: 2rem; flex-wrap: wrap; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
        <div>
          ${logoTwig({ variant: 'desktop', href: '/' })}
          <p style="margin: 0.5rem 0 0; font-size: 12px; color: var(--gray-500);">Logo cliquable (href="/")</p>
        </div>
        <div>
          ${logoTwig({ variant: 'desktop-slogan', href: '/' })}
          <p style="margin: 0.5rem 0 0; font-size: 12px; color: var(--gray-500);">Logo + slogan cliquable</p>
        </div>
        <div>
          ${logoTwig({ variant: 'mobile', href: '/' })}
          <p style="margin: 0.5rem 0 0; font-size: 12px; color: var(--gray-500);">Version mobile cliquable</p>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          "Logos avec lien cliquable vers la page d'accueil. Au survol, l'opacité diminue légèrement.",
      },
    },
  },
};

export const Standalone = {
  render: () => {
    return `
      <div style="display: flex; gap: 2rem; flex-wrap: wrap; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
        <div>
          ${logoTwig({ variant: 'desktop', href: null })}
          <p style="margin: 0.5rem 0 0; font-size: 12px; color: var(--gray-500);">Logo statique (pas de lien)</p>
        </div>
        <div>
          ${logoTwig({ variant: 'desktop-slogan', href: null })}
          <p style="margin: 0.5rem 0 0; font-size: 12px; color: var(--gray-500);">Logo + slogan statique</p>
        </div>
        <div>
          ${logoTwig({ variant: 'mobile', href: null })}
          <p style="margin: 0.5rem 0 0; font-size: 12px; color: var(--gray-500);">Version mobile statique</p>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Logos sans lien (href null ou vide). Utilisés dans des contextes non-cliquables comme les footers informatifs.',
      },
    },
  },
};

export const Responsive = {
  render: () => {
    return `
      <div style="padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
        <h3 style="margin: 0 0 1rem; font-size: 14px; font-weight: 600; color: var(--gray-700);">Exemple d'usage responsive</h3>
        <div style="display: flex; gap: 2rem; align-items: center; padding: 1.5rem; background: white; border-radius: var(--radius-2);">
          <div class="hide-on-mobile">
            ${logoTwig({ variant: 'desktop-slogan', href: '/' })}
          </div>
          <div class="show-on-mobile-only" style="display: none;">
            ${logoTwig({ variant: 'mobile', href: '/' })}
          </div>
        </div>
        <p style="margin: 1rem 0 0; font-size: 12px; color: var(--gray-500);">Sur desktop : logo avec slogan | Sur mobile : logo carré compact</p>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          "Exemple d'utilisation responsive : afficher desktop-slogan sur grand écran et mobile sur petit écran en utilisant des classes CSS.",
      },
    },
  },
};
