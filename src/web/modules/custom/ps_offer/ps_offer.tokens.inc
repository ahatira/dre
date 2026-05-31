<?php

declare(strict_types=1);

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\node\NodeInterface;

/**
 * Implements hook_token_info().
 */
function ps_offer_token_info(): array {
  return [
    'tokens' => [
      'node' => [
        'ps-offer-operation' => [
          'name' => t('Offer operation path segment'),
          'description' => t('Returns a translated operation segment for offer URLs (for example, a-louer or a-vendre).'),
        ],
        'ps-offer-asset' => [
          'name' => t('Offer asset path segment'),
          'description' => t('Returns an asset segment resolved from offer dictionary labels.'),
        ],
        'ps-offer-country' => [
          'name' => t('Offer country path segment'),
          'description' => t('Returns a country segment resolved from field_address and localized country names.'),
        ],
        'ps-offer-department' => [
          'name' => t('Offer department path segment'),
          'description' => t('Returns a country-aware department/region segment resolved from field_address.'),
        ],
        'ps-offer-city' => [
          'name' => t('Offer city path segment'),
          'description' => t('Returns a city segment resolved from field_address locality.'),
        ],
      ],
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function ps_offer_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
  $replacements = [];

  if ($type !== 'node' || empty($data['node']) || !$data['node'] instanceof NodeInterface) {
    return $replacements;
  }

  $node = $data['node'];
  if ($node->bundle() !== 'offer') {
    return $replacements;
  }

  $bubbleable_metadata->addCacheableDependency($node);
  $bubbleable_metadata->addCacheContexts([
    'languages:language_interface',
    'languages:language_content',
  ]);

  $langcode = isset($options['langcode']) && is_string($options['langcode']) ? $options['langcode'] : NULL;
  /** @var \Drupal\ps_offer\Service\OfferPathTokenProvider $provider */
  $provider = \Drupal::service('ps_offer.path_token_provider');

  foreach ($tokens as $name => $original) {
    $replacement = match ($name) {
      'ps-offer-operation' => $provider->getOperationSegment($node, $langcode),
      'ps-offer-asset' => $provider->getAssetSegment($node, $langcode),
      'ps-offer-country' => $provider->getCountrySegment($node, $langcode),
      'ps-offer-department' => $provider->getDepartmentSegment($node, $langcode),
      'ps-offer-city' => $provider->getCitySegment($node),
      default => NULL,
    };

    // Never override token replacements owned by other modules.
    if ($replacement !== NULL) {
      $replacements[$original] = $replacement;
    }
  }

  return $replacements;
}
