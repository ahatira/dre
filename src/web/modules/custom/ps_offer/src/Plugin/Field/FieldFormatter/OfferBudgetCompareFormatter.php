<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_offer\Trait\OfferBudgetFormattingTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Compact budget formatter for comparison tables.
 *
 * @FieldFormatter(
 *   id = "ps_offer_budget_compare",
 *   label = @Translation("Offer budget (compare table)"),
 *   field_types = {
 *     "decimal",
 *     "float",
 *     "integer"
 *   }
 * )
 */
final class OfferBudgetCompareFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  use OfferBudgetFormattingTrait;

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $entity = $items->getEntity();
    if ($entity === NULL) {
      return [];
    }

    $parts = $this->buildBudgetParts($entity);
    if ($parts === NULL) {
      return [];
    }

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-budget-compare-wrap']],
      'amount' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $parts['amount'],
        '#attributes' => ['class' => ['ps-offer-budget-compare', 'fw-semibold']],
      ],
    ];

    if ($parts['qualifiers'] !== '') {
      $build['qualifiers'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $parts['qualifiers'],
        '#attributes' => ['class' => ['ps-offer-budget-compare__qualifiers', 'small', 'text-muted']],
      ];
    }

    if ($parts['show_info'] && $this->shouldShowComparePriceInfo()) {
      $build['info'] = $this->buildCompareInfoTrigger($entity);
    }

    return [0 => $build];
  }

  /**
   * {@inheritdoc}
   */
  protected function budgetConfig(): ImmutableConfig {
    return $this->configFactory->get('ps_offer.settings');
  }

  private function shouldShowComparePriceInfo(): bool {
    if (!\Drupal::moduleHandler()->moduleExists('ps_compare')) {
      return FALSE;
    }

    return (bool) $this->configFactory->get('ps_compare.settings')->get('display_price_info');
  }

  /**
   * @return array<string, mixed>
   */
  private function buildCompareInfoTrigger(object $entity): array {
    $config = $this->budgetConfig();
    $items = $this->buildCompareInfoItems($entity, $config);
    if ($items === []) {
      return [];
    }

    $title = Html::escape((string) ($config->get('tooltip_title') ?? ''));
    $listItems = array_map(static fn (string $line): string => '<li>' . Html::escape($line) . '</li>', $items);
    $content = '<p class="mb-2 fw-semibold">' . $title . '</p><ul class="mb-0 ps-3">' . implode('', $listItems) . '</ul>';

    return [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#value' => 'i',
      '#attributes' => [
        'type' => 'button',
        'class' => ['ps-offer-budget-compare__info'],
        'aria-label' => (string) ($config->get('price_information') ?? $this->t('Price information')),
        'data-ps-compare-budget-info' => 'true',
        'data-bs-toggle' => 'popover',
        'data-bs-content' => $content,
        'data-bs-html' => 'true',
      ],
    ];
  }

  /**
   * @return list<string>
   */
  private function buildCompareInfoItems(object $entity, ImmutableConfig $config): array {
    $items = [
      (string) ($config->get('label_ht') ?? ''),
    ];

    if ($this->isRentalBudgetOperation($entity)) {
      $chargesIncluded = $entity->hasField('field_budget_cc')
        && !$entity->get('field_budget_cc')->isEmpty()
        && (bool) $entity->get('field_budget_cc')->value;

      $items[] = $chargesIncluded
        ? (string) ($config->get('label_cc') ?? '')
        : (string) ($config->get('label_hc') ?? '');
    }
    else {
      $items[] = (string) ($config->get('label_hd') ?? '');
    }

    $items[] = $this->formatCompareFeesLine($entity, $config);

    return array_values(array_filter($items, static fn (string $line): bool => $line !== ''));
  }

  private function formatCompareFeesLine(object $entity, ImmutableConfig $config): string {
    $prefix = trim((string) ($config->get('fees_prefix') ?? ''));
    $fees = '';

    if ($entity->hasField('field_budget_fees') && !$entity->get('field_budget_fees')->isEmpty()) {
      $fees = trim((string) $entity->get('field_budget_fees')->value);
    }

    if ($fees === '') {
      $fees = $this->isRentalBudgetOperation($entity)
        ? trim((string) ($config->get('default_fees_rental') ?? ''))
        : trim((string) ($config->get('default_fees_sale') ?? ''));
    }

    if ($fees === '') {
      return $prefix;
    }

    return $prefix !== '' ? $prefix . ' ' . $fees : $fees;
  }

}
