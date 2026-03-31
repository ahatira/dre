<?php

declare(strict_types=1);

namespace Drupal\ps_header_defaults\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

final class HeaderSearchForm extends FormBase
{
    public function getFormId(): string
    {
        return 'ps_header_defaults_header_search_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state): array
    {
        $form['#attributes']['class'][] = 'ps-header-search-form';
        $form['#method'] = 'get';
        $form['#action'] = Url::fromUri('internal:/search/node')->toString();
        $form['#token'] = false;

        $form['keys'] = [
            '#type' => 'search',
            '#title' => $this->t('Search'),
            '#title_display' => 'invisible',
            '#attributes' => [
                'placeholder' => $this->t('What are you looking for ?'),
            ],
        ];

        $form['actions'] = [
            '#type' => 'actions',
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Search'),
            '#attributes' => [
                'class' => ['btn', 'btn-success'],
            ],
        ];

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state): void
    {
    }
}
