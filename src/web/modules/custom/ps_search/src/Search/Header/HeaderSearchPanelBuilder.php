<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Header;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_search\Api\ApiRoutePaths;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\Search\Seo\SearchTransactionToggleBuilder;

/**
 * Builds the property search panel for the site header.
 */
final class HeaderSearchPanelBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly SearchTransactionToggleBuilder $transactionToggleBuilder,
    private readonly SearchPathResolverInterface $searchPathResolver,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Builds panel inner content (toggle + GET form to the search page).
   *
   * @return array<string, mixed>
   *   Render array for ps_header_search_panel #form slot.
   */
  public function buildPanelContent(): array {
    $searchPath = $this->searchPathResolver->getPublicPath();
    $action = Url::fromUserInput($searchPath, [
      'query' => ['operation_type' => 'LOC'],
    ])->toString();

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-header-search__form-inner', 'd-flex', 'flex-column', 'gap-3'],
      ],
      'toggle' => [
        '#type' => 'component',
        '#component' => 'ps_theme:transaction-toggle',
        '#props' => $this->transactionToggleBuilder->buildProps(),
      ],
      'form' => [
        '#type' => 'html_tag',
        '#tag' => 'form',
        '#attributes' => [
          'class' => ['ps-header-search__form', 'd-flex', 'flex-column', 'flex-sm-row', 'gap-2', 'align-items-stretch'],
          'method' => 'get',
          'action' => $action,
        ],
        'operation' => [
          '#type' => 'hidden',
          '#name' => 'operation_type',
          '#value' => 'LOC',
          '#attributes' => ['class' => ['js-ps-header-search-operation']],
        ],
        'locality' => [
          '#type' => 'textfield',
          '#title' => $this->t('Location'),
          '#title_display' => 'invisible',
          '#name' => 'locality[]',
          '#attributes' => [
            'class' => ['form-control'],
            'placeholder' => (string) $this->t('City, department, region…'),
            'maxlength' => 100,
            'autocomplete' => 'off',
          ],
        ],
        'submit' => [
          '#type' => 'html_tag',
          '#tag' => 'button',
          '#value' => (string) $this->t('Search'),
          '#attributes' => [
            'type' => 'submit',
            'class' => ['btn', 'btn-primary'],
          ],
        ],
      ],
      '#attached' => [
        'library' => ['ps_search/header.search'],
        'drupalSettings' => [
          'psSearch' => [
            'apiBase' => ApiRoutePaths::BASE,
            'locationSuggestUrl' => ApiRoutePaths::LOCATION_SUGGEST,
            'searchPath' => $searchPath,
          ],
        ],
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface', 'url.query_args:operation_type'],
        'tags' => ['config:ps_search.seo_url_mappings'],
      ],
    ];
  }

  /**
   * Returns the container class used by the header panel grid wrapper.
   */
  public function getContainerClass(): string {
    return (string) ($this->configFactory->get('ps_theme.settings')->get('container') ?: 'container-fluid');
  }

}
