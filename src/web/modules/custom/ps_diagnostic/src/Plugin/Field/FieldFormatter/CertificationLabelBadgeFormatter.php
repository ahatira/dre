<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders certification labels with check heading and badge image.
 */
#[FieldFormatter(
  id: 'certification_label_badge',
  label: new TranslatableMarkup('Certification label badge'),
  field_types: ['entity_reference'],
)]
final class CertificationLabelBadgeFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    mixed $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
    );
  }

  public static function defaultSettings(): array {
    return [
      'image_style' => 'certification_label_badge',
    ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);
    $options = $this->entityTypeManager->getStorage('image_style')->getQuery()
      ->accessCheck(FALSE)
      ->execute();
    $style_options = array_combine($options, $options);

    $elements['image_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Badge image style'),
      '#options' => $style_options,
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => $this->t('- None -'),
    ];

    return $elements;
  }

  public function settingsSummary(): array {
    $style = (string) $this->getSetting('image_style');
    return [
      $style !== '' ? $this->t('Badge image style: @style', ['@style' => $style]) : $this->t('Badge image style: none'),
    ];
  }

  public function viewElements(FieldItemListInterface $items, $langcode): array {
    if (!$items instanceof EntityReferenceFieldItemListInterface) {
      return [];
    }

    $labels = [];
    foreach ($items->referencedEntities() as $entity) {
      if (!$entity instanceof TermInterface || $entity->bundle() !== 'certification_label') {
        continue;
      }

      $labels[] = [
        '#theme' => 'ps_certification_label_item',
        '#label' => $entity->label(),
        '#badge' => $this->buildBadgeRenderArray($entity, $langcode),
        '#cache' => [
          'tags' => $entity->getCacheTags(),
        ],
      ];
    }

    if ($labels === []) {
      return [];
    }

    return [
      [
        '#theme' => 'ps_certification_label_list',
        '#items' => $labels,
        '#attached' => [
          'library' => ['ps_diagnostic/certification_label'],
        ],
      ],
    ];
  }

  /**
   * Builds the badge image render array for a certification label term.
   */
  private function buildBadgeRenderArray(TermInterface $term, string $langcode): array {
    if (!$term->hasField('field_badge') || $term->get('field_badge')->isEmpty()) {
      return [];
    }

    $media = $term->get('field_badge')->entity;
    if (!$media instanceof MediaInterface || !$media->hasField('field_media_image') || $media->get('field_media_image')->isEmpty()) {
      return [];
    }

    $file = $media->get('field_media_image')->entity;
    if (!$file instanceof FileInterface) {
      return [];
    }

    $style = trim((string) $this->getSetting('image_style'));
    if ($style === '') {
      return [
        '#theme' => 'image',
        '#uri' => $file->getFileUri(),
        '#alt' => $term->label(),
        '#title' => $term->label(),
        '#cache' => [
          'tags' => array_merge($media->getCacheTags(), $file->getCacheTags()),
        ],
      ];
    }

    return [
      '#theme' => 'image_style',
      '#style_name' => $style,
      '#uri' => $file->getFileUri(),
      '#alt' => $term->label(),
      '#title' => $term->label(),
      '#cache' => [
        'tags' => array_merge($media->getCacheTags(), $file->getCacheTags()),
      ],
    ];
  }

}
