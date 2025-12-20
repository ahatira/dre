import markup from './pager.twig';
import data from './pager.yml';

function clone(value) {
  return JSON.parse(JSON.stringify(value));
}

export default {
  title: 'Components/Pager',
  tags: ['autodocs'],
  argTypes: {
    heading_id: {
      control: 'text',
      description: 'ID of the pagination heading',
      table: { category: 'Accessibility' },
    },
    pagination_heading_level: {
      control: { type: 'select', options: ['h2', 'h3', 'h4'] },
      description: 'Heading level for the visually hidden title',
      table: { category: 'Accessibility' },
    },
    current: {
      control: { type: 'number', min: 1, max: 20 },
      description: 'Current active page number',
      table: { category: 'State' },
    },
    ellipses: {
      control: 'object',
      description: 'Show ellipses before/after pages',
      table: { category: 'Configuration' },
    },
    items: {
      control: 'object',
      description: 'Drupal pager items structure',
      table: { category: 'Data' },
    },
  },
};

export const Default = {
  name: 'Default',
  render: function (args) {
    return markup(args);
  },
  args: data,
};

export const FirstPage = {
  name: 'First Page',
  render: function (args) {
    return markup(args);
  },
  args: (function () {
    const args = clone(data);
    args.current = 1;
    delete args.items.previous;
    return args;
  })(),
};

export const LastPage = {
  name: 'Last Page',
  render: function (args) {
    return markup(args);
  },
  args: (function () {
    const args = clone(data);
    args.current = 5;
    delete args.items.next;
    return args;
  })(),
};

export const WithEllipses = {
  name: 'With Ellipses',
  render: function (args) {
    return markup(args);
  },
  args: (function () {
    const args = clone(data);
    args.current = 10;
    args.items.pages = {
      8: { href: '#' },
      9: { href: '#' },
      10: { href: '#' },
      11: { href: '#' },
      12: { href: '#' },
    };
    args.ellipses = { previous: true, next: true };
    return args;
  })(),
};

export const ManyPages = {
  name: 'Many Pages',
  render: function (args) {
    return markup(args);
  },
  args: (function () {
    const args = clone(data);
    args.current = 5;
    const pages = {};
    for (let i = 1; i <= 10; i++) {
      pages[i] = { href: '#' };
    }
    args.items.pages = pages;
    return args;
  })(),
};
