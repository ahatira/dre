<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Handle CSS classes.
 */
class PreprocessPage
{
    public function __construct(
        protected ThemeSettingsProvider $themeSettings,
    ) {
    }

  /**
   * Implements hook_preprocess_HOOK().
   */
    #[Hook('preprocess_maintenance_page')]
    #[Hook('preprocess_page')]
    public function preprocess(array &$variables): void
    {
        $variables['container'] = $this->themeSettings->getSetting('container') ?? 'container';
        $variables['footer_container_class'] =
            $this->themeSettings->getSetting('footer_container') ?? 'container-fluid';

        $footerTop = $variables['page']['footer_top'] ?? [];
        if (is_array($footerTop) && $footerTop !== []) {
            $variables['footer_top_columns'] = $this->buildFooterTopColumns($footerTop);
        }
    }

    /**
     * Group footer top blocks into dynamic columns using weight buckets.
     *
     * Bucket rule:
     * - 0..99 => column 1
     * - 100..199 => column 2
     * - 200..299 => column 3
     * - etc.
     *
     * This keeps a single Drupal region while allowing administrators to place
     * blocks in any column from the block UI using weights.
     */
    private function buildFooterTopColumns(array $footerTop): array
    {
        $buckets = [];

        foreach ($footerTop as $key => $block) {
            if (str_starts_with((string) $key, '#') || !is_array($block)) {
                continue;
            }

            $weight = (int) ($block['#weight'] ?? 0);
            $bucket = (int) floor($weight / 100);
            $bucket = max(0, $bucket);

            $buckets[$bucket][] = $block;
        }

        if ($buckets === []) {
            return [];
        }

        ksort($buckets);

        $columns = [];
        foreach ($buckets as $index => $blocks) {
            $column = [
                '#type' => 'container',
                '#attributes' => [
                    'class' => ['ps-footer__column'],
                    'data-ps-footer-column' => (string) $index,
                ],
            ];

            foreach ($blocks as $delta => $block) {
                $column['block_' . $delta] = $block;
            }

            $columns[] = $column;
        }

        return $columns;
    }
}
