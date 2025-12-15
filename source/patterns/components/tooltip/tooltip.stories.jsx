import iconsRegistry from '../../documentation/icons-registry.json';
import tooltipTwig from './tooltip.twig';
import tooltipData from './tooltip.yml';
import './tooltip.js';

export default {
  title: 'Components/Tooltip',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Utility-based tooltip system. Apply `data-ps-tooltip` to ANY element - JavaScript auto-generates bubble on hover/focus. Optional icon auto-injection via `data-ps-tooltip-icon`.',
      },
    },
  },
  render: (args) => tooltipTwig(args),
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Tooltip bubble content',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    content: {
      control: 'text',
      description: 'Element text content',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },

    // Display
    position: {
      control: { type: 'select' },
      options: ['top', 'bottom', 'left', 'right'],
      description: 'Bubble position',
      table: {
        category: 'Display',
        type: { summary: 'string' },
        defaultValue: { summary: 'top' },
      },
    },
    show_icon: {
      control: 'boolean',
      description: 'Auto-inject icon',
      table: {
        category: 'Display',
        type: { summary: 'boolean' },
        defaultValue: { summary: false },
      },
    },
    icon: {
      control: { type: 'select' },
      options: iconsRegistry.names,
      description: 'Icon name (if show_icon=true)',
      table: {
        category: 'Display',
        type: { summary: 'string' },
        defaultValue: { summary: 'info' },
      },
    },

    // Technical
    element: {
      control: { type: 'select' },
      options: ['span', 'button', 'a', 'h3'],
      description: 'HTML element type',
      table: {
        category: 'Technical',
        type: { summary: 'string' },
        defaultValue: { summary: 'span' },
      },
    },
  },
};

/**
 * Default - interactive demo with controls.
 */
export const Default = {
  args: tooltipData,
};

/**
 * Real usage examples on existing elements.
 */
export const RealUsage = {
  name: 'Usage: On Existing Elements',
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-6); padding: var(--size-6);">
      <!-- Simple text with tooltip (no icon) -->
      <p>
        Property located in 
        <span data-ps-tooltip data-ps-tooltip-content="Premium neighborhood with schools nearby" style="text-decoration: underline dotted; cursor: help;">
          downtown area
        </span>
      </p>

      <!-- Heading with info icon -->
      <h3 data-ps-tooltip data-ps-tooltip-content="Contact agent for detailed floor plans" data-ps-tooltip-icon="info" style="display: inline-block;">
        Floor Plans Available
      </h3>

      <!-- Button with help icon -->
      <button 
        class="ps-button ps-button--primary" 
        data-ps-tooltip 
        data-ps-tooltip-content="Schedule a private viewing with our agent" 
        data-ps-tooltip-icon="help"
        data-ps-tooltip-position="right"
      >
        Book Tour
      </button>

      <!-- Link with custom icon -->
      <a 
        href="#" 
        data-ps-tooltip 
        data-ps-tooltip-content="View all 24 photos in gallery" 
        data-ps-tooltip-icon="image"
        data-ps-tooltip-position="bottom"
      >
        View Photos
      </a>
    </div>
  `,
};

/**
 * All four positions demonstrated.
 */
export const Positions = {
  name: 'Showcase: All Positions',
  render: () => {
    const positions = ['top', 'bottom', 'left', 'right'];

    return `
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--size-12); padding: var(--size-12); place-items: center;">
        ${positions
          .map(
            (pos) => `
          <span 
            data-ps-tooltip 
            data-ps-tooltip-content="Tooltip on ${pos}" 
            data-ps-tooltip-position="${pos}"
            data-ps-tooltip-icon="info"
            style="padding: var(--size-3); border: 1px solid var(--gray-300); border-radius: var(--radius-2);"
          >
            ${pos.charAt(0).toUpperCase() + pos.slice(1)}
          </span>
        `
          )
          .join('')}
      </div>
    `;
  },
};
