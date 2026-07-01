<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Service;

use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_email\Service\OfferEmailCardHtmlRenderer;
use Drupal\ps_offer_webform\Service\OfferContactSnapshotFields;
use Drupal\ps_offer_webform\Service\OfferContactSnapshotPropsBuilder;

/**
 * Builds the offer recap block for offer webform confirmation emails.
 */
final class OfferContactEmailBlockBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly OfferEmailCardHtmlRenderer $offerEmailCardHtmlRenderer,
    private readonly OfferContactSnapshotPropsBuilder $snapshotPropsBuilder,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Renders search-style offer card + primary CTA from snapshot submission data.
   *
   * @param array<string, mixed> $data
   *   Webform submission values containing offer snapshot keys.
   */
  public function buildHtmlFromSnapshot(array $data, ?string $langcode = NULL): string {
    if (!OfferContactSnapshotFields::isComplete($data)) {
      return '';
    }

    $langcode ??= is_string($data[OfferContactSnapshotFields::OFFER_LANGCODE] ?? NULL)
      ? (string) $data[OfferContactSnapshotFields::OFFER_LANGCODE]
      : NULL;
    $props = $this->snapshotPropsBuilder->buildCardPropsFromData($data, $langcode);
    $offerUrl = is_string($props['url'] ?? NULL) ? (string) $props['url'] : '';
    if ($offerUrl === '') {
      return '';
    }

    $build = [
      '#theme' => 'ps_offer_contact_email_block',
      '#card' => $this->offerEmailCardHtmlRenderer->renderSearch($props),
      '#offer_url' => $offerUrl,
      '#cta_label' => is_string($props['cta_label'] ?? NULL) && $props['cta_label'] !== ''
        ? (string) $props['cta_label']
        : (string) $this->t('View the property', [], ['langcode' => $langcode]),
    ];

    return (string) $this->renderer->renderPlain($build);
  }

}
