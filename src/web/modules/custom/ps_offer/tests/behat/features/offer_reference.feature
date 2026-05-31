Feature: Offer reference regression checks
  In order to protect offer reference behavior
  As a project team
  I want reproducible automated checks for manual and auto modes

  Scenario: Run offer reference end-to-end script from Behat (default matrix case)
    Given I am on "/admin/content"
    When I run the offer reference e2e script for node 1
    Then the script output should contain "PASS: manual mode persisted reference"
    And the script output should contain "PASS: manual mode persisted auto=0"
    And the script output should contain "PASS: auto mode persisted auto=1"
    And the script output should contain "PASS: auto mode generated prefix matches operation/asset mapping"
    And the script output should contain "PASS: auto mode regenerated reference"

  Scenario Outline: Run offer reference matrix scenario <operation>/<asset>
    Given I am on "/admin/content"
    When I run the offer reference e2e script for node 1 with operation <operation> and asset <asset>
    Then the script output should contain "PASS: manual mode persisted reference"
    And the script output should contain "PASS: auto mode persisted auto=1"
    And the script output should contain "PASS: auto mode generated prefix matches operation/asset mapping"
    And the script output should contain "PASS: auto mode regenerated reference"

    Examples:
      | operation | asset |
      | LOC       | BUR   |
      | VEN       | COM   |
      | LOC       | ACT   |
      | LOC       | ENT   |
      | VEN       | BUR   |
      | VEN       | COW   |

  Scenario: Fallback mapping when operation is unknown
    Given I am on "/admin/content"
    When I run the offer reference e2e script for node 1 with operation FOO and asset BUR
    Then the script output should contain "PASS: auto mode generated prefix matches operation/asset mapping"

  Scenario: Fallback mapping when asset is unknown
    Given I am on "/admin/content"
    When I run the offer reference e2e script for node 1 with operation LOC and asset FOO
    Then the script output should contain "PASS: auto mode generated prefix matches operation/asset mapping"

  Scenario: Logical concurrency keeps references unique for auto mode
    Given I am on "/admin/content"
    When I run the offer reference uniqueness e2e script
    Then the script output should contain "PASS: logical concurrency uniqueness across 3 auto-generated references"
    And the script output should contain "PASS: logical concurrency references follow expected prefix format"
    And the script output should contain "PASS: logical concurrency counters are strictly increasing"

  Scenario: Logical concurrency uniqueness still holds with fallback mapping
    Given I am on "/admin/content"
    When I run the offer reference uniqueness e2e script with operation FOO and asset BAR
    Then the script output should contain "PASS: logical concurrency uniqueness across 3 auto-generated references"
    And the script output should contain "PASS: logical concurrency references follow expected prefix format"

  Scenario: Two existing offers remain unique during logical parallel roundtrip
    Given I am on "/admin/content"
    When I run the offer reference uniqueness e2e script in "parallel-roundtrip-two" mode with operation LOC and asset BUR
    Then the script output should contain "PASS: parallel logical roundtrip keeps references unique between existing offers"
    And the script output should contain "PASS: parallel logical roundtrip references follow expected prefix format"
