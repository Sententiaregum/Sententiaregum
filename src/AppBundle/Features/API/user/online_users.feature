@user
@online_users
Feature: online users
    As a user I'd like to see who of the people I follow are online

    Background:
        Given the following users exist:
            | username | password | email              |
            | test_1   | 123456   | test_1@example.org |
        And this user follows the following users:
            | username  |
            | admin     |
            | benbieler |
            | Ma27      |

    Scenario: get online users list
        Given the following users are online:
            | username  |
            | admin     |
            | anonymus  |
            | benbieler |
        And I'm logged in as "test_1" with password "123456"
        When I ask for a list containing online users
        Then I should see the following data:
            | username  | is_online |
            | admin     | true      |
            | benbieler | true      |
            | Ma27      | false     |
