@locale
Feature: switch locale
    As an authenticated user I'd like to switch the locale of my account
    in order to get the preferred translations in my dashboard.

    Background:
        Given I'm authenticated as "Ma27" with password "72aM"

    Scenario: switch to unsupported locale
        Given I have the following payload:
        """
          {
          "locale": "fr"
          }
        """
        When I submit a request to "PATCH /api/protected/locale.json"
        Then I should get a response with the 400 status code

    Scenario: switch locale
        Given I have the following payload:
        """
          {
          "locale": "en"
          }
        """
        When I submit a request to "PATCH /api/protected/locale.json"
        Then I should get a response with the 204 status code
        And I should have an account with name "Ma27" and locale "en"
