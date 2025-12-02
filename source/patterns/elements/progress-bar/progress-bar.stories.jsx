/**
 * PS Progress Bar — Atom
 * Linear or circular progress indicator for determinate or indeterminate tasks.
 *
 * ## Props
 * | Prop          | Type     | Default    | Description                                 |
 * |---------------|----------|------------|---------------------------------------------|
 * | value         | number   | 0          | Current value (0-100)                       |
 * | min           | number   | 0          | Minimum value                               |
 * | max           | number   | 100        | Maximum value                               |
 * | variant       | string   | 'linear'   | Type: 'linear' or 'circular'                |
 * | color         | string   | 'default'  | Semantic color (9 variants)                 |
 * | size          | string   | 'md'       | Size: xs, sm, md, lg, xl, xxl               |
 * | indeterminate | boolean  | false      | Indeterminate animation                     |
 * | striped       | boolean  | false      | Animated stripes (linear only)              |
 * | showLabel     | boolean  | false      | Show percentage label                       |
 * | label         | string   | ''         | Accessibility label                         |
 *
 * ## Design Tokens (3-layer system)
 * ### Layer 1: Root primitives
 * - Colors: --green-600, --blue-600, --red-600, --yellow-500, --gray-* (source/props/colors.css)
 * - Sizes: --size-1 to --size-16, --radius-round (source/props/sizes.css, borders.css)
 * - Durations: --duration-normal, --duration-slower (source/props/animations.css)
 * - Easing: --ease-3, --ease-in-out-3 (source/props/easing.css)
 * ### Layer 2: Component variables
 * - --ps-progress-track-bg, --ps-progress-fill-bg, --ps-progress-track-height
 * - --ps-progress-circular-size, --ps-progress-label-size, --ps-progress-gap
 * ### Layer 3: Context overrides
 * - Modifier classes (.ps-progress--primary, .ps-progress--lg, etc.)
 *
 * ## Accessibility
 * - role="progressbar"
 * - aria-valuenow, aria-valuemin, aria-valuemax
 * - aria-label
 * - Not focusable (non-interactive element)
 *
 * ## Usage Examples
 * Linear:
 *   <div class="ps-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" aria-label="Property document upload">
 *     <div class="ps-progress__track">
 *       <div class="ps-progress__fill" style="width: 60%;"></div>
 *     </div>
 *     <span class="ps-progress__label">60%</span>
 *   </div>
 * Circular:
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
        component: `Semantic progress indicator (linear or circular) for task status.
Supports sizes, semantic colors, indeterminate/striped states, and accessible labels.`,
      },
    },
  },
  argTypes: {
    // Content
    value: {
      description: 'Current progress value (0-100, percentage of completion)',
      control: { type: 'number', min: 0, max: 100, step: 1 },
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
      description: 'Semantic color variant (default: neutral gray, others use component variables)',
      control: { type: 'select' },
      options: [
        'default',
        'primary',
        'secondary',
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
            'default | primary | secondary | info | warning | success | danger | dark | light',
        },
        defaultValue: { summary: 'default' },
      },
    },
    size: {
      description:
        'Size variant (xs: 2px/24px, sm: 4px/32px, md: 8px/40px, lg: 12px/48px, xl: 16px/64px, xxl: 24px/80px - linear height / circular diameter)',
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
        'Accessibility label for screen readers (e.g., "Upload in progress", "Processing data")',
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
  args: { ...data, value: 60 },
};

export const AllColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (neutral gray)</p>
        ${progressBarTwig({ variant: 'linear', color: 'default', value: 60, showLabel: true, label: 'Property listing progress' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green)</p>
        ${progressBarTwig({ variant: 'linear', color: 'primary', value: 60, showLabel: true, label: 'Document upload' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple)</p>
        ${progressBarTwig({ variant: 'linear', color: 'secondary', value: 60, showLabel: true, label: 'Virtual tour loading' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (blue) - 85%</p>
        ${progressBarTwig({ variant: 'linear', color: 'info', value: 85, showLabel: true, label: 'Property data sync' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (yellow) - 45%</p>
        ${progressBarTwig({ variant: 'linear', color: 'warning', value: 45, showLabel: true, label: 'Profile completion' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (green) - 100%</p>
        ${progressBarTwig({ variant: 'linear', color: 'success', value: 100, showLabel: true, label: 'Lease agreement signed' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (red) - 15%</p>
        ${progressBarTwig({ variant: 'linear', color: 'danger', value: 15, showLabel: true, label: 'Critical: Low storage' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (near black)</p>
        ${progressBarTwig({ variant: 'linear', color: 'dark', value: 70, showLabel: true, label: 'Report generation' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Light (near white)</p>
        <div style="background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
          ${progressBarTwig({ variant: 'linear', color: 'light', value: 50, showLabel: true, label: 'Image optimization' })}
        </div>
      </div>
    </div>
  `,
};

export const AllStriped = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-4);">
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Default (neutral gray) - Striped</p>
        ${progressBarTwig({ variant: 'linear', color: 'default', value: 60, showLabel: true, striped: true, label: 'Property listing progress' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (brand green) - Striped</p>
        ${progressBarTwig({ variant: 'linear', color: 'primary', value: 60, showLabel: true, striped: true, label: 'Document upload' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple) - Striped</p>
        ${progressBarTwig({ variant: 'linear', color: 'secondary', value: 60, showLabel: true, striped: true, label: 'Virtual tour loading' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (blue) - Striped 85%</p>
        ${progressBarTwig({ variant: 'linear', color: 'info', value: 85, showLabel: true, striped: true, label: 'Property data sync' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (yellow) - Striped 45%</p>
        ${progressBarTwig({ variant: 'linear', color: 'warning', value: 45, showLabel: true, striped: true, label: 'Profile completion' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (green) - Striped 100%</p>
        ${progressBarTwig({ variant: 'linear', color: 'success', value: 100, showLabel: true, striped: true, label: 'Lease agreement signed' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (red) - Striped 15%</p>
        ${progressBarTwig({ variant: 'linear', color: 'danger', value: 15, showLabel: true, striped: true, label: 'Critical: Low storage' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Dark (near black) - Striped</p>
        ${progressBarTwig({ variant: 'linear', color: 'dark', value: 70, showLabel: true, striped: true, label: 'Report generation' })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Light (near white) - Striped</p>
        <div style="background: var(--gray-800); padding: var(--size-4); border-radius: var(--radius-2);">
          ${progressBarTwig({ variant: 'linear', color: 'light', value: 50, showLabel: true, striped: true, label: 'Image optimization' })}
        </div>
      </div>
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Linear Sizes</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XS (2px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'xs', value: 60, showLabel: true, color: 'primary', label: 'Compact view' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">SM (4px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'sm', value: 60, showLabel: true, color: 'primary', label: 'Small progress' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">MD (8px height, default)</p>
            ${progressBarTwig({ variant: 'linear', size: 'md', value: 60, showLabel: true, color: 'primary', label: 'Standard size' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">LG (12px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'lg', value: 60, showLabel: true, color: 'primary', label: 'Large display' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XL (16px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'xl', value: 60, showLabel: true, color: 'primary', label: 'Extra large' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XXL (24px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'xxl', value: 60, showLabel: true, color: 'primary', label: 'Hero display' })}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular Sizes</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center; flex-wrap: wrap;">
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', size: 'xs', value: 60, showLabel: true, color: 'primary', label: 'XS 24px' })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XS (24px)</span>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', size: 'sm', value: 60, showLabel: true, color: 'primary', label: 'SM 32px' })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">SM (32px)</span>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', size: 'md', value: 60, showLabel: true, color: 'primary', label: 'MD 40px' })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">MD (40px)</span>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', size: 'lg', value: 60, showLabel: true, color: 'primary', label: 'LG 48px' })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">LG (48px)</span>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', size: 'xl', value: 60, showLabel: true, color: 'primary', label: 'XL 64px' })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XL (64px)</span>
          </div>
          <div style="text-align: center;">
            ${progressBarTwig({ variant: 'circular', size: 'xxl', value: 60, showLabel: true, color: 'primary', label: 'XXL 80px' })}
            <span style="display: block; margin-top: var(--size-1); font-size: var(--font-size-0); color: var(--gray-600);">XXL (80px)</span>
          </div>
        </div>
      </div>
    </div>
  `,
};

export const AllStates = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Linear States</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Determinate (60%)</p>
            ${progressBarTwig({ variant: 'linear', value: 60, color: 'primary', showLabel: true })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Striped (animated diagonal stripes)</p>
            ${progressBarTwig({ variant: 'linear', striped: true, animated: true, color: 'info', value: 60, showLabel: true })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate (infinite animation)</p>
            ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Processing' })}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular States</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Determinate</p>
            ${progressBarTwig({ variant: 'circular', value: 75, color: 'success', size: 'lg', showLabel: true })}
          </div>
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate</p>
            ${progressBarTwig({ variant: 'circular', indeterminate: true, color: 'primary', size: 'lg' })}
          </div>
        </div>
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Document Upload</h3>
        ${progressBarTwig({ variant: 'linear', value: 45, color: 'info', showLabel: true, label: 'Uploading floor plans and contracts' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Processing Lease Application (Indeterminate)</h3>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Processing tenant application data' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Profile Completion Status</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${progressBarTwig({ variant: 'circular', value: 33, color: 'warning', size: 'lg', showLabel: true, label: 'Agent profile 33% complete' })}
          <span>Complete your agent profile</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Property Tour Video Loading</h3>
        ${progressBarTwig({ variant: 'linear', striped: true, animated: true, value: 70, color: 'primary', showLabel: true, label: 'Loading 3D virtual tour' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Multi-Step Lease Process</h3>
        <div style="display: flex; gap: var(--size-4); align-items: center;">
          ${progressBarTwig({ variant: 'circular', value: 100, color: 'success', size: 'md', showLabel: true, label: 'Step 1: Identity verified' })}
          <span style="font-size: var(--font-size-1);">Identity</span>
          ${progressBarTwig({ variant: 'circular', value: 66, color: 'info', size: 'md', showLabel: true, label: 'Step 2: Documents in progress' })}
          <span style="font-size: var(--font-size-1);">Documents</span>
          ${progressBarTwig({ variant: 'circular', value: 0, color: 'default', size: 'md', showLabel: false, label: 'Step 3: Payment pending' })}
          <span style="font-size: var(--font-size-1);">Payment</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Critical: Storage Quota Low</h3>
        ${progressBarTwig({ variant: 'linear', value: 92, color: 'danger', size: 'lg', showLabel: true, label: 'Storage usage: 92% - upgrade needed' })}
      </div>
    </div>
  `,
};
