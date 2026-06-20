<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\ps_migrate\Service\ImportPipeline;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Upload form simulating external CRM XML deposit into incoming/.
 */
final class ImportRunUploadForm extends FormBase {

  /**
   * Protected (not readonly) so DependencySerializationTrait can re-inject
   * after managed_file AJAX rebuilds cache the form object.
   */
  protected ImportPipeline $importPipeline;

  public function __construct(ImportPipeline $importPipeline) {
    $this->importPipeline = $importPipeline;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_migrate.import_pipeline'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_migrate_import_run_upload';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $maxBytes = (int) $this->config('ps_migrate.import_pipeline_settings')->get('max_upload_size');

    $form['description'] = [
      '#markup' => '<p>' . $this->t('Simulates an external service depositing CRM offer XML into the configured incoming folder.') . '</p>',
    ];

    $form['xml_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('CRM XML file'),
      '#description' => $this->t('Accepted: .xml only. Max @size MB.', [
        '@size' => round($maxBytes / 1048576, 1),
      ]),
      '#upload_location' => 'temporary://ps_migrate_upload',
      '#upload_validators' => [
        'FileExtension' => ['extensions' => 'xml'],
        'FileSizeLimit' => ['fileLimit' => $maxBytes],
      ],
      '#required' => TRUE,
    ];

    $form['process_now'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Process immediately after upload'),
      '#description' => $this->t('When unchecked, the file waits in incoming/ until Drush or cron runs the pipeline.'),
      '#default_value' => FALSE,
    ];

    $form['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Import mode'),
      '#options' => [
        'full' => $this->t('Full'),
        'delta' => $this->t('Delta'),
      ],
      '#default_value' => $this->config('ps_migrate.import_pipeline_settings')->get('mode'),
      '#states' => [
        'visible' => [
          ':input[name="process_now"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Upload XML'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $fids = $form_state->getValue('xml_file');
    if (empty($fids[0])) {
      $this->messenger()->addError($this->t('No file uploaded.'));
      return;
    }

    $file = File::load((int) $fids[0]);
    if ($file === NULL) {
      $this->messenger()->addError($this->t('Uploaded file could not be loaded.'));
      return;
    }

    $processNow = (bool) $form_state->getValue('process_now');
    $mode = (string) $form_state->getValue('mode');

    try {
      $result = $this->importPipeline->depositUploadedFile(
        $file->getFileUri(),
        $file->getFilename(),
        $processNow,
        $mode,
      );
      $file->delete();
    }
    catch (\Throwable $exception) {
      $this->messenger()->addError($this->t('Upload failed: @message', ['@message' => $exception->getMessage()]));
      return;
    }

    if ($result['processed'] ?? FALSE) {
      $run = $result['run'] ?? [];
      if (($run['status'] ?? '') === 'success') {
        $this->messenger()->addStatus($this->t('Uploaded and imported @file successfully.', ['@file' => $result['filename']]));
      }
      else {
        $this->messenger()->addError($this->t('Uploaded @file but import failed: @error', [
          '@file' => $result['filename'],
          '@error' => $run['error'] ?? $this->t('Unknown error'),
        ]));
      }
    }
    else {
      $this->messenger()->addStatus($this->t('Uploaded @file to incoming/. Run the pipeline to import.', [
        '@file' => $result['filename'],
      ]));
    }

    $form_state->setRedirectUrl(Url::fromRoute('entity.import_run.collection'));
  }

}
