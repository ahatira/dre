import brand from './brand.twig';
import data from './brand.yml';

const settings = {
  title: 'Base/Brand',
};

const Brand = {
  name: 'Brand Colors',
  render: (args) => brand(args),
  args: { ...data },
};

export default settings;
export { Brand };
