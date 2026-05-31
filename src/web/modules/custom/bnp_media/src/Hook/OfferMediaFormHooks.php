<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hook implementations for offer media UI enhancements.
 */
final class OfferMediaFormHooks {

  /**
   * Implements hook_form_node_offer_form_alter().
   */
  #[Hook('form_node_offer_form_alter')]
  public function alterOfferAddForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    $this->attachMediaLibraries($form);
  }

  /**
   * Implements hook_form_node_offer_edit_form_alter().
   */
  #[Hook('form_node_offer_edit_form_alter')]
  public function alterOfferEditForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    $this->attachMediaLibraries($form);
  }

  /**
   * Attaches BO media widget UI assets.
   */
  private function attachMediaLibraries(array &$form): void {
    $form['#attached']['library'][] = 'bnp_media/upload';
    $form['#attached']['library'][] = 'bnp_media/actions';
    $form['#attached']['library'][] = 'bnp_media/svg_loader';
  }

}
