/**
 * Video (Molecule)
 *
 * Video.js-powered responsive video component with YouTube/Vimeo/HTML5 support.
 * Auto-detects provider from URL, features responsive sizing, custom controls theme,
 * and full accessibility support. Compatible with Drupal Media entities.
 */

import videoTemplate from './video.twig';
import data from './video.yml';
import './video.css';
import './video-theme.css';

export default {
  title: 'Components/Video',
  tags: ['autodocs'],
  parameters: {
    layout: 'padded',
    docs: {
      description: {
        component:
          'Video.js-powered responsive video player supporting YouTube, Vimeo, and HTML5 videos. Includes custom PS Theme styling, responsive sizing (fluid mode), keyboard controls, and full WCAG 2.2 accessibility. Seamlessly integrates with Drupal Media entities.',
      },
    },
  },
  render: (args) => videoTemplate(args),
  argTypes: {
    // Content
    url: {
      control: 'text',
      description:
        'Full video URL (YouTube/Vimeo watch URL) or direct file path (MP4/WebM). Provider auto-detected.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    poster: {
      control: 'text',
      description:
        'Thumbnail image URL for preview state (RECOMMENDED for lazy loading). Shows before video loads.',
      table: {
        category: 'Content',
        type: { summary: 'string' },
      },
    },
    title: {
      control: 'text',
      description: 'Accessible title/description for the video (used in aria-label).',
      table: {
        category: 'Content',
        type: { summary: 'string' },
        defaultValue: { summary: 'Video' },
      },
    },
    sources: {
      control: 'object',
      description:
        'For HTML5 videos: Array of source objects [{src, type}] for multi-format browser compatibility (MP4/WebM/OGG).',
      table: {
        category: 'Content',
        type: { summary: 'array' },
      },
    },
    // Appearance
    aspect_ratio: {
      control: { type: 'select' },
      options: ['16:9', '4:3', '1:1', '21:9'],
      description: 'Video container aspect ratio.',
      table: {
        category: 'Appearance',
        type: { summary: 'string' },
        defaultValue: { summary: '16:9' },
      },
    },
    // Behavior
    controls: {
      control: 'boolean',
      description: 'Show Video.js control bar (play/pause, volume, progress, fullscreen).',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'true' },
      },
    },
    autoplay: {
      control: 'boolean',
      description: 'Autoplay video on load (disables lazy loading, requires muted).',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    loop: {
      control: 'boolean',
      description: 'Loop video playback (HTML5 videos).',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
    muted: {
      control: 'boolean',
      description: 'Mute video audio.',
      table: {
        category: 'Behavior',
        type: { summary: 'boolean' },
        defaultValue: { summary: 'false' },
      },
    },
  },
};

/**
 * Default: YouTube video with Video.js player
 */
export const Default = {
  args: data,
};

/**
 * Native HTML5 Video: Direct MP4 file with controls
 */
export const NativeHTML5 = {
  args: {
    url: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
    poster: '/images/16-9.jpg',
    aspect_ratio: '16:9',
    title: 'Property Walkthrough - Modern Office Space',
    controls: true,
  },
};

/**
 * Aspect Ratios: Single-line comparison at 100px height
 */
export const AspectRatios = {
  render: () => `
    <div style="display:flex; gap: var(--size-6); align-items: flex-start; overflow-x: auto; padding-bottom: var(--size-2);">
      <div style="flex:0 0 auto; border: 1px solid var(--border-default); background: var(--white); padding: var(--size-4);">
        <div style="margin-bottom: var(--size-3); font-weight: var(--font-weight-600);">16:9 (Wide)</div>
        ${videoTemplate({
          url: 'https://www.youtube.com/watch?v=W5mAwAj6NnM',
          poster: 'https://img.youtube.com/vi/W5mAwAj6NnM/maxresdefault.jpg',
          aspect_ratio: '16:9',
          fixed_height: '100px',
          title: '16:9 Wide Format',
        })}
      </div>
      <div style="flex:0 0 auto; border: 1px solid var(--border-default); background: var(--white); padding: var(--size-4);">
        <div style="margin-bottom: var(--size-3); font-weight: var(--font-weight-600);">4:3 (Classic)</div>
        ${videoTemplate({
          url: 'https://www.youtube.com/watch?v=W5mAwAj6NnM',
          poster: '/images/4-3.jpg',
          aspect_ratio: '4:3',
          fixed_height: '100px',
          title: '4:3 Classic Format',
        })}
      </div>
      <div style="flex:0 0 auto; border: 1px solid var(--border-default); background: var(--white); padding: var(--size-4);">
        <div style="margin-bottom: var(--size-3); font-weight: var(--font-weight-600);">1:1 (Square)</div>
        ${videoTemplate({
          url: 'https://www.youtube.com/watch?v=W5mAwAj6NnM',
          poster: '/images/1-1.jpg',
          aspect_ratio: '1:1',
          fixed_height: '100px',
          title: '1:1 Square Format',
        })}
      </div>
      <div style="flex:0 0 auto; border: 1px solid var(--border-default); background: var(--white); padding: var(--size-4);">
        <div style="margin-bottom: var(--size-3); font-weight: var(--font-weight-600);">21:9 (Ultrawide)</div>
        ${videoTemplate({
          url: 'https://www.youtube.com/watch?v=W5mAwAj6NnM',
          poster: '/images/16-9.jpg',
          aspect_ratio: '21:9',
          fixed_height: '100px',
          title: '21:9 Ultrawide Format',
        })}
      </div>
    </div>
  `,
};

/**
 * Autoplay with Muted Audio
 */
export const Autoplay = {
  args: {
    url: 'https://www.youtube.com/watch?v=W5mAwAj6NnM',
    poster: '/images/16-9.jpg',
    aspect_ratio: '16:9',
    title: 'Autoplay Demo',
    autoplay: true,
    muted: true,
    loop: true,
  },
};
