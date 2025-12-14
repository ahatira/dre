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
        component: `Progress indicator (linear or circular) with semantic colors and size variants. Used for file uploads, loading states, and step-by-step processes.\n\n**9 semantic colors**: neutral, primary, secondary, gold, info, warning, success, danger, light, dark\n**3 sizes**: small, medium (default), large\n**States**: determinate, indeterminate, striped (linear only)`,
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
        'neutral',
        'primary',
        'secondary',
        'gold',
        'info',
        'warning',
        'success',
        'danger',
        'light',
        'dark',
      ],
      table: {
        category: 'Appearance',
        type: {
          summary:
            'neutral | primary | secondary | gold | info | warning | success | danger | light | dark',
        },
        defaultValue: { summary: 'neutral' },
      },
    },
    size: {
      description:
        'Size variant (small: 6px/32px, medium: 8px/40px, large: 12px/56px - linear height / circular diameter)',
      control: { type: 'select' },
      options: ['small', 'medium', 'large'],
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
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

export const SemanticColors = {
  render: () => `
    <div style="display: grid; gap: var(--size-4);">
      ${[
        'neutral',
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
          ${['small', 'medium', 'large']
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
          ${['small', 'medium', 'large']
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
            ${progressBarTwig({ variant: 'circular', value: 75, color: 'success', size: 'large', showLabel: true, label: 'Determinate circular' })}
          </div>
          <div style="text-align: center;">
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-0); color: var(--gray-600);">Indeterminate</p>
            ${progressBarTwig({ variant: 'circular', indeterminate: true, color: 'primary', size: 'large', label: 'Indeterminate circular' })}
          </div>
        </div>
      </div>
    </div>
  `,
};

export const OnDarkBackground = {
  render: () => `
    <div style="padding: var(--size-6); background: var(--gray-900); border-radius: var(--radius-3);">
      <h3 style="margin: 0 0 var(--size-4); font-size: var(--font-size-2); font-weight: var(--font-weight-600); color: white;">Dark Variant on Dark Background</h3>
      <div style="display: grid; gap: var(--size-4);">
        <div>
          <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); color: var(--gray-300);">Light (visibility on dark)</p>
          ${progressBarTwig({ variant: 'linear', value: 65, color: 'light', showLabel: true, label: 'Light progress on dark background' })}
        </div>
        <div>
          <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); color: var(--gray-300);">Primary (brand color works well)</p>
          ${progressBarTwig({ variant: 'linear', value: 65, color: 'primary', showLabel: true, label: 'Primary progress on dark background' })}
        </div>
        <div>
          <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); color: var(--gray-300);">Success (high contrast)</p>
          ${progressBarTwig({ variant: 'linear', value: 65, color: 'success', showLabel: true, label: 'Success progress on dark background' })}
        </div>
      </div>
      <h3 style="margin: var(--size-6) 0 var(--size-4); font-size: var(--font-size-2); font-weight: var(--font-weight-600); color: white;">Dark Variant on Light Background</h3>
      <div style="padding: var(--size-4); background: white; border-radius: var(--radius-2);">
        <div style="display: grid; gap: var(--size-4);">
          <div>
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); color: var(--gray-700);">Dark (high contrast on light)</p>
            ${progressBarTwig({ variant: 'linear', value: 65, color: 'dark', showLabel: true, label: 'Dark progress on light background' })}
          </div>
          <div>
            <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); color: var(--gray-700);">Neutral (default gray)</p>
            ${progressBarTwig({ variant: 'linear', value: 65, color: 'neutral', showLabel: true, label: 'Neutral progress on light background' })}
          </div>
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Dark and light variants shown on appropriate backgrounds. Use light/primary/success on dark backgrounds, use dark/neutral on light backgrounds for proper contrast.',
      },
    },
  },
};

export const FileUpload = {
  render: () => `
    <div style="display: grid; gap: var(--size-6); max-width: 600px;">
      <div>
        <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); font-weight: var(--font-weight-600); color: var(--gray-800);">Uploading property photos...</p>
        ${progressBarTwig({ variant: 'linear', value: 45, color: 'info', showLabel: true, label: 'Uploading floor plans and contracts' })}
        <p style="margin: var(--size-1) 0 0; font-size: var(--font-size-0); color: var(--gray-600);">3 of 8 files uploaded</p>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); font-weight: var(--font-weight-600); color: var(--gray-800);">Processing documents...</p>
        ${progressBarTwig({ variant: 'linear', indeterminate: true, color: 'primary', label: 'Processing tenant application data' })}
        <p style="margin: var(--size-1) 0 0; font-size: var(--font-size-0); color: var(--gray-600);">Please wait while we verify your documents</p>
      </div>
      <div>
        <p style="margin: 0 0 var(--size-2); font-size: var(--font-size-1); font-weight: var(--font-weight-600); color: var(--gray-800);">Loading 3D virtual tour...</p>
        ${progressBarTwig({ variant: 'linear', striped: true, value: 70, color: 'primary', showLabel: true, label: 'Loading 3D virtual tour' })}
        <p style="margin: var(--size-1) 0 0; font-size: var(--font-size-0); color: var(--gray-600);">High-resolution model loading</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'File upload progress indicators showing determinate (with percentage), indeterminate (unknown duration), and striped (animated) states.',
      },
    },
  },
};

