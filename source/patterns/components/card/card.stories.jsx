import cardTwig from './card.twig';

export default {
  title: 'Components/Card (Generic)',
  tags: ['autodocs'],
  render: (args) => {
    // For Storybook, we simulate block content using a simple string
    // In real usage, use {% embed %} with {% block %} syntax
    const imageBlock = args.showImage ? `<img src="${args.imageUrl}" alt="Card image" />` : '';

    const contentBlock =
      args.contentHTML || '<h3>Card Title</h3><p>Card description goes here...</p>';

    // Inject blocks into template manually for Storybook
    return cardTwig(args)
      .replace(
        '<div class="ps-card__content">',
        `${imageBlock ? `<div class="ps-card__image">${imageBlock}</div>` : ''}<div class="ps-card__content">${contentBlock}`
      )
      .replace('</div></div>', '</div></div></div>');
  },
  argTypes: {
    // Appearance
    variant: {
      control: 'select',
      options: ['default', 'outlined', 'flat', 'elevated'],
      description: 'Visual variant',
      table: { category: 'Appearance' },
    },
    layout: {
      control: 'select',
      options: ['vertical', 'horizontal'],
      description: 'Layout orientation',
      table: { category: 'Layout' },
    },
    size: {
      control: 'select',
      options: ['small', 'medium', 'large'],
      description: 'Padding size',
      table: { category: 'Layout' },
    },

    // Link
    url: {
      control: 'text',
      description: 'Optional card link URL (wraps entire card)',
      table: { category: 'Behavior' },
    },

    // Storybook helpers (not real props)
    showImage: {
      control: 'boolean',
      description: '[Storybook only] Show image block',
      table: { category: 'Demo' },
    },
    imageUrl: {
      control: 'text',
      description: '[Storybook only] Image URL',
      table: { category: 'Demo' },
    },
    contentHTML: {
      control: 'text',
      description: '[Storybook only] Content HTML',
      table: { category: 'Demo' },
    },
  },
};

// Default Card
export const Default = {
  args: {
    variant: 'default',
    layout: 'vertical',
    size: 'medium',
    showImage: true,
    imageUrl: 'https://picsum.photos/400/300?random=1',
    contentHTML:
      '<h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">Card Title</h3><p style="margin: 12px 0 0; color: #666;">This is a generic card container. Content is composed using Twig blocks for maximum flexibility.</p>',
  },
};

// Visual Variants
export const VisualVariants = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
      ${['default', 'outlined', 'flat', 'elevated']
        .map((variant) =>
          cardTwig({
            variant,
            layout: 'vertical',
            size: 'medium',
          })
            .replace(
              '<div class="ps-card__content">',
              `<div class="ps-card__image"><img src="https://picsum.photos/400/300?random=${variant}" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #333;">${variant.charAt(0).toUpperCase() + variant.slice(1)} Card</h3><p style="margin: 8px 0 0; color: #666;">Visual variant: ${variant}</p>`
            )
            .replace('</div></div>', '</div></div></div>')
        )
        .join('')}
    </div>
  `,
};

// Layout Options
export const Layouts = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 24px;">
      <div>
        <h4 style="margin-bottom: 12px;">Vertical Layout</h4>
        ${cardTwig({ layout: 'vertical' })
          .replace(
            '<div class="ps-card__content">',
            '<div class="ps-card__image"><img src="https://picsum.photos/400/300?random=v" alt="Vertical" /></div><div class="ps-card__content"><h3 style="margin: 0;">Vertical Card</h3><p style="margin: 8px 0 0;">Image on top</p>'
          )
          .replace('</div></div>', '</div></div></div>')}
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout</h4>
        ${cardTwig({ layout: 'horizontal' })
          .replace(
            '<div class="ps-card__content">',
            '<div class="ps-card__image"><img src="https://picsum.photos/242/212?random=h" alt="Horizontal" /></div><div class="ps-card__content"><h3 style="margin: 0;">Horizontal Card</h3><p style="margin: 8px 0 0;">Image on left (242px width)</p>'
          )
          .replace('</div></div>', '</div></div></div>')}
      </div>
    </div>
  `,
};

// Size Options
export const Sizes = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
      ${['small', 'medium', 'large']
        .map((size) =>
          cardTwig({ size }).replace(
            '<div class="ps-card__content">',
            `<div class="ps-card__content"><h3 style="margin: 0; font-size: ${size === 'small' ? '16px' : size === 'large' ? '24px' : '20px'};">${size.charAt(0).toUpperCase() + size.slice(1)}</h3><p style="margin: 8px 0 0;">Padding: ${size === 'small' ? '16px' : size === 'large' ? '32px' : '30px 24px'}</p>`
          )
        )
        .join('')}
    </div>
  `,
};

// As Link (clickable card)
export const AsLink = {
  args: {
    variant: 'default',
    layout: 'vertical',
    size: 'medium',
    url: '#card-link',
    showImage: true,
    imageUrl: 'https://picsum.photos/400/300?random=link',
    contentHTML:
      '<h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">Clickable Card</h3><p style="margin: 12px 0 0; color: #666;">This entire card is clickable. Hover to see the shadow effect.</p>',
  },
};

// Composition Example (with structured blocks)
export const CompositionExample = {
  render: () => `
    <div style="max-width: 400px;">
      <h4 style="margin-bottom: 12px;">Example: News Article Card</h4>
      ${cardTwig({ variant: 'elevated' })
        .replace(
          '<div class="ps-card__content">',
          `<div class="ps-card__image"><img src="https://picsum.photos/400/300?random=news" alt="News" /></div><div class="ps-card__content">
          <div class="ps-card__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <span style="display: inline-block; padding: 4px 8px; background: #00915A; color: white; font-size: 12px; font-weight: 600; border-radius: 4px; text-transform: uppercase;">News</span>
            <span style="font-size: 14px; color: #777;">Nov 30, 2025</span>
          </div>
          <div class="ps-card__body">
            <h3 style="margin: 0 0 8px; font-size: 20px; font-weight: 700; color: #333;">Card Composition Pattern</h3>
            <p style="margin: 0; color: #666;">Use Twig blocks to compose custom card content. This example shows a news article with header (badge + date), body (title + excerpt), and footer (read more link).</p>
          </div>
          <div class="ps-card__footer" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee;">
            <a href="#" style="color: #00915A; text-decoration: none; font-weight: 600;">Read more →</a>
          </div>`
        )
        .replace('</div></div>', '</div></div></div>')}
    </div>
  `,
};
