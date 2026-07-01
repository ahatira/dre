<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;

final class FeatureModuleContext extends MinkContext implements Context {

  private string $lastScriptOutput = '';
  private array $lastPayloadDefaults = [];
  private int $lastCatalogueOptionsCount = -1;
  private int $lastCatalogueVisibility = -1;
  private string $roundtripInitialPayload = '{}';
  private string $roundtripUpdatedPayload = '{}';

  /**
   * @BeforeScenario
   */
  public function resetFeatureFixtures(): void {
    $this->runFeatureScript('cleanup_group', 'test_feature_group', '', '');
    $this->runFeatureScript('ensure_group', 'test_feature_group', '', 'Test Feature Group');
    $this->runFeatureScript('ensure_vocabulary', 'tags', '', '', 'flag', '');
  }

  /**
   * @Given the feature group :groupId exists
   */
  public function theFeatureGroupExists(string $groupId): void {
    $this->runFeatureScript('ensure_group', $groupId, '', 'Test Feature Group');
  }

  /**
   * @When I verify feature type :typeDriver exists in API
   */
  public function iVerifyFeatureTypeExistsInApi(string $typeDriver): void {
    $this->runFeatureScript('feature_type_exists', '', '', '', $typeDriver);
  }

  /**
   * @Given dictionary fixture :dictionaryType exists for feature tests
   */
  public function dictionaryFixtureExistsForFeatureTests(string $dictionaryType): void {
    $this->runFeatureScript('ensure_dictionary_fixture', $dictionaryType, '', '');
    $this->assertOutputContains('PASS: dictionary fixture ready');
  }

  /**
   * @When I create feature definition :defId with code :code and label :label in group :groupId using type :typeDriver
   */
  public function iCreateFeatureDefinitionWithCodeAndLabelInGroupUsingType(string $defId, string $code, string $label, string $groupId, string $typeDriver): void {
    $this->runFeatureScript('create_definition', $groupId, $defId, $label, $typeDriver, $code);
  }

  /**
   * @When I create feature definition :defId with code :code and label :label in group :groupId using type :typeDriver and payload defaults:
   */
  public function iCreateFeatureDefinitionWithCodeAndLabelInGroupUsingTypeAndPayloadDefaults(string $defId, string $code, string $label, string $groupId, string $typeDriver, string $payloadDefaults): void {
    $payloadB64 = base64_encode($payloadDefaults);
    $this->runFeatureScript('create_definition', $groupId, $defId, $label, $typeDriver, $code, false, $payloadB64);
  }

  /**
   * @When I create feature definition :defId with code :code and label :label in group :groupId using type :typeDriver with required asset types :assetTypesCsv
   */
  public function iCreateFeatureDefinitionWithRequiredAssetTypes(string $defId, string $code, string $label, string $groupId, string $typeDriver, string $assetTypesCsv): void {
    $required = array_values(array_filter(array_map(static fn(string $v): string => trim($v), explode(',', $assetTypesCsv)), static fn(string $v): bool => $v !== ''));
    $requiredB64 = base64_encode(json_encode($required, JSON_THROW_ON_ERROR));
    $this->runFeatureScript('create_definition', $groupId, $defId, $label, $typeDriver, $code, false, '', $requiredB64);
  }

  /**
   * @Given feature definition :defId with code :code and label :label exists in group :groupId using type :typeDriver
   */
  public function featureDefinitionWithCodeAndLabelExistsInGroupUsingType(string $defId, string $code, string $label, string $groupId, string $typeDriver): void {
    $this->runFeatureScript('create_definition', $groupId, $defId, $label, $typeDriver, $code, true);
  }

  /**
   * @When I try to create feature definition :defId with code :code and label :label in group :groupId using type :typeDriver
   */
  public function iTryToCreateFeatureDefinitionWithCodeAndLabelInGroupUsingType(string $defId, string $code, string $label, string $groupId, string $typeDriver): void {
    $this->runFeatureScript('create_definition', $groupId, $defId, $label, $typeDriver, $code, true);
  }

  /**
   * @When I try to create feature definition :defId with code :code and label :label in group :groupId using type :typeDriver and payload defaults:
   */
  public function iTryToCreateFeatureDefinitionWithCodeAndLabelInGroupUsingTypeAndPayloadDefaults(string $defId, string $code, string $label, string $groupId, string $typeDriver, string $payloadDefaults): void {
    $payloadB64 = base64_encode($payloadDefaults);
    $this->runFeatureScript('create_definition', $groupId, $defId, $label, $typeDriver, $code, true, $payloadB64);
  }

