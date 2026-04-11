<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountInterface;

/**
 * Preprocess hooks for menu templates.
 */
class PreprocessMenu
{
    /**
     * Implements hook_preprocess_HOOK() for menu--main.html.twig.
     */
    #[Hook('preprocess_menu__main')]
    public function preprocessMain(array &$variables): void
    {
        if (empty($variables['items']) || !is_array($variables['items'])) {
            return;
        }

        $variables['items'] = $this->normalizeMainMenuItems($variables['items'], 1);
    }

    /**
     * Implements hook_preprocess_HOOK() for menu--account.html.twig.
     *
     * Injects authentication state and the account display name so the template
     * can render two distinct states:
     *   - anonymous  → CTA button ("Log in / Sign up")
     *   - authenticated → Bootstrap dropdown (account icon + display name + chevron)
     */
    #[Hook('preprocess_menu__account')]
    public function preprocessAccount(array &$variables): void
    {
        $currentUser = $this->getCurrentUser();

        $variables['is_logged_in'] = !$currentUser->isAnonymous();
        $variables['account_name'] = $currentUser->isAnonymous()
            ? ''
            : $currentUser->getDisplayName();
    }

    /**
     * Centralize service-locator access required by hook class resolver.
     */
    protected function getCurrentUser(): AccountInterface
    {
        return \Drupal::currentUser();
    }

    /**
     * Normalize Drupal menu items into a megamenu-friendly data structure.
     *
     * The right-hand editorial panel is optional and sourced from a regular
     * level-2 menu item marked with the CSS class
     * `ps-megamenu-aside-source` on the link attributes. If no such item
     * exists, the menu still renders normally.
     *
     * Supported optional link attributes on the aside source item:
     * - data-megamenu-lead
     * - data-megamenu-cta-label
     * - data-megamenu-image-src
     * - data-megamenu-image-alt
     * - data-ps-megamenu="aside"
     *
     * @param array<int|string, array<string, mixed>> $items
     *   Raw menu items from template_preprocess_menu().
     * @param int $depth
     *   Current menu depth, 1-based.
     *
     * @return array<int|string, array<string, mixed>>
     *   Decorated menu items.
     */
    protected function normalizeMainMenuItems(array $items, int $depth): array
    {
        foreach ($items as &$item) {
            if (!is_array($item)) {
                continue;
            }

            $item['ps_description'] = $this->getItemDescription($item);
            $item['ps_link_attributes'] = $this->getLinkAttributes($item);

            if (!empty($item['below']) && is_array($item['below'])) {
                $item['below'] = $this->normalizeMainMenuItems($item['below'], $depth + 1);
            }

            if ($depth === 1) {
                $this->prepareTopLevelMegamenuItem($item);
            }
        }
        unset($item);

        return $items;
    }

    /**
     * Build derived megamenu data for a top-level menu item.
     *
     * @param array<string, mixed> $item
     *   A normalized level-1 menu item.
     */
    protected function prepareTopLevelMegamenuItem(array &$item): void
    {
        $columns = [];
        $aside = null;

        if (!empty($item['below']) && is_array($item['below'])) {
            foreach ($item['below'] as $child) {
                if (!is_array($child)) {
                    continue;
                }

                if ($this->isAsideSource($child)) {
                    $aside = $this->buildAside($child);
                    continue;
                }

                $column = $this->buildColumn($child);
                if ($column !== null) {
                    $columns[] = $column;
                }
            }
        }

        $item['ps_megamenu_columns'] = $columns;
        $item['ps_megamenu_aside'] = $aside;
        $item['ps_has_megamenu'] = $columns !== [] || $aside !== null;
    }

    /**
     * Build a single megamenu column from a level-2 item.
     *
     * @param array<string, mixed> $item
     *   A normalized level-2 menu item.
     *
     * @return array<string, mixed>|null
     *   A megamenu column definition, or NULL when the item has no useful
     *   renderable content.
     */
    protected function buildColumn(array $item): ?array
    {
        $links = [];
        $title = $item['title'] ?? '';

        if (!empty($item['below']) && is_array($item['below'])) {
            foreach ($item['below'] as $child) {
                if (!is_array($child)) {
                    continue;
                }

                $link = $this->buildLinkData($child);
                if ($link !== null) {
                    $links[] = $link;
                }
            }
        } elseif (($link = $this->buildLinkData($item)) !== null) {
            $links[] = $link;
            $title = '';
        }

        if ($links === [] && empty($item['title'])) {
            return null;
        }

        return [
            'title' => $title,
            'url' => $item['url'] ?? null,
            'attributes' => $item['ps_link_attributes'] ?? [],
            'description' => $item['ps_description'] ?? '',
            'links' => $links,
        ];
    }

