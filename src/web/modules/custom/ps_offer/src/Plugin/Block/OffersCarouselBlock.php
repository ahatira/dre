<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_offer\Service\OfferCarouselBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offers carousel — domain block (§4 Annonces body).
 */
#[Block(
  id: 'ps_offer_offers_carousel_block',
  admin_label: new TranslatableMarkup('Offers carousel'),
  category: new TranslatableMarkup('Property Search'),
)]
class OffersCarouselBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly OfferCarouselBuilder $carouselBuilder,
    private readonly OffersCarouselBlockFormBuilder $formBuilder,
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
      $container->get('ps_offer.offer_carousel_builder'),
      new OffersCarouselBlockFormBuilder(
        $container->get('entity_type.manager'),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'max_visible' => 4,
      'show_favorite' => TRUE,
      'show_compare' => TRUE,
      'autoplay' => FALSE,
      'offers' => [],
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
    return $this->buildCarouselRenderArray();
  }

  /**
   * Builds carousel body render array.
   *
   * @return array<string, mixed>
   */
  protected function buildCarouselRenderArray(): array {
    $result = $this->carouselBuilder->build($this->configuration);
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
  protected function buildCarouselResult(): array {
    return $this->carouselBuilder->build($this->configuration);
  }

}
