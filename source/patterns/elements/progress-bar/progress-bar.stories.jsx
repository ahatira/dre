
/**
 * PS Progress Bar — Atom
 * Indicateur de progression linéaire ou circulaire, pour tâches déterminées ou indéterminées.
 *
 * ## Props
 * | Prop          | Type     | Default    | Description                                 |
 * |---------------|----------|------------|---------------------------------------------|
 * | value         | number   | 0          | Valeur actuelle (0-100)                     |
 * | min           | number   | 0          | Valeur minimale                             |
 * | max           | number   | 100        | Valeur maximale                             |
 * | variant       | string   | 'linear'   | Type : 'linear' ou 'circular'               |
 * | color         | string   | 'primary'  | Couleur sémantique                          |
 * | size          | string   | 'md'       | Taille : xs, sm, md, lg, xl                 |
 * | indeterminate | boolean  | false      | Animation indéterminée                      |
 * | striped       | boolean  | false      | Rayures animées (linear)                    |
 * | showLabel     | boolean  | false      | Afficher le pourcentage                     |
 * | label         | string   | ''         | Label accessibilité                         |
 *
 * ## Design Tokens
 * - Couleurs : --ps-color-primary-600, --ps-color-neutral-500, --ps-color-info-600, --ps-color-success-600, --ps-color-warning-600, --ps-color-error-600
 * - Track : --ps-color-neutral-200
 * - Hauteurs linéaires : 4px, 8px, 12px
 * - Tailles circulaires : 40px, 64px, 96px
 * - Bordures : --ps-border-radius-full
 * - Transitions : --ps-transition-duration-normal
 *
 * ## Accessibilité
 * - role="progressbar"
 * - aria-valuenow, aria-valuemin, aria-valuemax
 * - aria-label
 * - Non focusable (élément non-interactif)
 *
 * ## Exemples d'usage
 * Linear :
 *   <div class="ps-progress ps-progress--linear ps-progress--primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" aria-label="Upload en cours">
 *     <div class="ps-progress__track">
 *       <div class="ps-progress__fill" style="width: 60%;"></div>
 *     </div>
 *     <span class="ps-progress__label">60%</span>
 *   </div>
 * Circular :
 *   <div class="ps-progress ps-progress--circular ps-progress--success" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
 *     <svg class="ps-progress__svg" viewBox="0 0 100 100">...</svg>
 *     <span class="ps-progress__label">75%</span>
 *   </div>
 */

import progressBarTwig from './progress-bar.twig';
import data from './progress-bar.yml';

export default {
  title: 'Elements/Progress Bar',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Indicateur de progression pour tâches déterminées ou indéterminées (upload, téléchargement, formulaire multi-étapes).\n\n' +
          '- **Variantes**: linear (barre horizontale), circular (anneau).\n' +
          '- **Couleurs**: primary, secondary, success, warning, danger, info — tokens sémantiques via `--ps-color-*-600`.\n' +
          '- **Tailles**: xs, sm, md (défaut), lg, xl — hauteurs linéaires: 4px, 8px, 12px; tailles circulaires: 40px, 64px, 96px.\n' +
          '- **États**: indeterminate (animation infinie), striped (rayures animées pour linear).\n' +
          '- **Label**: `showLabel` affiche le pourcentage; `label` fournit un texte pour les lecteurs d\'écran.\n' +
          '- **Accessibilité**: role="progressbar", aria-valuenow, aria-valuemin, aria-valuemax, aria-label; non focusable (élément non-interactif).\n' +
          '- **Design tokens**: --ps-color-neutral-200 (track), --ps-border-radius-full, --ps-transition-duration-normal.\n' +
          '- **Rendu minimal**: la classe de base applique les styles par défaut; les modificateurs n\'apparaissent que si l\'option change du défaut.',
      },
    },
  },
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
      ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'primary', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'secondary', value: 60, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'success', value: 75, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'warning', value: 45, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'danger', value: 30, showLabel: true })}
      ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'info', value: 85, showLabel: true })}
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
