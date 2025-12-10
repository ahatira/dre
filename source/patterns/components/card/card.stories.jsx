import cardTwig from './card.twig';
import data from './card.yml';

const settings = {
  title: 'Components/Card',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          '**Flexible container component** for displaying content with optional media, header, body, and footer sections.\n\n' +
          '### Key Features\n' +
          '- **6 Extensible Blocks**: `media`, `media_overlay`, `header`, `content`, `body`, `footer` for Drupal composition\n' +
          '- **4 Visual Variants**: default (border), outlined (thick border), flat (no border), elevated (shadow)\n' +
          '- **Responsive Layouts**: Vertical or horizontal (automatically stacks on mobile)\n' +
          '- **Flexible Positioning**: Image can be placed at start (top/left) or end (bottom/right)\n' +
          '- **Clickable Cards**: Add `url` prop to make entire card interactive\n\n' +
          '### Design System\n' +
          '3-layer CSS architecture: Global tokens → Component CSS variables (`--ps-card-*`) → Modifier classes.',
      },
    },
  },
  argTypes: {
    variant: {
      control: { type: 'select' },
      options: ['default', 'outlined', 'flat', 'elevated'],
      description: 'Visual style variant',
      table: {
        category: 'Appearance',
        type: { summary: 'default | outlined | flat | elevated' },
        defaultValue: { summary: 'default' },
      },
    },
    layout: {
      control: { type: 'inline-radio' },
      options: ['vertical', 'horizontal'],
      description: 'Layout orientation',
      table: {
        category: 'Appearance',
        type: { summary: 'vertical | horizontal' },
        defaultValue: { summary: 'vertical' },
      },
    },
    imagePosition: {
      control: { type: 'inline-radio' },
      options: ['start', 'end'],
      description: 'Image position: start (top/left) or end (bottom/right)',
      table: {
        category: 'Appearance',
        type: { summary: 'start | end' },
        defaultValue: { summary: 'start' },
      },
    },
    size: {
      control: { type: 'inline-radio' },
      options: ['small', 'medium', 'large'],
      description: 'Content padding size',
      table: {
        category: 'Appearance',
        type: { summary: 'small | medium | large' },
        defaultValue: { summary: 'medium' },
      },
    },
    radius: {
      control: { type: 'inline-radio' },
      options: ['none', 'sm', 'md', 'lg'],
      description: 'Border radius amount',
      table: {
        category: 'Appearance',
        type: { summary: 'none | sm | md | lg' },
        defaultValue: { summary: 'none' },
      },
    },
    url: {
      control: 'text',
      description: 'Optional URL - renders card as clickable <a> element',
      table: {
        category: 'Link',
        type: { summary: 'string | undefined' },
      },
    },
    image: {
      control: 'text',
      description: 'Image/media HTML or object {src, alt, ratio}',
      table: {
        category: 'Content',
        type: { summary: 'string | object' },
      },
    },
    header: {
      control: 'text',
      description: 'Header section HTML',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },
    body: {
      control: 'text',
      description: 'Body/content section HTML',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },
    footer: {
      control: 'text',
      description: 'Footer section HTML',
      table: {
        category: 'Content',
        type: { summary: 'string | html' },
      },
    },
  },
};

// Shared assets
const baseImage =
  '<img src="/images/3-2.jpg" alt="Sample image" style="display: block; width: 100%; height: 100%; object-fit: cover;" />';

// ==============================================
// STORY 1: Default (Interactive Playground)
// ==============================================

export const Default = {
  render: (args) => cardTwig(args),
  args: {
    ...data,
    image: baseImage,
    header: '<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Card Title</h3>',
    body: '<p style="margin: 0; font-size: 0.875rem; color: var(--gray-600);">This is the card description text. Customize all props using the controls below.</p>',
    footer: '<span style="color: var(--primary); font-weight: 600;">Action link →</span>',
  },
};

// ==============================================
// STORY 2: Variants
// ==============================================

export const Variants = {
  render: () => {
    const variants = [
      { key: 'default', label: 'Default', desc: '1px border' },
      { key: 'outlined', label: 'Outlined', desc: '2px border' },
      { key: 'flat', label: 'Flat', desc: 'No border' },
      { key: 'elevated', label: 'Elevated', desc: 'Shadow' },
    ];

    return `
      <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
        ${variants
          .map(
            ({ key, label, desc }) => `
          <div style="flex: 1; min-width: 200px;">
            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600;">${label}</h4>
            <p style="margin: 0 0 0.75rem 0; font-size: 0.75rem; color: var(--gray-500);">${desc}</p>
            ${cardTwig({
              variant: key,
              radius: 'md',
              size: 'small',
              image: baseImage,
              body: '<h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Card Title</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600);">Sample description</p>',
            })}
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
          '**Visual variants** provide different emphasis levels:\n\n' +
          '- `default`: Standard 1px border\n' +
          '- `outlined`: Emphasized 2px border\n' +
          '- `flat`: No border, minimal style\n' +
          '- `elevated`: No border, shadow depth',
      },
    },
  },
};

