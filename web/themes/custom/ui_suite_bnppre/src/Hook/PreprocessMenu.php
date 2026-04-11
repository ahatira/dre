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
}
