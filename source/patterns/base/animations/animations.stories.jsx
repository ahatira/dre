import animations from './animations.twig';
import data from './animations.yml';

const settings = {
  title: 'Base/Animations',
};

const Animations = {
  name: 'Animations',
  render: (args) => animations(args),
  args: { ...data },
};

export default settings;
export { Animations };
