<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Theme\ComponentPluginManager;
use Drupal\ui_patterns\Element\ComponentElementBuilder;
use Drupal\views_promo_card\Entity\PromoCardInterface;

/**
 * Renders promo cards from SDC patterns (UI Patterns).
 */
final class CardRenderer {

  /**
   * Constructs a CardRenderer.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ComponentElementBuilder $componentElementBuilder,
    private readonly PatternRegistry $patternRegistry,
    private readonly PatternIconHelper $patternIconHelper,
    private readonly ComponentPluginManager $componentPluginManager,
  ) {}

  /**
   * Builds a promo card render array.
   *
   * @return array<string, mixed>|null
   *   Render array or NULL when card is empty/disabled.
   */
  public function build(PromoCardInterface $card): ?array {
    if (!$card->status()) {
      return NULL;
    }

    $pattern_id = $card->getPatternId();
    $ui_patterns = $card->getUiPatterns();
    if ($pattern_id === '' || $ui_patterns === [] || !$this->patternRegistry->isValidPattern($pattern_id)) {
      return NULL;
    }

    return $this->buildPattern($card, $pattern_id, $ui_patterns);
  }

  /**
   * Builds an SDC-based promo card render array.
   *
   * @param \Drupal\views_promo_card\Entity\PromoCardInterface $card
   *   The promo card entity.
   * @param string $pattern_id
   *   The SDC pattern ID.
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   *
   * @return array<string, mixed>|null
   *   Render array or NULL when the pattern is empty.
   */
  public function buildPattern(PromoCardInterface $card, string $pattern_id, array $ui_patterns): ?array {
    $ui_patterns = $this->patternIconHelper->normalizeUiPatterns($ui_patterns + ['component_id' => $pattern_id]);
    $configuration = $ui_patterns + ['component_id' => $pattern_id];
    $build = [
      '#type' => 'component',
      '#component' => $pattern_id,
      '#ui_patterns' => $configuration,
      '#attributes' => [
        'class' => ['promo-card-slot'],
      ],
      '#cache' => [
        'tags' => ['promo_card_list', 'promo_card:' . $card->id()],
        'contexts' => ['languages:language_interface'],
      ],
    ];
    $build = $this->componentElementBuilder->build($build);
    if (empty($build['#props']) && empty($build['#slots'])) {
      return NULL;
    }
    return $build;
  }

  /**
   * Loads and builds a promo card by ID.
   */
  public function buildById(string $card_id): ?array {
    $card = $this->entityTypeManager->getStorage('promo_card')->load($card_id);
    return $card instanceof PromoCardInterface ? $this->build($card) : NULL;
  }

  /**
   * Builds a preview render array from raw pattern configuration.
   *
   * @param string $pattern_id
   *   The SDC pattern ID.
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   *
   * @return array<string, mixed>|null
   *   Preview render array or NULL when the pattern is invalid.
   */
  public function buildPreview(string $pattern_id, array $ui_patterns): ?array {
    if (!$this->patternRegistry->isValidPattern($pattern_id)) {
      return NULL;
    }
    $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
    $ui_patterns['props'] = $this->fillPreviewPropDefaults($pattern_id, $props);
    $ui_patterns = $this->patternIconHelper->normalizeUiPatterns($ui_patterns + ['component_id' => $pattern_id]);
    $configuration = $ui_patterns + ['component_id' => $pattern_id];
    $build = [
      '#type' => 'component',
      '#component' => $pattern_id,
      '#ui_patterns' => $configuration,
    ];
    $build = $this->componentElementBuilder->build($build);
    return $build;
  }

  /**
   * Fills missing required SDC props with admin preview placeholders.
   *
   * @param array<string, mixed> $props
   *   Submitted component props.
   *
   * @return array<string, mixed>
   *   Props with placeholders for empty required fields.
   */
  private function fillPreviewPropDefaults(string $pattern_id, array $props): array {
    $placeholders = [
      'title' => 'Preview title',
      'body' => 'Preview body text.',
      'card_title' => 'Preview title',
      'card_body' => 'Preview body text.',
      'cta_label' => 'Preview button',
      'cta_url' => '#',
      'cta_button_style' => 'outline',
      'button_label' => 'Preview button',
      'button_url' => '#',
      'button_style' => 'primary',
      'link_type' => '',
      'modal_id' => '',
    ];
    $url_props = ['cta_url', 'button_url'];

    try {
      $definition = $this->componentPluginManager->find($pattern_id)->getPluginDefinition();
      $required = $definition['props']['required'] ?? [];
      if (!is_array($required)) {
        return $props;
      }
      foreach ($required as $key) {
        if (!is_string($key) || $key === '') {
          continue;
        }
        if ($this->extractPropString($props, $key) !== '') {
          continue;
        }
        $placeholder = $placeholders[$key] ?? ('[' . $key . ']');
        if (in_array($key, $url_props, TRUE)) {
          $props[$key] = UiPatternsPropBuilder::url($placeholder);
        }
        elseif (in_array($key, ['button_style', 'cta_button_style', 'background', 'text_align', 'cta_width', 'border', 'elevation', 'icon_size', 'button_width'], TRUE)) {
          $props[$key] = UiPatternsPropBuilder::select($placeholder);
        }
        else {
          $props[$key] = UiPatternsPropBuilder::textfield($placeholder);
        }
      }
    }
    catch (\Exception) {
      // Keep submitted props when the pattern cannot be loaded.
    }

    return $props;
  }

  /**
   * Reads a scalar prop value from UI Patterns prop storage.
   */
  private function extractPropString(array $props, string $key): string {
    $prop = $props[$key] ?? NULL;
    if (!is_array($prop)) {
      return is_string($prop) ? trim($prop) : '';
    }
    $source = $prop['source'] ?? NULL;
    if (!is_array($source)) {
      return '';
    }
    return trim((string) ($source['value'] ?? ''));
  }

  /**
   * Builds an admin preview for a card, ignoring enabled status.
   *
   * @return array<string, mixed>|null
   *   Preview render array or NULL when the card has no valid pattern.
   */
  public function buildAdminPreview(PromoCardInterface $card): ?array {
    $pattern_id = $card->getPatternId();
    $ui_patterns = $card->getUiPatterns();
    if ($pattern_id === '' || $ui_patterns === []) {
      return NULL;
    }
    return $this->buildPreview($pattern_id, $ui_patterns);
  }

}
