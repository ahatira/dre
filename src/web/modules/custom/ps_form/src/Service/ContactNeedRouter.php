<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\Yaml\Yaml;

/**
 * Maps hub contact "need" values to direct-access webforms and routes.
 */
final class ContactNeedRouter {

  use StringTranslationTrait;

  public const HUB_WEBFORM_ID = 'contact';

  public const RENT_NEED = 'rent';

  public const FROM_HUB_QUERY = 'from_hub';

  public const FROM_HUB_FIELD = 'ps_from_hub';

  /**
   * Direct webforms keyed by hub need radio value.
   *
   * @var array<string, array{webform: string, path: string, title: string}>
   */
  private const DIRECT_BY_NEED = [
    'delegate' => [
      'webform' => 'entrust_search',
      'path' => '/form/entrust-search',
      'title' => 'Entrust my search',
    ],
    'advise' => [
      'webform' => 'get_advice',
      'path' => '/form/get-advice',
      'title' => 'Get advice',
    ],
    'market' => [
      'webform' => 'entrust_property',
      'path' => '/form/entrust-property',
      'title' => 'Entrust a property',
    ],
    'sell' => [
      'webform' => 'invest_sell',
      'path' => '/form/invest-sell',
      'title' => 'Invest or sell a property',
    ],
    'other' => [
      'webform' => 'other_request',
      'path' => '/form/other-request',
      'title' => 'Other request',
    ],
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns direct need definitions (webform, path, title).
   *
   * @return array<string, array{webform: string, path: string, title: string}>
   *   Direct need definitions keyed by need code.
   */
  public function getDirectDefinitions(): array {
    return self::DIRECT_BY_NEED;
  }

  /**
   * Returns the direct webform id for a hub need, if any.
   */
  public function getDirectWebformId(string $need): ?string {
    return self::DIRECT_BY_NEED[$need]['webform'] ?? NULL;
  }

  /**
   * Returns the public path for a webform id.
   */
  public function getPathForWebform(string $webformId): ?string {
    foreach (self::DIRECT_BY_NEED as $definition) {
      if ($definition['webform'] === $webformId) {
        return $definition['path'];
      }
    }

    return $webformId === self::HUB_WEBFORM_ID ? '/form/contact' : NULL;
  }

  /**
   * Returns hub need for a direct webform id.
   */
  public function getNeedForWebform(string $webformId): ?string {
    foreach (self::DIRECT_BY_NEED as $need => $definition) {
      if ($definition['webform'] === $webformId) {
        return $need;
      }
    }

    return NULL;
  }

  /**
   * Lists direct webform ids (implemented or planned).
   *
   * @return list<string>
   *   Direct webform machine names.
   */
  public function getDirectWebformIds(): array {
    return array_values(array_map(
      static fn (array $definition): string => $definition['webform'],
      self::DIRECT_BY_NEED,
    ));
  }

  /**
   * Maps hub need values to webform ids for JavaScript redirect.
   *
   * @return array<string, string>
   *   Need codes keyed to webform machine names.
   */
  public function getNeedToWebformMap(): array {
    $map = [];
    foreach (self::DIRECT_BY_NEED as $need => $definition) {
      $map[$need] = $definition['webform'];
    }

    return $map;
  }

  /**
   * Maps webform ids to shell route paths.
   *
   * @return array<string, string>
   *   Webform machine names keyed to public paths.
   */
  public function getWebformPathMap(): array {
    $map = [
      self::HUB_WEBFORM_ID => '/form/contact',
    ];
    foreach (self::DIRECT_BY_NEED as $definition) {
      $map[$definition['webform']] = $definition['path'];
    }

    return $map;
  }

  /**
   * Checks whether a webform belongs to the contact family.
   */
  public function isContactFamilyWebform(string $webformId): bool {
    return $webformId === self::HUB_WEBFORM_ID
      || in_array($webformId, $this->getDirectWebformIds(), TRUE);
  }

  /**
   * Returns the translated title of the hub wizard "Need" step.
   */
  public function getHubNeedStepTitle(): string {
    $elementsYaml = $this->configFactory->get('webform.webform.' . self::HUB_WEBFORM_ID)->get('elements');
    if (is_string($elementsYaml) && $elementsYaml !== '') {
      $elements = Yaml::parse($elementsYaml);
      if (is_array($elements)) {
        $title = $elements['step_need']['#title'] ?? NULL;
        if (is_string($title) && $title !== '') {
          return $title;
        }
      }
    }

    return (string) $this->t('Need');
  }

  /**
   * Resolves the page or link title for a contact-family webform.
   *
   * Prefers the webform config title (includes language overrides), then
   * English router fallbacks wrapped for interface translation.
   */
  public function getPageTitle(string $webformId): TranslatableMarkup|string {
    $configTitle = $this->getWebformConfigTitle($webformId);
    if ($configTitle !== NULL) {
      return $configTitle;
    }

    $fallback = $this->getFallbackTitle($webformId);
    if ($fallback !== NULL) {
      return $this->t($fallback);
    }

    return $this->t('Contact us');
  }

  /**
   * Resolves a display title for a direct webform (legacy callers).
   */
  public function getTitleForWebform(string $webformId): ?string {
    return $this->getWebformConfigTitle($webformId) ?? $this->getFallbackTitle($webformId);
  }

  /**
   * Reads the translated webform title from config when the form is open.
   */
  private function getWebformConfigTitle(string $webformId): ?string {
    $config = $this->configFactory->get('webform.webform.' . $webformId);
    if ($config->isNew()) {
      return NULL;
    }

    $status = $config->get('status');
    if ($status !== 'open' && $status !== TRUE) {
      return NULL;
    }

    $title = $config->get('title');
    if (!is_string($title) || $title === '') {
      return NULL;
    }

    return $title;
  }

  /**
   * English fallback title from the hub need map.
   */
  private function getFallbackTitle(string $webformId): ?string {
    foreach (self::DIRECT_BY_NEED as $definition) {
      if ($definition['webform'] === $webformId) {
        return $definition['title'];
      }
    }

    return NULL;
  }

}
