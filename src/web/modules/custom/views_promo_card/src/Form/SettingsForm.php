<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views_promo_card\Service\PatternRegistry;

/**
 * Global settings for Views Promo Card.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * Pattern registry service.
   */
  private PatternRegistry $patternRegistry;

  /**
   * Entity type manager.
   */
  private EntityTypeManagerInterface $entityTypeManager;

  /**
   * Module handler.
   */
  private ModuleHandlerInterface $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public static function create($container): static {
    /** @var static $instance */
    $instance = parent::create($container);
    $instance->patternRegistry = $container->get('views_promo_card.pattern_registry');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'views_promo_card_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['views_promo_card.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('views_promo_card.settings');
    $allowed_patterns = $config->get('allowed_patterns') ?? [];
    if (!is_array($allowed_patterns)) {
      $allowed_patterns = [];
    }

    $form['allowed_patterns'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed SDC patterns'),
      '#description' => $this->t('One pattern ID per line (for example <code>my_theme:search-push-card</code>). Only listed patterns can be used for promo cards.'),
      '#default_value' => implode("\n", $allowed_patterns),
      '#rows' => 6,
    ];

    $form['default_icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default icon'),
      '#description' => $this->t('Optional fallback icon for patterns that expose <code>icon_pack</code> and <code>icon_id</code> props. Use the pack:id format (for example <code>bnp_custom:entrusting-a-property</code>).'),
      '#default_value' => (string) ($config->get('default_icon') ?? ''),
      '#maxlength' => 255,
    ];

    $form['debug_log'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Log placement resolution'),
      '#description' => $this->t('Writes debug messages to the views_promo_card log channel.'),
      '#default_value' => $config->get('debug_log'),
    ];

    if ($this->moduleHandler->moduleExists('linkit')) {
      $profiles = $this->entityTypeManager->getStorage('linkit_profile')->loadMultiple();
      $profile_options = ['' => $this->t('- First available profile -')];
      foreach ($profiles as $profile) {
        $profile_options[$profile->id()] = $profile->label();
      }
      $form['linkit_profile'] = [
        '#type' => 'select',
        '#title' => $this->t('Linkit profile'),
        '#description' => $this->t('Profile used for URL fields in promo card forms.'),
        '#options' => $profile_options,
        '#default_value' => (string) ($config->get('linkit_profile') ?? ''),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $raw = (string) $form_state->getValue('allowed_patterns');
    $pattern_ids = array_values(array_filter(array_map('trim', preg_split('/\R/', $raw) ?: [])));
    foreach ($pattern_ids as $pattern_id) {
      if (!$this->patternRegistry->isDiscoverablePattern($pattern_id)) {
        $form_state->setErrorByName('allowed_patterns', $this->t('Pattern %pattern is not a discoverable SDC component.', [
          '%pattern' => $pattern_id,
        ]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $raw = (string) $form_state->getValue('allowed_patterns');
    $pattern_ids = array_values(array_unique(array_filter(array_map('trim', preg_split('/\R/', $raw) ?: []))));

    $this->config('views_promo_card.settings')
      ->set('allowed_patterns', $pattern_ids)
      ->set('default_icon', trim((string) $form_state->getValue('default_icon')))
      ->set('debug_log', (bool) $form_state->getValue('debug_log'))
      ->set('linkit_profile', trim((string) ($form_state->getValue('linkit_profile') ?? '')))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
