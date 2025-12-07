import borders from './borders.twig';
import data from './borders.yml';

const settings = {
  title: 'Base/Borders',
};

const Borders = {
  name: 'Borders',
  render: (args) => borders(args),
  args: { ...data },
};

export default settings;
export { Borders };
