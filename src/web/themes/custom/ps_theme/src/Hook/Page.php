<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_theme\Utility\FrontRouteHelper;
use Drupal\search\Form\SearchBlockForm;

/**
 * Page and HTML preprocess for public front polish.
 */
final class Page {

  use StringTranslationTrait;

  /**
   * Request attribute key for the active mega-menu render instance.
   */
  private const MEGA_MENU_INSTANCE_KEY = 'ps_mega_menu_instance';

  /**
   * Block IDs placed in the actions region, mapped to site-header slots.
   */
  private const ACTION_BLOCK_SLOTS = [
    'ps_theme_header_search' => 'search',
    'ps_theme_header_actions' => 'actions',
    'ps_theme_header_account' => 'account',
    'ps_theme_header_favorites' => 'favorites',
  ];

  /**
   * Block IDs placed in footer regions, mapped to site-footer slots.
   */
  private const FOOTER_TOP_BLOCK_SLOTS = [
    'ps_theme_footer_prefooter' => 'prefooter',
  ];

  private const FOOTER_BLOCK_SLOTS = [
    'ps_theme_footer_contact' => 'contact',
    'ps_theme_footer_social' => 'social',
    'ps_theme_footer_business' => 'business',
    'ps_theme_footer_about' => 'about',
  ];

