<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_search\Service\SearchShortcutsBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Search shortcuts grid — domain block (§5 Sélection recherches body).
 */
#[Block(
  id: 'ps_search_search_shortcuts_block',
  admin_label: new TranslatableMarkup('Search shortcuts'),
  category: new TranslatableMarkup('Property Search'),
)]
class SearchShortcutsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly SearchShortcutsBuilder $shortcutsBuilder,
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
      $container->get('ps_search.search_shortcuts_builder'),
      new SearchShortcutsBlockFormBuilder(
        $container->get('ps_search.preset_options_provider'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    $form['#attached']['library'][] = 'ps_homepage/homepage_block_form';
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
    return $this->buildShortcutsRenderArray();
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildShortcutsRenderArray(): array {
    return $this->shortcutsBuilder->build($this->configuration);
  }

}
