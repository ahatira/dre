import aspects from './aspects.twig';
import data from './aspects.yml';

const settings = {
  title: 'Base/Aspect ratios',
};

const Aspects = {
  name: 'Aspect ratios',
  render: (args) => aspects(args),
  args: { ...data },
};

export default settings;
export { Aspects };
