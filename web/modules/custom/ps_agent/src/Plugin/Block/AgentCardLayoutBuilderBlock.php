<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_agent\Entity\AgentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Renders Agent card component in Layout Builder using current Agent entity.
 */
#[Block(
    id: 'ps_agent_agent_card_layout_builder',
    admin_label: new TranslatableMarkup('Agent card (Layout Builder)'),
    category: new TranslatableMarkup('PS Agent'),
    context_definitions: [
        'entity' => new EntityContextDefinition(
            data_type: 'entity:agent',
            label: new TranslatableMarkup('Agent entity'),
            required: true,
        ),
    ],
)]
final class AgentCardLayoutBuilderBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        private readonly EntityTypeManagerInterface $entityTypeManager,
        private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

  /**
   *
   */
    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition,
    ): self {
        return new self(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('entity_type.manager'),
            $container->get('file_url_generator'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(): array
    {
        $entity = $this->getContextValue('entity');
        if (!$entity instanceof AgentInterface) {
            return [];
        }

        $photo_url = '';
        $photo_alt = '';
        if (!$entity->get('photo')->isEmpty() && $entity->get('photo')->entity !== null) {
            $file = $entity->get('photo')->entity;
            $photo_url = $this->fileUrlGenerator->generateString($file->getFileUri());
            $photo_alt = (string) ($entity->get('photo')->first()?->get('alt')->getString() ?? '');
        }

        $contact_ajax_url = '';
        if (!$entity->get('contact_cta')->isEmpty()) {
            $link_item = $entity->get('contact_cta')->first();
            if ($link_item !== null && $link_item->getUrl() !== null) {
                $contact_ajax_url = $link_item->getUrl()->toString();
            }
        }

        return [
            '#type' => 'component',
            '#component' => 'ui_suite_bnppre:agent_card',
            '#props' => [
                'agent_name' => $entity->getName() ?? '',
                'agent_phone_text' => $entity->getPhone() ?? '',
                'agent_image_url' => $photo_url,
                'agent_image_alt' => $photo_alt,
                'webform_id' => (string) ($entity->get('webform_id')->value ?? ''),
                'contact_ajax_url' => $contact_ajax_url,
                'aria_label' => (string) ($entity->getName() ?? $this->t('Agent details')),
            ],
        ];
    }
}
