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
          'description' => t('Returns a BNPPRE-style city segment resolved from field_address (for example, paris-12-75012).'),
        ],
        'ps-offer-seo-prefix' => [
          'name' => t('Offer SEO prefix path'),
          'description' => t('Returns operation/asset/locality segments aligned with property search SEO URLs.'),
        ],
        'ps-offer-contact-email' => [
          'name' => t('Offer contact notification email'),
          'description' => t('Returns the resolved consultant notification email for this offer.'),
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
    'languages:language_url',
  ]);
  $bubbleable_metadata->addCacheTags(['config:ps_search.seo_url_mappings', 'config:ps_offer.settings', 'config:system.site']);

  $langcode = isset($options['langcode']) && is_string($options['langcode'])
    ? $options['langcode']
    : $node->language()->getId();
  /** @var \Drupal\ps_offer\Service\OfferPathTokenProvider $provider */
  $provider = \Drupal::service('ps_offer.path_token_provider');
  /** @var \Drupal\ps_offer\Service\OfferContactResolver $contact_resolver */
  $contact_resolver = \Drupal::service('ps_offer.contact_resolver');

  foreach ($tokens as $name => $original) {
    $replacement = match ($name) {
      'ps-offer-operation' => $provider->getOperationSegment($node, $langcode),
      'ps-offer-asset' => $provider->getAssetSegment($node, $langcode),
      'ps-offer-country' => $provider->getCountrySegment($node, $langcode),
      'ps-offer-department' => $provider->getDepartmentSegment($node, $langcode),
      'ps-offer-city' => $provider->getCitySegment($node, $langcode),
      'ps-offer-seo-prefix' => $provider->getSeoPrefixPath($node, $langcode),
      'ps-offer-contact-email' => $contact_resolver->resolveContactEmail($node),
      default => NULL,
    };

    // Never override token replacements owned by other modules.
    if ($replacement !== NULL) {
      $replacements[$original] = $replacement;
    }
  }

  return $replacements;
}
