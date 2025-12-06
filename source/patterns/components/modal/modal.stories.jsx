import markup from './modal.twig';
import data from './modal.yml';

const settings = {
  title: 'Components/Modal',
  tags: ['autodocs'],
};

export const Default = {
  name: 'Modal',
  render: (args) => markup({ ...args, show: true }),
  args: { ...data },
};

export const Small = {
  name: 'Small Size',
  render: (args) => markup({ ...args, show: true, size: 'sm' }),
  args: { ...data },
};

export const Large = {
  name: 'Large Size',
  render: (args) => markup({ ...args, show: true, size: 'lg' }),
  args: { ...data },
};

export default settings;
