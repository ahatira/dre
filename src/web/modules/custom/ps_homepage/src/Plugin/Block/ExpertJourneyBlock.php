<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_homepage\Service\HomepageSectionBuilder;
use Drupal\ps_homepage\Utility\ExpertJourneyDefaultAssets;
use Drupal\ps_homepage\Utility\HomepageBlockConfiguration;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageMediaResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Homepage expert journey section (§6).
 */
#[Block(
  id: 'ps_homepage_expert_journey_block',
  admin_label: new TranslatableMarkup('Expert journey (homepage)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class ExpertJourneyBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly HomepageMediaResolver $mediaResolver,
    private readonly ExpertJourneyDefaultAssets $defaultAssets,
    private readonly HomepageSectionBuilder $sectionBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Form builder is stateless; instantiate on demand for LB submit/rebuild paths.
   */
  private function formBuilder(): ExpertJourneyBlockFormBuilder {
    return new ExpertJourneyBlockFormBuilder();
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
      $container->get('ps_homepage.expert_journey_default_assets'),
      $container->get('ps_homepage.section_builder'),
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
  public function setConfiguration(array $configuration) {
    $steps = $configuration['steps'] ?? NULL;
    parent::setConfiguration($configuration);
    if (is_array($steps)) {
      $this->configuration['steps'] = array_values($steps);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['provider'] = [
      '#type' => 'value',
      '#value' => $this->getPluginDefinition()['provider'],
    ];

    $form += $this->formBuilder()->buildForm($this->configuration);
    $form['#attached']['library'][] = 'ps_homepage/homepage_block_form';
    $form['#attached']['library'][] = 'media_library/widget';
    $form['#attached']['library'][] = 'media_library/ui';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    return $this->formBuilder()->buildForm($this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->formBuilder()->submitForm($this->configuration, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $langcode = HomepageContent::langcode();
    $heading = HomepageBlockConfiguration::heading($this->configuration);

    $steps = [];
    $cacheTags = [];
    $index = 0;
    foreach ($this->configuration['steps'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $label = trim((string) ($item['step_label'] ?? ''));
      if ($label === '') {
        continue;
      }

      $media = $this->mediaResolver->resolve($item['image'] ?? NULL, $langcode);
      $imageUrl = $media->url ?? $this->defaultAssets->imageUrl($index);
      $imageAlt = $media->alt !== '' ? $media->alt : $this->defaultAssets->imageAlt($index);
      $imageCredit = $media->credit !== '' ? $media->credit : $this->defaultAssets->imageCredit($index);
      $cacheTags = array_merge($cacheTags, $media->cacheTags);

      $steps[] = [
        'label' => $label,
        'title' => trim((string) ($item['step_title'] ?? '')),
        'body' => trim((string) ($item['step_body'] ?? '')),
        'image' => $imageUrl,
        'image_alt' => $imageAlt,
        'image_credit' => $imageCredit,
      ];
      $index++;
    }

    if ($steps === []) {
      return ['#markup' => ''];
    }

    return $this->sectionBuilder->build([
      'modifier' => 'expert',
      'section_class' => 'ps-homepage-expert',
      'header' => $heading + [
        'align' => 'center',
      ],
      'body' => [
        'content' => [
          '#type' => 'component',
          '#component' => 'ps_theme:expert-steps',
          '#props' => [
            'steps' => $steps,
          ],
        ],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique(array_merge(['config:block.block'], $cacheTags))),
      ],
      'attached' => [
        'library' => ['ps_homepage/homepage_media_credit'],
      ],
    ]);
  }

}
