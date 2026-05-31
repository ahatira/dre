<?php

declare(strict_types=1);

namespace Drupal\ps_agent;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * List builder for Agent entities.
 */
final class AgentListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['avatar'] = $this->t('Avatar');
    $header['label'] = $this->t('Name');
    $header['civility'] = $this->t('Civility');
    $header['email'] = $this->t('Email');
    $header['phone'] = $this->t('Phone');
    $header['internal_external'] = $this->t('Internal or external');
    $header['job_title'] = $this->t('Job title');
    $header['status'] = $this->t('Published');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\ps_agent\Entity\AgentInterface $entity */
    // Avatar column with rendered image.
    if (!$entity->get('avatar')->isEmpty()) {
      /** @var \Drupal\file\FileInterface $file */
      $file = $entity->get('avatar')->entity;
      if ($file && $file->access('view')) {
        $image_style = \Drupal::service('entity_type.manager')->getStorage('image_style')->load('thumbnail');
        if ($image_style) {
          $row['avatar'] = [
            '#theme' => 'image_style',
            '#style_name' => 'thumbnail',
            '#uri' => $file->getFileUri(),
            '#alt' => $entity->label(),
            '#attributes' => ['class' => ['agent-avatar']],
          ];
        }
        else {
          $row['avatar'] = [
            '#theme' => 'image',
            '#uri' => $file->getFileUri(),
            '#alt' => $entity->label(),
            '#width' => 100,
            '#height' => 100,
          ];
        }
      }
      else {
        $row['avatar'] = '';
      }
    }
    else {
      $row['avatar'] = '';
    }
    
    $row['label'] = $entity->toLink();
    
    // Civility as plain text.
    $civility_value = $entity->get('civility')->value;
    $row['civility'] = $civility_value ? $civility_value : '';
    
    $row['email'] = $entity->get('email')->value;
    $row['phone'] = $entity->get('phone')->value;
    $row['internal_external'] = $entity->get('internal_external')->value ?? '';
    $row['job_title'] = $entity->get('job_title')->value ?? '';
    $row['status'] = $entity->get('status')->value ? $this->t('Yes') : $this->t('No');
    return $row + parent::buildRow($entity);
  }

}
