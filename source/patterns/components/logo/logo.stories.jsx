import componentTwig from './logo.twig';
import data from './logo.yml';

export default {
  title: 'Components/Logo',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component:
          'BNP Paribas Real Estate responsive logo with optional slogan. Same logo image for all devices - automatically scales down on mobile with hidden slogan. Drupal-compatible with site branding conventions.',
      },
    },
  },
  render: (args) => componentTwig(args),
  argTypes: {
    site_logo: {
      name: 'site_logo',
      type: { name: 'string' },
      description: 'Path to logo image (same image for all devices, scales responsively)',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: '/logo/logo.svg' },
      },
    },
    site_slogan: {
      name: 'site_slogan',
      type: { name: 'string' },
      description: 'Optional slogan text displayed next to logo',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Real Estate for a Changing World' },
      },
    },
    url: {
      name: 'url',
      type: { name: 'string' },
      description: 'Link URL (wraps logo in <a> tag if provided)',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
    rel: {
      name: 'rel',
      type: { name: 'string' },
      description: 'Link rel attribute (e.g., "home")',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
    modifier_class: {
      name: 'modifier_class',
      type: { name: 'string' },
      description: 'Additional CSS classes',
      control: { type: 'text' },
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: 'null' },
      },
    },
  },
};

// Default story for Autodocs
export const Default = {
  args: { ...data },
};

// Desktop - Logo only
export const Desktop = {
  name: 'Desktop',
  args: {
    site_logo: '/logo/logo.svg',
    site_slogan: null,
  },
};

// Desktop with slogan
export const DesktopWithSlogan = {
  name: 'Desktop (With Slogan)',
  args: {
    site_logo: '/logo/logo.svg',
    site_slogan: 'Real Estate for a Changing World',
  },
};

// Mobile - Same logo, smaller size, slogan hidden
export const Mobile = {
  name: 'Mobile',
  parameters: {
    viewport: {
      defaultViewport: 'mobile1',
    },
  },
  args: {
    site_logo: '/logo/logo.svg',
    site_slogan: 'Real Estate for a Changing World',
  },
};

// Linked logo (homepage)
export const Linked = {
  name: 'Linked (Homepage)',
  args: {
    site_logo: '/logo/logo.svg',
    site_slogan: 'Real Estate for a Changing World',
    url: '#',
    rel: 'home',
  },
};
