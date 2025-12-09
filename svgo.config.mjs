/**
 * SVGO Configuration for PS Theme Icons
 * 
 * Optimizes SVG icons by:
 * - Removing hardcoded fill/stroke colors (allows currentColor)
 * - Preserving viewBox for proper scaling
 * - Cleaning up unnecessary attributes
 * - Multi-pass optimization for maximum compression
 * 
 * Based on Bootstrap Icons approach
 */
export default {
  multipass: true, // Run optimizations multiple times for better results
  js2svg: {
    pretty: true,
    indent: 2,
    eol: 'lf'
  },
  plugins: [
    {
      name: 'preset-default',
      params: {
        overrides: {
          removeUnknownsAndDefaults: {
            keepDataAttrs: false, // Remove data-* attributes
            keepRoleAttr: true    // Keep role attribute for accessibility
          }
        }
      }
    },
    // Preserve viewBox (prevent removal)
    {
      name: 'removeViewBox',
      active: false
    },
    // Additional plugins not in preset-default
    'cleanupListOfValues',
    {
      name: 'removeAttrs',
      params: {
        attrs: [
          'fill',        // Remove hardcoded fill colors
          'stroke',      // Remove hardcoded stroke colors
          'clip-rule'    // Remove unnecessary clip-rule
        ]
      }
    },
    // Normalize SVG attributes for consistency
    {
      name: 'convertStyleToAttrs',
      params: {
        keepImportant: false
      }
    },
    // Remove empty containers
    'removeEmptyContainers',
    // Merge paths when possible
    'mergePaths',
    // Round numbers to reduce file size
    {
      name: 'cleanupNumericValues',
      params: {
        floatPrecision: 2
      }
    }
  ]
};
