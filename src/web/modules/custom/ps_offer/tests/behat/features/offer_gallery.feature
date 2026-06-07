Feature: Offer gallery lightbox regression checks
  In order to protect offer gallery behavior
  As a project team
  I want reproducible automated checks for hero and lightbox assets

  Scenario: Run offer gallery end-to-end script from Behat
    Given I am on "/admin/content"
    When I run the offer gallery e2e script for node 2
    Then the script output should contain "RESULT: PASS"
    And the script output should contain "lightbox_counter"
    And the script output should contain "lightbox_thumb_dividers"
    And the script output should contain "js_preload_slides"
    And the script output should contain "js_swipe_support"
    And the script output should contain "js_hero_lightbox_sync"
    And the script output should contain "js_photoswipe_zoom"
    And the script output should contain "js_photoswipe_init"
