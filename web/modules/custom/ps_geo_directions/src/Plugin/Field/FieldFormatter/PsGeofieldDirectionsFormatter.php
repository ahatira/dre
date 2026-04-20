<?php
namespace Drupal\ps_geo_directions\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'ps_geofield_directions' formatter.
 *
 * @FieldFormatter(
 *   id = "ps_geofield_directions",
 *   label = @Translation("PS Geographic directions widget"),
 *   field_types = {"geofield"}
 * )
 */

use Drupal\geofield_map\Plugin\Field\FieldFormatter\GeofieldGoogleMapFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\geofield\GeoPHP\GeoPHPInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Utility\Token;
use Drupal\geofield_map\Services\GoogleMapsService;
use Drupal\geofield_map\Services\MarkerIconService;
use Symfony\Component\DependencyInjection\ContainerInterface;


class PsGeofieldDirectionsFormatter extends GeofieldGoogleMapFormatter {

	public function __construct(
		$plugin_id,
		$plugin_definition,
		FieldDefinitionInterface $field_definition,
		array $settings,
		$label,
		$view_mode,
		array $third_party_settings,
		ConfigFactoryInterface $config_factory,
		TranslationInterface $string_translation,
		LinkGeneratorInterface $link_generator,
		EntityTypeManagerInterface $entity_type_manager,
		EntityDisplayRepositoryInterface $entity_display_repository,
		EntityFieldManagerInterface $entity_field_manager,
		GeoPHPInterface $geophp_wrapper,
		RendererInterface $renderer,
		ModuleHandlerInterface $module_handler,
		Token $token,
		GoogleMapsService $google_maps_service,
		MarkerIconService $marker_icon_service
	) {
		parent::__construct(
			$plugin_id,
			$plugin_definition,
			$field_definition,
			$settings,
			$label,
			$view_mode,
			$third_party_settings,
			$config_factory,
			$string_translation,
			$link_generator,
			$entity_type_manager,
			$entity_display_repository,
			$entity_field_manager,
			$geophp_wrapper,
			$renderer,
			$module_handler,
			$token,
			$google_maps_service,
			$marker_icon_service
		);
	}

	public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
		return new static(
			$plugin_id,
			$plugin_definition,
			$configuration['field_definition'],
			$configuration['settings'],
			$configuration['label'],
			$configuration['view_mode'],
			$configuration['third_party_settings'],
			$container->get('config.factory'),
			$container->get('string_translation'),
			$container->get('link_generator'),
			$container->get('entity_type.manager'),
			$container->get('entity_display.repository'),
			$container->get('entity_field.manager'),
			$container->get('geofield.geophp'),
			$container->get('renderer'),
			$container->get('module_handler'),
			$container->get('token'),
			$container->get('geofield_map.google_maps'),
			$container->get('geofield_map.marker_icon')
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function defaultSettings() {
		return [
			'enable_directions' => TRUE,
			'directions_position' => 'TOP_LEFT',
		] + parent::defaultSettings();
	}

	/**
	 * {@inheritdoc}
	 */
	public function settingsForm(array $form, FormStateInterface $form_state) {
		$elements = parent::settingsForm($form, $form_state);
		$elements['enable_directions'] = [
			'#type' => 'checkbox',
			'#title' => $this->t('Enable the PS Directions widget'),
			'#default_value' => $this->getSetting('enable_directions'),
			'#description' => $this->t('Display the directions widget on the map.'),
		];
		$elements['directions_position'] = [
			'#type' => 'select',
			'#title' => $this->t('Directions widget position'),
			'#options' => [
				'TOP_LEFT' => $this->t('Top Left'),
				'TOP_RIGHT' => $this->t('Top Right'),
				'BOTTOM_LEFT' => $this->t('Bottom Left'),
				'BOTTOM_RIGHT' => $this->t('Bottom Right'),
			],
			'#default_value' => $this->getSetting('directions_position'),
			'#description' => $this->t('Choose the position of the directions widget overlay on the map.'),
			'#states' => [
				'visible' => [
					':input[name$="[enable_directions]"]' => ['checked' => TRUE],
				],
			],
		];
		return $elements;
	}

	/**
	 * {@inheritdoc}
	 */
	public function viewElements(FieldItemListInterface $items, $langcode) {
		// Log pour vérifier que le formatter est bien appelé.
		\Drupal::logger('ps_geo_directions')->notice('Formatter called');
		$elements = parent::viewElements($items, $langcode);
		if ($this->getSetting('enable_directions')) {
			$form = \Drupal::formBuilder()->getForm('Drupal\\ps_geo_directions\\Form\\DirectionsForm');
			$position = $this->getSetting('directions_position');
			// Overlay positions CSS.
			$positions_css = [
				'TOP_LEFT' => 'top:16px;left:16px;',
				'TOP_RIGHT' => 'top:16px;right:16px;',
				'BOTTOM_LEFT' => 'bottom:16px;left:16px;',
				'BOTTOM_RIGHT' => 'bottom:16px;right:16px;',
			];
			$overlay_style = 'position:absolute;z-index:10;background:#fff;padding:8px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,0.15);';
			$overlay = NULL;
			if (isset($positions_css[$position])) {
				$overlay = [
					'#type' => 'container',
					'#attributes' => [
						'class' => ['ps-geo-directions-overlay'],
						'style' => $overlay_style . $positions_css[$position],
					],
					'form' => $form,
				];
			}
			else {
				// Fallback: top left overlay.
				$overlay = [
					'#type' => 'container',
					'#attributes' => [
						'class' => ['ps-geo-directions-overlay'],
						'style' => $overlay_style . $positions_css['TOP_LEFT'],
					],
					'form' => $form,
				];
			}
			// Ajoute un wrapper position:relative autour de la carte et de l'overlay.
			// On force la hauteur du wrapper à celle de la carte (par défaut 400px, à ajuster selon config).
			$map_height = '400px';
			if (!empty($this->getSetting('map_dimensions')['height'])) {
				$map_height = is_numeric($this->getSetting('map_dimensions')['height']) ? $this->getSetting('map_dimensions')['height'] . 'px' : $this->getSetting('map_dimensions')['height'];
			}
			// Fusionne les #attached du parent et du wrapper.
			$wrapper = [
				'#type' => 'container',
				'#attributes' => [
					'class' => ['ps-geo-directions-map-wrapper'],
					'style' => 'position:relative;min-height:' . $map_height . ';',
				],
				'overlay' => $overlay,
				// On injecte tous les éléments de la carte dans ce wrapper.
				'map' => $elements,
			];
			// Fusionne les #attached du parent (carte) dans le wrapper.
			if (!empty($elements[0]['#attached'])) {
				$wrapper['#attached'] = $elements[0]['#attached'];
			}
			// Ajoute la librairie directions systématiquement.
			if (!isset($wrapper['#attached'])) {
				$wrapper['#attached'] = [];
			}
			if (!isset($wrapper['#attached']['library'])) {
				$wrapper['#attached']['library'] = [];
			}
			$wrapper['#attached']['library'][] = 'ps_geo_directions/directions';
			$elements = [$wrapper];
		}
		return $elements;
	}
}
