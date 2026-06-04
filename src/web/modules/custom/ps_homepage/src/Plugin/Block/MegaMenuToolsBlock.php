<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Promo/tools column for the Property Search main mega menu.
 */
#[Block(
  id: 'ps_mega_menu_tools',
  admin_label: new TranslatableMarkup('Mega menu — tools (Property Search)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class MegaMenuToolsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme' => 'ps_mega_menu_tools',
      '#cache' => [
        'contexts' => ['languages:language_interface'],
      ],
    ];
  }

}
