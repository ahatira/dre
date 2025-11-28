import { create } from 'storybook/theming/create';
import logo from '../logo.svg';

export default create({
  base: 'light',
  // Branding (adapted from Surface)
  brandTitle: 'BNP Paribas RealEstate',
  brandUrl: 'https://realestate.bnpparibas/',
  brandImage: logo,
  brandTarget: '_blank',
  fontBase: '"Helvetica", sans-serif',
  fontCode: 'monospace',
  // Use BNP primary green and dark green for accenting Storybook UI.
  colorPrimary: '#00A862',
  colorSecondary: '#007A53',
});