  /**
   * @Given I set initial feature builder payload to:
   */
  public function iSetInitialFeatureBuilderPayloadTo(string $payload): void {
    $this->roundtripInitialPayload = $payload;
  }

  /**
   * @Given I set updated feature builder payload to:
   */
  public function iSetUpdatedFeatureBuilderPayloadTo(string $payload): void {
    $this->roundtripUpdatedPayload = $payload;
  }

  /**
   * @Then feature definition :defId should exist
   */
  public function featureDefinitionShouldExist(string $defId): void {
    $this->runFeatureScript('definition_exists', '', $defId, '');
    $this->assertOutputContains('PASS: feature definition exists');
  }

  /**
   * @When I update feature definition :defId label to :label
   */
  public function iUpdateFeatureDefinitionLabelTo(string $defId, string $label): void {
    $this->runFeatureScript('update_definition_label', '', $defId, $label);
  }

  /**
   * @Then feature definition :defId should have label :label
   */
  public function featureDefinitionShouldHaveLabel(string $defId, string $label): void {
    $this->runFeatureScript('definition_label', '', $defId, '');
    $actual = trim(str_replace('RESULT:', '', $this->lastScriptOutput));
    if ($actual !== $label) {
      throw new RuntimeException("Unexpected feature label. expected='${label}' actual='${actual}'");
    }
  }

  /**
   * @Then feature definition :defId payload default :key should be :expected
   */
  public function featureDefinitionPayloadDefaultShouldBe(string $defId, string $key, string $expected): void {
    $payload = $this->loadPayloadDefaults($defId);
    if (!array_key_exists($key, $payload)) {
      throw new RuntimeException("Missing payload default key '${key}'.");
    }

    $normalizedExpected = $this->normalizeExpectedScalar($expected);
    if ($payload[$key] !== $normalizedExpected) {
      $actual = is_scalar($payload[$key]) ? (string) $payload[$key] : json_encode($payload[$key]);
      throw new RuntimeException("Unexpected payload default for key '${key}'. expected='${expected}' actual='${actual}'");
    }
  }

  /**
   * @Then feature definition :defId payload default list :key should contain :item
   */
  public function featureDefinitionPayloadDefaultListShouldContain(string $defId, string $key, string $item): void {
    $payload = $this->loadPayloadDefaults($defId);
    if (!isset($payload[$key]) || !is_array($payload[$key])) {
      throw new RuntimeException("Payload default key '${key}' is missing or not a list.");
    }

    if (!in_array($item, $payload[$key], true)) {
      throw new RuntimeException("Payload list '${key}' does not contain '${item}'.");
    }
  }

  /**
   * @Then feature definition :defId payload default list :key should have length :length
   */
  public function featureDefinitionPayloadDefaultListShouldHaveLength(string $defId, string $key, string $length): void {
    $payload = $this->loadPayloadDefaults($defId);
    if (!isset($payload[$key]) || !is_array($payload[$key])) {
      throw new RuntimeException("Payload default key '${key}' is missing or not a list.");
    }

    $expectedLength = (int) $length;
    if (count($payload[$key]) !== $expectedLength) {
      throw new RuntimeException("Unexpected payload list length for '${key}'. expected=${expectedLength} actual=" . count($payload[$key]));
    }
  }

  /**
   * @When I delete feature definition :defId
   */
  public function iDeleteFeatureDefinition(string $defId): void {
    $this->runFeatureScript('delete_definition', '', $defId, '');
  }

  /**
   * @Then feature definition :defId should not exist
   */
  public function featureDefinitionShouldNotExist(string $defId): void {
    $this->runFeatureScript('definition_not_exists', '', $defId, '');
    $this->assertOutputContains('PASS: feature definition not found');
  }

  /**
   * @Then the script output should contain :expected
   */
  public function theScriptOutputShouldContainForFeature(string $expected): void {
    $this->assertOutputContains($expected);
  }

