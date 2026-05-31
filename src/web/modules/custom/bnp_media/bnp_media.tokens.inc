<?php

declare(strict_types=1);

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;

/**
 * Implements hook_token_info().
 */
function bnp_media_token_info(): array {
  return [
    'tokens' => [
      'media' => [
        'social_large' => [
          'name' => t('Social image large URL'),
          'description' => t('Returns a social large image URL for a media entity.'),
        ],
        'social_medium' => [
          'name' => t('Social image medium URL'),
          'description' => t('Returns a social medium image URL for a media entity.'),
        ],
        'social_small' => [
          'name' => t('Social image small URL'),
          'description' => t('Returns a social small image URL for a media entity.'),
        ],
      ],
      'node' => [
        'share-image' => [
          'name' => t('Node share image URL'),
          'description' => t('Returns a share image URL for a node.'),
        ],
      ],
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function bnp_media_tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
  $replacements = [];
  /** @var \Drupal\bnp_media\Service\SocialImageTokenResolver $resolver */
  $resolver = \Drupal::service('bnp_media.social_image_token_resolver');

  if ($type === 'media' && !empty($data['media']) && $data['media'] instanceof MediaInterface) {
    $media = $data['media'];
    foreach ($tokens as $name => $original) {
      $style = match ($name) {
        'social_large' => 'large',
        'social_medium' => 'medium',
        'social_small' => 'thumbnail',
        default => NULL,
      };
      if ($style !== NULL) {
        $replacements[$original] = $resolver->resolveMediaUrl($media, $style);
      }
    }
  }

  if ($type === 'node' && !empty($data['node']) && $data['node'] instanceof NodeInterface) {
    $node = $data['node'];
    foreach ($tokens as $name => $original) {
      $parts = explode(':', $name);
      if ($parts[0] !== 'share-image') {
        continue;
      }

      $field_name = $parts[1] ?? NULL;
      $style = $parts[2] ?? 'large';
      $replacements[$original] = $resolver->resolveNodeUrl($node, $style, $field_name);
    }
  }

  return $replacements;
}
