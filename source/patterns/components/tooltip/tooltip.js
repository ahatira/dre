/**
 * Tooltip System (Utility-based)
 *
 * Pure JavaScript tooltip system - works on ANY element with data-ps-tooltip.
 * Auto-injects icon if data-ps-tooltip-icon is present.
 *
 * Usage:
 * <span data-ps-tooltip data-ps-tooltip-content="Help text">Label</span>
 * <h3 data-ps-tooltip data-ps-tooltip-content="Info" data-ps-tooltip-icon="info">Title</h3>
 * <button data-ps-tooltip data-ps-tooltip-content="Click me" data-ps-tooltip-icon="true">CTA</button>
 */

((Drupal, once) => {
  /**
   * Tooltip Behavior
   */
  Drupal.behaviors.psTooltip = {
    attach: (context) => {
      const triggers = once('ps-tooltip', '[data-ps-tooltip]', context);

      triggers.forEach((trigger) => {
        const content = trigger.dataset.psTooltipContent;
        const position = trigger.dataset.psTooltipPosition || 'top';
        const iconAttr = trigger.dataset.psTooltipIcon;

        if (!content) {
          return;
        }

        // Auto-inject icon if requested
        let injectedIcon = null;
        if (iconAttr) {
          const iconName = iconAttr === 'true' ? 'info' : iconAttr;
          injectedIcon = document.createElement('span');
          injectedIcon.className = 'ps-tooltip-icon';
          injectedIcon.setAttribute('data-icon', iconName);
          injectedIcon.setAttribute('aria-hidden', 'true');
          trigger.appendChild(injectedIcon);
          trigger.classList.add('has-tooltip-icon');
        }

        let bubble = null;
        let showTimeout = null;
        let hideTimeout = null;

        /**
         * Create tooltip bubble element
         */
        function createBubble() {
          bubble = document.createElement('div');
          bubble.className = `ps-tooltip-bubble ps-tooltip-bubble--${position}`;
          bubble.textContent = content;
          bubble.setAttribute('role', 'tooltip');
          document.body.appendChild(bubble);
        }

        /**
         * Position tooltip relative to trigger
         *
         * @param {void}
         */
        function positionBubble() {
          if (!bubble) {
            return;
          }

          const triggerRect = trigger.getBoundingClientRect();
          const bubbleRect = bubble.getBoundingClientRect();
          const offset = 8; // --ps-tooltip-bubble-offset

          let top, left;

          switch (position) {
            case 'top':
              top = triggerRect.top + window.scrollY - bubbleRect.height - offset;
              left = triggerRect.left + window.scrollX + triggerRect.width / 2;
              bubble.style.transform = 'translateX(-50%)';
              break;

            case 'bottom':
              top = triggerRect.bottom + window.scrollY + offset;
              left = triggerRect.left + window.scrollX + triggerRect.width / 2;
              bubble.style.transform = 'translateX(-50%)';
              break;

            case 'left':
              top = triggerRect.top + window.scrollY + triggerRect.height / 2;
              left = triggerRect.left + window.scrollX - bubbleRect.width - offset;
              bubble.style.transform = 'translateY(-50%)';
              break;

            case 'right':
              top = triggerRect.top + window.scrollY + triggerRect.height / 2;
              left = triggerRect.right + window.scrollX + offset;
              bubble.style.transform = 'translateY(-50%)';
              break;
          }

          bubble.style.top = `${top}px`;
          bubble.style.left = `${left}px`;
        }

        /**
         * Show tooltip with delay
         */
        function show() {
          clearTimeout(hideTimeout);

          showTimeout = setTimeout(() => {
            if (!bubble) {
              createBubble();
              positionBubble();
            }

            // Force reflow for transition
            bubble.offsetHeight;
            bubble.classList.add('is-visible');

            // Update position on scroll/resize
            window.addEventListener('scroll', positionBubble, true);
            window.addEventListener('resize', positionBubble);
          }, 200); // Small delay for better UX
        }

        /**
         * Hide tooltip with cleanup
         */
        function hide() {
          clearTimeout(showTimeout);

          hideTimeout = setTimeout(() => {
            if (bubble) {
              bubble.classList.remove('is-visible');

              // Remove from DOM after transition
              setTimeout(() => {
                if (bubble?.parentNode) {
                  bubble.parentNode.removeChild(bubble);
                  bubble = null;
                }
              }, 150); // Match CSS transition duration
            }

            window.removeEventListener('scroll', positionBubble, true);
            window.removeEventListener('resize', positionBubble);
          }, 50);
        }

        // Event listeners
        trigger.addEventListener('mouseenter', show);
        trigger.addEventListener('mouseleave', hide);
        trigger.addEventListener('focus', show);
        trigger.addEventListener('blur', hide);

        // Cleanup on detach
        trigger.addEventListener('DOMNodeRemoved', () => {
          if (bubble?.parentNode) {
            bubble.parentNode.removeChild(bubble);
          }
          if (injectedIcon?.parentNode) {
            injectedIcon.parentNode.removeChild(injectedIcon);
          }
        });
      });
    },
  };
})(Drupal, once);
