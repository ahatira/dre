<?php

declare(strict_types=1);

namespace Drupal\ps_favorites\Controller;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\flag\FlagServiceInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Favorites page controller.
 */
final class FavoritesController extends ControllerBase
{
    public function __construct(
        private readonly AccountProxyInterface $account,
        private readonly EntityTypeManagerInterface $entityTypeManagerService,
        private readonly FlagServiceInterface $flagService,
        private readonly CsrfTokenGenerator $csrfToken,
    ) {
    }

    public static function create(ContainerInterface $container): self
    {
        return new self(
            $container->get('current_user'),
            $container->get('entity_type.manager'),
            $container->get('flag'),
            $container->get('csrf_token'),
        );
    }

    public function listing(): array
    {
        if ($this->account->isAnonymous()) {
            return [
                '#type' => 'container',
                'message' => [
                    '#markup' => $this->t('Please <a href=":login">log in</a> to access your favorites.', [
                        ':login' => Url::fromRoute('user.login')->toString(),
                    ]),
                ],
            ];
        }

        $flaggingIds = $this->entityTypeManagerService->getStorage('flagging')->getQuery()
            ->accessCheck(false)
            ->condition('flag_id', 'offer_favorite')
            ->condition('entity_type', 'node')
            ->condition('uid', (int) $this->account->id())
            ->sort('created', 'DESC')
            ->execute();

        if ($flaggingIds === []) {
            return [
                '#type' => 'container',
                'message' => [
                    '#markup' => $this->t('You have no favorite offers yet.'),
                ],
            ];
        }

        $flaggings = $this->entityTypeManagerService->getStorage('flagging')->loadMultiple($flaggingIds);
        $offerIds = [];
        foreach ($flaggings as $flagging) {
            $offerIds[] = (int) $flagging->get('entity_id')->value;
        }

        $offers = $this->entityTypeManagerService->getStorage('node')->loadMultiple($offerIds);

        $items = [];
        foreach ($offerIds as $offerId) {
            if (!isset($offers[$offerId])) {
                continue;
            }
            $items[] = $offers[$offerId]->toLink()->toRenderable();
        }

        return [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#items' => $items,
            '#attributes' => ['class' => ['ps-favorites-list']],
            '#cache' => [
                'contexts' => ['user'],
                'tags' => ['flagging_list'],
            ],
        ];
    }

    public function merge(Request $request): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-Token', '');
        if (!$this->csrfToken->validate($token, 'rest')) {
            return new JsonResponse(['error' => 'CSRF validation failed.'], 403);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !isset($data['nids']) || !is_array($data['nids'])) {
            return new JsonResponse(['error' => 'Invalid payload.'], 400);
        }

        $nids = array_values(array_filter(
            array_map('intval', $data['nids']),
            fn(int $n): bool => $n > 0,
        ));
        // Cap merges per request to prevent abuse.
        $nids = array_slice($nids, 0, 100);

        $flag = $this->flagService->getFlagById('offer_favorite');
        if (!$flag) {
            return new JsonResponse(['error' => 'Flag not configured.'], 503);
        }

        $merged = [];
        $skipped = [];
        foreach ($nids as $nid) {
            $node = $this->entityTypeManagerService->getStorage('node')->load($nid);
            if (!$node instanceof NodeInterface || !$node->isPublished() || $node->bundle() !== 'offer') {
                $skipped[] = $nid;
                continue;
            }
            if ($this->flagService->getFlagging($flag, $node, $this->account)) {
                $skipped[] = $nid;
                continue;
            }
            $this->flagService->flag($flag, $node, $this->account);
            $merged[] = $nid;
        }

        return new JsonResponse(['merged' => $merged, 'skipped' => $skipped]);
    }
}
