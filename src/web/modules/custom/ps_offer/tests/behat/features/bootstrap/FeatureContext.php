<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;

final class FeatureContext extends MinkContext implements Context {

  private string $lastScriptOutput = '';

  /**
   * @When I run the offer gallery e2e script for node :nid
   */
  public function iRunTheOfferGalleryE2EScriptForNode(string $nid): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_gallery.sh http://localhost:8080 %s 2>&1',
      escapeshellarg($nid)
    );

    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);

    $this->lastScriptOutput = implode("\n", $output);

    if ($exitCode !== 0) {
      throw new RuntimeException("Offer gallery E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @When I run the offer reference e2e script for node :nid
   */
  public function iRunTheOfferReferenceE2EScriptForNode(string $nid): void {
    $this->runOfferReferenceScript($nid, 'LOC', 'BUR');
  }

  /**
   * @When I run the offer reference e2e script for node :nid with operation :operation and asset :asset
   */
  public function iRunTheOfferReferenceE2EScriptForNodeWithOperationAndAsset(string $nid, string $operation, string $asset): void {
    $this->runOfferReferenceScript($nid, strtoupper($operation), strtoupper($asset));
  }

  /**
   * @When I run the offer reference uniqueness e2e script
   */
  public function iRunTheOfferReferenceUniquenessE2EScript(): void {
    $this->runOfferReferenceUniquenessScript('LOC', 'BUR');
  }

  /**
   * @When I run the offer reference uniqueness e2e script with operation :operation and asset :asset
   */
  public function iRunTheOfferReferenceUniquenessE2EScriptWithOperationAndAsset(string $operation, string $asset): void {
    $this->runOfferReferenceUniquenessScript(strtoupper($operation), strtoupper($asset));
  }

  /**
   * @When I run the offer reference uniqueness e2e script in :mode mode with operation :operation and asset :asset
   */
  public function iRunTheOfferReferenceUniquenessE2EScriptInModeWithOperationAndAsset(string $mode, string $operation, string $asset): void {
    $this->runOfferReferenceUniquenessScript(strtoupper($operation), strtoupper($asset), $mode);
  }

  private function runOfferReferenceScript(string $nid, string $operation, string $asset): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_reference.sh %s %s %s 2>&1',
      escapeshellarg($nid),
      escapeshellarg($operation),
      escapeshellarg($asset)
    );

    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);

    $this->lastScriptOutput = implode("\n", $output);

    if ($exitCode !== 0) {
      throw new RuntimeException("Offer E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  private function runOfferReferenceUniquenessScript(string $operation, string $asset, string $mode = 'basic'): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_reference_uniqueness.sh %s %s %s 2>&1',
      escapeshellarg($operation),
      escapeshellarg($asset),
      escapeshellarg($mode)
    );

    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);

    $this->lastScriptOutput = implode("\n", $output);

    if ($exitCode !== 0) {
      throw new RuntimeException("Offer uniqueness E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @When I run the offer validation e2e script for node :nid with budget :budget and status :status
   */
  public function iRunTheOfferValidationE2EScriptForNodeWithBudgetAndStatus(string $nid, string $budget, string $status): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh %s %s %s 2>&1',
      escapeshellarg($nid),
      escapeshellarg($budget),
      escapeshellarg($status)
    );
    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);
    if ($exitCode !== 0 && strpos($this->lastScriptOutput, 'FAIL:') === false) {
      throw new RuntimeException("Offer validation E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @When I run the offer validation e2e script for node :nid with no primary agent
   */
  public function iRunTheOfferValidationE2EScriptForNodeWithNoPrimaryAgent(string $nid): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh %s 100 published offer no-agent 2>&1',
      escapeshellarg($nid)
    );
    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);
    // Pour l'instant, le script ne gère pas vraiment l'agent, mais le step est prêt.
  }

  /**
   * @When I run the offer validation e2e script for manual duplicate rejection
   */
  public function iRunTheOfferValidationE2EScriptForManualDuplicateRejection(): void {
    $cmd = 'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 100 draft offer manual-duplicate 2>&1';
    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);
    if ($exitCode !== 0 && strpos($this->lastScriptOutput, 'FAIL:') === false) {
      throw new RuntimeException("Offer validation E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @When I run the offer validation e2e script for manual duplicate rejection on published node
   */
  public function iRunTheOfferValidationE2EScriptForManualDuplicateRejectionOnPublishedNode(): void {
    $cmd = 'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 100 published offer manual-duplicate-published 2>&1';
    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);
    if ($exitCode !== 0 && strpos($this->lastScriptOutput, 'FAIL:') === false) {
      throw new RuntimeException("Offer validation E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @When I run the offer validation e2e script for manual self-edit preservation
   */
  public function iRunTheOfferValidationE2EScriptForManualSelfEditPreservation(): void {
    $cmd = 'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh 1 100 draft offer manual-self-edit 2>&1';
    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);
    if ($exitCode !== 0 && strpos($this->lastScriptOutput, 'FAIL:') === false) {
      throw new RuntimeException("Offer validation E2E script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @When I run the offer validation e2e script for node :nid with bundle :bundle
   */
  public function iRunTheOfferValidationE2EScriptForNodeWithBundle(string $nid, string $bundle): void {
    $cmd = sprintf(
      'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && cd /home/amine/ps_project_wsl/src && bash web/modules/custom/ps_offer/tests/e2e_offer_validation.sh %s 100 published %s 2>&1',
      escapeshellarg($nid),
      escapeshellarg($bundle)
    );
    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);
    $this->lastScriptOutput = implode("\n", $output);
  }

  /**
   * @Then the script output should contain :expected
   */
  public function theScriptOutputShouldContain(string $expected): void {
    if (strpos($this->lastScriptOutput, $expected) === false) {
      throw new \Exception("Script output does not contain expected string: '" . $expected . "'.\nActual output:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @Then print script output
   */
  public function printScriptOutput(): void {
    echo "\n" . $this->lastScriptOutput . "\n";
  }

}
