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
			   'origin' => '',
			   'enable_debug' => FALSE,
			   'poi_types' => ['transports', 'parkings', 'restaurants', 'hotels'],
			   'poi_radius' => 1000,
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
		$elements['origin'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Origin coordinates'),
			'#description' => $this->t('Enter coordinates as [lat],[lng] or use a token like [node:field_geofield].'),
			'#default_value' => $this->getSetting('origin'),
			'#states' => [
				'visible' => [
					':input[name$="[enable_directions]"]' => ['checked' => TRUE],
				],
			],
		];
		$elements['enable_debug'] = [
			'#type' => 'checkbox',
			'#title' => $this->t('Enable debug'),
			'#default_value' => $this->getSetting('enable_debug'),
			'#description' => $this->t('If checked, debug logs will be shown in the browser console.'),
			'#states' => [
				'visible' => [
					':input[name$="[enable_directions]"]' => ['checked' => TRUE],
				],
			],
		];
		   // Types de POI disponibles
		   $poi_options = [
			   'transports' => $this->t('Transports'),
			   'parkings' => $this->t('Parkings'),
			   'restaurants' => $this->t('Restaurants'),
			   'hotels' => $this->t('Hôtels'),
		   ];
		   $elements['poi_types'] = [
			   '#type' => 'checkboxes',
			   '#title' => $this->t('Types de POI à afficher'),
			   '#options' => $poi_options,
			   '#default_value' => $this->getSetting('poi_types'),
			   '#description' => $this->t('Sélectionnez les types de points d’intérêt à proposer.'),
			   '#states' => [
				   'visible' => [
					   ':input[name$="[enable_directions]"]' => ['checked' => TRUE],
				   ],
			   ],
		   ];
		   $elements['poi_radius'] = [
			   '#type' => 'number',
			   '#title' => $this->t('Rayon de recherche des POI (mètres)'),
			   '#default_value' => $this->getSetting('poi_radius'),
			   '#min' => 100,
			   '#max' => 10000,
			   '#step' => 100,
			   '#description' => $this->t('Rayon en mètres pour la recherche de POI autour du centre de la carte.'),
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
			// Passe dynamiquement la config POI/radius au DirectionsForm via argument
			$poi_types = array_filter($this->getSetting('poi_types'));
			$poi_radius = (int) $this->getSetting('poi_radius');
			$form = \Drupal::formBuilder()->getForm('Drupal\\ps_geo_directions\\Form\\DirectionsForm', [
				'poi_types' => $poi_types,
				'poi_radius' => $poi_radius,
			]);
			$position = $this->getSetting('directions_position');
			// Passe la valeur du champ origin à JS via drupalSettings
			$origin = $this->getSetting('origin');
			if (!empty($origin)) {
				// Remplace le token par sa valeur réelle si possible.
				/** @var \Drupal\Core\Utility\Token $token_service */
				$token_service = \Drupal::token();
				$entity = $items->getEntity();
				$replaced = $token_service->replace($origin, ['node' => $entity]);
				if (!isset($elements[0]['#attached'])) {
					$elements[0]['#attached'] = [];
				}
				if (!isset($elements[0]['#attached']['drupalSettings'])) {
					$elements[0]['#attached']['drupalSettings'] = [];
				}
				$elements[0]['#attached']['drupalSettings']['ps_geo_directions']['origin'] = $replaced;
			}
			// Passe enable_debug dans drupalSettings
			$enable_debug = $this->getSetting('enable_debug');
			if (!isset($elements[0]['#attached'])) {
				$elements[0]['#attached'] = [];
			}
			if (!isset($elements[0]['#attached']['drupalSettings'])) {
				$elements[0]['#attached']['drupalSettings'] = [];
			}
			$elements[0]['#attached']['drupalSettings']['ps_geo_directions']['enable_debug'] = $enable_debug ? TRUE : FALSE;
			   // Passe les types de POI et le radius dans drupalSettings pour le JS
			   $poi_types = array_filter($this->getSetting('poi_types'));
			   $poi_radius = (int) $this->getSetting('poi_radius');
			   if (!isset($elements[0]['#attached'])) {
				   $elements[0]['#attached'] = [];
			   }
			   if (!isset($elements[0]['#attached']['drupalSettings'])) {
				   $elements[0]['#attached']['drupalSettings'] = [];
			   }
			   $elements[0]['#attached']['drupalSettings']['ps_geo_directions']['poi_types'] = $poi_types;
			   $elements[0]['#attached']['drupalSettings']['ps_geo_directions']['poi_radius'] = $poi_radius;
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
