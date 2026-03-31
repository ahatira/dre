<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_header_defaults\Form\HeaderSearchForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Block(
    id: 'ps_header_defaults_header_search',
    admin_label: new TranslatableMarkup('Header search'),
)]
final class HeaderSearchBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    private readonly FormBuilderInterface $formBuilder;

    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        FormBuilderInterface $form_builder,
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->formBuilder = $form_builder;
    }

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
            $container->get('form_builder'),
        );
    }

    public function build(): array
    {
        return $this->formBuilder->getForm(HeaderSearchForm::class);
    }
}
