<?php

declare(strict_types=1);

namespace Drupal\ps_content\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_content\Service\ExpertsAccompagnementBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Expert journey — domain block (§6 Experts body).
 */
#[Block(
  id: 'ps_content_experts_accompagnement_block',
  admin_label: new TranslatableMarkup('Expert journey'),
  category: new TranslatableMarkup('Property Search'),
)]
class ExpertsAccompagnementBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly ExpertsAccompagnementBuilder $journeyBuilder,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Stateless form builder for LB submit/rebuild paths.
   */
  private function formBuilder(): ExpertsAccompagnementBlockFormBuilder {
    return new ExpertsAccompagnementBlockFormBuilder();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_content.experts_accompagnement_builder'),
    );
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
    return $this;
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
    $form = parent::blockForm($form, $form_state);
    $form['#attached']['library'][] = 'ps_homepage/homepage_block_form';
    $form['#attached']['library'][] = 'media_library/widget';
    $form['#attached']['library'][] = 'media_library/ui';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    return $form + $this->formBuilder()->buildForm($this->configuration);
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
    return $this->buildBodyRenderArray();
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildJourneyResult(): array {
    return $this->journeyBuilder->build($this->configuration);
  }

  /**
   * @return array<string, mixed>
   */
  private function buildBodyRenderArray(): array {
    $result = $this->buildJourneyResult();
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
