<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\filter\FilterFormatInterface;

/**
 * Hooks for Filter support.
 */
class Filter {

  public function __construct(
    protected RouteMatchInterface $currentRouteMatch,
    protected AccountProxyInterface $currentUser,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   *
   * Get all filter format tips as tabs and tab content.
   */
  #[Hook('preprocess_filter_tips')]
  public function preprocess(array &$variables): void {
    /** @var \Drupal\filter\FilterFormatInterface|null $current_format */
    $current_format = $this->currentRouteMatch->getParameter('filter_format');
    $current_format_id = $current_format ? $current_format->id() : FALSE;

    $build = [
      '#type' => 'component',
      '#component' => 'ui_suite_bnppre:nav',
      '#props' => [
        'variant' => 'tabs',
        'items' => [],
      ],
      '#slots' => [
        'tab_content' => [],
      ],
      '#attributes' => [
        'class' => [
          'mb-3',
        ],
      ],
    ];

    foreach (\filter_formats($this->currentUser) as $format_id => $format) {
      // Set the current format ID to the first format.
      if (!$current_format_id) {
        $current_format_id = $format_id;
      }
      $active = $current_format_id === $format_id;

      $build['#props']['items'][] = $this->getTab($format, $active);
      $build['#slots']['tab_content'][] = $this->getPane($format);
    }

    $variables['tips'] = $build;
  }

  /**
   * Get a tab.
   *
   * @param \Drupal\filter\FilterFormatInterface $format
   *   The filter format.
   * @param bool $active
   *   If the tab should be active or not.
   *
   * @return array
   *   The tab element.
   */
  protected function getTab(FilterFormatInterface $format, bool $active): array {
    return [
      'title' => $format->label(),
      'url' => Url::fromRoute('filter.tips', [
        'filter_format' => $format->id(),
      ])->toString(),
      'link_attributes' => [
        'class' => [
          $active ? 'active' : '',
        ],
      ],
    ];
  }

  /**
   * Get a pane.
   *
   * @param \Drupal\filter\FilterFormatInterface $format
   *   The filter format.
   *
   * @return array
   *   The pane element.
   */
  protected function getPane(FilterFormatInterface $format): array {
    $tips = [];
    // Iterate over each format's enabled filters.
    /** @var \Drupal\filter\FilterPluginCollection $filters */
    $filters = $format->filters();
    // @phpstan-ignore-next-line
    foreach ($filters->getAll() as $filter) {
      /** @var \Drupal\filter\Plugin\FilterBase $filter */
      // Ignore filters that are not enabled.
      if (!$filter->status) {
        continue;
      }

      $tip = $filter->tips(TRUE);
      if (isset($tip)) {
        $tips[] = ['#markup' => $tip];
      }
    }

    return [
      '#theme' => 'item_list',
      '#items' => $tips,
    ];
  }

}
