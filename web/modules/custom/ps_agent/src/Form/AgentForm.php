<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Agent entity form.
 *
 * Standard entity form for creating and editing agent entities with proper
 * field handling and validation.
 *
 * @ingroup ps_agent
 */
final class AgentForm extends ContentEntityForm
{
  /**
   * Constructs an AgentForm object.
   */
    public function __construct(
        EntityRepositoryInterface $entity_repository,
        EntityTypeBundleInfoInterface $entity_type_bundle_info,
        TimeInterface $time,
        protected DateFormatterInterface $dateFormatter,
    ) {
        parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    }

  /**
   * {@inheritdoc}
   */
    public static function create(ContainerInterface $container): static
    {
        return new static(
            $container->get('entity.repository'),
            $container->get('entity_type.bundle.info'),
            $container->get('datetime.time'),
            $container->get('date.formatter'),
        );
    }

  /**
   * {@inheritdoc}
   */
    public function form(array $form, FormStateInterface $formState): array
    {
        $form = parent::form($form, $formState);

        $agent = $this->entity;
        $authorName = $this->currentUser()->getDisplayName();
        if ($agent->hasField('uid') && !$agent->get('uid')->isEmpty() && method_exists($agent, 'getOwner')) {
            $authorName = $agent->getOwner()->getDisplayName();
        }

        $createdLabel = $this->t('Not saved yet');
        if ($agent->hasField('created') && !$agent->get('created')->isEmpty()) {
            $createdLabel = $this->dateFormatter->format((int) $agent->get('created')->value, 'short');
        }

        if (!isset($form['advanced'])) {
            $form['advanced'] = [
                '#type' => 'vertical_tabs',
                '#title' => $this->t('Advanced'),
                '#weight' => 90,
                '#attributes' => ['class' => ['entity-meta']],
            ];
        } else {
            $form['advanced']['#attributes']['class'][] = 'entity-meta';
        }

        // Improve type field display if not creating.
        if (!$agent->isNew() && isset($form['type'])) {
            $form['type']['#disabled'] = true;
        }

        // Populate Gin sidebar with metadata and secondary settings.
        $form['meta'] = [
            '#type' => 'details',
            '#group' => 'advanced',
            '#weight' => -10,
            '#title' => $this->t('Status'),
            '#attributes' => ['class' => ['entity-meta__header']],
            '#tree' => true,
            '#access' => true,
            '#open' => true,
        ];

        $form['meta']['last_saved'] = [
            '#type' => 'item',
            '#title' => $this->t('Last saved'),
            '#markup' => !$agent->isNew()
                ? $this->dateFormatter->format($agent->getChangedTime(), 'short')
                : $this->t('Not saved yet'),
            '#wrapper_attributes' => ['class' => ['entity-meta__last-saved']],
        ];

        // Author in meta section.
        $form['meta']['author'] = [
            '#type' => 'item',
            '#title' => $this->t('Author'),
            '#markup' => $authorName,
            '#wrapper_attributes' => ['class' => ['entity-meta__author']],
        ];

        if (isset($form['revision_log_message'])) {
            $form['revision_log_message']['#group'] = 'meta';
        }

        // Authoring information section.
        $form['author'] = [
            '#type' => 'details',
            '#title' => $this->t('Authoring information'),
            '#description' => $this->t('By @author on @date', [
                '@author' => $authorName,
                '@date' => $createdLabel,
            ]),
            '#group' => 'advanced',
            '#attributes' => ['class' => ['agent-form-author']],
            '#weight' => 90,
            '#open' => false,
        ];

        if (isset($form['uid'])) {
            $form['uid']['#group'] = 'author';
        } else {
            $form['author']['author_name'] = [
                '#type' => 'item',
                '#title' => $this->t('Author'),
                '#markup' => $authorName,
            ];
        }

        if (isset($form['created'])) {
            $form['created']['#group'] = 'author';
        } else {
            $form['author']['created_info'] = [
                '#type' => 'item',
                '#title' => $this->t('Created'),
                '#markup' => $createdLabel,
            ];
        }

        // URL alias section.
        $form['url_alias'] = [
            '#type' => 'details',
            '#title' => $this->t('URL alias'),
            '#description' => isset($form['path'])
                ? $this->t('Configure the alias for this agent.')
                : $this->t('An automatic alias will be generated after the first save.'),
            '#group' => 'advanced',
            '#weight' => 100,
            '#open' => false,
        ];

        if (isset($form['path'])) {
            $form['path']['#group'] = 'url_alias';
        } else {
            $form['url_alias']['info'] = [
                '#type' => 'item',
                '#markup' => $this->t('Pathauto will generate the alias from the agent name.'),
            ];
        }

        // Move type to author section if present.
        if (isset($form['type'])) {
            if (!isset($form['type']['#group'])) {
                $form['type']['#group'] = 'author';
            }
        }

        return $form;
    }

  /**
   * {@inheritdoc}
   */
    public function validate(array $form, FormStateInterface $formState): void
    {
        parent::validate($form, $formState);

        /** @var \Drupal\ps_agent\Entity\AgentInterface $agent */
        $agent = $this->entity;

        // Validate required fields.
        if (!$agent->getFirstName()) {
            $formState->setErrorByName('first_name', $this->t('First Name is required.'));
        }

        if (!$agent->getLastName()) {
            $formState->setErrorByName('last_name', $this->t('Last Name is required.'));
        }
    }

  /**
   * {@inheritdoc}
   */
    public function save(array $form, FormStateInterface $formState): int
    {
        /** @var \Drupal\ps_agent\Entity\AgentInterface $agent */
        $agent = $this->entity;
        $isNew = $agent->isNew();

        $result = $agent->save();

        $message = $isNew
            ? $this->t('Agent %label has been created.', ['%label' => $agent->label()])
            : $this->t('Agent %label has been updated.', ['%label' => $agent->label()]);

        $this->messenger()->addStatus($message);

        $formState->setRedirectUrl($agent->toUrl('collection'));

        return $result;
    }
}
