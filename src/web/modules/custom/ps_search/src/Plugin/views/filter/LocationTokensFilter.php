<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\search_api\Plugin\views\filter\SearchApiString;
use Drupal\views\Attribute\ViewsFilter;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Applies unified location token filtering on Search API queries.
 *
 * Matches each token with OR across city, postal code and department fields.
 */
#[ViewsFilter('ps_search_location_tokens')]
final class LocationTokensFilter extends SearchApiString {

  /**
   * Whether to hide operator form elements.
   *
   * @var bool
   */
  // phpcs:ignore Drupal.NamingConventions.ValidVariableName.LowerCamelName
  public $no_operator = TRUE;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly LocationSearchFilter $locationSearchFilter,
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
      $container->get('ps_search.location_filter'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, ?array &$options = NULL): void {
    parent::init($view, $display, $options);
    $this->normalizeValue();
  }

  /**
   * {@inheritdoc}
   */
  public function query(): void {
    $tokens = $this->extractFilterTokens();
    if ($tokens === []) {
      return;
    }

    $searchApiQuery = $this->getQuery()?->getSearchApiQuery();
    if ($searchApiQuery === NULL) {
      return;
    }

    $this->locationSearchFilter->applyToQuery($searchApiQuery, $tokens);
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state): void {
    $this->normalizeValue();
    $default = (string) ($this->value['value'] ?? '');

    if ($form_state->get('exposed')) {
      $identifier = $this->options['expose']['identifier'];
      $form['value'] = [
        '#type' => 'textfield',
        '#title' => $this->options['expose']['label'] ?: $this->t('Location tokens'),
        '#default_value' => $default,
        '#description' => $this->t('Comma-separated cities, postal codes or department codes.'),
      ];

      $user_input = $form_state->getUserInput();
      if (!isset($user_input[$identifier])) {
        $user_input[$identifier] = $default;
        $form_state->setUserInput($user_input);
      }
      return;
    }

    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location tokens'),
      '#default_value' => $default,
      '#description' => $this->t('Comma-separated cities, postal codes or department codes.'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function acceptExposedInput($input): bool {
    if (empty($this->options['exposed'])) {
      return TRUE;
    }

    $identifier = $this->options['expose']['identifier'] ?? '';
    if ($identifier === '' || !array_key_exists($identifier, $input)) {
      return FALSE;
    }

    $raw = $input[$identifier];
    if ($raw === '' || $raw === 'All' || $raw === []) {
      return FALSE;
    }

    if (is_array($raw)) {
      $tokens = array_values(array_filter(array_map('strval', $raw)));
      if ($tokens === []) {
        return FALSE;
      }
      $this->value = [
        'min' => '',
        'max' => '',
        'value' => implode(',', $tokens),
      ];
      return TRUE;
    }

    $this->value = [
      'min' => '',
      'max' => '',
      'value' => (string) $raw,
    ];
    return TRUE;
  }

  /**
   * Ensures numeric parent always receives an array value structure.
   */
  private function normalizeValue(): void {
    if (is_string($this->value)) {
      $this->value = [
        'min' => '',
        'max' => '',
        'value' => $this->value,
      ];
    }
  }

  /**
   * Parses exposed or configured filter values into location tokens.
   *
   * @return list<string>
   *   Normalized location tokens.
   */
  private function extractFilterTokens(): array {
    $this->normalizeValue();
    $value = $this->value;
    if (!is_array($value)) {
      return $this->locationSearchFilter->extractTokens($value);
    }

    if (isset($value['value']) && is_string($value['value']) && $value['value'] !== '') {
      return $this->locationSearchFilter->extractTokens($value['value']);
    }

    if (isset($value['min'], $value['max'])) {
      return [];
    }

    return $this->locationSearchFilter->extractTokens($value);
  }

}
