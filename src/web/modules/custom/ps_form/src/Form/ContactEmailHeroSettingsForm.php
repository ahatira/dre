<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Element\ManagedFile;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\ps_form\Form\Helper\ContactEmailHeroUploadHelper;
use Drupal\ps_form\Form\Trait\ContactConfigFormTrait;
use Drupal\ps_form\Service\ContactEmailHeroImageResolver;
use Drupal\ps_form\Service\ContactEmailSettings;
use Drupal\ps_form\Service\ContactNeedRouter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Per-webform hero banner settings for confirmation emails.
 */
final class ContactEmailHeroSettingsForm extends ConfigFormBase {

  use ContactConfigFormTrait;

  /**
   * Hub webform metadata for hero field labels.
   */
  protected ContactNeedRouter $contactNeedRouter;

  /**
   * File entity storage for marking uploaded heroes permanent.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Adds focal point UI to hero upload widgets.
   */
  protected ContactEmailHeroUploadHelper $heroUploadHelper;

  /**
   * Applies the email hero image style on output.
   */
  protected ContactEmailHeroImageResolver $heroImageResolver;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->contactNeedRouter = $container->get('ps_form.contact_need_router');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->heroUploadHelper = ContactEmailHeroUploadHelper::create($container);
    $instance->heroImageResolver = $container->get('ps_form.contact_email_hero_image_resolver');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_form_contact_email_hero_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $emailConfig = $this->getEmailConfig();
    $heroes = is_array($emailConfig['webform_heroes'] ?? NULL) ? $emailConfig['webform_heroes'] : [];
    $heroStyles = is_array($emailConfig['webform_hero_image_styles'] ?? NULL) ? $emailConfig['webform_hero_image_styles'] : [];
    $definitions = $this->contactNeedRouter->getAllManagedWebforms();
    $ratio = ContactEmailHeroImageResolver::ASPECT_RATIO;
    $defaultStyle = ImageStyle::load(ContactEmailSettings::DEFAULT_HERO_IMAGE_STYLE);
    $defaultStyleLabel = $defaultStyle !== NULL
      ? $defaultStyle->label()
      : ContactEmailSettings::DEFAULT_HERO_IMAGE_STYLE;
    $styleOptions = image_style_options(FALSE);
    unset($styleOptions[ContactEmailSettings::DEFAULT_HERO_IMAGE_STYLE], $styleOptions['ps_form_email_hero_admin']);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Upload one hero banner per hub webform. By default, images are cropped to a @ratio:1 cinema banner (@style). You can override the image style per webform below. Set the focal point on each preview to control the crop when the style uses focal point.',
        [
          '@ratio' => (string) $ratio,
          '@style' => $defaultStyleLabel,
        ],
      ) . '</p>',
    ];

    $form['heroes'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    foreach (ContactEmailSettings::HUB_WEBFORM_IDS as $webformId) {
      $title = $definitions[$webformId]['title'] ?? $webformId;
      $fid = (int) ($heroes[$webformId] ?? 0);
      $form['heroes'][$webformId] = [
        '#type' => 'details',
        '#title' => $title,
        '#open' => TRUE,
        '#tree' => TRUE,
        'image_style' => [
          '#type' => 'select',
          '#title' => $this->t('Image style'),
          '#description' => $this->t('Leave as default to use the standard email hero banner crop.'),
          '#options' => ['' => $this->t('Default (@style)', ['@style' => $defaultStyleLabel])] + $styleOptions,
          '#default_value' => (string) ($heroStyles[$webformId] ?? ''),
        ],
        'upload' => [
          '#type' => 'managed_file',
          '#title' => $this->t('Hero image'),
          '#upload_location' => 'public://ps-form/email-heroes',
          '#default_value' => $fid > 0 ? [$fid] : [],
          '#upload_validators' => [
            'FileExtension' => ['extensions' => 'png jpg jpeg webp'],
            'FileIsImage' => [],
          ],
          '#process' => [
            [ManagedFile::class, 'processManagedFile'],
            [$this->heroUploadHelper, 'process'],
          ],
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $heroesInput = $form_state->getValue('heroes');
    $heroes = [];
    $heroImageStyles = [];
    if (is_array($heroesInput)) {
      foreach (ContactEmailSettings::HUB_WEBFORM_IDS as $webformId) {
        $row = $heroesInput[$webformId] ?? [];
        $upload = is_array($row) ? ($row['upload'] ?? []) : [];
        $fid = is_array($upload) ? (int) ($upload[0] ?? 0) : 0;
        $focalPoint = is_array($upload) ? (string) ($upload['focal_point'] ?? '') : '';
        $styleId = trim((string) ($row['image_style'] ?? ''));
        if ($styleId !== '' && ImageStyle::load($styleId) === NULL) {
          $styleId = '';
        }
        $heroImageStyles[$webformId] = $styleId;

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
        $heroes[$webformId] = $fid;
      }
    }

    $this->saveEmailConfigPartial([
      'webform_heroes' => $heroes,
      'webform_hero_image_styles' => $heroImageStyles,
    ]);

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_form.email_heroes');
  }

}
