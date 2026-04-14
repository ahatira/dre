<?php

/**
 * @file
 * Contains ps_media.tokens.inc.
 */

/**
 * To have all PS Media general and global tokens.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Language\LanguageInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\node\Entity\Node;

/**
 * Get the URL with image style for a selected media entity.
 *
 * @param \Drupal\media\MediaInterface $media_entity
 *   The entity object for media with image.
 * @param string|null $style_name
 *   The name of the image style.
 *
 * @return string|null
 *   The image URL by media entity and image style name.
 */
function ps_media__image_url(MediaInterface $media_entity, ?string $style_name = NULL) {
  $image_field_name = ps_media__default_image_field_name($media_entity);

  if ($img_entity = $media_entity->get($image_field_name)->first()) {
    if ($file_entity = $img_entity->get('entity')->getTarget()) {
      if (!empty($style_name) && ImageStyle::load($style_name)) {
        return ImageStyle::load($style_name)
          ->buildUrl($file_entity->get('uri')->first()->getString());
      }

      return Drupal::service('file_url_generator')->generateAbsoluteString($file_entity->get('uri')->getString());
    }
  }

  return ps_media__get_fallback_social_share_image_url();
}

/**
 * Get the default image field name for a media entity.
 */
function ps_media__default_image_field_name(MediaInterface $media_entity): string {
  if (isset($media_entity->field_media_image)
    && !empty($media_entity->get('field_media_image')->first())) {
    return 'field_media_image';
  }

  if (isset($media_entity->field_media_cover_image)
    && !empty($media_entity->get('field_media_cover_image')->first())) {
    return 'field_media_cover_image';
  }

  return 'thumbnail';
}

/**
 * Get a fallback social share image URL from the active theme.
 */
function ps_media__get_fallback_social_share_image_url(): string {
  $active_theme = Drupal::theme()->getActiveTheme()->getPath();
  $origin_url = Drupal::request()->getSchemeAndHttpHost() . Drupal::request()->getBaseUrl();
  return $origin_url . '/' . $active_theme . '/share-image.png';
}

/**
 * Get the share image URL for a node.
 */
function ps_media__get_node_share_image_url(Node $node, ?string $style_name = 'social_large', ?string $field_name = ''): string {
  $image_url = '';

  if ($field_name === '') {
    if ($node->hasField('field_media') && !$node->get('field_media')->isEmpty()) {
      $entity = $node->get('field_media')->entity;
      if ($entity instanceof MediaInterface) {
        $image_url = ps_media__image_url($entity, $style_name);
      }
    }
    elseif ($node->hasField('field_image') && !$node->get('field_image')->isEmpty()) {
      $entity = $node->get('field_image')->entity;
      if ($entity instanceof MediaInterface) {
        $image_url = ps_media__image_url($entity, $style_name);
      }
    }
    elseif ($node->hasField('field_video') && !$node->get('field_video')->isEmpty()) {
      $entity = $node->get('field_video')->entity;
      if ($entity instanceof MediaInterface) {
        $image_url = ps_media__image_url($entity, $style_name);
      }
    }
  }
  elseif ($node->hasField($field_name) && !$node->get($field_name)->isEmpty()) {
    $entity = $node->get($field_name)->entity;
    if ($entity instanceof MediaInterface) {
      $image_url = ps_media__image_url($entity, $style_name);
    }
  }

  return $image_url === '' ? ps_media__get_fallback_social_share_image_url() : $image_url;
}

/**
 * Implements hook_token_info().
 */
function ps_media_token_info() {
  $info = [];

  $info['tokens']['media']['social_large'] = [
    'name' => t('Social Large'),
    'description' => t("Social Large (1200x630) image for the selected media type."),
    'module' => 'media',
    'type' => 'url',
  ];

  $info['tokens']['media']['social_medium'] = [
    'name' => t('Social Medium'),
    'description' => t("Social Medium (600x315) image for the selected media type."),
    'module' => 'media',
    'type' => 'url',
  ];

  $info['tokens']['media']['social_small'] = [
    'name' => t('Social Small'),
    'description' => t("Social Small (280x150) image for the selected media type."),
    'module' => 'media',
    'type' => 'url',
  ];

  // Define the new 'share-image' token type.
  $info['types']['share-image'] = [
    'name' => t('Share Image'),
    'description' => t('The social share image for the node. Checks field_media, field_image, field_video, and falls back to the theme default image if none are found.'),
    'needs-data' => 'node',
    'nested' => TRUE,
  ];

  // Define tokens for each field.
  $info['tokens']['share-image']['field_media'] = [
    'name' => t('Share Image from field_media'),
    'description' => t('The share image from field_media, if available.'),
    'type' => 'media',
  ];

  $info['tokens']['share-image']['field_image'] = [
    'name' => t('Share Image from field_image'),
    'description' => t('The share image from field_image, if available.'),
    'type' => 'media',
  ];

  $info['tokens']['share-image']['field_video'] = [
    'name' => t('Share Image from field_video'),
    'description' => t('The share image from field_video, if available.'),
    'type' => 'media',
  ];

  // Add share-image to node.
  $info['tokens']['node']['share-image'] = [
    'name' => t('Share Image'),
    'description' => t('The social share image for the node. Checks field_media, field_image, field_video, and falls back to the theme default image if none are found.'),
    'type' => 'share-image',
  ];

  return $info;
}

/**
 * Implements hook_tokens().
 */
function ps_media_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  if (isset($options['langcode'])) {
    $langcode = $options['langcode'];
  }
  else {
    $langcode = LanguageInterface::LANGCODE_DEFAULT;
  }

  $replacements = [];
  if ($type === 'media' && !empty($data['media'])) {
    /** @var \Drupal\media\MediaInterface $media_entity */
    $media_entity = Drupal::service('entity.repository')->getTranslationFromContext($data['media'], $langcode, ['operation' => 'media_entity_tokens']);

    foreach ($tokens as $token_name => $original) {
      switch ($token_name) {

        // Social Large (1200x630) image for the selected media type.
        case 'social_large':
          $replacements[$original] = ps_media__image_url($media_entity, 'social_large');
          break;

        // Social Medium (600x315) image for the selected media type.
        case 'social_medium':
          $replacements[$original] = ps_media__image_url($media_entity, 'social_medium');
          break;

        // Social Small (280x150) image for the selected media type.
        case 'social_small':
          $replacements[$original] = ps_media__image_url($media_entity, 'social_small');
          break;
      }
    }
  }

  if ($type === 'node' && !empty($data['node'])) {
    /** @var \Drupal\node\Entity\Node $node */
    $node = $data['node'];

    $replacements = [];
    foreach ($tokens as $token_name => $original) {

      // When only using the smart [node:share-image] token.
      if ($token_name == 'share-image') {
        $replacements[$original] = ps_media__get_node_share_image_url($node, 'social_large');
      }
      // When the the smart [node:share-image:#######:#####] has extra specific options.
      elseif ($token_name == 'share-image:') {

        // Get the array of token options.
        $token_options = explode(":", $token_name);

        // Get the field name.
        $field_name = '';
        if (isset($token_options[1]) && $token_options[1] != '') {
          $field_name = $token_options[1];
        }

        // Get the social image style name.
        $style_name = 'social_large';
        if (isset($token_options[2]) && $token_options[2] != '') {
          $style_name = $token_options[2];
        }

        $replacements[$original] = ps_media__get_node_share_image_url($node, $style_name, $field_name);

      }
    }
  }

  return $replacements;
}
