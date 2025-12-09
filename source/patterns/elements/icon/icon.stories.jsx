import iconsRegistry from '../../documentation/icons-registry.json';
import iconTwig from './icon.twig';
import data from './icon.yml';

export default {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'Reusable Twig wrapper (atom component) that renders an `<i>` element with icon rendering.\n\n' +
          'Icon.twig uses the `data-icon` CSS system to display SVG icons via pseudo-elements (::before/::after). ' +
          'Icons inherit color from parent via currentColor and scale with parent font-size.\n\n' +
          '**Note**: Icon.twig renders ONLY the `<i>` element. For icon + text content, apply `data-icon` directly to any HTML element (button, span, etc.) — ' +
          'see "Data-Icon System" story below.',
      },
    },
  },
  render: (args) => iconTwig(args),
  argTypes: {
    icon: {
      description: 'Icon name from sprite (no "icon-" prefix, e.g., "check", "arrow-right")',
      control: { type: 'select' },
      options: iconsRegistry.names,
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
      },
    },
    position: {
      description:
        'Pseudo-element position: start uses ::before (default), end uses ::after. Controls which pseudo-element renders the icon.',
      control: { type: 'inline-radio' },
      options: ['start', 'end'],
      table: {
        category: 'Appearance',
        type: { summary: 'start | end' },
        defaultValue: { summary: 'start' },
      },
    },
    ariaLabel: {
      description: 'ARIA label for icon-only buttons or decorative icon usage',
      control: 'text',
      table: {
        category: 'Accessibility',
        type: { summary: 'string' },
      },
    },
    ariaHidden: {
      description: 'Hide from screen readers (for purely decorative icons without meaning)',
      control: 'boolean',
      table: {
        category: 'Accessibility',
        defaultValue: { summary: false },
      },
    },
  },
};

// Default story - interactive playground
export const Default = {
  args: { ...data },
};

