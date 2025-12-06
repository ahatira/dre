import markup from './tabs.twig';
import data from './tabs.yml';

const settings = {
  title: 'Components/Tabs',
  tags: ['autodocs'],
  argTypes: {
    tabs: {
      description: 'Array of tab objects with id, label, content, active',
      table: { category: 'Content' },
    },
  },
};

export const Default = {
  name: 'Default',
  render: (args) => markup(args),
  args: { ...data },
};

export const FourTabs = {
  name: 'Four Tabs',
  render: (args) => markup(args),
  args: {
    tabs: [
      { id: 'tab1', label: 'Properties', content: 'Properties panel', active: true },
      { id: 'tab2', label: 'Details', content: 'Details panel', active: false },
      { id: 'tab3', label: 'History', content: 'History panel', active: false },
      { id: 'tab4', label: 'Settings', content: 'Settings panel', active: false },
    ],
  },
};

export default settings;
