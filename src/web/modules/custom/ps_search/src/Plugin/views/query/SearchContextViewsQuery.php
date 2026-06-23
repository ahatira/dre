<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\views\query;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_search\Contract\SearchQueryExecutorInterface;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\search_api\Plugin\views\query\SearchApiQuery;
use Drupal\views\Attribute\ViewsQuery;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Search API Views query driven by SearchContext instead of exposed BEF filters.
 */
#[ViewsQuery(
  id: 'ps_search_context',
  title: new TranslatableMarkup('Search Context Query'),
  help: new TranslatableMarkup('Builds the Search API query from the resolved SearchContext (v2).'),
)]
final class SearchContextViewsQuery extends SearchApiQuery {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly SearchQueryExecutorInterface $queryExecutor,
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly RequestStack $requestStack,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $plugin = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_search.query_executor'),
      $container->get('ps_search.engine_settings_reader'),
      $container->get('ps_search.map_bounds_resolver'),
      $container->get('request_stack'),
    );

    $plugin->setModuleHandler($container->get('module_handler'));
    $plugin->setMessenger($container->get('messenger'));
    $plugin->setLogger($container->get('logger.channel.search_api'));

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ViewExecutable $view): void {
    if (!$this->shouldApplySearchContext($view)) {
      parent::build($view);
      return;
    }

    $this->view = $view;
    $view->initPager();
    $view->pager->query();

    if ($this->shouldAbort()) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      parent::build($view);
      return;
    }

    $context = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);
    if (!$context instanceof SearchContext) {
      parent::build($view);
      return;
    }

    if (!$context->isValid) {
      $this->abort();
      $view->build_info['query'] = (string) $this->query;
      return;
    }

    $this->query->addTag('ps_search_context_query');
    $legacyBounds = $this->queryExecutor->shouldSkipLegacyMapBounds($context)
      ? NULL
      : $this->mapBoundsResolver->resolveActiveBounds($request);

    $this->queryExecutor->apply($this->query, $context, $legacyBounds);
    $this->queryExecutor->applyListSort($this->query, $context);

    if (!empty($this->options['bypass_access'])) {
      $this->query->setOption('search_api_bypass_access', TRUE);
    }

    if (!empty($this->options['query_tags'])) {
      foreach ($this->options['query_tags'] as $tag) {
        $this->query->addTag($tag);
      }
    }

    $view->build_info['query'] = (string) $this->query;
    $this->query->setOption(
      'search_api_retrieved_field_values',
      array_values($this->retrievedFieldValues),
    );
  }

  private function shouldApplySearchContext(ViewExecutable $view): bool {
    return $this->engineSettings->isSearchContextEnabled()
      && $view->id() === 'ps_search_offers'
      && ($view->current_display ?? '') === 'page_list';
  }

}
