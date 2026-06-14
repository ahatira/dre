<?php

declare(strict_types=1);

namespace Drupal\ps_faq\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_faq\Service\FaqAccordionBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * FAQ accordion — domain block (§9 FAQ body).
 */
#[Block(
  id: 'ps_faq_faq_block',
  admin_label: new TranslatableMarkup('FAQ accordion'),
  category: new TranslatableMarkup('Property Search'),
)]
class FaqBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly FaqAccordionBuilder $accordionBuilder,
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
      $container->get('ps_faq.faq_accordion_builder'),
      new FaqBlockFormBuilder(
        $container->get('entity_type.manager'),
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
    return $this->buildAccordionRenderArray();
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildAccordionRenderArray(): array {
    $result = $this->accordionBuilder->build($this->configuration['faq_items'] ?? []);
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
  protected function buildAccordionResult(): array {
    return $this->accordionBuilder->build($this->configuration['faq_items'] ?? []);
  }

}
