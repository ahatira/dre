<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds dedicated promo card pattern forms (replaces UI Patterns component_form).
 */
final class PromoCardPatternFormBuilder {

  use StringTranslationTrait;

  /**
   * Editor field definitions grouped by section for each pattern family.
   *
   * @var array<string, array<string, array<string, array<string, mixed>>>>
   */
  private const PATTERN_SCHEMAS = [
    'search-push-card' => [
      'content' => [
        'title' => [
          'type' => 'text',
          'title' => 'Title',
          'required' => TRUE,
        ],
        'body' => [
          'type' => 'text_format',
          'title' => 'Body text',
        ],
      ],
      'action' => [
        'cta_label' => [
          'type' => 'text',
          'title' => 'Button label',
          'required' => TRUE,
        ],
        'cta_url' => [
          'type' => 'url',
          'title' => 'Button URL',
          'required' => TRUE,
        ],
        'cta_button_style' => [
          'type' => 'select',
          'title' => 'Button style',
          'options' => [
            'primary' => 'Primary',
            'outline' => 'Outline',
          ],
          'default' => 'outline',
        ],
      ],
      'appearance' => [
        'background' => [
          'type' => 'select',
          'title' => 'Background',
          'options' => self::BACKGROUND_DEFAULT_OPTIONS,
          'default' => 'default',
        ],
        'text_align' => [
          'type' => 'select',
          'title' => 'Text alignment',
          'options' => self::TEXT_ALIGN_LEFT_OPTIONS,
          'default' => 'left',
        ],
        'cta_width' => [
          'type' => 'select',
          'title' => 'Button width',
          'options' => self::CTA_WIDTH_OPTIONS,
          'default' => 'auto',
        ],
        'border' => [
          'type' => 'select',
          'title' => 'Border',
          'options' => self::BORDER_OPTIONS,
          'default' => 'default',
        ],
        'elevation' => [
          'type' => 'select',
          'title' => 'Shadow / elevation',
          'options' => self::ELEVATION_OPTIONS,
          'default' => 'none',
        ],
        'attributes' => [
          'type' => 'attributes',
          'title' => 'Wrapper attributes (advanced)',
          'description' => 'Extra HTML attributes or CSS classes on the card wrapper.',
        ],
      ],
    ],
    'icon-card' => [
      'content' => [
        'icon' => [
          'type' => 'icon_picker',
          'title' => 'Icon',
        ],
        'title' => [
          'type' => 'text',
          'title' => 'Card title',
          'required' => TRUE,
        ],
        'body' => [
          'type' => 'text_format',
          'title' => 'Card body',
        ],
      ],
      'action' => [
        'button_label' => [
          'type' => 'text',
          'title' => 'Button label',
          'required' => TRUE,
        ],
        'button_url' => [
          'type' => 'url',
          'title' => 'Button URL',
          'required' => TRUE,
        ],
        'button_style' => [
          'type' => 'select',
          'title' => 'Button style',
          'options' => [
            'primary' => 'Primary',
            'outline' => 'Outline',
          ],
          'default' => 'primary',
        ],
        'button_width' => [
          'type' => 'select',
          'title' => 'Button width',
          'options' => self::CTA_WIDTH_OPTIONS,
          'default' => 'full',
        ],
      ],
      'advanced' => [
        'link_type' => [
          'type' => 'select',
          'title' => 'Link type',
          'options' => [
            '' => 'Standard link',
            'modal' => 'Bootstrap modal',
            'offcanvas' => 'Offcanvas dialog',
          ],
          'default' => '',
        ],
        'modal_id' => [
          'type' => 'text',
          'title' => 'Modal ID',
          'description' => 'Bootstrap modal ID (without #), when link type is modal.',
        ],
      ],
      'appearance' => [
        'background' => [
          'type' => 'select',
          'title' => 'Background',
          'options' => self::BACKGROUND_TRANSPARENT_OPTIONS,
          'default' => 'transparent',
        ],
        'text_align' => [
          'type' => 'select',
          'title' => 'Text alignment',
          'options' => self::TEXT_ALIGN_CENTER_OPTIONS,
          'default' => 'center',
        ],
        'icon_size' => [
          'type' => 'select',
          'title' => 'Icon size',
          'options' => self::ICON_SIZE_OPTIONS,
          'default' => 'md',
        ],
        'elevation' => [
          'type' => 'select',
          'title' => 'Shadow / elevation',
          'options' => self::ELEVATION_OPTIONS,
          'default' => 'none',
        ],
        'attributes' => [
          'type' => 'attributes',
          'title' => 'Wrapper attributes (advanced)',
          'description' => 'Extra HTML attributes or CSS classes on the card wrapper.',
        ],
      ],
    ],
    'cta-card' => [
      'content' => [
        'title' => [
          'type' => 'text',
          'title' => 'Title',
          'required' => TRUE,
        ],
      ],
      'action' => [
        'button_label' => [
          'type' => 'text',
          'title' => 'Button label',
          'required' => TRUE,
        ],
        'button_url' => [
          'type' => 'url',
          'title' => 'Button URL or modal anchor',
          'required' => TRUE,
        ],
        'button_style' => [
          'type' => 'select',
          'title' => 'Button style',
          'options' => [
            'primary' => 'Primary',
            'outline' => 'Outline',
          ],
          'default' => 'outline',
        ],
        'button_width' => [
          'type' => 'select',
          'title' => 'Button width',
          'options' => self::CTA_WIDTH_OPTIONS,
          'default' => 'full',
        ],
      ],
      'advanced' => [
        'icon' => [
          'type' => 'text',
          'title' => 'Icon name',
        ],
        'modal_id' => [
          'type' => 'text',
          'title' => 'Bootstrap modal ID',
          'description' => 'When set, the button opens this modal instead of linking to the URL.',
        ],
      ],
      'appearance' => [
        'background' => [
          'type' => 'select',
          'title' => 'Background',
          'options' => self::BACKGROUND_DEFAULT_OPTIONS,
          'default' => 'default',
        ],
        'text_align' => [
          'type' => 'select',
          'title' => 'Text alignment',
          'options' => self::TEXT_ALIGN_LEFT_OPTIONS,
          'default' => 'left',
        ],
        'elevation' => [
          'type' => 'select',
          'title' => 'Shadow / elevation',
          'options' => self::ELEVATION_OPTIONS,
          'default' => 'none',
        ],
        'attributes' => [
          'type' => 'attributes',
          'title' => 'Wrapper attributes (advanced)',
          'description' => 'Extra HTML attributes or CSS classes on the card wrapper.',
        ],
      ],
    ],
  ];

