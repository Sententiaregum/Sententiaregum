@user @repository
Feature: user repository
    All extended database query operations using doctrine should be
    abstracted in a repository

    Scenario: remove pending activations
        Given the following users exist:
            | username | password | email                  | activation_date | is_non_activated |
            | 0        | 123456   | 0@sententiaregum.dev   | -3 hours        | true             |
            | 1        | 123456   | 1@sententiaregum.dev   | -3 hours        | true             |
            | foo      | 123456   | foo@sententiaregum.dev | -1 day          | false            |
        When I try to delete all users with pending activation
        Then one user should still exist
        And two users should be removed

    Scenario Outline: get follower ids
        Given the user fixtures have been applied
        When I ask for a list of follower ids for user "Ma27" with limit <limit> and offset <offset>
        Then I should get "<id_count>" ids

        Examples:
          | limit | offset | id_count |
          | 25    | 0      | 2        |
          | 1     | 1      | 1        |
    Scenario: load follower by activation key and username
        Given the database is purged
        And the following users exist:
            | user_id | username | password | email          | activation_key | is_non_activated |
            | 1       | Ma27     | 123456   | test@localhost | 1234key1234    | true             |
        When I'd like to see a user by with username "Ma27" and key "1234key1234"
        Then I should see the user with id "1"

    Scenario: delete ancient attempt information
        Given the following users exist:
            | username | password | email                   | is_non_activated |
            | Ma27     | 123456   | ma27@sententiaregum.dev | false            |
        And the following auth data exist:
            | ip        | latest              | affected |
            | 127.0.0.1 | 2015-01-01 00:00:00 | Ma27     |
        When I delete ancient auth data
        Then no log about "127.0.0.1" should exist on user "Ma27" should exist

    Scenario: filter usernames that are already in use
        Given the following users exist:
            | username | password | email                  | activation_date | is_non_activated |
            | 0        | 123456   | 0@sententiaregum.dev   | -3 hours        | true             |
            | 1        | 123456   | 1@sententiaregum.dev   | -3 hours        | true             |
            | foo      | 123456   | foo@sententiaregum.dev | -1 day          | false            |
        When I want to filter for non-unique usernames with the following data:
            | username |
            | 0        |
            | Ma27     |
        Then I should see the following names:
            | username |
            | Ma27     |

    Scenario: persist a user
        When I try to persist the following user:
            | username | password | email        |
            | Ma27     | 123456   | ma27@foo.com |
        Then it should be present in the identity map

    Scenario: remove a user
        Given the following users exist:
            | username | password | email                | activation_date | is_non_activated |
            | 0        | 123456   | 0@sententiaregum.dev | -3 hours        | true             |
        When I try to remove the user "0"
        Then it should be scheduled for removal
