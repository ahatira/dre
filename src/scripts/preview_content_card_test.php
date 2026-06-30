<?php

declare(strict_types=1);

/**
 * Content-card preview + render B2B tests.
 *
 * Usage: cd src && vendor/bin/drush @ps.com php:script scripts/preview_content_card_test.php
 */

$pattern_id = 'ps_theme:content-card';
$form_builder = \Drupal::service('views_promo_card.pattern_form_builder');
$renderer = \Drupal::service('views_promo_card.card_renderer');
$frame = \Drupal::service('views_promo_card.preview_frame_builder');

function render_preview_html($pattern_id, $pattern_form, $form_builder, $renderer, $frame): string {
  $ui = $form_builder->valuesToUiPatterns($pattern_id, $pattern_form);
  $build = $renderer->buildPreview($pattern_id, $ui);
  if (!$build) {
    return '';
  }
  return $frame->buildDocument(['#type' => 'container', 'component' => $build], $pattern_id)->getContent();
}

function render_saved_html(string $card_id): string {
  $build = \Drupal::service('views_promo_card.card_renderer')->buildById($card_id);
  if (!$build) {
    return '';
  }
  return (string) \Drupal::service('renderer')->renderPlain($build);
}

function base_form(): array {
  return [
    'content' => [
      'icon' => ['target_id' => 'bnp_custom:entrusting-a-property'],
      'icon_position' => 'center',
      'icon_inline' => 0,
      'title' => 'Test Title',
      'title_tag' => 'h3',
      'subtitle' => '',
      'description' => ['value' => '', 'format' => 'basic_html'],
    ],
    'appearance' => [
      'background' => 'default',
      'text_align' => 'left',
      'icon_size' => 'md',
      'elevation' => 'none',
      'border' => 'none',
      'attributes' => '',
    ],
    'buttons' => [
      'layout' => 'stack',
      'items' => [
        0 => [
          'label' => 'Btn',
          'url' => '#',
          'variant' => 'primary',
          'outline' => 0,
          'mode' => 'page',
          'modal_id' => '',
          'target' => '_self',
        ],
      ],
    ],
  ];
}

function assert_contains(string $html, string $needle, string $label): bool {
  $ok = str_contains($html, $needle);
  echo ($ok ? 'PASS' : 'FAIL') . " $label\n";
  if (!$ok) {
    echo "  expected: $needle\n";
  }
  return $ok;
}

function assert_not_empty(string $html, string $label): bool {
  $ok = $html !== '';
  echo ($ok ? 'PASS' : 'FAIL') . " $label\n";
  return $ok;
}

$tests = [
  'external_url' => static function (array $f): array {
    $f['buttons']['items'][0] = [
      'label' => 'External',
      'url' => 'https://example.com',
      'variant' => 'primary',
      'outline' => 0,
      'mode' => 'page',
      'modal_id' => '',
      'target' => '_blank',
    ];
    return $f;
  },
  'multi_buttons_row' => static function (array $f): array {
    $f['buttons']['layout'] = 'row';
    $f['buttons']['items'] = [
      0 => ['label' => 'A', 'url' => '/contact', 'variant' => 'primary', 'outline' => 0, 'mode' => 'page', 'modal_id' => '', 'target' => '_self'],
      1 => ['label' => 'B', 'url' => '/about-us', 'variant' => 'secondary', 'outline' => 1, 'mode' => 'page', 'modal_id' => '', 'target' => '_self'],
      2 => ['label' => 'C', 'url' => 'https://bnppre.com', 'variant' => 'primary', 'outline' => 1, 'mode' => 'page', 'modal_id' => '', 'target' => '_blank'],
      3 => ['label' => 'D', 'url' => '#', 'variant' => 'secondary', 'outline' => 0, 'mode' => 'page', 'modal_id' => '', 'target' => '_self'],
    ];
    return $f;
  },
  'border_accent' => static function (array $f): array {
    $f['appearance']['border'] = 'accent';
    return $f;
  },
  'background_white_padding' => static function (array $f): array {
    $f['appearance']['background'] = 'white';
    return $f;
  },
];

$expectations = [
  'external_url' => ['href="https://example.com"', 'target="_blank"'],
  'multi_buttons_row' => ['actions--row', 'href="https://bnppre.com"', 'btn-outline-secondary', 'btn-outline-primary'],
  'border_accent' => ['ps-content-card--border-accent'],
  'background_white_padding' => ['ps-content-card--bg-white'],
];

$failures = 0;
foreach ($tests as $name => $modifier) {
  echo "=== preview:$name ===\n";
  $html = render_preview_html($pattern_id, $modifier(base_form()), $form_builder, $renderer, $frame);
  if (!assert_not_empty($html, 'preview html')) {
    $failures++;
    echo "\n";
    continue;
  }
  foreach ($expectations[$name] ?? [] as $exp) {
    if (!assert_contains($html, $exp, $name)) {
      $failures++;
    }
  }
  echo "\n";
}

echo "=== saved:preview_field_test ===\n";
$saved = render_saved_html('preview_field_test');
if ($saved === '') {
  echo "SKIP saved card not found or disabled\n";
}
else {
  assert_contains($saved, 'Multi-button B2B test', 'saved title');
  $count = substr_count($saved, 'ps-content-card__button');
  echo ($count === 4 ? 'PASS' : 'FAIL') . " saved 4 buttons (got $count)\n";
  if ($count !== 4) {
    $failures++;
  }
  assert_contains($saved, 'actions--row', 'saved row layout');
}

echo $failures === 0 ? "ALL TESTS PASSED\n" : "FAILURES: $failures\n";
exit($failures === 0 ? 0 : 1);