// Position parameter (controls ::before vs ::after pseudo-element)
export const WithPositioning = {
  render: () => `
    <div style="display: flex; gap: var(--size-8); flex-direction: column;">
      <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
        <p style="margin: 0; color: var(--info); font-size: var(--font-size-1);">
          💡 The <code>position</code> parameter controls which pseudo-element (::before or ::after) renders the icon. 
          This is useful when Icon.twig output is combined with text in a layout.
        </p>
      </div>

      <div style="background: var(--gray-50); padding: var(--size-6); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
        <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">position="start" (::before, default)</h4>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div style="display: flex; align-items: center; gap: var(--size-3); padding: var(--size-3); background: white; border-radius: var(--radius-2);">
            ${iconTwig({ icon: 'check', position: 'start' })}
            <span>Mark as complete</span>
          </div>
          <div style="display: flex; align-items: center; gap: var(--size-3); padding: var(--size-3); background: white; border-radius: var(--radius-2);">
            ${iconTwig({ icon: 'search', position: 'start' })}
            <span>Search properties</span>
          </div>
          <div style="display: flex; align-items: center; gap: var(--size-3); padding: var(--size-3); background: white; border-radius: var(--radius-2);">
            ${iconTwig({ icon: 'phone', position: 'start' })}
            <span>Contact agent</span>
          </div>
        </div>
      </div>
      
      <div style="background: var(--gray-50); padding: var(--size-6); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
        <h4 style="margin: 0 0 var(--size-4); color: var(--text-primary); font-size: var(--font-size-3); font-weight: 600;">position="end" (::after)</h4>
        <div style="display: flex; flex-direction: column; gap: var(--size-3);">
          <div style="display: flex; align-items: center; gap: var(--size-3); padding: var(--size-3); background: white; border-radius: var(--radius-2);">
            <span>Proceed to next step</span>
            ${iconTwig({ icon: 'arrow-right', position: 'end' })}
          </div>
          <div style="display: flex; align-items: center; gap: var(--size-3); padding: var(--size-3); background: white; border-radius: var(--radius-2);">
            <span>View details</span>
            ${iconTwig({ icon: 'chevron-right', position: 'end' })}
          </div>
          <div style="display: flex; align-items: center; gap: var(--size-3); padding: var(--size-3); background: white; border-radius: var(--radius-2);">
            <span>Download brochure</span>
            ${iconTwig({ icon: 'download', position: 'end' })}
          </div>
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'The `position` parameter controls which pseudo-element renders the icon: start (::before, default) or end (::after). ' +
          'This is useful for controlling icon placement when Icon.twig output is combined with other elements in a layout.',
      },
    },
  },
};

// Sizing (via parent font-size)
export const Sizing = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); flex-direction: column;">
      <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
        <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-1);">
          💡 Icons inherit <code>font-size</code> from their parent element. Set the parent's font-size to control icon dimensions.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--size-5);">
        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light); text-align: center;">
          <div style="font-size: var(--font-size-0); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <code style="font-size: var(--font-size-0); color: var(--text-secondary);">font-size-0</code>
          <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--gray-500);">12px</div>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light); text-align: center;">
          <div style="font-size: var(--font-size-1); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <code style="font-size: var(--font-size-0); color: var(--text-secondary);">font-size-1</code>
          <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--gray-500);">14px</div>
        </div>

        <div style="background: var(--primary-bg-subtle); padding: var(--size-5); border-radius: var(--radius-3); border: 2px solid var(--primary); text-align: center;">
          <div style="font-size: var(--font-size-2); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <code style="font-size: var(--font-size-0); color: var(--primary);">font-size-2 (default)</code>
          <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--gray-500);">16px</div>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light); text-align: center;">
          <div style="font-size: var(--font-size-3); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <code style="font-size: var(--font-size-0); color: var(--text-secondary);">font-size-3</code>
          <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--gray-500);">18px</div>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light); text-align: center;">
          <div style="font-size: var(--font-size-4); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <code style="font-size: var(--font-size-0); color: var(--text-secondary);">font-size-4</code>
          <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--gray-500);">20px</div>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light); text-align: center;">
          <div style="font-size: var(--font-size-6); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <code style="font-size: var(--font-size-0); color: var(--text-secondary);">font-size-6</code>
          <div style="margin-top: var(--size-2); font-size: var(--font-size-0); color: var(--gray-500);">28px</div>
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          "Icons automatically adapt to their parent's font-size. This makes them flexible and easy to scale contextually.",
      },
    },
  },
};

// Color inheritance (via parent color)
export const ColorInheritance = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); flex-direction: column;">
      <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
        <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-1);">
          🎨 Icons inherit <code>color</code> from their parent using <code>currentColor</code>. No need to set icon colors explicitly.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--size-5);">
        <div style="background: var(--primary-bg-subtle); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--primary);">
          <div style="color: var(--primary); font-size: var(--font-size-5); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <strong style="display: block; margin-bottom: var(--size-2); color: var(--primary);">Primary</strong>
          <p style="margin: 0; font-size: var(--font-size-1); color: var(--text-secondary);">Brand actions, main CTAs</p>
        </div>

        <div style="background: var(--success-bg-subtle); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--success);">
          <div style="color: var(--success); font-size: var(--font-size-5); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'check' })}
          </div>
          <strong style="display: block; margin-bottom: var(--size-2); color: var(--success);">Success</strong>
          <p style="margin: 0; font-size: var(--font-size-1); color: var(--text-secondary);">Confirmations, positive states</p>
        </div>

        <div style="background: var(--warning-bg-subtle); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--warning);">
          <div style="color: var(--warning); font-size: var(--font-size-5); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'alert' })}
          </div>
          <strong style="display: block; margin-bottom: var(--size-2); color: var(--warning);">Warning</strong>
          <p style="margin: 0; font-size: var(--font-size-1); color: var(--text-secondary);">Cautions, important notices</p>
        </div>

        <div style="background: var(--danger-bg-subtle); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--danger);">
          <div style="color: var(--danger); font-size: var(--font-size-5); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'close' })}
          </div>
          <strong style="display: block; margin-bottom: var(--size-2); color: var(--danger);">Danger</strong>
          <p style="margin: 0; font-size: var(--font-size-1); color: var(--text-secondary);">Errors, destructive actions</p>
        </div>

        <div style="background: var(--info-bg-subtle); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--info);">
          <div style="color: var(--info); font-size: var(--font-size-5); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'info' })}
          </div>
          <strong style="display: block; margin-bottom: var(--size-2); color: var(--info);">Info</strong>
          <p style="margin: 0; font-size: var(--font-size-1); color: var(--text-secondary);">Informational content, tips</p>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <div style="color: var(--gray-500); font-size: var(--font-size-5); margin-bottom: var(--size-3);">
            ${iconTwig({ icon: 'help' })}
          </div>
          <strong style="display: block; margin-bottom: var(--size-2); color: var(--gray-700);">Neutral</strong>
          <p style="margin: 0; font-size: var(--font-size-1); color: var(--text-secondary);">Secondary content, disabled states</p>
        </div>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Icons automatically inherit color from their parent element using currentColor, making them highly flexible and semantic.',
      },
    },
  },
};

// Accessibility: Icon-only with aria-label
export const IconOnly = {
  render: () => `
    <div style="display: flex; gap: var(--size-4);">
      <button aria-label="Close dialog" style="padding: var(--size-2); background: var(--gray-100); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
        ${iconTwig({ icon: 'close', ariaHidden: true })}
      </button>
      
      <button aria-label="Search properties" style="padding: var(--size-2); background: var(--gray-100); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
        ${iconTwig({ icon: 'search', ariaHidden: true })}
      </button>
      
      <button aria-label="Open menu" style="padding: var(--size-2); background: var(--gray-100); border: 1px solid var(--border-default); border-radius: var(--radius-2);">
        ${iconTwig({ icon: 'menu', ariaHidden: true })}
      </button>
    </div>
  `,
};

// Data-icon system: Direct HTML usage (no Icon.twig wrapper)
export const DataIconSystem = {
  render: () => `
    <div style="display: flex; gap: var(--size-8); flex-direction: column;">
      <div style="background: var(--primary-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--primary);">
        <p style="margin: 0; color: var(--primary); font-size: var(--font-size-1);">
          🚀 The <code>data-icon</code> CSS system can be applied directly to ANY HTML element (button, span, link, heading, etc.) 
          without using Icon.twig. Use <code>data-icon-position="end"</code> for ::after positioning.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--size-5);">
        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <h4 style="margin: 0 0 var(--size-3); color: var(--text-primary); font-size: var(--font-size-2); font-weight: 600;">Button with Icon (end position)</h4>
          <button style="
            background: var(--primary);
            color: white;
            padding: var(--size-3) var(--size-4);
            border: none;
            border-radius: var(--radius-2);
            cursor: pointer;
            font-weight: 500;
            transition: background 0.15s ease;
          " data-icon="arrow-right" data-icon-position="end" onmouseenter="this.style.background='var(--primary-hover)'" onmouseleave="this.style.background='var(--primary)'">
            Proceed to next
          </button>
          <code style="display: block; margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); padding: var(--size-2); background: white; border-radius: var(--radius-2); border: 1px solid var(--border-light);">&lt;button data-icon="arrow-right" data-icon-position="end"&gt;</code>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <h4 style="margin: 0 0 var(--size-3); color: var(--text-primary); font-size: var(--font-size-2); font-weight: 600;">Badge with Icon (start position)</h4>
          <span style="
            background: var(--success-bg-subtle);
            color: var(--success);
            padding: var(--size-2) var(--size-3);
            border-radius: var(--radius-2);
            font-weight: 500;
            display: inline-block;
          " data-icon="check" data-icon-position="start">
            Approved
          </span>
          <code style="display: block; margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); padding: var(--size-2); background: white; border-radius: var(--radius-2); border: 1px solid var(--border-light);">&lt;span data-icon="check"&gt;</code>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <h4 style="margin: 0 0 var(--size-3); color: var(--text-primary); font-size: var(--font-size-2); font-weight: 600;">Link with Icon (end position)</h4>
          <a href="#" style="
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--size-2);
            font-weight: 500;
          " data-icon="external-link" data-icon-position="end">
            View full listing
          </a>
          <code style="display: block; margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); padding: var(--size-2); background: white; border-radius: var(--radius-2); border: 1px solid var(--border-light);">&lt;a data-icon="external-link" data-icon-position="end"&gt;</code>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <h4 style="margin: 0 0 var(--size-3); color: var(--text-primary); font-size: var(--font-size-2); font-weight: 600;">Heading with Icon (start position)</h4>
          <h2 style="margin: 0; color: var(--text-primary); font-size: var(--font-size-4);" data-icon="star" data-icon-position="start">
            Featured Properties
          </h2>
          <code style="display: block; margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); padding: var(--size-2); background: white; border-radius: var(--radius-2); border: 1px solid var(--border-light);">&lt;h2 data-icon="star"&gt;</code>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <h4 style="margin: 0 0 var(--size-3); color: var(--text-primary); font-size: var(--font-size-2); font-weight: 600;">Icon-only (icon on button)</h4>
          <button style="
            width: 40px;
            height: 40px;
            padding: 0;
            background: var(--gray-100);
            border: 1px solid var(--border-default);
            border-radius: var(--radius-2);
            cursor: pointer;
            color: var(--text-primary);
            font-size: var(--font-size-3);
            display: flex;
            align-items: center;
            justify-content: center;
          " data-icon="search" aria-label="Search">
          </button>
          <code style="display: block; margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); padding: var(--size-2); background: white; border-radius: var(--radius-2); border: 1px solid var(--border-light);">&lt;button data-icon="search"&gt;&lt;/button&gt;</code>
        </div>

        <div style="background: var(--gray-50); padding: var(--size-5); border-radius: var(--radius-3); border: 1px solid var(--border-light);">
          <h4 style="margin: 0 0 var(--size-3); color: var(--text-primary); font-size: var(--font-size-2); font-weight: 600;">Icon inherits color</h4>
          <div style="display: flex; gap: var(--size-4); align-items: center;">
            <span style="color: var(--success);" data-icon="check">Verified</span>
            <span style="color: var(--warning);" data-icon="alert">Caution</span>
            <span style="color: var(--danger);" data-icon="close">Error</span>
          </div>
          <code style="display: block; margin-top: var(--size-3); font-size: var(--font-size-0); color: var(--text-secondary); padding: var(--size-2); background: white; border-radius: var(--radius-2); border: 1px solid var(--border-light);">&lt;span style="color: var(--success);" data-icon="check"&gt;</code>
        </div>
      </div>

      <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
        <strong style="color: var(--info); display: block; margin-bottom: var(--size-2);">Key Differences from Icon.twig:</strong>
        <ul style="margin: 0; padding-left: var(--size-5); color: var(--text-secondary); font-size: var(--font-size-1);">
          <li><strong>No wrapper needed</strong> — Use <code>data-icon</code> directly on your HTML element</li>
          <li><strong>Text content supported</strong> — Icon + text automatically spaced via flexbox + gap</li>
          <li><strong>Color inheritance</strong> — Icon inherits <code>currentColor</code> from parent</li>
          <li><strong>Position control</strong> — Use <code>data-icon-position="start"</code> (::before, default) or <code>"end"</code> (::after)</li>
          <li><strong>No Twig required</strong> — Use in any HTML template directly</li>
        </ul>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'The `data-icon` CSS system is a low-level primitive that can be applied directly to any HTML element. ' +
          'No Icon.twig component needed. Use `data-icon-position="end"` to position icon after text (uses ::after pseudo-element).',
      },
    },
  },
};

