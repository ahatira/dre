<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Controller;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\ps_compare\CompareRenderContext;
use Drupal\ps_compare\Service\CompareEmailSender;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Drupal\ps_compare\Service\ComparePageBuilder;
use Drupal\ps_compare\Service\ComparePanelBuilder;
use Drupal\ps_compare\Service\ComparePathResolver;
use Drupal\ps_compare\Service\CompareShareResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Compare page, modal, panel and email endpoints.
 */
final class CompareController extends ControllerBase implements ContainerInjectionInterface {

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly ComparePanelBuilder $panelBuilder,
    private readonly ComparePageBuilder $pageBuilder,
    private readonly ComparePathResolver $comparePathResolver,
    private readonly CompareShareResolver $shareResolver,
    private readonly CompareEmailSender $emailSender,
    private readonly CsrfTokenGenerator $csrfToken,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_compare.manager'),
      $container->get('ps_compare.panel_builder'),
      $container->get('ps_compare.page_builder'),
      $container->get('ps_compare.path_resolver'),
      $container->get('ps_compare.share_resolver'),
      $container->get('ps_compare.email_sender'),
      $container->get('csrf_token'),
      $container->get('renderer'),
    );
  }

  /**
   * Compare page — full page route (direct URL /compare).
   */
  public function page(Request $request): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-compare-route']],
      'content' => $this->buildCompareContent(CompareRenderContext::PAGE, $request),
    ];
  }

  /**
   * Returns comparison table HTML for the search page modal.
   */
  public function modal(): Response {
    $build = $this->buildCompareContent(CompareRenderContext::MODAL);
    return new Response((string) $this->renderer->renderRoot($build));
  }

  /**
   * Sends the comparison table by email.
   */
  public function email(Request $request): JsonResponse {
    $payload = json_decode($request->getContent() ?: '{}', TRUE);
    if (!is_array($payload)) {
      return new JsonResponse(['success' => FALSE, 'message' => $this->t('Invalid request.')], 400);
    }

    $recipient = trim((string) ($payload['email'] ?? ''));
    $message = trim((string) ($payload['message'] ?? ''));
    $token = (string) ($request->headers->get('X-CSRF-Token') ?? '');

    if (!$this->compareManager->canOpenComparisonPage()) {
      return new JsonResponse([
        'success' => FALSE,
        'message' => $this->t('Select at least @min properties to compare.', [
          '@min' => $this->compareManager->getMinItems(),
        ]),
      ], 400);
    }

    if (!$this->emailSender->send($recipient, $token, $message)) {
      return new JsonResponse([
        'success' => FALSE,
        'message' => $this->t('Unable to send the comparison. Check the email address and try again.'),
      ], 400);
    }

    return new JsonResponse([
      'success' => TRUE,
      'message' => $this->t('Comparison sent to @email.', ['@email' => $recipient]),
    ]);
  }

  /**
   * Returns a CSRF token for the email endpoint.
   */
  public function emailToken(): JsonResponse {
    return new JsonResponse([
      'token' => $this->csrfToken->get('ps_compare.email'),
    ]);
  }

  /**
   * Builds compare table or guidance empty state.
   *
   * @return array<string, mixed>
   */
  private function buildCompareContent(string $context = CompareRenderContext::PAGE, ?Request $request = NULL): array {
    $sharedReferences = $request !== NULL
      ? $this->shareResolver->extractReferencesFromRequest($request)
      : [];

    if ($sharedReferences !== []) {
      return $this->buildSharedCompareContent($context, $sharedReferences);
    }

    $count = $this->compareManager->getCompareCount();
    $canCompare = $this->compareManager->canOpenComparisonPage();

    if ($count === 0) {
      return [
        '#theme' => 'ps_compare_empty_state',
        '#title' => $this->t('Compare properties'),
        '#message' => $this->t('You have not selected any properties to compare yet.'),
        '#search_url' => '/find-property',
        '#cache' => [
          'contexts' => ['user'],
          'tags' => ['ps_compare:list', 'ps_compare:count'],
          'max-age' => 0,
        ],
      ];
    }

    if (!$canCompare) {
      return [
        '#theme' => 'ps_compare_empty_state',
        '#title' => $this->t('Compare properties'),
        '#message' => $this->t('Select at least @min properties to compare.', [
          '@min' => $this->compareManager->getMinItems(),
        ]),
        '#search_url' => '/find-property',
        '#cache' => [
          'contexts' => ['user'],
          'tags' => ['ps_compare:list', 'ps_compare:count'],
          'max-age' => 0,
        ],
      ];
    }

    return $this->pageBuilder->buildPage($context);
  }

  /**
   * Builds a comparison from shared offer references in the URL.
   *
   * @param list<string> $references
   *
   * @return array<string, mixed>
   */
  private function buildSharedCompareContent(string $context, array $references): array {
    $offers = $this->shareResolver->loadOffersByReferences($references);
    $minItems = $this->compareManager->getMinItems();

    if (count($offers) < $minItems) {
      return [
        '#theme' => 'ps_compare_empty_state',
        '#title' => $this->t('Compare properties'),
        '#message' => $this->t('This comparison link is invalid or has expired. At least @min valid property references are required.', [
          '@min' => $minItems,
        ]),
        '#search_url' => '/find-property',
        '#cache' => [
          'contexts' => ['url.query_args:refs', 'url.query_args:ref'],
          'max-age' => 0,
        ],
      ];
    }

    $shareUrl = $this->shareResolver->buildUrlFromOffers($offers, TRUE);

    return $this->pageBuilder->buildPage($context, $offers, [
      'shared_view' => TRUE,
      'share_url' => $shareUrl,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function count(): JsonResponse {
    return new JsonResponse([
      'count' => $this->compareManager->getCompareCount(),
      'minItems' => $this->compareManager->getMinItems(),
      'maxItems' => $this->compareManager->getMaxItems(),
      'canCompare' => $this->compareManager->canOpenComparisonPage(),
      'compareUrl' => $this->comparePathResolver->getPublicPath(),
    ]);
  }

  /**
   * Returns panel payload for client-side refresh.
   */
  public function panel(): JsonResponse {
    return new JsonResponse($this->panelBuilder->buildPanelPayload());
  }

  /**
   * Returns rendered panel list HTML for client-side refresh.
   */
  public function panelList(): Response {
    $build = $this->panelBuilder->buildPanelListRenderArray();
    return new Response((string) $this->renderer->renderRoot($build));
  }

  /**
   * {@inheritdoc}
   */
  public function compareState(Request $request): JsonResponse {
    $requestedIds = array_values(array_filter(array_map('intval', $request->query->all('ids'))));
    $comparedIds = $this->compareManager->getCompareIds('node');
    $comparedMap = array_fill_keys($comparedIds, TRUE);

    $states = [];
    if ($requestedIds !== []) {
      foreach ($requestedIds as $entityId) {
        $states[$entityId] = isset($comparedMap[$entityId]);
      }
    }
    else {
      foreach ($comparedIds as $entityId) {
        $states[$entityId] = TRUE;
      }
    }

    return new JsonResponse([
      'count' => count($comparedIds),
      'ids' => $comparedIds,
      'states' => $states,
      'minItems' => $this->compareManager->getMinItems(),
      'maxItems' => $this->compareManager->getMaxItems(),
      'canCompare' => $this->compareManager->canOpenComparisonPage(),
      'compareUrl' => $this->comparePathResolver->getPublicPath(),
    ]);
  }

}
