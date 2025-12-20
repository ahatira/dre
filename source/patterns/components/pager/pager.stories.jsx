import markup from './pager.twig';
import data from './pager.yml';

const clone = (value) => JSON.parse(JSON.stringify(value));

const settings = {
  title: 'Components/Pager',
  tags: ['autodocs'],
  argTypes: {
    heading_id: {
      control: 'text',
      description: 'ID of the hidden heading associated with the nav',
      table: { category: 'Accessibility' },
    },
    pager_heading_level: {
      control: { type: 'select', options: ['h2', 'h3', 'h4'] },
      description: 'Heading level for the hidden heading',
      table: { category: 'Accessibility' },
    },
    aria_label: {
      control: 'text',
      description: 'Label for the hidden heading (navigation)',
      table: { category: 'Accessibility' },
    },
    current: {
      control: { type: 'number', min: 1, max: 20 },
      description: 'Current active page number',
      table: { category: 'State' },
    },
    show_first_last: {
      control: 'boolean',
      description: 'Show First/Last page buttons',
      table: { category: 'Configuration' },
    },
    use_icons: {
      control: 'boolean',
      description: 'Use icons instead of text for navigation controls',
      table: { category: 'Configuration' },
    },
    ellipses: {
      control: 'object',
      description: 'Presence of ellipses before/after pages',
      table: { category: 'Configuration' },
    },
    items: {
      control: 'object',
      description: 'Drupal pager structure (first, previous, pages, next, last)',
      table: { category: 'Data' },
    },
  },
};

export const Default = {
  name: 'Pager',
  render: (args) => markup(args),
  args: data,
};

export const FirstPage = {
  name: 'First Page (No Previous)',
  render: (args) => markup(args),
  args: (() => {
    const args = clone(data);
    args.current = 1;
    delete args.items.previous;
    delete args.items.first;
    return args;
  })(),
};

export const LastPage = {
  name: 'Last Page (No Next)',
  render: (args) => markup(args),
  args: (() => {
    const args = clone(data);
    args.current = 5;
    delete args.items.next;
    delete args.items.last;
    return args;
  })(),
};

export const AvecEllipses = {
  name: 'With Ellipses',
  render: (args) => markup(args),
  args: (() => {
    const args = clone(data);
    args.current = 5;
    args.items.pages = {
      3: { href: '#' },
      4: { href: '#' },
      5: { href: '#' },
      6: { href: '#' },
      7: { href: '#' },
    };
    args.ellipses = { previous: true, next: true };
    return args;
  })(),
};

export const ModeIcones = {
  name: 'Icon Mode',
  render: (args) => markup(args),
  args: (() => {
    const args = clone(data);
    args.use_icons = true;
    return args;
  })(),
};

export const AvecPremiereDerniere = {
  name: 'With First/Last Buttons',
  render: (args) => markup(args),
  args: (() => {
    const args = clone(data);
    args.show_first_last = true;
    return args;
  })(),
};

export const CompletAvecIcones = {
  name: 'Complete with Icons',
  render: (args) => markup(args),
  args: (() => {
    const args = clone(data);
    args.show_first_last = true;
    args.use_icons = true;
    return args;
  })(),
};

export default settings;
