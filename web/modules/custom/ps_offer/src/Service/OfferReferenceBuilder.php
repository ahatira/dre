<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Entity\OfferReferenceSegmentInterface;

/**
 * Builds offer references from configurable segments.
 */
final class OfferReferenceBuilder {

  /**
   * Returns segments configuration formatted for front-end JS consumption.
   *
   * Only enabled segments are included. The counter segment (type "auto")
   * is included so the JS can render a placeholder; it is not computed
   * client-side. Keys are camelCase to match JS conventions.
   *
   * @return array<int, array<string, mixed>>
   */
  public function getJsSegmentsConfig(): array {
    $segments = $this->getConfiguredSegments();
    $js = [];

    foreach ($segments as $segment) {
      if (empty($segment['enabled'])) {
        continue;
      }

      $type = (string) ($segment['type'] ?? 'custom');
      $length = max(1, (int) ($segment['length'] ?? 1));
      $source_field = (string) ($segment['source_field'] ?? '');
      $options = is_array($segment['options'] ?? NULL) ? $segment['options'] : [];

      $js_segment = [
        'type' => $type,
        'length' => $length,
        'sourceField' => $source_field,
        'options' => [],
      ];

      switch ($type) {
        case 'static':
          $js_segment['options']['staticValue'] = strtoupper(trim((string) ($options['static_value'] ?? '')));
          break;

        case 'custom':
          // custom_map is already parsed (KEY => VALUE) by getConfiguredSegments().
          $js_segment['options']['customMap'] = $options['custom_map'] ?? [];
          break;

        case 'start':
          $js_segment['options']['startIndex'] = max(1, (int) ($options['start_index'] ?? 1));
          break;

        case 'date':
          $js_segment['sourceField'] = (string) ($options['date_source_field'] ?? $source_field ?: 'publish_on');
          $js_segment['options']['dateFormat'] = (string) ($options['date_format'] ?? 'YY');
          break;

        // 'auto': counter assigned on save, no client-side options needed.
      }

      $js[] = $js_segment;
    }

    return $js;
  }

  /**
   * Allowed preconfigured date formats.
   */
  public const DATE_FORMATS = [
    'YY' => 'y',
    'YYYY' => 'Y',
    'MM' => 'm',
    'YYMM' => 'ym',
    'YYMMDD' => 'ymd',
  ];

