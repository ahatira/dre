import exampleTwig from './example.twig';
import data from './example.yml';

export default {
  title: 'Base/Example',
};

export const Example = {
  render: (args) => exampleTwig(args),
  args: data,
};