  /**
   * @When I fetch catalogue options count for feature definition :defId
   */
  public function iFetchCatalogueOptionsCountForFeatureDefinition(string $defId): void {
    $this->runFeatureScript('catalogue_options_count', '', $defId, '');
    $raw = trim(str_replace('RESULT:', '', $this->lastScriptOutput));
    if (!is_numeric($raw)) {
      throw new RuntimeException("Invalid catalogue options count output: ${raw}");
    }
    $this->lastCatalogueOptionsCount = (int) $raw;
  }

  /**
   * @When I check catalogue visibility for feature definition :defId with asset type :assetType
   */
  public function iCheckCatalogueVisibilityForFeatureDefinitionWithAssetType(string $defId, string $assetType): void {
    $this->runFeatureScript('catalogue_definition_visible_for_asset', '', $defId, '', 'flag', $assetType);
    $raw = trim(str_replace('RESULT:', '', $this->lastScriptOutput));
    if (!is_numeric($raw)) {
      throw new RuntimeException("Invalid catalogue visibility output: ${raw}");
    }
    $this->lastCatalogueVisibility = (int) $raw;
  }

  /**
   * @When I run feature builder roundtrip for definition :defId
   */
  public function iRunFeatureBuilderRoundtripForDefinition(string $defId): void {
    $this->iRunFeatureBuilderRoundtripForDefinitionOnOfferNode($defId, '0');
  }

  /**
   * @When I run feature builder roundtrip for definition :defId on offer node :nid
   */
  public function iRunFeatureBuilderRoundtripForDefinitionOnOfferNode(string $defId, string $nid): void {
    $initialB64 = base64_encode($this->roundtripInitialPayload);
    $updatedB64 = base64_encode($this->roundtripUpdatedPayload);
    $this->runFeatureScript('roundtrip_builder_state', $nid, $defId, '', 'flag', '', false, $initialB64, $updatedB64);
  }

  /**
   * @Then catalogue visibility should be :expected
   */
  public function catalogueVisibilityShouldBe(string $expected): void {
    $expectedVisible = strtolower($expected) === 'visible' ? 1 : 0;
    if ($this->lastCatalogueVisibility !== $expectedVisible) {
      throw new RuntimeException("Unexpected catalogue visibility. expected=${expectedVisible} actual={$this->lastCatalogueVisibility}");
    }
  }

  /**
   * @Then catalogue options count should be :expected
   */
  public function catalogueOptionsCountShouldBe(string $expected): void {
    $expectedCount = (int) $expected;
    if ($this->lastCatalogueOptionsCount !== $expectedCount) {
      throw new RuntimeException("Unexpected catalogue options count. expected=${expectedCount} actual={$this->lastCatalogueOptionsCount}");
    }
  }

  private function runFeatureScript(string $action, string $groupId = '', string $defId = '', string $label = '', string $typeDriver = 'flag', string $code = '', bool $allowNonZeroExit = false, string $payloadB64 = '', string $requiredAssetsB64 = ''): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_feature/tests/e2e_feature.sh %s %s %s %s %s %s %s %s 2>&1',
      escapeshellarg($action),
      escapeshellarg($groupId),
      escapeshellarg($defId),
      escapeshellarg($label),
      escapeshellarg($typeDriver),
      escapeshellarg($code),
      escapeshellarg($payloadB64),
      escapeshellarg($requiredAssetsB64)
    );

    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);

    if ($exitCode !== 0 && !$allowNonZeroExit) {
      throw new RuntimeException("Feature E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  private function assertOutputContains(string $expected): void {
    if (strpos($this->lastScriptOutput, $expected) === false) {
      throw new RuntimeException("Script output missing expected fragment '${expected}'.\nOutput:\n" . $this->lastScriptOutput);
    }
  }

  private function loadPayloadDefaults(string $defId): array {
    $this->runFeatureScript('definition_payload_json', '', $defId, '');
    $raw = trim(str_replace('RESULT:', '', $this->lastScriptOutput));
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
      throw new RuntimeException("Invalid payload defaults JSON for '${defId}': ${raw}");
    }
    $this->lastPayloadDefaults = $decoded;
    return $decoded;
  }

  private function normalizeExpectedScalar(string $expected): mixed {
    $lower = strtolower($expected);
    if ($lower === 'true') {
      return true;
    }
    if ($lower === 'false') {
      return false;
    }
    if ($lower === 'null') {
      return null;
    }
    if (is_numeric($expected)) {
      return str_contains($expected, '.') ? (float) $expected : (int) $expected;
    }
    return $expected;
  }

}
