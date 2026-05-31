<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed\Plugin\EntityBrowser\Widget;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_browser\WidgetBase;

/**
 * Provides an entity selection proxy widget using entity autocomplete.
 *
 * @EntityBrowserWidget(
 *   id = "entity_browser_generic_entity_form_proxy",
 *   label = @Translation("Entity form proxy"),
 *   description = @Translation("Selects existing entities through a configurable autocomplete proxy form."),
 *   auto_select = FALSE
 * )
 */
final class EntityFormProxy extends WidgetBase {

	/**
	 * {@inheritdoc}
	 */
	public function defaultConfiguration(): array {
		return array_merge(parent::defaultConfiguration(), [
			'submit_text' => (string) $this->t('Select entities'),
			'target_entity_type' => 'media',
			'target_bundles' => [],
			'autocomplete_placeholder' => (string) $this->t('Start typing to search entities'),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getForm(array &$original_form, FormStateInterface $form_state, array $additional_widget_parameters): array {
		$form = parent::getForm($original_form, $form_state, $additional_widget_parameters);
		$selection_settings = [];
		if ($this->configuration['target_bundles'] !== []) {
			$selection_settings['target_bundles'] = array_values($this->configuration['target_bundles']);
		}

		$form['target_entities'] = [
			'#type' => 'entity_autocomplete',
			'#title' => $this->t('Entities'),
			'#target_type' => $this->configuration['target_entity_type'],
			'#tags' => TRUE,
			'#selection_settings' => $selection_settings,
			'#attributes' => [
				'placeholder' => $this->configuration['autocomplete_placeholder'],
			],
		];

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepareEntities(array $form, FormStateInterface $form_state): array {
		$values = $form_state->getValue('target_entities', []);
		if (!is_array($values)) {
			return [];
		}

		$target_type = (string) $this->configuration['target_entity_type'];
		$storage = $this->entityTypeManager->getStorage($target_type);

		$entities = [];
		foreach ($values as $value) {
			$target_id = NULL;
			if (is_array($value) && isset($value['target_id'])) {
				$target_id = (string) $value['target_id'];
			}
			elseif (is_scalar($value)) {
				$target_id = (string) $value;
			}

			if ($target_id === NULL || $target_id === '') {
				continue;
			}

			$entity = $storage->load($target_id);
			if ($entity instanceof EntityInterface) {
				$entities[] = $entity;
			}
		}

		return $entities;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate(array &$form, FormStateInterface $form_state): void {
		$entities = $this->prepareEntities($form, $form_state);
		if ($entities === []) {
			$form_state->setError($form['widget'], $this->t('Please select at least one entity.'));
			return;
		}

		if ($this->configuration['target_bundles'] !== []) {
			$allowed = array_values($this->configuration['target_bundles']);
			foreach ($entities as $entity) {
				if (method_exists($entity, 'bundle') && !in_array($entity->bundle(), $allowed, TRUE)) {
					$form_state->setError($form['widget'], $this->t('Selected entities must belong to configured bundles only.'));
					return;
				}
			}
		}

		parent::validate($form, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit(array &$element, array &$form, FormStateInterface $form_state): void {
		if (empty($form_state->getTriggeringElement()['#eb_widget_main_submit'])) {
			return;
		}

		$entities = $this->prepareEntities($form, $form_state);
		$this->selectEntities($entities, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
		$form = parent::buildConfigurationForm($form, $form_state);
		$form['target_entity_type'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Target entity type'),
			'#default_value' => $this->configuration['target_entity_type'],
			'#required' => TRUE,
		];
		$form['target_bundles'] = [
			'#type' => 'textarea',
			'#title' => $this->t('Target bundles'),
			'#default_value' => implode(PHP_EOL, $this->configuration['target_bundles']),
			'#description' => $this->t('One bundle machine name per line. Leave empty to allow all bundles.'),
			'#rows' => 4,
		];
		$form['autocomplete_placeholder'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Autocomplete placeholder'),
			'#default_value' => $this->configuration['autocomplete_placeholder'],
			'#required' => TRUE,
		];

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
		parent::submitConfigurationForm($form, $form_state);
		$bundles_raw = (string) ($this->configuration['target_bundles'] ?? '');
		$rows = preg_split('/\R+/', trim($bundles_raw)) ?: [];
		$this->configuration['target_bundles'] = array_values(array_filter(array_map('trim', $rows), static fn(string $row): bool => $row !== ''));
	}

	/**
	 * {@inheritdoc}
	 */
	public function access() {
		$entity_type = (string) $this->configuration['target_entity_type'];
		if ($entity_type === '' || !$this->entityTypeManager->hasDefinition($entity_type)) {
			return AccessResult::forbidden();
		}

		return AccessResult::allowed();
	}

}
