<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Hero;

use Drupal\Core\Url;
use Drupal\ps_form\Service\ContactDisplayModeManager;

/**
 * Builds the homepage search hero delegate bar SDC.
 */
final class HeroDelegateBarBuilder {

  public function __construct(
    private readonly ?ContactDisplayModeManager $contactDisplayMode = NULL,
  ) {}

  /**
   * Builds the delegate bar SDC render array.
   *
   * @param array<string, string> $labels
   *   Localized delegate labels from block configuration.
   *
   * @return array<string, mixed>
   *   Render array for search-hero-delegate.
   */
  public function build(array $labels): array {
    $buttonUrl = trim((string) ($labels['delegate_url'] ?? ''));
    if ($buttonUrl === '') {
      $buttonUrl = Url::fromRoute('ps_form.webform_contact')->toString();
    }
    else {
      $buttonUrl = Url::fromUserInput($buttonUrl)->toString();
    }

    $props = [
      'prompt' => (string) ($labels['delegate_prompt'] ?? ''),
      'tooltip' => (string) ($labels['delegate_tooltip'] ?? ''),
      'button_label' => (string) ($labels['delegate_button_label'] ?? ''),
      'button_url' => $buttonUrl,
      'dialog_attributes' => [],
    ];

    if ($this->shouldApplyContactDisplayMode($buttonUrl)) {
      $props['dialog_attributes'] = $this->contactDisplayMode->buildLinkAttributes();
    }

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:search-hero-delegate',
      '#props' => $props,
    ];
  }

  /**
   * Checks whether the delegate button targets a contact form route.
   */
  private function shouldApplyContactDisplayMode(string $buttonUrl): bool {
    if ($this->contactDisplayMode === NULL) {
      return FALSE;
    }

    $path = (string) (parse_url($buttonUrl, PHP_URL_PATH) ?? '');
    return $this->contactDisplayMode->isContactFormPath($path);
  }

}