  /**
   * Constructs the builder.
   */
  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected LockBackendInterface $lock,
    protected LoggerChannelFactoryInterface $loggerFactory,
  ) {}

  /**
   * Returns default segment configuration.
   *
   * @return array<int, array<string, mixed>>
   *   Default ordered segments.
   */
  public function getDefaultSegments(): array {
    return [
      [
        'id' => 'segment_1',
        'label' => 'Offer/Demand marker',
        'enabled' => TRUE,
        'weight' => 0,
        'type' => 'static',
        'source_field' => '',
        'length' => 1,
        'options' => [
          'static_value' => 'O',
        ],
      ],
      [
        'id' => 'segment_2',
        'label' => 'Transaction type',
        'enabled' => TRUE,
        'weight' => 10,
        'type' => 'custom',
        'source_field' => 'field_transaction_types',
        'length' => 1,
        'options' => [
          'custom_map' => [
            'LOC' => 'L',
            'L' => 'L',
            'V' => 'V',
            'VTE' => 'V',
            'VEN' => 'V',
            'VENTE' => 'V',
            'C' => 'C',
            'CES' => 'C',
            'CESSION' => 'C',
          ],
        ],
      ],
      [
        'id' => 'segment_3',
        'label' => 'Property type',
        'enabled' => TRUE,
        'weight' => 20,
        'type' => 'custom',
        'source_field' => 'field_property_type',
        'length' => 3,
        'options' => [
          'custom_map' => [
            'BUR' => 'BUR',
            'ACT' => 'ACT',
            'LOG' => 'LOG',
            'COM' => 'COM',
          ],
        ],
      ],
      [
        'id' => 'segment_4',
        'label' => 'Published date',
        'enabled' => TRUE,
        'weight' => 30,
        'type' => 'date',
        'source_field' => 'publish_on',
        'length' => 2,
        'options' => [
          'date_source_field' => 'publish_on',
          'date_format' => 'YY',
        ],
      ],
      [
        'id' => 'segment_5',
        'label' => 'Counter',
        'enabled' => TRUE,
        'weight' => 40,
        'type' => 'auto',
        'source_field' => '',
        'length' => 5,
        'options' => [
          'auto_start' => 1,
        ],
      ],
    ];
  }

  /**
   * Gets configured total reference length.
   */
  public function getConfiguredTotalLength(): int {
    $total_length = (int) ($this->configFactory->get('ps_offer.settings')->get('reference_builder.total_length') ?? 12);
    return $total_length > 0 ? $total_length : 12;
  }

  /**
   * Validates a manual reference against current length policy.
   */
  public function isReferenceValid(string $reference): bool {
    $reference = strtoupper(trim($reference));
    return $reference !== ''
      && preg_match('/^[A-Z0-9]+$/', $reference) === 1
      && strlen($reference) === $this->getConfiguredTotalLength();
  }

  /**
   * Generates a reference and collected warnings.
   *
   * @return array{reference: string, warnings: string[]}
   *   The generated reference and warning messages.
   */
  public function generate(NodeInterface $node): array {
    $warnings = [];
    $segments = $this->getConfiguredSegments();
    $total_length = $this->getConfiguredTotalLength();

    $segments = array_values(array_filter($segments, static fn(array $segment): bool => !empty($segment['enabled'])));
    usort($segments, static fn(array $a, array $b): int => ((int) ($a['weight'] ?? 0)) <=> ((int) ($b['weight'] ?? 0)));

    if ($segments === []) {
      $segments = $this->getDefaultSegments();
    }

    $parts = [];
    $auto_index = NULL;
    $auto_length = 0;
    $auto_start = 1;

    foreach ($segments as $index => $segment) {
      $type = (string) ($segment['type'] ?? 'custom');
      $length = max(1, (int) ($segment['length'] ?? 1));
      $source_field = (string) ($segment['source_field'] ?? '');
      $options = is_array($segment['options'] ?? NULL) ? $segment['options'] : [];

      if ($type === 'auto') {
        $auto_index = $index;
        $auto_length = $length;
        $auto_start = max(1, (int) ($options['auto_start'] ?? 1));
        $parts[$index] = '';
        continue;
      }

      $raw_value = $this->resolveSegmentRawValue($node, $type, $source_field, $options, $warnings);
      $parts[$index] = $this->fitToLength($raw_value, $length, (string) ($segment['label'] ?? ('segment ' . ($index + 1))), $warnings);
    }

    if ($auto_index !== NULL) {
      $prefix = implode('', array_slice($parts, 0, $auto_index));
      $suffix = implode('', array_slice($parts, $auto_index + 1));
      $auto_value = $this->buildAutoValue($node, $prefix, $suffix, $auto_length, $auto_start, $warnings);
      $parts[$auto_index] = $auto_value;
    }

    $reference = implode('', $parts);
    $reference = $this->fitReferenceToTotalLength($reference, $total_length, $warnings);

    return [
      'reference' => $reference,
      'warnings' => $warnings,
    ];
  }

  /**
   * Returns configured segments from config entities with fallback.
   *
   * @return array<int, array<string, mixed>>
   *   Ordered segment config arrays.
   */
  protected function getConfiguredSegments(): array {
    $storage = $this->entityTypeManager->getStorage('ps_offer_reference_segment');
    $entities = $storage->loadMultiple();

    if ($entities !== []) {
      /** @var array<int, \Drupal\ps_offer\Entity\OfferReferenceSegmentInterface> $entities */
      uasort($entities, static fn(OfferReferenceSegmentInterface $a, OfferReferenceSegmentInterface $b): int => $a->getWeight() <=> $b->getWeight());

      $segments = [];
      foreach ($entities as $entity) {
        $segments[] = [
          'id' => $entity->id(),
          'label' => $entity->label(),
          'enabled' => $entity->isEnabled(),
          'weight' => $entity->getWeight(),
          'type' => $entity->getSegmentType(),
          'source_field' => $entity->getSourceField(),
          'length' => $entity->getLength(),
          'options' => [
            'static_value' => $entity->getStaticValue(),
            'custom_map' => $this->parseCustomMapText($entity->getCustomMapText()),
            'start_index' => $entity->getStartIndex(),
            'date_source_field' => $entity->getDateSourceField(),
            'date_format' => $entity->getDateFormat(),
            'auto_start' => $entity->getAutoStart(),
          ],
        ];
      }

      return $segments;
    }

    // Backward-compatible fallback until legacy settings are retired.
    $legacy = $this->configFactory->get('ps_offer.settings')->get('reference_builder.segments');
    if (is_array($legacy) && $legacy !== []) {
      return $legacy;
    }

    return $this->getDefaultSegments();
  }

  /**
   * Resolves a raw segment value before length fitting.
   */
  protected function resolveSegmentRawValue(NodeInterface $node, string $type, string $source_field, array $options, array &$warnings): string {
    return match ($type) {
      'static' => (string) ($options['static_value'] ?? ''),
      'date' => $this->buildDateValue($node, (string) ($options['date_source_field'] ?? $source_field ?: 'publish_on'), (string) ($options['date_format'] ?? 'YY'), $warnings),
      'start' => $this->buildStartValue($this->extractNodeValue($node, $source_field), max(1, (int) ($options['start_index'] ?? 1))),
      'custom' => $this->buildCustomValue($this->extractNodeValue($node, $source_field), is_array($options['custom_map'] ?? NULL) ? $options['custom_map'] : [], $warnings),
      default => $this->extractNodeValue($node, $source_field),
    };
  }

  /**
   * Builds custom mapped value with fallback on first character.
   */
  protected function buildCustomValue(string $source_value, array $custom_map, array &$warnings): string {
    $source_value = strtoupper(trim($source_value));
    if ($source_value === '') {
      $warnings[] = 'Custom mapping source value is empty, fallback will use X.';
      return 'X';
    }

    if (isset($custom_map[$source_value]) && $custom_map[$source_value] !== '') {
      return (string) $custom_map[$source_value];
    }

    $warnings[] = sprintf('Missing custom mapping for "%s", fallback to first character.', $source_value);
    return substr($source_value, 0, 1);
  }

  /**
   * Builds a value from a 1-based start index.
   */
  protected function buildStartValue(string $source_value, int $start_index): string {
    $source_value = strtoupper(trim($source_value));
    $offset = max(0, $start_index - 1);
    return (string) substr($source_value, $offset);
  }

  /**
   * Builds a date value from a configured date source and format.
   */
  protected function buildDateValue(NodeInterface $node, string $source_field, string $format_key, array &$warnings): string {
    $timestamp = $this->extractDateTimestamp($node, $source_field, $warnings);
    $php_format = self::DATE_FORMATS[$format_key] ?? self::DATE_FORMATS['YY'];
    return date($php_format, $timestamp);
  }

  /**
   * Extracts a date timestamp from the node field or falls back to created time.
   */
  protected function extractDateTimestamp(NodeInterface $node, string $source_field, array &$warnings): int {
    if ($source_field === 'created') {
      return (int) ($node->getCreatedTime() ?: time());
    }

    if ($node->hasField($source_field) && !$node->get($source_field)->isEmpty()) {
      $item = $node->get($source_field)->first();
      if ($item) {
        $values = $item->getValue();
        $candidate = $values['value'] ?? $values['timestamp'] ?? reset($values);
        if (is_numeric($candidate)) {
          return (int) $candidate;
        }
        if (is_string($candidate) && $candidate !== '') {
          $parsed = strtotime($candidate);
          if ($parsed !== FALSE) {
            return $parsed;
          }
        }
      }
    }

    $warnings[] = sprintf('Date source "%s" is unavailable, fallback to created date.', $source_field);
    return (int) ($node->getCreatedTime() ?: time());
  }

  /**
   * Extracts a normalized node field value.
   */
  protected function extractNodeValue(NodeInterface $node, string $field_name): string {
    if ($field_name === '') {
      return '';
    }

    if ($field_name === 'created') {
      return (string) $node->getCreatedTime();
    }

    if (!$node->hasField($field_name) || $node->get($field_name)->isEmpty()) {
      return '';
    }

    $item = $node->get($field_name)->first();
    if (!$item) {
      return '';
    }

    $values = $item->getValue();
    $value = $values['value'] ?? $values['target_id'] ?? reset($values);
    $value = is_scalar($value) ? (string) $value : '';

    $value = strtoupper(trim($value));
    $value = preg_replace('/[^A-Z0-9]/', '', $value) ?? '';
    return $value;
  }

  /**
   * Fits an arbitrary segment value to the expected length.
   */
  protected function fitToLength(string $value, int $length, string $label, array &$warnings): string {
    $value = strtoupper(trim($value));
    $value = preg_replace('/[^A-Z0-9]/', '', $value) ?? '';

    if (strlen($value) < $length) {
      $warnings[] = sprintf('Segment "%s" is shorter than %d characters, padded with X.', $label, $length);
      $value = str_pad($value, $length, 'X');
    }

    if (strlen($value) > $length) {
      $value = substr($value, 0, $length);
    }

    return $value;
  }

  /**
   * Builds the auto-increment segment value.
   */
  protected function buildAutoValue(NodeInterface $node, string $prefix, string $suffix, int $length, int $auto_start, array &$warnings): string {
    $length = max(1, $length);
    $storage = $this->entityTypeManager->getStorage('node');

    $lock_key = 'ps_offer.reference_builder.' . md5($prefix . '|' . $suffix . '|' . $length);
    if (!$this->lock->acquire($lock_key, 5.0)) {
      $warnings[] = 'Reference counter lock not available, using fallback start value.';
      return str_pad((string) $auto_start, $length, '0', STR_PAD_LEFT);
    }

    try {
      $pattern = $prefix . '%' . $suffix;
      $ids = $storage->getQuery()
        ->accessCheck(FALSE)
        ->condition('type', 'offer')
        ->condition('field_reference', $pattern, 'LIKE')
        ->execute();

      $max = $auto_start - 1;
      if ($ids) {
        /** @var \Drupal\node\NodeInterface[] $nodes */
        $nodes = $storage->loadMultiple($ids);
        $prefix_len = strlen($prefix);
        foreach ($nodes as $existing) {
          if (!$existing->hasField('field_reference') || $existing->get('field_reference')->isEmpty()) {
            continue;
          }

          $reference = (string) $existing->get('field_reference')->value;
          if ($suffix !== '' && !str_ends_with($reference, $suffix)) {
            continue;
          }

          if (strlen($reference) < $prefix_len + $length) {
            continue;
          }

          $counter_str = substr($reference, $prefix_len, $length);
          if (!ctype_digit($counter_str)) {
            continue;
          }

          $max = max($max, (int) $counter_str);
        }
      }

      $next = $max + 1;
      $counter = str_pad((string) $next, $length, '0', STR_PAD_LEFT);
      if (strlen($counter) > $length) {
        $warnings[] = 'Auto counter exceeded its segment length and was truncated.';
        $counter = substr($counter, -$length);
      }

      return $counter;
    }
    finally {
      $this->lock->release($lock_key);
    }
  }

  /**
   * Fits final reference to configured total length.
   */
  protected function fitReferenceToTotalLength(string $reference, int $total_length, array &$warnings): string {
    $reference = strtoupper($reference);
    if (strlen($reference) < $total_length) {
      $warnings[] = sprintf('Generated reference is shorter than %d characters, padded with X.', $total_length);
      return str_pad($reference, $total_length, 'X');
    }

    if (strlen($reference) > $total_length) {
      $warnings[] = sprintf('Generated reference is longer than %d characters, truncated.', $total_length);
      return substr($reference, 0, $total_length);
    }

    return $reference;
  }

  /**
   * Parses custom mapping textarea into a normalized map array.
   *
   * @return array<string, string>
   *   Mapping array.
   */
  protected function parseCustomMapText(string $text): array {
    $map = [];
    $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '' || !str_contains($line, '=')) {
        continue;
      }

      [$key, $value] = explode('=', $line, 2);
      $key = strtoupper(trim($key));
      $value = strtoupper(trim($value));

      if ($key !== '' && $value !== '') {
        $map[$key] = $value;
      }
    }

    return $map;
  }

}
