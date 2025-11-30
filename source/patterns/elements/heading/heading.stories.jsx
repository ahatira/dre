import component from './heading.twig';
import data from './heading.yml';
import './heading.css';

export default {
  title: 'Elements/Heading',
  render: (args) => component(args),
  args: data,
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          '**Key Features**\n\n' +
          '- **Semantic HTML**: Uses native heading elements (h1–h6) for proper document structure\n' +
          '- **6 hierarchy levels**: h1 (48px) to h6 (16px) with consistent typographic scale\n' +
          '- **7 semantic colors**: Default, primary, secondary, success, warning, danger, info\n' +
          '- **4 font weights**: Light (300), regular (400), bold (700, default), extra (800)\n' +
          '- **3 text alignments**: Left (default), center, right\n' +
          '- **Optional icons**: Supports left or right icon positioning\n' +
          '- **Tokenized design**: All typography, colors, and spacing use design tokens\n' +
          '- **Minimal markup**: Default h1 requires no modifier classes\n\n' +
          '**Usage Guidelines**\n\n' +
          '- Use h1 once per page for the main page title\n' +
          '- Maintain logical heading hierarchy (don\'t skip levels: h1 → h3)\n' +
          '- Use semantic levels (h1–h6) for document structure, not visual styling\n' +
          '- Apply color variants to emphasize importance (primary for key sections, warning for alerts)\n' +
          '- Use regular or light weight for less prominent headings\n' +
          '- Add icons sparingly to enhance meaning (location, events, actions)\n' +
          '- Use center/right alignment intentionally for specific layouts\n' +
          '- Use visuallyHidden for structural headings that don\'t need visual presence\n\n' +
          '**Accessibility**\n\n' +
          '- Headings create document outline for screen readers and keyboard navigation\n' +
          '- Logical hierarchy is critical (h1 → h2 → h3, no skipping)\n' +
          '- One h1 per page establishes main topic\n' +
          '- visuallyHidden option preserves semantic structure while hiding visually\n' +
          '- Icons marked with `aria-hidden="true"` (decorative only)\n' +
          '- Color contrast meets WCAG AA standards via design tokens\n' +
          '- Text wrapped in `<span class="ps-heading__text">` for styling flexibility\n\n' +
          '**Design Tokens**\n\n' +
          '- **Typography**: `--ps-heading-h1-size` through `--ps-heading-h6-size` (48px to 16px), `--line-height-tight|snug|normal`\n' +
          '- **Weights**: `--font-weight-300` (light), `--font-weight-400` (regular), `--ps-font-weight-bold` (700), `--font-weight-800` (extra)\n' +
          '- **Colors**: `--ps-color-text` (default), `--brand-primary`, `--brand-secondary`, `--btn-success/warning/danger/info`\n' +
          '- **Spacing**: `--ps-spacing-6` (bottom margin), `--size-2` (icon gap)\n' +
          '- **Font family**: `--font-sans` (BNPP Sans)\n\n' +
          '**Do Not**\n\n' +
          '- Don\'t skip heading levels (h1 → h3)—it breaks document structure\n' +
          '- Don\'t use multiple h1 elements on the same page\n' +
          '- Don\'t choose heading level based on visual appearance—use semantic structure first\n' +
          '- Don\'t hardcode font sizes, colors, or weights—always use design tokens\n' +
          '- Don\'t use headings for non-heading content (use styled text instead)\n' +
          '- Don\'t combine multiple icons in one heading\n' +
          '- Don\'t rely on color alone to convey meaning (use text + color)',
      },
    },
  },
  argTypes: {
    // Content
    text: {
      control: 'text',
      description: 'Heading text content',
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Heading text' },
      },
    },
    icon: {
      control: 'text',
      description: 'Optional icon class (e.g., icon-pin-map, icon-calendar)',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '' },
      },
    },
    // Structure
    level: {
      control: { type: 'select' },
      options: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
      description: 'Semantic heading level (h1: 48px, h2: 36px, h3: 28px, h4: 24px, h5: 20px, h6: 16px)',
      table: {
        category: 'Structure',
        type: { summary: 'h1 | h2 | h3 | h4 | h5 | h6' },
        defaultValue: { summary: 'h1' },
      },
    },
    // Appearance
    color: {
      control: { type: 'select' },
      options: ['default', 'primary', 'secondary', 'success', 'warning', 'danger', 'info'],
      description: 'Semantic color variant',
      table: {
        category: 'Appearance',
        type: { summary: 'default | primary | secondary | success | warning | danger | info' },
        defaultValue: { summary: 'default' },
      },
    },
    weight: {
      control: { type: 'select' },
      options: ['light', 'regular', 'bold', 'extra'],
      description: 'Font weight variant (light: 300, regular: 400, bold: 700, extra: 800)',
      table: {
        category: 'Appearance',
        type: { summary: 'light | regular | bold | extra' },
        defaultValue: { summary: 'bold' },
      },
    },
    align: {
      control: { type: 'inline-radio' },
      options: ['left', 'center', 'right'],
      description: 'Text alignment',
      table: {
        category: 'Appearance',
        type: { summary: 'left | center | right' },
        defaultValue: { summary: 'left' },
      },
    },
    iconPosition: {
      control: { type: 'inline-radio' },
      options: ['left', 'right'],
      description: 'Icon position relative to text',
      table: {
        category: 'Appearance',
        type: { summary: 'left | right' },
        defaultValue: { summary: 'left' },
      },
    },
    // Accessibility
    visuallyHidden: {
      control: 'boolean',
      description: 'Hide visually but keep for screen readers (accessibility)',
      table: {
        category: 'Accessibility',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

export const Default = {
  render: (args) => component(args),
  args: { ...data },
};

export const AllLevels = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-3);">
      ${component({ level: 'h1', text: 'H1 - Main Page Title (48px)' })}
      ${component({ level: 'h2', text: 'H2 - Section Title (36px)' })}
      ${component({ level: 'h3', text: 'H3 - Subsection Title (28px)' })}
      ${component({ level: 'h4', text: 'H4 - Content Block Title (24px)' })}
      ${component({ level: 'h5', text: 'H5 - Small Heading (20px)' })}
      ${component({ level: 'h6', text: 'H6 - Micro Title (16px)' })}
    </div>
  `,
};

export const AllColors = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Default Heading (gray)', color: 'default' })}
      ${component({ text: 'Primary Heading (green)', color: 'primary' })}
      ${component({ text: 'Secondary Heading (purple)', color: 'secondary' })}
      ${component({ text: 'Success Heading', color: 'success' })}
      ${component({ text: 'Warning Heading', color: 'warning' })}
      ${component({ text: 'Danger Heading', color: 'danger' })}
      ${component({ text: 'Info Heading', color: 'info' })}
    </div>
  `,
};

