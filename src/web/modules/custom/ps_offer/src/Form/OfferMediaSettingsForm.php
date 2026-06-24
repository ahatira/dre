<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;

/**
 * Default offer image and gallery badge icons.
 */
final class OfferMediaSettingsForm extends ConfigFormBase {

  use IconAutocompleteHelperTrait;
  use OfferSettingsFormTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_media_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_offer.settings', 'ps_media.gallery_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $offerConfig = $this->offerSettingsConfig();
    $galleryConfig = $this->config('ps_media.gallery_settings');

    $form['default_image'] = [
      '#type' => 'details',
      '#title' => $this->t('Default offer image'),
      '#open' => TRUE,
    ];

    $default_image_fid = $this->resolveManagedFileFid(
      'default_image_fid',
      (int) ($offerConfig->get('default_image_fid') ?? 0),
      $form_state,
    );

    $form['default_image']['default_image_fid'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Default image'),
      '#description' => $this->t('Displayed when an offer has no photo in its gallery. Allowed formats: png jpg jpeg webp.'),
      '#upload_location' => 'public://ps-offer/default/',
      '#default_value' => $default_image_fid > 0 ? [$default_image_fid] : [],
      '#upload_validators' => [
        'FileExtension' => [
          'extensions' => 'png jpg jpeg webp',
        ],
      ],
      '#multiple' => FALSE,
    ];

    $form['default_image']['default_image_alt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default image alt text'),
      '#default_value' => $offerConfig->get('default_image_alt') ?? '',
    ];

    $form['badge_icons'] = [
      '#type' => 'details',
      '#title' => $this->t('Gallery badge icons'),
      '#description' => $this->t('Icons displayed on offer gallery badges (photos, videos, 3D visit, plan).'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['badge_icons']['badge_icon_photos'] = $this->buildIconPickerElement(
      $this->t('Photos badge icon'),
      $this->getIconDefault($galleryConfig->get('badge_icon_photos'), 'bnp_custom:camera'),
      ['required' => TRUE],
    );

    $form['badge_icons']['badge_icon_videos'] = $this->buildIconPickerElement(
      $this->t('Videos badge icon'),
      $this->getIconDefault($galleryConfig->get('badge_icon_videos'), 'bnp_custom:video'),
      ['required' => TRUE],
    );

    $form['badge_icons']['badge_icon_visit_3d'] = $this->buildIconPickerElement(
      $this->t('3D visit badge icon'),
      $this->getIconDefault($galleryConfig->get('badge_icon_visit_3d'), 'bnp_custom:visite-guidee'),
      ['required' => TRUE],
    );

    $form['badge_icons']['badge_icon_plan'] = $this->buildIconPickerElement(
      $this->t('Plan badge icon'),
      $this->getIconDefault($galleryConfig->get('badge_icon_plan'), 'bnp_custom:floors'),
      ['required' => TRUE],
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->saveOfferDefaultImage($form_state);

    $editable = $this->configFactory->getEditable('ps_media.gallery_settings');
    foreach ([
      'badge_icon_photos' => 'bnp_custom:camera',
      'badge_icon_videos' => 'bnp_custom:video',
      'badge_icon_visit_3d' => 'bnp_custom:visite-guidee',
      'badge_icon_plan' => 'bnp_custom:floors',
    ] as $key => $fallback) {
      $editable->set(
        $key,
        $this->extractIconId($this->getSubmittedIconValue($form_state, $key, 'badge_icons'), $fallback),
      );
    }
    $editable->save();

    parent::submitForm($form, $form_state);
  }

}
