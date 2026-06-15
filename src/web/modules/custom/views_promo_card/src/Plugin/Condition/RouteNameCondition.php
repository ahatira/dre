<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Matches the current route name.
 */
#[Condition(
  id: 'promo_card_route_name',
  label: new TranslatableMarkup('Route name'),
)]
final class RouteNameCondition extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a RouteNameCondition.
   */
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
  public function defaultConfiguration(): array {
    return ['routes' => ''] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['routes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Route names'),
      '#description' => $this->t('One route name per line (e.g. view.ps_search_offers.page_list).'),
      '#default_value' => $this->configuration['routes'] ?? '',
      '#rows' => 4,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['routes'] = trim((string) $form_state->getValue('routes'));
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    $routes = array_filter(array_map('trim', explode("\n", (string) ($this->configuration['routes'] ?? ''))));
    if ($routes === []) {
      return TRUE;
    }
    $current = $this->routeMatch->getRouteName();
    return $current !== NULL && in_array($current, $routes, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return $this->t('Route name matches configured list');
  }

}
