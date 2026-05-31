<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines a diagnostic type config entity.
 *
 * @ConfigEntityType(
 *   id = "ps_diagnostic_type",
 *   label = @Translation("Diagnostic type"),
 *   handlers = {
 *     "list_builder" = "Drupal\ps_diagnostic\DiagnosticTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ps_diagnostic\Form\DiagnosticTypeForm",
 *       "edit" = "Drupal\ps_diagnostic\Form\DiagnosticTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     }
 *   },
 *   config_prefix = "type",
 *   admin_permission = "administer ps diagnostic types",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "enabled"
 *   },
 *   links = {
 *     "collection" = "/admin/ps/structure/diagnostic/types",
 *     "add-form" = "/admin/ps/structure/diagnostic/types/add",
 *     "edit-form" = "/admin/ps/structure/diagnostic/types/{ps_diagnostic_type}/edit",
 *     "delete-form" = "/admin/ps/structure/diagnostic/types/{ps_diagnostic_type}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "unit",
 *     "icon",
 *     "description",
 *     "enabled",
 *     "weight",
 *     "classes"
 *   }
 * )
 */
final class DiagnosticType extends ConfigEntityBase implements DiagnosticTypeInterface {

  protected string $id;

  protected string $label;

  protected string $unit = '';

  protected string $icon = '';

  protected ?string $description = NULL;

  protected bool $enabled = TRUE;

  protected int $weight = 0;

  /**
   * @var array<int, array{label:string,color:string,range_max:int}>
   */
  protected array $classes = [];

  public function isEnabled(): bool {
    return $this->enabled;
  }

  public function getUnit(): string {
    return trim($this->unit);
  }

  public function getIcon(): string {
    return trim($this->icon);
  }

  public function getClasses(): array {
    $classes = [];
    foreach ($this->classes as $class) {
      $label = trim((string) ($class['label'] ?? ''));
      if ($label === '') {
        continue;
      }
      $color = trim((string) ($class['color'] ?? ''));
      $classes[] = [
        'label' => $label,
        'color' => $color,
        'range_max' => (int) ($class['range_max'] ?? 0),
      ];
    }
    return $classes;
  }

}
