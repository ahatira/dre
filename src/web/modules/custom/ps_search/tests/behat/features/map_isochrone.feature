Feature: Map zone and isochrone sync
  In order to search by travel-time zone from the map
  As a visitor
  I want markers, isochrone geometry and zone counters to stay aligned

  Scenario: Search page exposes map zone controls
    When I am on "/find-property"
    Then the response status code should be 200
    And the response should contain "js-ps-distance-zone-toggle"
    And the response should contain "js-ps-customize-validate"

  Scenario: Isochrone API returns geometry for marker coordinates
    When I am on "/ps-search/isochrone?lat=48.8566&lng=2.3522&transport=walking&minutes=5"
    Then the response status code should be 200
    And the response should contain "\"map_bounds\""
    And the response should contain "\"polygon\""

  Scenario: Run isochrone e2e regression script
    When I run the isochrone e2e script
    Then the script output should contain "0 failed"

  Scenario: Run map isochrone sync e2e script
    When I run the map isochrone sync e2e script
    Then the script output should contain "0 failed"

  Scenario: Run map zone e2e regression script
    When I run the map zone e2e script
    Then the script output should contain "0 failed"
