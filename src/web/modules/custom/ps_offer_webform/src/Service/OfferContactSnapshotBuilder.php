<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_agent\Entity\AgentInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_offer\Service\OfferContactResolver;
use Drupal\ps_offer\Service\OfferDefaultImageResolver;
use Drupal\ps_offer\Service\OfferGalleryImageResolver;
use Drupal\ps_theme\Utility\OfferEmailCardPropsBuilder;

/**
 * Builds hidden snapshot values from an offer node at submission time.
 */
final class OfferContactSnapshotBuilder {

  public function __construct(
    private readonly OfferContactResolver $contactResolver,
    private readonly OfferGalleryImageResolver $galleryImageResolver,
    private readonly OfferDefaultImageResolver $defaultImageResolver,
    private readonly DictionaryResolver $dictionaryResolver,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly TimeInterface $time,
  ) {}

  /**
   * Builds snapshot values for offer webform hidden fields.
   *
   * @return array<string, string>
   *   Snapshot values keyed by OfferContactSnapshotFields constants.
   */
  public function buildFromNode(NodeInterface $node, ?string $langcode = NULL): array {
    if ($node->bundle() !== 'offer') {
      return [];
    }

    $langcode ??= $node->language()->getId();
    $props = OfferEmailCardPropsBuilder::build($node, $langcode);

    $operationCode = $node->hasField('field_operation_type') && !$node->get('field_operation_type')->isEmpty()
      ? strtoupper(trim((string) $node->get('field_operation_type')->value))
      : '';
    $assetCode = $node->hasField('field_asset_type') && !$node->get('field_asset_type')->isEmpty()
      ? strtoupper(trim((string) $node->get('field_asset_type')->value))
      : '';

    $agent = $this->contactResolver->resolveAgent($node);
    $imageUri = $this->galleryImageResolver->resolvePrimaryImageUri($node)
      ?? $this->defaultImageResolver->getFileUri()
      ?? '';

    $surfaceSuffix = $props['surface_suffix'] ?? NULL;
    $location = $props['location'] ?? NULL;

    return [
      OfferContactSnapshotFields::OFFER_REFERENCE => trim((string) ($props['reference'] ?? '')),
      OfferContactSnapshotFields::OFFER_BUSINESS_ID => $this->fieldValue($node, 'field_business_id'),
      OfferContactSnapshotFields::OFFER_OPERATION_CODE => $operationCode,
      OfferContactSnapshotFields::OFFER_ASSET_CODE => $assetCode,
      OfferContactSnapshotFields::OFFER_CAPTURED_AT => $this->dateFormatter->format($this->time->getRequestTime(), 'custom', 'c'),
      OfferContactSnapshotFields::OFFER_TITLE => trim((string) ($props['title'] ?? '')),
      OfferContactSnapshotFields::OFFER_OPERATION_LABEL => $operationCode !== ''
        ? $this->dictionaryResolver->resolveLabel('operation_type', $operationCode)
        : '',
      OfferContactSnapshotFields::OFFER_ASSET_LABEL => trim((string) ($props['property_type'] ?? '')),
      OfferContactSnapshotFields::OFFER_SURFACE_PRIMARY => trim((string) ($props['surface_primary'] ?? '')),
      OfferContactSnapshotFields::OFFER_SURFACE_SUFFIX => is_string($surfaceSuffix) ? trim($surfaceSuffix) : '',
      OfferContactSnapshotFields::OFFER_LOCATION => is_string($location) ? trim($location) : '',
      OfferContactSnapshotFields::OFFER_PRICE_DISPLAY => trim((string) ($props['price_amount'] ?? '')),
      OfferContactSnapshotFields::OFFER_PRICE_QUALIFIERS => $this->plainQualifiers($props['price_qualifiers'] ?? ''),
      OfferContactSnapshotFields::OFFER_EXCLUSIVE => !empty($props['exclusive']) ? '1' : '0',
      OfferContactSnapshotFields::OFFER_URL => trim((string) ($props['url'] ?? '')),
      OfferContactSnapshotFields::OFFER_IMAGE_URI => $imageUri,
      OfferContactSnapshotFields::OFFER_IMAGE_ALT => trim((string) ($props['image_alt'] ?? '')),
      OfferContactSnapshotFields::OFFER_LANGCODE => $langcode,
      OfferContactSnapshotFields::OFFER_AGENT_EMAIL => $this->contactResolver->resolveContactEmail($node),
      OfferContactSnapshotFields::OFFER_AGENT_ID => $this->formatAgentId($agent),
    ];
  }

  private function fieldValue(NodeInterface $node, string $fieldName): string {
    if (!$node->hasField($fieldName) || $node->get($fieldName)->isEmpty()) {
      return '';
    }

    return trim((string) $node->get($fieldName)->value);
  }

  private function plainQualifiers(mixed $qualifiers): string {
    if ($qualifiers instanceof MarkupInterface) {
      $qualifiers = (string) $qualifiers;
    }

    if (!is_string($qualifiers)) {
      return '';
    }

    $plain = html_entity_decode(strip_tags($qualifiers), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(str_replace('m2', 'm²', $plain));
  }

  private function formatAgentId(?AgentInterface $agent): string {
    if ($agent === NULL) {
      return '';
    }

    return (string) $agent->id();
  }

}
