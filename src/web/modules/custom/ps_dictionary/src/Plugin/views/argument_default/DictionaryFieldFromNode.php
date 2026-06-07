<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\views\Attribute\ViewsArgumentDefault;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default argument: dictionary code from the node on the current route.
 *
 * Used by Views contextual filters on ps_dictionary field columns (e.g.
 * field_operation_type_value) to load similar offers on offer detail pages.
 */
#[ViewsArgumentDefault(
  id: 'dictionary_field_from_node',
  title: new TranslatableMarkup('Dictionary field value from node page'),
)]
final class DictionaryFieldFromNode extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RouteMatchInterface $routeMatch,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getArgument(): ?string {
    $node = $this->routeMatch->getParameter('node') ?? $this->routeMatch->getParameter('node_preview');
    if (!$node instanceof NodeInterface) {
      return NULL;
    }

    $table = (string) ($this->argument->table ?? '');
    if (!str_starts_with($table, 'node__')) {
      return NULL;
    }

    $field_name = substr($table, strlen('node__'));
    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return NULL;
    }

    CacheableMetadata::createFromRenderArray($this->view->element)
      ->merge(CacheableMetadata::createFromObject($node))
      ->applyTo($this->view->element);

    return (string) $node->get($field_name)->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return ['url'];
  }

}
