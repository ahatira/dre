<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Behat context for ps_search URL and filter bar scenarios.
 */
final class SearchFeatureContext extends MinkContext implements Context {

  private string $lastScriptOutput = '';

  /**
   * @When I run the search urls e2e script
   */
  public function iRunTheSearchUrlsE2eScript(): void {
    $this->runProjectScript('web/modules/custom/ps_search/tests/b2b_search_urls_security.sh', 'Search URLs E2E');
  }

  /**
   * @When I run the map zone e2e script
   */
  public function iRunTheMapZoneE2eScript(): void {
    $this->runProjectScript('web/modules/custom/ps_search/tests/b2b_map_zone.sh', 'Map zone E2E');
  }

  /**
   * @When I run the isochrone e2e script
   */
  public function iRunTheIsochroneE2eScript(): void {
    $this->runProjectScript('web/modules/custom/ps_search/tests/b2b_isochrone.sh', 'Isochrone E2E');
  }

  /**
   * @When I run the map isochrone sync e2e script
   */
  public function iRunTheMapIsochroneSyncE2eScript(): void {
    $this->runProjectScript('web/modules/custom/ps_search/tests/b2b_map_isochrone_sync.sh', 'Map isochrone sync E2E');
  }

  /**
   * @When I run the marker cluster e2e script
   */
  public function iRunTheMarkerClusterE2eScript(): void {
    $this->runProjectScript('web/modules/custom/ps_search/tests/b2b_markers_cluster.sh', 'Marker cluster E2E');
  }

  /**
   * Runs a bash script from the Composer project root.
   */
  private function runProjectScript(string $relativeScript, string $label): void {
    $cmd = 'export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin && '
      . 'cd /home/amine/ps_project_wsl/src && '
      . 'bash ' . $relativeScript . ' 2>&1';

    $output = [];
    $exitCode = 0;
    exec('bash -lc ' . escapeshellarg($cmd), $output, $exitCode);

    $this->lastScriptOutput = implode("\n", $output);

    if ($exitCode !== 0) {
      throw new RuntimeException("{$label} script failed:\n" . $this->lastScriptOutput);
    }
  }

  /**
   * @Then the script output should contain :text
   */
  public function theScriptOutputShouldContain(string $text): void {
    if (!str_contains($this->lastScriptOutput, $text)) {
      throw new RuntimeException(
        "Expected script output to contain \"{$text}\".\nOutput:\n" . $this->lastScriptOutput
      );
    }
  }

}
