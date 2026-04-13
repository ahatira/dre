<?php

declare(strict_types=1);

namespace Drupal\ps_block\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Footer contact block.
 */
#[Block(
    id: 'ps_block_footer_contact_block',
    admin_label: new TranslatableMarkup('PS Footer Contact'),
    category: new TranslatableMarkup('Property Search'),
)]
final class FooterContactBlock extends BlockBase
{
    public function defaultConfiguration(): array
    {
        return [
            'city_1_label' => 'City 1 :',
            'city_1_value' => '+32 2 646 49 49',
            'city_1_uri' => 'tel:+3226464949',
            'city_2_label' => 'City 2 :',
            'city_2_value' => '+32 2 646 49 49',
            'city_2_uri' => 'tel:+3226464949',
            'email_value' => 'contact.web@realestate.bnpparibas',
            'email_uri' => 'mailto:contact.web@realestate.bnpparibas',
        ] + parent::defaultConfiguration();
    }

    public function blockForm($form, FormStateInterface $form_state): array
    {
        $form = parent::blockForm($form, $form_state);

        $form['city_1_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City 1 label'),
            '#default_value' => (string) ($this->configuration['city_1_label'] ?? ''),
            '#required' => true,
        ];
        $form['city_1_value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City 1 value'),
            '#default_value' => (string) ($this->configuration['city_1_value'] ?? ''),
            '#required' => true,
        ];
        $form['city_1_uri'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City 1 link URI'),
            '#description' => $this->t('Example: tel:+3226464949'),
            '#default_value' => (string) ($this->configuration['city_1_uri'] ?? ''),
            '#required' => true,
        ];

        $form['city_2_label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City 2 label'),
            '#default_value' => (string) ($this->configuration['city_2_label'] ?? ''),
            '#required' => true,
        ];
        $form['city_2_value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City 2 value'),
            '#default_value' => (string) ($this->configuration['city_2_value'] ?? ''),
            '#required' => true,
        ];
        $form['city_2_uri'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City 2 link URI'),
            '#description' => $this->t('Example: tel:+3226464949'),
            '#default_value' => (string) ($this->configuration['city_2_uri'] ?? ''),
            '#required' => true,
        ];

        $form['email_value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Email text'),
            '#default_value' => (string) ($this->configuration['email_value'] ?? ''),
            '#required' => true,
        ];
        $form['email_uri'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Email link URI'),
            '#description' => $this->t('Example: mailto:contact.web@realestate.bnpparibas'),
            '#default_value' => (string) ($this->configuration['email_uri'] ?? ''),
            '#required' => true,
        ];

        return $form;
    }

    public function blockSubmit($form, FormStateInterface $form_state): void
    {
        $this->configuration['city_1_label'] = trim((string) $form_state->getValue('city_1_label'));
        $this->configuration['city_1_value'] = trim((string) $form_state->getValue('city_1_value'));
        $this->configuration['city_1_uri'] = trim((string) $form_state->getValue('city_1_uri'));
        $this->configuration['city_2_label'] = trim((string) $form_state->getValue('city_2_label'));
        $this->configuration['city_2_value'] = trim((string) $form_state->getValue('city_2_value'));
        $this->configuration['city_2_uri'] = trim((string) $form_state->getValue('city_2_uri'));
        $this->configuration['email_value'] = trim((string) $form_state->getValue('email_value'));
        $this->configuration['email_uri'] = trim((string) $form_state->getValue('email_uri'));
    }

    public function build(): array
    {
        $contacts = [
            [
                'label' => (string) ($this->configuration['city_1_label'] ?? ''),
                'value' => (string) ($this->configuration['city_1_value'] ?? ''),
                'uri' => (string) ($this->configuration['city_1_uri'] ?? ''),
            ],
            [
                'label' => (string) ($this->configuration['city_2_label'] ?? ''),
                'value' => (string) ($this->configuration['city_2_value'] ?? ''),
                'uri' => (string) ($this->configuration['city_2_uri'] ?? ''),
            ],
        ];

        return [
            '#theme' => 'ps_block_footer_contact_block',
            '#contacts' => $contacts,
            '#email' => [
                'value' => (string) ($this->configuration['email_value'] ?? ''),
                'uri' => (string) ($this->configuration['email_uri'] ?? ''),
            ],
            '#cache' => [
                'max-age' => Cache::PERMANENT,
            ],
        ];
    }
}
