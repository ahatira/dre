import icon from './icon.twig';
import data from './icon.yml';

const settings = {
  title: 'Elements/Icon',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: 'Icon font system using <i class="ps-icon ps-icon-{name}"> markup. Sizes: small (16px), medium (20px), large (24px), xlarge (32px). Colors: dark-grey, light-grey, green, white.',
      },
    },
  },
  argTypes: {
    name: {
      description: 'Icon name',
      control: { type: 'select' },
      options: ['arrow-down','arrow-left','arrow-right','arrow-up','calendar','check','close','delete','edit','info','menu','minus','plus','search','test','warning'],
      table: {
        type: { summary: 'string' },
        defaultValue: { summary: 'search' },
      },
    },
    size: {
      description: 'Icon size',
      control: { type: 'select' },
      options: ['small', 'medium', 'large', 'xlarge'],
      table: {
        type: { summary: 'small | medium | large | xlarge' },
        defaultValue: { summary: 'large' },
      },
    },
    colorVariant: {
      description: 'Icon color variant',
      control: { type: 'select' },
      options: ['dark-grey', 'light-grey', 'green', 'white'],
      table: {
        type: { summary: 'dark-grey | light-grey | green | white' },
        defaultValue: { summary: 'dark-grey' },
      },
    },
    disabled: {
      description: 'Disabled state',
      control: { type: 'boolean' },
      table: {
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    ariaLabel: {
      description: 'Accessibility label (use for informative icons)',
      control: { type: 'text' },
      table: {
        type: { summary: 'string' },
      },
    },
  },
};

export const Default = {
  render: (args) => icon(args),
  args: { ...data },
};

export const ArrowRight = {
  render: () => icon({ name: 'arrow-right', size: 'large' }),
};

export const AllSizes = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'search', size: 'small' })}
      ${icon({ name: 'search', size: 'medium' })}
      ${icon({ name: 'search', size: 'large' })}
      ${icon({ name: 'search', size: 'xlarge' })}
    </div>
  `,
};

export const AllColors = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'check', size: 'large', colorVariant: 'dark-grey' })}
      ${icon({ name: 'check', size: 'large', colorVariant: 'light-grey' })}
      ${icon({ name: 'check', size: 'large', colorVariant: 'green' })}
      <div style="background: var(--gray-900); padding: var(--size-3); border-radius: var(--radius-2);">
        ${icon({ name: 'check', size: 'large', colorVariant: 'white' })}
      </div>
    </div>
  `,
};

export const Gallery = {
  render: () => {
    const names = [
      'arrow-down','arrow-left','arrow-right','arrow-up','calendar','check','close','delete','edit','info','menu','minus','plus','search','test','warning'
    ];
    return `
      <div style="display: grid; grid-template-columns: repeat(8, minmax(0, 1fr)); gap: var(--size-6);">
        ${names.map((n) => `
          <div style="display: flex; flex-direction: column; align-items: center; gap: var(--size-3);">
            ${icon({ name: n, size: 'xlarge' })}
            <code style="font-size: var(--font-size-0);">ps-icon-${n}</code>
          </div>
        `).join('')}
      </div>
    `;
  },
};

export const Disabled = {
  render: () => `
    <div style="display: flex; gap: var(--size-6); align-items: center;">
      ${icon({ name: 'search', size: 'large', disabled: false })}
      ${icon({ name: 'search', size: 'large', disabled: true })}
    </div>
  `,
};

export default settings;

