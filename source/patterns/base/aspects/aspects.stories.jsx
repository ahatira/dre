import aspects from './aspects.twig';
import data from './aspects.yml';

const settings = {
  title: 'Base/Aspects',
};

const Aspects = {
  name: 'Aspect Ratios',
  render: (args) => aspects(args),
  args: { ...data },
};

export default settings;
export { Aspects };
