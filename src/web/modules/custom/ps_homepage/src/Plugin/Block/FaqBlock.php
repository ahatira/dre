<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Service\HomepageSectionBuilder;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;
use Drupal\ps_homepage\Utility\HomepageContent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage FAQ accordion section (§9).
 */
#[Block(
  id: 'ps_homepage_faq_block',
  admin_label: new TranslatableMarkup('FAQ (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class FaqBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly HomepageSectionBuilder $sectionBuilder,
    private readonly FaqBlockFormBuilder $formBuilder,
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
      $container->get('entity_type.manager'),
      $container->get('ps_homepage.section_builder'),
      new FaqBlockFormBuilder(
        $container->get('entity_type.manager'),
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
    $title = HomepageBlockConfiguration::string($this->configuration, 'title');
    $footer = HomepageBlockConfiguration::footerCta($this->configuration);

    $nids = [];
    foreach ($this->configuration['faq_items'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $nid = (int) ($item['nid'] ?? 0);
      if ($nid > 0) {
        $nids[] = $nid;
      }
    }

    if ($nids === []) {
      return ['#markup' => ''];
    }

    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    $accordionItems = [];
    foreach ($nids as $nid) {
      $node = $nodes[$nid] ?? NULL;
      if (!$node instanceof NodeInterface || !$node->isPublished()) {
        continue;
      }

      if ($node->hasTranslation($langcode)) {
        $node = $node->getTranslation($langcode);
      }

      $question = trim((string) $node->get('field_question')->value);
      if ($question === '') {
        continue;
      }

      $answerField = $node->get('field_answer');
      $answerHtml = $answerField->isEmpty() ? '' : (string) $answerField->processed;

      $accordionItems[] = [
        '#type' => 'component',
        '#component' => 'ui_suite_bnp:accordion_item',
        '#slots' => [
          'title' => $question,
          'content' => [
            '#markup' => $answerHtml,
          ],
        ],
        '#props' => [
          'opened' => FALSE,
          'item_id' => 'ps-homepage-faq-' . $nid,
          'heading_level' => 3,
        ],
      ];
    }

    if ($accordionItems === []) {
      return ['#markup' => ''];
    }

    $footerUrl = $footer['url'] !== '' ? Url::fromUserInput($footer['url'])->toString() : '';

    return $this->sectionBuilder->build([
      'modifier' => 'faq',
      'section_class' => 'ps-homepage-faq',
      'header' => [
        'title' => $title,
        'subtitle' => '',
        'align' => 'center',
      ],
      'body' => [
        'accordion_wrapper' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-faq__accordion']],
          'accordion' => [
            '#type' => 'component',
            '#component' => 'ui_suite_bnp:accordion',
            '#props' => [
              'keep_open' => FALSE,
              'accordion_id' => Html::getUniqueId('ps-homepage-faq'),
            ],
            '#slots' => [
              'content' => $accordionItems,
            ],
          ],
        ],
      ],
      'footer' => [
        'label' => $footer['label'],
        'url' => $footerUrl,
      ],
      'attached' => [
        'library' => ['ps_homepage/homepage_faq'],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_merge(
          ['config:block.block'],
          array_map(static fn (int $nid): string => 'node:' . $nid, $nids),
        ),
      ],
    ]);
  }

}