// Gallery - All icons organized by category
export const Gallery = {
  render: () => {
    // Map folder names to friendly category names
    const categoryLabels = {
      ad: 'Property Ads & Listings',
      blog: 'Blog & Content',
      country: 'Countries & Regions',
      generic: 'Generic UI Elements',
      metropole: 'Metropolis & Cities',
      'mobile-only': 'Mobile Specific',
      other: 'Miscellaneous',
      search: 'Search & Discovery',
      'social-media': 'Social Media',
      tools: 'Tools & Utilities',
      tutoffice: 'Tutoffice Platform',
      univers: 'Universe & Categories',
      website: 'Website & Navigation',
    };

    const categories = {};
    for (const [folder, icons] of Object.entries(iconsRegistry.categories)) {
      const label = categoryLabels[folder] || folder.charAt(0).toUpperCase() + folder.slice(1);
      categories[label] = icons;
    }

    return `
      <div style="display: flex; flex-direction: column; gap: var(--size-10);">
        <div style="background: var(--info-bg-subtle); padding: var(--size-4); border-radius: var(--radius-2); border-left: 4px solid var(--info);">
          <strong style="color: var(--info); font-size: var(--font-size-3);">📦 ${iconsRegistry.total} icons available</strong>
          <p style="margin: var(--size-2) 0 0; color: var(--text-secondary); font-size: var(--font-size-1);">
            Click any icon to copy its name to clipboard. Icons are organized by their source folder for easy discovery.
          </p>
        </div>

        ${Object.entries(categories)
          .sort(([a], [b]) => a.localeCompare(b))
          .map(
            ([categoryName, icons]) => `
            <div>
              <h3 style="margin: 0 0 var(--size-5); color: var(--text-primary); font-size: var(--font-size-4); font-weight: 600; border-bottom: 2px solid var(--border-light); padding-bottom: var(--size-2);">
                ${categoryName} <span style="color: var(--text-secondary); font-weight: 400; font-size: var(--font-size-2);">(${icons.length})</span>
              </h3>
              <div style="
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: var(--size-4);
              ">
                ${icons
                  .sort()
                  .map(
                    (iconName) => `
                  <div 
                    onclick="navigator.clipboard.writeText('${iconName}').then(() => {
                      this.style.background = 'var(--success-bg-subtle)';
                      this.style.borderColor = 'var(--success)';
                      const code = this.querySelector('code');
                      const originalText = code.textContent;
                      code.textContent = '✓ Copied!';
                      code.style.color = 'var(--success)';
                      setTimeout(() => {
                        this.style.background = 'var(--gray-50)';
                        this.style.borderColor = 'var(--border-default)';
                        code.textContent = originalText;
                        code.style.color = 'var(--text-secondary)';
                      }, 800);
                    })"
                    style="
                      display: flex;
                      flex-direction: column;
                      align-items: center;
                      gap: var(--size-3);
                      padding: var(--size-5) var(--size-3);
                      background: var(--gray-50);
                      border: 1px solid var(--border-default);
                      border-radius: var(--radius-2);
                      cursor: pointer;
                      transition: all 0.15s ease;
                    "
                    onmouseenter="this.style.background='var(--gray-100)'; this.style.borderColor='var(--primary)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';"
                    onmouseleave="this.style.background='var(--gray-50)'; this.style.borderColor='var(--border-default)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                  >
                    <div style="font-size: 32px; color: var(--text-primary); line-height: 1;">
                      ${iconTwig({ icon: iconName, ariaHidden: true })}
                    </div>
                    <code style="
                      font-size: var(--font-size-0);
                      color: var(--text-secondary);
                      text-align: center;
                      word-break: break-word;
                      font-family: 'Courier New', monospace;
                      transition: color 0.15s ease;
                    ">${iconName}</code>
                  </div>
                `
                  )
                  .join('')}
              </div>
            </div>
          `
          )
          .join('')}
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          'Complete gallery of all 140 available icons organized by their source folder. Click any icon card to copy its name to clipboard for easy use in your templates.',
      },
    },
  },
};