  /**
   * Background options when the component preset is the default surface.
   *
   * @var array<string, string>
   */
  private const BACKGROUND_DEFAULT_OPTIONS = [
    'default' => 'Default (component preset)',
    'white' => 'White',
    'muted' => 'Muted grey',
    'dark' => 'Dark',
    'primary' => 'Brand green',
  ];

  /**
   * Background options when transparent is the natural default (icon-card).
   *
   * @var array<string, string>
   */
  private const BACKGROUND_TRANSPARENT_OPTIONS = [
    'transparent' => 'Transparent',
    'white' => 'White box',
    'muted' => 'Muted grey box',
    'dark' => 'Dark box',
    'primary' => 'Brand green box',
  ];

  /**
   * Text alignment with left as first/default label order.
   *
   * @var array<string, string>
   */
  private const TEXT_ALIGN_LEFT_OPTIONS = [
    'left' => 'Left',
    'center' => 'Center',
  ];

  /**
   * Text alignment with center as first/default label order.
   *
   * @var array<string, string>
   */
  private const TEXT_ALIGN_CENTER_OPTIONS = [
    'center' => 'Center',
    'left' => 'Left',
  ];

  /**
   * CTA / button width options.
   *
   * @var array<string, string>
   */
  private const CTA_WIDTH_OPTIONS = [
    'auto' => 'Auto',
    'full' => 'Full width',
  ];

  /**
   * Border style options (search-push-card).
   *
   * @var array<string, string>
   */
  private const BORDER_OPTIONS = [
    'default' => 'Subtle border',
    'none' => 'No border',
    'accent' => 'Accent (brand)',
  ];

  /**
   * Elevation / shadow options.
   *
   * @var array<string, string>
   */
  private const ELEVATION_OPTIONS = [
    'none' => 'Flat',
    'sm' => 'Subtle shadow',
    'md' => 'Raised',
  ];

  /**
   * Icon size presets for icon-card.
   *
   * @var array<string, string>
   */
  private const ICON_SIZE_OPTIONS = [
    'sm' => 'Small (24px)',
    'md' => 'Medium (32px)',
    'lg' => 'Large (48px)',
  ];