    /**
     * Build a render-safe link payload for a menu item.
     *
     * @param array<string, mixed> $item
     *   A normalized menu item.
     *
     * @return array<string, mixed>|null
     *   A link payload or NULL when the item should not render as a link.
     */
    protected function buildLinkData(array $item): ?array
    {
        if (empty($item['title'])) {
            return null;
        }

        return [
            'title' => $item['title'],
            'url' => $item['url'] ?? null,
            'attributes' => $item['ps_link_attributes'] ?? [],
            'description' => $item['ps_description'] ?? '',
            'in_active_trail' => (bool) ($item['in_active_trail'] ?? false),
        ];
    }

    /**
     * Build the optional right-hand editorial panel.
     *
     * @param array<string, mixed> $item
     *   The aside source level-2 menu item.
     *
     * @return array<string, mixed>
     *   Aside render data.
     */
    protected function buildAside(array $item): array
    {
        $attributes = $item['ps_link_attributes'] ?? [];

        return [
            'title' => $item['title'] ?? '',
            'lead' => $this->getDataAttribute($attributes, 'data-megamenu-lead'),
            'text' => $item['ps_description'] ?? '',
            'url' => $item['url'] ?? null,
            'cta_label' => $this->getDataAttribute($attributes, 'data-megamenu-cta-label') ?: 'Learn more',
            'image_src' => $this->getDataAttribute($attributes, 'data-megamenu-image-src'),
            'image_alt' => $this->getDataAttribute($attributes, 'data-megamenu-image-alt') ?: ($item['title'] ?? ''),
        ];
    }

    /**
     * Determine whether a level-2 item should feed the megamenu aside panel.
     *
     * @param array<string, mixed> $item
     *   A normalized menu item.
     */
    protected function isAsideSource(array $item): bool
    {
        $attributes = $item['ps_link_attributes'] ?? [];

        return $this->hasCssClass($attributes, 'ps-megamenu-aside-source')
            || $this->getDataAttribute($attributes, 'data-ps-megamenu') === 'aside';
    }

    /**
     * Read the description from the original menu link plugin when available.
     *
     * @param array<string, mixed> $item
     *   A Drupal menu item.
     */
    protected function getItemDescription(array $item): string
    {
        $originalLink = $item['original_link'] ?? null;
        if (!is_object($originalLink) || !method_exists($originalLink, 'getDescription')) {
            return '';
        }

        $description = $originalLink->getDescription();
        return is_string($description) ? trim($description) : '';
    }

    /**
     * Extract link HTML attributes from a menu item URL options.
     *
     * @param array<string, mixed> $item
     *   A Drupal menu item.
     *
     * @return array<string, mixed>
     *   The extracted attributes array.
     */
    protected function getLinkAttributes(array $item): array
    {
        $url = $item['url'] ?? null;
        if (!is_object($url) || !method_exists($url, 'getOption')) {
            return [];
        }

        $attributes = $url->getOption('attributes');
        return is_array($attributes) ? $attributes : [];
    }

    /**
     * Check for a class value in extracted attributes.
     *
     * @param array<string, mixed> $attributes
     *   Link attributes.
     */
    protected function hasCssClass(array $attributes, string $class): bool
    {
        $classes = $attributes['class'] ?? [];

        if (is_string($classes)) {
            $classes = preg_split('/\s+/', trim($classes)) ?: [];
        }

        return is_array($classes) && in_array($class, $classes, true);
    }

    /**
     * Read a data attribute value from extracted attributes.
     *
     * @param array<string, mixed> $attributes
     *   Link attributes.
     */
    protected function getDataAttribute(array $attributes, string $name): string
    {
        $value = $attributes[$name] ?? '';
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
