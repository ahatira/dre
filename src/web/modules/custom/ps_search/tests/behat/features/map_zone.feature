Feature: Map zone search sync
  In order to keep list and map aligned per zone
  As a visitor
  I want bounds-aware APIs and search page counters to stay consistent

  Scenario: Search page loads with results header
    When I am on "/find-property"
    Then the response status code should be 200
    And I should see "results"

  Scenario: Markers API returns zone metadata
    When I am on "/ps-search/markers"
    Then the response status code should be 200
    And the response should contain "\"zone_count\""
    And the response should contain "\"markers\""

  Scenario: Global count API stays business-scoped
    When I am on "/ps-search/count"
    Then the response status code should be 200
    And the response should contain "\"count\""

  Scenario: Run map zone e2e regression script
    When I run the map zone e2e script
    Then the script output should contain "0 failed"
