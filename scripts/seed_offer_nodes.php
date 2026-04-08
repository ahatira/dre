<?php

declare(strict_types=1);

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

$themeScreenshot = DRUPAL_ROOT . '/themes/custom/ui_suite_bnppre/screenshot.png';
$fileSystem = \Drupal::service('file_system');

if (!file_exists($themeScreenshot)) {
    throw new RuntimeException('Theme screenshot not found: ' . $themeScreenshot);
}

/**
 * Get or create a managed file from the theme screenshot for demo offers.
 */
$getDemoImageFid = static function (string $machineName, string $label) use ($fileSystem, $themeScreenshot): int {
    $destination = 'public://offer-demo-' . $machineName . '.png';

    $existing = \Drupal::entityTypeManager()
        ->getStorage('file')
        ->loadByProperties(['uri' => $destination]);
    if ($existing) {
        /** @var \Drupal\file\Entity\File $file */
        $file = reset($existing);
        return (int) $file->id();
    }

    $fileSystem->copy($themeScreenshot, $destination, FileSystemInterface::EXISTS_REPLACE);

    $file = File::create([
        'uri' => $destination,
        'filename' => $label . '.png',
        'status' => 1,
    ]);
    $file->setPermanent();
    $file->save();

    return (int) $file->id();
};

$offers = [
    [
        'title' => 'Edificio ARA - Rent Offices MADRID Barrio de Chamberi',
        'reference' => 'OLBUR2200801',
        'price' => '20000.00',
        'price_unit' => 'HT/HC/m2/an',
        'surface' => '611.3 m2',
        'city' => 'Madrid',
        'postal_code' => '28010',
        'availability' => 'Immediately',
        'mandate' => 'exclusive',
        'consultant' => 'Sophia Dacosta',
        'consultant_phone' => '+32 2 646 49 49',
        'description' => '<p>Prime office space in central Madrid with immediate availability and premium common areas.</p>',
        'equipments' => '<ul><li>Elevators</li><li>Reversible air conditioning</li><li>Optical fiber cabling</li><li>High false floor</li></ul>',
        'services' => '<ul><li>Reception services</li><li>Intercom access control</li><li>Meeting rooms</li><li>Cafeteria</li></ul>',
        'building_condition' => '<ul><li>Restructured building with common areas</li><li>Office floors in very good condition</li></ul>',
        'more_information' => '<ul><li>Several courtyards</li><li>Garden-level shared areas</li><li>High flexibility of lot split</li></ul>',
        'energy' => '<p>Energy label pending supplier upload.</p>',
        'certifications' => '<p>LEED Platinum candidate badge available.</p>',
        'surface_table' => '<ul><li>R+5 - RDC - Office - 207 m2 - Contact us</li><li>R+5 - R+5 - Office - 530 m2 - Contact us</li></ul>',
        'location' => '<p>37 CL Hermanos Garcia Noblejas - 28037 Madrid</p>',
        'transport' => '<ul><li>Bus: N° XX-XX-XX</li><li>Metro: M2 Madrid</li><li>Road access</li></ul>',
    ],
    [
        'title' => 'Paris Monceau - Flexible Office Floors',
        'reference' => 'PAR-MONCEAU-002',
        'price' => '18500.00',
        'price_unit' => 'HT/HC/m2/an',
        'surface' => '540 m2',
        'city' => 'Paris',
        'postal_code' => '75008',
        'availability' => 'Q3 2026',
        'mandate' => 'open',
        'consultant' => 'Alex Martin',
        'consultant_phone' => '+33 1 45 00 00 00',
        'description' => '<p>Modern office floors near Parc Monceau, designed for flexible occupation and premium services.</p>',
        'equipments' => '<ul><li>Raised floors</li><li>LED lighting</li><li>Smart access</li><li>High-speed connectivity</li></ul>',
        'services' => '<ul><li>Hostess</li><li>Meeting spaces</li><li>Security service</li><li>Bike parking</li></ul>',
        'building_condition' => '<ul><li>Refurbished facades and lobbies</li><li>Modernized technical equipment</li></ul>',
        'more_information' => '<ul><li>Sub-division possible</li><li>Natural light on all floors</li><li>Terrace access on upper floor</li></ul>',
        'energy' => '<p>Energy optimization plan in progress.</p>',
        'certifications' => '<p>HQE certification in progress.</p>',
        'surface_table' => '<ul><li>Floor 4 - Office - 240 m2 - Available</li><li>Floor 5 - Office - 300 m2 - Available</li></ul>',
        'location' => '<p>44 Boulevard de Courcelles - 75008 Paris</p>',
        'transport' => '<ul><li>Metro: Monceau</li><li>RER: Charles de Gaulle - Etoile</li><li>Direct road access</li></ul>',
    ],
    [
        'title' => 'Edge Case Offer - Minimal Data',
        'reference' => 'EDGE-OFFER-003',
        'price' => '9500.00',
        'price_unit' => 'HT/HC/m2/an',
        'surface' => '320 m2',
        'city' => 'Lyon',
        'postal_code' => '69002',
        'availability' => 'On request',
        'mandate' => 'open',
        'description' => '<p>Minimal content seed used to validate template fallbacks and empty-state rendering behavior.</p>',
        'consultant_action_title' => '',
        'visit_cta_title' => '',
        'surface_table_cta_title' => '',
        'brochure_title' => '',
        'map_filters' => '',
        'map' => '',
        'contact_form' => '',
        'disable_gallery' => true,
        'disable_plan' => true,
        // Intentionally keep most sections empty.
        'location' => '<p>2 Rue de Brest - 69002 Lyon</p>',
    ],
];

