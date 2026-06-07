<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\text\Plugin\Field\FieldType\TextItemBase;

/**
 * Renders offer description with a read-more toggle.
 *
 * @FieldFormatter(
 *   id = "ps_offer_description",
 *   label = @Translation("Offer description (collapsible)"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
final class OfferDescriptionFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    foreach ($items as $delta => $item) {
      if (!$item instanceof TextItemBase) {
        continue;
      }
      $text = (string) ($item->value ?? '');
      if ($text === '') {
        continue;
      }

      $elements[$delta] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-offer-description', 'ps-offer-description--collapsible']],
        'content' => [
          '#type' => 'processed_text',
          '#text' => $text,
          '#format' => $item->format ?? 'basic_html',
          '#langcode' => $item->getLangcode(),
          '#attributes' => ['class' => ['ps-offer-description__content']],
        ],
        'toggle' => [
          '#type' => 'html_tag',
          '#tag' => 'button',
          '#value' => $this->t('See more'),
          '#attributes' => [
            'type' => 'button',
            'class' => ['btn', 'btn-link', 'ps-offer-description__toggle'],
            'data-ps-read-more' => '',
          ],
        ],
        '#attached' => [
          'library' => ['ps_theme/offer-detail'],
        ],
      ];
    }

    return $elements;
  }

}