export const MultiStepProcess = {
  render: () => `
    <div style="display: flex; gap: var(--size-4); align-items: center; max-width: 600px;">
      ${progressBarTwig({ variant: 'circular', value: 100, color: 'success', size: 'medium', showLabel: true, label: 'Step 1: Identity verified' })}
      <span style="font-size: var(--font-size-1); color: var(--gray-700);">Identity</span>
      ${progressBarTwig({ variant: 'circular', value: 66, color: 'info', size: 'medium', showLabel: true, label: 'Step 2: Documents in progress' })}
      <span style="font-size: var(--font-size-1); color: var(--gray-700);">Documents</span>
      ${progressBarTwig({ variant: 'circular', value: 0, color: 'neutral', size: 'medium', showLabel: false, label: 'Step 3: Payment pending' })}
      <span style="font-size: var(--font-size-1); color: var(--gray-500);">Payment</span>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Multi-step process tracker using circular progress indicators to show completed (100% green), in-progress (66% blue), and pending (0% gray) steps.',
      },
    },
  },
};

export const ProfileCompletion = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center; padding: var(--size-6); background: var(--gray-50); border-radius: var(--radius-3);">
      ${progressBarTwig({ variant: 'circular', value: 33, color: 'warning', size: 'large', showLabel: true, label: 'Agent profile 33% complete' })}
      <div>
        <p style="margin: 0 0 var(--size-1); font-size: var(--font-size-2); font-weight: var(--font-weight-600);">Complete your profile</p>
        <p style="margin: 0; font-size: var(--font-size-1); color: var(--gray-600);">Add certifications and experience to improve visibility</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Profile completion indicator with circular progress showing 33% completion in warning color to encourage action.',
      },
    },
  },
};

export const CriticalState = {
  render: () => `
    <div style="display: grid; gap: var(--size-4); max-width: 600px;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--size-2);">
          <p style="margin: 0; font-size: var(--font-size-1); font-weight: var(--font-weight-600); color: var(--danger);">Storage almost full</p>
          <span style="font-size: var(--font-size-0); color: var(--danger);">92% used</span>
        </div>
        ${progressBarTwig({ variant: 'linear', value: 92, color: 'danger', size: 'large', showLabel: false, label: 'Storage usage: 92% - upgrade needed' })}
        <p style="margin: var(--size-1) 0 0; font-size: var(--font-size-0); color: var(--gray-600);">4.6 GB of 5 GB used. Upgrade to continue uploading.</p>
      </div>
      <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--size-2);">
          <p style="margin: 0; font-size: var(--font-size-1); font-weight: var(--font-weight-600); color: var(--success);">Upload complete</p>
          <span style="font-size: var(--font-size-0); color: var(--success);">100%</span>
        </div>
        ${progressBarTwig({ variant: 'linear', value: 100, color: 'success', size: 'large', showLabel: false, label: 'Upload complete' })}
        <p style="margin: var(--size-1) 0 0; font-size: var(--font-size-0); color: var(--gray-600);">All documents successfully uploaded</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Critical states using danger (92% storage full) and success (100% upload complete) colors with contextual messaging.',
      },
    },
  },
};
