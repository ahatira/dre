import spinnerTwig from './spinner.twig';
import data from './spinner.yml';

export default {
  title: 'Elements/Spinner',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Indicateur de chargement animé pour états asynchrones. Disponible en 3 variantes (circular, dots, bars), 5 tailles, et 4 couleurs. Inclut role="status" et aria-live pour accessibilité.',
      },
    },
  },
  argTypes: {
    variant: {
      control: 'select',
      options: ['circular', 'dots', 'bars'],
      description: 'Type de spinner',
      table: {
        type: { summary: 'circular | dots | bars' },
        defaultValue: { summary: 'circular' },
      },
    },
    size: {
      control: 'select',
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      description: 'Taille du spinner',
      table: {
        type: { summary: 'xs | sm | md | lg | xl' },
        defaultValue: { summary: 'md' },
      },
    },
    color: {
      control: 'select',
      options: ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger', 'white'],
      description: 'Couleur du spinner',
      table: {
        type: { summary: 'default | primary | secondary | success | info | warning | danger | white' },
        defaultValue: { summary: 'default' },
      },
    },
    text: {
      control: 'text',
      description: 'Texte pour lecteurs d\'écran (annoncé aux utilisateurs)',
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'Chargement en cours...' },
      },
    },
    centered: {
      control: 'boolean',
      description: 'Centrer le spinner dans son conteneur parent',
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => spinnerTwig(args),
  args: { ...data },
};

// === Grouped Showcases ===

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: 3rem; align-items: center; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 1rem;">${spinnerTwig({ variant: 'circular', size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 14px; color: var(--gray-700);">Circular</p>
        <p style="margin: 0.25rem 0 0; font-size: 12px; color: var(--gray-500);">SVG rotatif</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 1rem;">${spinnerTwig({ variant: 'dots', size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 14px; color: var(--gray-700);">Dots</p>
        <p style="margin: 0.25rem 0 0; font-size: 12px; color: var(--gray-500);">3 points bouncing</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 1rem;">${spinnerTwig({ variant: 'bars', size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 14px; color: var(--gray-700);">Bars</p>
        <p style="margin: 0.25rem 0 0; font-size: 12px; color: var(--gray-500);">3 barres stretch</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Les trois variantes de spinner disponibles. Circular est recommandé par défaut, Dots pour un effet plus subtil, et Bars pour une alternative visuelle.',
      },
    },
  },
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: 3rem; align-items: flex-end; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'xs' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">XS</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">16px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'sm' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">SM</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">24px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'md' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">MD</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">32px · Défaut</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'lg' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">LG</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">48px</p>
      </div>
      <div style="text-align: center;">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ size: 'xl' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 12px; color: var(--gray-700);">XL</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">64px</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: '5 tailles disponibles : XS (16px) pour boutons, SM (24px) pour inline, MD (32px) par défaut, LG (48px) pour zones centrées, XL (64px) pour chargements pleine page.',
      },
    },
  },
};

export const AllColors = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'default' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Default</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Gris neutre</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'primary' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Primary</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Vert BNP</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'secondary' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Secondary</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Rose accent</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'success' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Success</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Vert succès</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'info' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Info</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Bleu info</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'warning' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Warning</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Jaune alert</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: white; border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'danger' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: var(--gray-700);">Danger</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-500);">Rouge erreur</p>
      </div>
      <div style="text-align: center; padding: 1rem; background: var(--gray-800); border-radius: var(--radius-2); box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="margin-bottom: 0.75rem;">${spinnerTwig({ color: 'white' })}</div>
        <p style="margin: 0; font-weight: 600; font-size: 13px; color: white;">White</p>
        <p style="margin: 0.25rem 0 0; font-size: 11px; color: var(--gray-400);">Sur fond sombre</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Toutes les couleurs sémantiques disponibles : Default (gris neutre), Primary (vert BNP), Secondary (rose accent), Success/Info/Warning/Danger pour états contextuels, et White pour fonds sombres.',
      },
    },
  },
};

export const Centered = {
  render: () => `
    <div style="position: relative; height: 200px; border: 2px dashed var(--gray-300); border-radius: var(--radius-2); background: var(--gray-50);">
      <p style="margin: 1rem; font-size: 14px; color: var(--gray-600); font-weight: 500;">Le spinner est centré verticalement et horizontalement dans ce conteneur</p>
      ${spinnerTwig({ centered: true, size: 'lg', color: 'primary' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Le modifier `centered: true` positionne le spinner en absolute avec transform translate pour un centrage parfait. Le conteneur parent doit avoir `position: relative`.',
      },
    },
  },
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2.5rem; padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      
      <!-- Boutons avec différentes couleurs -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Boutons de chargement</p>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <button class="ps-button ps-button--primary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Enregistrer...
          </button>
          <button class="ps-button ps-button--secondary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Traitement...
          </button>
          <button class="ps-button ps-button--success" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Validation...
          </button>
          <button class="ps-button ps-button--danger" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
            ${spinnerTwig({ size: 'xs', color: 'white' })}
            Suppression...
          </button>
        </div>
      </div>

      <!-- Chargement de page centré -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Chargement de page</p>
        <div style="position: relative; height: 180px; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
          ${spinnerTwig({ centered: true, size: 'xl', color: 'primary', text: 'Chargement de la page...' })}
        </div>
      </div>

      <!-- Inline avec texte -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Chargement inline</p>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; background: white; padding: 1.5rem; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
          <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0; font-size: 14px;">
            ${spinnerTwig({ size: 'sm', color: 'default' })}
            Chargement des données...
          </p>
          <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0; font-size: 14px;">
            ${spinnerTwig({ size: 'sm', color: 'info' })}
            Synchronisation en cours...
          </p>
          <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0; font-size: 14px;">
            ${spinnerTwig({ size: 'sm', color: 'warning', variant: 'dots' })}
            Traitement des fichiers...
          </p>
        </div>
      </div>

      <!-- Contextes sémantiques -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Contextes sémantiques</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'circular', size: 'md', color: 'success' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Validation...</p>
          </div>
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'dots', size: 'md', color: 'info' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Information...</p>
          </div>
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'bars', size: 'md', color: 'warning' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Attention...</p>
          </div>
          <div style="padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200); text-align: center;">
            ${spinnerTwig({ variant: 'circular', size: 'md', color: 'danger' })}
            <p style="margin: 0.75rem 0 0; font-size: 13px; color: var(--gray-700);">Suppression...</p>
          </div>
        </div>
      </div>

      <!-- Sur fond sombre -->
      <div>
        <p style="margin: 0 0 1rem; font-weight: 600; font-size: 15px; color: var(--gray-800);">Sur fond sombre</p>
        <div style="padding: 2rem; background: var(--gray-800); border-radius: var(--radius-2); display: flex; gap: 2rem; align-items: center; justify-content: center;">
          ${spinnerTwig({ size: 'lg', color: 'white' })}
          <p style="color: white; margin: 0; font-size: 14px;">Chargement...</p>
        </div>
      </div>

    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Exemples d\'utilisation réels du spinner dans différents contextes : boutons de chargement (taille xs), pages centrées (taille xl), texte inline (taille sm/md), et contextes sémantiques (success, info, warning, danger).',
      },
    },
  },
};
