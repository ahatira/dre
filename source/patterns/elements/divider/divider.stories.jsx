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
          'Visual separator for content sections with horizontal and vertical orientations. Supports styles, thickness, semantic colors, and centered text or icon using design tokens.'
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
        defaultValue: { summary: 'horizontal' }
      },
    },
    style: {
      control: 'select',
      options: ['solid', 'dashed', 'dotted'],
      description: 'Line style (solid, dashed, or dotted).',
      table: { 
        category: 'Appearance',
        defaultValue: { summary: 'solid' }
      },
    },
    thickness: {
      control: 'select',
      options: ['thin', 'medium', 'thick'],
      description: 'Line thickness (thin: 1px, medium: 2px, thick: 4px).',
      table: { 
        category: 'Appearance',
        defaultValue: { summary: 'medium' }
      },
    },
    color: {
      control: 'select',
      options: ['neutral', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Semantic color of the divider.',
      table: { 
        category: 'Appearance',
        defaultValue: { summary: 'neutral' }
      },
    },
    spacing: {
      control: 'select',
      options: ['sm', 'md', 'lg'],
      description: 'Spacing around the divider (sm: 8px, md: 16px, lg: 24px).',
      table: { 
        category: 'Layout',
        defaultValue: { summary: 'md' }
      },
    },
    text: {
      control: 'text',
      description: 'Optional centered text content.',
      table: { 
        category: 'Content',
        defaultValue: { summary: '""' }
      },
    },
    icon: {
      control: 'select',
      options: ['', 'check', 'star', 'arrow-right', 'plus', 'info', 'warning', 'heart'],
      description: 'Optional centered icon name (without "icon-" prefix).',
      table: { 
        category: 'Content',
        defaultValue: { summary: '""' }
      },
    },
  },
};

export const Default = {
  args: { ...data },
};

export const AllStyles = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem;">
      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Horizontal Styles</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Solid (default)</small>
            ${dividerTwig({ style: 'solid', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Dashed</small>
            ${dividerTwig({ style: 'dashed', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Dotted</small>
            ${dividerTwig({ style: 'dotted', spacing: 'sm' })}
          </div>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Thickness</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Thin (1px)</small>
            ${dividerTwig({ thickness: 'thin', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Medium (2px, default)</small>
            ${dividerTwig({ thickness: 'medium', spacing: 'sm' })}
          </div>
          <div>
            <small style="color: #666; display: block; margin-bottom: 0.5rem;">Thick (4px)</small>
            ${dividerTwig({ thickness: 'thick', spacing: 'sm' })}
          </div>
        </div>
      </div>
    </div>
  `,
};

export const AllColors = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Neutral (gray, default)</small>
        ${dividerTwig({ color: 'neutral', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Primary (green)</small>
        ${dividerTwig({ color: 'primary', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Secondary (purple)</small>
        ${dividerTwig({ color: 'secondary', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Success</small>
        ${dividerTwig({ color: 'success', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Warning</small>
        ${dividerTwig({ color: 'warning', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Danger</small>
        ${dividerTwig({ color: 'danger', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Info</small>
        ${dividerTwig({ color: 'info', spacing: 'sm' })}
      </div>
    </div>
  `,
};

export const AllSpacing = {
  render: () => `
    <div style="background: #f5f5f5; padding: 1rem;">
      <div style="background: white; padding: 0.5rem;">Content before</div>
      ${dividerTwig({ spacing: 'sm', color: 'primary' })}
      <div style="background: white; padding: 0.5rem;">Small spacing (8px)</div>
      ${dividerTwig({ spacing: 'md', color: 'primary' })}
      <div style="background: white; padding: 0.5rem;">Medium spacing (16px, default)</div>
      ${dividerTwig({ spacing: 'lg', color: 'primary' })}
      <div style="background: white; padding: 0.5rem;">Large spacing (24px)</div>
    </div>
  `,
};

export const WithContent = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With text</small>
        ${dividerTwig({ text: 'or', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With text + primary color</small>
        ${dividerTwig({ text: 'Section', color: 'primary', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With text + dashed</small>
        ${dividerTwig({ text: 'Or', style: 'dashed', spacing: 'sm' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With icon</small>
        ${dividerTwig({ icon: 'check', spacing: 'sm' })}
      </div>
    </div>
  `,
};

export const Vertical = {
  render: () => `
    <div style="display: flex; align-items: center; height: 100px; gap: 1rem;">
      <span>Left text</span>
      ${dividerTwig({ orientation: 'vertical', spacing: 'md' })}
      <span>Right text</span>
    </div>
  `,
};

export const VerticalMultiple = {
  render: () => `
    <div style="display: flex; align-items: center; height: 80px; gap: 0.5rem;">
      <span>Option 1</span>
      ${dividerTwig({ orientation: 'vertical', thickness: 'thin', spacing: 'sm' })}
      <span>Option 2</span>
      ${dividerTwig({ orientation: 'vertical', thickness: 'thin', spacing: 'sm' })}
      <span>Option 3</span>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 600px;">
      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Login form with text divider</h3>
        <div style="border: 1px solid #e0e0e0; padding: 1.5rem; border-radius: 4px;">
          <input type="email" placeholder="Email" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" />
          <br/><br/>
          <input type="password" placeholder="Password" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;" />
          <br/><br/>
          <button style="width: 100%; padding: 0.75rem; background: #00915A; color: white; border: none; border-radius: 4px; cursor: pointer;">Sign in</button>
          ${dividerTwig({ text: 'or', spacing: 'md' })}
          <button style="width: 100%; padding: 0.75rem; background: white; color: #333; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">Continue with Google</button>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Content sections</h3>
        <div>
          <p style="margin: 0;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
          ${dividerTwig({ spacing: 'lg', thickness: 'thin' })}
          <p style="margin: 0;">Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
          ${dividerTwig({ spacing: 'lg', thickness: 'thin' })}
          <p style="margin: 0;">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>
        </div>
      </div>

      <div>
        <h3 style="margin: 0 0 1rem 0; font-size: 14px; font-weight: 600;">Divider with accent</h3>
        <div>
          <h4 style="margin: 0 0 0.5rem 0; color: #00915A;">Important section</h4>
          <p style="margin: 0; color: #666;">Highlighted content with primary divider.</p>
          ${dividerTwig({ spacing: 'md', color: 'primary', thickness: 'thick' })}
          <h4 style="margin: 0 0 0.5rem 0;">Standard section</h4>
          <p style="margin: 0; color: #666;">Normal content with neutral divider.</p>
        </div>
      </div>
    </div>
  `,
};
