<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\views_promo_card\Service\PreviewBuilder;
use Drupal\views_promo_card\Service\PreviewFrameBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AJAX preview controller for promo card placement admin forms.
 */
final class PromoCardPlacementPreviewController extends ControllerBase {

  /**
   * Constructs a PromoCardPlacementPreviewController.
   */
  public function __construct(
    private readonly PreviewBuilder $previewBuilder,
    private readonly RendererInterface $renderer,
    private readonly PreviewFrameBuilder $previewFrameBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('views_promo_card.preview_builder'),
      $container->get('renderer'),
      $container->get('views_promo_card.preview_frame_builder'),
    );
  }

  /**
   * Renders a contextual placement preview from submitted form values.
   */
  public function preview(Request $request): Response {
    $layout = $request->request->all('layout');
    $editor = is_array($layout['editor'] ?? NULL) ? $layout['editor'] : [];

    $cards = is_array($editor['cards'] ?? NULL) ? $editor['cards'] : [];
    $card_id = (string) ($cards['card_id'] ?? '');

    $rules = is_array($editor['placement_rules'] ?? NULL) ? $editor['placement_rules'] : [];
    $position = max(1, (int) ($rules['fixed_position'] ?? 3));

    $build = $this->previewBuilder->buildPlacementPreview($card_id, $position);
    if ($build === NULL) {
      return new Response('', Response::HTTP_NO_CONTENT);
    }

    $pattern_id = '';
    if ($card_id !== '') {
      $card = $this->entityTypeManager()->getStorage('promo_card')->load($card_id);
      if ($card !== NULL) {
        $pattern_id = (string) $card->get('pattern_id')->value;
      }
    }

    return $this->previewFrameBuilder->buildDocument($build, $pattern_id);
  }

}
