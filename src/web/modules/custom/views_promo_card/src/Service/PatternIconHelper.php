<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Theme\ComponentPluginManager;
use Drupal\views_promo_card\Utility\IconIdUtility;

/**
 * Detects icon props on SDC patterns and normalizes admin/render values.
 */
final class PatternIconHelper {

  /**
   * Icon prop names expected by icon-capable SDC components.
   *
   * @var list<string>
   */
  private const ICON_PROP_NAMES = ['icon_pack', 'icon_id'];

  /**
   * Constructs a PatternIconHelper.
   */
  public function __construct(
    private readonly ComponentPluginManager $componentPluginManager,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Whether the UI Icons Picker form element is available.
   */
  public function iconPickerAvailable(): bool {
    return $this->moduleHandler->moduleExists('ui_icons_picker');
  }

  /**
   * Checks whether an SDC pattern exposes icon_pack and icon_id props.
   */
  public function patternHasIconProps(string $pattern_id): bool {
    if ($pattern_id === '') {
      return FALSE;
    }

    try {
      $definition = $this->componentPluginManager->find($pattern_id)->getPluginDefinition();
      $prop_names = array_keys($definition['props']['properties'] ?? []);
      foreach (self::ICON_PROP_NAMES as $icon_prop) {
        if (!in_array($icon_prop, $prop_names, TRUE)) {
          return FALSE;
        }
      }
      return TRUE;
    }
    catch (\Exception) {
      return FALSE;
    }
  }

  /**
   * Returns the configured fallback icon (pack:id), if any.
   */
  public function getDefaultIcon(): string {
    return trim((string) ($this->configFactory->get('views_promo_card.settings')->get('default_icon') ?? ''));
  }

  /**
   * Returns the default icon pack for the icon picker, if configured.
   */
  public function getDefaultIconPack(): string {
    $default_icon = $this->getDefaultIcon();
    if ($default_icon === '') {
      return '';
    }

    $parts = IconIdUtility::splitIconId($default_icon);
    return $parts['pack'] ?? '';
  }

  /**
   * Builds a pack:id default from stored UI Patterns props.
   */
  public function iconFromProps(array $props): string {
    $pack = UiPatternsValueReader::getPropValue(['props' => $props], 'icon_pack');
    $id = UiPatternsValueReader::getPropValue(['props' => $props], 'icon_id');
    if ($pack !== '' && $id !== '') {
      return $pack . ':' . $id;
    }

    if ($this->hasExplicitEmptyIcon(['props' => $props])) {
      return '';
    }

    return $this->getDefaultIcon();
  }

  /**
   * Returns the icon picker default for admin forms.
   *
   * @param array<string, mixed> $ui_patterns
   *   Stored UI Patterns configuration.
   */
  public function getFormIconDefault(array $ui_patterns): string {
    if ($this->hasExplicitEmptyIcon($ui_patterns)) {
      return '';
    }

    $pack = UiPatternsValueReader::getPropValue($ui_patterns, 'icon_pack');
    $id = UiPatternsValueReader::getPropValue($ui_patterns, 'icon_id');
    if ($pack !== '' && $id !== '') {
      return $pack . ':' . $id;
    }

    return $this->getDefaultIcon();
  }

  /**
   * Whether the editor explicitly saved the card without an icon.
   *
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   */
  public function hasExplicitEmptyIcon(array $ui_patterns): bool {
    $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
    if (!array_key_exists('icon_pack', $props) || !array_key_exists('icon_id', $props)) {
      return FALSE;
    }

    return UiPatternsValueReader::getPropValue($ui_patterns, 'icon_pack') === ''
      && UiPatternsValueReader::getPropValue($ui_patterns, 'icon_id') === '';
  }

  /**
   * Ensures icon props are populated before render (fallback when allowed).
   *
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   *
   * @return array<string, mixed>
   *   Normalized configuration.
   */
  public function normalizeUiPatterns(array $ui_patterns): array {
    $pattern_id = (string) ($ui_patterns['component_id'] ?? '');
    if (!$this->patternHasIconProps($pattern_id)) {
      return $ui_patterns;
    }

    $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
    $pack = UiPatternsValueReader::getPropValue($ui_patterns, 'icon_pack');
    $id = UiPatternsValueReader::getPropValue($ui_patterns, 'icon_id');
    if ($pack !== '' && $id !== '') {
      return $ui_patterns;
    }

    if ($this->hasExplicitEmptyIcon($ui_patterns)) {
      return $ui_patterns;
    }

    $icon_id = $this->iconFromProps($props);
    if ($icon_id === '') {
      return $ui_patterns;
    }

    $ui_patterns['props'] = $this->applyIconToProps($props, $icon_id);
    return $ui_patterns;
  }

  /**
   * Merges a submitted icon_picker value into UI Patterns configuration.
   *
   * @param array<string, mixed> $ui_patterns
   *   Submitted UI Patterns values.
   * @param mixed $icon_picker
   *   Raw icon_picker submission.
   *
   * @return array<string, mixed>
   *   Updated UI Patterns values.
   */
  public function mergeSubmittedIcon(array $ui_patterns, mixed $icon_picker): array {
    $pattern_id = (string) ($ui_patterns['component_id'] ?? '');
    if (!$this->patternHasIconProps($pattern_id)) {
      return $ui_patterns;
    }

    $icon_id = IconIdUtility::extractFromSubmission($icon_picker, '');
    if ($icon_id === '') {
      $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
      $ui_patterns['props'] = $this->clearIconProps($props);
      return $ui_patterns;
    }

    $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
    $ui_patterns['props'] = $this->applyIconToProps($props, $icon_id);
    return $ui_patterns;
  }

  /**
   * Applies a pack:id icon to UI Patterns props (icon_pack + icon_id).
   *
   * @param array<string, mixed> $props
   *   Existing props configuration.
   * @param string $icon_id
   *   Icon in pack:id format.
   *
   * @return array<string, mixed>
   *   Updated props.
   */
  public function applyIconToProps(array $props, string $icon_id): array {
    $parts = IconIdUtility::resolveParts($icon_id, '', '');
    if ($parts['pack'] === '' || $parts['id'] === '') {
      return $props;
    }

    $props['icon_pack'] = UiPatternsPropBuilder::textfield($parts['pack']);
    $props['icon_id'] = UiPatternsPropBuilder::textfield($parts['id']);
    return $props;
  }

  /**
   * Persists an explicit "no icon" choice in UI Patterns props.
   *
   * @param array<string, mixed> $props
   *   Existing props configuration.
   *
   * @return array<string, mixed>
   *   Updated props with empty icon_pack and icon_id.
   */
  public function clearIconProps(array $props): array {
    $props['icon_pack'] = UiPatternsPropBuilder::textfield('');
    $props['icon_id'] = UiPatternsPropBuilder::textfield('');
    return $props;
  }

}
