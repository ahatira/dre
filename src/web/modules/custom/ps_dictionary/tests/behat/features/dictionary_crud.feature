Feature: Dictionary CRUD and resolution
  In order to guarantee dictionary integrity and resolution
  As a project team
  I want automated checks for CRUD, uniqueness, and fallback

  Scenario: Create a new dictionary type
    Given I am on "/admin/ps/structure/dictionary"
    When I create a dictionary type with code "test_type" and label "Test Type"
    Then the dictionary type "test_type" should exist

  Scenario: Add, edit, and delete a dictionary entry
    Given the dictionary type "test_type" exists
    When I add a dictionary entry with code "FOO" and label "Foo Label" to type "test_type"
    Then the dictionary entry "FOO" should exist in type "test_type"
    When I edit the dictionary entry "FOO" in type "test_type" to label "Bar Label"
    Then the dictionary entry "FOO" in type "test_type" should have label "Bar Label"
    When I delete the dictionary entry "FOO" from type "test_type"
    Then the dictionary entry "FOO" should not exist in type "test_type"

  Scenario: Prevent duplicate dictionary entry code
    Given the dictionary type "test_type" exists
    And a dictionary entry with code "DUP" exists in type "test_type"
    When I try to add another dictionary entry with code "DUP" to type "test_type"
    Then I should see an error about duplicate code

  Scenario: DictionaryResolver resolves code to label
    Given the dictionary type "test_type" exists
    And a dictionary entry with code "RES" and label "Resolvable" exists in type "test_type"
    When I resolve code "RES" in type "test_type"
    Then the resolved label should be "Resolvable"

  Scenario: DictionaryResolver fallback for unknown code
    Given the dictionary type "test_type" exists
    When I resolve code "UNKNOWN" in type "test_type"
    Then the resolved label should be "NULL"

  Scenario: Autocomplete API returns dictionary entry
    Given the dictionary type "test_type" exists
    And a dictionary entry with code "API" and label "Api Label" exists in type "test_type"
    When I query autocomplete for type "test_type" with query "API"
    Then the autocomplete response should contain "API"
