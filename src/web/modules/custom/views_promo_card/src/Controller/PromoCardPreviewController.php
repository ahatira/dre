<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views_promo_card\Service\CardRenderer;
use Drupal\views_promo_card\Service\PreviewFrameBuilder;
use Drupal\views_promo_card\Service\PromoCardPatternFormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AJAX preview controller for promo card admin forms.
 */
final class PromoCardPreviewController extends ControllerBase {

  use StringTranslationTrait;

  /**
   * Card renderer service.
   */
  private CardRenderer $cardRenderer;

  /**
   * Pattern form builder service.
   */
  private PromoCardPatternFormBuilder $patternFormBuilder;

  /**
   * Preview iframe document builder.
   */
  private PreviewFrameBuilder $previewFrameBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    /** @var static $instance */
    $instance = parent::create($container);
    $instance->cardRenderer = $container->get('views_promo_card.card_renderer');
    $instance->patternFormBuilder = $container->get('views_promo_card.pattern_form_builder');
    $instance->previewFrameBuilder = $container->get('views_promo_card.preview_frame_builder');
    return $instance;
  }

  /**
   * Renders a promo card preview from submitted form values.
   */
  public function preview(Request $request): Response {
    $layout = $request->request->all('layout');
    $editor = is_array($layout['editor'] ?? NULL) ? $layout['editor'] : [];
    $pattern_id = (string) ($editor['pattern_id'] ?? $request->request->get('pattern_id', ''));
    $pattern_form = is_array($editor['pattern_form'] ?? NULL) ? $editor['pattern_form'] : [];

    if ($pattern_id === '') {
      return new Response('', Response::HTTP_NO_CONTENT);
    }

    $ui_patterns = $this->patternFormBuilder->valuesToUiPatterns($pattern_id, $pattern_form);

    try {
      $build = $this->cardRenderer->buildPreview($pattern_id, $ui_patterns);
      if ($build === NULL) {
        return new Response('', Response::HTTP_NO_CONTENT);
      }

      $frame = [
        '#type' => 'container',
        '#attributes' => ['class' => ['promo-card-preview-frame']],
        'card' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['promo-card-preview-frame__card']],
          'component' => $build,
        ],
      ];

      return $this->previewFrameBuilder->buildDocument($frame, $pattern_id);
    }
    catch (\Throwable) {
      return new Response('', Response::HTTP_NO_CONTENT);
    }
  }

}
