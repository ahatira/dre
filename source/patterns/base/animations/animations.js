/**
 * Animation Demo Behavior
 *
 * Handles interactive animation demonstrations in Storybook.
 * Click on animation cards to trigger the animations.
 */

((Drupal) => {
  Drupal.behaviors.animationDemo = {
    attach(context) {
      // Get all animation boxes
      const boxes = context.querySelectorAll('.anim-card__box');

      // Duration circles (trigger fill on click)
      const circles = context.querySelectorAll('.duration-circle');

      boxes.forEach((box) => {
        // Prevent multiple attachments
        if (box.dataset.animationAttached) {
          return;
        }
        box.dataset.animationAttached = 'true';

        // Get the animation name from the data attribute
        const animationName = box.dataset.animation;

        // Click handler to trigger animation
        box.parentElement.addEventListener('click', (e) => {
          e.preventDefault();

          // Remove animation to reset
          box.style.animation = 'none';

          // Force reflow to restart animation
          void box.offsetWidth;

          // Apply animation using CSS custom properties
          box.style.animation = `var(--animation-${animationName})`;

          // Remove animation after it completes
          box.addEventListener(
            'animationend',
            function handler() {
              box.style.animation = 'none';
              box.removeEventListener('animationend', handler);
            },
            { once: true }
          );
        });
      });

      circles.forEach((circle) => {
        if (circle.dataset.durationAttached) {
          return;
        }
        circle.dataset.durationAttached = 'true';

        circle.addEventListener('click', (event) => {
          event.preventDefault();

          // Reset animation and angle
          circle.style.animation = 'none';
          circle.style.setProperty('--angle', '0deg');
          // Force reflow
          void circle.offsetWidth;

          const duration =
            window.getComputedStyle(circle).getPropertyValue('--duration-fill').trim() || '1s';

          circle.style.animation = `duration-fill ${duration} linear forwards`;
        });
      });
    },
  };
})(Drupal);
