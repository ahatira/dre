<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Prepare header and row cells.
 */
class PreprocessViewsViewTable {

  use StringTranslationTrait;

  public function __construct(
    protected RendererInterface $renderer,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_view_table')]
  public function preprocess(array &$variables): void {
    if (isset($variables['header']) && \is_array($variables['header'])) {
      /** @var array{content?: string, sort_indicator?: array, url?: string, title?: string, wrapper_element?: string} $column */
      foreach ($variables['header'] as $key => $column) {
        $column += [
          'content' => '',
          'sort_indicator' => [],
          'url' => '',
          'title' => '',
          'wrapper_element' => '',
        ];
        $withSortIndicator = !empty($column['sort_indicator']);
        $columnContent = $column['content'] . $this->renderer->render($column['sort_indicator']);

        if ($column['url']) {
          $variables['header'][$key]['preparedContent'] = [
            '#type' => 'html_tag',
            '#tag' => 'a',
            '#value' => new FormattableMarkup($columnContent, []),
            '#attributes' => [
              'href' => $column['url'],
              'title' => $column['title'],
              'rel' => 'nofollow',
              'class' => [
                $withSortIndicator ? 'icon-link' : '',
              ],
            ],
            '#prefix' => $column['wrapper_element'] ? "<{$column['wrapper_element']}>" : '',
            '#suffix' => $column['wrapper_element'] ? "</{$column['wrapper_element']}>" : '',
          ];
        }
        else {
          $variables['header'][$key]['preparedContent'] = [
            '#markup' => new FormattableMarkup($columnContent, []),
            '#prefix' => $column['wrapper_element'] ? "<{$column['wrapper_element']}>" : '',
            '#suffix' => $column['wrapper_element'] ? "</{$column['wrapper_element']}>" : '',
          ];
        }
      }
    }

    if (isset($variables['rows']) && \is_array($variables['rows'])) {
      /** @var array{columns: array} $row */
      foreach ($variables['rows'] as $rowKey => $row) {
        /** @var array{content: array{array{separator?: array, field_output?: array}}, wrapper_element?: string} $column */
        foreach ($row['columns'] as $columnKey => $column) {
          $column += [
            'wrapper_element' => '',
          ];

          $columnContent = '';
          foreach ($column['content'] as $content) {
            $columnContent .= isset($content['separator']) ? $this->renderer->render($content['separator']) : '';
            $columnContent .= isset($content['field_output']) ? $this->renderer->render($content['field_output']) : '';
          }

          $variables['rows'][$rowKey]['columns'][$columnKey]['preparedContent'] = [
            '#markup' => new FormattableMarkup($columnContent, []),
            '#prefix' => $column['wrapper_element'] ? "<{$column['wrapper_element']}>" : '',
            '#suffix' => $column['wrapper_element'] ? "</{$column['wrapper_element']}>" : '',
          ];
        }
      }
    }
  }

}
