Feature: Offer validation business rules
  In order to guarantee business integrity
  As a project team
  I want automated checks for offer validation rules

  Scenario: Budget missing or zero blocks publication
    Given I am on "/admin/content"
    When I run the offer validation e2e script for node 1 with budget "0" and status "published"
    Then the script output should contain "FAIL: budget validation (publication)"

  Scenario: Budget missing or zero only warns in draft
    Given I am on "/admin/content"
    When I run the offer validation e2e script for node 1 with budget "0" and status "draft"
    Then the script output should contain "WARN: budget validation (draft)"

  Scenario: Offer without primary agent is unpublished automatically
    Given I am on "/admin/content"
    When I run the offer validation e2e script for node 1 with no primary agent
    Then the script output should contain "PASS: offer unpublished due to missing agent"

  Scenario: No validation applied outside offer bundle
    Given I am on "/admin/content"
    When I run the offer validation e2e script for node 1 with bundle "page"
    Then the script output should contain "PASS: no validation applied (non-offer bundle)"

  Scenario: Manual duplicate reference is explicitly rejected
    Given I am on "/admin/content"
    When I run the offer validation e2e script for manual duplicate rejection
    Then the script output should contain "PASS: manual duplicate rejected"
    And the script output should contain "PASS: manual duplicate validation"

  Scenario: Manual self-reference is preserved on edit
    Given I am on "/admin/content"
    When I run the offer validation e2e script for manual self-edit preservation
    Then the script output should contain "PASS: manual self reference preserved on edit"
    And the script output should contain "PASS: manual self-edit validation"

  Scenario: Manual duplicate reference is explicitly rejected on published node
    Given I am on "/admin/content"
    When I run the offer validation e2e script for manual duplicate rejection on published node
    Then the script output should contain "PASS: manual duplicate rejected on published"
    And the script output should contain "PASS: manual duplicate published validation"
