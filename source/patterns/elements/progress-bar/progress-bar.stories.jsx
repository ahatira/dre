import progressBarTwig from './progress-bar.twig';
import data from './progress-bar.yml';

export default {
  title: 'Elements/Progress Bar',
  tags: ['autodocs'],
  argTypes: {
    value: { control: 'number', description: 'Valeur actuelle (0-100)' },
    min: { control: 'number', description: 'Valeur minimale' },
    max: { control: 'number', description: 'Valeur maximale' },
    variant: { control: 'select', options: ['linear', 'circular'], description: 'Type de barre' },
    color: {
      control: 'select',
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Couleur sémantique (default = gris)',
    },
    size: { control: 'select', options: ['xs', 'sm', 'md', 'lg', 'xl'], description: 'Taille' },
    indeterminate: { control: 'boolean', description: 'Indéterminé (animation)' },
    striped: { control: 'boolean', description: 'Rayures animées (linear)' },
    showLabel: { control: 'boolean', description: 'Afficher le pourcentage' },
    label: { control: 'text', description: 'Label accessibilité' },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => progressBarTwig(args),
  args: { ...data, value: 60 },
};

// === Individual Color Variants (Linear) ===
export const Primary = {
  render: (args) => progressBarTwig(args),
  args: { ...data, color: 'primary', value: 60 },
};

export const Secondary = {
  render: (args) => progressBarTwig(args),
  args: { ...data, color: 'secondary', value: 60 },
};

export const Success = {
  render: (args) => progressBarTwig(args),
  args: { ...data, color: 'success', value: 75 },
};

export const Warning = {
  render: (args) => progressBarTwig(args),
  args: { ...data, color: 'warning', value: 45 },
};

export const Danger = {
  render: (args) => progressBarTwig(args),
  args: { ...data, color: 'danger', value: 30 },
};

export const Info = {
  render: (args) => progressBarTwig(args),
  args: { ...data, color: 'info', value: 85 },
};

// === Size Variants ===
export const XS = {
  render: (args) => progressBarTwig(args),
  args: { ...data, size: 'xs', value: 60 },
};

export const SM = {
  render: (args) => progressBarTwig(args),
  args: { ...data, size: 'sm', value: 60 },
};

export const MD = {
  render: (args) => progressBarTwig(args),
  args: { ...data, size: 'md', value: 60 },
};

export const LG = {
  render: (args) => progressBarTwig(args),
  args: { ...data, size: 'lg', value: 60 },
};

export const XL = {
  render: (args) => progressBarTwig(args),
  args: { ...data, size: 'xl', value: 60 },
};

// === Circular Variants ===
export const CircularPrimary = {
  render: (args) => progressBarTwig(args),
  args: { ...data, variant: 'circular', color: 'primary', value: 60, size: 'lg' },
};

export const CircularSuccess = {
  render: (args) => progressBarTwig(args),
  args: { ...data, variant: 'circular', color: 'success', value: 75, size: 'lg' },
};

export const CircularWarning = {
  render: (args) => progressBarTwig(args),
  args: { ...data, variant: 'circular', color: 'warning', value: 45, size: 'lg' },
};

// === Special States ===
export const IndeterminateLinear = {
  render: (args) => progressBarTwig(args),
  args: { ...data, indeterminate: true, label: 'Chargement en cours', showLabel: false, color: 'primary' },
};

export const IndeterminateCircular = {
  render: (args) => progressBarTwig(args),
  args: { ...data, variant: 'circular', indeterminate: true, label: 'Chargement', showLabel: false, size: 'lg' },
};

export const Striped = {
  render: (args) => progressBarTwig(args),
  args: { ...data, value: 45, striped: true, color: 'warning' },
};

export const WithLabel = {
  render: (args) => progressBarTwig(args),
  args: { ...data, value: 60, showLabel: true },
};

// === Showcase Stories ===
export const AllColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem; padding: 1rem;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Primary</p>
        ${progressBarTwig({ variant: 'linear', color: 'primary', value: 60, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Secondary</p>
        ${progressBarTwig({ variant: 'linear', color: 'secondary', value: 60, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Success</p>
        ${progressBarTwig({ variant: 'linear', color: 'success', value: 75, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Warning</p>
        ${progressBarTwig({ variant: 'linear', color: 'warning', value: 45, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Danger</p>
        ${progressBarTwig({ variant: 'linear', color: 'danger', value: 30, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Info</p>
        ${progressBarTwig({ variant: 'linear', color: 'info', value: 85, showLabel: true })}
      </div>
    </div>
  `,
  args: {},
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1.5rem; padding: 1rem;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">XS (Extra Small)</p>
        ${progressBarTwig({ variant: 'linear', size: 'xs', value: 60, showLabel: true, color: 'primary' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">SM (Small)</p>
        ${progressBarTwig({ variant: 'linear', size: 'sm', value: 60, showLabel: true, color: 'primary' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">MD (Medium)</p>
        ${progressBarTwig({ variant: 'linear', size: 'md', value: 60, showLabel: true, color: 'primary' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">LG (Large)</p>
        ${progressBarTwig({ variant: 'linear', size: 'lg', value: 60, showLabel: true, color: 'primary' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">XL (Extra Large)</p>
        ${progressBarTwig({ variant: 'linear', size: 'xl', value: 60, showLabel: true, color: 'primary' })}
      </div>
      <div style="margin-top: 1rem;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Circular Sizes</p>
        <div style="display: flex; gap: 2rem; align-items: center;">
          ${progressBarTwig({ variant: 'circular', size: 'xs', value: 60, showLabel: true, color: 'primary' })}
          ${progressBarTwig({ variant: 'circular', size: 'sm', value: 60, showLabel: true, color: 'primary' })}
          ${progressBarTwig({ variant: 'circular', size: 'md', value: 60, showLabel: true, color: 'primary' })}
          ${progressBarTwig({ variant: 'circular', size: 'lg', value: 60, showLabel: true, color: 'primary' })}
          ${progressBarTwig({ variant: 'circular', size: 'xl', value: 60, showLabel: true, color: 'primary' })}
        </div>
      </div>
    </div>
  `,
  args: {},
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem; padding: 1rem;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Linear - Standard</p>
        ${progressBarTwig({ variant: 'linear', value: 60, color: 'primary', showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Linear - Indeterminate</p>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Chargement en cours' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Linear - Striped</p>
        ${progressBarTwig({ variant: 'linear', value: 45, striped: true, color: 'warning', showLabel: true })}
      </div>
      <div style="margin-top: 1rem;">
        <p style="margin: 0 0 1rem; font-weight: 500;">Circular Variants</p>
        <div style="display: flex; gap: 2rem; align-items: center;">
          ${progressBarTwig({ variant: 'circular', value: 60, color: 'primary', size: 'lg', showLabel: true })}
          ${progressBarTwig({ variant: 'circular', value: 75, color: 'success', size: 'lg', showLabel: true })}
          ${progressBarTwig({ variant: 'circular', indeterminate: true, color: 'info', size: 'lg', label: 'Chargement' })}
        </div>
      </div>
    </div>
  `,
  args: {},
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem; padding: 1rem; max-width: 600px;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 600;">Upload de fichier</p>
        ${progressBarTwig({ variant: 'linear', value: 65, color: 'primary', showLabel: true, label: 'Upload de document.pdf' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 600;">Installation en cours</p>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'info', label: 'Installation des dépendances' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 600;">Traitement avec avertissement</p>
        ${progressBarTwig({ variant: 'linear', value: 45, striped: true, color: 'warning', showLabel: true, label: 'Traitement des données' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 600;">Opération critique</p>
        ${progressBarTwig({ variant: 'linear', value: 30, color: 'danger', showLabel: true, label: 'Espace disque restant' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 600;">Validation complétée</p>
        ${progressBarTwig({ variant: 'linear', value: 100, color: 'success', showLabel: true, label: 'Synchronisation terminée' })}
      </div>
      <div style="margin-top: 1rem;">
        <p style="margin: 0 0 1rem; font-weight: 600;">Statut de profil (circulaire)</p>
        <div style="display: flex; gap: 2rem; align-items: center;">
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', value: 35, color: 'danger', size: 'lg', showLabel: true })}
            <p style="margin: 0.5rem 0 0; font-size: 12px;">Incomplet</p>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', value: 75, color: 'warning', size: 'lg', showLabel: true })}
            <p style="margin: 0.5rem 0 0; font-size: 12px;">Presque prêt</p>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', value: 100, color: 'success', size: 'lg', showLabel: true })}
            <p style="margin: 0.5rem 0 0; font-size: 12px;">Complet</p>
          </div>
        </div>
      </div>
    </div>
  `,
  args: {},
};
