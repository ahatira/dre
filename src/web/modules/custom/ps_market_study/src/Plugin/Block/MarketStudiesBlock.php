<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_market_study\Service\MarketStudiesGridBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Market studies grid — domain block (§8 Études body).
 */
#[Block(
  id: 'ps_market_study_market_studies_block',
  admin_label: new TranslatableMarkup('Market studies grid'),
  category: new TranslatableMarkup('Property Search'),
)]
class MarketStudiesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly MarketStudiesGridBuilder $gridBuilder,
    private readonly MarketStudiesBlockFormBuilder $formBuilder,
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
      $container->get('ps_market_study.market_studies_grid_builder'),
      new MarketStudiesBlockFormBuilder($container->get('entity_type.manager')),
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
    return $this->buildGridRenderArray();
  }

  /**
   * Builds the body render array with attachments and cache metadata.
   *
   * @return array<string, mixed>
   *   Body render array.
   */
  protected function buildGridRenderArray(): array {
    $result = $this->buildGridResult();
    $body = $result['body'];
    if (!empty($result['attached'])) {
      $body['#attached'] = array_merge_recursive($body['#attached'] ?? [], $result['attached']);
    }
    if (!empty($result['cache'])) {
      $body['#cache'] = array_merge($result['cache'], $body['#cache'] ?? []);
    }
    return $body;
  }

  /**
   * Builds the grid result from block configuration.
   *
   * @return array<string, mixed>
   *   Grid builder result.
   */
  protected function buildGridResult(): array {
    return $this->gridBuilder->build($this->configuration);
  }

}
