<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_agent\Entity\AgentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default offer contact agent settings.
 */
final class OfferContactSettingsForm extends ConfigFormBase {

  use OfferSettingsFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
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
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_contact_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_offer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->offerSettingsConfig();
    $default_agent = NULL;
    $agent_id = (int) ($config->get('default_contact_agent') ?? 0);
    if ($agent_id > 0) {
      $default_agent = $this->entityTypeManager->getStorage('ps_agent')->load($agent_id);
      if (!$default_agent instanceof AgentInterface) {
        $default_agent = NULL;
      }
    }

    $form['intro'] = [
      '#type' => 'item',
      '#markup' => $this->t('When an offer has no primary or secondary agent, this consultant is shown on the detail page and used for contact form notifications (before the site email fallback).'),
    ];

    $form['default_contact_agent'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Default contact agent'),
      '#description' => $this->t('Published ps_agent entity used as fallback consultant.'),
      '#target_type' => 'ps_agent',
      '#default_value' => $default_agent,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->configFactory->getEditable('ps_offer.settings')
      ->set('default_contact_agent', (int) $form_state->getValue('default_contact_agent'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
