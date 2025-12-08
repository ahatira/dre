import backgroundsTemplate from './backgrounds.twig';
import data from './backgrounds.yml';

const settings = {
  title: 'Base/Backgrounds',
};

const Backgrounds = {
  name: 'Backgrounds',
  render: (args) => backgroundsTemplate(args),
  args: { ...data },
};

export default settings;
export { Backgrounds };
