<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Form;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\ps_feature\Service\FeatureCatalogueCsvImporter;
use Drupal\ps_feature\Service\FeatureCatalogueCsvImporterInterface;
use Drupal\ps_feature\Service\FeatureCatalogueCsvMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Back-office form to import feature catalogue definitions from CSV.
 */
final class FeatureCatalogueImportForm extends FormBase {

  public function __construct(
    private readonly FeatureCatalogueCsvImporterInterface $csvImporter,
    private readonly FileSystemInterface $fileSystem,
    private readonly FeatureCatalogueCsvMapper $mapper,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $mapper = new FeatureCatalogueCsvMapper();
    $csvImporter = $container->has(FeatureCatalogueCsvImporterInterface::class)
      ? $container->get(FeatureCatalogueCsvImporterInterface::class)
      : new FeatureCatalogueCsvImporter(
        $container->get('entity_type.manager'),
        $container->get('language_manager'),
        $mapper,
        $container->get('ps_feature.type_manager'),
      );

    return new self(
      $csvImporter,
      $container->get('file_system'),
      $mapper,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_feature_catalogue_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#attached']['library'][] = 'ps_feature/admin';

    $templateUrl = Url::fromRoute('ps_feature.catalogue_import_template');

    $form['guide'] = [
      '#type' => 'details',
      '#title' => $this->t('Import guide'),
      '#open' => TRUE,
      '#weight' => -30,
      '#attributes' => ['class' => ['ps-feature-catalogue-import__guide']],
    ];
    $form['guide']['intro'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Prepare a business CSV file to create or update feature definitions in the catalogue.'),
    ];
    $form['guide']['workflow'] = [
      '#theme' => 'item_list',
      '#list_type' => 'ol',
      '#title' => $this->t('Steps'),
      '#items' => [
        $this->t('Download the CSV template and share it with the content team.'),
        $this->t('Fill in one row per feature definition.'),
        $this->t('Upload the prepared file below.'),
        $this->t('Run a dry run first to validate the file without saving changes.'),
        $this->t('Import the CSV to apply the catalogue.'),
      ],
    ];
    $form['guide']['download'] = [
      '#type' => 'link',
      '#title' => $this->t('Download CSV template'),
      '#url' => $templateUrl,
      '#attributes' => [
        'class' => ['button', 'button--small', 'ps-feature-catalogue-import__download'],
      ],
    ];

    $form['guide']['columns'] = [
      '#type' => 'details',
      '#title' => $this->t('Column reference'),
      '#open' => FALSE,
    ];
    $form['guide']['columns']['table'] = $this->buildColumnReferenceTable();

    $form['guide']['allowed_values'] = [
      '#type' => 'details',
      '#title' => $this->t('Allowed values'),
      '#open' => FALSE,
    ];
    $form['guide']['allowed_values']['categories'] = [
      '#theme' => 'item_list',
      '#title' => $this->t('Categories'),
      '#items' => $this->mapper->getAllowedCategoryLabels(),
    ];
    $form['guide']['allowed_values']['value_types'] = [
      '#theme' => 'item_list',
      '#title' => $this->t('Value types'),
      '#items' => $this->mapper->getAllowedValueTypeLabels(),
    ];

    $form['import'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Import file'),
      '#weight' => 0,
    ];
    $form['import']['csv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('CSV file'),
      '#description' => $this->t('Accepted format: .csv'),
      '#upload_location' => 'temporary://ps_feature_catalogue_import',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#required' => TRUE,
    ];
    $form['import']['dry_run'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dry run'),
      '#description' => $this->t('Validate the CSV without creating or updating feature definitions.'),
      '#default_value' => FALSE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import CSV'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $fileIds = $form_state->getValue('csv_file');
    $fileId = is_array($fileIds) && isset($fileIds[0]) ? (int) $fileIds[0] : 0;
    if ($fileId <= 0) {
      $form_state->setErrorByName('csv_file', $this->t('Please upload a CSV file.'));
      return;
    }

    $file = File::load($fileId);
    if (!$file instanceof File) {
      $form_state->setErrorByName('csv_file', $this->t('Uploaded file could not be loaded.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $dryRun = (bool) $form_state->getValue('dry_run');

    $path = $this->resolveUploadedPath($form_state);
    if ($path === NULL) {
      $this->messenger()->addError($this->t('Unable to read uploaded CSV file.'));
      return;
    }

    $result = $this->csvImporter->importFromCsv($path, $dryRun);
    foreach ($result['errors'] as $error) {
      $this->messenger()->addWarning($error);
    }

    if ($dryRun) {
      $this->messenger()->addStatus($this->t(
        'Dry run finished: @imported valid rows, @skipped skipped, @errors messages.',
        [
          '@imported' => (string) $result['imported'],
          '@skipped' => (string) $result['skipped'],
          '@errors' => (string) count($result['errors']),
        ],
      ));
    }
    else {
      $this->messenger()->addStatus($this->t(
        'Import finished: @imported imported, @skipped skipped, @errors messages.',
        [
          '@imported' => (string) $result['imported'],
          '@skipped' => (string) $result['skipped'],
          '@errors' => (string) count($result['errors']),
        ],
      ));
    }

    $form_state->setRedirectUrl(Url::fromRoute('entity.fb_feature_definition.collection'));
  }

  /**
   * Resolves uploaded managed file to local path.
   */
  private function resolveUploadedPath(FormStateInterface $form_state): ?string {
    $fileIds = $form_state->getValue('csv_file');
    $fileId = is_array($fileIds) && isset($fileIds[0]) ? (int) $fileIds[0] : 0;
    if ($fileId <= 0) {
      return NULL;
    }

    $file = File::load($fileId);
    if (!$file instanceof File) {
      return NULL;
    }

    $realPath = $this->fileSystem->realpath($file->getFileUri());
    return $realPath !== FALSE ? $realPath : NULL;
  }

  /**
   * Builds the column reference table for the import guide.
   *
   * @return array<string, mixed>
   *   Table render array.
   */
  private function buildColumnReferenceTable(): array {
    $rows = [
      ['code', TRUE, $this->t('CRM element code used as the definition ID.')],
      ['categorie', TRUE, $this->t('Feature group label.')],
      ['libelle', TRUE, $this->t('Default label shown in the back office and front office.')],
      ['type_valeur', TRUE, $this->t('Value type (indicator, yes/no, number, text, date).')],
      ['description', FALSE, $this->t('Optional help text for editors.')],
      ['unite', FALSE, $this->t('Unit of measure, for example m² or kWh.')],
      ['ordre', FALSE, $this->t('Sort weight within the group.')],
      ['filtre_recherche', FALSE, $this->t('Expose as a search filter (Oui/Non).')],
      ['libelle_{langcode}', FALSE, $this->t('Translated label, for example libelle_fr.')],
      ['description_{langcode}', FALSE, $this->t('Translated description, for example description_en.')],
    ];

    $tableRows = [];
    foreach ($rows as $index => [$column, $required, $description]) {
      $tableRows[$index] = [
        'column' => ['#markup' => Markup::create($this->formatCode($column))],
        'requirement' => ['#markup' => $this->requirementBadge($required)],
        'description' => ['#markup' => $description],
      ];
    }

    return [
      '#type' => 'table',
      '#header' => [
        $this->t('Column'),
        $this->t('Requirement'),
        $this->t('Description'),
      ],
      '#attributes' => ['class' => ['ps-feature-catalogue-import__columns-table']],
    ] + $tableRows;
  }

  /**
   * Formats a column name as a code element.
   */
  private function formatCode(string $column): string {
    return '<code>' . htmlspecialchars($column, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code>';
  }

  /**
   * Renders a requirement badge for the column reference table.
   */
  private function requirementBadge(bool $required): Markup {
    $class = $required ? 'badge badge--primary' : 'badge badge--secondary';
    $label = $required ? $this->t('Required') : $this->t('Optional');

    return Markup::create('<span class="' . $class . '">' . $label . '</span>');
  }

}
