<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form\Helper;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Drupal\focal_point\Plugin\Field\FieldWidget\FocalPointImageWidget;
use Drupal\ps_form\Service\ContactEmailHeroImageResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds focal point controls to managed_file hero upload elements.
 */
final class ContactEmailHeroUploadHelper {

  use StringTranslationTrait;

  /**
   * Admin preview style (must not use a focal point effect).
   */
  private const PREVIEW_STYLE = 'ps_form_email_hero_admin';

  public function __construct(
    private readonly ContactEmailHeroImageResolver $heroImageResolver,
    TranslationInterface $stringTranslation,
  ) {
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * Creates a helper instance from the container.
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_form.contact_email_hero_image_resolver'),
      $container->get('string_translation'),
    );
  }

  /**
   * Form API #process callback: preview + focal point UI after managed_file.
   *
   * @param array<string, mixed> $element
   *   The managed_file form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array<string, mixed> $form
   *   The full form.
   *
   * @return array<string, mixed>
   *   The processed element.
   */
  public function process(array $element, FormStateInterface $form_state, array $form): array {
    if (empty($element['#files'])) {
      return $element;
    }

    $file = reset($element['#files']);
    if ($file === FALSE) {
      return $element;
    }

    $selector = 'focal-point-' . implode('-', $element['#parents']);
    $defaultFocalPoint = $this->heroImageResolver->getFocalPointValue($file);

    $previewFocalPointValue = str_replace(',', 'x', $defaultFocalPoint);

    $element['preview'] = [
      '#type' => 'container',
      '#weight' => 10,
      'indicator' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => ['focal-point-indicator', $selector],
          'data-selector' => $selector,
        ],
      ],
      'thumbnail' => [
        '#theme' => 'image_style',
        '#style_name' => self::PREVIEW_STYLE,
        '#uri' => $file->getFileUri(),
        '#alt' => $element['#title'] ?? '',
      ],
      'preview_link' => [
        '#type' => 'link',
        '#title' => $this->t('Preview crop'),
        '#url' => Url::fromRoute('focal_point.preview', [
          'fid' => $file->id(),
          'focal_point_value' => $previewFocalPointValue,
        ], [
          'query' => ['focal_point_token' => FocalPointImageWidget::getPreviewToken()],
        ]),
        '#attached' => [
          'library' => ['core/drupal.dialog.ajax'],
        ],
        '#attributes' => [
          'class' => ['focal-point-preview-link', 'use-ajax', $selector . '-preview-link'],
          'data-selector' => $selector,
          'data-dialog-type' => 'modal',
        ],
      ],
    ];

    $element['focal_point'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Focal point'),
      '#description' => $this->t('Click the preview image to set the focus area used when cropping to the 2.35:1 email banner.'),
      '#default_value' => $defaultFocalPoint,
      '#element_validate' => [[FocalPointImageWidget::class, 'validateFocalPoint']],
      '#attributes' => [
        'class' => ['focal-point', $selector],
        'data-selector' => $selector,
        'data-field-name' => $selector,
      ],
      '#wrapper_attributes' => [
        'class' => ['focal-point-wrapper'],
      ],
      '#attached' => [
        'library' => ['focal_point/drupal.focal_point'],
      ],
      '#weight' => 20,
    ];

    return $element;
  }

}
