<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Drupal\ps_form\Form\Helper\ContactEmailHeroUploadHelper;
use Drupal\ps_form\Form\Helper\ManagedFileFormValueHelper;
use Drupal\ps_form\Service\ContactEmailHeroImageResolver;
use Drupal\ps_form\Service\ContactWebformEmailHeroSettings;
use Drupal\webform\WebformInterface;

/**
 * Confirmation email hero settings on hub webform General settings.
 */
final class ContactWebformEmailHeroHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly ContactWebformEmailHeroSettings $heroSettings,
    private readonly ContactEmailHeroImageResolver $heroImageResolver,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Adds hero upload controls to hub webform third-party settings.
   */
  #[Hook('webform_third_party_settings_form_alter')]
  public function webformThirdPartySettingsFormAlter(array &$form, FormStateInterface $form_state): void {
    $webform = $form_state->getFormObject()?->getEntity();
    if (!$webform instanceof WebformInterface) {
      return;
    }

    $webformId = $webform->id();
    if (!in_array($webformId, ContactWebformEmailSettings::HUB_WEBFORM_IDS, TRUE)) {
      return;
    }

    $configuredFid = $this->heroSettings->getHeroFileId($webformId) ?? 0;
    $uploadParents = [
      'third_party_settings',
      ContactWebformEmailHeroSettings::THIRD_PARTY_MODULE,
      'hero_file',
      'upload',
    ];
    $fid = ManagedFileFormValueHelper::resolveNestedManagedFileFid($uploadParents, $configuredFid, $form_state);

    $defaultStyle = ImageStyle::load(ContactWebformEmailHeroSettings::DEFAULT_HERO_IMAGE_STYLE);
    $defaultStyleLabel = $defaultStyle !== NULL
      ? $defaultStyle->label()
      : ContactWebformEmailHeroSettings::DEFAULT_HERO_IMAGE_STYLE;
    $styleOptions = image_style_options(FALSE);
    unset(
      $styleOptions[ContactWebformEmailHeroSettings::DEFAULT_HERO_IMAGE_STYLE],
      $styleOptions['ps_form_email_hero_admin'],
    );

    $form['third_party_settings'][ContactWebformEmailHeroSettings::THIRD_PARTY_MODULE] = [
      '#type' => 'details',
      '#title' => $this->t('Confirmation email hero'),
      '#description' => $this->t(
        'Banner shown at the top of visitor confirmation emails for this webform. Copy is managed in the email hub; subject line stays on the webform email handler.',
      ),
      '#open' => TRUE,
      '#weight' => 50,
      '#tree' => TRUE,
      'email_hero_image_style' => [
        '#type' => 'select',
        '#title' => $this->t('Image style'),
        '#description' => $this->t('Leave as default to use the standard @ratio:1 email hero banner crop (@style).', [
          '@ratio' => (string) ContactEmailHeroImageResolver::ASPECT_RATIO,
          '@style' => $defaultStyleLabel,
        ]),
        '#options' => ['' => $this->t('Default (@style)', ['@style' => $defaultStyleLabel])] + $styleOptions,
        '#default_value' => (string) $webform->getThirdPartySetting(
          ContactWebformEmailHeroSettings::THIRD_PARTY_MODULE,
          ContactWebformEmailHeroSettings::SETTING_STYLE,
          '',
        ),
      ],
      'hero_file' => [
        '#type' => 'container',
        '#tree' => TRUE,
        'upload' => [
          '#type' => 'managed_file',
          '#title' => $this->t('Hero image'),
          '#upload_location' => 'public://ps-form/email-heroes',
          '#default_value' => $fid > 0 ? [$fid] : [],
          '#upload_validators' => [
            'FileExtension' => ['extensions' => 'png jpg jpeg webp'],
            'FileIsImage' => [],
          ],
          '#multiple' => FALSE,
        ],
      ],
    ];

    $form['third_party_settings'][ContactWebformEmailHeroSettings::THIRD_PARTY_MODULE]['hero_file']['#after_build'][] = [
      ContactEmailHeroUploadHelper::class,
      'afterBuildWebformHeroUpload',
    ];
  }

  /**
   * Attaches hero submit handler to the Save button (not $form['#submit']).
   *
   * Drupal only executes the triggering element's #submit handlers on Save.
   */
  #[Hook('form_webform_settings_form_alter')]
  public function formWebformSettingsFormAlter(array &$form, FormStateInterface $form_state): void {
    $webform = $form_state->getFormObject()?->getEntity();
    if (!$webform instanceof WebformInterface) {
      return;
    }

    if (!in_array($webform->id(), ContactWebformEmailSettings::HUB_WEBFORM_IDS, TRUE)) {
      return;
    }

    if (!isset($form['actions']['submit']['#submit'])) {
      return;
    }

    $handlers = $form['actions']['submit']['#submit'];
    foreach ($handlers as $handler) {
      if (is_array($handler) && ($handler[0] ?? NULL) === self::class && ($handler[1] ?? NULL) === 'submitHeroSettings') {
        return;
      }
    }

    array_unshift($form['actions']['submit']['#submit'], [self::class, 'submitHeroSettings']);
  }

  /**
   * Persists hero file id, focal point and image style before webform save.
   *
   * @param array<string, mixed> $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function submitHeroSettings(array &$form, FormStateInterface $form_state): void {
    /** @var self $hooks */
    $hooks = \Drupal::service(ContactWebformEmailHeroHooks::class);
    $hooks->processHeroSettingsSubmit($form_state);
  }

  /**
   * Normalizes hero third-party settings on webform settings submit.
   */
  private function processHeroSettingsSubmit(FormStateInterface $form_state): void {
    $webform = $form_state->getFormObject()?->getEntity();
    if (!$webform instanceof WebformInterface) {
      return;
    }

    $webformId = $webform->id();
    if (!in_array($webformId, ContactWebformEmailSettings::HUB_WEBFORM_IDS, TRUE)) {
      return;
    }

    $module = ContactWebformEmailHeroSettings::THIRD_PARTY_MODULE;
    $settings = $form_state->getValue(['third_party_settings', $module]);
    if (!is_array($settings)) {
      return;
    }

    $heroFile = is_array($settings['hero_file'] ?? NULL) ? $settings['hero_file'] : [];
    $upload = is_array($heroFile['upload'] ?? NULL) ? $heroFile['upload'] : [];
    $configuredFid = $this->heroSettings->getHeroFileId($webformId) ?? 0;
    $fid = ManagedFileFormValueHelper::extractManagedFileFid($upload);
    if ($fid <= 0) {
      $fid = ManagedFileFormValueHelper::resolveNestedManagedFileFid(
        ['third_party_settings', $module, 'hero_file', 'upload'],
        $configuredFid,
        $form_state,
      );
    }

    $focalPoint = trim((string) ($heroFile['focal_point'] ?? ''));
    $styleId = trim((string) ($settings['email_hero_image_style'] ?? ''));
    if ($styleId !== '' && ImageStyle::load($styleId) === NULL) {
      $styleId = '';
    }

    if ($fid > 0) {
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      if ($file instanceof FileInterface) {
        $file->setPermanent();
        $file->save();
        if ($focalPoint !== '') {
          $this->heroImageResolver->saveFocalPoint($file, $focalPoint, $webformId);
        }
      }
    }

    $webform->unsetThirdPartySetting($module, 'hero_file');
    if ($fid > 0) {
      $webform->setThirdPartySetting($module, ContactWebformEmailHeroSettings::SETTING_FID, $fid);
    }
    else {
      $webform->unsetThirdPartySetting($module, ContactWebformEmailHeroSettings::SETTING_FID);
    }
    $webform->setThirdPartySetting($module, ContactWebformEmailHeroSettings::SETTING_STYLE, $styleId);

    $normalized = [
      ContactWebformEmailHeroSettings::SETTING_FID => $fid,
      ContactWebformEmailHeroSettings::SETTING_STYLE => $styleId,
    ];

    $form_state->setValue(['third_party_settings', $module], $normalized);
  }

}
