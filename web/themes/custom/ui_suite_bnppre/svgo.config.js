/**
 * SVGO configuration for BNPPRE icons.
 *
 * Goals:
 * - Optimize SVG markup while preserving rendering fidelity.
 * - Keep `viewBox` (required for responsive icon sizing).
 * - Remove empty defs, unused IDs, and unnecessary metadata.
 * - Normalize style syntax before post-processing.
 *
 * Note:
 * - Removal of `fill` attributes is handled in `scripts/bnppre-icon.js`.
 * - `fill-rule` and `clip-rule` are preserved (geometric rendering rules).
 */
module.exports = {
  multipass: true,
  plugins: [
    {
      name: 'preset-default',
      params: {
        overrides: {
          // Remove empty <defs>, <g>, and other containers.
          removeEmptyContainers: true,
          // Smart ID cleanup: preserve IDs referenced in url(#...), remove unused ones.
          cleanupIds: {
            preserve: ['clip', 'mask', 'filter', 'pattern', 'gradient'],
            remove: true,
          },
          // Remove XML declaration.
          removeXMLProcInst: true,
        },
      },
    },
    // Convert inline style into attributes to simplify downstream checks/cleanup.
    'convertStyleToAttrs',
    // Minify path data (shorthand for SVG path commands).
    'convertPathData',
  ],
};
