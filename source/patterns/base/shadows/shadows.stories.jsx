import shadows from './shadows.twig';
import data from './shadows.yml';

const settings = {
  title: 'Base/Shadows',
};

const Shadows = {
  name: 'Shadows',
  render: (args) => shadows(args),
  args: { ...data },
};

export default settings;
export { Shadows };
