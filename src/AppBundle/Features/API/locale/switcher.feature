@locale
@switcher
Feature: locale switcher
    A locale switcher is necessary to control the language
    of a logged in user.

    Scenario: change invalid locale
        Given I'm logged in as "Ma27" with password "72aM"
        When I try to change my locale to "fr"
        Then I should get an error

    Scenario: change locale
        Given I'm logged in as "Ma27" with password "72aM"
        When I try to change my locale to "en"
        Then the locale should be changed
        And a cookie should be set
