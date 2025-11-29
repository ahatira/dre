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

// === Harmonized Showcase Stories ===
export const AllLinearColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
      ${progressBarTwig({ variant: 'linear', color: 'default', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', color: 'primary', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', color: 'secondary', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', color: 'success', value: 75, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', color: 'warning', value: 45, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', color: 'danger', value: 30, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', color: 'info', value: 85, showLabel: true })}
    </div>
  `,
  args: {},
};

export const AllCircularColors = {
  render: () => `
    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; padding: 1rem; align-items: center;">
      ${progressBarTwig({ variant: 'circular', color: 'default', value: 60, size: 'lg', showLabel: true })}
      ${progressBarTwig({ variant: 'circular', color: 'primary', value: 60, size: 'lg', showLabel: true })}
      ${progressBarTwig({ variant: 'circular', color: 'secondary', value: 60, size: 'lg', showLabel: true })}
      ${progressBarTwig({ variant: 'circular', color: 'success', value: 75, size: 'lg', showLabel: true })}
      ${progressBarTwig({ variant: 'circular', color: 'warning', value: 45, size: 'lg', showLabel: true })}
      ${progressBarTwig({ variant: 'circular', color: 'danger', value: 30, size: 'lg', showLabel: true })}
      ${progressBarTwig({ variant: 'circular', color: 'info', value: 85, size: 'lg', showLabel: true })}
    </div>
  `,
  args: {},
};

export const AllStripedColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
      ${progressBarTwig({ variant: 'linear', striped: true, color: 'primary', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, color: 'secondary', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, color: 'success', value: 75, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, color: 'warning', value: 45, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, color: 'danger', value: 30, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, color: 'info', value: 85, showLabel: true })}
    </div>
  `,
  args: {},
};

export const AllLinearSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
      ${progressBarTwig({ variant: 'linear', size: 'xs', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'linear', size: 'sm', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'linear', size: 'md', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'linear', size: 'lg', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'linear', size: 'xl', value: 60, showLabel: true, color: 'primary' })}
    </div>
  `,
  args: {},
};

export const AllCircularSizes = {
  render: () => `
    <div style="display: flex; gap: 1.5rem; padding: 1rem; align-items: center;">
      ${progressBarTwig({ variant: 'circular', size: 'xs', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'circular', size: 'sm', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'circular', size: 'md', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'circular', size: 'lg', value: 60, showLabel: true, color: 'primary' })}
      ${progressBarTwig({ variant: 'circular', size: 'xl', value: 60, showLabel: true, color: 'primary' })}
    </div>
  `,
  args: {},
};

export const UseCases = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 2rem; padding: 1rem; align-items: start;">
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Téléversement de fichier</p>
        ${progressBarTwig({ variant: 'linear', value: 45, color: 'info', showLabel: true, label: 'Téléversement (45%)' })}
      </div>
      <div>
        <p style="margin: 0 0 0.5rem; font-weight: 500;">Traitement en cours</p>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Traitement' })}
      </div>
      <div style="text-align: center;">
        <p style="margin: 0 0 0.5rem; font-weight: 500;">État global</p>
        ${progressBarTwig({ variant: 'circular', value: 100, color: 'success', size: 'lg', showLabel: true })}
      </div>
    </div>
  `,
  args: {},
};
