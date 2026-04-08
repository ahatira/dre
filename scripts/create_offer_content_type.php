<?php

declare(strict_types=1);

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\NodeType;

$bundle = 'offer';

$nodeType = NodeType::load($bundle);
if (!$nodeType) {
    $nodeType = NodeType::create([
        'type' => $bundle,
        'name' => 'Offer',
        'description' => 'Commercial real estate offer detail page.',
        'new_revision' => true,
        'preview_mode' => 1,
        'display_submitted' => false,
    ]);
    $nodeType->save();
    print "Created content type: offer\n";
} else {
    print "Content type already exists: offer\n";
}

$fields = [
    'field_reference' => [
        'label' => 'Reference',
        'type' => 'string',
        'required' => true,
        'storage_settings' => ['max_length' => 128],
        'field_settings' => ['max_length' => 128],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_price' => [
        'label' => 'Price',
        'type' => 'decimal',
        'required' => true,
        'storage_settings' => ['precision' => 14, 'scale' => 2],
        'field_settings' => ['prefix' => '', 'suffix' => ''],
        'widget' => 'number',
        'formatter' => 'number_decimal',
    ],
    'field_price_unit' => [
        'label' => 'Price unit',
        'type' => 'string',
        'storage_settings' => ['max_length' => 64],
        'field_settings' => ['max_length' => 64],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_surface' => [
        'label' => 'Surface',
        'type' => 'string',
        'storage_settings' => ['max_length' => 64],
        'field_settings' => ['max_length' => 64],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_city' => [
        'label' => 'City',
        'type' => 'string',
        'storage_settings' => ['max_length' => 128],
        'field_settings' => ['max_length' => 128],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_postal_code' => [
        'label' => 'Postal code',
        'type' => 'string',
        'storage_settings' => ['max_length' => 32],
        'field_settings' => ['max_length' => 32],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_availability' => [
        'label' => 'Availability',
        'type' => 'string',
        'storage_settings' => ['max_length' => 128],
        'field_settings' => ['max_length' => 128],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_mandate' => [
        'label' => 'Mandate type',
        'type' => 'list_string',
        'storage_settings' => [
            'allowed_values' => [
                'exclusive' => 'Exclusive',
                'co-exclusive' => 'Co-exclusive',
                'open' => 'Open',
            ],
        ],
        'field_settings' => [],
        'widget' => 'options_select',
        'formatter' => 'list_default',
    ],
    'field_gallery' => [
        'label' => 'Gallery images',
        'type' => 'image',
        'cardinality' => -1,
        'storage_settings' => [
            'uri_scheme' => 'public',
            'default_image' => ['uuid' => null, 'alt' => '', 'title' => '', 'width' => null, 'height' => null],
        ],
        'field_settings' => [
            'alt_field' => true,
            'alt_field_required' => false,
            'title_field' => false,
            'title_field_required' => false,
            'file_extensions' => 'png jpg jpeg webp',
            'max_filesize' => '10 MB',
            'max_resolution' => '',
            'min_resolution' => '',
            'default_image' => ['uuid' => null, 'alt' => '', 'title' => '', 'width' => null, 'height' => null],
        ],
        'widget' => 'image_image',
        'formatter' => 'image',
    ],
    'field_plan' => [
        'label' => 'Plan image',
        'type' => 'image',
        'storage_settings' => [
            'uri_scheme' => 'public',
            'default_image' => ['uuid' => null, 'alt' => '', 'title' => '', 'width' => null, 'height' => null],
        ],
        'field_settings' => [
            'alt_field' => true,
            'alt_field_required' => false,
            'title_field' => false,
            'title_field_required' => false,
            'file_extensions' => 'png jpg jpeg webp',
            'max_filesize' => '10 MB',
            'max_resolution' => '',
            'min_resolution' => '',
            'default_image' => ['uuid' => null, 'alt' => '', 'title' => '', 'width' => null, 'height' => null],
        ],
        'widget' => 'image_image',
        'formatter' => 'image',
    ],
    'field_consultant' => [
        'label' => 'Consultant name',
        'type' => 'string',
        'storage_settings' => ['max_length' => 255],
        'field_settings' => ['max_length' => 255],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_consultant_phone' => [
        'label' => 'Consultant phone',
        'type' => 'string',
        'storage_settings' => ['max_length' => 64],
        'field_settings' => ['max_length' => 64],
        'widget' => 'string_textfield',
        'formatter' => 'string',
    ],
    'field_consultant_actions' => [
        'label' => 'Consultant action link',
        'type' => 'link',
        'storage_settings' => [],
        'field_settings' => ['title' => DRUPAL_OPTIONAL],
        'widget' => 'link_default',
        'formatter' => 'link',
    ],
    'field_visit_cta' => [
        'label' => 'Visit CTA link',
        'type' => 'link',
        'storage_settings' => [],
        'field_settings' => ['title' => DRUPAL_OPTIONAL],
        'widget' => 'link_default',
        'formatter' => 'link',
    ],
    'field_surface_table_cta' => [
        'label' => 'Surface table CTA link',
        'type' => 'link',
        'storage_settings' => [],
        'field_settings' => ['title' => DRUPAL_OPTIONAL],
        'widget' => 'link_default',
        'formatter' => 'link',
    ],
    'field_brochure' => [
        'label' => 'Brochure link',
        'type' => 'link',
        'storage_settings' => [],
        'field_settings' => ['title' => DRUPAL_OPTIONAL],
        'widget' => 'link_default',
        'formatter' => 'link',
    ],
    'field_description' => [
        'label' => 'Description',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_equipments' => [
        'label' => 'Equipments',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_services' => [
        'label' => 'Services',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_building_condition' => [
        'label' => 'Building condition',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_more_information' => [
        'label' => 'More information',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_energy' => [
        'label' => 'Energy details',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_certifications' => [
        'label' => 'Certifications',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_surface_table' => [
        'label' => 'Surface table',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_location' => [
        'label' => 'Location',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_transport' => [
        'label' => 'Transport',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_map_filters' => [
        'label' => 'Map filters content',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_map' => [
        'label' => 'Map embed content',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
    'field_contact_form' => [
        'label' => 'Contact form embed',
        'type' => 'text_long',
        'storage_settings' => [],
        'field_settings' => [],
        'widget' => 'text_textarea',
        'formatter' => 'text_default',
    ],
];

$formDisplay = \Drupal::service('entity_display.repository')->getFormDisplay('node', $bundle, 'default');
$viewDisplay = \Drupal::service('entity_display.repository')->getViewDisplay('node', $bundle, 'default');

$weight = 0;
foreach ($fields as $fieldName => $definition) {
    $storage = FieldStorageConfig::loadByName('node', $fieldName);
    if (!$storage) {
        $storage = FieldStorageConfig::create([
            'field_name' => $fieldName,
            'entity_type' => 'node',
            'type' => $definition['type'],
            'cardinality' => $definition['cardinality'] ?? 1,
            'settings' => $definition['storage_settings'] ?? [],
            'translatable' => true,
        ]);
        $storage->save();
        print "Created field storage: {$fieldName}\n";
    }

    $field = FieldConfig::loadByName('node', $bundle, $fieldName);
    if (!$field) {
        $field = FieldConfig::create([
            'field_name' => $fieldName,
            'entity_type' => 'node',
            'bundle' => $bundle,
            'label' => $definition['label'],
            'required' => $definition['required'] ?? false,
            'settings' => $definition['field_settings'] ?? [],
            'translatable' => true,
        ]);
        $field->save();
        print "Created field: {$fieldName}\n";
    }

    $formDisplay->setComponent($fieldName, [
        'type' => $definition['widget'],
        'weight' => $weight,
        'region' => 'content',
        'settings' => [],
    ]);

    $viewDisplay->setComponent($fieldName, [
        'type' => $definition['formatter'],
        'label' => 'hidden',
        'weight' => $weight,
        'region' => 'content',
        'settings' => [],
    ]);

    $weight++;
}

$formDisplay->setComponent('title', [
    'type' => 'string_textfield',
    'weight' => -10,
    'region' => 'content',
    'settings' => ['size' => 60, 'placeholder' => ''],
]);
$formDisplay->setComponent('status', [
    'type' => 'boolean_checkbox',
    'weight' => 100,
    'region' => 'content',
    'settings' => ['display_label' => true],
]);

$viewDisplay->setComponent('links', [
    'weight' => 999,
    'region' => 'content',
]);

$formDisplay->save();
$viewDisplay->save();

\Drupal::service('entity_type.manager')->getStorage('entity_form_display')->resetCache();
\Drupal::service('entity_type.manager')->getStorage('entity_view_display')->resetCache();

print "Offer content type and fields are ready.\n";