$created = 0;
$updated = 0;

foreach ($offers as $index => $data) {
    $reference = $data['reference'];

    $query = \Drupal::entityQuery('node')
        ->accessCheck(false)
        ->condition('type', 'offer')
        ->condition('field_reference', $reference)
        ->range(0, 1);
    $ids = $query->execute();

    $node = $ids ? Node::load((int) reset($ids)) : Node::create([
        'type' => 'offer',
        'langcode' => 'en',
        'uid' => 1,
    ]);

    $imageFid = $getDemoImageFid((string) ($index + 1), (string) $reference);

    $node->setTitle($data['title']);
    $node->set('status', 1);
    $setString = static function (Node $node, string $field, array $source, ?string $key = null): void {
        $lookup = $key ?? str_replace('field_', '', $field);
        if (array_key_exists($lookup, $source) && $source[$lookup] !== null && $source[$lookup] !== '') {
            $node->set($field, (string) $source[$lookup]);
        }
    };
    $setHtml = static function (Node $node, string $field, array $source, ?string $key = null): void {
        $lookup = $key ?? str_replace('field_', '', $field);
        if (array_key_exists($lookup, $source) && $source[$lookup] !== null && $source[$lookup] !== '') {
            $node->set($field, ['value' => (string) $source[$lookup], 'format' => 'basic_html']);
        }
    };
    $setLink = static function (Node $node, string $field, ?string $title, ?string $uri = 'internal:#'): void {
        if ($title !== null && $title !== '') {
            $node->set($field, [
                'uri' => $uri,
                'title' => $title,
            ]);
        }
    };

    $node->set('field_reference', $reference);
    $setString($node, 'field_price', $data, 'price');
    $setString($node, 'field_price_unit', $data, 'price_unit');
    $setString($node, 'field_surface', $data, 'surface');
    $setString($node, 'field_city', $data, 'city');
    $setString($node, 'field_postal_code', $data, 'postal_code');
    $setString($node, 'field_availability', $data, 'availability');
    $setString($node, 'field_mandate', $data, 'mandate');
    $setString($node, 'field_consultant', $data, 'consultant');
    $setString($node, 'field_consultant_phone', $data, 'consultant_phone');

    $setLink($node, 'field_consultant_actions', $data['consultant_action_title'] ?? 'Contact the consultancy');
    $setLink($node, 'field_visit_cta', $data['visit_cta_title'] ?? 'Schedule a visit');
    $setLink($node, 'field_surface_table_cta', $data['surface_table_cta_title'] ?? 'Access to the surface area table');

    if (!empty($data['brochure_title'])) {
        $setLink($node, 'field_brochure', $data['brochure_title'], $data['brochure_uri'] ?? 'https://example.com/demo-brochure.pdf');
    } elseif (!isset($data['brochure_title']) && !isset($data['brochure_uri'])) {
        $setLink($node, 'field_brochure', 'Download the brochure', 'https://example.com/demo-brochure.pdf');
    }

    if (empty($data['disable_gallery'])) {
        $node->set('field_gallery', [
            ['target_id' => $imageFid, 'alt' => $data['title']],
            ['target_id' => $imageFid, 'alt' => $data['title'] . ' (2)'],
            ['target_id' => $imageFid, 'alt' => $data['title'] . ' (3)'],
        ]);
    }

    if (empty($data['disable_plan'])) {
        $node->set('field_plan', [
            'target_id' => $imageFid,
            'alt' => 'Floor plan',
        ]);
    }

    $setHtml($node, 'field_description', $data, 'description');
    $setHtml($node, 'field_equipments', $data, 'equipments');
    $setHtml($node, 'field_services', $data, 'services');
    $setHtml($node, 'field_building_condition', $data, 'building_condition');
    $setHtml($node, 'field_more_information', $data, 'more_information');
    $setHtml($node, 'field_energy', $data, 'energy');
    $setHtml($node, 'field_certifications', $data, 'certifications');
    $setHtml($node, 'field_surface_table', $data, 'surface_table');
    $setHtml($node, 'field_location', $data, 'location');
    $setHtml($node, 'field_transport', $data, 'transport');

    if (!empty($data['map_filters'])) {
        $setHtml($node, 'field_map_filters', $data, 'map_filters');
    } elseif (!array_key_exists('map_filters', $data)) {
        $node->set('field_map_filters', ['value' => '<p>Transport, Parkings, Restaurants, Hotels</p>', 'format' => 'basic_html']);
    }

    if (!empty($data['map'])) {
        $setHtml($node, 'field_map', $data, 'map');
    } elseif (!array_key_exists('map', $data)) {
        $node->set('field_map', ['value' => '<p>Map placeholder for visual QA.</p>', 'format' => 'basic_html']);
    }

    if (!empty($data['contact_form'])) {
        $setHtml($node, 'field_contact_form', $data, 'contact_form');
    } elseif (!array_key_exists('contact_form', $data)) {
        $node->set('field_contact_form', ['value' => '<p>Contact form placeholder. Use modal fallback if no form block is embedded.</p>', 'format' => 'basic_html']);
    }

    $isNew = $node->isNew();
    $node->save();

    if ($isNew) {
        $created++;
        print 'Created offer node #' . $node->id() . ' (' . $reference . ")\n";
    } else {
        $updated++;
        print 'Updated offer node #' . $node->id() . ' (' . $reference . ")\n";
    }
}

print "Seed complete. Created: {$created}. Updated: {$updated}.\n";
