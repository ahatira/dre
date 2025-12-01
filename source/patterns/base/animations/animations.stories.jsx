import animations from './animations.twig';

/**
 * Animation & Duration Tokens
 *
 * Design system timing and easing for consistent motion:
 * - 6 Duration tokens: instant to slowest (0.1s to 1s)
 * - 20 Animation presets: fade, scale, slide (8 directions), shake (2 axes), spin, ping, blink, float, bounce, pulse
 * - 35 Easing curves: ease-*, in-out-*, elastic-*, spring-*
 *
 * All tokens defined in `source/props/animations.css` and `source/props/easing.css`
 */

const settings = {
  title: 'Base/Animations',
  parameters: {
    docs: {
      description: {
        component: `
## Animation & Duration System

Complete timing and motion system for consistent animations.

### Duration Tokens (6)

\`\`\`css
transition: transform var(--duration-instant) var(--ease-3); /* 0.1s */
transition: opacity var(--duration-fast) var(--ease-2); /* 0.15s */
transition: all var(--duration-normal) var(--ease-1); /* 0.3s - default */
transition: transform var(--duration-slow) var(--ease-4); /* 0.5s */
transition: height var(--duration-slower) var(--ease-in-out-1); /* 0.75s */
transition: all var(--duration-slowest) var(--ease-elastic-1); /* 1s */
\`\`\`

### Animation Presets (20)

\`\`\`css
/* Fade */
animation: var(--animation-fade-in);
animation: var(--animation-fade-out);

/* Scale */
animation: var(--animation-scale-up);
animation: var(--animation-scale-down);

/* Slide In (4 directions) */
animation: var(--animation-slide-in-up);
animation: var(--animation-slide-in-down);
animation: var(--animation-slide-in-left);
animation: var(--animation-slide-in-right);

/* Slide Out (4 directions) */
animation: var(--animation-slide-out-up);
animation: var(--animation-slide-out-down);
animation: var(--animation-slide-out-left);
animation: var(--animation-slide-out-right);

/* Shake (2 axes) */
animation: var(--animation-shake-x);
animation: var(--animation-shake-y);

/* Continuous */
animation: var(--animation-spin); /* Rotate 360° */
animation: var(--animation-ping); /* Scale + fade pulse */
animation: var(--animation-blink); /* Opacity pulse */
animation: var(--animation-float); /* Vertical float */
animation: var(--animation-bounce); /* Bounce effect */
animation: var(--animation-pulse); /* Scale pulse */
\`\`\`

### Easing Curves (35)

\`\`\`css
/* Standard easing (5) - General purpose */
var(--ease-1) to var(--ease-5)

/* In easing (5) - Accelerate (slow start) */
var(--ease-in-1) to var(--ease-in-5)

/* Out easing (5) - Decelerate (slow end) */
var(--ease-out-1) to var(--ease-out-5)

/* In-Out easing (5) - Smooth acceleration & deceleration */
var(--ease-in-out-1) to var(--ease-in-out-5)

/* Elastic easing (5) - Bounce/spring effect */
var(--ease-elastic-1) to var(--ease-elastic-5)

/* Squish easing (5) - Overshoot effect */
var(--ease-squish-1) to var(--ease-squish-5)

/* Step easing (5) - Discrete jumps */
var(--ease-step-1) to var(--ease-step-5) /* 2, 3, 4, 7, 10 steps */
\`\`\`

**Total**: 35 cubic-bezier curves + step functions for all animation needs.

### Reference

- **Durations**: \`source/props/animations.css\` (6 tokens + 20 presets)
- **Easing**: \`source/props/easing.css\` (35 curves: 30 cubic-bezier + 5 steps)
- **Usage**: Combine durations with easing for custom transitions
        `,
      },
    },
  },
};

const Animations = {
  name: 'Animations & Durations',
  render: (args) => animations(args),
};

export default settings;
export { Animations };
