<?php

namespace Drupal\ps_media\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'ps_media_documents_formatter' formatter.
 *
 * Renders document media items with conditional visibility.
 *
 * @FieldFormatter(
 *   id = "ps_media_documents_formatter",
 *   label = @Translation("Documents (PS Media)"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class DocumentsFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'hide_if_empty' => TRUE,  // Q19: hide block if empty
      'show_titles' => TRUE,
      'link_text' => 'Download',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['hide_if_empty'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide block if no documents'),
      '#default_value' => $this->getSetting('hide_if_empty'),
      '#description' => $this->t('When checked, the entire Documents block will be hidden if there are no documents.'),
    ];

    $elements['show_titles'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show document titles'),
      '#default_value' => $this->getSetting('show_titles'),
    ];

    $elements['link_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Download link text'),
      '#default_value' => $this->getSetting('link_text'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $summary[] = $this->t('Hide if empty: @hide', [
      '@hide' => $this->getSetting('hide_if_empty') ? 'Yes' : 'No',
    ]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    // Implement Q19: Hide if empty
    if ($items->isEmpty() && $this->getSetting('hide_if_empty')) {
      return $elements;
    }

    if ($items->isEmpty()) {
      return $elements;
    }

    // Get media entities (files only)
    $entities = $this->getEntitiesToView($items, $langcode);

    if (empty($entities)) {
      return $elements;
    }

    // Build render array for documents
    $elements[0] = [
      '#theme' => 'documents_list',
      '#offer' => $items->getEntity(),
      '#documents' => $entities,
      '#show_titles' => $this->getSetting('show_titles'),
      '#link_text' => $this->getSetting('link_text'),
      '#attached' => [
        'library' => ['ps_media/documents'],
      ],
    ];

    return $elements;
  }

}
