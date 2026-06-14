<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Service\HomepageCtaLinkBuilder;
use Drupal\ps_homepage\Utility\HomepageContent;
use Drupal\ps_homepage\Utility\HomepageLocalizedFieldResolver;
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
    private readonly HomepageCtaLinkBuilder $ctaLinkBuilder,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly ExpertJourneyBlockFormBuilder $formBuilder,
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
      $container->get('ps_homepage.cta_link_builder'),
      $container->get('extension.path.resolver'),
      new ExpertJourneyBlockFormBuilder(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'title_en' => 'Your expert journey',
      'title_fr' => 'Votre parcours expert',
      'subtitle_en' => 'BNP Paribas Real Estate supports you at every step',
      'subtitle_fr' => 'BNP Paribas Real Estate vous accompagne à chaque étape',
      'cta_title_en' => 'Need personalised support?',
      'cta_title_fr' => 'Besoin d\'un accompagnement personnalisé ?',
      'cta_body_en' => 'Our consultants help you secure the right property faster.',
      'cta_body_fr' => 'Nos consultants vous aident à sécuriser le bon local plus rapidement.',
      'cta_button_label_en' => 'Talk to an expert',
      'cta_button_label_fr' => 'Parler à un expert',
      'cta_button_url_en' => '/contact',
      'cta_button_url_fr' => '/contact',
      'cta_link_type' => 'offcanvas',
      'modal_id' => '',
      'image' => NULL,
      'image_alt' => '',
      'steps' => ExpertJourneyBlockFormBuilder::defaultSteps(),
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
    $imageUrl = $this->mediaResolver->resolveUrl($this->configuration['image'] ?? NULL)
      ?? $this->defaultThemeImageUrl();

    $steps = [];
    foreach ($this->configuration['steps'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }
      $label = trim((string) ($item['step_label_' . $langcode] ?? $item['step_label_en'] ?? ''));
      if ($label !== '') {
        $steps[] = $label;
      }
    }

    if ($steps === []) {
      return ['#markup' => ''];
    }

    $cta = $this->ctaLinkBuilder->resolve([
      'link_type' => $this->configuration['cta_link_type'] ?? 'url',
      'modal_id' => $this->configuration['modal_id'] ?? '',
      'button_url_en' => $this->configuration['cta_button_url_en'] ?? '',
      'button_url_fr' => $this->configuration['cta_button_url_fr'] ?? '',
    ], $langcode);

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-expert', 'container', 'py-5']],
      'heading' => [
        '#type' => 'component',
        '#component' => 'ps_theme:section-heading',
        '#props' => $heading,
      ],
      'content' => [
        '#type' => 'component',
        '#component' => 'ps_theme:expert-steps',
        '#props' => [
          'steps' => $steps,
          'image' => $imageUrl ?? '',
          'image_alt' => (string) ($this->configuration['image_alt'] ?? ''),
          'cta_title' => HomepageLocalizedFieldResolver::resolve($this->configuration, 'cta_title', $langcode),
          'cta_body' => HomepageLocalizedFieldResolver::resolve($this->configuration, 'cta_body', $langcode),
          'cta_button_label' => HomepageLocalizedFieldResolver::resolve($this->configuration, 'cta_button_label', $langcode),
          'cta_button_url' => $cta['url'],
          'cta_link_type' => $cta['link_type'],
          'cta_modal_id' => $cta['modal_id'],
        ],
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

  private function defaultThemeImageUrl(): string {
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/assets/images/hero/hero-profile.png')->toString();
  }

}
