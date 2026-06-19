<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\ps_search\Service\SearchSolrCircuitBreaker;
use Drupal\search_api\Event\QueryPreExecuteEvent;
use Drupal\search_api\Event\SearchApiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Aborts Search API queries when Solr is known to be unavailable.
 */
final class SearchSolrQueryGuardSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly SearchSolrCircuitBreaker $circuitBreaker,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      SearchApiEvents::QUERY_PRE_EXECUTE => 'onPreExecute',
    ];
  }

  /**
   * Aborts offers index queries when Solr is already marked unavailable.
   */
  public function onPreExecute(QueryPreExecuteEvent $event): void {
    if ($this->circuitBreaker->isUnavailable()) {
      $event->getQuery()->abort();
    }
  }

}
