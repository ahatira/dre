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

    // Appearance (radius)
    radius: {
      control: 'select',
      options: ['none', 'sm', 'md', 'lg'],
      description: 'Border radius',
      table: { category: 'Appearance' },
    },

    // Layout (image position)
    imagePosition: {
      control: 'select',
      options: ['top', 'bottom', 'left', 'right'],
      description: 'Image position (vertical: top/bottom, horizontal: left/right)',
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
    radius: 'md',
    imagePosition: 'top',
    showImage: true,
    imageUrl:
      'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop&q=80',
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
              `<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #333;">${variant.charAt(0).toUpperCase() + variant.slice(1)} Card</h3><p style="margin: 8px 0 0; color: #666;">Visual variant: ${variant}</p>`
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
            '<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=300&fit=crop&q=80" alt="Vertical" /></div><div class="ps-card__content"><h3 style="margin: 0;">Vertical Card</h3><p style="margin: 8px 0 0;">Image on top</p>'
          )
          .replace('</div></div>', '</div></div></div>')}
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout</h4>
        ${cardTwig({ layout: 'horizontal' })
          .replace(
            '<div class="ps-card__content">',
            '<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=242&h=212&fit=crop&q=80" alt="Horizontal" /></div><div class="ps-card__content"><h3 style="margin: 0;">Horizontal Card</h3><p style="margin: 8px 0 0;">Image on left (242px width)</p>'
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

// Radius Options
export const RadiusOptions = {
  render: () => `
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
      ${['none', 'sm', 'md', 'lg']
        .map((radius) =>
          cardTwig({ radius })
            .replace(
              '<div class="ps-card__content">',
              `<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0; font-size: 18px;">${radius === 'none' ? 'No Radius' : `${radius.toUpperCase()} Radius`}</h3><p style="margin: 8px 0 0;">Border radius: ${radius}</p>`
            )
            .replace('</div></div>', '</div></div></div>')
        )
        .join('')}
    </div>
  `,
};

// Image Position Options
export const ImagePositions = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: 32px;">
      <div>
        <h4 style="margin-bottom: 12px;">Vertical Layout - Image Positions</h4>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
          ${['top', 'bottom']
            .map((pos) =>
              cardTwig({ layout: 'vertical', imagePosition: pos })
                .replace(
                  '<div class="ps-card__content">',
                  `<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1560184897-ae75f418493e?w=400&h=300&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0;">Image ${pos === 'top' ? 'Top' : 'Bottom'}</h3><p style="margin: 8px 0 0;">Position: ${pos}</p>`
                )
                .replace('</div></div>', '</div></div></div>')
            )
            .join('')}
        </div>
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">Horizontal Layout - Image Positions</h4>
        <div style="display: flex; flex-direction: column; gap: 16px;">
          ${['left', 'right']
            .map((pos) =>
              cardTwig({ layout: 'horizontal', imagePosition: pos })
                .replace(
                  '<div class="ps-card__content">',
                  `<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=242&h=212&fit=crop&q=80" alt="Card" /></div><div class="ps-card__content"><h3 style="margin: 0;">Image ${pos === 'left' ? 'Left' : 'Right'}</h3><p style="margin: 8px 0 0;">Position: ${pos} (242px width)</p>`
                )
                .replace('</div></div>', '</div></div></div>')
            )
            .join('')}
        </div>
      </div>
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
    imageUrl:
      'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop&q=80',
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
          `<div class="ps-card__image"><img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=400&h=300&fit=crop&q=80" alt="News" /></div><div class="ps-card__content">
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
