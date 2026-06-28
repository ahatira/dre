<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_agent\Entity\AgentInterface;
use Drupal\ps_offer\Service\OfferContactResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders the primary agent inside the offer agent card SDC wrapper.
 *
 * @FieldFormatter(
 *   id = "ps_offer_agent_card",
 *   label = @Translation("Offer agent card"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
final class OfferAgentCardFormatter extends EntityReferenceEntityFormatter {

  public function __construct(
    string $plugin_id,
    mixed $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    LoggerChannelFactoryInterface $logger_factory,
    EntityTypeManagerInterface $entity_type_manager,
    EntityDisplayRepositoryInterface $entity_display_repository,
    private readonly OfferContactResolver $contactResolver,
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings,
      $logger_factory,
      $entity_type_manager,
      $entity_display_repository,
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_display.repository'),
      $container->get('ps_offer.contact_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'view_mode' => 'card',
      'consultant_label' => 'Your consultant',
      'contact_label' => 'Contact the consultancy',
      'contact_label_mobile' => 'Contact',
      'visit_title' => 'Would you like to visit?',
      'visit_label' => 'Schedule a visit',
      'visit_label_mobile' => 'Visit',
      'contact_dialog_options' => '{"width":800,"dialogClasses":"modal-dialog-centered modal-lg"}',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['consultant_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Consultant label'),
      '#default_value' => $this->getSetting('consultant_label'),
      '#required' => TRUE,
    ];
    $elements['contact_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Contact button label'),
      '#default_value' => $this->getSetting('contact_label'),
      '#required' => TRUE,
    ];
    $elements['visit_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Visit section title'),
      '#default_value' => $this->getSetting('visit_title'),
      '#required' => TRUE,
    ];
    $elements['visit_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Visit button label'),
      '#default_value' => $this->getSetting('visit_label'),
      '#required' => TRUE,
    ];
    $elements['contact_dialog_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Contact modal dialog options (JSON)'),
      '#default_value' => $this->getSetting('contact_dialog_options'),
      '#required' => TRUE,
      '#rows'  => 3,
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Consultant label: @label', [
      '@label' => $this->getSetting('consultant_label'),
    ]);
    $summary[] = $this->t('Contact label: @label', [
      '@label' => $this->getSetting('contact_label'),
    ]);
    $summary[] = $this->t('Visit title: @label', [
      '@label' => $this->getSetting('visit_title'),
    ]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $offer = $items->getEntity();
    if (!$offer instanceof NodeInterface || $offer->bundle() !== 'offer') {
      return [];
    }

    $agent = $this->contactResolver->resolveAgent($offer);
    $agent_render = $agent instanceof AgentInterface
      ? $this->buildAgentRenderArray($agent, $langcode)
      : ['#markup' => ''];

    return [$this->buildAgentCardElement($agent, $agent_render, $offer)];
  }

  /**
   * Builds the agent card SDC render element.
   *
   * @param array<string, mixed> $agent_render
   *
   * @return array<string, mixed>
   */
  private function buildAgentCardElement(?AgentInterface $agent, array $agent_render, NodeInterface $offer): array {
    if ($agent instanceof AgentInterface) {
      $agent_render['#consultant_label'] = (string) $this->t((string) $this->getSetting('consultant_label'));
    }

    $phone = $agent instanceof AgentInterface && $agent->hasField('phone')
      ? trim((string) ($agent->get('phone')->value ?? ''))
      : '';

    $contact_url = Url::fromRoute('ps_offer.webform', [
      'node' => $offer->id(),
      'webform' => 'offer_contact',
    ], [
      'query' => [
        '_webform_dialog' => '1',
        'source_entity_type' => 'node',
        'source_entity_id' => $offer->id(),
      ],
    ])->toString();

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:offer-agent-card',
      '#props' => [
        'consultant_label' => (string) $this->t((string) $this->getSetting('consultant_label')),
        'contact_label' => (string) $this->t((string) $this->getSetting('contact_label')),
        'contact_label_mobile' => (string) $this->t((string) $this->getSetting('contact_label_mobile')),
        'visit_title' => (string) $this->t((string) $this->getSetting('visit_title')),
        'visit_label' => (string) $this->t((string) $this->getSetting('visit_label')),
        'visit_label_mobile' => (string) $this->t((string) $this->getSetting('visit_label_mobile')),
        'contact_url' => $contact_url,
        'contact_dialog_options' => (string) $this->getSetting('contact_dialog_options'),
        'visit_phone' => $this->normalizeTelUrl($phone),
        'visit_enabled' => $phone !== '',
      ],
      '#slots' => [
        'agent' => $agent_render,
      ],
      '#cache' => [
        'tags' => array_values(array_unique(array_merge(
          $offer->getCacheTags(),
          $agent instanceof EntityInterface ? $agent->getCacheTags() : [],
          ['config:ps_offer.settings'],
        ))),
        'contexts' => ['languages:language_interface'],
      ],
    ];
  }

  /**
   * Builds the ps_agent view mode render array.
   *
   * @return array<string, mixed>
   */
  private function buildAgentRenderArray(AgentInterface $agent, string $langcode): array {
    $view_mode = (string) ($this->getSetting('view_mode') ?? 'card');
    $view_builder = $this->entityTypeManager->getViewBuilder('ps_agent');
    return $view_builder->view($agent, $view_mode, $langcode);
  }

  /**
   * Builds a tel: URL from a raw phone number.
   */
  private function normalizeTelUrl(string $phone): string {
    if ($phone === '') {
      return '';
    }

    $normalized = preg_replace('/[^\d+]/', '', $phone) ?? '';
    if ($normalized === '') {
      return '';
    }

    return 'tel:' . $normalized;
  }

}
