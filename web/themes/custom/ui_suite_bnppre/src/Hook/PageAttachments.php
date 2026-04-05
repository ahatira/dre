<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Page attachments hook implementations.
 */
class PageAttachments
{
    /**
     * Implements hook_page_attachments().
     */
    #[Hook('page_attachments')]
    public function pageAttachments(array &$attachments): void
    {
        // Header library is attached directly by the header component Twig.
    }
}
