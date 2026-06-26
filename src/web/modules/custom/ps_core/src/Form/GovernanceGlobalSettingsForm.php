<?php

declare(strict_types=1);

namespace Drupal\ps_core\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Global import governance defaults and inheritance settings.
 */
final class GovernanceGlobalSettingsForm extends ConfigFormBase {

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
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
      $container->get('ps_core.import_governance_global_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_core_governance_global_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [ImportGovernanceGlobalResolver::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(ImportGovernanceGlobalResolver::CONFIG_NAME);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Global CRM import defaults apply to all domains unless a domain policy overrides them. Domain forms can show inheritance hints when a setting uses Inherit.',
      ) . '</p>',
    ];

    $form['hierarchy'] = [
      '#type' => 'item',
      '#title' => $this->t('Configuration hierarchy'),
      '#markup' => $this->t('Global CRM pipeline → domain governance → per-entity protection flags.'),
    ];

    $form['current_global'] = [
      '#type' => 'item',
      '#title' => $this->t('Current global lock strategy'),
      '#markup' => $this->globalResolver->getGlobalLockStrategyLabel(),
    ];

    $pipelineRoute = $this->globalResolver->getGlobalLockStrategySettingsRouteName();
    if ($pipelineRoute !== NULL && Url::fromRoute($pipelineRoute)->access()) {
      $form['pipeline_link'] = [
        '#type' => 'item',
        '#title' => $this->t('CRM import pipeline'),
        '#markup' => Link::createFromRoute(
          $this->t('Edit CRM import pipeline settings'),
          $pipelineRoute,
        )->toString(),
      ];
    }

    $form['import'] = [
      '#type' => 'details',
      '#title' => $this->t('Domain form hints'),
      '#open' => TRUE,
    ];
    $form['import']['show_domain_inheritance_hints'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show inheritance hints on domain governance forms'),
      '#description' => $this->t(
        'When enabled, domain forms display the effective global CRM lock strategy next to inherit options.',
      ),
      '#default_value' => $config->get('import.show_domain_inheritance_hints'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(ImportGovernanceGlobalResolver::CONFIG_NAME)
      ->set(
        'import.show_domain_inheritance_hints',
        (bool) $form_state->getValue('show_domain_inheritance_hints'),
      )
      ->save();

    parent::submitForm($form, $form_state);
  }

}
