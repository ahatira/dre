import { addons } from 'storybook/manager-api';
import psTheme from './theme';

addons.setConfig({
  theme: psTheme,
  sidebar: {
    collapsedRoots: ['base', 'elements', 'components', 'collections', 'layouts', 'pages'],
  },
});
