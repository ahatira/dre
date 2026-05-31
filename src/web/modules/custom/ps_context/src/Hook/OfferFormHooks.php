<?php

declare(strict_types=1);

namespace Drupal\ps_context\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_context\Service\OfferMatrixRules;

/**
 * Hook implementations for ps_context: offer form matrix rules.
 */
final class OfferFormHooks {

  public function __construct(
    private readonly OfferMatrixRules $matrixRules,
  ) {}

  /**
   * Implements hook_form_node_offer_form_alter() for the offer add form.
   */
  #[Hook('form_node_offer_form_alter')]
  public function alterOfferAddForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    $this->matrixRules->applyFormRules($form, $form_state);
  }

  /**
   * Implements hook_form_node_offer_edit_form_alter() for the offer edit form.
   */
  #[Hook('form_node_offer_edit_form_alter')]
  public function alterOfferEditForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    $this->matrixRules->applyFormRules($form, $form_state);
  }

}
