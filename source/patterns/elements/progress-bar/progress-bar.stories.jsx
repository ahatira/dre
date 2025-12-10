/**
 * PS Progress Bar — Atom
 * Linear or circular progress indicator for determinate or indeterminate tasks.
 *
 * ## Design Tokens (3-layer system)
 * ### Layer 1: Root primitives (source/props/*.css)
 * - Semantic colors: --primary, --secondary, --success, --danger, --info, --warning, --gold (brand.css)
 * - Sizes: --size-1 to --size-20, --radius-round (sizes.css, borders.css)
 * - Typography: --font-size-0 to --font-size-4, --font-weight-500 (fonts.css)
 * - Animations: --duration-normal, --duration-slower, --ease-3 (animations.css, easing.css)
 * ### Layer 2: Component variables (--ps-progress-*)
 * - --ps-progress-track-bg, --ps-progress-fill-bg, --ps-progress-track-height
 * - --ps-progress-circular-size, --ps-progress-label-size, --ps-progress-gap
 * ### Layer 3: Context overrides
 * - Modifier classes (.ps-progress--primary, .ps-progress--lg, etc.)
 *
 * ## Accessibility (WCAG 2.2 AA)
 * - role="progressbar" - Identifies element as progress indicator
 * - aria-valuenow, aria-valuemin, aria-valuemax - Convey current state
 * - aria-label - Provides context for screen readers
 * - Not focusable (non-interactive element)
 * - Contrast: Minimum 3:1 between track and fill
 */

import progressBarTwig from './progress-bar.twig';
import data from './progress-bar.yml';

export default {
  title: 'Elements/Progress Bar',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `Progress indicator (linear or circular) with semantic colors, size variants, and states.\n\n**10 semantic colors**: default, primary, secondary, gold, info, warning, success, danger, dark, light\n**6 sizes**: xs, sm, md, lg, xl, xxl\n**States**: determinate, indeterminate, striped (linear only)`,
      },
    },
  },
  argTypes: {
    // Content
    value: {
      description: 'Current progress value (0-100, percentage of completion)',
      control: { type: 'range', min: 0, max: 100, step: 5 },
      table: {
        category: 'Content',
        type: { summary: 'number' },
        defaultValue: { summary: '0' },
      },
    },
    min: {
      description: 'Minimum value for progress range',
      control: { type: 'number' },
      table: {
        category: 'Content',
        type: { summary: 'number' },
        defaultValue: { summary: '0' },
      },
    },
    max: {
      description: 'Maximum value for progress range',
      control: { type: 'number' },
      table: {
        category: 'Content',
        type: { summary: 'number' },
        defaultValue: { summary: '100' },
      },
    },
    // Appearance
    variant: {
      description: 'Progress bar type (linear: horizontal bar, circular: ring)',
      control: { type: 'select' },
      options: ['linear', 'circular'],
      table: {
        category: 'Appearance',
        type: { summary: 'linear | circular' },
        defaultValue: { summary: 'linear' },
      },
    },
    color: {
      description: 'Semantic color variant using Layer 1 tokens (--primary, --success, etc.)',
      control: { type: 'select' },
      options: [
        'default',
        'primary',
        'secondary',
        'gold',
        'info',
        'warning',
        'success',
        'danger',
        'dark',
        'light',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'default | primary | secondary | gold | info | warning | success | danger | dark | light',
        },
        defaultValue: { summary: 'default' },
      },
    },
    size: {
      description:
        'Size variant (linear height / circular diameter): xs(2px/24px), sm(4px/32px), md(8px/40px), lg(12px/48px), xl(16px/64px), xxl(24px/80px)',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl | xxl' },
        defaultValue: { summary: 'md' },
      },
    },
    showLabel: {
      description: 'Display percentage label next to (linear) or inside (circular) progress bar',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Behavior
    indeterminate: {
      description: 'Indeterminate state with infinite animation (for tasks with unknown duration)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    striped: {
      description: 'Animated diagonal stripes (linear variant only, adds visual feedback)',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    // Accessibility
    label: {
      description:
        'Accessibility label for screen readers (e.g., "Property upload", "Profile completion")',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => progressBarTwig(args),
};

export const Variants = {
  name: 'All Colors',
  render: () => `
    <div style="display: grid; gap: var(--size-4);">
      ${[
        'default',
        'primary',
        'secondary',
        'gold',
        'info',
        'warning',
        'success',
        'danger',
        'dark',
        'light',
      ]
        .map(
          (color) => `
        <div>
          <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600); text-transform: capitalize;">${color}</p>
          ${color === 'light' ? '<div style="background: var(--gray-800); padding: var(--size-3); border-radius: var(--radius-2);">' : ''}
          ${progressBarTwig({ variant: 'linear', color, value: 65, showLabel: true, label: `${color} progress` })}
          ${color === 'light' ? '</div>' : ''}
        </div>
      `
        )
        .join('')}
    </div>
  `,
};

export const Sizes = {
  render: () => `
    <div style="display: grid; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Linear Sizes</h3>
        <div style="display: grid; gap: var(--size-3);">
          ${['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
            .map(
              (size) => `
            <div>
              <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600); text-transform: uppercase;">${size}</p>
              ${progressBarTwig({ variant: 'linear', size, value: 60, showLabel: true, color: 'primary', label: `${size} linear` })}
            </div>
          `
            )
            .join('')}
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Circular Sizes</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center; flex-wrap: wrap;">
          ${['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
            .map(
              (size) => `
            <div style="text-align: center;">
              ${progressBarTwig({ variant: 'circular', size, value: 60, showLabel: true, color: 'primary', label: `${size} circular` })}
              <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600); text-transform: uppercase;">${size}</span>
            </div>
          `
            )
            .join('')}
        </div>
      </div>
    </div>
  `,
};

