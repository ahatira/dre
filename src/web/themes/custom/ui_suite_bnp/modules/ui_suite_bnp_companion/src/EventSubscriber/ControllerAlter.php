<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_companion\EventSubscriber;

use Drupal\ui_suite_bnp\Utility\Bootstrap;
use Drupal\ui_suite_bnp\Utility\Element;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alter controllers responses.
 */
class ControllerAlter implements EventSubscriberInterface {

  public const int KERNEL_WEIGHT = 5;

  /**
   * Alter controllers responses.
   */
  public function onView(ViewEvent $event): void {
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');

    if ($route == 'layout_builder.choose_block') {
      $build = $event->getControllerResult();
      if (\is_array($build)) {
        $element = Element::create($build);
        // Create content block link.
        if (isset($element->add_block)
          && $element->add_block instanceof Element
          && $element->add_block->isType('link')
        ) {
          $element->add_block->addClass([
            'btn',
            'btn-primary',
            'mb-3',
          ]);
          $element->add_block->setIcon(Bootstrap::icon('plus-lg'));
        }

        // Block categories details.
        if (isset($element->block_categories) && $element->block_categories instanceof Element) {
          $categories = $element->block_categories->children();
          foreach ($categories as $category) {
            if ($category->isType('details')) {
              $category->setProperty('isOffcanvas', TRUE);
              // Close details by default if not from Layout Builder Browser.
              if ($category->getContext('usb_details_closed', TRUE)) {
                $category->setProperty('open', FALSE);
              }
            }

            if (
              isset($category->links)
              && $category->links instanceof Element
              && $category->links->getProperty('theme') == 'links'
            ) {
              $category->links->appendProperty('context', [
                'usb_suggestion' => 'links__layout_builder_links',
              ]);
            }
          }
        }
        $event->setControllerResult($build);
      }
    }
    elseif ($route == 'layout_builder.choose_inline_block') {
      $build = $event->getControllerResult();
      if (\is_array($build)) {
        $element = Element::create($build);
        // Block links.
        if (
          isset($element->links)
          && $element->links instanceof Element
          && $element->links->getProperty('theme') == 'links'
        ) {
          $element->links->addClass('mb-3');
          $element->links->appendProperty('context', [
            'usb_suggestion' => 'links__layout_builder_links',
          ]);
        }

        // Back button link.
        if (
          isset($element->back_button)
          && $element->back_button instanceof Element
          && $element->back_button->isType('link')
        ) {
          $element->back_button->addClass([
            'btn',
            'btn-primary',
            'mb-3',
          ]);
          $element->back_button->setIcon(Bootstrap::icon('chevron-left'));
        }
        $event->setControllerResult($build);
      }
    }
    elseif ($route == 'section_library.choose_template_from_library') {
      $build = $event->getControllerResult();
      if (\is_array($build)) {
        $element = Element::create($build);
        // Section library links.
        if (
          isset($element->sections)
          && $element->sections instanceof Element
          && $element->sections->getProperty('theme') == 'links'
        ) {
          $element->sections->appendProperty('context', [
            'usb_suggestion' => 'links__layout_builder_links',
          ]);
        }
        $event->setControllerResult($build);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events = [];
    $events[KernelEvents::VIEW][] = ['onView', static::KERNEL_WEIGHT];
    return $events;
  }

}
