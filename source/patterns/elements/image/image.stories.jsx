import markup from './image.twig';
import data from './image.yml';

const settings = {
  title: 'Elements/Image',
  tags: ['autodocs'],
  args: { ...data },
};

export const image = {
  name: 'image',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
