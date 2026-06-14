<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage tools & resources section.
 */
final class ToolsBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 8;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [];

    $form += $this->buildLanguageTabs($config, function (string $langcode, array $config): array {
      return [
        'header_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Section header'),
          '#open' => TRUE,
        ] + $this->buildHeadingFields($langcode, $config),
      ];
    });

    $form['media'] = [
      '#type' => 'details',
      '#title' => $this->t('Illustration'),
      '#open' => FALSE,
    ];
    $form['media']['illustration'] = $this->buildManagedFileElement(
      $this->t('Illustration image'),
      $config['illustration'] ?? NULL,
      'public://homepage/tools/',
    );
    $form['media']['illustration_alt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Illustration alt text'),
      '#default_value' => $config['illustration_alt'] ?? '',
      '#maxlength' => 255,
    ];

    $items = $this->sortItemsByWeight($config['items'] ?? []);
    if ($items === []) {
      $items = self::defaultItems();
    }

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Question (EN)'),
        $this->t('Question (FR)'),
        $this->t('Answer (EN)'),
        $this->t('Answer (FR)'),
        $this->t('Link (EN)'),
        $this->t('Link (FR)'),
        $this->t('URL (EN)'),
        $this->t('URL (FR)'),
        $this->t('Open'),
        $this->t('Remove'),
      ],
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'tools-weight',
        ],
      ],
      '#description' => $this->t('Up to @max accordion items. Only one may be opened by default.', ['@max' => self::MAX_ITEMS]),
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $form['items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['tools-weight']],
      ];
      $form['items'][$delta]['question_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['question_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['question_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['question_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['answer_en'] = [
        '#type' => 'text_format',
        '#title_display' => 'invisible',
        '#format' => 'basic_html',
        '#default_value' => $this->textFormatDefault($item['answer_en'] ?? ''),
      ];
      $form['items'][$delta]['answer_fr'] = [
        '#type' => 'text_format',
        '#title_display' => 'invisible',
        '#format' => 'basic_html',
        '#default_value' => $this->textFormatDefault($item['answer_fr'] ?? ''),
      ];
      $form['items'][$delta]['link_label_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['link_label_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_label_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['link_label_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_url_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['link_url_en'] ?? '',
        '#maxlength' => 512,
      ];
      $form['items'][$delta]['link_url_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['link_url_fr'] ?? '',
        '#maxlength' => 512,
      ];
      $form['items'][$delta]['opened_by_default'] = [
        '#type' => 'checkbox',
        '#title_display' => 'invisible',
        '#return_value' => 1,
        '#default_value' => !empty($item['opened_by_default']),
      ];
      $form['items'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title_display' => 'invisible',
        '#return_value' => 1,
      ];
    }

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    foreach (['en', 'fr'] as $langcode) {
      $config['title_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'header_' . $langcode,
        'title_' . $langcode,
      ]));
      $config['subtitle_' . $langcode] = trim((string) $form_state->getValue([
        'lang_' . $langcode,
        'header_' . $langcode,
        'subtitle_' . $langcode,
      ]));
    }

    $config['illustration'] = $this->persistManagedFile($form_state->getValue(['media', 'illustration']));
    $config['illustration_alt'] = trim((string) $form_state->getValue(['media', 'illustration_alt']));

    $rows = $form_state->getValue('items');
    if (!is_array($rows)) {
      $config['items'] = [];
      return;
    }

    $items = [];
    $openedDelta = NULL;
    foreach ($rows as $delta => $row) {
      if (!is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $questionEn = trim((string) ($row['question_en'] ?? ''));
      $questionFr = trim((string) ($row['question_fr'] ?? ''));
      if ($questionEn === '' && $questionFr === '') {
        continue;
      }

      $opened = !empty($row['opened_by_default']);
      if ($opened && $openedDelta === NULL) {
        $openedDelta = $delta;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'question_en' => $questionEn,
        'question_fr' => $questionFr,
        'answer_en' => $this->textFormatValue($row['answer_en'] ?? ''),
        'answer_fr' => $this->textFormatValue($row['answer_fr'] ?? ''),
        'link_label_en' => trim((string) ($row['link_label_en'] ?? '')),
        'link_label_fr' => trim((string) ($row['link_label_fr'] ?? '')),
        'link_url_en' => trim((string) ($row['link_url_en'] ?? '')),
        'link_url_fr' => trim((string) ($row['link_url_fr'] ?? '')),
        'opened_by_default' => $opened && $openedDelta === $delta,
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  /**
   * @return list<array<string, mixed>>
   */
  public static function defaultItems(): array {
    return [
      [
        'weight' => 0,
        'question_en' => 'How do I estimate my office space needs?',
        'question_fr' => 'Comment estimer mes besoins en surface de bureaux ?',
        'answer_en' => '<p>Use our surface calculator to estimate the space required for your teams.</p>',
        'answer_fr' => '<p>Utilisez notre calculateur de surface pour estimer l\'espace nécessaire à vos équipes.</p>',
        'link_label_en' => 'Calculate my surface',
        'link_label_fr' => 'Calculer ma surface',
        'link_url_en' => '/tools/surface-calculator',
        'link_url_fr' => '/outils/calculateur-surface',
        'opened_by_default' => TRUE,
      ],
      [
        'weight' => 1,
        'question_en' => 'Office or coworking: which is right for me?',
        'question_fr' => 'Bureaux ou coworking : que choisir ?',
        'answer_en' => '<p>Take our quick test to find the best workspace model for your business.</p>',
        'answer_fr' => '<p>Faites notre test rapide pour identifier le modèle d\'espace le plus adapté.</p>',
        'link_label_en' => 'Take the test',
        'link_label_fr' => 'Faire le test',
        'link_url_en' => '/tools/workspace-test',
        'link_url_fr' => '/outils/test-espaces',
        'opened_by_default' => FALSE,
      ],
      [
        'weight' => 2,
        'question_en' => 'How do I delegate my property search?',
        'question_fr' => 'Comment déléguer ma recherche immobilière ?',
        'answer_en' => '<p>Our experts can manage your search end-to-end and shortlist the best opportunities.</p>',
        'answer_fr' => '<p>Nos experts peuvent piloter votre recherche et présélectionner les meilleures opportunités.</p>',
        'link_label_en' => 'Contact an expert',
        'link_label_fr' => 'Contacter un expert',
        'link_url_en' => '/contact',
        'link_url_fr' => '/contact',
        'opened_by_default' => FALSE,
      ],
    ];
  }

  private function textFormatDefault(mixed $value): string {
    if (is_array($value)) {
      return (string) ($value['value'] ?? '');
    }
    return (string) $value;
  }

  private function textFormatValue(mixed $value): string {
    if (!is_array($value)) {
      return trim((string) $value);
    }
    return trim((string) ($value['value'] ?? ''));
  }

}
