<?php

declare(strict_types=1);

namespace Drupal\ps_media\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Theme\Icon\IconDefinitionInterface;

/**
 * Configures offer gallery badge icons for site administrators.
 */
final class GallerySettingsForm extends ConfigFormBase {

  private const ICON_PACKS = ['bnp_custom'];

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

    $form['badge_icons']['badge_icon_photos'] = $this->buildIconField(
      $this->t('Photos badge icon'),
      $this->getIconDefault($config->get('badge_icon_photos'), 'bnp_custom:camera'),
    );

    $form['badge_icons']['badge_icon_videos'] = $this->buildIconField(
      $this->t('Videos badge icon'),
      $this->getIconDefault($config->get('badge_icon_videos'), 'bnp_custom:video'),
    );

    $form['badge_icons']['badge_icon_visit_3d'] = $this->buildIconField(
      $this->t('3D visit badge icon'),
      $this->getIconDefault($config->get('badge_icon_visit_3d'), 'bnp_custom:visite-guidee'),
    );

    $form['badge_icons']['badge_icon_plan'] = $this->buildIconField(
      $this->t('Plan badge icon'),
      $this->getIconDefault($config->get('badge_icon_plan'), 'bnp_custom:floors'),
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
        $this->extractIconId($this->getSubmittedIconValue($form_state, $key), $fallback),
      );
    }

    $editable->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Reads a submitted icon_autocomplete value from the form state.
   */
  private function getSubmittedIconValue(FormStateInterface $form_state, string $key): mixed {
    $badgeIcons = $form_state->getValue('badge_icons');
    if (is_array($badgeIcons) && array_key_exists($key, $badgeIcons)) {
      return $badgeIcons[$key];
    }

    return $form_state->getValue($key);
  }

  /**
   * Builds an icon autocomplete element for a gallery badge.
   *
   * @return array<string, mixed>
   *   Icon autocomplete form element.
   */
  private function buildIconField(\Stringable|string $title, string $defaultValue): array {
    return [
      '#type' => 'icon_autocomplete',
      '#title' => (string) $title,
      '#default_value' => $defaultValue,
      '#allowed_icon_pack' => self::ICON_PACKS,
      '#result_format' => 'grid',
      '#return_id' => TRUE,
      '#required' => TRUE,
    ];
  }

  /**
   * Returns a stored icon id or the configured fallback.
   */
  private function getIconDefault(mixed $value, string $fallback): string {
    if (is_string($value) && $value !== '') {
      return $value;
    }

    return $fallback;
  }

  /**
   * Extracts a pack:id icon value from an icon_autocomplete submission.
   */
  private function extractIconId(mixed $value, string $fallback): string {
    if (is_string($value) && $value !== '') {
      return $value;
    }

    if (!is_array($value)) {
      return $fallback;
    }

    if (!empty($value['target_id']) && is_string($value['target_id'])) {
      return $value['target_id'];
    }

    if (!empty($value['icon_id']) && is_string($value['icon_id'])) {
      return $value['icon_id'];
    }

    if (!empty($value['icon']) && $value['icon'] instanceof IconDefinitionInterface) {
      return $value['icon']->getId();
    }

    if (!empty($value['object']) && $value['object'] instanceof IconDefinitionInterface) {
      return $value['object']->getId();
    }

    return $fallback;
  }

}
