<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_homepage\Form\HomepageSectionShellFormTrait;

/**
 * Layout Builder block — homepage section footer CTA (S-D shell).
 */
#[Block(
  id: 'ps_homepage_section_footer_block',
  admin_label: new TranslatableMarkup('Homepage section footer'),
  category: new TranslatableMarkup('Property Search'),
)]
final class HomepageSectionFooterBlock extends BlockBase {

  use HomepageSectionShellFormTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'cta_label' => '',
      'cta_url' => '',
      'cta_style' => 'outline',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    return $form + $this->buildSectionFooterForm($this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->submitSectionFooterForm($this->configuration, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $label = trim((string) ($this->configuration['cta_label'] ?? ''));
    $url = trim((string) ($this->configuration['cta_url'] ?? ''));
    if ($label === '' || $url === '') {
      return ['#markup' => ''];
    }

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:homepage-section-footer',
      '#props' => [
        'cta_label' => $label,
        'cta_url' => Url::fromUserInput($url)->toString(),
        'cta_style' => (string) ($this->configuration['cta_style'] ?? 'outline'),
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

}
