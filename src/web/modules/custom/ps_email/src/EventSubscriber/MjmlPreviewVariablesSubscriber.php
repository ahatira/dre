<?php

declare(strict_types=1);

namespace Drupal\ps_email\EventSubscriber;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_email\Service\EmailMjmlPreviewVariablesBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Injects PS email variables into mjml_render_devel AJAX preview requests.
 */
final class MjmlPreviewVariablesSubscriber implements EventSubscriberInterface {

  private const PREVIEW_ROUTE = 'mjml_render_devel.render_ajax';

  private const THEME_TEMPLATE_PREFIX = 'ps_theme_email/templates/';

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EmailMjmlPreviewVariablesBuilder $previewVariablesBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onRequest', 30],
    ];
  }

  /**
   * Merges live email shell variables before mjml_render_devel renders Twig.
   */
  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    if (!$this->moduleHandler->moduleExists('mjml_render_devel')) {
      return;
    }

    $request = $event->getRequest();
    if ($request->attributes->get('_route') !== self::PREVIEW_ROUTE) {
      return;
    }

    $encodedPath = $request->attributes->get('template_path');
    if (!is_string($encodedPath) || $encodedPath === '') {
      return;
    }

    $decodedPath = base64_decode($encodedPath, TRUE);
    if ($decodedPath === FALSE || !str_contains($decodedPath, self::THEME_TEMPLATE_PREFIX)) {
      return;
    }

    $mockData = [];
    $content = $request->getContent();
    if ($content !== '') {
      $parsed = json_decode($content, TRUE);
      if (is_array($parsed)) {
        $mockData = $parsed;
      }
    }

    $langcode = is_string($mockData['_langcode'] ?? NULL)
      ? $mockData['_langcode']
      : $this->languageManager->getCurrentLanguage()->getId();

    $templateName = $this->extractTemplateName($decodedPath);
    $defaults = $this->previewVariablesBuilder->buildForTemplate($templateName, $langcode);
    $merged = array_merge($defaults, $mockData);

    $request->initialize(
      $request->query->all(),
      $request->request->all(),
      $request->attributes->all(),
      $request->cookies->all(),
      $request->files->all(),
      $request->server->all(),
      json_encode($merged, JSON_THROW_ON_ERROR),
    );
  }

  /**
   * Extracts the template base name from a relative theme path.
   */
  private function extractTemplateName(string $relativePath): string {
    $basename = basename($relativePath);

    return (string) preg_replace('/\.html\.twig\.mjml$/', '', $basename);
  }

}