export const AllWeights = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-3);">
      ${component({ text: 'Light Weight (300)', weight: 'light', level: 'h2' })}
      ${component({ text: 'Regular Weight (400)', weight: 'regular', level: 'h2' })}
      ${component({ text: 'Bold Weight (700 - default)', weight: 'bold', level: 'h2' })}
      ${component({ text: 'Extra Weight (800)', weight: 'extra', level: 'h2' })}
    </div>
  `,
};

export const AllAlignments = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ text: 'Left Aligned (default)', align: 'left', level: 'h2' })}
      ${component({ text: 'Center Aligned', align: 'center', level: 'h2' })}
      ${component({ text: 'Right Aligned', align: 'right', level: 'h2' })}
    </div>
  `,
};
export const WithIcons = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-4);">
      ${component({ level: 'h2', text: 'Location', icon: 'icon-pin-map' })}
      ${component({ level: 'h3', text: 'Upcoming Events', icon: 'icon-calendar' })}
      ${component({ level: 'h4', text: 'View Details', icon: 'icon-arrow-right', iconPosition: 'right' })}
      ${component({ level: 'h2', text: 'Our Offices', icon: 'icon-offices', align: 'center' })}
    </div>
  `,
};

export const UseCases = {
  render: () => `
    <div style="display:flex; flex-direction:column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Page Structure</h3>
        ${component({ level: 'h1', text: 'Main Page Title', color: 'primary' })}
        ${component({ level: 'h2', text: 'Section Heading' })}
        ${component({ level: 'h3', text: 'Subsection Title', weight: 'regular' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">With Icons</h3>
        ${component({ level: 'h2', text: 'Property Search', icon: 'icon-search', color: 'primary' })}
        ${component({ level: 'h3', text: 'Contact Information', icon: 'icon-phone' })}
      </div>
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--size-4);">Semantic Colors</h3>
        ${component({ level: 'h3', text: 'Success Message', color: 'success', icon: 'icon-check' })}
        ${component({ level: 'h3', text: 'Warning Notice', color: 'warning', icon: 'icon-infos' })}
      </div>
    </div>
  `,
};
