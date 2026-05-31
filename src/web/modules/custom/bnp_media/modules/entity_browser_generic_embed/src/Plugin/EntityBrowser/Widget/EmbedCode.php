<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed\Plugin\EntityBrowser\Widget;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_browser\WidgetBase;

/**
 * Provides an embed-code widget for media creation and selection.
 *
 * @EntityBrowserWidget(
 *   id = "entity_browser_generic_embed_code",
 *   label = @Translation("Embed code"),
 *   description = @Translation("Creates or reuses media entities from embed URLs."),
 *   auto_select = FALSE
 * )
 */
final class EmbedCode extends WidgetBase {

	/**
	 * {@inheritdoc}
	 */
	public function defaultConfiguration(): array {
		return array_merge(parent::defaultConfiguration(), [
			'submit_text' => (string) $this->t('Create and select media'),
			'media_bundle' => 'remote_video',
			'url_field' => 'field_media_oembed_video',
			'name_field' => 'name',
			'deduplicate' => TRUE,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getForm(array &$original_form, FormStateInterface $form_state, array $additional_widget_parameters): array {
		$form = parent::getForm($original_form, $form_state, $additional_widget_parameters);
		$form['embed_input'] = [
			'#type' => 'textarea',
			'#title' => $this->t('Embed URLs'),
			'#description' => $this->t('Enter one URL per line. Existing media will be reused when deduplication is enabled.'),
			'#required' => TRUE,
			'#rows' => 6,
		];

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepareEntities(array $form, FormStateInterface $form_state): array {
		$entities = [];
		$parsed = $this->parseUrls((string) $form_state->getValue('embed_input', ''));
		$storage = $this->getMediaStorage();

		foreach ($parsed['valid'] as $url) {
			$target = $this->resolveTargetFromUrl($url);
			$entity = $this->configuration['deduplicate']
				? $this->findExistingMedia($storage, $url, $target['media_bundle'], $target['url_field'])
				: NULL;

			if (!$entity) {
				$entity = $storage->create([
					'bundle' => $target['media_bundle'],
					$this->configuration['name_field'] => $this->buildDefaultName($url),
					$target['url_field'] => $url,
				]);
			}
			$entities[] = $entity;
		}

		return $entities;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate(array &$form, FormStateInterface $form_state): void {
		$parsed = $this->parseUrls((string) $form_state->getValue('embed_input', ''));
		if ($parsed['valid'] === []) {
			$form_state->setError($form['widget'], $this->t('Please provide at least one valid URL.'));
			return;
		}

		if ($parsed['invalid'] !== []) {
			$form_state->setError(
				$form['widget'],
				$this->t('Invalid URL(s): @urls', ['@urls' => implode(', ', $parsed['invalid'])])
			);
			return;
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
		array_walk(
			$entities,
			static function (EntityInterface $entity): void {
				if ($entity->isNew()) {
					$entity->save();
				}
			}
		);
		$this->selectEntities($entities, $form_state);
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
		$form = parent::buildConfigurationForm($form, $form_state);
		$form['media_bundle'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Media bundle'),
			'#default_value' => $this->configuration['media_bundle'],
			'#required' => TRUE,
		];
		$form['url_field'] = [
			'#type' => 'textfield',
			'#title' => $this->t('URL field machine name'),
			'#default_value' => $this->configuration['url_field'],
			'#required' => TRUE,
		];
		$form['name_field'] = [
			'#type' => 'textfield',
			'#title' => $this->t('Name field machine name'),
			'#default_value' => $this->configuration['name_field'],
			'#required' => TRUE,
		];
		$form['deduplicate'] = [
			'#type' => 'checkbox',
			'#title' => $this->t('Reuse existing media when URL already exists'),
			'#default_value' => $this->configuration['deduplicate'],
		];

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function access() {
		$bundle = (string) $this->configuration['media_bundle'];
		if ($bundle === '') {
			return AccessResult::forbidden();
		}

		$handler = $this->entityTypeManager->getAccessControlHandler('media');
		return $handler->createAccess($bundle, NULL, [], TRUE);
	}

	/**
	 * Parses and validates user-provided URLs.
	 *
	 * @return array{valid: string[], invalid: string[]}
	 *   URLs grouped by validity.
	 */
	private function parseUrls(string $raw): array {
		$rows = preg_split('/\R+/', trim($raw)) ?: [];
		$rows = array_values(array_filter(array_map('trim', $rows), static fn(string $row): bool => $row !== ''));

		$valid = [];
		$invalid = [];
		foreach ($rows as $row) {
			if (filter_var($row, FILTER_VALIDATE_URL)) {
				$valid[] = $row;
			}
			else {
				$invalid[] = $row;
			}
		}

		return [
			'valid' => $valid,
			'invalid' => $invalid,
		];
	}

	/**
	 * Finds an existing media entity by configured URL field.
	 */
	private function findExistingMedia(EntityStorageInterface $storage, string $url, string $bundle, string $url_field): ?EntityInterface {
		$query = $storage->getQuery()
			->accessCheck(FALSE)
			->condition('bundle', $bundle)
			->condition($url_field, $url)
			->range(0, 1);

		$ids = $query->execute();
		if ($ids === []) {
			return NULL;
		}

		$id = (int) reset($ids);
		$entity = $storage->load($id);
		return $entity instanceof EntityInterface ? $entity : NULL;
	}

	/**
	 * Returns media entity storage.
	 */
	private function getMediaStorage(): EntityStorageInterface {
		return $this->entityTypeManager->getStorage('media');
	}

	/**
	 * Builds a fallback media name from a URL.
	 */
	private function buildDefaultName(string $url): string {
		$host = (string) parse_url($url, PHP_URL_HOST);
		$path = trim((string) parse_url($url, PHP_URL_PATH), '/');
		$suffix = $path !== '' ? $path : substr(hash('sha1', $url), 0, 8);
		return trim($host . ' ' . $suffix);
	}

	/**
	 * Resolves media bundle and URL field based on URL pattern.
	 *
	 * MediaHub AssetLink MP4 URLs are stored in mediahub_video without oEmbed.
	 * All other URLs keep the configured defaults (typically remote_video/oEmbed).
	 *
	 * @return array{media_bundle: string, url_field: string}
	 *   Target media bundle and URL field machine name.
	 */
	private function resolveTargetFromUrl(string $url): array {
		$default = [
			'media_bundle' => (string) $this->configuration['media_bundle'],
			'url_field' => (string) $this->configuration['url_field'],
		];

		if (!$this->isMediaHubAssetLinkMp4($url)) {
			return $default;
		}

		if (!$this->hasBundleWithField('mediahub_video', 'field_media_video_url')) {
			return $default;
		}

		return [
			'media_bundle' => 'mediahub_video',
			'url_field' => 'field_media_video_url',
		];
	}

	/**
	 * Checks if URL matches MediaHub AssetLink MP4 shape.
	 */
	private function isMediaHubAssetLinkMp4(string $url): bool {
		$host = strtolower((string) parse_url($url, PHP_URL_HOST));
		$path = (string) parse_url($url, PHP_URL_PATH);

		if ($host === '' || $path === '') {
			return FALSE;
		}

		if (!str_contains($host, 'mediahub')) {
			return FALSE;
		}

		if (!str_contains($path, '/AssetLink/')) {
			return FALSE;
		}

		return (bool) preg_match('/\.mp4$/i', $path);
	}

	/**
	 * Returns TRUE if the media bundle exists and owns the expected URL field.
	 */
	private function hasBundleWithField(string $bundle, string $field_name): bool {
		$media_type = $this->entityTypeManager->getStorage('media_type')->load($bundle);
		if (!$media_type) {
			return FALSE;
		}

		$fields = $this->entityTypeManager->getStorage('field_config')
			->loadByProperties([
				'entity_type' => 'media',
				'bundle' => $bundle,
				'field_name' => $field_name,
			]);

		return $fields !== [];
	}

}
