<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views_promo_card\Utility\IconIdUtility;

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
    'content-card' => [
      'content' => [
        'icon' => [
          'type' => 'icon_picker',
          'title' => 'Icon',
        ],
        'icon_position' => [
          'type' => 'select',
          'title' => 'Icon position',
          'options' => self::ICON_POSITION_OPTIONS,
          'default' => 'center',
        ],
        'icon_inline' => [
          'type' => 'checkbox',
          'title' => 'Icon inline with title',
          'description' => 'When enabled, the icon appears beside the title instead of above it.',
        ],
        'title' => [
          'type' => 'text',
          'title' => 'Title',
          'required' => TRUE,
        ],
        'title_tag' => [
          'type' => 'select',
          'title' => 'Title level',
          'options' => self::TITLE_TAG_OPTIONS,
          'default' => 'h3',
        ],
        'subtitle' => [
          'type' => 'text',
          'title' => 'Subtitle',
        ],
        'description' => [
          'type' => 'text_format',
          'title' => 'Description',
        ],
      ],
      'appearance' => [
        'background' => [
          'type' => 'select',
          'title' => 'Background',
          'options' => self::BACKGROUND_CONTENT_CARD_OPTIONS,
          'default' => 'default',
        ],
        'text_align' => [
          'type' => 'select',
          'title' => 'Text alignment',
          'options' => self::TEXT_ALIGN_LEFT_CENTER_END_OPTIONS,
          'default' => 'left',
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
        'border' => [
          'type' => 'select',
          'title' => 'Border',
          'options' => self::BORDER_OPTIONS,
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
          'title' => 'Link opening mode',
          'options' => self::LINK_OPEN_MODE_OPTIONS,
          'default' => '',
        ],
        'modal_id' => [
          'type' => 'text',
          'title' => 'Modal ID',
          'description' => 'Bootstrap modal ID (without #), when link opening mode is Modal.',
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
          'type' => 'icon_picker',
          'title' => 'Icon',
          'storage' => 'string',
        ],
        'link_type' => [
          'type' => 'select',
          'title' => 'Link opening mode',
          'options' => self::LINK_OPEN_MODE_OPTIONS,
          'default' => '',
        ],
        'modal_id' => [
          'type' => 'text',
          'title' => 'Modal ID',
          'description' => 'Bootstrap modal ID (without #), when link opening mode is Modal.',
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
   * Maximum action buttons on content-card.
   */
  private const MAX_CONTENT_CARD_BUTTONS = 4;

  /**
   * Link opening mode options shared by icon-card and cta-card.
   *
   * @var array<string, string>
   */
  private const LINK_OPEN_MODE_OPTIONS = [
    '' => 'Page',
    'modal' => 'Modal',
    'offcanvas' => 'Offcanvas',
  ];

  /**
   * Button opening mode options for content-card (explicit page value).
   *
   * @var array<string, string>
   */
  private const BUTTON_MODE_OPTIONS = [
    'page' => 'Page',
    'modal' => 'Modal',
    'offcanvas' => 'Offcanvas',
  ];

  /**
   * Button variant options aligned with ui_suite_bnp.
   *
   * @var array<string, string>
   */
  private const BUTTON_VARIANT_OPTIONS = [
    'primary' => 'Primary',
    'secondary' => 'Secondary',
  ];

  /**
   * Title heading levels allowed on content-card.
   *
   * @var array<string, string>
   */
  private const TITLE_TAG_OPTIONS = [
    'h2' => 'Heading 2',
    'h3' => 'Heading 3 (recommended)',
    'h4' => 'Heading 4',
  ];

  /**
   * Icon position options for content-card.
   *
   * @var array<string, string>
   */
  private const ICON_POSITION_OPTIONS = [
    'start' => 'Start',
    'center' => 'Center',
    'end' => 'End',
  ];

  /**
   * Link target options for page-mode buttons.
   *
   * @var array<string, string>
   */
  private const BUTTON_TARGET_OPTIONS = [
    '_self' => 'Same window',
    '_blank' => 'New window',
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
   * Background options for content-card (default surface + transparent).
   *
   * @var array<string, string>
   */
  private const BACKGROUND_CONTENT_CARD_OPTIONS = [
    'default' => 'Default (component preset)',
    'transparent' => 'Transparent',
    'white' => 'White',
    'muted' => 'Muted grey',
    'dark' => 'Dark',
    'primary' => 'Brand green',
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
   * Text alignment including end (right) for content-card.
   *
   * @var array<string, string>
   */
  private const TEXT_ALIGN_LEFT_CENTER_END_OPTIONS = [
    'left' => 'Left',
    'center' => 'Center',
    'end' => 'Right',
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
    'buttons' => 'Buttons',
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
      $section = $this->buildSection($section_id, $fields, $defaults, $pattern_id, $ui_patterns, $weight);
      if ($section !== NULL) {
        $container[$section_id] = $section;
        $weight += 10;
      }
    }

    if ($schema_key === 'content-card') {
      $container['buttons'] = $this->buildContentCardButtonsSection($ui_patterns, $pattern_id, $weight);
      $this->applyContentCardButtonFieldStates($container);
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

    $ui_patterns = $this->mergeSubmittedIconPickers($pattern_id, $pattern_form, $schema, $ui_patterns);
    if ($schema_key === 'content-card') {
      $ui_patterns = $this->mergeContentCardButtons($ui_patterns, $pattern_form);
    }

    return $ui_patterns;
  }

  /**
   * Merges icon picker submissions from any schema section into UI Patterns props.
   *
   * @param string $pattern_id
   *   Selected pattern ID.
   * @param array<string, mixed> $pattern_form
   *   Submitted pattern form values.
   * @param array<string, array<string, array<string, mixed>>> $schema
   *   Pattern schema definition.
   * @param array<string, mixed> $ui_patterns
   *   Partial UI Patterns configuration.
   *
   * @return array<string, mixed>
   *   Updated UI Patterns configuration.
   */
  private function mergeSubmittedIconPickers(
    string $pattern_id,
    array $pattern_form,
    array $schema,
    array $ui_patterns,
  ): array {
    foreach ($schema as $section_id => $fields) {
      $section_values = is_array($pattern_form[$section_id] ?? NULL) ? $pattern_form[$section_id] : [];
      foreach ($fields as $field_id => $definition) {
        if (($definition['type'] ?? '') !== 'icon_picker') {
          continue;
        }
        $icon_picker = $section_values[$field_id] ?? NULL;
        if (($definition['storage'] ?? '') === 'string') {
          $icon_id = IconIdUtility::extractFromSubmission($icon_picker, '');
          if ($icon_id !== '') {
            $ui_patterns['props'][$field_id] = UiPatternsPropBuilder::textfield($icon_id);
          }
          continue;
        }
        if ($this->patternIconHelper->patternHasIconProps($pattern_id)) {
          $ui_patterns = $this->patternIconHelper->mergeSubmittedIcon($ui_patterns, $icon_picker);
        }
      }
    }

    if ($this->patternIconHelper->patternHasIconProps($pattern_id)) {
      $ui_patterns = $this->patternIconHelper->normalizeUiPatterns($ui_patterns);
    }

    return $ui_patterns;
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
   * @param array<string, mixed> $ui_patterns
   *   Stored UI Patterns configuration.
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
    array $ui_patterns,
    int $weight,
  ): ?array {
    $elements = [];
    foreach ($fields as $field_id => $definition) {
      $element = $this->buildFieldElement($field_id, $definition, $defaults, $pattern_id, $ui_patterns);
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
   * @param array<string, mixed> $ui_patterns
   *   Stored UI Patterns configuration.
   *
   * @return array<string, mixed>|null
   *   Field element or NULL when unavailable.
   */
  private function buildFieldElement(
    string $field_id,
    array $definition,
    array $defaults,
    string $pattern_id,
    array $ui_patterns,
  ): ?array {
    $type = (string) ($definition['type'] ?? 'text');
    $title = (string) ($definition['title'] ?? $field_id);
    $default = $defaults[$field_id] ?? (string) ($definition['default'] ?? '');

    if ($type === 'icon_picker') {
      if (!$this->patternIconHelper->iconPickerAvailable()) {
        return NULL;
      }

      $storage = (string) ($definition['storage'] ?? '');
      $uses_string_storage = $storage === 'string';
      if (!$uses_string_storage && !$this->patternIconHelper->patternHasIconProps($pattern_id)) {
        return NULL;
      }

      $default_icon = $uses_string_storage
        ? (string) ($defaults[$field_id] ?? $this->patternIconHelper->getDefaultIcon())
        : $this->patternIconHelper->getFormIconDefault($ui_patterns);

      $element = [
        '#type' => 'icon_picker',
        '#title' => $this->t($title),
        '#default_value' => $default_icon,
        '#return_id' => TRUE,
        '#wrapper_attributes' => ['class' => ['views-promo-card-icon-picker', 'ps-icon-picker']],
        '#attached' => ['library' => ['ps_core/icon_picker']],
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
      'checkbox' => [
        '#type' => 'checkbox',
        '#default_value' => in_array(strtolower($default), ['1', 'true', 'yes', 'on'], TRUE),
        '#return_value' => 1,
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
    if ($schema_key === 'icon-card') {
      $this->applyLinkTypeFieldStates(
        $container,
        ':input[name="layout[editor][pattern_form][advanced][link_type]"]',
        'advanced',
        'action',
        'button_url',
      );
      return;
    }

    if ($schema_key === 'cta-card') {
      $this->applyLinkTypeFieldStates(
        $container,
        ':input[name="layout[editor][pattern_form][advanced][link_type]"]',
        'advanced',
        'action',
        'button_url',
      );
    }
  }

  /**
   * Applies visibility/required states for Page/Modal/Offcanvas link modes.
   *
   * @param array<string, mixed> $container
   *   Pattern form container.
   * @param string $link_type_selector
   *   CSS selector for the link opening mode field.
   * @param string $advanced_section
   *   Advanced options section key.
   * @param string $action_section
   *   Call to action section key.
   * @param string $url_field
   *   URL field key in the action section.
   */
  private function applyLinkTypeFieldStates(
    array &$container,
    string $link_type_selector,
    string $advanced_section,
    string $action_section,
    string $url_field,
  ): void {
    if (isset($container[$advanced_section]['modal_id'])) {
      $container[$advanced_section]['modal_id']['#states'] = [
        'visible' => [
          $link_type_selector => ['value' => 'modal'],
        ],
        'required' => [
          $link_type_selector => ['value' => 'modal'],
        ],
      ];
    }

    if (isset($container[$action_section][$url_field])) {
      unset($container[$action_section][$url_field]['#required']);
      $container[$action_section][$url_field]['#states'] = [
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
    if (($definition['type'] ?? '') === 'checkbox') {
      return !empty($raw) ? '1' : '0';
    }
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

  /**
   * Adds content-card buttons JSON prop from the buttons form section.
   *
   * @param array<string, mixed> $ui_patterns
   *   Partial UI Patterns configuration.
   * @param array<string, mixed> $pattern_form
   *   Submitted pattern form values.
   *
   * @return array<string, mixed>
   *   Updated UI Patterns configuration.
   */
  private function mergeContentCardButtons(array $ui_patterns, array $pattern_form): array {
    $ui_patterns['props']['buttons'] = UiPatternsPropBuilder::json(
      $this->buildButtonsStorage($pattern_form),
    );
    return $ui_patterns;
  }

  /**
   * Builds the content-card buttons fieldset (up to four buttons).
   *
   * @param array<string, mixed> $ui_patterns
   *   Stored UI Patterns configuration.
   * @param string $pattern_id
   *   Selected pattern ID.
   * @param int $weight
   *   Section weight.
   *
   * @return array<string, mixed>
   *   Buttons section render array.
   */
  private function buildContentCardButtonsSection(array $ui_patterns, string $pattern_id, int $weight): array {
    $stored = $this->parseButtonsDefaults($ui_patterns);
    $layout = (string) ($stored['layout'] ?? 'stack');
    $stored_items = is_array($stored['items'] ?? NULL) ? $stored['items'] : [];

    $section = [
      '#type' => 'fieldset',
      '#title' => $this->t('Buttons'),
      '#tree' => TRUE,
      '#weight' => $weight,
      '#attributes' => ['class' => ['promo-card-pattern-section', 'promo-card-pattern-section--buttons']],
      'layout' => [
        '#type' => 'select',
        '#title' => $this->t('Buttons layout'),
        '#options' => $this->normalizeSelectOptions([
          'stack' => 'Stack (vertical)',
          'row' => 'Row',
          'inline' => 'Inline',
        ]),
        '#default_value' => $layout !== '' ? $layout : 'stack',
      ],
      'items' => [
        '#tree' => TRUE,
      ],
    ];

    for ($i = 0; $i < self::MAX_CONTENT_CARD_BUTTONS; $i++) {
      $item_defaults = is_array($stored_items[$i] ?? NULL) ? $stored_items[$i] : [];
      $section['items'][$i] = $this->buildContentCardButtonItem($i, $item_defaults);
    }

    if ($this->moduleHandler->moduleExists('token')) {
      $section['token_help'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => ['site', 'current-user'],
        '#weight' => 100,
      ];
    }

    return $section;
  }

  /**
   * Builds one content-card button details element.
   *
   * @param int $index
   *   Zero-based button index.
   * @param array<string, mixed> $defaults
   *   Stored button defaults.
   *
   * @return array<string, mixed>
   *   Button fieldset render array.
   */
  private function buildContentCardButtonItem(int $index, array $defaults): array {
    $mode = (string) ($defaults['mode'] ?? 'page');
    if ($mode === '') {
      $mode = 'page';
    }

    $url_element = $this->buildUrlElement(trim((string) ($defaults['url'] ?? '')));
    $url_element['#title'] = $this->t('URL');

    return [
      '#type' => 'details',
      '#title' => $this->t('Button @num', ['@num' => $index + 1]),
      '#open' => $index === 0 || trim((string) ($defaults['label'] ?? '')) !== '',
      'label' => [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#default_value' => (string) ($defaults['label'] ?? ''),
      ],
      'url' => $url_element,
      'variant' => [
        '#type' => 'select',
        '#title' => $this->t('Variant'),
        '#options' => $this->normalizeSelectOptions(self::BUTTON_VARIANT_OPTIONS),
        '#default_value' => (string) ($defaults['variant'] ?? 'primary'),
      ],
      'outline' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Outline style'),
        '#default_value' => !empty($defaults['outline']),
        '#return_value' => 1,
      ],
      'mode' => [
        '#type' => 'select',
        '#title' => $this->t('Opening mode'),
        '#options' => $this->normalizeSelectOptions(self::BUTTON_MODE_OPTIONS),
        '#default_value' => $mode,
      ],
      'modal_id' => [
        '#type' => 'textfield',
        '#title' => $this->t('Modal ID'),
        '#description' => $this->t('Bootstrap modal ID (without #), when opening mode is Modal.'),
        '#default_value' => (string) ($defaults['modal_id'] ?? ''),
      ],
      'target' => [
        '#type' => 'select',
        '#title' => $this->t('Link target'),
        '#options' => $this->normalizeSelectOptions(self::BUTTON_TARGET_OPTIONS),
        '#default_value' => (string) ($defaults['target'] ?? '_self'),
      ],
    ];
  }

  /**
   * Applies #states for content-card button fields (mode-dependent visibility).
   *
   * @param array<string, mixed> $container
   *   Pattern form container.
   */
  private function applyContentCardButtonFieldStates(array &$container): void {
    if (!isset($container['buttons']['items']) || !is_array($container['buttons']['items'])) {
      return;
    }

    foreach ($container['buttons']['items'] as $index => &$item) {
      if (!is_array($item)) {
        continue;
      }
      $mode_selector = ':input[name="layout[editor][pattern_form][buttons][items][' . $index . '][mode]"]';

      if (isset($item['modal_id'])) {
        $item['modal_id']['#states'] = [
          'visible' => [
            $mode_selector => ['value' => 'modal'],
          ],
          'required' => [
            $mode_selector => ['value' => 'modal'],
          ],
        ];
      }

      if (isset($item['url'])) {
        $item['url']['#states'] = [
          'visible' => [
            $mode_selector => [
              ['value' => 'page'],
              ['value' => 'offcanvas'],
            ],
          ],
          'required' => [
            $mode_selector => ['value' => 'page'],
          ],
        ];
      }

      if (isset($item['target'])) {
        $item['target']['#states'] = [
          'visible' => [
            $mode_selector => ['value' => 'page'],
          ],
        ];
      }
    }
  }

  /**
   * Parses stored buttons JSON from UI Patterns props.
   *
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   *
   * @return array<string, mixed>
   *   Buttons configuration with layout and items keys.
   */
  private function parseButtonsDefaults(array $ui_patterns): array {
    $raw = UiPatternsValueReader::getPropValue($ui_patterns, 'buttons');
    if ($raw === '') {
      return [
        'layout' => 'stack',
        'items' => [],
      ];
    }

    try {
      $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException) {
      return [
        'layout' => 'stack',
        'items' => [],
      ];
    }

    return is_array($decoded) ? $decoded : [
      'layout' => 'stack',
      'items' => [],
    ];
  }

  /**
   * Builds buttons storage payload from submitted form values.
   *
   * @param array<string, mixed> $pattern_form
   *   Submitted pattern form values.
   *
   * @return array<string, mixed>
   *   Buttons JSON payload.
   */
  private function buildButtonsStorage(array $pattern_form): array {
    $section = is_array($pattern_form['buttons'] ?? NULL) ? $pattern_form['buttons'] : [];
    $layout = trim((string) ($section['layout'] ?? 'stack'));
    $submitted_items = is_array($section['items'] ?? NULL) ? $section['items'] : [];
    $items = [];

    for ($i = 0; $i < self::MAX_CONTENT_CARD_BUTTONS; $i++) {
      $item = is_array($submitted_items[$i] ?? NULL) ? $submitted_items[$i] : [];
      $label = trim((string) ($item['label'] ?? ''));
      if ($label === '') {
        continue;
      }

      $mode = trim((string) ($item['mode'] ?? 'page'));
      if ($mode === '') {
        $mode = 'page';
      }

      $items[] = [
        'label' => $label,
        'url' => trim((string) ($item['url'] ?? '')),
        'variant' => (string) ($item['variant'] ?? 'primary'),
        'outline' => !empty($item['outline']),
        'mode' => $mode,
        'modal_id' => trim((string) ($item['modal_id'] ?? '')),
        'target' => (string) ($item['target'] ?? '_self'),
      ];
    }

    return [
      'layout' => $layout !== '' ? $layout : 'stack',
      'items' => $items,
    ];
  }

}
