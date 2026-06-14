<?php

declare(strict_types=1);

namespace Drupal\ps_content\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_content\Service\OutilsAccordionBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tools & resources accordion — domain block (§3 Outils body).
 */
#[Block(
  id: 'ps_content_outils_accordion_block',
  admin_label: new TranslatableMarkup('Tools & resources accordion'),
  category: new TranslatableMarkup('Property Search'),
)]
class OutilsAccordionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly OutilsAccordionBuilder $accordionBuilder,
    private readonly OutilsAccordionBlockFormBuilder $formBuilder,
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
      $container->get('ps_content.outils_accordion_builder'),
      new OutilsAccordionBlockFormBuilder(),
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
    return $this->buildBodyRenderArray();
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildAccordionResult(): array {
    return $this->accordionBuilder->build($this->configuration);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildBodyRenderArray(): array {
    $result = $this->buildAccordionResult();
    $body = $result['body'];
    if (!empty($result['attached'])) {
      $body['#attached'] = array_merge_recursive($body['#attached'] ?? [], $result['attached']);
    }
    if (!empty($result['cache'])) {
      $body['#cache'] = array_merge($result['cache'], $body['#cache'] ?? []);
    }
    return $body;
  }

}
