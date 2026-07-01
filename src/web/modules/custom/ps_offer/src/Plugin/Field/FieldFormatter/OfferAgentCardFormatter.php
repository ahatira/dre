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
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Drupal\ps_offer\Service\OfferContactResolver;
use Drupal\ps_offer\Service\OfferWebformModalBuilder;
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
    private readonly ContactDisplayModeManager $displayModeManager,
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
      $container->get('ps_form.contact_display_mode'),
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

    $contactLabel = (string) $this->t((string) $this->getSetting('contact_label'));
    $visitLabel = (string) $this->t((string) $this->getSetting('visit_label'));

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:offer-agent-card',
      '#props' => [
        'consultant_label' => (string) $this->t((string) $this->getSetting('consultant_label')),
        'contact_label' => $contactLabel,
        'contact_label_mobile' => (string) $this->t((string) $this->getSetting('contact_label_mobile')),
        'visit_title' => (string) $this->t((string) $this->getSetting('visit_title')),
        'visit_label' => $visitLabel,
        'visit_label_mobile' => (string) $this->t((string) $this->getSetting('visit_label_mobile')),
        'contact_link' => $this->buildWebformLinkProps($offer, 'offer_contact', 'btn btn-primary ps-offer-agent-card__contact', $contactLabel),
        'visit_link' => $this->buildWebformLinkProps($offer, 'schedule_visit', 'btn ps-offer-agent-card__visit-btn', $visitLabel),
        'mobile_contact_link' => $this->buildWebformLinkProps($offer, 'offer_contact', 'btn btn-primary ps-offer-agent-card__mobile-contact', $contactLabel),
        'mobile_visit_link' => $this->buildWebformLinkProps($offer, 'schedule_visit', 'btn ps-offer-agent-card__mobile-visit', (string) $this->t((string) $this->getSetting('visit_label_mobile'))),
      ],
      '#slots' => [
        'agent' => $agent_render,
      ],
      '#cache' => [
        'tags' => array_values(array_unique(array_merge(
          $offer->getCacheTags(),
          $agent instanceof EntityInterface ? $agent->getCacheTags() : [],
          ['config:ps_offer.settings', 'config:ps_form.settings'],
        ))),
        'contexts' => ['languages:language_interface'],
      ],
    ];
  }

  /**
   * Builds link props for an offer webform using the site contact display mode.
   *
   * @return array{url: string, class: string, attributes: array<string, string>}
   */
  private function buildWebformLinkProps(
    NodeInterface $offer,
    string $webformId,
    string $buttonClass,
    ?string $dialogTitle = NULL,
  ): array {
    if (!in_array($webformId, OfferWebformModalBuilder::OFFER_WEBFORMS, TRUE)) {
      return [
        'url' => '',
        'class' => $buttonClass,
        'attributes' => [],
      ];
    }

    $url = Url::fromRoute('ps_offer.webform', [
      'node' => $offer->id(),
      'webform' => $webformId,
    ], [
      'query' => [
        'source_entity_type' => 'node',
        'source_entity_id' => $offer->id(),
      ],
    ])->toString();

    $attributes = $this->displayModeManager->buildLinkAttributes($dialogTitle);
    $classes = trim($buttonClass . ' ' . ($attributes['class'] ?? ''));
    unset($attributes['class']);

    return [
      'url' => $url,
      'class' => $classes,
      'attributes' => $attributes,
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

}
