import skipLinkTwig from './skip-link.twig';
import data from './skip-link.yml';

export default {
  title: 'Elements/Skip Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `WCAG “Skip to content” link for keyboard navigation.
Hidden by default, appears on focus; targets an in-page anchor by id.`,
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description:
        'Link text displayed to user (e.g., "Skip to main content", "Skip to navigation")',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Skip to main content' },
      },
    },
    // Link
    targetId: {
      description:
        'ID of the target anchor element (must exist in page, e.g., main-content, navigation, search)',
      control: { type: 'text' },
      table: {
        category: 'Link',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'main-content' },
      },
    },
  },
  args: { ...data },
};

export const Default = {
  render: (args) => skipLinkTwig(args),
  args: { ...data },
};

export const ToMainContent = {
  render: (args) => skipLinkTwig(args),
  args: {
    targetId: 'main-content',
    label: 'Skip to main content',
  },
};

export const ToNavigation = {
  render: (args) => skipLinkTwig(args),
  args: {
    targetId: 'navigation',
    label: 'Skip to navigation',
  },
};

export const ToSearch = {
  render: (args) => skipLinkTwig(args),
  args: {
    targetId: 'search',
    label: 'Skip to search',
  },
};

export const WithLongLabel = {
  render: (args) => skipLinkTwig(args),
  args: {
    targetId: 'main-content',
    label: 'Skip to main content area',
  },
};
