<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

use Drupal\ps_search\GeoZone\GeoZoneType;

/**
 * Immutable geo zone definition from the ps_search geo zones referential.
 */
final class GeoZone {

  /**
   * @param list<string> $postalPrefixes
   */
  public function __construct(
    public readonly string $id,
    public readonly GeoZoneType $type,
    public readonly string $countryCode,
    public readonly string $code,
    public readonly string $label,
    public readonly string $slug,
    public readonly float $lat,
    public readonly float $lng,
    public readonly GeoBoundingBox $bbox,
    public readonly array $postalPrefixes,
    public readonly ?string $parentId = NULL,
    public readonly int $weight = 0,
  ) {}

  /**
   * @param array<string, mixed> $data
   */
  public static function fromConfigArray(string $id, array $data, string $countryCode): ?self {
    $type = GeoZoneType::fromConfig((string) ($data['type'] ?? ''));
    if ($type === NULL) {
      return NULL;
    }

    $bbox = GeoBoundingBox::fromConfigArray(is_array($data['bbox'] ?? NULL) ? $data['bbox'] : []);
    if (!$bbox instanceof GeoBoundingBox) {
      return NULL;
    }

    $prefixes = [];
    foreach ($data['postal_prefixes'] ?? [] as $prefix) {
      if (is_string($prefix) && $prefix !== '') {
        $prefixes[] = $prefix;
      }
    }

    $label = trim((string) ($data['label'] ?? ''));
    $slug = trim((string) ($data['slug'] ?? ''));
    $code = trim((string) ($data['code'] ?? ''));

    if ($label === '' || $slug === '' || $code === '') {
      return NULL;
    }

    if (!isset($data['lat'], $data['lng']) || !is_numeric($data['lat']) || !is_numeric($data['lng'])) {
      return NULL;
    }

    return new self(
      id: $id,
      type: $type,
      countryCode: strtolower($countryCode),
      code: $code,
      label: $label,
      slug: $slug,
      lat: (float) $data['lat'],
      lng: (float) $data['lng'],
      bbox: $bbox,
      postalPrefixes: array_values(array_unique($prefixes)),
      parentId: isset($data['parent']) && is_string($data['parent']) && $data['parent'] !== ''
        ? $data['parent']
        : NULL,
      weight: (int) ($data['weight'] ?? 0),
    );
  }

  /**
   * @return array<string, mixed>
   */
  public function toConfigArray(): array {
    return [
      'id' => $this->id,
      'type' => $this->type->value,
      'code' => $this->code,
      'label' => $this->label,
      'slug' => $this->slug,
      'lat' => $this->lat,
      'lng' => $this->lng,
      'bbox' => $this->bbox->toConfigArray(),
      'postal_prefixes' => $this->postalPrefixes,
      'parent' => $this->parentId,
      'weight' => $this->weight,
    ];
  }

}
