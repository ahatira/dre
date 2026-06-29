<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileInterface;
use Drupal\ps_form\Form\Trait\ContactConfigFormTrait;
use Drupal\ps_form\Service\ContactEmailSettings;
use Drupal\ps_form\Service\ContactNeedRouter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Per-webform hero banner settings for confirmation emails.
 */
final class ContactEmailHeroSettingsForm extends ConfigFormBase {

  use ContactConfigFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ContactNeedRouter $contactNeedRouter,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('ps_form.contact_need_router'),
      $container->get('entity_type.manager'),
    );
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
    $definitions = $this->contactNeedRouter->getAllManagedWebforms();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Upload one hero banner per hub webform. Recommended size: 1200×600 px (PNG, JPG or WebP).',
      ) . '</p>',
    ];

    $form['heroes'] = [
      '#type' => 'details',
      '#title' => $this->t('Hero banners'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    foreach (ContactEmailSettings::HUB_WEBFORM_IDS as $webformId) {
      $title = $definitions[$webformId]['title'] ?? $webformId;
      $fid = (int) ($heroes[$webformId] ?? 0);
      $form['heroes'][$webformId] = [
        '#type' => 'managed_file',
        '#title' => $title,
        '#upload_location' => 'public://ps-form/email-heroes',
        '#default_value' => $fid > 0 ? [$fid] : [],
        '#upload_validators' => [
          'FileExtension' => ['extensions' => 'png jpg jpeg webp'],
          'FileImageDimensions' => ['maxDimensions' => '1200x600'],
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
    if (is_array($heroesInput)) {
      foreach (ContactEmailSettings::HUB_WEBFORM_IDS as $webformId) {
        $value = $heroesInput[$webformId] ?? [];
        $fid = is_array($value) ? (int) ($value[0] ?? 0) : 0;
        if ($fid > 0) {
          $file = $this->entityTypeManager->getStorage('file')->load($fid);
          if ($file instanceof FileInterface) {
            $file->setPermanent();
            $file->save();
          }
        }
        $heroes[$webformId] = $fid;
      }
    }

    $this->saveEmailConfigPartial(['webform_heroes' => $heroes]);

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_form.email_heroes');
  }

}
