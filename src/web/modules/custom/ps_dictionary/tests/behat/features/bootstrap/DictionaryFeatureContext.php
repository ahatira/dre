<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;

final class DictionaryFeatureContext extends MinkContext implements Context {

  private string $lastScriptOutput = '';
  private string $lastResolvedLabel = '';

  /**
   * @BeforeScenario
   */
  public function resetDictionaryFixtures(): void {
    $this->runDictionaryScript('cleanup_type', 'test_type', '', '', '0', true);
    $this->runDictionaryScript('ensure_type', 'test_type', '', 'Test Type', '0');
  }

  /**
   * @Given the dictionary type :type exists
   */
  public function theDictionaryTypeExists(string $type): void {
    $this->runDictionaryScript('ensure_type', $type, '', 'Test Type', '0');
  }

  /**
   * @When I create a dictionary type with code :type and label :label
   */
  public function iCreateADictionaryTypeWithCodeAndLabel(string $type, string $label): void {
    $this->runDictionaryScript('ensure_type', $type, '', $label, '0');
  }

  /**
   * @Then the dictionary type :type should exist
   */
  public function theDictionaryTypeShouldExist(string $type): void {
    $this->runDictionaryScript('type_exists', $type);
    $this->assertOutputContains('PASS: dictionary type exists');
  }

  /**
   * @When I add a dictionary entry with code :code and label :label to type :type
   */
  public function iAddADictionaryEntryWithCodeAndLabelToType(string $code, string $label, string $type): void {
    $this->runDictionaryScript('create_entry', $type, $code, $label, '0');
  }

  /**
   * @Given a dictionary entry with code :code exists in type :type
   */
  public function aDictionaryEntryWithCodeExistsInType(string $code, string $type): void {
    $this->runDictionaryScript('create_entry', $type, $code, $code . ' Label', '0', true);
  }

  /**
   * @Given a dictionary entry with code :code and label :label exists in type :type
   */
  public function aDictionaryEntryWithCodeAndLabelExistsInType(string $code, string $label, string $type): void {
    $this->runDictionaryScript('create_entry', $type, $code, $label, '0', true);
  }

  /**
   * @Then the dictionary entry :code should exist in type :type
   */
  public function theDictionaryEntryShouldExistInType(string $code, string $type): void {
    $this->runDictionaryScript('entry_exists', $type, $code);
    $this->assertOutputContains('PASS: dictionary entry exists');
  }

  /**
   * @When I edit the dictionary entry :code in type :type to label :label
   */
  public function iEditTheDictionaryEntryInTypeToLabel(string $code, string $type, string $label): void {
    $this->runDictionaryScript('update_entry', $type, $code, $label, '0');
  }

  /**
   * @Then the dictionary entry :code in type :type should have label :label
   */
  public function theDictionaryEntryInTypeShouldHaveLabel(string $code, string $type, string $label): void {
    $this->runDictionaryScript('resolve_label', $type, $code);
    $actual = trim(str_replace('RESULT:', '', $this->lastScriptOutput));
    if ($actual !== $label) {
      throw new \RuntimeException("Unexpected label. expected='${label}' actual='${actual}'");
    }
  }

  /**
   * @When I delete the dictionary entry :code from type :type
   */
  public function iDeleteTheDictionaryEntryFromType(string $code, string $type): void {
    $this->runDictionaryScript('delete_entry', $type, $code);
  }

  /**
   * @Then the dictionary entry :code should not exist in type :type
   */
  public function theDictionaryEntryShouldNotExistInType(string $code, string $type): void {
    $this->runDictionaryScript('entry_not_exists', $type, $code);
    $this->assertOutputContains('PASS: dictionary entry not found');
  }

  /**
   * @When I try to add another dictionary entry with code :code to type :type
   */
  public function iTryToAddAnotherDictionaryEntryWithCodeToType(string $code, string $type): void {
    $this->runDictionaryScript('create_entry', $type, $code, 'Duplicate Label', '0', true);
  }

  /**
   * @Then I should see an error about duplicate code
   */
  public function iShouldSeeAnErrorAboutDuplicateCode(): void {
    $this->assertOutputContains('ERROR: duplicate code');
  }

  /**
   * @When I resolve code :code in type :type
   */
  public function iResolveCodeInType(string $code, string $type): void {
    $this->runDictionaryScript('resolve_label', $type, $code);
    $this->lastResolvedLabel = trim(str_replace('RESULT:', '', $this->lastScriptOutput));
  }

  /**
   * @Then the resolved label should be :label
   */
  public function theResolvedLabelShouldBe(string $label): void {
    if ($this->lastResolvedLabel !== $label) {
      throw new \RuntimeException("Unexpected resolved label. expected='${label}' actual='${this->lastResolvedLabel}'");
    }
  }

  /**
   * @When I query autocomplete for type :type with query :query
   */
  public function iQueryAutocompleteForTypeWithQuery(string $type, string $query): void {
    $this->runDictionaryScript('autocomplete_contains', $type, $query, $query, '0');
  }

  /**
   * @Then the autocomplete response should contain :expected
   */
  public function theAutocompleteResponseShouldContain(string $expected): void {
    $this->assertOutputContains('PASS: autocomplete contains expected value');
    if (strpos($this->lastScriptOutput, $expected) === false) {
      throw new \RuntimeException("Autocomplete response does not contain '${expected}'.\nOutput:\n" . $this->lastScriptOutput);
    }
  }

  private function runDictionaryScript(string $action, string $type = '', string $code = '', string $label = '', string $weight = '0', bool $allowNonZeroExit = false): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_dictionary/tests/e2e_dictionary.sh %s %s %s %s %s 2>&1',
      escapeshellarg($action),
      escapeshellarg($type),
      escapeshellarg($code),
      escapeshellarg($label),
      escapeshellarg($weight)
    );

    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);

    if ($exitCode !== 0 && !$allowNonZeroExit) {
      throw new \RuntimeException("Dictionary E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  private function assertOutputContains(string $expected): void {
    if (strpos($this->lastScriptOutput, $expected) === false) {
      throw new \RuntimeException("Script output missing expected fragment '${expected}'.\nOutput:\n" . $this->lastScriptOutput);
    }
  }

}
