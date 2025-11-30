import eyebrowTwig from './eyebrow.twig';
import data from './eyebrow.yml';

const settings = {
  title: 'Elements/Eyebrow',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          '**Key Features**\n\n' +
          '- **Contextual label**: Short text placed above a heading to provide category or context\n' +
          '- **5 semantic variants**: Primary, secondary, accent, neutral (default), muted\n' +
          '- **2 size options**: Small (12px) and medium (14px, default)\n' +
          '- **Text transformations**: Uppercase (default) and bold options\n' +
          '- **Optional decorations**: Line, dot, or icon can be added before text\n' +
          '- **Tokenized design**: All colors, typography, and spacing use design tokens\n\n' +
          '**Usage Guidelines**\n\n' +
          '- Place eyebrows directly above headings to add context (e.g., "News", "Blog", "Case Study")\n' +
          '- Use uppercase for maximum visibility and recognition\n' +
          '- Use muted variant with "DATE" text for date labels in cards\n' +
          '- Keep text short (1-3 words) for maximum impact\n' +
          '- Use primary/secondary variants to match section importance\n' +
          '- Use decorations (line, dot, icon) sparingly to avoid visual clutter\n' +
          '- Maintain consistent variant usage across similar content types\n\n' +
          '**Accessibility**\n\n' +
          '- Uses semantic `<span>` element (not a heading) to avoid disrupting document outline\n' +
          '- Decorative elements (line, dot, icon) marked with `aria-hidden="true"`\n' +
          '- Color contrast meets WCAG AA standards via design tokens\n' +
          '- Placed before heading in DOM order for logical screen reader flow\n' +
          '- Non-interactive element (no focus required)\n\n' +
          '**Design Tokens**\n\n' +
          '- **Colors**: `--brand-primary`, `--brand-secondary`, `--accent-gold`, `--ps-color-neutral-600`, `--ps-color-neutral-500` (muted)\n' +
          '- **Typography**: `--font-size-00` (12px/small), `--font-size-0` (14px/medium), `--font-weight-regular`, `--font-weight-semibold` (bold), `--line-height-1`\n' +
          '- **Spacing**: `--size-2` (8px gap for decorations), `--size-3` (12px margin-bottom)\n\n' +
          '**Do Not**\n\n' +
          '- Don\'t use eyebrows as headings—they provide context, not structure\n' +
          '- Don\'t use long text (more than 3 words)—it diminishes impact\n' +
          '- Don\'t hardcode colors or sizes—always use design tokens\n' +
          '- Don\'t combine multiple decorations (line + dot + icon)—choose one or none\n' +
          '- Don\'t use eyebrows without an associated heading below\n' +
          '- Don\'t place eyebrows after headings—they must come first',
      },
    },
  },
  argTypes: {
    text: {
      description: 'Text content displayed in the eyebrow.',
      control: { type: 'text' },
      table: { 
        category: 'Content',
        type: { summary: 'string' }, 
        defaultValue: { summary: '""' } 
      },
    },
    variant: {
      description: 'Semantic color variant.',
      control: { type: 'select' },
      options: ['primary', 'secondary', 'accent', 'neutral', 'muted'],
      table: {
        category: 'Appearance',
        type: { summary: 'primary | secondary | accent | neutral | muted' },
        defaultValue: { summary: 'neutral' },
      },
    },
    size: {
      description: 'Text size (small: 12px, medium: 14px).',
      control: { type: 'select' },
      options: ['small', 'medium'],
      table: { 
        category: 'Appearance',
        type: { summary: 'small | medium' }, 
        defaultValue: { summary: 'medium' } 
      },
    },
    uppercase: {
      description: 'Transform text to uppercase.',
      control: { type: 'boolean' },
      table: { 
        category: 'Appearance',
        type: { summary: 'boolean' }, 
        defaultValue: { summary: true } 
      },
    },
    bold: {
      description: 'Apply bold font weight.',
      control: { type: 'boolean' },
      table: { 
        category: 'Appearance',
        type: { summary: 'boolean' }, 
        defaultValue: { summary: false } 
      },
    },
    withLine: {
      description: 'Add decorative horizontal line before text.',
      control: { type: 'boolean' },
      table: { 
        category: 'Appearance',
        type: { summary: 'boolean' }, 
        defaultValue: { summary: false } 
      },
    },
    withDot: {
      description: 'Add decorative dot before text.',
      control: { type: 'boolean' },
      table: { 
        category: 'Appearance',
        type: { summary: 'boolean' }, 
        defaultValue: { summary: false } 
      },
    },
    icon: {
      description: 'Optional icon name (without "icon-" prefix).',
      control: { type: 'select' },
      options: ['', 'check', 'medal', 'star', 'info', 'arrow-right', 'heart', 'document'],
      table: { 
        category: 'Content',
        type: { summary: 'string' }, 
        defaultValue: { summary: '""' } 
      },
    },
  },
};

