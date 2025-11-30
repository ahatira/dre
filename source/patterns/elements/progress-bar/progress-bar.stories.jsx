
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
 * | color         | string   | 'primary'  | Semantic color                              |
 * | size          | string   | 'md'       | Size: xs, sm, md, lg, xl                    |
 * | indeterminate | boolean  | false      | Indeterminate animation                     |
 * | striped       | boolean  | false      | Animated stripes (linear only)              |
 * | showLabel     | boolean  | false      | Show percentage label                       |
 * | label         | string   | ''         | Accessibility label                         |
 *
 * ## Design Tokens
 * - Colors: --ps-color-primary-600, --ps-color-neutral-500, --ps-color-info-600, --ps-color-success-600, --ps-color-warning-600, --ps-color-error-600
 * - Track: --ps-color-neutral-200
 * - Linear heights: 2px (xs), 4px (sm), 8px (md), 12px (lg), 16px (xl)
 * - Circular sizes: 24px (xs), 32px (sm), 40px (md), 48px (lg), 64px (xl)
 * - Border: --ps-border-radius-full
 * - Transitions: --ps-transition-duration-normal
 *
 * ## Accessibility
 * - role="progressbar"
 * - aria-valuenow, aria-valuemin, aria-valuemax
 * - aria-label
 * - Not focusable (non-interactive element)
 *
 * ## Usage Examples
 * Linear:
 *   <div class="ps-progress ps-progress--linear ps-progress--primary" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" aria-label="Upload in progress">
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
      description: 'Semantic color variant (default: neutral gray, others use --ps-color-*-600 tokens)',
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | success | warning | danger | info' },
        defaultValue: { summary: 'default' },
      },
    },
    size: {
      description: 'Size variant (xs: 2px/24px, sm: 4px/32px, md: 8px/40px, lg: 12px/48px, xl: 16px/64px - linear height / circular diameter)',
      control: { type: 'select' },
      options: ['xs', 'sm', 'md', 'lg', 'xl'],
      table: {
        category: 'Appearance',
        type: { summary: 'xs | sm | md | lg | xl' },
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
      description: 'Accessibility label for screen readers (e.g., "Upload in progress", "Processing data")',
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
        ${progressBarTwig({ variant: 'linear', color: 'default', value: 60, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Primary (green)</p>
        ${progressBarTwig({ variant: 'linear', color: 'primary', value: 60, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Secondary (purple)</p>
        ${progressBarTwig({ variant: 'linear', color: 'secondary', value: 60, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Success (75%)</p>
        ${progressBarTwig({ variant: 'linear', color: 'success', value: 75, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Warning (45%)</p>
        ${progressBarTwig({ variant: 'linear', color: 'warning', value: 45, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Danger (30%)</p>
        ${progressBarTwig({ variant: 'linear', color: 'danger', value: 30, showLabel: true })}
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">Info (85%)</p>
        ${progressBarTwig({ variant: 'linear', color: 'info', value: 85, showLabel: true })}
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
            ${progressBarTwig({ variant: 'linear', size: 'xs', value: 60, showLabel: true, color: 'primary' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">SM (4px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'sm', value: 60, showLabel: true, color: 'primary' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">MD (8px height, default)</p>
            ${progressBarTwig({ variant: 'linear', size: 'md', value: 60, showLabel: true, color: 'primary' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">LG (12px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'lg', value: 60, showLabel: true, color: 'primary' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--gray-600);">XL (16px height)</p>
            ${progressBarTwig({ variant: 'linear', size: 'xl', value: 60, showLabel: true, color: 'primary' })}
          </div>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Circular Sizes</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${progressBarTwig({ variant: 'circular', size: 'xs', value: 60, showLabel: true, color: 'primary' })}
          <span style="font-size: var(--font-size-0); color: var(--gray-600);">XS (24px)</span>
          ${progressBarTwig({ variant: 'circular', size: 'sm', value: 60, showLabel: true, color: 'primary' })}
          <span style="font-size: var(--font-size-0); color: var(--gray-600);">SM (32px)</span>
          ${progressBarTwig({ variant: 'circular', size: 'md', value: 60, showLabel: true, color: 'primary' })}
          <span style="font-size: var(--font-size-0); color: var(--gray-600);">MD (40px)</span>
          ${progressBarTwig({ variant: 'circular', size: 'lg', value: 60, showLabel: true, color: 'primary' })}
          <span style="font-size: var(--font-size-0); color: var(--gray-600);">LG (48px)</span>
          ${progressBarTwig({ variant: 'circular', size: 'xl', value: 60, showLabel: true, color: 'primary' })}
          <span style="font-size: var(--font-size-0); color: var(--gray-600);">XL (64px)</span>
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
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">File Upload Progress</h3>
        ${progressBarTwig({ variant: 'linear', value: 45, color: 'info', showLabel: true, label: 'Upload (45%)' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Processing Task (Indeterminate)</h3>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Processing data' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Step Completion</h3>
        <div style="display: flex; gap: var(--size-6); align-items: center;">
          ${progressBarTwig({ variant: 'circular', value: 33, color: 'warning', size: 'lg', showLabel: true })}
          <span>Step 1 of 3</span>
          ${progressBarTwig({ variant: 'circular', value: 66, color: 'info', size: 'lg', showLabel: true })}
          <span>Step 2 of 3</span>
          ${progressBarTwig({ variant: 'circular', value: 100, color: 'success', size: 'lg', showLabel: true })}
          <span>Complete!</span>
        </div>
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Loading with Stripes</h3>
        ${progressBarTwig({ variant: 'linear', striped: true, animated: true, value: 70, color: 'primary', showLabel: true, label: 'Loading content' })}
      </div>
    </div>
  `,
};
