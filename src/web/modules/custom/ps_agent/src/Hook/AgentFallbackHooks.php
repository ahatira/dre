<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

final class AgentFallbackHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly RendererInterface $renderer,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  #[Hook('entity_view')]
  public function entityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, string $view_mode): void {
    if ($entity->getEntityTypeId() !== 'ps_agent' || !$entity->hasField('avatar')) {
      return;
    }

    if (!$this->needsAvatarFallback($entity)) {
      return;
    }

    $uri = $this->resolveFallbackUri($entity);
    if ($uri === NULL) {
      return;
    }

    $image_style = $this->resolveAvatarImageStyle($entity, $view_mode, $display);
    [$width, $height] = $this->resolveAvatarDimensions($image_style);

    $build['avatar'] = $this->buildFallbackRenderArray($uri, $entity->label(), $image_style, $width, $height);
    $build['avatar']['#weight'] = 0;
  }

  #[Hook('preprocess_views_view_field')]
  public function preprocessViewsViewField(array &$variables): void {
    if (empty($variables['view']) || empty($variables['field']) || !isset($variables['row'])) {
      return;
    }

    $view = $variables['view'];
    $field = $variables['field'];

    if ($view->id() !== 'ps_agent_admin' || ($field->field ?? NULL) !== 'avatar') {
      return;
    }

    if (trim((string) ($variables['output'] ?? '')) !== '') {
      return;
    }

    $entity = $variables['row']->_entity ?? NULL;
    if (!$entity instanceof EntityInterface || $entity->getEntityTypeId() !== 'ps_agent') {
      return;
    }

    if (!$this->needsAvatarFallback($entity)) {
      $file = $entity->get('avatar')->entity;
      if ($file instanceof EntityInterface && method_exists($file, 'access') && method_exists($file, 'getFileUri') && $file->access('view')) {
        $uri = (string) $file->getFileUri();
        if ($uri !== '') {
          $render = [
            '#theme' => 'image_style',
            '#style_name' => 'agent_avatar_xs',
            '#uri' => $uri,
            '#alt' => $entity->label(),
          ];
          $variables['output'] = Markup::create((string) $this->renderer->render($render));
        }
      }
      return;
    }

    $uri = $this->resolveFallbackUri($entity);
    if ($uri === NULL) {
      return;
    }

    $render = $this->buildFallbackRenderArray($uri, $entity->label(), 'agent_avatar_xs', 40, 40);

    $variables['output'] = Markup::create((string) $this->renderer->render($render));
  }

  /**
   * Determines whether a fallback avatar should be rendered.
   */
  private function needsAvatarFallback(EntityInterface $entity): bool {
    if (!$entity->hasField('avatar')) {
      return FALSE;
    }

    $avatar = $entity->get('avatar');
    if ($avatar->isEmpty()) {
      return TRUE;
    }

    $file = $avatar->entity;
    if ($file === NULL) {
      return TRUE;
    }

    if (!method_exists($file, 'access') || !$file->access('view')) {
      return TRUE;
    }

    if (!method_exists($file, 'getFileUri')) {
      return TRUE;
    }

    return trim((string) $file->getFileUri()) === '';
  }

  /**
   * Resolves the image style configured for the avatar on the active display.
   */
  private function resolveAvatarImageStyle(EntityInterface $entity, string $view_mode, EntityViewDisplayInterface $display): string {
    $component = $display->getComponent('avatar');
    $style = (string) ($component['settings']['image_style'] ?? '');
    if ($style !== '') {
      return $style;
    }

    return match ($view_mode) {
      'card' => 'agent_avatar_md',
      'full' => 'agent_avatar_lg',
      default => 'agent_avatar_md',
    };
  }

  /**
   * Resolves fallback dimensions from a known image style.
   *
   * @return array{0: int, 1: int}
   */
  private function resolveAvatarDimensions(string $image_style): array {
    return match ($image_style) {
      'agent_avatar_xs' => [40, 40],
      'agent_avatar_sm' => [64, 64],
      'agent_avatar_lg' => [128, 128],
      default => [96, 96],
    };
  }

  private function resolveFallbackUri(EntityInterface $entity): ?string {
    $config = $this->configFactory->get('ps_agent.fallback');
    $civility = $entity->hasField('civility') ? (string) ($entity->get('civility')->value ?? '') : NULL;
    $fid = $this->resolveFallbackFid($config, $civility);

    if ($fid > 0) {
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      if ($file instanceof EntityInterface && method_exists($file, 'getFileUri')) {
        $uri = $file->getFileUri();
        if (is_string($uri) && $uri !== '') {
          return $uri;
        }
      }
    }

    $modulePath = $this->moduleExtensionList->getPath('ps_agent');
    $civility = $entity->hasField('civility') ? (string) ($entity->get('civility')->value ?? '') : '';
    $map = [
      'MR' => 'avatar-fallback-mr.svg',
      'MRS' => 'avatar-fallback-mrs.svg',
      'MS' => 'avatar-fallback-ms.svg',
    ];

    $fileName = $map[$civility] ?? 'avatar-fallback-default.svg';
    return '/' . $modulePath . '/images/' . $fileName;
  }

  private function resolveFallbackFid(\Drupal\Core\Config\ImmutableConfig $config, ?string $civility): int {
    $code = (string) ($civility ?? '');

    return match ($code) {
      'MR' => (int) ($config->get('mr_fid') ?? 0),
      'MRS' => (int) ($config->get('mrs_fid') ?? 0),
      'MS' => (int) ($config->get('ms_fid') ?? 0),
      default => (int) ($config->get('default_fid') ?? 0),
    };
  }

  private function buildFallbackRenderArray(string $uri, string $alt, string $imageStyle, int $width, int $height): array {
    if (str_starts_with($uri, 'public://') || str_starts_with($uri, 'private://') || str_starts_with($uri, 'temporary://')) {
      $extension = strtolower((string) pathinfo($uri, PATHINFO_EXTENSION));
      if ($extension === 'svg') {
        return [
          '#type' => 'html_tag',
          '#tag' => 'img',
          '#attributes' => [
            'src' => $this->fileUrlGenerator->generateString($uri),
            'alt' => $alt,
            'width' => $width,
            'height' => $height,
            'loading' => 'lazy',
            'decoding' => 'async',
            'class' => ['ps-agent-card-profile__fallback'],
          ],
        ];
      }

      return [
        '#theme' => 'image_style',
        '#style_name' => $imageStyle,
        '#uri' => $uri,
        '#alt' => $alt,
      ];
    }

    return [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#attributes' => [
        'src' => $uri,
        'alt' => $alt,
        'width' => $width,
        'height' => $height,
        'loading' => 'lazy',
        'decoding' => 'async',
        'class' => ['ps-agent-card-profile__fallback'],
      ],
    ];
  }

}
