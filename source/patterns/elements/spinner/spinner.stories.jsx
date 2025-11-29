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
      options: ['primary', 'secondary', 'white', 'neutral'],
      description: 'Couleur du spinner',
      table: {
        type: { summary: 'primary | secondary | white | neutral' },
        defaultValue: { summary: 'primary' },
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

// === Variant Stories ===

export const Circular = {
  render: () => spinnerTwig({ variant: 'circular' }),
};

export const Dots = {
  render: () => spinnerTwig({ variant: 'dots' }),
};

export const Bars = {
  render: () => spinnerTwig({ variant: 'bars' }),
};

// === Grouped Showcases ===

export const AllVariants = {
  render: () => `
    <div style="display: flex; gap: 2rem; align-items: center; padding: 1rem;">
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Circular</p>
        ${spinnerTwig({ variant: 'circular' })}
      </div>
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Dots</p>
        ${spinnerTwig({ variant: 'dots' })}
      </div>
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Bars</p>
        ${spinnerTwig({ variant: 'bars' })}
      </div>
    </div>
  `,
  args: {},
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: 2rem; align-items: center; padding: 1rem;">
      ${spinnerTwig({ size: 'xs' })}
      ${spinnerTwig({ size: 'sm' })}
      ${spinnerTwig({ size: 'md' })}
      ${spinnerTwig({ size: 'lg' })}
      ${spinnerTwig({ size: 'xl' })}
    </div>
  `,
  args: {},
};

export const AllColors = {
  render: () => `
    <div style="display: flex; gap: 2rem; align-items: center; padding: 1rem;">
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Primary</p>
        ${spinnerTwig({ color: 'primary' })}
      </div>
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Secondary</p>
        ${spinnerTwig({ color: 'secondary' })}
      </div>
      <div style="text-align: center; background: var(--gray-800); padding: 1rem; border-radius: var(--radius-2);">
        <p style="margin: 0 0 0.5rem; font-weight: 500; color: white;">White</p>
        ${spinnerTwig({ color: 'white' })}
      </div>
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Neutral</p>
        ${spinnerTwig({ color: 'neutral' })}
      </div>
    </div>
  `,
  args: {},
};

export const Centered = {
  render: () => `
    <div style="position: relative; height: 200px; border: 2px dashed var(--gray-300); border-radius: var(--radius-2);">
      <p style="margin: 1rem; font-size: 14px; color: var(--gray-600);">Le spinner est centré dans ce conteneur</p>
      ${spinnerTwig({ centered: true, size: 'lg' })}
    </div>
  `,
  args: {},
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem; padding: 1rem;">
      <!-- Inline with button -->
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Bouton de chargement</p>
        <button class="ps-button ps-button--primary" disabled style="display: inline-flex; align-items: center; gap: var(--size-2);">
          ${spinnerTwig({ size: 'xs', color: 'white' })}
          Envoi en cours...
        </button>
      </div>

      <!-- Centered in container -->
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Chargement de page</p>
        <div style="position: relative; height: 150px; background: var(--gray-50); border-radius: var(--radius-2);">
          ${spinnerTwig({ centered: true, size: 'lg', text: 'Chargement de la page...' })}
        </div>
      </div>

      <!-- Inline with text -->
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Chargement inline</p>
        <p style="display: flex; align-items: center; gap: var(--size-2); margin: 0;">
          ${spinnerTwig({ size: 'sm' })}
          Traitement de vos données...
        </p>
      </div>

      <!-- Different variants -->
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Variantes pour différents contextes</p>
        <div style="display: flex; gap: 2rem;">
          <div style="text-align: center;">
            ${spinnerTwig({ variant: 'circular', size: 'md' })}
            <p style="margin: 0.5rem 0 0; font-size: 12px;">Par défaut</p>
          </div>
          <div style="text-align: center;">
            ${spinnerTwig({ variant: 'dots', size: 'md', color: 'secondary' })}
            <p style="margin: 0.5rem 0 0; font-size: 12px;">Subtil</p>
          </div>
          <div style="text-align: center;">
            ${spinnerTwig({ variant: 'bars', size: 'md' })}
            <p style="margin: 0.5rem 0 0; font-size: 12px;">Alternatif</p>
          </div>
        </div>
      </div>
    </div>
  `,
  args: {},
};
