<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Drupal\ps_offer_webform\Service\OfferContactEmailBlockBuilder;
use Drupal\ps_offer_webform\Service\OfferContactSnapshotEmailRecapBuilder;
use Drupal\ps_offer_webform\Service\OfferContactSnapshotFields;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Offer recap tokens for offer webform emails.
 */
final class OfferWebformEmailTokensHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly ContactWebformEmailSettings $contactWebformEmailSettings,
    private readonly OfferContactEmailBlockBuilder $offerContactEmailBlockBuilder,
    private readonly OfferContactSnapshotEmailRecapBuilder $offerContactSnapshotEmailRecapBuilder,
  ) {}

  /**
   * Declares offer email tokens on webform submissions.
   */
  #[Hook('token_info')]
  public function tokenInfo(): array {
    return [
      'tokens' => [
        'webform_submission' => [
          'ps-offer-email-block' => [
            'name' => (string) $this->t('Offer recap block'),
            'description' => (string) $this->t('Search-style offer card and primary CTA for offer contact emails.'),
          ],
          'ps-offer-snapshot-recap' => [
            'name' => (string) $this->t('Offer snapshot recap'),
            'description' => (string) $this->t('Readable table of offer snapshot fields captured at submission time.'),
          ],
        ],
      ],
    ];
  }

  /**
   * Replaces offer email tokens.
   *
   * @return array<string, mixed>
   */
  #[Hook('tokens')]
  public function tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
    if ($type !== 'webform_submission') {
      return [];
    }

    $submission = $data['webform_submission'] ?? NULL;
    if (!$submission instanceof WebformSubmissionInterface) {
      return [];
    }

    $webformId = $submission->getWebform()->id();
    if (!$this->contactWebformEmailSettings->isOfferWebform($webformId)) {
      return [];
    }

    $bubbleable_metadata->addCacheableDependency($submission);
    $bubbleable_metadata->addCacheableDependency($submission->getWebform());

    $submissionData = $submission->getData();
    if (!OfferContactSnapshotFields::isComplete($submissionData)) {
      return $this->emptyReplacements($tokens);
    }

    $langcode = $options['langcode'] ?? $submission->language()->getId();
    $langcode = is_string($langcode) ? $langcode : NULL;

    $replacements = [];

    if (isset($tokens['ps-offer-email-block'])) {
      $html = $this->offerContactEmailBlockBuilder->buildHtmlFromSnapshot($submissionData, $langcode);
      $replacements[$tokens['ps-offer-email-block']] = $html !== '' ? Markup::create($html) : '';
    }

    if (isset($tokens['ps-offer-snapshot-recap'])) {
      $html = $this->offerContactSnapshotEmailRecapBuilder->buildHtmlFromSnapshot($submissionData, $langcode);
      $replacements[$tokens['ps-offer-snapshot-recap']] = $html !== '' ? Markup::create($html) : '';
    }

    return $replacements;
  }

  /**
   * @param array<string, string> $tokens
   *
   * @return array<string, string>
   */
  private function emptyReplacements(array $tokens): array {
    $replacements = [];
    foreach (['ps-offer-email-block', 'ps-offer-snapshot-recap'] as $name) {
      if (isset($tokens[$name])) {
        $replacements[$tokens[$name]] = '';
      }
    }

    return $replacements;
  }

}