// ==============================================
// STORY 3: Layouts
// ==============================================

export const Layouts = {
  render: () => {
    return `
      <div style="display: grid; gap: 3rem;">
        <!-- Vertical Layout -->
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Vertical Layout</h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
            ${cardTwig({
              layout: 'vertical',
              imagePosition: 'start',
              radius: 'md',
              image: baseImage,
              body: '<h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Image Start</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600);">Image at top</p>',
            })}
            ${cardTwig({
              layout: 'vertical',
              imagePosition: 'end',
              radius: 'md',
              variant: 'outlined',
              header:
                '<h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Image End</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600);">Image at bottom</p>',
              image: baseImage,
            })}
          </div>
        </div>
        
        <!-- Horizontal Layout -->
        <div>
          <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Horizontal Layout (responsive)</h3>
          <div style="display: grid; gap: 1.5rem;">
            ${cardTwig({
              layout: 'horizontal',
              imagePosition: 'start',
              radius: 'md',
              image: baseImage,
              body: '<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Image Start</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600);">40/60 split. Image left, content right. Stacks on mobile.</p>',
            })}
            ${cardTwig({
              layout: 'horizontal',
              imagePosition: 'end',
              radius: 'md',
              variant: 'elevated',
              image: baseImage,
              body: '<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Image End</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600);">Content left, image right. Automatically responsive.</p>',
            })}
          </div>
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Layout options:**\n\n' +
          '- **Vertical**: Image above or below content (`imagePosition: "start"` or `"end"`)\n' +
          '- **Horizontal**: Image left or right (40/60 split, `imagePosition: "start"` or `"end"`)\n\n' +
          '**Responsive:** Horizontal cards automatically stack to vertical on mobile (<768px)',
      },
    },
  },
};

// ==============================================
// STORY 4: Content Sections
// ==============================================

export const ContentSections = {
  render: () => {
    return `
      <div style="display: grid; gap: 2rem; max-width: 600px;">
        <div>
          <h3 style="margin: 0 0 0.75rem 0; font-size: 1rem; font-weight: 600;">Full Card (all sections)</h3>
          ${cardTwig({
            variant: 'elevated',
            radius: 'md',
            image: baseImage,
            header:
              '<div style="padding-bottom: 0.5rem; border-bottom: 1px solid var(--gray-200);"><span style="font-size: 0.75rem; color: var(--primary); font-weight: 600;">CATEGORY</span></div>',
            body: '<h3 style="margin: 0; font-size: 1.125rem; font-weight: 600;">Card with Header, Body & Footer</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600); line-height: 1.5;">This card demonstrates all available content sections working together.</p>',
            footer:
              '<div style="display: flex; gap: 1rem; align-items: center; font-size: 0.875rem;"><span style="color: var(--gray-500);">📅 Dec 10, 2025</span><span style="margin-left: auto; color: var(--primary); font-weight: 600;">View →</span></div>',
          })}
        </div>
        
        <div>
          <h3 style="margin: 0 0 0.75rem 0; font-size: 1rem; font-weight: 600;">Content Only (no image)</h3>
          ${cardTwig({
            variant: 'outlined',
            radius: 'md',
            size: 'large',
            body: '<h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;">Heading Without Media</h3><p style="margin: 1rem 0 0; font-size: 1rem; color: var(--gray-600); line-height: 1.6;">Cards don\'t always need images. This layout works well for announcements, CTAs, or text-focused content.</p>',
            footer:
              '<button style="padding: 0.75rem 2rem; background: var(--primary); color: var(--white); border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Call to Action</button>',
          })}
        </div>
        
        <div>
          <h3 style="margin: 0 0 0.75rem 0; font-size: 1rem; font-weight: 600;">Minimal Card</h3>
          ${cardTwig({
            variant: 'flat',
            radius: 'none',
            body: '<h3 style="margin: 0; font-size: 1rem; font-weight: 600;">Simple Text Card</h3><p style="margin: 0.5rem 0 0; font-size: 0.875rem; color: var(--gray-600);">Minimal styling with just body content.</p>',
          })}
        </div>
      </div>
    `;
  },
  parameters: {
    docs: {
      description: {
        story:
          '**Content flexibility examples:**\n\n' +
          '- **Full card**: All sections (media, header, body, footer)\n' +
          '- **Content only**: No image, useful for announcements or CTAs\n' +
          '- **Minimal**: Single section, simple layout',
      },
    },
  },
};

export default settings;