export const States = {
  render: () => `
    <div style="display: grid; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Linear States</h3>
        <div style="display: grid; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600);">Determinate (60%)</p>
            ${progressBarTwig({ variant: 'linear', value: 60, color: 'primary', showLabel: true, label: 'Determinate linear' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600);">Striped</p>
            ${progressBarTwig({ variant: 'linear', striped: true, color: 'info', value: 60, showLabel: true, label: 'Striped linear' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate (infinite animation)</p>
            ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Indeterminate linear' })}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Circular States</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600);">Determinate</p>
            ${progressBarTwig({ variant: 'circular', value: 75, color: 'success', size: 'lg', showLabel: true, label: 'Determinate circular' })}
          </div>
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate</p>
            ${progressBarTwig({ variant: 'circular', indeterminate: true, color: 'primary', size: 'lg', label: 'Indeterminate circular' })}
          </div>
        </div>
      </div>
    </div>
  `,
};

export const RealEstateCases = {
  name: 'Real Estate Use Cases',
  render: () => `
    <div style="display: grid; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Property Document Upload</h3>
        <p style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); color: var(--gray-600);">Tracking file upload progress (floor plans, contracts, photos)</p>
        ${progressBarTwig({ variant: 'linear', value: 45, color: 'info', showLabel: true, label: 'Uploading floor plans and contracts' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Lease Application Processing</h3>
        <p style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); color: var(--gray-600);">Indeterminate state for server-side processing</p>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Processing tenant application data' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Agent Profile Completion</h3>
        <p style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); color: var(--gray-600);">Circular indicator for profile status</p>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${progressBarTwig({ variant: 'circular', value: 33, color: 'warning', size: 'lg', showLabel: true, label: 'Agent profile 33% complete' })}
          <div>
            <p style="margin: 0 0 var(--size-1); font-weight: var(--font-weight-600);">Complete your profile</p>
            <p style="margin: 0; font-size: var(--font-size-0); color: var(--gray-600);">Add certifications and experience</p>
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Virtual Tour Loading</h3>
        <p style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); color: var(--gray-600);">Striped animation for 3D model loading</p>
        ${progressBarTwig({ variant: 'linear', striped: true, value: 70, color: 'primary', showLabel: true, label: 'Loading 3D virtual tour' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Multi-Step Lease Process</h3>
        <p style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); color: var(--gray-600);">Step-by-step progress tracking</p>
        <div style="display: flex; gap: var(--size-4); align-items: center;">
          ${progressBarTwig({ variant: 'circular', value: 100, color: 'success', size: 'md', showLabel: true, label: 'Step 1: Identity verified' })}
          <span style="font-size: var(--font-size-1); color: var(--gray-700);">Identity</span>
          ${progressBarTwig({ variant: 'circular', value: 66, color: 'info', size: 'md', showLabel: true, label: 'Step 2: Documents in progress' })}
          <span style="font-size: var(--font-size-1); color: var(--gray-700);">Documents</span>
          ${progressBarTwig({ variant: 'circular', value: 0, color: 'default', size: 'md', showLabel: false, label: 'Step 3: Payment pending' })}
          <span style="font-size: var(--font-size-1); color: var(--gray-500);">Payment</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-2); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Storage Quota Warning</h3>
        <p style="margin: 0 0 var(--size-3); font-size: var(--font-size-1); color: var(--gray-600);">Critical state with danger color</p>
        ${progressBarTwig({ variant: 'linear', value: 92, color: 'danger', size: 'lg', showLabel: true, label: 'Storage usage: 92% - upgrade needed' })}
      </div>
    </div>
  `,
};
