import markup from './toast.twig';
import data from './toast.yml';

export default {
  title: 'Components/Toast',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Temporary notification message that appears at screen edge to provide feedback on user actions. Auto-dismisses after duration or can be manually closed.',
      },
    },
  },
  render: (args) => markup(args),
  argTypes: {
    // Content
    message: {
      control: 'text',
      description: 'Notification message text',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },

    // Appearance
    type: {
      control: 'select',
      options: ['success', 'error', 'warning', 'info'],
      description: 'Semantic type affecting color scheme',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'info' },
      },
    },

    position: {
      control: 'select',
      options: ['bottom-right', 'bottom-left', 'top-right', 'top-left'],
      description: 'Screen position for toast appearance',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'bottom-right' },
      },
    },

    // Behavior
    dismissible: {
      control: 'boolean',
      description: 'Show close button for manual dismissal',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: true },
      },
    },

    duration: {
      control: { type: 'number', min: 1000, max: 10000, step: 500 },
      description: 'Auto-dismiss duration in milliseconds',
      table: {
        category: 'Behavior',
        type: { summary: 'number' },
        defaultValue: { summary: 4000 },
      },
    },
  },
};

// 1. Default story - Dynamic link to show toast
export const Default = {
  args: data,
  render: (args) => {
    return `
      <div style="padding: var(--size-4);">
        <button 
          onclick="window.Toast && window.Toast.create({ message: '${args.message}', type: '${args.type}', duration: ${args.duration} })"
          style="padding: var(--size-3) var(--size-5); background: var(--primary); color: var(--white); border: none; border-radius: var(--radius-2); cursor: pointer; font-size: var(--font-size-1); font-weight: var(--font-weight-600);"
          onmouseover="this.style.opacity='0.9'"
          onmouseout="this.style.opacity='1'"
        >
          Show Toast
        </button>
      </div>
    `;
  },
};

// 2. Color Variants - Without Dismiss (Static Design)
export const ColorVariantsWithoutDismiss = {
  name: 'Color Variants (Without Dismiss)',
  render: () => {
    const types = [
      { type: 'success', message: 'Property added to favorites successfully!' },
      { type: 'error', message: 'Unable to save search. Please try again.' },
      { type: 'warning', message: 'Your session will expire in 5 minutes.' },
      { type: 'info', message: 'New properties match your search criteria.' },
    ];

    return `
      <div style="padding: var(--size-4); display: flex; flex-direction: column; gap: var(--size-4);">
        ${types
          .map(
            (t) => `
          <div 
            class="ps-toast ps-toast--show${t.type !== 'info' ? ` ps-toast--${t.type}` : ''}"
            role="status" 
            aria-live="polite" 
            aria-atomic="true"
            data-static="true"
          >
            <div class="ps-toast__content">
              ${t.message}
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// 3. Color Variants - With Dismiss (Static Design)
export const ColorVariantsWithDismiss = {
  name: 'Color Variants (With Dismiss)',
  render: () => {
    const types = [
      { type: 'success', message: 'Property added to favorites successfully!' },
      { type: 'error', message: 'Unable to save search. Please try again.' },
      { type: 'warning', message: 'Your session will expire in 5 minutes.' },
      { type: 'info', message: 'New properties match your search criteria.' },
    ];

    return `
      <div style="padding: var(--size-4); display: flex; flex-direction: column; gap: var(--size-4);">
        ${types
          .map(
            (t) => `
          <div 
            class="ps-toast ps-toast--show${t.type !== 'info' ? ` ps-toast--${t.type}` : ''}"
            role="status" 
            aria-live="polite" 
            aria-atomic="true"
            data-static="true"
          >
            <div class="ps-toast__content">
              ${t.message}
            </div>
            <button 
              type="button" 
              class="ps-toast__close" 
              aria-label="Dismiss notification" 
              data-icon="close"
            ></button>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// 4. All Positions (Static Design)
export const AllPositions = {
  name: 'All Positions',
  render: () => {
    const positions = [
      { position: 'top-left', label: 'Top Left Position' },
      { position: 'top-right', label: 'Top Right Position' },
      { position: 'bottom-left', label: 'Bottom Left Position' },
      { position: 'bottom-right', label: 'Bottom Right Position' },
    ];

    return `
      <div style="position: relative; height: 600px; border: 2px dashed var(--border-default); border-radius: var(--radius-2); overflow: hidden;">
        ${positions
          .map(
            (pos) => `
          <div class="ps-toast-container ps-toast-container--${pos.position}" style="position: absolute;">
            <div 
              class="ps-toast ps-toast--show"
              role="status" 
              aria-live="polite" 
              aria-atomic="true"
              data-static="true"
            >
              <div class="ps-toast__content">
                ${pos.label}
              </div>
              <button 
                type="button" 
                class="ps-toast__close" 
                aria-label="Dismiss notification" 
                data-icon="close"
              ></button>
            </div>
          </div>
        `
          )
          .join('')}
      </div>
    `;
  },
};

// 5. Interactive Demo - Dynamic buttons for types and positions
export const InteractiveDemo = {
  name: 'Interactive Demo',
  render: () => {
    const buttonStyle = `
      padding: var(--size-2) var(--size-4);
      border: none;
      border-radius: var(--radius-2);
      cursor: pointer;
      font-size: var(--font-size-1);
      font-weight: var(--font-weight-600);
      color: var(--white);
      transition: opacity var(--duration-fast) var(--ease-out-quad);
    `;

    const types = [
      { type: 'success', label: 'Success', color: 'var(--success)' },
      { type: 'error', label: 'Error', color: 'var(--danger)' },
      { type: 'warning', label: 'Warning', color: 'var(--warning)' },
      { type: 'info', label: 'Info', color: 'var(--info)' },
    ];

    const positions = [
      { value: 'top-left', label: 'Top Left' },
      { value: 'top-right', label: 'Top Right' },
      { value: 'bottom-left', label: 'Bottom Left' },
      { value: 'bottom-right', label: 'Bottom Right' },
    ];

    return `
      <div style="padding: var(--size-4);">
        <div style="margin-bottom: var(--size-6);">
          <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-3); font-weight: var(--font-weight-700);">
            Toast Types
          </h3>
          <div style="display: flex; gap: var(--size-3); flex-wrap: wrap;">
            ${types
              .map(
                (t) => `
              <button 
                onclick="window.Toast && window.Toast.create({ message: '${t.label} notification message', type: '${t.type}', duration: 4000 })"
                style="${buttonStyle} background: ${t.color};"
                onmouseover="this.style.opacity='0.9'"
                onmouseout="this.style.opacity='1'"
              >
                ${t.label}
              </button>
            `
              )
              .join('')}
          </div>
        </div>

        <div>
          <h3 style="margin-bottom: var(--size-3); font-size: var(--font-size-3); font-weight: var(--font-weight-700);">
            Toast Positions
          </h3>
          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--size-3); max-width: 400px;">
            ${positions
              .map(
                (pos) => `
              <button 
                onclick="window.Toast && window.Toast.create({ message: 'Toast from ${pos.label}', type: 'info', position: '${pos.value}', duration: 5000 })"
                style="${buttonStyle} background: var(--primary);"
                onmouseover="this.style.opacity='0.9'"
                onmouseout="this.style.opacity='1'"
              >
                ${pos.label}
              </button>
            `
              )
              .join('')}
          </div>
        </div>

        <p style="margin-top: var(--size-5); color: var(--text-secondary); font-size: var(--font-size-0);">
          Toasts auto-dismiss after 4-5 seconds or click the close button.
        </p>
      </div>
    `;
  },
};
