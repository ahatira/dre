<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\Field\FieldFormatter;

use Drupal\bnp_media\Service\OembedIframeBuilder;
use Drupal\bnp_media\Service\RemoteVideoProviderResolver;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'bnp_oembed' formatter.
 *
 * @FieldFormatter(
 *   id = "bnp_oembed",
 *   label = @Translation("BNP oEmbed iframe"),
 *   description = @Translation("Render remote video URLs via media.oembed_iframe."),
 *   field_types = {
 *     "string",
 *     "string_long",
 *     "link"
 *   }
 * )
 */
final class BnpOEmbedFormatter extends FormatterBase {

  /**
   * The provider resolver service.
   */
  protected RemoteVideoProviderResolver $providerResolver;

  /**
   * The iframe URL builder service.
   */
  protected OembedIframeBuilder $iframeBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->providerResolver = $container->get('bnp_media.remote_video_provider_resolver');
    $instance->iframeBuilder = $container->get('bnp_media.oembed_iframe_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'max_width' => 1024,
      'max_height' => 576,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['max_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Max width'),
      '#default_value' => (int) $this->getSetting('max_width'),
      '#min' => 0,
      '#step' => 1,
    ];
    $elements['max_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Max height'),
      '#default_value' => (int) $this->getSetting('max_height'),
      '#min' => 0,
      '#step' => 1,
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $summary[] = $this->t('Max width: @value', ['@value' => (int) $this->getSetting('max_width')]);
    $summary[] = $this->t('Max height: @value', ['@value' => (int) $this->getSetting('max_height')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $max_width = (int) $this->getSetting('max_width');
    $max_height = (int) $this->getSetting('max_height');

    foreach ($items as $delta => $item) {
      $url = $this->extractUrl($item);
      if ($url === '') {
        continue;
      }

      $provider = $this->providerResolver->resolveFromUrl($url);
      $iframe_src = $this->iframeBuilder->buildIframeUrl($url, $provider, $max_width, $max_height);

      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'iframe',
        '#attributes' => [
          'src' => $iframe_src,
          'title' => (string) $this->fieldDefinition->getLabel(),
          'loading' => 'lazy',
          'allowfullscreen' => 'allowfullscreen',
          'frameborder' => '0',
          'width' => $max_width > 0 ? (string) $max_width : '100%',
          'height' => $max_height > 0 ? (string) $max_height : '100%',
        ],
        '#attached' => [
          'library' => [
            'media/oembed.formatter',
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * Extracts a source URL from supported field item types.
   */
  private function extractUrl(FieldItemInterface $item): string {
    if (isset($item->uri)) {
      return (string) $item->uri;
    }

    if (isset($item->value)) {
      return (string) $item->value;
    }

    return '';
  }

}
