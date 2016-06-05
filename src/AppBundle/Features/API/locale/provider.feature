@locale
@provider
Feature: locale provider
    In order to provide information about the
    locale system, an API which gives information about the locales is necessary.

    Scenario: list of supported locales
        When I'd like to see all locales with display names
        Then I should see the following locales:
            | display_name | shortcut |
            | Deutsch      | de       |
            | English      | en       |
