<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Matches a query string parameter value.
 */
#[Condition(
  id: 'promo_card_request_parameter',
  label: new TranslatableMarkup('Request parameter'),
)]
final class RequestParameterCondition extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a RequestParameterCondition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RequestStack $requestStack,
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
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'parameter' => '',
      'value' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['parameter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Parameter name'),
      '#default_value' => $this->configuration['parameter'] ?? '',
    ];
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Expected value'),
      '#description' => $this->t('Leave empty to match any value for the parameter.'),
      '#default_value' => $this->configuration['value'] ?? '',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['parameter'] = trim((string) $form_state->getValue('parameter'));
    $this->configuration['value'] = trim((string) $form_state->getValue('value'));
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return FALSE;
    }
    $parameter = (string) ($this->configuration['parameter'] ?? '');
    if ($parameter === '') {
      return TRUE;
    }
    if (!$request->query->has($parameter)) {
      return FALSE;
    }
    $expected = (string) ($this->configuration['value'] ?? '');
    if ($expected === '') {
      return TRUE;
    }
    return (string) $request->query->get($parameter) === $expected;
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return $this->t('Request parameter @param', [
      '@param' => (string) ($this->configuration['parameter'] ?? ''),
    ]);
  }

}
