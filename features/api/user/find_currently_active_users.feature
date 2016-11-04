@user
Feature: find currently active users
    In order to know which of the followed users are active to chat,
    a list which provides this information is needed.

    Background:
        Given I'm authenticated as "benbieler" with password "releibneb"

    Scenario: find following users that are currently active
        Given the following users are online:
            | username  | password  |
            | Ma27      | 72aM      |
            | admin     | 123456    |
        When I submit a request to "GET /api/protected/users/online.json"
        Then I should get a response with the 200 status code
        And the following users should be active:
            | username  |
            | Ma27      |
