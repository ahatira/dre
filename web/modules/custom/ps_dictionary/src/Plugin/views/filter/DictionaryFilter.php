<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\views\Attribute\ViewsFilter;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\ManyToOne;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter handler for ps_dictionary fields.
 *
 * Provides select, checkboxes, and radio button widgets for filtering
 * by dictionary values. Options are loaded dynamically from the
 * configured dictionary type via DictionaryManager.
 *
 * @ingroup views_filter_handlers
 */
#[ViewsFilter("ps_dictionary_filter")]
class DictionaryFilter extends ManyToOne {

  /**
   * Constructs a DictionaryFilter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    protected readonly DictionaryManagerInterface $dictionaryManager,
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
      $container->get('ps_dictionary.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL): void {
    parent::init($view, $display, $options);

    // Get dictionary type from definition or options.
    $dictionary_type = $this->definition['dictionary_type'] ?? $this->options['dictionary_type'] ?? NULL;

    if ($dictionary_type) {
      // Load options from dictionary manager.
      $this->valueOptions = $this->dictionaryManager->getOptions($dictionary_type, TRUE);
    }
    else {
      $this->valueOptions = [];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions(): array {
    $options = parent::defineOptions();
    $options['dictionary_type'] = ['default' => ''];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state): void {
    parent::buildOptionsForm($form, $form_state);

    // Add dictionary type selector for admin UI.
    $dictionary_types = $this->dictionaryManager->getAvailableTypes();

    $form['dictionary_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Dictionary type'),
      '#description' => $this->t('Select which dictionary provides the filter values.'),
      '#options' => ['' => $this->t('- Auto-detect from field -')] + $dictionary_types,
      '#default_value' => $this->options['dictionary_type'] ?? '',
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Adds dictionary-specific cache tags for automatic invalidation
   * when dictionary entries are updated.
   */
  public function getCacheTags(): array {
    $tags = parent::getCacheTags();
    // Add dictionary cache tags for automatic invalidation.
    $dictionary_type = $this->definition['dictionary_type'] ?? $this->options['dictionary_type'] ?? NULL;
    if ($dictionary_type) {
      $tags[] = 'ps_dictionary:' . $dictionary_type;
    }
    return $tags;
  }

}
