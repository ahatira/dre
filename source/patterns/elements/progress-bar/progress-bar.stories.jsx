import markup from './progress-bar.twig';
import data from './progress-bar.yml';

const settings = {
  title: 'Elements/Progress Bar',
  tags: ['autodocs'],
  args: { ...data },
};

export const progressBar = {
  name: 'progress-bar',
  render: (args) => markup(args),
  args: { ...data },
};

export default settings;
