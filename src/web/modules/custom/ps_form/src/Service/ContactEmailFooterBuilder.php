<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_email\Service\EmailShellSettings;

/**
 * Builds footer variables for contact confirmation emails.
 */
final class ContactEmailFooterBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EmailShellSettings $emailShellSettings,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Returns footer variables for email-wrap preprocessing.
   *
   * @return array<string, mixed>
   *   Footer variables.
   */
  public function buildFooterVariables(?string $langcode = NULL): array {
    $siteConfig = $this->configFactory->get('system.site');
    $siteName = (string) ($siteConfig->get('name') ?? 'BNP Paribas Real Estate');

    $address = $this->emailShellSettings->getFooterScalar('address', $langcode);
    $phone = $this->emailShellSettings->getFooterScalar('phone', $langcode);
    $phoneLink = $this->emailShellSettings->getFooterScalar('phone_link', $langcode);
    $offersUrl = $this->emailShellSettings->getFooterScalar('offers_url', $langcode);
    $offersLabel = $this->emailShellSettings->getFooterScalar('offers_label', $langcode);
    $services = $this->emailShellSettings->getFooterScalar('services', $langcode);

    if ($this->emailShellSettings->reuseSiteFooter()) {
      $social = $this->loadSocialLinks();
      if ($address === '' && $social['address'] !== '') {
        $address = $social['address'];
      }
      if ($phone === '' && $social['phone'] !== '') {
        $phone = $social['phone'];
        $phoneLink = $social['phone_link'];
      }
      if ($services === '' && $social['services'] !== '') {
        $services = $social['services'];
      }
    }

    if ($offersUrl === '') {
      $offersUrl = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
    }
    if ($offersLabel === '') {
      $offersLabel = (string) $this->t('Browse all our listings on @url', ['@url' => parse_url($offersUrl, PHP_URL_HOST) ?: $offersUrl]);
    }

    return [
      'ps_contact_footer_address' => $address,
      'ps_contact_footer_phone' => $phone,
      'ps_contact_footer_phone_link' => $phoneLink !== '' ? $phoneLink : ($phone !== '' ? 'tel:' . preg_replace('/\s+/', '', $phone) : ''),
      'ps_contact_footer_offers_url' => $offersUrl,
      'ps_contact_footer_offers_label' => $offersLabel,
      'ps_contact_footer_services' => $services,
      'ps_contact_footer_social_links' => $this->loadSocialLinks()['links'],
      'ps_contact_footer_legal' => $this->emailShellSettings->getLegalMarkup($langcode),
      'ps_contact_footer_site_name' => $siteName,
    ];
  }

  /**
   * Loads social and contact hints from the theme footer block when available.
   *
   * @return array{links: list<array{label: string, url: string}>, address: string, phone: string, phone_link: string, services: string}
   *   Parsed footer data.
   */
  private function loadSocialLinks(): array {
    $result = [
      'links' => [],
      'address' => '',
      'phone' => '',
      'phone_link' => '',
      'services' => '',
    ];

    $block = $this->entityTypeManager->getStorage('block')->load('ps_theme_footer_social');
    if ($block !== NULL) {
      $settings = $block->get('settings') ?? [];
      $platforms = is_array($settings['platforms'] ?? NULL) ? $settings['platforms'] : [];
      foreach ($platforms as $platform => $data) {
        if (!is_array($data)) {
          continue;
        }
        $value = trim((string) ($data['value'] ?? ''));
        if ($value === '') {
          continue;
        }
        $label = trim((string) ($data['description'] ?? ucfirst((string) $platform)));
        $url = str_contains($value, '://') ? $value : match ($platform) {
          'linkedin' => 'https://www.linkedin.com/' . ltrim($value, '/'),
          'youtube' => 'https://www.youtube.com/' . ltrim($value, '/'),
          'twitter', 'x' => 'https://twitter.com/' . ltrim($value, '/'),
          'email' => 'mailto:' . $value,
          default => $value,
        };
        $result['links'][] = [
          'label' => $label,
          'url' => $url,
        ];
      }
    }

    return $result;
  }

}
