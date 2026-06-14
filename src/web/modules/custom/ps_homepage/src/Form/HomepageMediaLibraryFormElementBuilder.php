<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Form;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\media_library\MediaLibraryState;
use Drupal\media_library\MediaLibraryUiBuilder;
use Drupal\ps_content\Service\ContentMediaResolver;

/**
 * Builds Media Library pickers for homepage block configuration forms.
 */
final class HomepageMediaLibraryFormElementBuilder {

  use StringTranslationTrait;

  private const OPENER_ID = 'ps_homepage.media_library_opener.homepage_block';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContentMediaResolver $mediaResolver,
    private readonly MediaLibraryUiBuilder $mediaLibraryUiBuilder,
  ) {}

  /**
   * Builds a media library picker element.
   *
   * @return array<string, mixed>
   */
  public function build(
    \Stringable|string $title,
    mixed $defaultMid,
    bool $required = FALSE,
    array $allowedBundles = ['image'],
  ): array {
    $defaultMid = (int) $defaultMid;

    return [
      '#type' => 'container',
      '#title' => $title,
      '#required' => $required,
      '#default_mid' => $defaultMid > 0 ? $defaultMid : NULL,
      '#allowed_bundles' => $allowedBundles,
      '#attributes' => ['class' => ['ps-homepage-media-field']],
      '#process' => [
        [self::class, 'processElement'],
      ],
    ];
  }

  /**
   * Form API #process callback.
   *
   * @param array<string, mixed> $element
   * @param array<string, mixed> $form
   *
   * @return array<string, mixed>
   */
  public static function processElement(array $element, FormStateInterface $form_state, array &$form): array {
    $builder = \Drupal::service('ps_homepage.media_library_form_element_builder');
    \assert($builder instanceof self);

    unset($element['#process']);

    $allowedBundles = $element['#allowed_bundles'] ?? ['image'];
    if (!is_array($allowedBundles) || $allowedBundles === []) {
      $allowedBundles = ['image'];
    }

    $field_name = (string) end($element['#array_parents']);
    $parents = $element['#array_parents'];
    $parent_prefix = array_slice($parents, 0, -1);
    $storage_key = self::storageKey($parents);
    $mid = (int) ($form_state->get($storage_key) ?? $element['#default_mid'] ?? 0);
    $id_suffix = $parent_prefix ? '-' . implode('-', $parent_prefix) : '';
    $field_widget_id = implode(':', array_filter([$field_name, $id_suffix]));
    $wrapper_id = $field_name . '-media-library-wrapper' . $id_suffix;

    unset($element['#default_mid'], $element['#allowed_bundles']);

    $element['#type'] = 'fieldset';
    $element['#title'] = $element['#title'] ?? $builder->t('Image');
    $element['#required'] = (bool) ($element['#required'] ?? FALSE);
    $element['#attributes'] = [
      'id' => $wrapper_id,
      'class' => ['js-media-library-widget', 'ps-homepage-media-field'],
    ];
    $element['#attached']['library'][] = 'media_library/widget';
    $element['#theme_wrappers'] = [
      'fieldset__media_library_widget',
    ];

    if ($mid <= 0) {
      $element['#field_prefix']['empty_selection'] = [
        '#markup' => $builder->t('No media items are selected.'),
      ];
    }
    else {
      $element['preview'] = $builder->buildSelectedMediaPreview($mid);
      $element['remove_button'] = [
        '#type' => 'submit',
        '#value' => $builder->t('Remove'),
        '#name' => $field_name . '-media-library-remove' . $id_suffix,
        '#submit' => [[self::class, 'removeMediaSelection']],
        '#ajax' => [
          'callback' => [self::class, 'ajaxUpdateWidget'],
          'wrapper' => $wrapper_id,
          'progress' => [
            'type' => 'throbber',
            'message' => $builder->t('Removing media.'),
          ],
        ],
        '#limit_validation_errors' => [],
      ];
    }

    $state = MediaLibraryState::create(
      self::OPENER_ID,
      array_values($allowedBundles),
      (string) reset($allowedBundles),
      1,
      ['field_widget_id' => $field_widget_id],
    );

    $element['open_button'] = [
      '#type' => 'button',
      '#value' => $builder->t('Add media'),
      '#name' => $field_name . '-media-library-open-button' . $id_suffix,
      '#attributes' => [
        'class' => ['js-media-library-open-button', 'media-library-open-button', 'button'],
      ],
      '#media_library_state' => $state,
      '#ajax' => [
        'callback' => [self::class, 'openMediaLibrary'],
        'progress' => [
          'type' => 'throbber',
          'message' => $builder->t('Opening media library.'),
        ],
      ],
      '#limit_validation_errors' => [],
    ];

    if ($mid > 0) {
      $element['open_button']['#access'] = FALSE;
    }

    $element['media_library_selection'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'data-media-library-widget-value' => $field_widget_id,
      ],
    ];

    $element['media_library_update_widget'] = [
      '#type' => 'submit',
      '#value' => $builder->t('Update widget'),
      '#name' => $field_name . '-media-library-update' . $id_suffix,
      '#ajax' => [
        'callback' => [self::class, 'ajaxUpdateWidget'],
        'wrapper' => $wrapper_id,
        'progress' => [
          'type' => 'throbber',
          'message' => $builder->t('Adding selection.'),
        ],
      ],
      '#attributes' => [
        'data-media-library-widget-update' => $field_widget_id,
        'class' => ['js-hide'],
      ],
      '#submit' => [[self::class, 'submitMediaSelection']],
      '#limit_validation_errors' => [],
    ];

    $element['target_id'] = [
      '#type' => 'hidden',
      '#value' => $mid > 0 ? (string) $mid : '',
    ];

    return $element;
  }

  /**
   * AJAX callback to open the media library modal.
   */
  public static function openMediaLibrary(array $form, FormStateInterface $form_state): AjaxResponse {
    $triggering_element = $form_state->getTriggeringElement();
    $library_ui = \Drupal::service('media_library.ui_builder')->buildUi($triggering_element['#media_library_state']);
    $dialog_options = MediaLibraryUiBuilder::dialogOptions();

    return (new AjaxResponse())
      ->addCommand(new OpenModalDialogCommand($dialog_options['title'], $library_ui, $dialog_options));
  }

  /**
   * Submit handler: stores the selected media ID in form state.
   */
  public static function submitMediaSelection(array &$form, FormStateInterface $form_state): void {
    $button = $form_state->getTriggeringElement();
    $parents = array_slice($button['#array_parents'], 0, -1);
    $values = NestedArray::getValue($form_state->getUserInput(), $parents);

    $mid = 0;
    if (is_array($values) && !empty($values['media_library_selection'])) {
      $ids = explode(',', (string) $values['media_library_selection']);
      $ids = array_filter($ids, 'is_numeric');
      $mid = (int) reset($ids);
    }

    $form_state->set(self::storageKey($parents), $mid > 0 ? $mid : NULL);
    $form_state->setRebuild();
  }

  /**
   * Submit handler: clears the selected media ID.
   */
  public static function removeMediaSelection(array &$form, FormStateInterface $form_state): void {
    $button = $form_state->getTriggeringElement();
    $parents = array_slice($button['#array_parents'], 0, -1);
    $form_state->set(self::storageKey($parents), NULL);
    $form_state->setRebuild();
  }

  /**
   * AJAX callback: replaces the widget after selection changes.
   */
  public static function ajaxUpdateWidget(array $form, FormStateInterface $form_state): AjaxResponse {
    $triggering_element = $form_state->getTriggeringElement();
    $wrapper_id = $triggering_element['#ajax']['wrapper'];
    $parents = array_slice($triggering_element['#array_parents'], 0, -1);
    $element = NestedArray::getValue($form, $parents);

    if (isset($element['media_library_selection'])) {
      $element['media_library_selection']['#value'] = '';
    }

    return (new AjaxResponse())
      ->addCommand(new ReplaceCommand("#$wrapper_id", $element));
  }

  /**
   * @param list<string|int> $parents
   */
  private static function storageKey(array $parents): string {
    return 'ps_homepage_media:' . implode(':', $parents);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildSelectedMediaPreview(int $mid): array {
    $media = $this->entityTypeManager->getStorage('media')->load($mid);
    if (!$media instanceof MediaInterface) {
      return [];
    }

    $reference = $this->mediaResolver->resolve($mid);
    $fileUri = $this->resolveMediaFileUri($media);
    $preview = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-media-preview']],
    ];

    if ($fileUri !== NULL) {
      $preview['image'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-media-preview__image']],
        'img' => [
          '#theme' => 'image_style',
          '#style_name' => 'thumbnail',
          '#uri' => $fileUri,
          '#alt' => $reference->alt !== '' ? $reference->alt : $media->label(),
        ],
      ];
    }

    $preview['meta'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-media-preview__meta']],
      'label' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $media->label(),
        '#attributes' => ['class' => ['ps-homepage-media-preview__label']],
      ],
    ];

    if ($reference->credit !== '') {
      $preview['meta']['credit'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => '© ' . $reference->credit,
        '#attributes' => ['class' => ['ps-homepage-media-preview__credit']],
      ];
    }

    $preview['meta']['actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-media-preview__actions']],
      'edit' => [
        '#type' => 'link',
        '#title' => $this->t('Edit media & credit'),
        '#url' => Url::fromRoute('entity.media.edit_form', ['media' => $mid]),
        '#attributes' => [
          'class' => ['button', 'button--small'],
          'target' => '_blank',
        ],
      ],
    ];

    return $preview;
  }

  private function resolveMediaFileUri(MediaInterface $media): ?string {
    $candidates = match ($media->bundle()) {
      'image', 'visite_guided' => ['field_media_image'],
      'gallery' => ['field_media_gallery_image'],
      default => ['thumbnail', 'field_media_image'],
    };

    foreach ($candidates as $fieldName) {
      if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
        continue;
      }
      $file = $media->get($fieldName)->entity;
      if ($file instanceof FileInterface) {
        return $file->getFileUri();
      }
    }

    return NULL;
  }

}
