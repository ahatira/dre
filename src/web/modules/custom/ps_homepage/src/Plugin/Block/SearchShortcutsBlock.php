<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageLocalizedFieldResolver;
use Drupal\ps_search\Service\SearchPresetQueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage search shortcuts grid (§5).
 */
#[Block(
  id: 'ps_homepage_search_shortcuts_block',
  admin_label: new TranslatableMarkup('Search shortcuts (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class SearchShortcutsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly SearchPresetQueryBuilder $presetQueryBuilder,
    private readonly SearchShortcutsBlockFormBuilder $formBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_search.preset_query_builder'),
      new SearchShortcutsBlockFormBuilder(
        $container->get('ps_search.preset_options_provider'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'title_en' => 'Start your search',
      'title_fr' => 'Lancez votre recherche',
      'subtitle_en' => 'Quick access to the most popular asset types',
      'subtitle_fr' => 'Accès rapide aux univers les plus recherchés',
      'items' => SearchShortcutsBlockFormBuilder::defaultItems(),
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    return $form + $this->formBuilder->buildForm($this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->formBuilder->submitForm($this->configuration, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $langcode = HomepageContent::langcode();
    $heading = HomepageLocalizedFieldResolver::resolveHeading($this->configuration, $langcode);

    $columns = [];
    foreach ($this->configuration['items'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['title_' . $langcode] ?? $item['title_en'] ?? ''));
      if ($title === '') {
        continue;
      }

      $linkLabel = trim((string) ($item['link_label_' . $langcode] ?? $item['link_label_en'] ?? ''));
      $linkUrl = $this->resolveShortcutUrl($item, $langcode);
      if ($linkLabel === '' || $linkUrl === '') {
        continue;
      }

      $iconParts = IconIdUtility::resolveParts($item['icon'] ?? '', 'bnp_custom', 'offices');
      $columns[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['col-12', 'col-md-6', 'col-lg-3']],
        'card' => [
          '#type' => 'component',
          '#component' => 'ps_theme:search-shortcut-card',
          '#props' => [
            'icon_pack' => $iconParts['pack'],
            'icon_id' => $iconParts['id'],
            'title' => $title,
            'link_label' => $linkLabel,
            'link_url' => $linkUrl,
          ],
        ],
      ];
    }

    if ($columns === []) {
      return ['#markup' => ''];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-shortcuts', 'container', 'py-5']],
      'heading' => [
        '#type' => 'component',
        '#component' => 'ps_theme:section-heading',
        '#props' => $heading,
      ],
      'grid' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['row', 'g-4', 'ps-homepage-shortcuts__grid']],
      ] + $columns,
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

  /**
   * @param array<string, mixed> $item
   */
  private function resolveShortcutUrl(array $item, string $langcode): string {
    $linkType = (string) ($item['link_type'] ?? 'search_preset');
    if ($linkType === 'custom_url') {
      $url = trim((string) ($item['url_' . $langcode] ?? $item['url_en'] ?? ''));
      return $url !== '' ? Url::fromUserInput($url)->toString() : '';
    }

    return $this->presetQueryBuilder->buildUrl(
      isset($item['preset_operation']) ? (string) $item['preset_operation'] : NULL,
      isset($item['preset_asset']) ? (string) $item['preset_asset'] : NULL,
      isset($item['preset_locality']) ? (string) $item['preset_locality'] : NULL,
      $langcode,
    );
  }

}
