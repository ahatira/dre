<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Plugin\views\filter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_feature\Plugin\views\query\FeatureDefinitionEntityQuery;
use Drupal\views\Plugin\views\filter\InOperator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base in-operator filter for feature definition Views.
 */
abstract class FeatureDefinitionInOperatorFilterBase extends InOperator {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function acceptExposedInput($input): bool {
    if (empty($this->options['exposed'])) {
      return TRUE;
    }

    $identifier = $this->options['expose']['identifier'];
    $value = $input[$identifier] ?? NULL;
    if ($value === '' || $value === NULL || $value === 'All' || $value === []) {
      return FALSE;
    }

    return parent::acceptExposedInput($input);
  }

  /**
   * {@inheritdoc}
   */
  public function validateExposed(&$form, FormStateInterface $form_state): void {
    if (empty($this->options['exposed'])) {
      return;
    }

    $identifier = $this->options['expose']['identifier'];
    $value = $form_state->getValue($identifier);
    if ($value === '' || $value === NULL || $value === []) {
      $form_state->setValue($identifier, 'All');
      $input = $form_state->getUserInput();
      $input[$identifier] = 'All';
      $form_state->setUserInput($input);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    if (!$this->query instanceof FeatureDefinitionEntityQuery) {
      return;
    }
    $this->ensureMyTable();
    if (!is_array($this->value) || $this->value === []) {
      return;
    }
    $this->query->addWhere(
      $this->options['group'],
      $this->realField,
      $this->value,
      strtoupper($this->operator),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getValueOptions(): ?array {
    if (!isset($this->valueOptions)) {
      $this->valueOptions = $this->buildValueOptions();
    }
    return $this->valueOptions;
  }

  /**
   * Builds select options for the filter.
   *
   * @return array<string, string>
   *   Options keyed by stored value.
   */
  abstract protected function buildValueOptions(): array;

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state): void {
    if ($form_state->get('exposed')) {
      $identifier = $this->options['expose']['identifier'];
      $user_input = $form_state->getUserInput();
      if (array_key_exists($identifier, $user_input) && ($user_input[$identifier] === '' || $user_input[$identifier] === [])) {
        unset($user_input[$identifier]);
        $form_state->setUserInput($user_input);
      }
    }

    parent::valueForm($form, $form_state);

    if ($form_state->get('exposed') || $this->value !== []) {
      return;
    }

    $form['value']['#default_value'] = [];
  }

}
