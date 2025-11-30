import accordionTwig from './accordion.twig';
import data from './accordion.yml';
import './accordion.js';

const settings = {
  title: 'Components/Accordion',
  tags: ['autodocs'],
  render: (args) => accordionTwig(args),
  args: data,
  parameters: {
    docs: {
      description: {
        component:
          'Accessible accordion component for collapsible content sections with ARIA support and keyboard navigation.',
      },
    },
  },
  argTypes: {
    items: {
      description: 'Array of accordion sections with title and content',
      control: { type: 'object' },
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    singleOpen: {
      description: 'Only one section can be open at a time',
      control: { type: 'boolean' },
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    bordered: {
      description: 'Show borders between accordion items',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    flush: {
      description: 'Remove padding for dense layouts',
      control: { type: 'boolean' },
      table: {
        category: 'Appearance',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    headingLevel: {
      description: 'Semantic heading level for accessibility',
      control: { type: 'select' },
      options: ['h2', 'h3', 'h4', 'h5'],
      table: {
        category: 'Accessibility',
        type: { summary: 'h2 | h3 | h4 | h5' },
        defaultValue: { summary: 'h3' },
      },
    },
  },
};

export const Default = {
  render: (args) => accordionTwig(args),
  args: { ...data },
};

export const AllVariants = {
  render: () => `
    <div style="display: flex; flex-direction: column; gap: var(--size-8);">
      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-2);">Bordered (Default)</h3>
        ${accordionTwig({
          bordered: true,
          singleOpen: true,
          items: [
            {
              title: 'Section 1',
              content: '<p>Bordered accordion with single open mode.</p>',
              open: true,
            },
            { title: 'Section 2', content: '<p>Only one section can be open at a time.</p>' },
            { title: 'Section 3', content: '<p>Click to expand this section.</p>' },
          ],
        })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-2);">No Borders</h3>
        ${accordionTwig({
          bordered: false,
          singleOpen: true,
          items: [
            { title: 'Section 1', content: '<p>Accordion without borders.</p>', open: true },
            { title: 'Section 2', content: '<p>Clean minimal appearance.</p>' },
          ],
        })}
      </div>

      <div>
        <h3 style="margin: 0 0 var(--size-4) 0; font-size: var(--font-size-2);">Flush</h3>
        ${accordionTwig({
          bordered: true,
          flush: true,
          singleOpen: true,
          items: [
            {
              title: 'Section 1',
              content: '<p>Reduced padding for compact layouts.</p>',
              open: true,
            },
            { title: 'Section 2', content: '<p>Useful in sidebars or dense interfaces.</p>' },
          ],
        })}
      </div>
    </div>
  `,
};

export const SingleOpen = {
  render: () =>
    accordionTwig({
      singleOpen: true,
      bordered: true,
      items: [
        {
          title: 'Question 1',
          content: '<p>Only one section can be open at a time.</p>',
          open: true,
        },
        { title: 'Question 2', content: '<p>Opening this will close the previous section.</p>' },
        { title: 'Question 3', content: '<p>This is the default behavior for FAQs.</p>' },
      ],
    }),
};

export const MultiOpen = {
  render: () =>
    accordionTwig({
      singleOpen: false,
      bordered: true,
      items: [
        {
          title: 'Section 1',
          content: '<p>Multiple sections can be open simultaneously.</p>',
          open: true,
        },
        { title: 'Section 2', content: '<p>This section can also be opened.</p>', open: true },
        { title: 'Section 3', content: '<p>All sections are independent.</p>' },
      ],
    }),
};

export const WithHTML = {
  render: () =>
    accordionTwig({
      singleOpen: true,
      bordered: true,
      items: [
        {
          title: 'Rich Content Example',
          content: `
          <p>Accordion panels support <strong>full HTML</strong> content including:</p>
          <ul>
            <li>Lists and nested structures</li>
            <li>Links: <a href="#">Learn more</a></li>
            <li>Formatted text with <em>emphasis</em></li>
          </ul>
        `,
          open: true,
        },
        {
          title: 'Multiple Paragraphs',
          content: `
          <p>First paragraph with detailed information about the topic.</p>
          <p>Second paragraph continuing the explanation with additional context and examples.</p>
          <p>Final paragraph with a call to action or summary.</p>
        `,
        },
      ],
    }),
};

export const FAQ = {
  render: () =>
    accordionTwig({
      singleOpen: true,
      bordered: true,
      headingLevel: 'h3',
      items: [
        {
          title: 'How do I reset my password?',
          content:
            '<p>Click "Forgot Password" on the login page. Enter your email address and follow the instructions sent to your inbox. If you don\'t receive an email within 5 minutes, check your spam folder.</p>',
          open: true,
        },
        {
          title: 'What payment methods do you accept?',
          content:
            '<p>We accept all major credit cards (Visa, Mastercard, American Express), PayPal, and bank transfers. For large transactions, we also offer invoice payment options for approved accounts.</p>',
        },
        {
          title: 'How long does shipping take?',
          content:
            '<p>Standard shipping takes 5-7 business days. Express shipping (2-3 days) and overnight options are available at checkout. International shipping times vary by destination (7-21 days).</p>',
        },
        {
          title: 'What is your return policy?',
          content:
            '<p>We offer a 30-day return policy for most items. Products must be unused and in original packaging. Return shipping is free for defective items; customers pay return shipping for other returns.</p>',
        },
      ],
    }),
};

export const ProductDetails = {
  render: () =>
    accordionTwig({
      singleOpen: false,
      bordered: true,
      items: [
        {
          title: 'Description',
          content:
            '<p>High-quality product designed for professional use. Manufactured with premium materials and engineered for durability and performance in demanding environments.</p>',
          open: true,
        },
        {
          title: 'Specifications',
          content: `
          <ul>
            <li><strong>Dimensions:</strong> 30 × 20 × 15 cm</li>
            <li><strong>Weight:</strong> 2.5 kg</li>
            <li><strong>Material:</strong> Aluminum alloy</li>
            <li><strong>Color:</strong> Matte black</li>
            <li><strong>Warranty:</strong> 2 years</li>
          </ul>
        `,
          open: true,
        },
        {
          title: 'Shipping & Returns',
          content:
            '<p>Free shipping on orders over €50. Standard delivery takes 3-5 business days. Returns accepted within 30 days of purchase.</p>',
        },
      ],
    }),
};

export default settings;
