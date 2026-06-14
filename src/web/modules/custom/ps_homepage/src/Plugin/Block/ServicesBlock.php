<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_homepage\Service\HomepageCtaLinkBuilder;
use Drupal\ps_homepage\Service\HomepageSectionBuilder;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;
use Drupal\ps_homepage\Utility\HomepageContent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage services grid (§2).
 */
#[Block(
  id: 'ps_homepage_services_block',
  admin_label: new TranslatableMarkup('Services grid (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class ServicesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly HomepageCtaLinkBuilder $ctaLinkBuilder,
    private readonly HomepageSectionBuilder $sectionBuilder,
    private readonly ServicesBlockFormBuilder $formBuilder,
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
      $container->get('ps_homepage.cta_link_builder'),
      $container->get('ps_homepage.section_builder'),
      new ServicesBlockFormBuilder(
        $container->get('ps_search.preset_options_provider'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return parent::defaultConfiguration();
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
    $heading = HomepageBlockConfiguration::heading($this->configuration);
    $items = $this->configuration['items'] ?? [];

    $columns = [];
    foreach ($items as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['card_title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $iconParts = IconIdUtility::resolveParts($item['icon'] ?? '', 'bnp_custom', 'offices');
      $cta = $this->ctaLinkBuilder->resolve($item, $langcode);

      $columns[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['col-12', 'col-md-6', 'col-lg-3']],
        'card' => [
          '#type' => 'component',
          '#component' => 'ps_theme:icon-card',
          '#props' => [
            'icon_pack' => $iconParts['pack'],
            'icon_id' => $iconParts['id'],
            'title' => $title,
            'body' => trim((string) ($item['body'] ?? '')),
            'button_label' => trim((string) ($item['button_label'] ?? '')),
            'button_url' => $cta['url'],
            'button_style' => (string) ($item['button_style'] ?? 'outline'),
            'link_type' => $cta['link_type'],
            'modal_id' => $cta['modal_id'],
          ],
        ],
      ];
    }

    if ($columns === []) {
      return ['#markup' => ''];
    }

    return $this->sectionBuilder->build([
      'modifier' => 'services',
      'header' => $heading,
      'body' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['row', 'g-4', 'ps-homepage-services__grid']],
      ] + $columns,
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ]);
  }

}
