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

export const AllVariants = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: var(--size-8); max-width: 1000px;">
      <!-- Styles -->
      <div>
        <strong style="display: block; margin-bottom: var(--size-3);">Styles</strong>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          <div><small style="color: var(--text-secondary);">Solid (default)</small>${dividerTwig({ spacing: 'sm' })}</div>
          <div><small style="color: var(--text-secondary);">Dashed</small>${dividerTwig({ style: 'dashed', spacing: 'sm' })}</div>
          <div><small style="color: var(--text-secondary);">Dotted</small>${dividerTwig({ style: 'dotted', spacing: 'sm' })}</div>
        </div>
      </div>
      
      <!-- Thickness -->
      <div>
        <strong style="display: block; margin-bottom: var(--size-3);">Thickness</strong>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          <div><small style="color: var(--text-secondary);">Thin (1px)</small>${dividerTwig({ thickness: 'thin', spacing: 'sm' })}</div>
          <div><small style="color: var(--text-secondary);">Medium (2px, default)</small>${dividerTwig({ spacing: 'sm' })}</div>
          <div><small style="color: var(--text-secondary);">Thick (4px)</small>${dividerTwig({ thickness: 'thick', spacing: 'sm' })}</div>
        </div>
      </div>
      
      <!-- Semantic Colors -->
      <div style="grid-column: 1 / -1;">
        <strong style="display: block; margin-bottom: var(--size-3);">Semantic Colors</strong>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--size-3);">
          ${dividerTwig({ color: 'neutral', spacing: 'sm' })}
          ${dividerTwig({ color: 'primary', spacing: 'sm' })}
          ${dividerTwig({ color: 'secondary', spacing: 'sm' })}
          ${dividerTwig({ color: 'success', spacing: 'sm' })}
          ${dividerTwig({ color: 'warning', spacing: 'sm' })}
          ${dividerTwig({ color: 'danger', spacing: 'sm' })}
          ${dividerTwig({ color: 'info', spacing: 'sm' })}
        </div>
      </div>
      
      <!-- With Content -->
      <div style="grid-column: 1 / -1;">
        <strong style="display: block; margin-bottom: var(--size-3);">With Content</strong>
        <div style="display: flex; flex-direction: column; gap: var(--size-4);">
          ${dividerTwig({ text: 'or', spacing: 'sm' })}
          ${dividerTwig({ text: 'Section', color: 'primary', spacing: 'sm' })}
          ${dividerTwig({ icon: 'check', spacing: 'sm' })}
        </div>
      </div>
    </div>
  `,
};

export const Vertical = {
  render: () => `
    <div style="display: flex; align-items: center; height: 80px;">
      <span>Left text</span>
      ${dividerTwig({ orientation: 'vertical' })}
      <span>Right text</span>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8); max-width: 600px;">
      <!-- Login form with "or" divider -->
      <div style="border: 1px solid var(--border-default); padding: var(--size-6); border-radius: var(--radius-2);">
        <input type="email" placeholder="Email" style="width: 100%; padding: var(--size-2); border: 1px solid var(--border-default); border-radius: var(--radius-1); margin-bottom: var(--size-3);" />
        <input type="password" placeholder="Password" style="width: 100%; padding: var(--size-2); border: 1px solid var(--border-default); border-radius: var(--radius-1); margin-bottom: var(--size-3);" />
        <button style="width: 100%; padding: var(--size-3); background: var(--primary); color: var(--primary-text); border: none; border-radius: var(--radius-1); cursor: pointer; margin-bottom: var(--size-4);">Sign in</button>
        ${dividerTwig({ text: 'or' })}
        <button style="width: 100%; padding: var(--size-3); background: white; color: var(--text-primary); border: 1px solid var(--border-default); border-radius: var(--radius-1); cursor: pointer; margin-top: var(--size-4);">Continue with Google</button>
      </div>
      
      <!-- Content sections -->
      <div>
        <p style="margin: 0;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        ${dividerTwig({ spacing: 'lg', thickness: 'thin' })}
        <p style="margin: 0;">Ut enim ad minim veniam, quis nostrud exercitation.</p>
      </div>
      
      <!-- Vertical in toolbar -->
      <div style="display: flex; align-items: center; height: 40px; gap: var(--size-2);">
        <button style="padding: var(--size-2) var(--size-3); background: var(--bg-page); border: 1px solid var(--border-default); border-radius: var(--radius-1); cursor: pointer;">Edit</button>
        ${dividerTwig({ orientation: 'vertical', thickness: 'thin', spacing: 'sm' })}
        <button style="padding: var(--size-2) var(--size-3); background: var(--bg-page); border: 1px solid var(--border-default); border-radius: var(--radius-1); cursor: pointer;">Delete</button>
        ${dividerTwig({ orientation: 'vertical', thickness: 'thin', spacing: 'sm' })}
        <button style="padding: var(--size-2) var(--size-3); background: var(--bg-page); border: 1px solid var(--border-default); border-radius: var(--radius-1); cursor: pointer;">Share</button>
      </div>
    </div>
  `,
};
