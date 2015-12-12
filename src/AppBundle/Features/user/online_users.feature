@online_users
Feature: online users
  As a user I'd like to see who of the people I follow are online

  Background:
    Given the following users exist:
        | user_id | username | password | email              |
        |       1 | test_1   | 123456   | test_1@example.org |
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
     When the user "test_1" asks for a list containing online users
     Then he should see the following data:
      | username  | is_online |
      | admin     | true      |
      | benbieler | true      |
      | Ma27      | false     |
