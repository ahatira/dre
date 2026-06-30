<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Builds email-safe offer card props with absolute URLs.
 */
final class OfferEmailCardPropsBuilder {

  use OfferNodeCardPropsTrait;

  /**
   * Builds offer email card props from a node.
   *
   * @return array<string, mixed>
   *   Props for offer-email-card-vertical / compact templates.
   */
  public static function build(NodeInterface $node, ?string $langcode = NULL): array {
    return (new self())->buildProps($node, $langcode);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildProps(NodeInterface $node, ?string $langcode): array {
    $operationCode = (string) ($node->get('field_operation_type')->value ?? '');
    $primaryImage = $this->resolvePrimaryImageUrl($node);
    $image = $primaryImage !== NULL ? $this->absoluteUrl($primaryImage) : NULL;
    $budget = $this->buildBudgetParts($node);
    $title = $this->formatListTitle($node, $operationCode);
    $reference = trim((string) ($node->get('field_reference')->value ?? ''));
    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');

    $urlOptions = ['absolute' => TRUE];
    if ($langcode !== NULL && $langcode !== '') {
      $language = \Drupal::languageManager()->getLanguage($langcode);
      if ($language !== NULL) {
        $urlOptions['language'] = $language;
      }
    }

    return [
      'title' => $title,
      'reference' => $reference,
      'property_type' => $assetCode !== '' ? $this->dictionaryLabel('asset_type', $assetCode) : '',
      'surface' => $this->formatSurface($node),
      'location' => $this->formatListLocation($node),
      'price_amount' => $budget['amount'],
      'price_qualifiers' => $budget['qualifiers'] !== '' ? Markup::create($this->formatQualifiersMarkup($budget['qualifiers'])) : '',
      'price_on_request_label' => (string) ($this->budgetConfig()->get('on_request') ?? ''),
      'exclusive' => $this->isExclusive($node),
      'url' => Url::fromRoute('entity.node.canonical', ['node' => $node->id()], $urlOptions)->toString(),
      'cta_label' => (string) $this->t('View the property'),
      'image' => $image,
      'image_alt' => $this->resolveImageAlt($node, $title),
    ];
  }

  private function absoluteUrl(string $url): string {
    if (str_contains($url, '://')) {
      return $url;
    }

    return \Drupal::service('file_url_generator')->generateAbsoluteString($url);
  }

  private function formatListTitle(NodeInterface $node, string $operationCode): string {
    $commercial = trim((string) ($node->get('field_commercial_title')->value ?? ''));
    if ($commercial !== '') {
      return $commercial;
    }

    $parts = [];
    if ($operationCode !== '') {
      $parts[] = $this->dictionaryLabel('operation_type', $operationCode);
    }

    $assetCode = (string) ($node->get('field_asset_type')->value ?? '');
    if ($assetCode !== '') {
      $parts[] = $this->dictionaryLabel('asset_type', $assetCode);
    }

    if ($node->hasField('field_address') && !$node->get('field_address')->isEmpty()) {
      $address = $node->get('field_address')->first();
      $locality = trim((string) ($address->locality ?? ''));
      if ($locality !== '') {
        $parts[] = mb_strtoupper($locality);
      }
    }

    return $parts !== [] ? implode(' ', $parts) : ($node->label() ?? '');
  }

  private function formatListLocation(NodeInterface $node): ?string {
    if (!$node->hasField('field_address') || $node->get('field_address')->isEmpty()) {
      return NULL;
    }

    $address = $node->get('field_address')->first();
    if ($address === NULL) {
      return NULL;
    }

    $postal = trim((string) ($address->postal_code ?? ''));
    $locality = trim((string) ($address->locality ?? ''));
    $locality = $locality !== '' ? mb_strtoupper($locality) : '';
    $parts = array_filter([$postal, $locality]);

    return $parts !== [] ? implode(' ', $parts) : NULL;
  }

}
