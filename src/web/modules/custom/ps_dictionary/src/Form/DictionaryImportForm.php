<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\ps_dictionary\Service\DictionaryCsvImporterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Back-office form to import dictionary entries from CSV.
 */
final class DictionaryImportForm extends FormBase {

  public function __construct(
    private readonly DictionaryCsvImporterInterface $csvImporter,
    private readonly FileSystemInterface $fileSystem,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(DictionaryCsvImporterInterface::class),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_dictionary_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['source'] = [
      '#type' => 'radios',
      '#title' => $this->t('CSV source'),
      '#options' => [
        'fixture' => $this->t('Default fixture bundled with module'),
        'upload' => $this->t('Upload a CSV file'),
      ],
      '#default_value' => 'fixture',
      '#required' => TRUE,
    ];

    $form['csv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('CSV file'),
      '#description' => $this->t('Upload a CSV file with columns: type,code,label,weight and optional translations with label_{langcode} (for example label_fr, label_en).'),
      '#upload_location' => 'temporary://ps_dictionary_import',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#states' => [
        'visible' => [
          ':input[name="source"]' => ['value' => 'upload'],
        ],
        'required' => [
          ':input[name="source"]' => ['value' => 'upload'],
        ],
      ],
    ];

    $form['type_filter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Type filter (optional)'),
      '#description' => $this->t('Machine name of the dictionary type to import only this type.'),
      '#maxlength' => 128,
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

    if ($form_state->getValue('source') !== 'upload') {
      return;
    }

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
    $source = (string) $form_state->getValue('source');
    $typeFilter = trim((string) $form_state->getValue('type_filter'));
    $typeFilter = $typeFilter === '' ? NULL : $typeFilter;

    $path = $source === 'fixture' ? $this->defaultFixturePath() : $this->resolveUploadedPath($form_state);
    if ($path === NULL) {
      $this->messenger()->addError($this->t('Unable to read uploaded CSV file.'));
      return;
    }

    $result = $this->csvImporter->importFromCsv($path, $typeFilter);
    foreach ($result['errors'] as $error) {
      $this->messenger()->addWarning($error);
    }

    $this->messenger()->addStatus($this->t(
      'Import finished: @imported imported, @skipped skipped, @errors errors.',
      [
        '@imported' => (string) $result['imported'],
        '@skipped' => (string) $result['skipped'],
        '@errors' => (string) count($result['errors']),
      ],
    ));

    $form_state->setRedirect('ps_dictionary.type_collection');
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
   * Returns the path to the module fixture CSV.
   */
  private function defaultFixturePath(): string {
    return dirname(__DIR__, 2) . '/data/dictionary_entries.csv';
  }

}
