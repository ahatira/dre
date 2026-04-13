<?php

declare(strict_types=1);

namespace Drupal\ps_block\Plugin\SocialMediaLinks\Iconset;

use Drupal\Core\Theme\Icon\Plugin\IconPackManagerInterface;
use Drupal\social_media_links\IconsetBase;
use Drupal\social_media_links\IconsetFinderService;
use Drupal\social_media_links\IconsetInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides BNPPRE iconset based on ui_suite_bnppre social-media SVG icons.
 *
 * @Iconset(
 *   id = "bnppre",
 *   name = "BNPPRE Social Media",
 *   publisher = "BNP Paribas Real Estate",
 *   publisherUrl = "https://www.bnpparibas-realestate.com",
 * )
 */
class Bnppre extends IconsetBase implements IconsetInterface
{
    /**
     * Icon pack manager.
     *
     * @var \Drupal\Core\Theme\Icon\Plugin\IconPackManagerInterface
     */
    protected IconPackManagerInterface $iconPackManager;

    /**
     * Constructs the iconset plugin.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        IconsetFinderService $finder,
        IconPackManagerInterface $icon_pack_manager,
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $finder);
        $this->iconPackManager = $icon_pack_manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition,
    ) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('social_media_links.finder'),
            $container->get('plugin.manager.icon_pack'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($iconset_id)
    {
        // Non-empty value keeps the iconset available in style options.
        $this->path = 'theme:ui_suite_bnppre/assets/icons/social-media';
    }

    /**
     * {@inheritdoc}
     */
    public function getStyle()
    {
        return [
            '16' => '16px',
            '20' => '20px',
            '32' => '32px',
            '40' => '40px',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIconElement($platform, $style)
    {
        $icon_name = $this->mapIconName($platform->getIconName());
        $icon_full_id = 'bnppre:' . $icon_name;

        // Return an empty render array for unsupported platforms.
        if (!$this->iconPackManager->getIcon($icon_full_id)) {
            return [];
        }

        return [
            '#type' => 'icon',
            '#pack_id' => 'bnppre',
            '#icon_id' => $icon_name,
            '#settings' => [
                'size' => $style . 'px',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIconPath($icon_name, $style)
    {
        return 'assets/icons/social-media/' . $icon_name . '.svg';
    }

    /**
     * Maps platform icon names to available BNPPRE icon IDs.
     */
    protected function mapIconName(string $icon_name): string
    {
        return match ($icon_name) {
            'x-twitter' => 'twitter',
            'youtubechannel' => 'youtube',
            default => $icon_name,
        };
    }
}
