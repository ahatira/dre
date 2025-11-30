import skipLinkTwig from './skip-link.twig';
import data from './skip-link.yml';

export default {
  title: 'Elements/Skip Link',
  tags: ['autodocs'],
  parameters: {
    docs: {
      description: {
        component: `WCAG skip link for keyboard navigation to main content.

**Key Features:**
- Hidden by default, visible only on keyboard focus (Tab key)
- Positioned absolute at top-left with high z-index
- Links to specific page sections via target ID (main-content, navigation, search)
- Green background (--brand-primary) with white text
- Smooth transform animation (translateY from -150% to 0)
- Required for WCAG 2.2 Level A compliance (criterion 2.4.1)

**Usage:**
- Must be the first focusable element on the page
- Allows keyboard users to bypass repetitive navigation
- Target element must have matching id attribute
- Typical targets: #main-content, #navigation, #search
- Always visible on focus, invisible otherwise

**Accessibility:**
- Meets WCAG 2.4.1 Bypass Blocks (Level A)
- Essential for keyboard-only users
- Focus-visible outline for keyboard navigation
- No aria attributes needed (native link semantics)`,
      },
    },
  },
  argTypes: {
    // Content
    label: {
      description: 'Link text displayed to user (e.g., "Skip to main content", "Skip to navigation")',
      control: { type: 'text' },
      table: {
        category: 'Content',
        type: { summary: 'string', required: true },
        defaultValue: { summary: 'Skip to main content' },
      },
    },
    // Link
    targetId: {
      description: 'ID of the target anchor element (must exist in page, e.g., main-content, navigation, search)',
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

export const UseCases = {
  render: () => `
    <div style="position: relative; padding: 4rem 1rem 1rem; border: 2px dashed var(--gray-300); min-height: 200px;">
      <p style="position: absolute; top: var(--size-4); left: var(--size-4); margin: 0; font-size: var(--font-size-0); color: var(--gray-600);">
        👆 Press Tab to see the skip link appear at top-left
      </p>
      ${skipLinkTwig({ targetId: 'main-content', label: 'Skip to main content' })}
      <div style="margin-top: var(--size-8);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Multiple Skip Links Example</h3>
        <div style="display: flex; flex-direction: column; gap: var(--size-2);">
          ${skipLinkTwig({ targetId: 'main-content', label: 'Skip to main content' })}
          ${skipLinkTwig({ targetId: 'navigation', label: 'Skip to navigation' })}
          ${skipLinkTwig({ targetId: 'search', label: 'Skip to search' })}
        </div>
      </div>
      <div style="margin-top: var(--size-6);">
        <h3 style="margin: 0 0 var(--size-3) 0; font-size: var(--font-size-2);">Implementation Guidelines</h3>
        <ul style="margin: 0; padding-left: var(--size-6); font-size: var(--font-size-1);">
          <li>Must be the first focusable element on the page</li>
          <li>Allows keyboard users to bypass repetitive navigation</li>
          <li>Required for WCAG 2.2 criterion 2.4.1 (Level A)</li>
          <li>Visible only on keyboard focus, hidden otherwise</li>
          <li>Target element must have matching id attribute</li>
        </ul>
      </div>
      <div id="main-content" style="margin-top: var(--size-8); padding: var(--size-4); background: var(--gray-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Main Content Area</strong> - Skip link points here (id="main-content")</p>
      </div>
      <div id="navigation" style="margin-top: var(--size-4); padding: var(--size-4); background: var(--blue-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Navigation Area</strong> - Alternative target (id="navigation")</p>
      </div>
      <div id="search" style="margin-top: var(--size-4); padding: var(--size-4); background: var(--green-50); border-radius: var(--radius-2);">
        <p style="margin: 0;"><strong>Search Area</strong> - Alternative target (id="search")</p>
      </div>
    </div>
  `,
};
