<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Drupal\ps_migrate\Service\ImportPipelineRollbackService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Confirms rollback of a CRM import run.
 */
final class ImportRunRollbackForm extends ConfirmFormBase {

  private ?ImportRunInterface $importRun = NULL;

  public function __construct(
    private readonly ImportPipelineRollbackService $rollbackService,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_migrate.import_pipeline_rollback'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_migrate_import_run_rollback';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?ImportRunInterface $import_run = NULL): array {
    $this->importRun = $import_run;
    if ($import_run === NULL) {
      throw new \InvalidArgumentException('Missing import run parameter.');
    }

    if (!$this->rollbackService->canRollback($import_run)) {
      $this->messenger()->addWarning($this->t('This import run cannot be rolled back.'));
      $form['blocked'] = [
        '#markup' => '<p>' . $this->t('Rollback is unavailable. The run may have failed, already been rolled back, has no snapshot, or a newer successful run exists.') . '</p>',
      ];
      $form['actions'] = ['#type' => 'actions'];
      $form['actions']['cancel'] = [
        '#type' => 'link',
        '#title' => $this->t('Back to run detail'),
        '#url' => $import_run->toUrl(),
        '#attributes' => ['class' => ['button']],
      ];
      return $form;
    }

    $snapshot = $import_run->getSnapshot();
    $created = count($snapshot['offers']['created'] ?? []);
    $updated = count($snapshot['offers']['updated'] ?? []);

    $form['summary'] = [
      '#theme' => 'item_list',
      '#title' => $this->t('Planned rollback actions'),
      '#items' => [
        $this->t('Unpublish @count offer(s) created during this run.', ['@count' => $created]),
        $this->t('Restore previous revision for @count updated offer(s).', ['@count' => $updated]),
        $this->t('Reverse feature group/definition status changes when recorded.'),
        $this->t('Files and media are not rolled back (best effort).'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    return $this->t('Roll back import run %filename?', [
      '%filename' => $this->importRun?->getFilename() ?? '',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return $this->importRun?->toUrl() ?? Url::fromRoute('entity.import_run.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText(): TranslatableMarkup {
    return $this->t('Roll back run');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    if ($this->importRun === NULL) {
      return;
    }

    $summary = $this->rollbackService->rollback($this->importRun);
    if (($summary['status'] ?? '') === 'success') {
      $this->messenger()->addStatus($this->t('Import run rolled back successfully.'));
    }
    elseif (($summary['status'] ?? '') === 'partial') {
      $this->messenger()->addWarning($this->t('Import run rolled back with warnings.'));
    }
    else {
      $this->messenger()->addError($this->t('Import run rollback failed.'));
    }

    foreach ($summary['warnings'] ?? [] as $warning) {
      $this->messenger()->addWarning($warning);
    }
    foreach ($summary['errors'] ?? [] as $error) {
      $this->messenger()->addError($error);
    }

    $form_state->setRedirectUrl($this->importRun->toUrl());
  }

}
