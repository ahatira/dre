<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_email\Service\OfferEmailCardHtmlRenderer;
use Drupal\ps_search\Entity\SearchAlert;
use Drupal\ps_theme\Utility\OfferEmailCardPropsBuilder;

/**
 * Sends search alert digest emails.
 */
final class SearchAlertMailer {

  use StringTranslationTrait;

  public function __construct(
    private readonly MailManagerInterface $mailManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RendererInterface $renderer,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly OfferEmailCardHtmlRenderer $offerEmailCardHtmlRenderer,
    private readonly SearchAlertCriteriaSummaryBuilder $criteriaSummaryBuilder,
  ) {}

  /**
   * Sends a digest email for the given alert and offer IDs.
   *
   * @param array<int, int> $nids
   */
  public function sendDigest(SearchAlert $alert, array $nids): bool {
    if ($alert->get('optout_email')->value) {
      return FALSE;
    }

    $email = $alert->getProfEmail();
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return FALSE;
    }

    $langcode = $alert->language()->getId() ?: $this->languageManager->getDefaultLanguage()->getId();
    $body = $this->buildBody($alert, $nids, $langcode);
    if ($body === '') {
      return FALSE;
    }

    $settings = $this->configFactory->get('ps_search.alert_settings');
    $siteName = (string) ($this->configFactory->get('system.site')->get('name') ?? '');
    $subject = (string) $this->t('@count new @properties for your alert “@title”', [
      '@count' => count($nids),
      '@properties' => count($nids) === 1 ? $this->t('property') : $this->t('properties'),
      '@title' => $alert->getAlertName(),
      '@site' => $siteName !== '' ? $siteName : 'Property Search',
    ], ['langcode' => $langcode]);

    $params = [
      'subject' => $subject,
      'body' => $body,
      'bcc' => trim((string) ($settings->get('bcc_mail') ?? '')),
      'from_mail' => trim((string) ($settings->get('from_mail') ?? '')),
      'from_name' => trim((string) ($settings->get('from_name') ?? '')),
    ];

    $result = $this->mailManager->mail(
      'ps_search',
      'search_alert_digest',
      $email,
      $langcode,
      $params,
    );

    return !empty($result['result']);
  }

  /**
   * Marks alert as sent after successful digest dispatch.
   *
   * @param array<int, int> $nids
   */
  public function markSent(SearchAlert $alert, array $nids): void {
    $alert->set('last_sent', \Drupal::time()->getRequestTime());
    $alert->set('last_match_count', count($nids));
    $alert->save();
  }

  /**
   * @param array<int, int> $nids
   */
  private function buildBody(SearchAlert $alert, array $nids, string $langcode): string {
    $cards = [];
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    foreach ($nodes as $node) {
      if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
        continue;
      }
      $props = OfferEmailCardPropsBuilder::build($node, $langcode);
      $cards[] = $this->offerEmailCardHtmlRenderer->renderSearch($props);
    }

    if ($cards === []) {
      return '';
    }

    $criteria = $this->loadCriteria($alert);
    $structured = $this->criteriaSummaryBuilder->buildStructured($criteria);
    $searchUrl = $alert->get('search_url')->value;
    if ((!is_string($searchUrl) || $searchUrl === '') && !empty($criteria['search_url'])) {
      $searchUrl = (string) $criteria['search_url'];
    }

    $build = [
      '#theme' => 'ps_search_alert_digest_body',
      '#list_title' => (string) $this->t('New matching properties:', [], ['langcode' => $langcode]),
      '#criteria_zones' => $structured['zones'],
      '#criteria_tags' => $structured['criteria'],
      '#cards' => $cards,
      '#search_url' => is_string($searchUrl) && $searchUrl !== '' ? $searchUrl : NULL,
      '#search_cta_label' => (string) $this->t('View your saved search', [], ['langcode' => $langcode]),
    ];

    return (string) $this->renderer->renderPlain($build);
  }

  /**
   * @return array<string, mixed>
   */
  private function loadCriteria(SearchAlert $alert): array {
    $raw = $alert->get('criteria')->value;
    if (!is_string($raw) || $raw === '') {
      return [];
    }

    try {
      $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException) {
      return [];
    }

    return is_array($decoded) ? $decoded : [];
  }

}
