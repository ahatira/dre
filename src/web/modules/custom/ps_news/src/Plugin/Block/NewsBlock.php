<?php

declare(strict_types=1);

namespace Drupal\ps_news\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_news\Service\NewsGridBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * News grid — domain block (§7 Actualités body).
 */
#[Block(
  id: 'ps_news_news_block',
  admin_label: new TranslatableMarkup('News grid'),
  category: new TranslatableMarkup('Property Search'),
)]
class NewsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly NewsGridBuilder $gridBuilder,
    private readonly NewsBlockFormBuilder $formBuilder,
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
      $container->get('ps_news.news_grid_builder'),
      new NewsBlockFormBuilder(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'items_count' => 3,
    ] + parent::defaultConfiguration();
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
   * @return array<string, mixed>
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
   * @return array<string, mixed>
   */
  protected function buildGridResult(): array {
    return $this->gridBuilder->build((int) ($this->configuration['items_count'] ?? 3));
  }

}
