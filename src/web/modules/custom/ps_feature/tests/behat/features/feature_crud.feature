Feature: PS Feature UI and API regression
  In order to secure feature catalog behavior
  As a project team
  I want Behat coverage on UI/API and fast Drush E2E actions

  Scenario: Feature types admin UI is accessible
    Given I am on "/admin/ps/config/features"
    When I verify feature type "dictionary" exists in API
    Then the script output should contain "PASS: feature type exists"

  Scenario: Feature type API exposes expected drivers
    Given the feature group "test_feature_group" exists
    When I verify feature type "flag" exists in API
    Then the script output should contain "PASS: feature type exists"

  Scenario: Create and verify feature definition
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_total" with code "total_area" and label "Total Area" in group "test_feature_group" using type "numeric"
    Then feature definition "test_feature_total" should exist

  Scenario: Prevent duplicate feature code in same group
    Given the feature group "test_feature_group" exists
    And feature definition "test_feature_dup_a" with code "dup_code" and label "Dup A" exists in group "test_feature_group" using type "text"
    When I try to create feature definition "test_feature_dup_b" with code "dup_code" and label "Dup B" in group "test_feature_group" using type "text"
    Then the script output should contain "ERROR: duplicate code in group"

  Scenario: Update then delete feature definition
    Given the feature group "test_feature_group" exists
    And feature definition "test_feature_ops" with code "ops_code" and label "Ops Label" exists in group "test_feature_group" using type "flag"
    When I update feature definition "test_feature_ops" label to "Ops Label Updated"
    Then feature definition "test_feature_ops" should have label "Ops Label Updated"
    When I delete feature definition "test_feature_ops"
    Then feature definition "test_feature_ops" should not exist

  Scenario: Dictionary driver payload defaults are persisted
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_dict" with code "dict_code" and label "Dictionary Feature" in group "test_feature_group" using type "dictionary" and payload defaults:
      """
      {"dictionary_id":"asset_type","allow_custom":true}
      """
    Then feature definition "test_feature_dict" should exist
    And feature definition "test_feature_dict" payload default "dictionary_id" should be "asset_type"
    And feature definition "test_feature_dict" payload default "allow_custom" should be "true"

  Scenario: List driver payload defaults are persisted
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_list" with code "list_code" and label "List Feature" in group "test_feature_group" using type "list" and payload defaults:
      """
      {"options":["A","B","C"],"multiple":true}
      """
    Then feature definition "test_feature_list" should exist
    And feature definition "test_feature_list" payload default "multiple" should be "true"
    And feature definition "test_feature_list" payload default list "options" should contain "B"
    And feature definition "test_feature_list" payload default list "options" should have length "3"

  Scenario: Taxonomy driver payload defaults are persisted
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_taxo" with code "taxo_code" and label "Taxonomy Feature" in group "test_feature_group" using type "taxonomy" and payload defaults:
      """
      {"vocabulary_id":"tags","multiple":false}
      """
    Then feature definition "test_feature_taxo" should exist
    And feature definition "test_feature_taxo" payload default "vocabulary_id" should be "tags"
    And feature definition "test_feature_taxo" payload default "multiple" should be "false"

  Scenario: Taxonomy driver rejects unknown vocabulary
    Given the feature group "test_feature_group" exists
    When I try to create feature definition "test_feature_taxo_invalid" with code "taxo_invalid" and label "Taxonomy Invalid" in group "test_feature_group" using type "taxonomy" and payload defaults:
      """
      {"vocabulary_id":"unknown_vocab","multiple":false}
      """
    Then the script output should contain "ERROR: taxonomy vocabulary does not exist"

  Scenario: Dictionary driver uses dictionary_id for catalogue options
    Given the feature group "test_feature_group" exists
    And dictionary fixture "test_feature_dict_type" exists for feature tests
    When I create feature definition "test_feature_dict_catalogue" with code "dict_catalogue" and label "Dictionary Catalogue" in group "test_feature_group" using type "dictionary" and payload defaults:
      """
      {"dictionary_id":"test_feature_dict_type","allow_custom":false}
      """
    And I fetch catalogue options count for feature definition "test_feature_dict_catalogue"
    Then catalogue options count should be "2"

  Scenario: List driver uses inline options for catalogue options
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_list_catalogue" with code "list_catalogue" and label "List Catalogue" in group "test_feature_group" using type "list" and payload defaults:
      """
      {"options":["X","Y","Z"],"multiple":true}
      """
    And I fetch catalogue options count for feature definition "test_feature_list_catalogue"
    Then catalogue options count should be "3"

  Scenario: required_asset_types shows definition for allowed asset
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_required_bur" with code "required_bur" and label "Required BUR" in group "test_feature_group" using type "flag" with required asset types "BUR"
    And I check catalogue visibility for feature definition "test_feature_required_bur" with asset type "BUR"
    Then catalogue visibility should be "visible"

  Scenario: required_asset_types hides definition for disallowed asset
    Given the feature group "test_feature_group" exists
    When I create feature definition "test_feature_required_bur_only" with code "required_bur_only" and label "Required BUR Only" in group "test_feature_group" using type "flag" with required asset types "BUR"
    And I check catalogue visibility for feature definition "test_feature_required_bur_only" with asset type "COW"
    Then catalogue visibility should be "hidden"

  Scenario: Feature builder JSON state roundtrip is preserved after update
    Given the feature group "test_feature_group" exists
    And feature definition "test_feature_roundtrip" with code "roundtrip" and label "Roundtrip Feature" exists in group "test_feature_group" using type "numeric"
    And I set initial feature builder payload to:
      """
      {"value":120.5,"unit":"sqm"}
      """
    And I set updated feature builder payload to:
      """
      {"value":88.0,"unit":"sqm"}
      """
    When I run feature builder roundtrip for definition "test_feature_roundtrip" on offer node "1"
    Then the script output should contain "PASS: roundtrip state initial payload"
    And the script output should contain "PASS: roundtrip state updated payload"
    And the script output should contain "PASS: roundtrip state changed after update"
