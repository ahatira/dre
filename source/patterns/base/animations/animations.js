/**
 * Animation Demo Behavior
 *
 * Handles interactive animation preset demonstrations.
 * Triggers animations on click for Storybook animation showcase.
 */

((Drupal) => {
  Drupal.behaviors.animationDemo = {
    attach(context) {
      // Handle animation preset triggers
      const triggers = context.querySelectorAll('.trigger-btn[data-animation]');

      triggers.forEach((trigger) => {
        // Prevent multiple attachments
        if (trigger.dataset.animationAttached) {
          return;
        }
        trigger.dataset.animationAttached = 'true';

        trigger.addEventListener('click', function () {
          const animationName = this.dataset.animation;
          const target = this.querySelector('.animated-preset');

          if (!target) {
            return;
          }

          // Remove any existing animation
          target.style.animation = 'none';

          // Force reflow to restart animation
          void target.offsetWidth;

          // Apply animation from CSS custom property
          target.style.animation = `var(--animation-${animationName})`;

          // Remove animation after it completes
          target.addEventListener(
            'animationend',
            function handler() {
              target.style.animation = 'none';
              target.removeEventListener('animationend', handler);
            },
            { once: true }
          );
        });
      });
    },
  };
})(Drupal);
