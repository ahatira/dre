<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form\Helper;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Url;
use Drupal\focal_point\Plugin\Field\FieldWidget\FocalPointImageWidget;
use Drupal\ps_form\Service\ContactEmailSettings;
use Drupal\ps_form\Service\ContactEmailHeroImageResolver;

/**
 * Adds focal point controls next to managed_file hero upload elements.
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
   * Form API #after_build callback: preview + focal point UI beside uploads.
   *
   * Static entry point avoids non-serializable closures in AJAX form cache.
   *
   * @param array<string, mixed> $form
   *   The complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array<string, mixed>
   *   The form with hero focal point widgets attached.
   */
  public static function afterBuildHeroSettingsForm(array $form, FormStateInterface $form_state): array {
    if (empty($form['heroes']) || !is_array($form['heroes'])) {
      return $form;
    }

    /** @var self $helper */
    $helper = \Drupal::service('ps_form.contact_email_hero_upload_helper');
    foreach (ContactEmailSettings::HUB_WEBFORM_IDS as $webformId) {
      if (!isset($form['heroes'][$webformId]['hero_file']) || !is_array($form['heroes'][$webformId]['hero_file'])) {
        continue;
      }
      $form['heroes'][$webformId]['hero_file'] = $helper->attachFocalPointUi(
        $form['heroes'][$webformId]['hero_file'],
        $form_state,
      );
    }

    return $form;
  }

  /**
   * Adds preview and focal point fields next to a hero managed_file element.
   *
   * @param array<string, mixed> $element
   *   The hero upload group container.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The active form state.
   *
   * @return array<string, mixed>
   *   The processed element.
   */
  public function attachFocalPointUi(array $element, FormStateInterface $form_state): array {
    if (empty($element['upload']['#files'])) {
      return $element;
    }

    $file = reset($element['upload']['#files']);
    if ($file === FALSE) {
      return $element;
    }

    $selector = 'focal-point-' . implode('-', $element['#parents']);
    $defaultFocalPoint = $this->heroImageResolver->getFocalPointValue($file);
    $previewFocalPointValue = str_replace(',', 'x', $defaultFocalPoint);
    $focalParents = array_merge($element['#parents'], ['focal_point']);

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
        '#alt' => $element['upload']['#title'] ?? '',
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

    $focalElement = [
      '#type' => 'textfield',
      '#title' => $this->t('Focal point'),
      '#description' => $this->t('Click the preview image to set the focus area used when cropping to the 2.35:1 email banner.'),
      '#default_value' => $defaultFocalPoint,
      '#parents' => $focalParents,
      '#element_validate' => [[self::class, 'validateHeroFocalPoint']],
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
    \Drupal::formBuilder()->doBuildForm('', $focalElement, $form_state);
    $element['focal_point'] = $focalElement;

    return $element;
  }

  /**
   * Validates optional hero focal point input on full form submit.
   *
   * @param array<string, mixed> $element
   *   The focal point form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validateHeroFocalPoint(array &$element, FormStateInterface $form_state): void {
    $value = trim((string) ($element['#value'] ?? ''));
    if ($value === '') {
      return;
    }

    if (\Drupal::service('focal_point.manager')->validateFocalPoint($value)) {
      return;
    }

    $form_state->setError($element, \Drupal::translation()->translate(
      'The @title field should be in the form "leftoffset,topoffset" where offsets are in percentages. Ex: 25,75.',
      ['@title' => strtolower((string) ($element['#title'] ?? 'focal point'))],
    ));
  }

}
