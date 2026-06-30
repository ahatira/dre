<?php

declare(strict_types=1);

namespace Drupal\ps_email\Processor;

use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailProcessorInterface;
use Drupal\symfony_mailer\Processor\EmailProcessorTrait;
use Html2Text\Html2Text;

/**
 * Regenerates the plain-text alternative from the final HTML shell.
 *
 * Symfony Mailer's wrap adjuster builds text from a body-only render, which
 * drops the harmonized shell (rich footer, hidden signoff). This processor
 * runs after all HTML mutations so multipart emails stay consistent.
 */
final class EmailPlainTextFromHtmlProcessor implements EmailProcessorInterface {

  use EmailProcessorTrait;

  /**
   * {@inheritdoc}
   */
  public function postRender(EmailInterface $email): void {
    $html = $email->getHtmlBody();
    if ($html === NULL || $html === '') {
      return;
    }

    $email->setTextBody((new Html2Text($html))->getText());
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(int $phase): int {
    return $phase === EmailInterface::PHASE_POST_RENDER ? 900 : EmailInterface::DEFAULT_WEIGHT;
  }

}
