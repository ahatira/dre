/** @type { import('@storybook/html-vite').StorybookConfig } */

const config = {
  stories: ['../source/patterns/**/*.mdx', '../source/patterns/**/*.stories.@(js|jsx|mjs|ts|tsx)'],
  addons: [
    '@storybook/addon-links',
    '@storybook/addon-themes',
    '@storybook/addon-docs',
    '@storybook/addon-a11y',
  ],
  framework: {
    name: '@storybook/html-vite',
    options: {},
  },
  staticDirs: ['../dist', '../source/assets'],
  docs: {
    autodocs: 'tag',
  },
  viteFinal: async (config) => {
    return {
      ...config,
      resolve: {
        ...(config.resolve ?? {}),
        alias: {
          ...(config.resolve?.alias ?? {}),
          path: 'path-browserify',
        },
      },
      build: {
        ...(config.build ?? {}),
        chunkSizeWarningLimit: 2000,
      },
      optimizeDeps: {
        ...(config.optimizeDeps ?? {}),
        include: [
          ...(config.optimizeDeps?.include ?? []),
          'path-browserify',
        ],
      },
    };
  },
};

export default config;
