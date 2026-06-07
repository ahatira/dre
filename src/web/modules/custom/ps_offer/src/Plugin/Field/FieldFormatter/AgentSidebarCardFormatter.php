<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Renders the primary agent inside the sidebar SDC wrapper.
 *
 * @FieldFormatter(
 *   id = "ps_offer_agent_sidebar_card",
 *   label = @Translation("Agent sidebar card"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
final class AgentSidebarCardFormatter extends EntityReferenceEntityFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'view_mode' => 'card',
      'consultant_label' => 'Your consultant',
      'contact_label' => 'Contact the consultancy',
      'visit_title' => 'Would you like to visit?',
      'visit_label' => 'Schedule a visit',
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
      '#rows' => 3,
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
    $elements = parent::viewElements($items, $langcode);
    if ($elements === []) {
      return $elements;
    }

    $agent_render = reset($elements);
    $agent_render['#consultant_label'] = (string) $this->getSetting('consultant_label');
    $offer = $items->getEntity();
    $agents = $this->getEntitiesToView($items, $langcode);
    $agent = $agents !== [] ? reset($agents) : NULL;
    $phone = $agent instanceof EntityInterface && $agent->hasField('phone')
      ? trim((string) ($agent->get('phone')->value ?? ''))
      : '';

    $contact_url = '';
    if ($offer instanceof NodeInterface) {
      $contact_url = Url::fromRoute('ps_form.offer_contact_modal', ['node' => $offer->id()], [
        'query' => [
          '_webform_dialog' => '1',
          'source_entity_type' => 'node',
          'source_entity_id' => $offer->id(),
        ],
      ])->toString();
    }

    return [
      0 => [
        '#type' => 'component',
        '#component' => 'ps_theme:agent-sidebar-card',
        '#props' => [
          'consultant_label' => (string) $this->getSetting('consultant_label'),
          'contact_label' => (string) $this->getSetting('contact_label'),
          'visit_title' => (string) $this->getSetting('visit_title'),
          'visit_label' => (string) $this->getSetting('visit_label'),
          'contact_url' => $contact_url,
          'contact_dialog_options' => (string) $this->getSetting('contact_dialog_options'),
          'visit_phone' => $this->normalizeTelUrl($phone),
          'visit_enabled' => $phone !== '',
        ],
        '#slots' => [
          'agent' => $agent_render,
        ],
      ],
    ];
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
