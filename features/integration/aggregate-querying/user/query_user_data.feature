@aggregate-querying
Feature: query user data
    The user aggregate is the most important aggregate in the whole platform.
    Most of the parts rely on this aggregate and need to be supplied with certain data.

    Scenario: purge expired pending activations
        Given a user with name "sententiaregum" has an expired activation
        When I try to delete all users with pending activation
        Then the user "sententiaregum" should be removed

    Scenario Outline: get follower ids
        When I ask for a list of follower ids for user "Ma27" with limit <limit> and offset <offset>
        Then I should get "<id_count>" ids

        Examples:
          | limit | offset | id_count |
          | 25    | 0      | 2        |
          | 1     | 1      | 1        |
          | 25    | 25     | 0        |
    Scenario: load user by activation key and username
        Given the user "sententiaregum" is not activated and has activation key "A5WTBA1JE01A8KFDK7LVLN"
        When I'd like to see a user by with username "sententiaregum" and key "A5WTBA1JE01A8KFDK7LVLN"
        Then I should get one result

    Scenario: delete ancient attempt information
        Given the following auth data exist:
            | ip        | latest              | affected |
            | 127.0.0.1 | 2015-01-01 00:00:00 | Ma27     |
        When I delete ancient auth data
        Then no log about "127.0.0.1" should exist on user "Ma27" should exist

    Scenario: filter usernames that are already in use
        When I want to filter for non-unique usernames with the following data:
            | username  |
            | test      |
            | Ma27      |
            | benbieler |
        Then I should see the following names:
            | username |
            | test     |

    Scenario: persist a user
        When I try to persist the following user:
            | username | password | email        |
            | Ma27     | 123456   | ma27@foo.com |
        Then it should be present in the identity map
        And it should be scheduled for insert

    Scenario: remove a user
        When I try to remove the user "Ma27"
        Then it should be scheduled for removal
