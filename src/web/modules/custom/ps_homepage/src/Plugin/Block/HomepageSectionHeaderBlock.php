<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_homepage\Form\HomepageSectionShellFormTrait;

/**
 * Layout Builder block — homepage section header (S-D shell).
 */
#[Block(
  id: 'ps_homepage_section_header_block',
  admin_label: new TranslatableMarkup('Homepage section header'),
  category: new TranslatableMarkup('Property Search'),
)]
final class HomepageSectionHeaderBlock extends BlockBase {

  use HomepageSectionShellFormTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'title' => '',
      'subtitle' => '',
      'align' => 'center',
      'accent' => 'bar',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    return $form + $this->buildSectionHeaderForm($this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->submitSectionHeaderForm($this->configuration, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $title = trim((string) ($this->configuration['title'] ?? ''));
    if ($title === '') {
      return ['#markup' => ''];
    }

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:homepage-section-header',
      '#props' => [
        'title' => $title,
        'subtitle' => trim((string) ($this->configuration['subtitle'] ?? '')),
        'align' => (string) ($this->configuration['align'] ?? 'center'),
        'accent' => (string) ($this->configuration['accent'] ?? 'bar'),
      ],
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

}
