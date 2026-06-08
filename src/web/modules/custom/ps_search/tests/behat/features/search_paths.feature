Feature: Search page paths and SEO URLs
  In order to guarantee search URLs work after migration
  As a visitor
  I want canonical and SEO search paths to load correctly

  Scenario: EN flexible search page loads
    When I am on "/find-property"
    Then the response status code should be 200
    And I should see "Show"

  Scenario: FR flexible search page loads with translated slug
    When I am on "/fr/recherche-immobiliere"
    Then the response status code should be 200

  Scenario: FR SEO office rent page shows active BUR filter
    When I am on "/fr/a-louer/bureaux/"
    Then the response status code should be 200
    And I should see "Bureau à louer"

  Scenario: EN SEO office rent page loads
    When I am on "/for-rent/office/"
    Then the response status code should be 200

  Scenario: Run search URL e2e regression script
    When I run the search urls e2e script
    Then the script output should contain "0 failed"
