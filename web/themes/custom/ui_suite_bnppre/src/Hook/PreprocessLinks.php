<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Template\Attribute;
use Drupal\ui_patterns\Plugin\UiPatterns\PropType\LinksPropType;

/**
 * Ensure links structure fits into list group structure.
 */
class PreprocessLinks
{
    /**
     * Implements hook_preprocess_HOOK().
     */
    #[Hook('preprocess_links__contextual')]
    #[Hook('preprocess_links__layout_builder_links')]
    #[Hook('preprocess_links__media_library_menu')]
    public function preprocess(array &$variables): void
    {
        if (empty($variables['links']) || !\is_array($variables['links'])) {
            return;
        }

        $variables['preprocessed_items'] = LinksPropType::normalize(\array_filter(
            $variables['links'],
        ));
    }

    /**
     * Implements hook_preprocess_HOOK() for links--language-block.html.twig.
     */
    #[Hook('preprocess_links__language_block')]
    public function preprocessLanguageBlock(array &$variables): void
    {
        if (empty($variables['links']) || !\is_array($variables['links'])) {
            return;
        }

        $displayMode = $this->getDisplayMode();
        $iconsAvailable = $this->isIconDisplayEnabled();
        $nativeLanguages = $this->getNativeLanguages();

        foreach ($variables['links'] as &$item) {
            if (!\is_array($item) || empty($item['attributes']) || !$item['attributes'] instanceof Attribute) {
                continue;
            }

            $langcode = $this->extractLangcode($item);
            if ($langcode === '') {
                continue;
            }

            $label = $this->buildLabel($langcode, $displayMode, $nativeLanguages);
            $item['text'] = $this->applyLabelToText($item['text'] ?? '', $label, $iconsAvailable);

            if (!empty($item['link']) && \is_array($item['link'])) {
                $item['link']['#title'] = $this->applyLabelToText(
                    $item['link']['#title'] ?? '',
                    $label,
                    $iconsAvailable,
                );

                $item['link']['#options']['attributes']['class'][] = 'dropdown-item';
                $item['link']['#attributes']['class'][] = 'dropdown-item';
            }
        }
        unset($item);
    }

    /**
     * Extract language code from list item attributes.
     */
    protected function extractLangcode(array $item): string
    {
        if (empty($item['attributes']) || !$item['attributes'] instanceof Attribute) {
            return '';
        }

        $attributes = $item['attributes']->toArray();
        if (isset($attributes['data-drupal-language'])) {
            return (string) $attributes['data-drupal-language'];
        }

        return '';
    }

    /**
     * Build label from selected format.
     *
     * @param array<string, \Drupal\Core\Language\LanguageInterface> $nativeLanguages
     *   Native languages keyed by langcode.
     */
    protected function buildLabel(string $langcode, string $displayMode, array $nativeLanguages): string
    {
        return match ($displayMode) {
            'code_upper' => strtoupper($langcode),
            'code_capitalize' => $this->capitalizeLangcode($langcode),
            'name_native' => isset($nativeLanguages[$langcode])
                ? $nativeLanguages[$langcode]->getName()
                : $this->getTranslatedLanguageName($langcode),
            'name_translated' => $this->getTranslatedLanguageName($langcode),
            default => strtolower($langcode),
        };
    }

    /**
     * Keep icon render arrays only when enabled and available.
     *
     * @param mixed $text
     *   Existing text/title variable from links preprocess.
     *
     * @return mixed
     *   Updated text preserving icon markup when possible.
     */
    protected function applyLabelToText(mixed $text, string $label, bool $iconsAvailable): mixed
    {
        if (!$iconsAvailable) {
            return $label;
        }

        if (\is_array($text) && ($text['#theme'] ?? '') === 'languageicons_link_content') {
            $text['#text'] = $label;
            return $text;
        }

        return $label;
    }

    /**
     * Read selected language display mode from active theme settings.
     */
    protected function getDisplayMode(): string
    {
        $displayMode = (string) (theme_get_setting('language_switcher.display_mode') ?? 'code_lower');
        $allowedModes = ['code_lower', 'code_upper', 'code_capitalize', 'name_native', 'name_translated'];
        return in_array($displayMode, $allowedModes, true)
            ? $displayMode
            : 'code_lower';
    }

    /**
     * Capitalize each language code segment while preserving separators.
     */
    protected function capitalizeLangcode(string $langcode): string
    {
        $parts = preg_split('/([-_])/', strtolower($langcode), -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts)) {
            return ucfirst(strtolower($langcode));
        }

        foreach ($parts as $index => $part) {
            if ($part === '-' || $part === '_') {
                continue;
            }
            $parts[$index] = ucfirst($part);
        }

        return implode('', $parts);
    }

    /**
     * Check if language icons can be displayed.
     */
    protected function isIconDisplayEnabled(): bool
    {
        $showIconsSetting = (bool) (theme_get_setting('language_switcher.show_icons') ?? true);
        return $showIconsSetting && \Drupal::moduleHandler()->moduleExists('languageicons');
    }

    /**
     * Return native language list keyed by langcode.
     *
     * @return array<string, \Drupal\Core\Language\LanguageInterface>
     *   Native language objects.
     */
    protected function getNativeLanguages(): array
    {
        return \Drupal::languageManager()->getNativeLanguages();
    }

    /**
     * Return translated language name.
     */
    protected function getTranslatedLanguageName(string $langcode): string
    {
        return \Drupal::languageManager()->getLanguageName($langcode);
    }
}
