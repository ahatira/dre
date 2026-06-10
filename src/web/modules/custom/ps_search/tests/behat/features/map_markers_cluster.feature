Feature: Map marker grid clusters
  In order to browse dense map zones without marker truncation
  As a visitor
  I want server-side clusters when a zone exceeds the markers cap

  Scenario: Markers API exposes cluster metadata keys
    When I am on "/ps-search/markers"
    Then the response status code should be 200
    And the response should contain "\"display_mode\""
    And the response should contain "\"clusters\""

  Scenario: Run marker cluster e2e regression script
    When I run the marker cluster e2e script
    Then the script output should contain "0 failed"
