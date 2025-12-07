import backgroundsTemplate from './backgrounds.twig';
import data from './backgrounds.yml';

const settings = {
  title: 'Base/Backgrounds',
};

const Backgrounds = {
  name: 'Backgrounds',
  render: (args) => backgroundsTemplate(args),
  args: { ...data },
  parameters: {
    tags: ['autodocs'],
    layout: 'fullscreen',
    docs: {
      description: {
        component:
          'Comprehensive background color utilities system for semantic and neutral backgrounds.',
        story:
          'Complete reference for all background utility classes, including semantic colors (primary, secondary, success, danger, warning, info, gold) at three levels (subtle, base, emphasis), and neutral backgrounds.',
      },
    },
  },
};

export default settings;
export { Backgrounds };
