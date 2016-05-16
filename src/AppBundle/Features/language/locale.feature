@locale
Feature: l10n/18n support for users
    In order to support multiple locales for users (currently English (en) and German (de)),
    a system supporting i18n and l10n is mandatory.

    Scenario: list of supported locales
        When I'd like to see all locales with display names
        Then I should see the following locales:
            | display_name | shortcut |
            | Deutsch      | de       |
            | English      | en       |

    Scenario: change invalid locale
        Given I'm logged in as "Ma27" with password "72aM"
        When I try to change my locale to "fr"
        Then I should get an error

    Scenario: change locale
        Given I'm logged in as "Ma27" with password "72aM"
        When I try to change my locale to "en"
        Then the locale should be changed
        And a cookie should be set
