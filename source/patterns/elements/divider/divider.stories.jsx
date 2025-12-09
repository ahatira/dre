import dividerTwig from './divider.twig';
import data from './divider.yml';

export default {
  title: 'Elements/Divider',
  tags: ['autodocs'],
  render: (args) => dividerTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Visual separator with horizontal/vertical orientations, component-scoped CSS variables, and optional centered content.',
      },
    },
  },
  argTypes: {
    orientation: {
      control: 'select',
      options: ['horizontal', 'vertical'],
      description: 'Orientation of the divider.',
      table: {
        category: 'Layout',
        defaultValue: { summary: 'horizontal' },
      },
    },
    style: {
      control: 'select',
      options: ['solid', 'dashed', 'dotted'],
      description: 'Line style (solid, dashed, or dotted).',
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'solid' },
      },
    },
    thickness: {
      control: 'select',
      options: ['thin', 'medium', 'thick'],
      description: 'Line thickness (thin: 1px, medium: 2px, thick: 4px).',
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'medium' },
      },
    },
    color: {
      control: 'select',
      options: ['neutral', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Semantic color of the divider.',
      table: {
        category: 'Appearance',
        defaultValue: { summary: 'neutral' },
      },
    },
    spacing: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Spacing around the divider (sm: 8px, md: 16px, lg: 24px).',
      table: {
        category: 'Layout',
        defaultValue: { summary: 'md' },
      },
    },
    text: {
      control: 'text',
      description: 'Optional centered text content.',
      table: {
        category: 'Content',
        defaultValue: { summary: '""' },
      },
    },
    icon: {
      control: 'select',
      options: ['', 'check', 'star', 'arrow-right', 'plus', 'info', 'warning', 'heart'],
      description: 'Optional centered icon name (without "icon-" prefix).',
      table: {
        category: 'Content',
        defaultValue: { summary: '""' },
      },
    },
  },
};

export const Default = {
  args: { ...data },
};

export const Styles = {
  render: () => `
    <div style="max-width: 600px;">
      <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Solid (default)</p>
      ${dividerTwig({ style: 'solid', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Dashed</p>
      ${dividerTwig({ style: 'dashed', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Dotted</p>
      ${dividerTwig({ style: 'dotted', spacing: 'sm' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Divider line styles: solid (default), dashed, dotted.',
      },
    },
  },
};

export const Thickness = {
  render: () => `
    <div style="max-width: 600px;">
      <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Thin (1px)</p>
      ${dividerTwig({ thickness: 'thin', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Medium (2px, default)</p>
      ${dividerTwig({ thickness: 'medium', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Thick (4px)</p>
      ${dividerTwig({ thickness: 'thick', spacing: 'sm' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Divider line thickness variations: thin (1px), medium (2px, default), thick (4px).',
      },
    },
  },
};

export const SemanticColors = {
  render: () => `
    <div style="max-width: 600px;">
      <p style="margin: 0 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Neutral (default)</p>
      ${dividerTwig({ color: 'neutral', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Primary (brand green)</p>
      ${dividerTwig({ color: 'primary', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Secondary (brand pink)</p>
      ${dividerTwig({ color: 'secondary', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Success (teal)</p>
      ${dividerTwig({ color: 'success', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Warning (yellow)</p>
      ${dividerTwig({ color: 'warning', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Danger (red)</p>
      ${dividerTwig({ color: 'danger', spacing: 'sm' })}
      
      <p style="margin: var(--size-4) 0 var(--size-2) 0; font-size: var(--font-size-0); color: var(--text-secondary);">Info (blue)</p>
      ${dividerTwig({ color: 'info', spacing: 'sm' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'All semantic color variants using design tokens from brand.css.',
      },
    },
  },
};

export const Spacing = {
  render: () => `
    <div style="max-width: 600px;">
      <div style="background: var(--gray-50); padding: var(--size-2); border-radius: var(--radius-1);">
        <p style="margin: 0; font-size: var(--font-size-0);">Content before</p>
        ${dividerTwig({ spacing: 'sm' })}
        <p style="margin: 0; font-size: var(--font-size-0); color: var(--text-secondary);">Small spacing (8px)</p>
      </div>
      
      <div style="background: var(--gray-50); padding: var(--size-2); border-radius: var(--radius-1); margin-top: var(--size-6);">
        <p style="margin: 0; font-size: var(--font-size-0);">Content before</p>
        ${dividerTwig({ spacing: 'md' })}
        <p style="margin: 0; font-size: var(--font-size-0); color: var(--text-secondary);">Medium spacing (16px, default)</p>
      </div>
      
      <div style="background: var(--gray-50); padding: var(--size-2); border-radius: var(--radius-1); margin-top: var(--size-6);">
        <p style="margin: 0; font-size: var(--font-size-0);">Content before</p>
        ${dividerTwig({ spacing: 'lg' })}
        <p style="margin: 0; font-size: var(--font-size-0); color: var(--text-secondary);">Large spacing (24px)</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story: 'Spacing variants: sm (8px), md (16px, default), lg (24px).',
      },
    },
  },
};

export const WithText = {
  render: () => `
    <div style="max-width: 600px;">
      ${dividerTwig({ text: 'ou', spacing: 'md' })}
      ${dividerTwig({ text: 'Section suivante', spacing: 'md', color: 'primary' })}
      ${dividerTwig({ text: 'Séparation', spacing: 'md', style: 'dashed' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Dividers with centered text content. Commonly used in forms (e.g., "or" between sign-in methods) or section separators.',
      },
    },
  },
};

export const WithIcon = {
  render: () => `
    <div style="max-width: 600px;">
      ${dividerTwig({ icon: 'check', spacing: 'md' })}
      ${dividerTwig({ icon: 'star', spacing: 'md', color: 'warning' })}
      ${dividerTwig({ icon: 'heart', spacing: 'md', color: 'danger' })}
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Dividers with centered icon. Icon names are without "icon-" prefix (automatically added by icon system).',
      },
    },
  },
};

export const Vertical = {
  render: () => `
    <div style="display: flex; align-items: center; gap: var(--size-3); height: 48px;">
      <span style="font-size: var(--font-size-1);">Gauche</span>
      ${dividerTwig({ orientation: 'vertical' })}
      <span style="font-size: var(--font-size-1);">Centre</span>
      ${dividerTwig({ orientation: 'vertical' })}
      <span style="font-size: var(--font-size-1);">Droite</span>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Vertical orientation for inline separators (e.g., toolbar buttons, breadcrumb navigation).',
      },
    },
  },
};
