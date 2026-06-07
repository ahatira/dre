<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;

/**
 * Builds props for the offer-card SDC from an offer node.
 */
final class OfferCardPropsBuilder {

  use StringTranslationTrait;

  private const IMAGE_STYLE = 'bnp_media_admin_card';

  /**
   * Builds offer-card component props from a node.
   *
   * @return array<string, mixed>
   *   Props keyed for ps_theme:offer-card.
   */
  public static function build(NodeInterface $node): array {
    $instance = new self();
    return $instance->buildProps($node);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildProps(NodeInterface $node): array {
    $operationCode = (string) ($node->get('field_operation_type')->value ?? '');
    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');

    $title = (string) ($node->get('field_commercial_title')->value ?? '');
    if ($title === '') {
      $title = $node->label() ?? '';
    }

    $badges = [];
    if ($assetCode !== '') {
      $badges[] = $this->dictionaryLabel('asset_type', $assetCode);
    }

    $imageUrl = $this->resolveImageUrl($node);
    if ($imageUrl === NULL) {
      $imageUrl = $this->placeholderImageUrl();
    }

    return [
      'title' => $title,
      'url' => $node->toUrl()->toString(),
      'image' => $imageUrl,
      'image_alt' => $title,
      'location' => $this->formatLocation($node),
      'surface' => $this->formatSurface($node),
      'price' => $this->formatPrice($node, $operationCode),
      'operation' => $operationCode === 'VEN' ? 'sale' : 'rent',
      'badges' => array_values(array_filter($badges)),
      'favorite' => TRUE,
    ];
  }

  private function resolveImageUrl(NodeInterface $node): ?string {
    if (!$node->hasField('field_media_gallery') || $node->get('field_media_gallery')->isEmpty()) {
      return NULL;
    }

    $style = ImageStyle::load(self::IMAGE_STYLE);
    if ($style === NULL) {
      return NULL;
    }

    foreach ($node->get('field_media_gallery')->referencedEntities() as $media) {
      if (!$media instanceof MediaInterface) {
        continue;
      }
      $uri = $this->resolveMediaUri($media);
      if ($uri !== NULL) {
        return $style->buildUrl($uri);
      }
    }

    return NULL;
  }

  private function placeholderImageUrl(): string {
    $theme = \Drupal::theme()->getActiveTheme()->getPath();
    return '/' . $theme . '/assets/images/offer-placeholder.svg';
  }

  private function resolveMediaUri(MediaInterface $media): ?string {
    $bundle = $media->bundle();
    $candidates = match ($bundle) {
      'image', 'visite_guided' => ['field_media_image'],
      'gallery' => ['field_media_gallery_image'],
      default => ['thumbnail', 'field_media_image'],
    };

    foreach ($candidates as $fieldName) {
      if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
        continue;
      }
      $file = $media->get($fieldName)->entity;
      if ($file !== NULL) {
        return $file->getFileUri();
      }
    }

    return NULL;
  }

  private function formatLocation(NodeInterface $node): ?string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $address = $node->get('field_address')->first();
    if ($address === NULL) {
      return NULL;
    }

    $locality = trim((string) ($address->locality ?? ''));
    $postal = trim((string) ($address->postal_code ?? ''));
    $parts = array_filter([$locality, $postal]);

    return $parts !== [] ? implode(' ', $parts) : NULL;
  }

  private function formatSurface(NodeInterface $node): ?string {
    if (\Drupal::hasService('ps_offer.surface_kpi_builder')) {
      $text = \Drupal::service('ps_offer.surface_kpi_builder')->buildKpiSummary($node);
      return $text !== '' ? $text : NULL;
    }

    return NULL;
  }

  private function formatPrice(NodeInterface $node, string $operationCode): string {
    if (!$node->hasField('field_budget_value') || $node->get('field_budget_value')->isEmpty()) {
      return (string) $this->t('On request');
    }

    $raw = $node->get('field_budget_value')->value;
    if ($raw === NULL || $raw === '' || (float) $raw <= 0) {
      return (string) $this->t('On request');
    }

    $amount = number_format((float) $raw, 0, ',', ' ');
    $currencyCode = (string) ($node->get('field_budget_currency')->value ?? 'EUR');
    $currency = match (strtoupper($currencyCode)) {
      'EUR' => '€',
      default => $this->dictionaryLabel('currency', $currencyCode) ?: $currencyCode,
    };

    $suffix = '';
    if ($operationCode === 'LOC') {
      $period = (string) ($node->get('field_budget_period')->value ?? '');
      $suffix = match ($period) {
        'MONTH' => ' /' . $this->t('month'),
        'YEAR' => ' /' . $this->t('year'),
        default => '',
      };
    }

    return $amount . ' ' . $currency . $suffix;
  }

  private function dictionaryLabel(string $type, string $code): string {
    if ($code === '') {
      return '';
    }
    $label = \Drupal::service('ps_dictionary.resolver')->resolveLabel($type, $code);
    return $label ?: $code;
  }

}
