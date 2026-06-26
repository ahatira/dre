<?php

declare(strict_types=1);

namespace Drupal\ps_seo\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Admin UI tweaks for the SEO configuration hub.
 */
final class SeoAdminHooks {

  private const HUB_BASE_ROUTE = 'ps_seo.admin_overview';

  /**
   * Primary contrib local tasks shown on the SEO hub tab row.
   *
   * @var list<string>
   */
  private const HUB_TASK_IDS = [
    'metatag_defaults',
    'metatag.settings',
    'pathauto.patterns.form',
    'redirect.list',
    'redirect.settings',
    'simple_sitemap.sitemaps',
  ];

  /**
   * Hub tab labels for contrib tasks.
   *
   * @var array<string, \Drupal\Core\StringTranslation\TranslatableMarkup>
   */
  private const TASK_TITLES = [
    'metatag_defaults' => 'Metatags',
    'metatag.settings' => 'Metatag settings',
    'pathauto.patterns.form' => 'Pathauto',
    'redirect.list' => 'Redirects',
    'redirect.settings' => 'Redirect settings',
    'simple_sitemap.sitemaps' => 'Sitemap',
  ];

  /**
   * Attaches primary contrib SEO screens to the ps_seo hub tab row.
   */
  #[Hook('local_tasks_alter')]
  public function localTasksAlter(array &$local_tasks): void {
    foreach (self::HUB_TASK_IDS as $task_id) {
      if (!isset($local_tasks[$task_id])) {
        continue;
      }

      $local_tasks[$task_id]['base_route'] = self::HUB_BASE_ROUTE;
      unset($local_tasks[$task_id]['parent_id']);

      if (isset(self::TASK_TITLES[$task_id])) {
        $local_tasks[$task_id]['title'] = self::TASK_TITLES[$task_id];
      }
    }
  }

}