  /**
   * Section titles keyed by section machine name.
   *
   * @var array<string, \Stringable|string>
   */
  private const SECTION_TITLES = [
    'content' => 'Content',
    'action' => 'Call to action',
    'advanced' => 'Advanced options',
    'appearance' => 'Appearance',
  ];

  /**
   * Constructs a PromoCardPatternFormBuilder.
   */
  public function __construct(
    private readonly PatternIconHelper $patternIconHelper,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Whether a dedicated editor exists for the given pattern.
   */
  public function supportsPattern(string $pattern_id): bool {
    return $this->resolveSchemaKey($pattern_id) !== NULL;
  }

  /**
   * Builds pattern-specific fieldsets under the pattern form container.
   *
   * @param array<string, mixed> $container
   *   The pattern_form render array (must use #tree = TRUE).
   * @param string $pattern_id
   *   Selected SDC pattern ID.
   * @param array<string, mixed> $ui_patterns
   *   Stored UI Patterns configuration.
   */
  public function buildForm(array &$container, string $pattern_id, array $ui_patterns): void {
    $schema_key = $this->resolveSchemaKey($pattern_id);
    if ($schema_key === NULL) {
      $container['unsupported'] = [
        '#type' => 'item',
        '#markup' => '<p>' . $this->t('No dedicated editor is available for this pattern yet.') . '</p>',
      ];
      return;
    }

    $defaults = UiPatternsValueReader::getPropValues($ui_patterns);
    $schema = self::PATTERN_SCHEMAS[$schema_key];
    $weight = 0;

    foreach ($schema as $section_id => $fields) {
      $section = $this->buildSection($section_id, $fields, $defaults, $pattern_id, $weight);
      if ($section !== NULL) {
        $container[$section_id] = $section;
        $weight += 10;
      }
    }

    $this->applyPatternFieldStates($container, $schema_key);
  }

  /**
   * Converts submitted pattern form values to UI Patterns storage.
   *
   * @param string $pattern_id
   *   Selected pattern ID.
   * @param array<string, mixed> $pattern_form
   *   Submitted pattern_form values.
   *
   * @return array<string, mixed>
   *   UI Patterns component configuration.
   */
  public function valuesToUiPatterns(string $pattern_id, array $pattern_form): array {
    $schema_key = $this->resolveSchemaKey($pattern_id);
    if ($schema_key === NULL) {
      return [
        'component_id' => $pattern_id,
        'variant_id' => NULL,
        'props' => [],
        'slots' => [],
      ];
    }

    $props = [];
    $schema = self::PATTERN_SCHEMAS[$schema_key];

    foreach ($schema as $section_id => $fields) {
      $section_values = is_array($pattern_form[$section_id] ?? NULL) ? $pattern_form[$section_id] : [];
      foreach ($fields as $field_id => $definition) {
        if (($definition['type'] ?? '') === 'icon_picker') {
          continue;
        }
        $raw = $section_values[$field_id] ?? NULL;
        $value = $this->normalizeSubmittedValue($definition, $raw);
        if ($value === '' && ($definition['type'] ?? '') === 'select') {
          $value = (string) ($definition['default'] ?? '');
        }
        $props[$field_id] = $this->buildPropConfig($definition, $value);
      }
    }

    $ui_patterns = [
      'component_id' => $pattern_id,
      'variant_id' => NULL,
      'props' => $props,
      'slots' => [],
    ];

    $icon_picker = $pattern_form['content']['icon'] ?? NULL;
    return $this->patternIconHelper->mergeSubmittedIcon($ui_patterns, $icon_picker);
  }

  /**
   * Builds a form section for a schema group.
   *
   * @param string $section_id
   *   Section machine name.
   * @param array<string, array<string, mixed>> $fields
   *   Field definitions.
   * @param array<string, string> $defaults
   *   Default prop values.
   * @param string $pattern_id
   *   Pattern ID (for icon picker constraints).
   * @param int $weight
   *   Section weight.
   *
   * @return array<string, mixed>|null
   *   Section render array, or NULL when empty.
   */
  private function buildSection(
    string $section_id,
    array $fields,
    array $defaults,
    string $pattern_id,
    int $weight,
  ): ?array {
    $elements = [];
    foreach ($fields as $field_id => $definition) {
      $element = $this->buildFieldElement($field_id, $definition, $defaults, $pattern_id);
      if ($element !== NULL) {
        $elements[$field_id] = $element;
      }
    }

    if ($elements === []) {
      return NULL;
    }

    $section_title = (string) (self::SECTION_TITLES[$section_id] ?? $section_id);
    $is_collapsible = in_array($section_id, ['advanced', 'appearance'], TRUE);
    $section = [
      '#type' => $is_collapsible ? 'details' : 'fieldset',
      '#title' => $this->t($section_title),
      '#tree' => TRUE,
      '#weight' => $weight,
      '#attributes' => ['class' => ['promo-card-pattern-section', 'promo-card-pattern-section--' . $section_id]],
    ] + $elements;

    if ($is_collapsible) {
      $section['#open'] = $section_id === 'appearance';
    }

    if (!in_array($section_id, ['advanced', 'appearance'], TRUE) && $this->moduleHandler->moduleExists('token')) {
      $section['token_help'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => ['site', 'current-user'],
        '#weight' => 100,
      ];
    }

    return $section;
  }

  /**
   * Builds a single field form element.
   *
   * @param string $field_id
   *   Field / prop machine name.
   * @param array<string, mixed> $definition
   *   Field definition from schema.
   * @param array<string, string> $defaults
   *   Default values keyed by prop ID.
   * @param string $pattern_id
   *   Pattern ID.
   *
   * @return array<string, mixed>|null
   *   Field element or NULL when unavailable.
   */
  private function buildFieldElement(
    string $field_id,
    array $definition,
    array $defaults,
    string $pattern_id,
  ): ?array {
    $type = (string) ($definition['type'] ?? 'text');
    $title = (string) ($definition['title'] ?? $field_id);
    $default = $defaults[$field_id] ?? (string) ($definition['default'] ?? '');

    if ($type === 'icon_picker') {
      if (!$this->patternIconHelper->patternHasIconProps($pattern_id) || !$this->patternIconHelper->iconPickerAvailable()) {
        return NULL;
      }
      $props = [];
      foreach (['icon_pack', 'icon_id'] as $icon_prop) {
        if (($defaults[$icon_prop] ?? '') !== '') {
          $props[$icon_prop] = UiPatternsPropBuilder::textfield($defaults[$icon_prop]);
        }
      }
      $element = [
        '#type' => 'icon_picker',
        '#title' => $this->t($title),
        '#default_value' => $this->patternIconHelper->iconFromProps($props),
        '#return_id' => TRUE,
        '#wrapper_attributes' => ['class' => ['views-promo-card-icon-picker']],
      ];
      $default_pack = $this->patternIconHelper->getDefaultIconPack();
      if ($default_pack !== '') {
        $element['#allowed_icon_pack'] = [$default_pack];
      }
      return $element;
    }

    $element = match ($type) {
      'text_format' => [
        '#type' => 'text_format',
        '#default_value' => $default,
        '#format' => 'basic_html',
        '#allowed_formats' => ['basic_html'],
      ],
      'url' => $this->buildUrlElement($default),
      'select' => [
        '#type' => 'select',
        '#options' => $this->normalizeSelectOptions($definition['options'] ?? []),
        '#default_value' => $default !== '' ? $default : (string) ($definition['default'] ?? ''),
      ],
      'attributes' => [
        '#type' => 'textfield',
        '#default_value' => $default,
        '#placeholder' => 'class="my-card" id="promo-push"',
        '#description' => $this->t('HTML attributes with double-quoted values, or a space-separated list of CSS classes.'),
      ],
      default => [
        '#type' => 'textfield',
        '#default_value' => $default,
      ],
    };

    $element['#title'] = $this->t($title);
    if (!empty($definition['required'])) {
      $element['#required'] = TRUE;
    }
    if (!empty($definition['description']) && ($type !== 'attributes')) {
      $element['#description'] = $this->t((string) $definition['description']);
    }

    return $element;
  }

  /**
   * Builds a URL field with Linkit when available.
   *
   * @param string $default
   *   Default URL value.
   *
   * @return array<string, mixed>
   *   URL form element.
   */
  private function buildUrlElement(string $default): array {
    $profile_id = $this->getLinkitProfileId();
    if ($profile_id !== NULL) {
      return [
        '#type' => 'linkit',
        '#default_value' => $default,
        '#autocomplete_route_name' => 'linkit.autocomplete',
        '#autocomplete_route_parameters' => [
          'linkit_profile_id' => $profile_id,
        ],
        '#description' => $this->t('Start typing to find content, or enter a path or URL. Tokens supported.'),
      ];
    }

    return [
      '#type' => 'textfield',
      '#default_value' => $default,
      '#description' => $this->t('Relative paths (e.g. /calculator) or absolute URLs. Tokens supported.'),
    ];
  }

  /**
   * Applies cross-field #states for pattern-specific UX rules.
   *
   * @param array<string, mixed> $container
   *   Pattern form container.
   * @param string $schema_key
   *   Resolved schema key.
   */
  private function applyPatternFieldStates(array &$container, string $schema_key): void {
    if ($schema_key !== 'icon-card') {
      return;
    }

    $link_type_selector = ':input[name="layout[editor][pattern_form][advanced][link_type]"]';

    if (isset($container['advanced']['modal_id'])) {
      $container['advanced']['modal_id']['#states'] = [
        'visible' => [
          $link_type_selector => ['value' => 'modal'],
        ],
        'required' => [
          $link_type_selector => ['value' => 'modal'],
        ],
      ];
    }

    if (isset($container['action']['button_url'])) {
      unset($container['action']['button_url']['#required']);
      $container['action']['button_url']['#states'] = [
        'visible' => [
          $link_type_selector => [
            ['value' => ''],
            ['value' => 'offcanvas'],
          ],
        ],
        'required' => [
          $link_type_selector => [
            ['value' => ''],
            ['value' => 'offcanvas'],
          ],
        ],
      ];
    }
  }

  /**
   * Returns the configured or first available Linkit profile ID.
   */
  private function getLinkitProfileId(): ?string {
    if (!$this->moduleHandler->moduleExists('linkit')) {
      return NULL;
    }

    $configured = trim((string) ($this->configFactory->get('views_promo_card.settings')->get('linkit_profile') ?? ''));
    if ($configured !== '') {
      $profile = $this->entityTypeManager->getStorage('linkit_profile')->load($configured);
      if ($profile !== NULL) {
        return $configured;
      }
    }

    $profiles = $this->entityTypeManager->getStorage('linkit_profile')->loadMultiple();
    if ($profiles === []) {
      return NULL;
    }

    return (string) array_key_first($profiles);
  }

  /**
   * Normalizes a submitted field value to a string for UI Patterns storage.
   *
   * @param array<string, mixed> $definition
   *   Field definition.
   * @param mixed $raw
   *   Raw submitted value.
   */
  private function normalizeSubmittedValue(array $definition, mixed $raw): string {
    if (($definition['type'] ?? '') === 'text_format' && is_array($raw)) {
      return trim((string) ($raw['value'] ?? ''));
    }
    return trim((string) ($raw ?? ''));
  }

  /**
   * Builds a UI Patterns prop config array for a field value.
   *
   * @param array<string, mixed> $definition
   *   Field definition.
   * @param string $value
   *   Flat string value.
   *
   * @return array<string, mixed>
   *   UI Patterns prop configuration.
   */
  private function buildPropConfig(array $definition, string $value): array {
    $type = (string) ($definition['type'] ?? 'text');
    return match ($type) {
      'url' => UiPatternsPropBuilder::url($value),
      'select' => UiPatternsPropBuilder::select($value),
      'attributes' => UiPatternsPropBuilder::attributes($value),
      default => UiPatternsPropBuilder::textfield($value),
    };
  }

  /**
   * Resolves a schema key from a full pattern ID.
   */
  private function resolveSchemaKey(string $pattern_id): ?string {
    if (isset(self::PATTERN_SCHEMAS[$pattern_id])) {
      return $pattern_id;
    }
    foreach (array_keys(self::PATTERN_SCHEMAS) as $key) {
      if (str_ends_with($pattern_id, $key)) {
        return $key;
      }
    }
    return NULL;
  }

  /**
   * Normalizes select option labels for translation.
   *
   * @param array<string, string> $options
   *   Raw option labels.
   *
   * @return array<string, string>
   *   Translated options.
   */
  private function normalizeSelectOptions(array $options): array {
    $normalized = [];
    foreach ($options as $value => $label) {
      $normalized[$value] = $this->t($label);
    }
    return $normalized;
  }

}
