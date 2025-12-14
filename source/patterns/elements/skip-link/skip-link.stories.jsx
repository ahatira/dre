import skipLinkTwig from './skip-link.twig';
import data from './skip-link.yml';

export default {
  title: 'Elements/Skip Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `WCAG accessibility link for keyboard navigation. Hidden by default, appears on Tab focus.
Targets an in-page anchor by ID. Essential for keyboard users to skip repetitive navigation.`,
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description:
        'Link text displayed on focus (e.g., "Skip to main content", "Skip to navigation")',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Skip to main content' },
      },
    },
    // Accessibility
    targetId: {
      description:
        'ID of the target anchor element (must exist in page DOM, e.g., main-content, navigation, search)',
      control: { type: 'text' },
      table: {
        category: 'Accessibility',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'main-content' },
      },
    },
  },
  args: data,
};

export const Default = {
  render: (args) => skipLinkTwig(args),
  args: data,
};

export const CommonTargets = {
  render: () => `
    <div style="padding: 2rem; background: var(--gray-50); border-radius: var(--radius-2);">
      <p style="margin: 0 0 1.5rem; font-size: 14px; color: var(--gray-700); font-weight: 500;">Press <kbd style="padding: 0.25rem 0.5rem; background: white; border: 1px solid var(--gray-300); border-radius: var(--radius-1); font-family: monospace;">Tab</kbd> to see skip links appear (hidden by default, visible on focus)</p>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        ${skipLinkTwig({ targetId: 'main-content', label: 'Skip to main content' })}
        ${skipLinkTwig({ targetId: 'navigation', label: 'Skip to navigation' })}
        ${skipLinkTwig({ targetId: 'search', label: 'Skip to search' })}
        ${skipLinkTwig({ targetId: 'footer', label: 'Skip to footer' })}
      </div>
      <div style="margin-top: 2rem; padding: 1.5rem; background: white; border-radius: var(--radius-2); border: 1px solid var(--gray-200);">
        <p style="margin: 0; font-size: 13px; color: var(--gray-600);"><strong>Usage:</strong> Place skip link as first focusable element in <code>&lt;body&gt;</code>. Target element must have <code>id</code> and <code>tabindex="-1"</code> for focus management.</p>
      </div>
    </div>
  `,
  parameters: {
    docs: {
      description: {
        story:
          'Common skip link targets for accessible navigation. Main content is most common, but navigation, search, and footer are also used.',
      },
    },
  },
};
