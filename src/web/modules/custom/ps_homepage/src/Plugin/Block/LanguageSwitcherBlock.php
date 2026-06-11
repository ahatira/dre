<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Stellar header language switcher.
 */
#[Block(
  id: 'ps_homepage_language_switcher_block',
  admin_label: new TranslatableMarkup('Language switcher (Stellar)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class LanguageSwitcherBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Language Icons module settings.
   */
  private readonly ImmutableConfig $languageiconsConfig;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly LanguageManagerInterface $languageManager,
    private readonly PathMatcherInterface $pathMatcher,
    private readonly RouteMatchInterface $routeMatch,
    ConfigFactoryInterface $configFactory,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->languageiconsConfig = $configFactory->get('languageicons.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('language_manager'),
      $container->get('path.matcher'),
      $container->get('current_route_match'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    $access = $this->languageManager->isMultilingual()
      ? AccessResult::allowed()
      : AccessResult::forbidden();

    return $access->addCacheTags(['config:configurable_language_list']);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $type = LanguageInterface::TYPE_INTERFACE;

    if ($this->pathMatcher->isFrontPage() || !$this->routeMatch->getRouteObject()) {
      $url = Url::fromRoute('<front>');
    }
    else {
      $url = Url::fromRouteMatch($this->routeMatch);
    }

    $switch_links = $this->languageManager->getLanguageSwitchLinks($type, $url);
    if (!$switch_links || empty($switch_links->links)) {
      return [];
    }

    $current_id = $this->languageManager->getCurrentLanguage($type)->getId();
    $current_icon = NULL;
    $current_label = NULL;
    $menu_links = [];
    $build = [];
    $cache_metadata = BubbleableMetadata::createFromRenderArray($build)
      ->addCacheContexts(['url.path', 'url.query_args', 'url.site', 'languages:' . $type])
      ->addCacheTags(['config:languageicons.settings']);

    foreach ($switch_links->links as $langcode => $link) {
      $language = $link['language'] ?? $this->languageManager->getLanguage($langcode);
      $icon = $this->buildFlagIcon($langcode, $language);
      $label = ucfirst($langcode);
      $is_active = $langcode === $current_id;

      if ($is_active) {
        $current_icon = $icon;
        $current_label = $label;
      }

      if ($link['url'] instanceof Url) {
        $cache_metadata->addCacheableDependency($link['url']->access(NULL, TRUE));
      }

      $target_url = NULL;
      if (!$is_active && $link['url'] instanceof Url) {
        $target_url = clone $link['url'];
        $target_url->setOption('language', $language);
        if (!empty($link['query'])) {
          $existing_query = $target_url->getOption('query') ?? [];
          $target_url->setOption('query', $existing_query + $link['query']);
        }
      }

      $menu_links[] = [
        'url' => $target_url?->toString(),
        'langcode' => $langcode,
        'label' => $label,
        'icon' => $icon,
        'active' => $is_active,
      ];
    }

    if ($current_label === NULL) {
      return [];
    }

    $build = [
      '#theme' => 'ps_language_switcher',
      '#current_icon' => $current_icon,
      '#current_label' => $current_label,
      '#current_langcode' => $current_id,
      '#links' => $menu_links,
      '#attached' => [
        'library' => [
          'ps_theme/component_dropdown',
        ],
      ],
    ];
    $cache_metadata->applyTo($build);

    return $build;
  }

  /**
   * Builds a language flag icon render array from languageicons config.
   */
  private function buildFlagIcon(string $langcode, LanguageInterface $language): array {
    $path = $this->languageiconsConfig->get('path');
    if (!$path) {
      return [];
    }

    $size = $this->languageiconsConfig->get('size');
    [$width, $height] = $size ? explode('x', (string) $size) : ['', ''];
    $name = $language->getName();

    return [
      '#theme' => 'image',
      '#uri' => str_replace('*', $langcode, Html::escape($path)),
      '#alt' => $name,
      '#title' => $name,
      '#width' => $width ?: NULL,
      '#height' => $height ?: NULL,
      '#attributes' => ['class' => ['language-icon']],
    ];
  }

}
