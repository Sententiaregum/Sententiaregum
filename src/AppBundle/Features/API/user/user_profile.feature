@users @details
Feature: user profile
    In order to gather information about users, a API providing data about his profile is necessary.
    A profile can be a simple overview about permissions for the security system or more detailed information
    for other users

    Background:
        Given the following users exist:
            | username  | password | email                        | activation_date | is_non_activated |
            | logged_in | 123456   | logged_in@sententiaregum.dev | -6 hours        | false            |

    Scenario: request credential data after login
        Given I'm logged in as "logged_in" with password "123456"
        When I attempt to gather sensitive data
        Then I should see the following information:
            | username  | roles     |
            | logged_in | ROLE_USER |
        And the proper api key should be adjusted
