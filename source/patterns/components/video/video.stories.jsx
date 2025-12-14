import markup from './video.twig';
import data from './video.yml';

const settings = {
  title: 'Components/Video',
  tags: ['autodocs'],
  argTypes: {
    video_id: {
      control: 'text',
      description: 'YouTube video ID',
      table: { category: 'Content' },
    },
    provider: {
      control: { type: 'select' },
      options: ['youtube', 'vimeo'],
      description: 'Video provider platform',
      table: { category: 'Configuration' },
    },
    aspect_ratio: {
      control: { type: 'select' },
      options: ['16:9', '4:3', '1:1'],
      description: 'Video aspect ratio',
      table: { category: 'Display' },
    },
    title: {
      control: 'text',
      description: 'Video title/label',
      table: { category: 'Content' },
    },
  },
};

export const Default = {
  name: '16:9 YouTube',
  render: (args) => markup(args),
  args: Object.assign({}, data, { aspect_ratio: '16:9' }),
};

export const Square = {
  name: '1:1 Square',
  render: (args) => markup(args),
  args: Object.assign({}, data, { aspect_ratio: '1:1' }),
};

export const Classic = {
  name: '4:3 Classic',
  render: (args) => markup(args),
  args: Object.assign({}, data, { aspect_ratio: '4:3' }),
};

export default settings;