export const Default = {
  render: (args) => eyebrowTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Primary</small>
        ${eyebrowTwig({ text: 'News', variant: 'primary' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Secondary</small>
        ${eyebrowTwig({ text: 'Article', variant: 'secondary' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Accent</small>
        ${eyebrowTwig({ text: 'Case Study', variant: 'accent' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Neutral (default)</small>
        ${eyebrowTwig({ text: 'Information', variant: 'neutral' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Muted (subtle, like date labels)</small>
        ${eyebrowTwig({ text: 'DATE', variant: 'muted', size: 'small' })}
      </div>
    </div>
  `,
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Small (12px)</small>
        ${eyebrowTwig({ text: 'SMALL TEXT', variant: 'primary', size: 'small' })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Medium (14px, default)</small>
        ${eyebrowTwig({ text: 'MEDIUM TEXT', variant: 'primary', size: 'medium' })}
      </div>
    </div>
  `,
};

export const AllStyles = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Uppercase (default)</small>
        ${eyebrowTwig({ text: 'Uppercase text', variant: 'primary', uppercase: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Lowercase</small>
        ${eyebrowTwig({ text: 'Lowercase text', variant: 'primary', uppercase: false })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">Bold</small>
        ${eyebrowTwig({ text: 'Bold text', variant: 'accent', bold: true })}
      </div>
    </div>
  `,
};

export const WithDecorations = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With line</small>
        ${eyebrowTwig({ text: 'With line decoration', variant: 'neutral', withLine: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With dot</small>
        ${eyebrowTwig({ text: 'With dot decoration', variant: 'secondary', withDot: true })}
      </div>
      <div>
        <small style="color: #666; display: block; margin-bottom: 0.5rem;">With icon</small>
        ${eyebrowTwig({ text: 'With icon', variant: 'primary', icon: 'check' })}
      </div>
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 32px; padding: 20px;">
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Page hero</h4>
        ${eyebrowTwig({ text: 'News', variant: 'primary', uppercase: true })}
        <h2 style="margin-top: 8px; font-size: 32px;">Main Heading Title</h2>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">News card (with DATE label)</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 400px;">
          <div style="margin-bottom: 12px;">
            ${eyebrowTwig({ text: 'DATE', variant: 'muted', size: 'small', uppercase: true })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 20px;">News title</h3>
          <p style="margin: 0; color: #666;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut nulla convallis...</p>
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Study card (with DATE label)</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 400px;">
          <div style="margin-bottom: 12px;">
            ${eyebrowTwig({ text: 'DATE', variant: 'muted', size: 'small', uppercase: true })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 20px;">Study title</h3>
          <p style="margin: 0; color: #666;">Lorem ipsum dolor sit amet consectetur. Enim fames hendrerit amet nibh tempus sit nibh facilisis...</p>
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Publication card (with DATE label)</h4>
        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; max-width: 400px;">
          <div style="margin-bottom: 8px;">
            ${eyebrowTwig({ text: 'DATE', variant: 'muted', size: 'small', uppercase: true })}
          </div>
          <h3 style="margin: 0 0 8px 0; font-size: 20px;">Publication title</h3>
          <p style="margin: 0; color: #666;">Lorem ipsum dolor sit amet consectetur. Nunc sit a quis...</p>
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Blog section with dot</h4>
        ${eyebrowTwig({ text: 'Blog', variant: 'secondary', withDot: true })}
        <h2 style="margin-top: 8px; font-size: 28px;">Latest Articles</h2>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Section with decorative line</h4>
        ${eyebrowTwig({ text: 'Our Services', variant: 'neutral', withLine: true, size: 'small' })}
        <h2 style="margin-top: 8px; font-size: 28px;">What We Offer</h2>
      </div>
      <div>
        <h4 style="margin-bottom: 8px; font-size: 14px; color: #666;">Category label with icon</h4>
        ${eyebrowTwig({ text: 'Featured', variant: 'primary', icon: 'medal', bold: true })}
        <h3 style="margin-top: 8px; font-size: 24px;">Premium Content</h3>
      </div>
    </div>
  `,
};

export default settings;
