@users
Feature: user repository
  All extended database query operations using doctrine should be
  abstracted in a repository

  Scenario: remove pending activations
    Given the following users exist:
        | username | password | email                   | activation_date | is_non_activated |
        | 0        | 123456   | 0@sententiaregum.dev    | -3 hours        | true             |
        | 1        | 123456   | 1@sententiaregum.dev    | -3 hours        | true             |
        | foo      | 123456   | foo@sententiaregum.dev  | -1 day          | false            |
    When I try to delete all users with pending activation
    Then one user should still exist
     And two users should be removed

  Scenario: get follower ids
    Given the user fixtures have been applied
     When I ask for a list of follower ids for user "Ma27"
     Then I should get two ids

  Scenario: load follower by activation key and username
    Given the database is purged
      And the following users exist:
        | user_id | username | password | email          | activation_key | is_non_activated |
        | 1       | Ma27     | 123456   | test@localhost | 1234key1234    | true             |
    When I'd like to see a user by with username "Ma27" and key "1234key1234"
    Then I should see the user with id "1"
