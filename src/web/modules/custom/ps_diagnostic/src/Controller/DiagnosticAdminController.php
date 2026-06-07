<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

final class DiagnosticAdminController extends ControllerBase {

  public function overview(): array {
    return [
      '#type' => 'container',
      'intro' => [
        '#markup' => '<p>' . $this->t('Manage diagnostics from this simple interface.') . '</p>',
      ],
      'links' => [
        '#theme' => 'item_list',
        '#items' => [
          Link::createFromRoute($this->t('Diagnostic settings'), 'ps_diagnostic.settings')->toRenderable(),
          Link::createFromRoute($this->t('Diagnostic types'), 'entity.ps_diagnostic_type.collection')->toRenderable(),
          Link::createFromRoute($this->t('Add diagnostic type'), 'entity.ps_diagnostic_type.add_form')->toRenderable(),
          Link::fromTextAndUrl($this->t('Certification labels'), Url::fromUri('internal:/admin/structure/taxonomy/manage/certification_label/overview'))->toRenderable(),
          Link::createFromRoute($this->t('Offer section headings'), 'ps_offer.section_settings')->toRenderable(),
        ],
      ],
    ];
  }

}
