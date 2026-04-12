<?php

declare(strict_types=1);

namespace Drupal\ps_favorites\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Header favorites action with dynamic count.
 */
#[Block(
    id: 'ps_favorites_header_block',
    admin_label: new TranslatableMarkup('PS Favorites Header'),
    category: new TranslatableMarkup('Property Search'),
)]
final class FavoritesHeaderBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        private readonly AccountProxyInterface $currentUser,
        private readonly EntityTypeManagerInterface $entityTypeManager,
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition,
    ): self {
        return new self(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('current_user'),
            $container->get('entity_type.manager'),
        );
    }

    public function build(): array
    {
        $isAnonymous = $this->currentUser->isAnonymous();
        $count = $isAnonymous ? 0 : $this->countUserFavorites((int) $this->currentUser->id());

        return [
            '#theme' => 'ps_favorites_header_block',
            '#favorites_url' => Url::fromRoute('ps_favorites.my_favorites')->toString(),
            '#count' => $count,
            '#is_anonymous' => $isAnonymous,
            '#attached' => [
                'library' => ['ps_favorites/favorites'],
            ],
            '#cache' => [
                'contexts' => ['user'],
                'tags' => $isAnonymous ? [] : ['flagging_list'],
            ],
        ];
    }

    private function countUserFavorites(int $uid): int
    {
        $query = $this->entityTypeManager->getStorage('flagging')->getQuery();
        $query->accessCheck(false)
            ->condition('flag_id', 'offer_favorite')
            ->condition('entity_type', 'node')
            ->condition('uid', $uid)
            ->count();

        return (int) $query->execute();
    }
}