  private const FOOTER_BOTTOM_BLOCK_SLOTS = [
    'ps_theme_footer_branding' => 'branding',
    'ps_theme_footer_legal' => 'legal',
    'ps_theme_footer_copyright' => 'copyright',
  ];

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_html')]
  public function preprocessHtml(array &$variables): void {
    if (!FrontRouteHelper::isPublicRoute()) {
      return;
    }

    if (!empty($variables['html_attributes'])) {
      $variables['html_attributes']->addClass('ps-public-route');
    }
    $variables['ps_public_route'] = TRUE;
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_page')]
  public function preprocessPage(array &$variables): void {
    if (!FrontRouteHelper::isPublicRoute()) {
      return;
    }

    $variables['#attached']['library'][] = 'ps_theme/front_public';
    $variables['#attached']['library'][] = 'ps_theme/header';
    $variables['ps_public_route'] = TRUE;
    $variables['ps_hide_header_chrome'] = TRUE;

    $this->prepareNavigationInstances($variables);
    $this->prepareSiteFooterSlots($variables);

    foreach ($variables['page']['header'] ?? [] as $key => $build) {
      if (!is_array($build)) {
        continue;
      }
      $plugin = $build['#plugin_id'] ?? '';
      if (in_array($plugin, [
        'local_tasks_block',
        'local_actions_block',
        'page_title_block',
        'system_breadcrumb_block',
      ], TRUE)) {
        unset($variables['page']['header'][$key]);
      }
    }

    $this->prepareSiteHeaderSlots($variables);
  }

  /**
   * Renders navigation twice so mega-menu IDs stay unique (mobile + desktop).
   *
   * @param array<string, mixed> $variables
   *   Preprocess variables for page.html.twig.
   */
  private function prepareNavigationInstances(array &$variables): void {
    $navigation = $variables['page']['navigation'] ?? [];
    if ($navigation === []) {
      return;
    }

    $variables['ps_site_header_navigation_mobile'] = $this->renderNavigationInstance($navigation, 'mobile');
    $variables['ps_site_header_navigation_desktop'] = $this->renderNavigationInstance($navigation, 'desktop');
    unset($variables['page']['navigation']);
  }

  /**
   * Renders a navigation region copy tagged for mega-menu instance scoping.
   *
   * @param array<string|int, mixed> $region
   *   Navigation region render array.
   * @param string $instance
   *   Mega-menu instance key (mobile or desktop).
   */
  private function renderNavigationInstance(array $region, string $instance): string {
    $request = \Drupal::request();
    $request->attributes->set(self::MEGA_MENU_INSTANCE_KEY, $instance);

    try {
      $region = $this->bustNavigationCache($region, $instance);
      $build = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-site-header__navigation-root', 'ps-site-header__navigation-root--' . $instance],
          'data-ps-menu-instance' => $instance,
        ],
        '#children' => $region,
      ];

      return (string) \Drupal::service('renderer')->renderInIsolation($build);
    }
    finally {
      $request->attributes->remove(self::MEGA_MENU_INSTANCE_KEY);
    }
  }

  /**
   * Ensures each navigation render pass gets a fresh mega-menu markup.
   *
   * @param array<string|int, mixed> $region
   *   Navigation region render array.
   * @param string $instance
   *   Mega-menu instance key (mobile or desktop).
   *
   * @return array<string|int, mixed>
   *   Region with per-instance cache keys.
   */
  private function bustNavigationCache(array $region, string $instance): array {
    foreach ($region as $key => &$child) {
      if (!is_array($child)) {
        continue;
      }
      if (is_string($key) && str_starts_with($key, '#')) {
        continue;
      }
      $child['#cache']['max-age'] = 0;
      $child['#cache']['keys'] = array_merge($child['#cache']['keys'] ?? [], ['ps_nav_' . $instance]);
    }
    return $region;
  }

  /**
   * Splits the actions region into site-header slots.
   *
   * @param array<string, mixed> $variables
   *   Preprocess variables for page.html.twig.
   */
  private function prepareSiteHeaderSlots(array &$variables): void {
    $actions = $variables['page']['actions'] ?? [];

    $search_build = is_array($actions['ps_theme_header_search'] ?? NULL)
      ? $actions['ps_theme_header_search']
      : [];
    unset($variables['page']['actions']['ps_theme_header_search']);
    $variables['ps_site_header_search'] = $this->buildHeaderSearchSlot($search_build, $variables);

    foreach (self::ACTION_BLOCK_SLOTS as $block_id => $slot) {
      if ($slot === 'search') {
        continue;
      }

      if (!isset($actions[$block_id]) || !is_array($actions[$block_id])) {
        continue;
      }

      $build = $actions[$block_id];
      unset($variables['page']['actions'][$block_id]);

      $variables['ps_site_header_' . $slot] = $this->renderRegionMarkup([$block_id => $build]);
    }
  }

  /**
   * Builds search slot markup from the placed core search form block.
   *
   * @param array<string|int, mixed> $block_build
   *   Block render array from the actions region (attachments only).
   * @param array<string, mixed> $variables
   *   Page preprocess variables (attachments bubble up here).
   *
   * @return array<string, string>|null
   *   Search slot markup for site-header, or NULL when unavailable.
   */
  private function buildHeaderSearchSlot(array $block_build, array &$variables): ?array {
    $block = \Drupal::entityTypeManager()->getStorage('block')->load('ps_theme_header_search');
    if (!$block || !$block->status()) {
      return NULL;
    }

    if (!empty($block_build['#attached'])) {
      $variables['#attached'] = BubbleableMetadata::mergeAttachments(
        $variables['#attached'] ?? [],
        $block_build['#attached'],
      );
    }

    $configuration = $block->getPlugin()->getConfiguration();
    $page_id = $configuration['page_id'] ?? NULL;
    $form = \Drupal::formBuilder()->getForm(SearchBlockForm::class, $page_id);
    $form['#attributes']['class'][] = 'ps-header-search__form-inner';

    $variables['#attached']['library'][] = 'ps_theme/header_search';

    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $label = $langcode === 'fr' ? (string) $this->t('Rechercher') : (string) $this->t('Search');

    $renderer = \Drupal::service('renderer');

    $container = \Drupal::config('ps_theme.settings')->get('container') ?: 'container-fluid';
    $panel = [
      '#theme' => 'ps_header_search_panel',
      '#label' => $label,
      '#form' => $form,
      '#container' => $container,
    ];
    $mobileTrigger = [
      '#theme' => 'ps_header_search_trigger',
      '#label' => $label,
      '#variant' => 'mobile_row',
    ];
    $toolbarTrigger = [
      '#theme' => 'ps_header_search_trigger',
      '#label' => $label,
      '#variant' => 'toolbar',
    ];

    return [
      'panel' => (string) $renderer->renderInIsolation($panel),
      'mobile_trigger' => (string) $renderer->renderInIsolation($mobileTrigger),
      'toolbar_trigger' => (string) $renderer->renderInIsolation($toolbarTrigger),
    ];
  }

  /**
   * Splits footer regions into site-footer component slots.
   *
   * @param array<string, mixed> $variables
   *   Preprocess variables for page.html.twig.
   */
  private function prepareSiteFooterSlots(array &$variables): void {
    $variables['ps_site_footer'] = [
      'prefooter' => $this->extractFooterSlot($variables, 'footer_top', self::FOOTER_TOP_BLOCK_SLOTS, 'prefooter'),
      'contact' => $this->extractFooterSlot($variables, 'footer', self::FOOTER_BLOCK_SLOTS, 'contact'),
      'social' => $this->extractFooterSlot($variables, 'footer', self::FOOTER_BLOCK_SLOTS, 'social'),
      'business' => $this->extractFooterSlot($variables, 'footer', self::FOOTER_BLOCK_SLOTS, 'business'),
      'about' => $this->extractFooterSlot($variables, 'footer', self::FOOTER_BLOCK_SLOTS, 'about'),
      'branding' => $this->extractFooterSlot($variables, 'footer_bottom', self::FOOTER_BOTTOM_BLOCK_SLOTS, 'branding'),
      'legal' => $this->extractFooterSlot($variables, 'footer_bottom', self::FOOTER_BOTTOM_BLOCK_SLOTS, 'legal'),
      'copyright' => $this->extractFooterSlot($variables, 'footer_bottom', self::FOOTER_BOTTOM_BLOCK_SLOTS, 'copyright'),
    ];

    $variables['ps_has_site_footer'] = (bool) array_filter($variables['ps_site_footer']);
  }

  /**
   * Extracts and renders a single footer slot from a region.
   *
   * @param array<string, mixed> $variables
   *   Page preprocess variables.
   * @param string $region_key
   *   Region machine name.
   * @param array<string, string> $slot_map
   *   Block ID to slot name map.
   * @param string $slot
   *   Slot name to extract.
   */
  private function extractFooterSlot(array &$variables, string $region_key, array $slot_map, string $slot): ?string {
    $block_id = array_search($slot, $slot_map, TRUE);
    if ($block_id === FALSE) {
      return NULL;
    }

    $region = $variables['page'][$region_key] ?? [];
    if (!isset($region[$block_id]) || !is_array($region[$block_id])) {
      return NULL;
    }

    $build = $region[$block_id];
    unset($variables['page'][$region_key][$block_id]);

    return $this->renderRegionMarkup([$block_id => $build]);
  }

  /**
   * Renders a block region once so markup can be printed in multiple places.
   *
   * @param array<string|int, mixed> $region
   *   Region render array from the page layout.
   */
  private function renderRegionMarkup(array $region): ?string {
    if ($region === []) {
      return NULL;
    }

    $build = [
      '#type' => 'container',
      '#children' => $region,
    ];

    return (string) \Drupal::service('renderer')->render($build);
  }

}
