import markup from './menu-item.twig';
import data from './menu-item.yml';

const settings = {
  title: 'Components/Menu Item',
  tags: ['autodocs'],
};

export const Default = {
  name: 'Menu Item',
  render: (args) => `<ul>${markup(args)}</ul>`,
  args: { ...data },
};

export const Active = {
  name: 'Active State',
  render: (args) => `<ul>${markup({ ...args, active: true })}</ul>`,
  args: { ...data },
};

export const WithIcon = {
  name: 'With Icon',
  render: (args) => `<ul>${markup({ ...args, icon: 'home' })}</ul>`,
  args: { ...data },
};

export default settings;
