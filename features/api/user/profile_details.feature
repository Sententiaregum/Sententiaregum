@user
Feature: profile details
    As a user I'd like to gather information about the profiles of other users.

    Background:
        Given I'm authenticated as "Ma27" with password "72aM"

    Scenario: retrieve security profile of myself
        When I submit a request to "GET /api/protected/users/credentials.json"
        Then I should get a response with the 200 status code
        And I should get "Ma27" for the response property path "[username]"
        And I should get "de" for the response property path "[locale]"
