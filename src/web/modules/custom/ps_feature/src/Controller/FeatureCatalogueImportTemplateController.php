<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Serves the bundled feature catalogue CSV template for download.
 */
final class FeatureCatalogueImportTemplateController extends ControllerBase {

  private const TEMPLATE_FILENAME = 'feature_catalogue_import.template.csv';

  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('extension.list.module'),
      $container->get('file_system'),
    );
  }

  /**
   * Downloads the feature catalogue CSV template.
   */
  public function download(): BinaryFileResponse {
    $modulePath = $this->moduleExtensionList->getPath('ps_feature');
    $relativePath = $modulePath . '/data/' . self::TEMPLATE_FILENAME;
    $realPath = $this->fileSystem->realpath($relativePath);
    if ($realPath === FALSE || !is_readable($realPath)) {
      throw new NotFoundHttpException();
    }

    $response = new BinaryFileResponse($realPath);
    $response->setContentDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      self::TEMPLATE_FILENAME,
    );
    $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

    return $response;
  }

}
