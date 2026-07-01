<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds a readable HTML table of offer snapshot fields for emails.
 */
final class OfferContactSnapshotEmailRecapBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Renders offer snapshot fields as an email-safe table.
   *
   * @param array<string, mixed> $data
   *   Webform submission values.
   */
  public function buildHtmlFromSnapshot(array $data, ?string $langcode = NULL): string {
    if (!OfferContactSnapshotFields::isComplete($data)) {
      return '';
    }

    $options = $langcode !== NULL && $langcode !== '' ? ['langcode' => $langcode] : [];
    $rows = [];
    foreach (OfferContactSnapshotFields::emailRecapLabels() as $key => $label) {
      $rows[] = [
        'label' => (string) $this->t($label, [], $options),
        'value' => $this->formatValue($key, $data[$key] ?? '', $options),
      ];
    }

    $build = [
      '#theme' => 'ps_offer_snapshot_email_recap',
      '#rows' => $rows,
    ];

    return (string) $this->renderer->renderPlain($build);
  }

  /**
   * @param array<string, string> $options
   */
  private function formatValue(string $key, mixed $raw, array $options): string {
    $value = trim((string) $raw);
    if ($value === '') {
      return '—';
    }

    if ($key === OfferContactSnapshotFields::OFFER_EXCLUSIVE) {
      return $value === '1'
        ? (string) $this->t('Yes', [], $options)
        : (string) $this->t('No', [], $options);
    }

    return $value;
  }

}
