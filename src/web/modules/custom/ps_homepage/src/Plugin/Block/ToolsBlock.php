<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Service\HomepageSectionBuilder;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageMediaResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage tools & resources accordion section (§3).
 */
#[Block(
  id: 'ps_homepage_tools_block',
  admin_label: new TranslatableMarkup('Tools & resources (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class ToolsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly HomepageMediaResolver $mediaResolver,
    private readonly HomepageSectionBuilder $sectionBuilder,
    private readonly ToolsBlockFormBuilder $formBuilder,
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
      $container->get('ps_homepage.media_resolver'),
      $container->get('ps_homepage.section_builder'),
      new ToolsBlockFormBuilder(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'illustration' => NULL,
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
    $heading = HomepageBlockConfiguration::heading($this->configuration);
    $illustration = $this->mediaResolver->resolve($this->configuration['illustration'] ?? NULL, $langcode);
    $illustrationUrl = $illustration->url;

    $accordionItems = [];
    foreach ($this->configuration['items'] ?? [] as $index => $item) {
      if (!is_array($item)) {
        continue;
      }

      $question = trim((string) ($item['question'] ?? ''));
      if ($question === '') {
        continue;
      }

      $answer = trim((string) ($item['answer'] ?? ''));
      $linkLabel = trim((string) ($item['link_label'] ?? ''));
      $linkUrl = trim((string) ($item['link_url'] ?? ''));

      $content = [];
      if ($answer !== '') {
        $content['answer'] = [
          '#markup' => check_markup($answer, 'basic_html'),
        ];
      }
      if ($linkLabel !== '' && $linkUrl !== '') {
        $content['link'] = [
          '#type' => 'link',
          '#title' => $linkLabel,
          '#url' => Url::fromUserInput($linkUrl),
          '#attributes' => ['class' => ['ps-homepage-tools__item-link']],
        ];
      }

      $accordionItems[] = [
        '#type' => 'component',
        '#component' => 'ui_suite_bnp:accordion_item',
        '#slots' => [
          'title' => $question,
          'content' => $content,
        ],
        '#props' => [
          'opened' => !empty($item['opened_by_default']),
          'item_id' => 'ps-homepage-tools-' . $index,
          'heading_level' => 3,
        ],
      ];
    }

    if ($accordionItems === []) {
      return ['#markup' => ''];
    }

    $layoutClasses = ['ps-homepage-tools__layout', 'd-flex', 'flex-column', 'flex-lg-row', 'gap-4'];
    if ($illustrationUrl !== NULL) {
      $layoutClasses[] = 'ps-homepage-tools__layout--with-image';
    }

    $layout = [
      '#type' => 'container',
      '#attributes' => ['class' => $layoutClasses],
      'accordion' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-tools__accordion']],
        'widget' => [
          '#type' => 'component',
          '#component' => 'ui_suite_bnp:accordion',
          '#props' => [
            'keep_open' => FALSE,
            'accordion_id' => Html::getUniqueId('ps-homepage-tools'),
          ],
          '#slots' => [
            'content' => $accordionItems,
          ],
        ],
      ],
    ];

    if ($illustrationUrl !== NULL) {
      $layout['illustration'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-homepage-tools__illustration']],
        'image' => [
          '#type' => 'component',
          '#component' => 'ui_suite_bnp:media-credit',
          '#props' => [
            'image_url' => $illustrationUrl,
            'image_alt' => $illustration->alt,
            'credit' => $illustration->credit,
            'image_class' => 'img-fluid',
            'loading' => 'lazy',
          ],
        ],
      ];
    }

    return $this->sectionBuilder->build([
      'modifier' => 'tools',
      'section_class' => 'ps-homepage-tools',
      'header' => $heading,
      'body' => [
        'layout' => $layout,
      ],
      'attached' => [
        'library' => ['ps_homepage/homepage_tools'],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique(array_merge(['config:block.block'], $illustration->cacheTags))),
      ],
    ]);
  }

}
