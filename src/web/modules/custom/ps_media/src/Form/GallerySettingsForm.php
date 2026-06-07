<?php

declare(strict_types=1);

namespace Drupal\ps_media\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;

/**
 * Configures offer gallery badge icons for site administrators.
 */
final class GallerySettingsForm extends ConfigFormBase {

  use IconAutocompleteHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_media.gallery_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_media_gallery_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_media.gallery_settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Choose the icons displayed on offer gallery badges (photos, videos, 3D visit, plan).') . '</p>',
    ];

    $form['badge_icons'] = [
      '#type' => 'details',
      '#title' => $this->t('Gallery badge icons'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['badge_icons']['badge_icon_photos'] = $this->buildIconPickerElement(
      $this->t('Photos badge icon'),
      $this->getIconDefault($config->get('badge_icon_photos'), 'bnp_custom:camera'),
      ['required' => TRUE],
    );

    $form['badge_icons']['badge_icon_videos'] = $this->buildIconPickerElement(
      $this->t('Videos badge icon'),
      $this->getIconDefault($config->get('badge_icon_videos'), 'bnp_custom:video'),
      ['required' => TRUE],
    );

    $form['badge_icons']['badge_icon_visit_3d'] = $this->buildIconPickerElement(
      $this->t('3D visit badge icon'),
      $this->getIconDefault($config->get('badge_icon_visit_3d'), 'bnp_custom:visite-guidee'),
      ['required' => TRUE],
    );

    $form['badge_icons']['badge_icon_plan'] = $this->buildIconPickerElement(
      $this->t('Plan badge icon'),
      $this->getIconDefault($config->get('badge_icon_plan'), 'bnp_custom:floors'),
      ['required' => TRUE],
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
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
