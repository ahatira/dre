<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Url;
use Drupal\mjml_render_devel\Service\TemplateDiscovery;

/**
 * Builds MJML Devel preview URLs for ps_theme_email templates.
 */
final class MjmlPreviewLinkBuilder {

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Returns a preview URL for a template base name (without .html.twig.mjml).
   *
   * mjml_render_devel expects a base64-encoded relative path, not a short slug.
   */
  public function buildUrl(string $templateName): ?Url {
    if (!$this->moduleHandler->moduleExists('mjml_render_devel')) {
      return NULL;
    }

    /** @var \Drupal\mjml_render_devel\Service\TemplateDiscovery $discovery */
    $discovery = \Drupal::service(TemplateDiscovery::class);
    foreach ($discovery->discoverAll() as $extensions) {
      foreach ($extensions as $extension) {
        foreach ($extension['templates'] as $template) {
          if (($template['name'] ?? '') !== $templateName) {
            continue;
          }

          return Url::fromRoute('mjml_render_devel.preview', [
            'template_path' => base64_encode((string) $template['path']),
          ]);
        }
      }
    }

    return NULL;
  }

}
